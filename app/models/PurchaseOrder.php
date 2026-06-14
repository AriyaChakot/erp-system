<?php
require_once __DIR__ . '/BaseModel.php';

class PurchaseOrder extends BaseModel {
    protected $table = 'purchase_orders';

    public function findAllWithVendor(): array {
        $sql = "SELECT po.*, v.name AS vendor_name,
                       e.name AS requested_by_name,
                       COUNT(poi.id) AS item_count
                FROM purchase_orders po
                LEFT JOIN vendors v   ON po.vendor_id    = v.id
                LEFT JOIN employees e ON po.requested_by = e.id
                LEFT JOIN purchase_order_items poi ON po.id = poi.po_id
                GROUP BY po.id
                ORDER BY po.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function findByIdWithItems(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT po.*, v.name AS vendor_name, v.tax_id AS vendor_tax_id,
                    v.address AS vendor_address, v.phone AS vendor_phone,
                    er.name AS requested_by_name, ea.name AS approved_by_name
             FROM purchase_orders po
             LEFT JOIN vendors v   ON po.vendor_id    = v.id
             LEFT JOIN employees er ON po.requested_by = er.id
             LEFT JOIN employees ea ON po.approved_by  = ea.id
             WHERE po.id = ?"
        );
        $stmt->execute([$id]);
        $po = $stmt->fetch();
        if (!$po) return null;

        $items = $this->db->prepare(
            "SELECT poi.*,
                    COALESCE(SUM(gri.quantity_received), 0) AS qty_received_total
             FROM purchase_order_items poi
             LEFT JOIN goods_receipt_items gri ON poi.id = gri.po_item_id
             WHERE poi.po_id = ?
             GROUP BY poi.id"
        );
        $items->execute([$id]);
        $po['items'] = $items->fetchAll();

        $grs = $this->db->prepare(
            "SELECT gr.*, e.name AS received_by_name
             FROM goods_receipts gr
             LEFT JOIN employees e ON gr.received_by = e.id
             WHERE gr.po_id = ?
             ORDER BY gr.receipt_date DESC"
        );
        $grs->execute([$id]);
        $po['receipts'] = $grs->fetchAll();

        return $po;
    }

    public function createWithItems(array $data, array $items): int {
        $this->db->beginTransaction();
        try {
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += (float)$item['unit_cost'] * (int)$item['quantity_ordered'];
            }
            $vatRate   = 0.07;
            $vatAmount = round($subtotal * $vatRate, 2);
            $data['subtotal']   = round($subtotal, 2);
            $data['vat_amount'] = $vatAmount;
            $data['total']      = round($subtotal + $vatAmount, 2);

            $poId = $this->insert($data);

            foreach ($items as $item) {
                $item['po_id']   = $poId;
                $item['subtotal']= round((float)$item['unit_cost'] * (int)$item['quantity_ordered'], 2);
                $this->db->prepare(
                    "INSERT INTO purchase_order_items
                     (po_id, product_id, product_name, product_code, quantity_ordered, unit_cost, subtotal)
                     VALUES (?,?,?,?,?,?,?)"
                )->execute([
                    $poId,
                    $item['product_id'] ?: null,
                    $item['product_name'],
                    $item['product_code'] ?? '',
                    $item['quantity_ordered'],
                    $item['unit_cost'],
                    $item['subtotal'],
                ]);
            }

            $this->db->commit();
            return $poId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function receiveGoods(int $poId, array $grData, array $items): int {
        $this->db->beginTransaction();
        try {
            require_once __DIR__ . '/StockItem.php';
            require_once __DIR__ . '/StockMovement.php';
            require_once __DIR__ . '/StockBatch.php';

            $stockModel   = new StockItem();
            $movModel     = new StockMovement();
            $batchModel   = new StockBatch();

            // Insert GR header
            $grId = $this->db->prepare(
                "INSERT INTO goods_receipts (gr_number, po_id, received_by, receipt_date, status, notes)
                 VALUES (?,?,?,?,?,?)"
            );
            $grId->execute([
                $grData['gr_number'],
                $poId,
                $grData['received_by'] ?: null,
                $grData['receipt_date'],
                'completed',
                $grData['notes'] ?? '',
            ]);
            $grId = (int) $this->db->lastInsertId();

            foreach ($items as $item) {
                if ((int)$item['quantity_received'] <= 0) continue;

                $warehouseId = (int)$item['warehouse_id'];
                $productId   = (int)$item['product_id'];
                $qty         = (int)$item['quantity_received'];
                $cost        = (float)$item['unit_cost'];

                // GR item
                $this->db->prepare(
                    "INSERT INTO goods_receipt_items
                     (gr_id, po_item_id, product_id, quantity_received, unit_cost, warehouse_id)
                     VALUES (?,?,?,?,?,?)"
                )->execute([$grId, $item['po_item_id'], $productId, $qty, $cost, $warehouseId]);

                // Update PO item received qty
                $this->db->prepare(
                    "UPDATE purchase_order_items SET quantity_received = quantity_received + ? WHERE id = ?"
                )->execute([$qty, $item['po_item_id']]);

                // Stock movement
                $movModel->createMovement([
                    'movement_type'  => 'in',
                    'reference_type' => 'goods_receipt',
                    'reference_id'   => $grId,
                    'product_id'     => $productId,
                    'warehouse_id'   => $warehouseId,
                    'quantity'       => $qty,
                    'unit_cost'      => $cost,
                    'created_by'     => null,
                ]);

                // Update stock_items
                $stockModel->updateStock($productId, $warehouseId, $qty, '+');

                // FIFO batch
                $batchModel->insert([
                    'product_id'     => $productId,
                    'warehouse_id'   => $warehouseId,
                    'batch_number'   => $batchModel->generateBatchNumber(),
                    'received_date'  => $grData['receipt_date'],
                    'quantity'       => $qty,
                    'unit_cost'      => $cost,
                    'reference_gr_id'=> $grId,
                ]);
            }

            // Determine new PO status
            $checkItems = $this->db->prepare(
                "SELECT quantity_ordered, quantity_received FROM purchase_order_items WHERE po_id = ?"
            );
            $checkItems->execute([$poId]);
            $allItems = $checkItems->fetchAll();
            $allDone  = true;
            $anyDone  = false;
            foreach ($allItems as $ai) {
                if ($ai['quantity_received'] >= $ai['quantity_ordered']) $anyDone = true;
                else $allDone = false;
            }
            $newStatus = $allDone ? 'received' : ($anyDone ? 'partial' : 'approved');
            $this->db->prepare("UPDATE purchase_orders SET status = ?, received_date = ? WHERE id = ?")
                     ->execute([$newStatus, $grData['receipt_date'], $poId]);

            $this->db->commit();
            return $grId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getStats(): array {
        $sql = "SELECT
                    COUNT(*) AS total,
                    SUM(status='draft')    AS draft,
                    SUM(status='approved') AS approved,
                    SUM(status='partial')  AS partial,
                    SUM(status='received') AS received,
                    SUM(status='cancelled')AS cancelled,
                    COALESCE(SUM(total),0) AS total_value
                FROM purchase_orders";
        return $this->db->query($sql)->fetch();
    }

    public function getPendingCount(): int {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM purchase_orders WHERE status IN ('draft','approved','partial')"
        )->fetchColumn();
    }

    public function generatePONumber(): string {
        $date = date('Ymd');
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM purchase_orders WHERE po_number LIKE ?"
        );
        $stmt->execute(["PO-$date-%"]);
        $seq = (int)$stmt->fetchColumn() + 1;
        return 'PO-' . $date . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function generateGRNumber(): string {
        $date = date('Ymd');
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM goods_receipts WHERE gr_number LIKE ?"
        );
        $stmt->execute(["GR-$date-%"]);
        $seq = (int)$stmt->fetchColumn() + 1;
        return 'GR-' . $date . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}

<?php
require_once __DIR__ . '/BaseModel.php';

class StockBatch extends BaseModel {
    protected $table = 'stock_batches';

    public function getByProduct(int $productId, int $warehouseId = 0): array {
        $extra = $warehouseId ? " AND sb.warehouse_id = $warehouseId" : '';
        $sql   = "SELECT sb.*, w.name AS warehouse_name
                  FROM stock_batches sb
                  JOIN warehouses w ON sb.warehouse_id = w.id
                  WHERE sb.product_id = ? AND sb.quantity > 0 $extra
                  ORDER BY sb.received_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function findAllWithDetails(array $filters = []): array {
        $where  = 'sb.quantity >= 0';
        $params = [];

        if (!empty($filters['product_id'])) {
            $where   .= ' AND sb.product_id = ?';
            $params[] = $filters['product_id'];
        }
        if (!empty($filters['warehouse_id'])) {
            $where   .= ' AND sb.warehouse_id = ?';
            $params[] = $filters['warehouse_id'];
        }
        if (!empty($filters['expiring_days'])) {
            $where   .= ' AND sb.expiry_date IS NOT NULL AND sb.expiry_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY)';
            $params[] = $filters['expiring_days'];
        }

        $sql = "SELECT sb.*, p.name AS product_name, p.code AS product_code, p.unit,
                       w.name AS warehouse_name
                FROM stock_batches sb
                JOIN products p   ON sb.product_id   = p.id
                JOIN warehouses w ON sb.warehouse_id  = w.id
                WHERE $where
                ORDER BY sb.received_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getExpiringBatches(int $days = 30): array {
        return $this->findAllWithDetails(['expiring_days' => $days]);
    }

    public function getTotalValue(): float {
        $val = $this->db->query(
            "SELECT COALESCE(SUM(quantity * unit_cost), 0) FROM stock_batches WHERE quantity > 0"
        )->fetchColumn();
        return (float) $val;
    }

    public function getValuationReport(): array {
        $sql = "SELECT p.id, p.code, p.name, p.unit,
                       SUM(sb.quantity) AS total_qty,
                       SUM(sb.quantity * sb.unit_cost) AS total_value,
                       CASE WHEN SUM(sb.quantity) > 0
                            THEN SUM(sb.quantity * sb.unit_cost) / SUM(sb.quantity)
                            ELSE 0 END AS avg_cost
                FROM stock_batches sb
                JOIN products p ON sb.product_id = p.id
                WHERE sb.quantity > 0
                GROUP BY p.id, p.code, p.name, p.unit
                ORDER BY total_value DESC";
        return $this->db->query($sql)->fetchAll();
    }

    /** FIFO deduction — call inside a transaction */
    public function consumeFIFO(int $productId, int $warehouseId, int $qty): void {
        $batches = $this->getByProduct($productId, $warehouseId);
        $remain  = $qty;
        foreach ($batches as $b) {
            if ($remain <= 0) break;
            $take = min($remain, (int)$b['quantity']);
            $stmt = $this->db->prepare(
                "UPDATE stock_batches SET quantity = quantity - ? WHERE id = ?"
            );
            $stmt->execute([$take, $b['id']]);
            $remain -= $take;
        }
    }

    public function generateBatchNumber(): string {
        return 'BAT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
    }
}

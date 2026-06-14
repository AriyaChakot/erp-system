<?php
require_once __DIR__ . '/BaseModel.php';

class Order extends BaseModel {
    protected $table = 'orders';

    public function findAllWithCustomer(): array {
        $sql = "SELECT o.*,
                       o.customer_name,
                       o.total_amount AS total,
                       o.order_no    AS order_number
                FROM orders o
                ORDER BY o.order_date DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function findByIdWithItems(int $id): ?array {
        $order = $this->findById($id);
        if (!$order) return null;

        // Normalize column names
        $order['order_number'] = $order['order_no']      ?? $order['order_number'] ?? '';
        $order['total']        = $order['total_amount']  ?? $order['total']        ?? 0;

        $stmt = $this->db->prepare(
            "SELECT oi.*, p.code AS product_code
             FROM order_items oi
             LEFT JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?"
        );
        $stmt->execute([$id]);
        $order['items'] = $stmt->fetchAll();
        return $order;
    }

    public function createWithItems(array $orderData, array $items): int {
        $this->db->beginTransaction();
        try {
            // Map field names to actual schema
            $insertData = [
                'order_no'       => $orderData['order_number'] ?? $orderData['order_no'] ?? '',
                'customer_name'  => $orderData['customer_name'] ?? '',
                'customer_email' => $orderData['customer_email'] ?? null,
                'status'         => $orderData['status'] ?? 'pending',
                'order_date'     => $orderData['order_date'] ?? date('Y-m-d'),
                'notes'          => $orderData['notes'] ?? null,
            ];
            $orderId = $this->insert($insertData);
            $total = 0;
            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['price'];
                $total += $subtotal;
                $this->db->prepare(
                    "INSERT INTO order_items (order_id, product_id, product_name, quantity, price, subtotal)
                     VALUES (?, ?, ?, ?, ?, ?)"
                )->execute([$orderId, $item['product_id'], $item['product_name'],
                            $item['quantity'], $item['price'], $subtotal]);

                $this->db->prepare("UPDATE products SET stock_qty = stock_qty - ? WHERE id = ?")
                         ->execute([$item['quantity'], $item['product_id']]);
            }
            $this->update($orderId, ['total_amount' => $total]);
            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function generateOrderNumber(): string {
        $prefix = 'ORD-' . date('Ymd') . '-';
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE order_no LIKE ?");
        $stmt->execute([$prefix . '%']);
        return $prefix . str_pad((int)$stmt->fetchColumn() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function getSummary(): array {
        $sql = "SELECT
                    COUNT(*) AS total_orders,
                    SUM(CASE WHEN status='pending'   THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) AS completed,
                    COALESCE(SUM(CASE WHEN status='completed' THEN total_amount ELSE 0 END), 0) AS revenue
                FROM orders";
        return $this->db->query($sql)->fetch();
    }

    public function updateStatus(int $id, string $status): void {
        $this->update($id, ['status' => $status]);
    }
}

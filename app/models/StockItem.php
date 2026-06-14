<?php
require_once __DIR__ . '/BaseModel.php';

class StockItem extends BaseModel {
    protected $table = 'stock_items';

    public function findAllWithDetails(int $warehouseId = 0): array {
        $where = $warehouseId ? "AND si.warehouse_id = $warehouseId" : '';
        $sql = "SELECT si.*, p.code AS product_code, p.name AS product_name,
                       p.unit, p.price AS product_cost,
                       w.name AS warehouse_name
                FROM stock_items si
                JOIN products p   ON si.product_id   = p.id
                JOIN warehouses w ON si.warehouse_id  = w.id
                WHERE p.status = 'active' $where
                ORDER BY p.name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getLowStock(): array {
        $sql = "SELECT si.*, p.code AS product_code, p.name AS product_name,
                       p.unit, w.name AS warehouse_name
                FROM stock_items si
                JOIN products p   ON si.product_id  = p.id
                JOIN warehouses w ON si.warehouse_id = w.id
                WHERE si.quantity <= si.min_quantity AND p.status = 'active'
                ORDER BY si.quantity ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getLowStockCount(): int {
        $sql = "SELECT COUNT(*) FROM stock_items si
                JOIN products p ON si.product_id = p.id
                WHERE si.quantity <= si.min_quantity AND p.status = 'active'";
        return (int) $this->db->query($sql)->fetchColumn();
    }

    public function getByProduct(int $productId): array {
        $sql = "SELECT si.*, w.name AS warehouse_name
                FROM stock_items si
                JOIN warehouses w ON si.warehouse_id = w.id
                WHERE si.product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function getByWarehouseProduct(int $warehouseId, int $productId): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM stock_items WHERE warehouse_id = ? AND product_id = ?"
        );
        $stmt->execute([$warehouseId, $productId]);
        return $stmt->fetch() ?: null;
    }

    public function updateStock(int $productId, int $warehouseId, int $qty, string $op = '+'): void {
        $sql = "INSERT INTO stock_items (product_id, warehouse_id, quantity, min_quantity)
                VALUES (?, ?, ?, 5)
                ON DUPLICATE KEY UPDATE quantity = quantity $op ?";
        $absQty = abs($qty);
        $stmt   = $this->db->prepare($sql);
        $stmt->execute([$productId, $warehouseId, $absQty, $absQty]);

        // Keep products.stock_qty in sync (total across all warehouses)
        $total = $this->db->prepare(
            "SELECT COALESCE(SUM(quantity),0) FROM stock_items WHERE product_id = ?"
        );
        $total->execute([$productId]);
        $this->db->prepare("UPDATE products SET stock_qty = ? WHERE id = ?")
                 ->execute([$total->fetchColumn(), $productId]);
    }

    public function getTotalValue(): float {
        $sql = "SELECT COALESCE(SUM(sb.quantity * sb.unit_cost), 0)
                FROM stock_batches sb
                WHERE sb.quantity > 0";
        return (float) $this->db->query($sql)->fetchColumn();
    }

    public function getTotalSKU(): int {
        return (int) $this->db->query(
            "SELECT COUNT(DISTINCT product_id) FROM stock_items WHERE quantity > 0"
        )->fetchColumn();
    }
}

<?php
require_once __DIR__ . '/BaseModel.php';

class StockMovement extends BaseModel {
    protected $table = 'stock_movements';

    public function findAllWithDetails(array $filters = []): array {
        $where  = '1=1';
        $params = [];

        if (!empty($filters['type'])) {
            $where   .= ' AND sm.movement_type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['product_id'])) {
            $where   .= ' AND sm.product_id = ?';
            $params[] = $filters['product_id'];
        }
        if (!empty($filters['warehouse_id'])) {
            $where   .= ' AND sm.warehouse_id = ?';
            $params[] = $filters['warehouse_id'];
        }
        if (!empty($filters['date_from'])) {
            $where   .= ' AND DATE(sm.created_at) >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where   .= ' AND DATE(sm.created_at) <= ?';
            $params[] = $filters['date_to'];
        }

        $sql = "SELECT sm.*,
                       p.name AS product_name, p.code AS product_code,
                       w.name AS warehouse_name,
                       wd.name AS dest_warehouse_name,
                       e.name AS created_by_name
                FROM stock_movements sm
                JOIN products p        ON sm.product_id        = p.id
                JOIN warehouses w      ON sm.warehouse_id      = w.id
                LEFT JOIN warehouses wd ON sm.warehouse_dest_id = wd.id
                LEFT JOIN employees e  ON sm.created_by        = e.id
                WHERE $where
                ORDER BY sm.created_at DESC
                LIMIT 500";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getRecent(int $limit = 10): array {
        $sql = "SELECT sm.*, p.name AS product_name, w.name AS warehouse_name
                FROM stock_movements sm
                JOIN products p   ON sm.product_id  = p.id
                JOIN warehouses w ON sm.warehouse_id = w.id
                ORDER BY sm.created_at DESC
                LIMIT $limit";
        return $this->db->query($sql)->fetchAll();
    }

    public function createMovement(array $data): int {
        // Fetch current balance before insert
        $si = $this->db->prepare(
            "SELECT COALESCE(quantity, 0) FROM stock_items WHERE product_id = ? AND warehouse_id = ?"
        );
        $si->execute([$data['product_id'], $data['warehouse_id']]);
        $before = (int) $si->fetchColumn();

        $after = match($data['movement_type']) {
            'in', 'transfer' => $before + abs((int)$data['quantity']),
            'out'            => $before - abs((int)$data['quantity']),
            'adjustment'     => $before + (int)$data['quantity'],   // signed
            default          => $before,
        };

        $data['balance_before'] = $before;
        $data['balance_after']  = $after;
        return $this->insert($data);
    }
}

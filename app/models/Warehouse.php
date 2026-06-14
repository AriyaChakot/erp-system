<?php
require_once __DIR__ . '/BaseModel.php';

class Warehouse extends BaseModel {
    protected $table = 'warehouses';

    public function getActive(): array {
        return $this->findAll("status = 'active'", [], 'name ASC');
    }

    public function search(string $q): array {
        return $this->findAll(
            "code LIKE ? OR name LIKE ? OR location LIKE ?",
            ["%$q%", "%$q%", "%$q%"],
            'name ASC'
        );
    }

    public function findAllWithManager(): array {
        $sql = "SELECT w.*, e.name AS manager_name
                FROM warehouses w
                LEFT JOIN employees e ON w.manager_id = e.id
                ORDER BY w.name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function generateCode(): string {
        $stmt = $this->db->query("SELECT MAX(CAST(SUBSTRING(code,4) AS UNSIGNED)) AS mx FROM warehouses WHERE code LIKE 'WH-%'");
        $row  = $stmt->fetch();
        $next = (int)($row['mx'] ?? 0) + 1;
        return 'WH-' . str_pad($next, 2, '0', STR_PAD_LEFT);
    }
}

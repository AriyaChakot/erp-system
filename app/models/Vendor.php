<?php
require_once __DIR__ . '/BaseModel.php';

class Vendor extends BaseModel {
    protected $table = 'vendors';

    public function getActive(): array {
        return $this->findAll("status = 'active'", [], 'name ASC');
    }

    public function search(string $q): array {
        return $this->findAll(
            "code LIKE ? OR name LIKE ? OR contact_person LIKE ? OR email LIKE ?",
            ["%$q%", "%$q%", "%$q%", "%$q%"],
            'name ASC'
        );
    }

    public function getWithPOCount(): array {
        $sql = "SELECT v.*, COUNT(po.id) AS po_count,
                       COALESCE(SUM(po.total), 0) AS total_value
                FROM vendors v
                LEFT JOIN purchase_orders po ON v.id = po.vendor_id
                GROUP BY v.id
                ORDER BY v.name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function generateCode(): string {
        $stmt = $this->db->query("SELECT MAX(CAST(SUBSTRING(code,5) AS UNSIGNED)) AS mx FROM vendors WHERE code LIKE 'VEN-%'");
        $row  = $stmt->fetch();
        $next = (int)($row['mx'] ?? 0) + 1;
        return 'VEN-' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}

<?php
require_once __DIR__ . '/BaseModel.php';

class OvertimeRequest extends BaseModel {
    protected $table = 'overtime_requests';

    public function findAllWithEmployee(int $employeeId = 0): array {
        $where  = '1=1';
        $params = [];
        if ($employeeId) { $where .= ' AND ot.employee_id = ?'; $params[] = $employeeId; }
        $sql = "SELECT ot.*, e.name AS employee_name, e.department, e.salary,
                       ap.name AS approved_by_name
                FROM overtime_requests ot
                JOIN employees e ON ot.employee_id = e.id
                LEFT JOIN employees ap ON ot.approved_by = ap.id
                WHERE $where
                ORDER BY ot.ot_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getApprovedByPeriod(string $from, string $to, int $employeeId = 0): array {
        $where  = "ot.status = 'approved' AND ot.ot_date BETWEEN ? AND ?";
        $params = [$from, $to];
        if ($employeeId) { $where .= ' AND ot.employee_id = ?'; $params[] = $employeeId; }
        $sql = "SELECT ot.*, e.salary FROM overtime_requests ot
                JOIN employees e ON ot.employee_id = e.id
                WHERE $where
                ORDER BY ot.ot_date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getPendingCount(): int {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM overtime_requests WHERE status = 'pending'"
        )->fetchColumn();
    }

    public function generateRequestNumber(): string {
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM overtime_requests WHERE request_number LIKE ?");
        $stmt->execute(["OT-$year-%"]);
        $seq = (int)$stmt->fetchColumn() + 1;
        return 'OT-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}

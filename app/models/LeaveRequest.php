<?php
require_once __DIR__ . '/BaseModel.php';

class LeaveRequest extends BaseModel {
    protected $table = 'leave_requests';

    public function findAllWithEmployee(int $employeeId = 0): array {
        $where  = '1=1';
        $params = [];
        if ($employeeId) { $where .= ' AND lr.employee_id = ?'; $params[] = $employeeId; }
        $sql = "SELECT lr.*, e.name AS employee_name, e.department,
                       lt.name AS leave_type_name, lt.is_paid,
                       ap.name AS approved_by_name
                FROM leave_requests lr
                JOIN employees e   ON lr.employee_id   = e.id
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                LEFT JOIN employees ap ON lr.approved_by = ap.id
                WHERE $where
                ORDER BY lr.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getPending(): array {
        return $this->findAllWithEmployee() + [];
    }

    public function getLeaveTypes(): array {
        return $this->db->query("SELECT * FROM leave_types ORDER BY name")->fetchAll();
    }

    public function getBalance(int $employeeId, int $year): array {
        $sql = "SELECT lt.*, lb.allocated, lb.used,
                       COALESCE(lb.allocated, lt.days_per_year) - COALESCE(lb.used, 0) AS remaining
                FROM leave_types lt
                LEFT JOIN leave_balances lb ON lt.id = lb.leave_type_id
                    AND lb.employee_id = ? AND lb.year = ?
                ORDER BY lt.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$employeeId, $year]);
        return $stmt->fetchAll();
    }

    public function approve(int $id, int $approvedBy): void {
        $this->db->beginTransaction();
        try {
            $req = $this->findById($id);
            if (!$req || $req['status'] !== 'pending') {
                $this->db->rollBack();
                return;
            }
            $this->update($id, [
                'status'      => 'approved',
                'approved_by' => $approvedBy,
                'approved_at' => date('Y-m-d H:i:s'),
            ]);
            // Update leave balance
            $year = date('Y', strtotime($req['start_date']));
            $this->db->prepare(
                "INSERT INTO leave_balances (employee_id, leave_type_id, year, allocated, used)
                 VALUES (?, ?, ?, (SELECT days_per_year FROM leave_types WHERE id = ?), ?)
                 ON DUPLICATE KEY UPDATE used = used + ?"
            )->execute([
                $req['employee_id'],
                $req['leave_type_id'],
                $year,
                $req['leave_type_id'],
                $req['days_count'],
                $req['days_count'],
            ]);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function reject(int $id, int $approvedBy): void {
        $this->update($id, [
            'status'      => 'rejected',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function calculateDays(string $start, string $end): float {
        $days  = 0;
        $cur   = strtotime($start);
        $endTs = strtotime($end);
        while ($cur <= $endTs) {
            $dow = (int)date('N', $cur);
            if ($dow <= 5) $days++;
            $cur += 86400;
        }
        return (float)$days;
    }

    public function getUnpaidLeaveDaysInPeriod(int $employeeId, string $from, string $to): float {
        $stmt = $this->db->prepare(
            "SELECT SUM(lr.days_count) FROM leave_requests lr
             JOIN leave_types lt ON lr.leave_type_id = lt.id
             WHERE lr.employee_id = ?
               AND lr.status = 'approved'
               AND lt.is_paid = 0
               AND lr.start_date >= ? AND lr.end_date <= ?"
        );
        $stmt->execute([$employeeId, $from, $to]);
        return (float) ($stmt->fetchColumn() ?? 0);
    }

    public function generateRequestNumber(): string {
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM leave_requests WHERE request_number LIKE ?");
        $stmt->execute(["LV-$year-%"]);
        $seq = (int)$stmt->fetchColumn() + 1;
        return 'LV-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}

<?php
require_once __DIR__ . '/BaseModel.php';

class Expense extends BaseModel {
    protected $table = 'expenses';

    public function findAllWithAccount(string $status = ''): array {
        $where  = '1=1';
        $params = [];
        if ($status) { $where .= ' AND e.status = ?'; $params[] = $status; }
        $sql = "SELECT e.*, coa.name AS account_name,
                       ec.name AS created_by_name,
                       ea.name AS approved_by_name
                FROM expenses e
                LEFT JOIN chart_of_accounts coa ON e.account_id  = coa.id
                LEFT JOIN employees ec            ON e.created_by  = ec.id
                LEFT JOIN employees ea            ON e.approved_by = ea.id
                WHERE $where
                ORDER BY e.expense_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getStats(): array {
        $sql = "SELECT
                    COUNT(*) AS total,
                    SUM(status='pending')  AS pending,
                    SUM(status='approved') AS approved,
                    COALESCE(SUM(CASE WHEN status IN ('approved','paid') THEN amount END), 0) AS total_amount
                FROM expenses";
        return $this->db->query($sql)->fetch();
    }

    public function getSummaryByCategory(int $year, int $month): array {
        $dateFrom = sprintf('%d-%02d-01', $year, $month);
        $dateTo   = date('Y-m-t', strtotime($dateFrom));
        $stmt = $this->db->prepare(
            "SELECT category, SUM(amount) AS total
             FROM expenses
             WHERE expense_date BETWEEN ? AND ? AND status IN ('approved','paid')
             GROUP BY category
             ORDER BY total DESC"
        );
        $stmt->execute([$dateFrom, $dateTo]);
        return $stmt->fetchAll();
    }

    public function generateExpenseNumber(): string {
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM expenses WHERE expense_number LIKE ?");
        $stmt->execute(["EXP-$year-%"]);
        $seq = (int)$stmt->fetchColumn() + 1;
        return 'EXP-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getExpenseAccounts(): array {
        return $this->db->query(
            "SELECT id, code, name FROM chart_of_accounts WHERE account_type = 'expense' AND is_active = 1 ORDER BY code"
        )->fetchAll();
    }

    public static function getCategories(): array {
        return [
            'utilities'    => 'สาธารณูปโภค',
            'rent'         => 'ค่าเช่า',
            'salary'       => 'เงินเดือน',
            'transport'    => 'ขนส่ง/เดินทาง',
            'marketing'    => 'การตลาด',
            'equipment'    => 'อุปกรณ์/ครุภัณฑ์',
            'maintenance'  => 'ซ่อมบำรุง',
            'insurance'    => 'ประกันภัย',
            'misc'         => 'อื่นๆ',
        ];
    }
}

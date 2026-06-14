<?php
require_once __DIR__ . '/BaseModel.php';

class PayrollSlip extends BaseModel {
    protected $table = 'payroll_slips';

    public function findByPeriod(int $periodId): array {
        $sql = "SELECT ps.*, e.name AS employee_name, e.department, e.position
                FROM payroll_slips ps
                JOIN employees e ON ps.employee_id = e.id
                WHERE ps.period_id = ?
                ORDER BY e.department, e.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$periodId]);
        return $stmt->fetchAll();
    }

    public function findByIdWithEmployee(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT ps.*, e.name AS employee_name, e.department, e.position,
                    e.email AS employee_email, e.phone AS employee_phone,
                    pp.period_name, pp.year, pp.month, pp.pay_date
             FROM payroll_slips ps
             JOIN employees e     ON ps.employee_id = e.id
             JOIN payroll_periods pp ON ps.period_id = pp.id
             WHERE ps.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getPeriods(): array {
        return $this->db->query(
            "SELECT pp.*, COUNT(ps.id) AS slip_count,
                    COALESCE(SUM(ps.net_salary),0) AS total_net
             FROM payroll_periods pp
             LEFT JOIN payroll_slips ps ON pp.id = ps.period_id
             GROUP BY pp.id
             ORDER BY pp.year DESC, pp.month DESC"
        )->fetchAll();
    }

    public function getPeriodById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM payroll_periods WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function createPeriod(array $data): int {
        return (int) $this->db->prepare(
            "INSERT INTO payroll_periods (period_name, year, month, start_date, end_date, pay_date, status)
             VALUES (?,?,?,?,?,?,'open')"
        )->execute([
            $data['period_name'], $data['year'], $data['month'],
            $data['start_date'], $data['end_date'], $data['pay_date'],
        ]) ? (int)$this->db->lastInsertId() : 0;
    }

    public function processPayroll(int $periodId): int {
        $period  = $this->getPeriodById($periodId);
        if (!$period) return 0;

        require_once __DIR__ . '/Employee.php';
        require_once __DIR__ . '/LeaveRequest.php';
        require_once __DIR__ . '/OvertimeRequest.php';

        $employees = (new Employee())->findAll("status='active'");
        $leaveModel = new LeaveRequest();
        $otModel    = new OvertimeRequest();
        $count      = 0;

        foreach ($employees as $emp) {
            $empId      = (int)$emp['id'];
            $baseSalary = (float)$emp['salary'];
            $dailyRate  = $baseSalary / 26;
            $hourlyRate = $dailyRate / 8;

            // Unpaid leave deduction
            $unpaidDays = $leaveModel->getUnpaidLeaveDaysInPeriod($empId, $period['start_date'], $period['end_date']);
            $deductLeave= round($unpaidDays * $dailyRate, 2);

            // OT calculation
            $otRows   = $otModel->getApprovedByPeriod($period['start_date'], $period['end_date'], $empId);
            $otAmount = 0;
            $otHours  = 0;
            foreach ($otRows as $ot) {
                $rate      = match($ot['ot_type']) { 'weekend'=>2.0, 'holiday'=>3.0, default=>1.5 };
                $otAmount += round((float)$ot['ot_hours'] * $hourlyRate * $rate, 2);
                $otHours  += (float)$ot['ot_hours'];
            }

            $gross     = round($baseSalary - $deductLeave + $otAmount, 2);
            $ss        = $this->calculateSocialSecurity($gross);
            $tax       = $this->calculateIncomeTax($gross * 12) / 12;
            $taxRound  = round($tax, 2);
            $totalDed  = round($ss + $taxRound, 2);
            $net       = round($gross - $totalDed, 2);

            $this->delete_by_period_emp($periodId, $empId);
            $this->db->prepare(
                "INSERT INTO payroll_slips
                 (slip_number, period_id, employee_id, base_salary, ot_amount, gross_salary,
                  social_security, income_tax, total_deductions, net_salary, working_days, leave_days, ot_hours, status)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,'draft')"
            )->execute([
                $this->generateSlipNumber(),
                $periodId, $empId, $baseSalary, $otAmount, $gross,
                $ss, $taxRound, $totalDed, $net,
                26, $unpaidDays, $otHours,
            ]);
            $count++;
        }

        $this->db->prepare("UPDATE payroll_periods SET status='processing' WHERE id=?")->execute([$periodId]);
        return $count;
    }

    private function delete_by_period_emp(int $periodId, int $empId): void {
        $this->db->prepare(
            "DELETE FROM payroll_slips WHERE period_id = ? AND employee_id = ? AND status = 'draft'"
        )->execute([$periodId, $empId]);
    }

    public static function calculateSocialSecurity(float $gross): float {
        return round(min($gross * 0.05, 750), 2);
    }

    public static function calculateIncomeTax(float $annualGross): float {
        // Standard deduction 50% max 100,000 + personal allowance 60,000
        $deduction   = min($annualGross * 0.5, 100000) + 60000;
        $taxableInc  = max(0, $annualGross - $deduction);

        $brackets = [
            [150000,  0.00],
            [150000,  0.05],
            [200000,  0.10],
            [250000,  0.15],
            [250000,  0.20],
            [1000000, 0.25],
            [3000000, 0.30],
            [PHP_INT_MAX, 0.35],
        ];

        $tax     = 0;
        $remain  = $taxableInc;
        foreach ($brackets as [$bandSize, $rate]) {
            if ($remain <= 0) break;
            $taxable = min($remain, $bandSize);
            $tax    += $taxable * $rate;
            $remain -= $taxable;
        }
        return round($tax, 2);
    }

    public function approvePayroll(int $periodId): void {
        $this->db->prepare(
            "UPDATE payroll_slips SET status='approved' WHERE period_id=? AND status='draft'"
        )->execute([$periodId]);
        $this->db->prepare("UPDATE payroll_periods SET status='closed' WHERE id=?")->execute([$periodId]);
    }

    public function generateSlipNumber(): string {
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM payroll_slips WHERE slip_number LIKE ?");
        $stmt->execute(["SAL-$year-%"]);
        $seq = (int)$stmt->fetchColumn() + 1;
        return 'SAL-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}

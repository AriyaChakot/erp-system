<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/LeaveRequest.php';
require_once __DIR__ . '/../models/OvertimeRequest.php';
require_once __DIR__ . '/../models/PayrollSlip.php';
require_once __DIR__ . '/../models/Employee.php';

class HRController extends BaseController {
    private LeaveRequest    $leaveModel;
    private OvertimeRequest $otModel;
    private PayrollSlip     $payrollModel;

    public function __construct() {
        $this->leaveModel   = new LeaveRequest();
        $this->otModel      = new OvertimeRequest();
        $this->payrollModel = new PayrollSlip();
    }

    public function index(): void {
        $this->requireAuth();
        $empModel = new Employee();
        $this->render('hr/index', [
            'headcount'     => $empModel->count("status='active'"),
            'pendingLeaves' => count(array_filter(
                $this->leaveModel->findAllWithEmployee(),
                fn($r) => $r['status'] === 'pending'
            )),
            'pendingOT'     => $this->otModel->getPendingCount(),
            'recentLeaves'  => array_slice($this->leaveModel->findAllWithEmployee(), 0, 8),
            'flash'         => $this->getFlash(),
            'pageTitle'     => 'HR Dashboard',
            'activePage'    => 'hr',
        ]);
    }

    public function leaveRequests(): void {
        $this->requireAuth();
        $all = $this->leaveModel->findAllWithEmployee();
        $this->render('hr/leave_requests', [
            'requests'  => $all,
            'flash'     => $this->getFlash(),
            'pageTitle' => 'การลา',
            'activePage'=> 'leaves',
        ]);
    }

    public function myLeaves(): void {
        $this->requireAuth();
        // Use user session; map to employee by email
        $empId    = $this->getMyEmployeeId();
        $requests = $empId ? $this->leaveModel->findAllWithEmployee($empId) : [];
        $balances = $empId ? $this->leaveModel->getBalance($empId, (int)date('Y')) : [];
        $this->render('hr/leave_requests', [
            'requests'  => $requests,
            'balances'  => $balances,
            'myView'    => true,
            'flash'     => $this->getFlash(),
            'pageTitle' => 'วันลาของฉัน',
            'activePage'=> 'leaves',
        ]);
    }

    public function createLeave(): void {
        $this->requireAuth();
        $empId    = $this->getMyEmployeeId();
        $balances = $empId ? $this->leaveModel->getBalance($empId, (int)date('Y')) : [];
        $this->render('hr/leave_form', [
            'leaveTypes'=> $this->leaveModel->getLeaveTypes(),
            'balances'  => $balances,
            'reqNumber' => $this->leaveModel->generateRequestNumber(),
            'pageTitle' => 'ขอลา',
            'activePage'=> 'leaves',
        ]);
    }

    public function storeLeave(): void {
        $this->requireAuth();
        $empId    = $this->getMyEmployeeId();
        $start    = $this->input('start_date');
        $end      = $this->input('end_date');
        $days     = LeaveRequest::calculateDays($start, $end);

        if ($days <= 0) {
            $this->setFlash('danger', 'วันที่ไม่ถูกต้อง กรุณาตรวจสอบ');
            $this->redirect('/hr/leave');
        }

        $this->leaveModel->insert([
            'request_number' => $this->leaveModel->generateRequestNumber(),
            'employee_id'    => $empId ?? 1,
            'leave_type_id'  => (int)$this->input('leave_type_id'),
            'start_date'     => $start,
            'end_date'       => $end,
            'days_count'     => $days,
            'reason'         => $this->input('reason'),
            'status'         => 'pending',
        ]);
        $this->setFlash('success', "ส่งคำขอลา $days วันเรียบร้อยแล้ว — รอการอนุมัติ");
        $this->redirect('/hr/my-leaves');
    }

    public function approveLeave(int $id): void {
        $this->requireAdmin();
        $this->leaveModel->approve($id, 1);
        $this->setFlash('success', 'อนุมัติการลาเรียบร้อยแล้ว');
        $this->redirect('/hr/leaves');
    }

    public function rejectLeave(int $id): void {
        $this->requireAdmin();
        $this->leaveModel->reject($id, 1);
        $this->setFlash('warning', 'ปฏิเสธการลาแล้ว');
        $this->redirect('/hr/leaves');
    }

    public function overtime(): void {
        $this->requireAuth();
        $this->render('hr/overtime', [
            'requests'  => $this->otModel->findAllWithEmployee(),
            'flash'     => $this->getFlash(),
            'pageTitle' => 'ล่วงเวลา (OT)',
            'activePage'=> 'overtime',
        ]);
    }

    public function createOT(): void {
        $this->requireAuth();
        $empModel = new Employee();
        $this->render('hr/overtime_form', [
            'employees' => $empModel->findAll("status='active'", [], 'name ASC'),
            'reqNumber' => $this->otModel->generateRequestNumber(),
            'pageTitle' => 'ขอ OT',
            'activePage'=> 'overtime',
        ]);
    }

    public function storeOT(): void {
        $this->requireAuth();
        $start = $this->input('start_time');
        $end   = $this->input('end_time');
        $hours = 0;
        if ($start && $end) {
            $diff  = strtotime("1970-01-01 $end") - strtotime("1970-01-01 $start");
            $hours = round($diff / 3600, 2);
        }
        $otType = $this->input('ot_type');
        $rate   = match($otType) { 'weekend'=>2.0, 'holiday'=>3.0, default=>1.5 };

        $this->otModel->insert([
            'request_number' => $this->otModel->generateRequestNumber(),
            'employee_id'    => (int)$this->input('employee_id'),
            'ot_date'        => $this->input('ot_date'),
            'start_time'     => $start,
            'end_time'       => $end,
            'ot_hours'       => $hours,
            'ot_type'        => $otType,
            'ot_rate'        => $rate,
            'reason'         => $this->input('reason'),
            'status'         => 'pending',
        ]);
        $this->setFlash('success', "ส่งคำขอ OT $hours ชม. เรียบร้อยแล้ว");
        $this->redirect('/hr/overtime');
    }

    public function approveOT(int $id): void {
        $this->requireAdmin();
        $this->otModel->update($id, ['status' => 'approved', 'approved_by' => 1]);
        $this->setFlash('success', 'อนุมัติ OT เรียบร้อยแล้ว');
        $this->redirect('/hr/overtime');
    }

    public function payroll(): void {
        $this->requireAdmin();
        $this->render('hr/payroll', [
            'periods'   => $this->payrollModel->getPeriods(),
            'flash'     => $this->getFlash(),
            'pageTitle' => 'เงินเดือน',
            'activePage'=> 'payroll',
        ]);
    }

    public function createPeriod(): void {
        $this->requireAdmin();
        $this->render('hr/payroll_period_form', [
            'pageTitle' => 'สร้าง Payroll Period',
            'activePage'=> 'payroll',
        ]);
    }

    public function storePeriod(): void {
        $this->requireAdmin();
        $year  = (int)$this->input('year');
        $month = (int)$this->input('month');
        $this->payrollModel->createPeriod([
            'period_name' => $this->input('period_name'),
            'year'        => $year,
            'month'       => $month,
            'start_date'  => $this->input('start_date'),
            'end_date'    => $this->input('end_date'),
            'pay_date'    => $this->input('pay_date'),
        ]);
        $this->setFlash('success', 'สร้าง Payroll Period เรียบร้อยแล้ว');
        $this->redirect('/hr/payroll');
    }

    public function viewPayroll(int $id): void {
        $this->requireAdmin();
        $period = $this->payrollModel->getPeriodById($id);
        if (!$period) { $this->setFlash('danger', 'ไม่พบ period'); $this->redirect('/hr/payroll'); }
        $slips  = $this->payrollModel->findByPeriod($id);
        $this->render('hr/payroll_period', [
            'period'    => $period,
            'slips'     => $slips,
            'flash'     => $this->getFlash(),
            'pageTitle' => 'Payroll: ' . $period['period_name'],
            'activePage'=> 'payroll',
        ]);
    }

    public function processPayroll(int $id): void {
        $this->requireAdmin();
        $count = $this->payrollModel->processPayroll($id);
        $this->setFlash('success', "คำนวณเงินเดือน $count คนเรียบร้อยแล้ว");
        $this->redirect('/hr/period/' . $id);
    }

    public function viewSlip(int $id): void {
        $this->requireAuth();
        $slip = $this->payrollModel->findByIdWithEmployee($id);
        if (!$slip) { $this->setFlash('danger', 'ไม่พบ payslip'); $this->redirect('/hr/payroll'); }
        $this->render('hr/payslip', [
            'slip'      => $slip,
            'pageTitle' => 'Payslip ' . $slip['slip_number'],
            'activePage'=> 'payroll',
        ]);
    }

    public function approvePayroll(int $id): void {
        $this->requireAdmin();
        $this->payrollModel->approvePayroll($id);
        $this->setFlash('success', 'อนุมัติ Payroll เรียบร้อยแล้ว');
        $this->redirect('/hr/period/' . $id);
    }

    private function getMyEmployeeId(): ?int {
        // Try to match user email to employee email
        $email = $_SESSION['user_email'] ?? '';
        if (!$email) return null;
        $stmt  = (new Employee())->db ?? null;
        if (!$stmt) {
            $emp = new Employee();
            $rows = $emp->findAll('email = ?', [$email]);
            return $rows ? (int)$rows[0]['id'] : null;
        }
        return null;
    }
}

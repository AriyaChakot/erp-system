<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/IssueReport.php';

class ReportController extends BaseController {
    private IssueReport $model;

    public function __construct() {
        $this->model = new IssueReport();
    }

    public function index(): void {
        $this->requireAuth();
        $this->render('reports/index', [
            'pageTitle'  => 'รายงานปัญหาของฉัน',
            'activePage' => 'report',
            'reports'    => $this->model->findByUser((int)$_SESSION['user_id']),
        ]);
    }

    public function create(): void {
        $this->requireAuth();
        $this->render('reports/create', [
            'pageTitle'  => 'แจ้งปัญหา',
            'activePage' => 'report',
        ]);
    }

    public function store(): void {
        $this->requireAuth();
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (!$subject || !$message) {
            $this->setFlash('danger', 'กรุณากรอกข้อมูลให้ครบถ้วน');
            $this->redirect('/report/create');
            return;
        }

        $this->model->insert([
            'user_id' => (int)$_SESSION['user_id'],
            'subject' => $subject,
            'message' => $message,
            'status'  => 'open',
        ]);
        $this->setFlash('success', 'ส่งรายงานปัญหาสำเร็จ Admin จะตอบกลับเร็วๆ นี้');
        $this->redirect('/report');
    }
}

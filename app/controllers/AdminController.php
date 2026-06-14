<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/IssueReport.php';

class AdminController extends BaseController {
    private User $userModel;
    private IssueReport $reportModel;

    public function __construct() {
        $this->userModel   = new User();
        $this->reportModel = new IssueReport();
    }

    public function index(): void {
        $this->requireAdmin();
        $this->render('admin/index', [
            'pageTitle'      => 'Admin Dashboard',
            'activePage'     => 'admin',
            'totalUsers'     => $this->userModel->count(),
            'pendingUsers'   => $this->userModel->count("role = 'pending'"),
            'openReports'    => $this->reportModel->count("status = 'open'"),
            'totalReports'   => $this->reportModel->count(),
            'recentUsers'    => $this->userModel->findAll('', [], 'created_at DESC'),
        ]);
    }

    public function users(): void {
        $this->requireAdmin();
        $this->render('admin/users', [
            'pageTitle'  => 'จัดการผู้ใช้',
            'activePage' => 'admin',
            'users'      => $this->userModel->findAll('', [], 'created_at DESC'),
        ]);
    }

    public function updateRole(): void {
        $this->requireAdmin();
        $userId = (int)($_POST['user_id'] ?? 0);
        $role   = $_POST['role'] ?? '';

        if (!in_array($role, ['user', 'admin', 'pending'], true)) {
            $this->setFlash('danger', 'Role ไม่ถูกต้อง');
            $this->redirect('/admin/users');
            return;
        }
        if ($userId === (int)$_SESSION['user_id']) {
            $this->setFlash('warning', 'ไม่สามารถเปลี่ยน Role ของตัวเองได้');
            $this->redirect('/admin/users');
            return;
        }

        $user = $this->userModel->findById($userId);
        if (!$user) { $this->setFlash('danger', 'ไม่พบผู้ใช้'); $this->redirect('/admin/users'); return; }

        $this->userModel->update($userId, ['role' => $role]);
        $this->setFlash('success', "เปลี่ยน Role ของ {$user['name']} เป็น {$role} สำเร็จ");
        $this->redirect('/admin/users');
    }

    public function deleteUser(): void {
        $this->requireAdmin();
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId === (int)$_SESSION['user_id']) {
            $this->setFlash('warning', 'ไม่สามารถลบบัญชีของตัวเองได้');
            $this->redirect('/admin/users');
            return;
        }
        $user = $this->userModel->findById($userId);
        if ($user) {
            $this->userModel->delete($userId);
            $this->setFlash('success', "ลบผู้ใช้ {$user['name']} สำเร็จ");
        }
        $this->redirect('/admin/users');
    }

    public function notifications(): void {
        $this->requireAdmin();
        $this->render('admin/notifications', [
            'pageTitle'  => 'แจ้งเตือนปัญหา',
            'activePage' => 'admin',
            'reports'    => $this->reportModel->findAllWithUsers(),
        ]);
    }

    public function reply(): void {
        $this->requireAdmin();
        $reportId  = (int)($_POST['report_id'] ?? 0);
        $replyText = trim($_POST['reply'] ?? '');
        $status    = $_POST['status'] ?? 'in_progress';

        if (!in_array($status, ['open', 'in_progress', 'resolved'], true)) $status = 'in_progress';

        $this->reportModel->update($reportId, [
            'admin_reply' => $replyText,
            'status'      => $status,
            'replied_at'  => date('Y-m-d H:i:s'),
        ]);
        $this->setFlash('success', 'ตอบกลับสำเร็จ');
        $this->redirect('/admin/notifications');
    }
}

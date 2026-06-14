<?php
class BaseController {
    protected function render(string $view, array $data = []): void {
        // Inject common data automatically
        $data['currentUser'] = $this->currentUser();
        if (!isset($data['flash'])) {
            $data['flash'] = $this->getFlash();
        }

        // Header notification counts
        if (!empty($_SESSION['user_id'])) {
            require_once __DIR__ . '/../models/IssueReport.php';
            $irm = new IssueReport();
            $data['_myReportCount'] = $irm->countByUser((int)$_SESSION['user_id']);
            if ($_SESSION['user_role'] === 'admin') {
                require_once __DIR__ . '/../models/User.php';
                $data['_pendingUserCount'] = (new User())->count("role = 'pending'");
                $data['_openReportCount']  = $irm->count("status = 'open'");
            }

            // ERP module sidebar badge counts (graceful — tables may not exist yet)
            try {
                if (!isset($data['_pendingPOCount'])) {
                    require_once __DIR__ . '/../models/PurchaseOrder.php';
                    $data['_pendingPOCount'] = (new PurchaseOrder())->getPendingCount();
                }
            } catch (Throwable) { $data['_pendingPOCount'] = 0; }

            try {
                if (!isset($data['_lowStockCount'])) {
                    require_once __DIR__ . '/../models/StockItem.php';
                    $data['_lowStockCount'] = (new StockItem())->getLowStockCount();
                }
            } catch (Throwable) { $data['_lowStockCount'] = 0; }

            try {
                if (!isset($data['_overdueInvoiceCount'])) {
                    require_once __DIR__ . '/../models/Invoice.php';
                    $stats = (new Invoice())->getStats();
                    $data['_overdueInvoiceCount'] = (int)($stats['overdue_count'] ?? 0);
                }
            } catch (Throwable) { $data['_overdueInvoiceCount'] = 0; }

            try {
                if (!isset($data['_pendingLeaveCount'])) {
                    require_once __DIR__ . '/../models/LeaveRequest.php';
                    $allLeaves = (new LeaveRequest())->findAllWithEmployee();
                    $data['_pendingLeaveCount'] = count(array_filter($allLeaves, fn($r) => $r['status'] === 'pending'));
                }
            } catch (Throwable) { $data['_pendingLeaveCount'] = 0; }
        }

        extract($data);
        $viewFile = __DIR__ . "/../views/$view.php";
        if (!file_exists($viewFile)) {
            die("View not found: $view");
        }
        require __DIR__ . '/../views/layout/header.php';
        require $viewFile;
        require __DIR__ . '/../views/layout/footer.php';
    }

    protected function renderAuth(string $view, array $data = []): void {
        if (!isset($data['flash'])) {
            $data['flash'] = $this->getFlash();
        }
        extract($data);
        require __DIR__ . '/../views/layout/header_auth.php';
        require __DIR__ . "/../views/$view.php";
        require __DIR__ . '/../views/layout/footer_auth.php';
    }

    protected function requireAuth(): void {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        if ($_SESSION['user_role'] === 'pending') {
            $this->redirect('/pending');
        }
    }

    protected function requireAdmin(): void {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        if ($_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            die("<h1 style='font-family:sans-serif'>403 – ไม่มีสิทธิ์เข้าถึง</h1>");
        }
    }

    protected function currentUser(): ?array {
        if (empty($_SESSION['user_id'])) return null;
        return [
            'id'    => $_SESSION['user_id'],
            'name'  => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role'  => $_SESSION['user_role'],
        ];
    }

    protected function redirect(string $path): void {
        header("Location: " . BASE_URL . $path);
        exit;
    }

    protected function input(string $key, string $default = ''): string {
        return htmlspecialchars(trim($_POST[$key] ?? $_GET[$key] ?? $default), ENT_QUOTES, 'UTF-8');
    }

    protected function setFlash(string $type, string $msg): void {
        $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    }

    protected function getFlash(): ?array {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}

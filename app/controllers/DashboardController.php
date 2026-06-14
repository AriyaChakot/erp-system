<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/Order.php';

class DashboardController extends BaseController {
    public function index(): void {
        $this->requireAuth();
        $product  = new Product();
        $customer = new Customer();
        $employee = new Employee();
        $order    = new Order();

        // Optional new-module stats (graceful if tables don't exist yet)
        $pendingPOCount     = 0;
        $lowStockCount      = 0;
        $overdueInvoiceCount= 0;
        $pendingLeaveCount  = 0;

        try {
            require_once __DIR__ . '/../models/PurchaseOrder.php';
            $poModel = new PurchaseOrder();
            $pendingPOCount = $poModel->getPendingCount();
        } catch (Throwable) {}

        try {
            require_once __DIR__ . '/../models/StockItem.php';
            $stockModel = new StockItem();
            $lowStockCount = $stockModel->getLowStockCount();
        } catch (Throwable) {}

        try {
            require_once __DIR__ . '/../models/Invoice.php';
            $invoiceModel = new Invoice();
            $stats = $invoiceModel->getStats();
            $overdueInvoiceCount = (int)($stats['overdue_count'] ?? 0);
        } catch (Throwable) {}

        try {
            require_once __DIR__ . '/../models/LeaveRequest.php';
            $leaveModel = new LeaveRequest();
            $allLeaves = $leaveModel->findAllWithEmployee();
            $pendingLeaveCount = count(array_filter($allLeaves, fn($r) => $r['status'] === 'pending'));
        } catch (Throwable) {}

        $this->render('dashboard/index', [
            'totalProducts'      => $product->count("status='active'"),
            'totalCustomers'     => $customer->count("status='active'"),
            'totalEmployees'     => $employee->count("status='active'"),
            'orderSummary'       => $order->getSummary(),
            'recentOrders'       => $order->findAllWithCustomer(),
            'lowStock'           => $product->getLowStock(10),
            'pendingPOCount'     => $pendingPOCount,
            'lowStockCount'      => $lowStockCount,
            'overdueInvoiceCount'=> $overdueInvoiceCount,
            'pendingLeaveCount'  => $pendingLeaveCount,
            'flash'              => $this->getFlash(),
            'pageTitle'          => 'Dashboard',
            'activePage'         => 'dashboard',
        ]);
    }
}

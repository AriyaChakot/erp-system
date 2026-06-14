<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/Product.php';

class OrderController extends BaseController {
    private Order $model;

    public function __construct() {
        $this->model = new Order();
    }

    public function index(): void {
        $this->requireAuth();
        $this->render('orders/index', [
            'orders'    => $this->model->findAllWithCustomer(),
            'flash'     => $this->getFlash(),
            'pageTitle' => 'คำสั่งซื้อ',
            'activePage'=> 'orders',
        ]);
    }

    public function create(): void {
        $customer = new Customer();
        $employee = new Employee();
        $product  = new Product();
        $this->render('orders/form', [
            'customers'  => $customer->findAll("status='active'"),
            'employees'  => $employee->findAll("status='active'"),
            'products'   => $product->findAll("status='active' AND stock > 0"),
            'orderNumber'=> $this->model->generateOrderNumber(),
            'pageTitle'  => 'สร้างคำสั่งซื้อ',
            'activePage' => 'orders',
        ]);
    }

    public function store(): void {
        $items = [];
        $productIds   = $_POST['product_id']   ?? [];
        $quantities   = $_POST['quantity']     ?? [];
        $prices       = $_POST['price']        ?? [];
        $productNames = $_POST['product_name'] ?? [];

        foreach ($productIds as $i => $pid) {
            if (!$pid || (int)($quantities[$i] ?? 0) <= 0) continue;
            $items[] = [
                'product_id'   => (int) $pid,
                'product_name' => htmlspecialchars($productNames[$i] ?? '', ENT_QUOTES, 'UTF-8'),
                'quantity'     => (int) $quantities[$i],
                'price'        => (float) $prices[$i],
            ];
        }

        if (empty($items)) {
            $this->setFlash('danger', 'กรุณาเพิ่มสินค้าอย่างน้อย 1 รายการ');
            $this->redirect('/orders/create');
        }

        $orderData = [
            'order_number' => $this->input('order_number'),
            'customer_id'  => (int) $this->input('customer_id') ?: null,
            'employee_id'  => (int) $this->input('employee_id') ?: null,
            'status'       => 'pending',
            'notes'        => $this->input('notes'),
            'total'        => 0,
        ];

        $id = $this->model->createWithItems($orderData, $items);
        $this->setFlash('success', "สร้างคำสั่งซื้อ #{$orderData['order_number']} เรียบร้อยแล้ว");
        $this->redirect('/orders/view/' . $id);
    }

    public function view(int $id): void {
        $order = $this->model->findByIdWithItems($id);
        if (!$order) { $this->setFlash('danger', 'ไม่พบคำสั่งซื้อ'); $this->redirect('/orders'); }
        $this->render('orders/view', [
            'order'     => $order,
            'flash'     => $this->getFlash(),
            'pageTitle' => 'รายละเอียดคำสั่งซื้อ',
            'activePage'=> 'orders',
        ]);
    }

    public function updateStatus(int $id): void {
        $status = $this->input('status');
        $allowed = ['pending','processing','completed','cancelled'];
        if (in_array($status, $allowed)) {
            $this->model->update($id, ['status' => $status]);
            $this->setFlash('success', 'อัพเดทสถานะเรียบร้อยแล้ว');
        }
        $this->redirect('/orders/view/' . $id);
    }

    public function delete(int $id): void {
        $this->model->delete($id);
        $this->setFlash('success', 'ลบคำสั่งซื้อเรียบร้อยแล้ว');
        $this->redirect('/orders');
    }
}

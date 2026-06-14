<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Customer.php';

class CustomerController extends BaseController {
    private Customer $model;

    public function __construct() {
        $this->model = new Customer();
    }

    public function index(): void {
        $this->requireAuth();
        $search = $this->input('search');
        $customers = $search ? $this->model->search($search) : $this->model->findAll();
        $this->render('customers/index', [
            'customers' => $customers, 'search' => $search,
            'flash' => $this->getFlash(), 'pageTitle' => 'ลูกค้า', 'activePage' => 'customers',
        ]);
    }

    public function create(): void {
        $this->render('customers/form', [
            'customer' => null, 'pageTitle' => 'เพิ่มลูกค้า', 'activePage' => 'customers',
        ]);
    }

    public function store(): void {
        $data = [
            'name'    => $this->input('name'),
            'email'   => $this->input('email'),
            'phone'   => $this->input('phone'),
            'address' => $this->input('address'),
            'company' => $this->input('company'),
            'status'  => $this->input('status', 'active'),
        ];
        $this->model->insert($data);
        $this->setFlash('success', 'เพิ่มลูกค้าเรียบร้อยแล้ว');
        $this->redirect('/customers');
    }

    public function edit(int $id): void {
        $customer = $this->model->findById($id);
        if (!$customer) { $this->setFlash('danger', 'ไม่พบลูกค้า'); $this->redirect('/customers'); }
        $this->render('customers/form', [
            'customer' => $customer, 'pageTitle' => 'แก้ไขลูกค้า', 'activePage' => 'customers',
        ]);
    }

    public function update(int $id): void {
        $data = [
            'name'    => $this->input('name'),
            'email'   => $this->input('email'),
            'phone'   => $this->input('phone'),
            'address' => $this->input('address'),
            'company' => $this->input('company'),
            'status'  => $this->input('status', 'active'),
        ];
        $this->model->update($id, $data);
        $this->setFlash('success', 'แก้ไขลูกค้าเรียบร้อยแล้ว');
        $this->redirect('/customers');
    }

    public function delete(int $id): void {
        $this->model->delete($id);
        $this->setFlash('success', 'ลบลูกค้าเรียบร้อยแล้ว');
        $this->redirect('/customers');
    }
}

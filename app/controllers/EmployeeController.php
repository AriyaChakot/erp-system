<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Employee.php';

class EmployeeController extends BaseController {
    private Employee $model;

    public function __construct() {
        $this->model = new Employee();
    }

    public function index(): void {
        $this->requireAuth();
        $search = $this->input('search');
        $employees = $search ? $this->model->search($search) : $this->model->findAll();
        $this->render('employees/index', [
            'employees' => $employees, 'search' => $search,
            'flash' => $this->getFlash(), 'pageTitle' => 'พนักงาน', 'activePage' => 'employees',
        ]);
    }

    public function create(): void {
        $this->render('employees/form', [
            'employee' => null, 'pageTitle' => 'เพิ่มพนักงาน', 'activePage' => 'employees',
        ]);
    }

    public function store(): void {
        $data = [
            'name'       => $this->input('name'),
            'email'      => $this->input('email'),
            'phone'      => $this->input('phone'),
            'department' => $this->input('department'),
            'position'   => $this->input('position'),
            'salary'     => (float) $this->input('salary'),
            'hire_date'  => $this->input('hire_date'),
            'status'     => $this->input('status', 'active'),
        ];
        $this->model->insert($data);
        $this->setFlash('success', 'เพิ่มพนักงานเรียบร้อยแล้ว');
        $this->redirect('/employees');
    }

    public function edit(int $id): void {
        $employee = $this->model->findById($id);
        if (!$employee) { $this->setFlash('danger', 'ไม่พบพนักงาน'); $this->redirect('/employees'); }
        $this->render('employees/form', [
            'employee' => $employee, 'pageTitle' => 'แก้ไขพนักงาน', 'activePage' => 'employees',
        ]);
    }

    public function update(int $id): void {
        $data = [
            'name'       => $this->input('name'),
            'email'      => $this->input('email'),
            'phone'      => $this->input('phone'),
            'department' => $this->input('department'),
            'position'   => $this->input('position'),
            'salary'     => (float) $this->input('salary'),
            'hire_date'  => $this->input('hire_date'),
            'status'     => $this->input('status', 'active'),
        ];
        $this->model->update($id, $data);
        $this->setFlash('success', 'แก้ไขพนักงานเรียบร้อยแล้ว');
        $this->redirect('/employees');
    }

    public function delete(int $id): void {
        $this->model->delete($id);
        $this->setFlash('success', 'ลบพนักงานเรียบร้อยแล้ว');
        $this->redirect('/employees');
    }
}

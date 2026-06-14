<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Product.php';

class ProductController extends BaseController {
    private Product $model;

    public function __construct() {
        $this->model = new Product();
    }

    public function index(): void {
        $this->requireAuth();
        $search = $this->input('search');
        $products = $search ? $this->model->search($search) : $this->model->findAll("1=1", [], 'id DESC');
        $this->render('products/index', [
            'products' => $products, 'search' => $search,
            'flash' => $this->getFlash(), 'pageTitle' => 'สินค้า', 'activePage' => 'products',
        ]);
    }

    public function create(): void {
        $this->render('products/form', [
            'product' => null, 'pageTitle' => 'เพิ่มสินค้า', 'activePage' => 'products',
        ]);
    }

    public function store(): void {
        $data = [
            'code'        => strtoupper($this->input('code')),
            'name'        => $this->input('name'),
            'description' => $this->input('description'),
            'category'    => $this->input('category'),
            'price'       => (float) $this->input('price'),
            'cost'        => (float) $this->input('cost'),
            'stock'       => (int) $this->input('stock'),
            'unit'        => $this->input('unit'),
            'status'      => $this->input('status', 'active'),
        ];
        $this->model->insert($data);
        $this->setFlash('success', 'เพิ่มสินค้าเรียบร้อยแล้ว');
        $this->redirect('/products');
    }

    public function edit(int $id): void {
        $product = $this->model->findById($id);
        if (!$product) { $this->setFlash('danger', 'ไม่พบสินค้า'); $this->redirect('/products'); }
        $this->render('products/form', [
            'product' => $product, 'pageTitle' => 'แก้ไขสินค้า', 'activePage' => 'products',
        ]);
    }

    public function update(int $id): void {
        $data = [
            'code'        => strtoupper($this->input('code')),
            'name'        => $this->input('name'),
            'description' => $this->input('description'),
            'category'    => $this->input('category'),
            'price'       => (float) $this->input('price'),
            'cost'        => (float) $this->input('cost'),
            'stock'       => (int) $this->input('stock'),
            'unit'        => $this->input('unit'),
            'status'      => $this->input('status', 'active'),
        ];
        $this->model->update($id, $data);
        $this->setFlash('success', 'แก้ไขสินค้าเรียบร้อยแล้ว');
        $this->redirect('/products');
    }

    public function delete(int $id): void {
        $this->model->delete($id);
        $this->setFlash('success', 'ลบสินค้าเรียบร้อยแล้ว');
        $this->redirect('/products');
    }
}

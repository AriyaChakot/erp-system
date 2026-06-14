<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Vendor.php';
require_once __DIR__ . '/../models/PurchaseOrder.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Warehouse.php';

class PurchasingController extends BaseController {
    private Vendor        $vendorModel;
    private PurchaseOrder $poModel;

    public function __construct() {
        $this->vendorModel = new Vendor();
        $this->poModel     = new PurchaseOrder();
    }

    public function index(): void {
        $this->requireAuth();
        $this->render('purchasing/index', [
            'pos'        => $this->poModel->findAllWithVendor(),
            'stats'      => $this->poModel->getStats(),
            'flash'      => $this->getFlash(),
            'pageTitle'  => 'ใบสั่งซื้อ',
            'activePage' => 'purchasing',
        ]);
    }

    public function vendors(): void {
        $this->requireAuth();
        $search  = $this->input('search');
        $vendors = $search ? $this->vendorModel->search($search) : $this->vendorModel->getWithPOCount();
        $this->render('purchasing/vendors', [
            'vendors'   => $vendors,
            'search'    => $search,
            'flash'     => $this->getFlash(),
            'pageTitle' => 'Vendors',
            'activePage'=> 'vendors',
        ]);
    }

    public function createVendor(): void {
        $this->requireAuth();
        $this->render('purchasing/vendor_form', [
            'vendor'    => null,
            'nextCode'  => $this->vendorModel->generateCode(),
            'pageTitle' => 'เพิ่ม Vendor',
            'activePage'=> 'vendors',
        ]);
    }

    public function storeVendor(): void {
        $this->requireAuth();
        $data = [
            'code'           => strtoupper(trim($this->input('code'))),
            'name'           => $this->input('name'),
            'contact_person' => $this->input('contact_person'),
            'email'          => $this->input('email'),
            'phone'          => $this->input('phone'),
            'address'        => $this->input('address'),
            'tax_id'         => $this->input('tax_id'),
            'payment_terms'  => (int)$this->input('payment_terms') ?: 30,
            'status'         => 'active',
        ];
        if (empty($data['name'])) {
            $this->setFlash('danger', 'กรุณากรอกชื่อ Vendor');
            $this->redirect('/purchasing/vendor');
        }
        $this->vendorModel->insert($data);
        $this->setFlash('success', 'เพิ่ม Vendor เรียบร้อยแล้ว');
        $this->redirect('/purchasing/vendors');
    }

    public function editVendor(int $id): void {
        $this->requireAuth();
        $vendor = $this->vendorModel->findById($id);
        if (!$vendor) { $this->setFlash('danger', 'ไม่พบ Vendor'); $this->redirect('/purchasing/vendors'); }
        $this->render('purchasing/vendor_form', [
            'vendor'    => $vendor,
            'pageTitle' => 'แก้ไข Vendor',
            'activePage'=> 'vendors',
        ]);
    }

    public function updateVendor(int $id): void {
        $this->requireAuth();
        $data = [
            'name'           => $this->input('name'),
            'contact_person' => $this->input('contact_person'),
            'email'          => $this->input('email'),
            'phone'          => $this->input('phone'),
            'address'        => $this->input('address'),
            'tax_id'         => $this->input('tax_id'),
            'payment_terms'  => (int)$this->input('payment_terms') ?: 30,
            'status'         => $this->input('status') ?: 'active',
        ];
        $this->vendorModel->update($id, $data);
        $this->setFlash('success', 'อัพเดท Vendor เรียบร้อยแล้ว');
        $this->redirect('/purchasing/vendors');
    }

    public function create(): void {
        $this->requireAuth();
        $this->render('purchasing/form', [
            'vendors'     => $this->vendorModel->getActive(),
            'employees'   => (new Employee())->findAll("status='active'", [], 'name ASC'),
            'products'    => (new Product())->findAll("status='active'", [], 'name ASC'),
            'poNumber'    => $this->poModel->generatePONumber(),
            'pageTitle'   => 'สร้างใบสั่งซื้อ',
            'activePage'  => 'purchasing',
        ]);
    }

    public function store(): void {
        $this->requireAuth();
        $productIds   = $_POST['product_id']       ?? [];
        $productNames = $_POST['product_name']     ?? [];
        $productCodes = $_POST['product_code']     ?? [];
        $quantities   = $_POST['quantity_ordered'] ?? [];
        $costs        = $_POST['unit_cost']        ?? [];

        $items = [];
        foreach ($productIds as $i => $pid) {
            $qty = (int)($quantities[$i] ?? 0);
            if ($qty <= 0) continue;
            $items[] = [
                'product_id'       => (int)$pid ?: null,
                'product_name'     => htmlspecialchars($productNames[$i] ?? '', ENT_QUOTES, 'UTF-8'),
                'product_code'     => htmlspecialchars($productCodes[$i]  ?? '', ENT_QUOTES, 'UTF-8'),
                'quantity_ordered' => $qty,
                'unit_cost'        => (float)($costs[$i] ?? 0),
            ];
        }

        if (empty($items)) {
            $this->setFlash('danger', 'กรุณาเพิ่มสินค้าอย่างน้อย 1 รายการ');
            $this->redirect('/purchasing/create');
        }

        $data = [
            'po_number'     => $this->input('po_number'),
            'vendor_id'     => (int)$this->input('vendor_id') ?: null,
            'requested_by'  => (int)$this->input('requested_by') ?: null,
            'status'        => 'draft',
            'expected_date' => $this->input('expected_date') ?: null,
            'notes'         => $this->input('notes'),
        ];

        $id = $this->poModel->createWithItems($data, $items);
        $this->setFlash('success', "สร้าง PO #{$data['po_number']} เรียบร้อยแล้ว");
        $this->redirect('/purchasing/view/' . $id);
    }

    public function view(int $id): void {
        $this->requireAuth();
        $po = $this->poModel->findByIdWithItems($id);
        if (!$po) { $this->setFlash('danger', 'ไม่พบใบสั่งซื้อ'); $this->redirect('/purchasing'); }
        $this->render('purchasing/view', [
            'po'        => $po,
            'flash'     => $this->getFlash(),
            'pageTitle' => 'ใบสั่งซื้อ ' . $po['po_number'],
            'activePage'=> 'purchasing',
        ]);
    }

    public function approve(int $id): void {
        $this->requireAdmin();
        $po = $this->poModel->findById($id);
        if ($po && $po['status'] === 'draft') {
            $this->poModel->update($id, ['status' => 'approved']);
            $this->setFlash('success', 'อนุมัติใบสั่งซื้อเรียบร้อยแล้ว');
        }
        $this->redirect('/purchasing/view/' . $id);
    }

    public function receive(int $id): void {
        $this->requireAuth();
        $po = $this->poModel->findByIdWithItems($id);
        if (!$po || !in_array($po['status'], ['approved', 'partial', 'sent'])) {
            $this->setFlash('danger', 'ไม่สามารถรับสินค้าสำหรับ PO นี้ได้');
            $this->redirect('/purchasing/view/' . $id);
        }
        $this->render('purchasing/receive', [
            'po'         => $po,
            'warehouses' => (new Warehouse())->getActive(),
            'grNumber'   => $this->poModel->generateGRNumber(),
            'pageTitle'  => 'บันทึกรับสินค้า - ' . $po['po_number'],
            'activePage' => 'purchasing',
        ]);
    }

    public function storeReceipt(int $id): void {
        $this->requireAuth();
        $poItemIds    = $_POST['po_item_id']       ?? [];
        $quantities   = $_POST['quantity_received']?? [];
        $costs        = $_POST['unit_cost']        ?? [];
        $productIds   = $_POST['product_id']       ?? [];
        $warehouseIds = $_POST['warehouse_id']     ?? [];

        $items = [];
        foreach ($poItemIds as $i => $poItemId) {
            $qty = (int)($quantities[$i] ?? 0);
            if ($qty <= 0) continue;
            $items[] = [
                'po_item_id'       => (int)$poItemId,
                'product_id'       => (int)($productIds[$i] ?? 0) ?: null,
                'quantity_received'=> $qty,
                'unit_cost'        => (float)($costs[$i] ?? 0),
                'warehouse_id'     => (int)($warehouseIds[$i] ?? 1),
            ];
        }

        if (empty($items)) {
            $this->setFlash('danger', 'กรุณาระบุจำนวนที่รับอย่างน้อย 1 รายการ');
            $this->redirect('/purchasing/receive/' . $id);
        }

        $grData = [
            'gr_number'    => $this->input('gr_number'),
            'received_by'  => (int)$this->input('received_by') ?: null,
            'receipt_date' => $this->input('receipt_date') ?: date('Y-m-d'),
            'notes'        => $this->input('notes'),
        ];

        $this->poModel->receiveGoods($id, $grData, $items);
        $this->setFlash('success', 'บันทึกรับสินค้าเรียบร้อยแล้ว — Stock อัพเดทแล้ว');
        $this->redirect('/purchasing/view/' . $id);
    }

    public function cancel(int $id): void {
        $this->requireAdmin();
        $po = $this->poModel->findById($id);
        if ($po && in_array($po['status'], ['draft', 'approved'])) {
            $this->poModel->update($id, ['status' => 'cancelled']);
            $this->setFlash('success', 'ยกเลิกใบสั่งซื้อเรียบร้อยแล้ว');
        }
        $this->redirect('/purchasing/view/' . $id);
    }
}

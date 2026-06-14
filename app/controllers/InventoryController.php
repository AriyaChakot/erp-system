<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Warehouse.php';
require_once __DIR__ . '/../models/StockItem.php';
require_once __DIR__ . '/../models/StockMovement.php';
require_once __DIR__ . '/../models/StockBatch.php';
require_once __DIR__ . '/../models/Product.php';

class InventoryController extends BaseController {
    private Warehouse     $warehouseModel;
    private StockItem     $stockItemModel;
    private StockMovement $movementModel;
    private StockBatch    $batchModel;

    public function __construct() {
        $this->warehouseModel = new Warehouse();
        $this->stockItemModel = new StockItem();
        $this->movementModel  = new StockMovement();
        $this->batchModel     = new StockBatch();
    }

    public function index(): void {
        $this->requireAuth();
        $this->render('inventory/index', [
            'totalSKU'       => $this->stockItemModel->getTotalSKU(),
            'totalValue'     => $this->stockItemModel->getTotalValue(),
            'lowStockCount'  => $this->stockItemModel->getLowStockCount(),
            'recentMovements'=> $this->movementModel->getRecent(15),
            'warehouses'     => $this->warehouseModel->getActive(),
            'flash'          => $this->getFlash(),
            'pageTitle'      => 'คลังสินค้า',
            'activePage'     => 'inventory',
        ]);
    }

    public function warehouses(): void {
        $this->requireAuth();
        $this->render('inventory/warehouses', [
            'warehouses' => $this->warehouseModel->findAllWithManager(),
            'flash'      => $this->getFlash(),
            'pageTitle'  => 'จัดการคลัง',
            'activePage' => 'inventory',
        ]);
    }

    public function createWarehouse(): void {
        $this->requireAuth();
        require_once __DIR__ . '/../models/Employee.php';
        $this->render('inventory/warehouse_form', [
            'warehouse' => null,
            'employees' => (new Employee())->findAll("status='active'", [], 'name ASC'),
            'nextCode'  => $this->warehouseModel->generateCode(),
            'pageTitle' => 'เพิ่มคลังใหม่',
            'activePage'=> 'inventory',
        ]);
    }

    public function storeWarehouse(): void {
        $this->requireAdmin();
        $data = [
            'code'       => strtoupper(trim($this->input('code'))),
            'name'       => $this->input('name'),
            'location'   => $this->input('location'),
            'manager_id' => (int)$this->input('manager_id') ?: null,
            'status'     => 'active',
        ];
        if (empty($data['code']) || empty($data['name'])) {
            $this->setFlash('danger', 'กรุณากรอกรหัสและชื่อคลัง');
            $this->redirect('/inventory/warehouses');
        }
        $this->warehouseModel->insert($data);
        $this->setFlash('success', 'เพิ่มคลัง ' . $data['name'] . ' เรียบร้อยแล้ว');
        $this->redirect('/inventory/warehouses');
    }

    public function editWarehouse(int $id): void {
        $this->requireAdmin();
        $wh = $this->warehouseModel->findById($id);
        if (!$wh) { $this->setFlash('danger', 'ไม่พบคลัง'); $this->redirect('/inventory/warehouses'); }
        require_once __DIR__ . '/../models/Employee.php';
        $this->render('inventory/warehouse_form', [
            'warehouse' => $wh,
            'employees' => (new Employee())->findAll("status='active'", [], 'name ASC'),
            'pageTitle' => 'แก้ไขคลัง',
            'activePage'=> 'inventory',
        ]);
    }

    public function updateWarehouse(int $id): void {
        $this->requireAdmin();
        $data = [
            'name'       => $this->input('name'),
            'location'   => $this->input('location'),
            'manager_id' => (int)$this->input('manager_id') ?: null,
            'status'     => $this->input('status') ?: 'active',
        ];
        $this->warehouseModel->update($id, $data);
        $this->setFlash('success', 'แก้ไขข้อมูลคลังเรียบร้อยแล้ว');
        $this->redirect('/inventory/warehouses');
    }

    public function stockItems(): void {
        $this->requireAuth();
        $warehouseId = (int)($this->input('warehouse_id') ?: 0);
        $this->render('inventory/stock_items', [
            'items'      => $this->stockItemModel->findAllWithDetails($warehouseId),
            'warehouses' => $this->warehouseModel->getActive(),
            'selectedWH' => $warehouseId,
            'flash'      => $this->getFlash(),
            'pageTitle'  => 'ภาพรวม Stock',
            'activePage' => 'inventory',
        ]);
    }

    public function movements(): void {
        $this->requireAuth();
        $filters = [
            'type'         => $this->input('type'),
            'warehouse_id' => (int)$this->input('warehouse_id') ?: 0,
            'date_from'    => $this->input('date_from'),
            'date_to'      => $this->input('date_to'),
        ];
        $filters = array_filter($filters);
        require_once __DIR__ . '/../models/Product.php';
        $this->render('inventory/movements', [
            'movements'  => $this->movementModel->findAllWithDetails($filters),
            'warehouses' => $this->warehouseModel->getActive(),
            'filters'    => $filters,
            'flash'      => $this->getFlash(),
            'pageTitle'  => 'Stock Movements',
            'activePage' => 'movements',
        ]);
    }

    public function adjustStock(): void {
        $this->requireAuth();
        require_once __DIR__ . '/../models/Product.php';
        $this->render('inventory/adjust', [
            'products'   => (new Product())->findAll("status='active'", [], 'name ASC'),
            'warehouses' => $this->warehouseModel->getActive(),
            'pageTitle'  => 'ปรับ Stock',
            'activePage' => 'inventory',
        ]);
    }

    public function storeAdjustment(): void {
        $this->requireAuth();
        $productId   = (int)$this->input('product_id');
        $warehouseId = (int)$this->input('warehouse_id');
        $qty         = (int)$this->input('quantity');
        $direction   = $this->input('direction'); // '+' or '-'
        $notes       = $this->input('notes');

        if (!$productId || !$warehouseId || $qty <= 0) {
            $this->setFlash('danger', 'กรุณากรอกข้อมูลให้ครบถ้วน');
            $this->redirect('/inventory/adjust');
        }

        $signedQty = $direction === '-' ? -$qty : $qty;
        $op        = $direction === '-' ? '-' : '+';

        $this->movementModel->createMovement([
            'movement_type'  => 'adjustment',
            'reference_type' => 'manual',
            'product_id'     => $productId,
            'warehouse_id'   => $warehouseId,
            'quantity'       => $signedQty,
            'notes'          => $notes,
            'created_by'     => null,
        ]);
        $this->stockItemModel->updateStock($productId, $warehouseId, $qty, $op);

        // FIFO batch: add batch on positive adjustment
        if ($direction === '+') {
            $this->batchModel->insert([
                'product_id'    => $productId,
                'warehouse_id'  => $warehouseId,
                'batch_number'  => $this->batchModel->generateBatchNumber(),
                'received_date' => date('Y-m-d'),
                'quantity'      => $qty,
                'unit_cost'     => 0,
            ]);
        } else {
            $this->batchModel->consumeFIFO($productId, $warehouseId, $qty);
        }

        $this->setFlash('success', 'ปรับ Stock เรียบร้อยแล้ว');
        $this->redirect('/inventory/stock');
    }

    public function transfer(): void {
        $this->requireAuth();
        require_once __DIR__ . '/../models/Product.php';
        $this->render('inventory/transfer', [
            'products'   => (new Product())->findAll("status='active'", [], 'name ASC'),
            'warehouses' => $this->warehouseModel->getActive(),
            'pageTitle'  => 'โอน Stock',
            'activePage' => 'inventory',
        ]);
    }

    public function storeTransfer(): void {
        $this->requireAuth();
        $productId   = (int)$this->input('product_id');
        $fromWH      = (int)$this->input('from_warehouse_id');
        $toWH        = (int)$this->input('to_warehouse_id');
        $qty         = (int)$this->input('quantity');
        $notes       = $this->input('notes');

        if (!$productId || !$fromWH || !$toWH || $qty <= 0 || $fromWH === $toWH) {
            $this->setFlash('danger', 'กรุณากรอกข้อมูลให้ครบถ้วนและเลือกคลังที่แตกต่างกัน');
            $this->redirect('/inventory/transfer');
        }

        $current = $this->stockItemModel->getByWarehouseProduct($fromWH, $productId);
        if (!$current || $current['quantity'] < $qty) {
            $this->setFlash('danger', 'Stock ในคลังต้นทางไม่เพียงพอ');
            $this->redirect('/inventory/transfer');
        }

        // Transaction: OUT from source, IN to destination
        $this->movementModel->createMovement([
            'movement_type'    => 'transfer',
            'reference_type'   => 'transfer',
            'product_id'       => $productId,
            'warehouse_id'     => $fromWH,
            'warehouse_dest_id'=> $toWH,
            'quantity'         => -$qty,
            'notes'            => $notes,
            'created_by'       => null,
        ]);
        $this->movementModel->createMovement([
            'movement_type'  => 'in',
            'reference_type' => 'transfer',
            'product_id'     => $productId,
            'warehouse_id'   => $toWH,
            'quantity'       => $qty,
            'notes'          => $notes,
            'created_by'     => null,
        ]);
        $this->stockItemModel->updateStock($productId, $fromWH, $qty, '-');
        $this->stockItemModel->updateStock($productId, $toWH,   $qty, '+');
        $this->batchModel->consumeFIFO($productId, $fromWH, $qty);
        $this->batchModel->insert([
            'product_id'    => $productId,
            'warehouse_id'  => $toWH,
            'batch_number'  => $this->batchModel->generateBatchNumber(),
            'received_date' => date('Y-m-d'),
            'quantity'      => $qty,
            'unit_cost'     => 0,
        ]);

        $this->setFlash('success', 'โอน Stock เรียบร้อยแล้ว');
        $this->redirect('/inventory/movements');
    }

    public function batches(): void {
        $this->requireAuth();
        $filters = [
            'product_id'   => (int)$this->input('product_id') ?: 0,
            'warehouse_id' => (int)$this->input('warehouse_id') ?: 0,
        ];
        $filters = array_filter($filters);
        require_once __DIR__ . '/../models/Product.php';
        $this->render('inventory/batches', [
            'batches'    => $this->batchModel->findAllWithDetails($filters),
            'expiring'   => $this->batchModel->getExpiringBatches(30),
            'products'   => (new Product())->findAll("status='active'", [], 'name ASC'),
            'warehouses' => $this->warehouseModel->getActive(),
            'filters'    => $filters,
            'flash'      => $this->getFlash(),
            'pageTitle'  => 'Batch Tracking',
            'activePage' => 'inventory',
        ]);
    }

    public function lowStock(): void {
        $this->requireAuth();
        $this->render('inventory/low_stock', [
            'items'     => $this->stockItemModel->getLowStock(),
            'flash'     => $this->getFlash(),
            'pageTitle' => 'สินค้าใกล้หมด',
            'activePage'=> 'low-stock',
        ]);
    }

    public function valuation(): void {
        $this->requireAuth();
        $report     = $this->batchModel->getValuationReport();
        $totalValue = array_sum(array_column($report, 'total_value'));
        $this->render('inventory/valuation', [
            'report'     => $report,
            'totalValue' => $totalValue,
            'flash'      => $this->getFlash(),
            'pageTitle'  => 'มูลค่าสินค้าคงคลัง (FIFO)',
            'activePage' => 'inventory',
        ]);
    }
}

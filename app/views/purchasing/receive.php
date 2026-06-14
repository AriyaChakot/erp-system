<div class="page-header">
    <h4><i class="bi bi-box-arrow-in-down me-2"></i>บันทึกรับสินค้า — <?= htmlspecialchars($po['po_number']) ?></h4>
    <a href="<?= BASE_URL ?>/purchasing/view/<?= $po['id'] ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>กลับ
    </a>
</div>

<form method="post" action="<?= BASE_URL ?>/purchasing/receipt/<?= $po['id'] ?>">
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <label class="form-label fw-medium">GR Number</label>
        <input type="text" name="gr_number" class="form-control" readonly value="<?= htmlspecialchars($grNumber) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-medium">วันที่รับสินค้า <span class="text-danger">*</span></label>
        <input type="date" name="receipt_date" class="form-control" required value="<?= date('Y-m-d') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-medium">ผู้รับสินค้า</label>
        <select name="received_by" class="form-select">
            <option value="">— ไม่ระบุ —</option>
            <?php
            require_once __DIR__ . '/../../models/Employee.php';
            $empModel = new Employee();
            foreach ($empModel->findAll("status='active'",[]) as $e):
            ?>
            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-medium">หมายเหตุ</label>
        <input type="text" name="notes" class="form-control" placeholder="หมายเหตุการรับ">
    </div>
</div>

<div class="table-card mb-3">
    <div class="px-3 pt-3 pb-2">
        <h6 class="fw-bold mb-0">รายการที่รับ</h6>
        <div class="text-muted small">กรอกจำนวนที่รับจริงในครั้งนี้ (ว่างเปล่า = ไม่รับ)</div>
    </div>
    <table class="table mb-0">
        <thead class="table-light">
            <tr>
                <th>รหัส</th>
                <th>สินค้า</th>
                <th class="text-end">สั่งซื้อ</th>
                <th class="text-end">รับแล้ว</th>
                <th class="text-end">คงค้าง</th>
                <th style="width:120px">รับครั้งนี้</th>
                <th style="width:120px">ต้นทุน/หน่วย</th>
                <th style="width:160px">คลังปลายทาง</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($po['items'] as $item): ?>
        <?php
            $alreadyReceived = (int)($item['qty_received_total'] ?? $item['quantity_received']);
            $pending         = max(0, (int)$item['quantity_ordered'] - $alreadyReceived);
        ?>
        <tr>
            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($item['product_code'] ?? '') ?></span></td>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td class="text-end"><?= number_format($item['quantity_ordered']) ?></td>
            <td class="text-end text-success"><?= number_format($alreadyReceived) ?></td>
            <td class="text-end <?= $pending > 0 ? 'text-danger fw-bold' : 'text-muted' ?>"><?= number_format($pending) ?></td>
            <td>
                <input type="hidden" name="po_item_id[]"  value="<?= $item['id'] ?>">
                <input type="hidden" name="product_id[]"  value="<?= $item['product_id'] ?? '' ?>">
                <input type="number" name="quantity_received[]" class="form-control form-control-sm"
                    min="0" max="<?= $pending ?>" value="<?= $pending ?>" placeholder="0">
            </td>
            <td>
                <input type="number" name="unit_cost[]" class="form-control form-control-sm"
                    step="0.01" min="0" value="<?= htmlspecialchars($item['unit_cost']) ?>" required>
            </td>
            <td>
                <select name="warehouse_id[]" class="form-select form-select-sm" required>
                    <?php foreach ($warehouses as $wh): ?>
                    <option value="<?= $wh['id'] ?>" <?= $wh['id'] == 1 ? 'selected' : '' ?>>
                        <?= htmlspecialchars($wh['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>ยืนยันรับสินค้า
    </button>
    <a href="<?= BASE_URL ?>/purchasing/view/<?= $po['id'] ?>" class="btn btn-outline-secondary">ยกเลิก</a>
</div>
</form>

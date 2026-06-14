<div class="page-header">
    <h4><i class="bi bi-cart3 me-2"></i>คำสั่งซื้อ: <?= htmlspecialchars($order['order_number']) ?></h4>
    <a href="<?= BASE_URL ?>/orders" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>กลับ</a>
</div>

<?php
$colors = ['pending'=>'warning','processing'=>'info','completed'=>'success','cancelled'=>'danger'];
$labels = ['pending'=>'รอดำเนินการ','processing'=>'กำลังดำเนิน','completed'=>'สำเร็จ','cancelled'=>'ยกเลิก'];
?>

<div class="row g-3">
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="table-card mb-3">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">รายการสินค้า</h6>
                <span class="badge bg-<?= $colors[$order['status']] ?? 'secondary' ?> fs-6">
                    <?= $labels[$order['status']] ?? $order['status'] ?>
                </span>
            </div>
            <table class="table">
                <thead>
                    <tr><th>สินค้า</th><th>จำนวน</th><th>ราคา/หน่วย</th><th>รวม</th></tr>
                </thead>
                <tbody>
                <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($item['product_name']) ?>
                        <?php if ($item['product_code']): ?>
                        <small class="text-muted">(<?= htmlspecialchars($item['product_code']) ?>)</small>
                        <?php endif; ?>
                    </td>
                    <td><?= number_format($item['quantity']) ?></td>
                    <td><?= number_format($item['price'], 2) ?></td>
                    <td><?= number_format($item['subtotal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">ยอดรวมทั้งสิ้น</td>
                        <td class="fw-bold fs-5"><?= number_format($order['total'], 2) ?> บาท</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Info -->
        <div class="form-card mb-3">
            <h6 class="fw-bold mb-3">ข้อมูลคำสั่งซื้อ</h6>
            <dl class="row mb-0">
                <dt class="col-5 text-muted">ลูกค้า</dt>
                <dd class="col-7"><?= htmlspecialchars($order['customer_name'] ?? '-') ?></dd>
                <dt class="col-5 text-muted">วันที่</dt>
                <dd class="col-7"><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></dd>
                <?php if ($order['notes']): ?>
                <dt class="col-5 text-muted">หมายเหตุ</dt>
                <dd class="col-7"><?= htmlspecialchars($order['notes']) ?></dd>
                <?php endif; ?>
            </dl>
        </div>

        <!-- Update Status -->
        <div class="form-card">
            <h6 class="fw-bold mb-3">อัพเดทสถานะ</h6>
            <form method="post" action="<?= BASE_URL ?>/orders/status/<?= $order['id'] ?>">
                <select name="status" class="form-select mb-2">
                    <?php foreach ($labels as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $order['status'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary w-100">บันทึกสถานะ</button>
            </form>
        </div>
    </div>
</div>

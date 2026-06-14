<?php
$statusLabel = ['draft'=>'ร่าง','approved'=>'อนุมัติแล้ว','sent'=>'ส่งแล้ว','partial'=>'รับบางส่วน','received'=>'รับครบ','cancelled'=>'ยกเลิก'];
$statusColor = ['draft'=>'secondary','approved'=>'primary','sent'=>'info','partial'=>'warning','received'=>'success','cancelled'=>'danger'];
?>
<div class="page-header">
    <div>
        <h4 class="mb-0"><i class="bi bi-cart3 me-2"></i><?= htmlspecialchars($po['po_number']) ?></h4>
        <span class="badge bg-<?= $statusColor[$po['status']] ?? 'secondary' ?> mt-1">
            <?= $statusLabel[$po['status']] ?? $po['status'] ?>
        </span>
    </div>
    <div class="d-flex gap-2">
        <?php if ($po['status'] === 'draft' && ($currentUser['role'] ?? '') === 'admin'): ?>
        <form method="post" action="<?= BASE_URL ?>/purchasing/approve/<?= $po['id'] ?>">
            <button class="btn btn-success btn-sm"><i class="bi bi-check-circle me-1"></i>อนุมัติ</button>
        </form>
        <?php endif; ?>
        <?php if (in_array($po['status'], ['approved','partial','sent'])): ?>
        <a href="<?= BASE_URL ?>/purchasing/receive/<?= $po['id'] ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-box-arrow-in-down me-1"></i>บันทึกรับสินค้า
        </a>
        <?php endif; ?>
        <?php if (in_array($po['status'], ['draft','approved']) && ($currentUser['role'] ?? '') === 'admin'): ?>
        <form method="post" action="<?= BASE_URL ?>/purchasing/cancel/<?= $po['id'] ?>" onsubmit="return confirm('ยืนยันยกเลิก PO นี้?')">
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle me-1"></i>ยกเลิก</button>
        </form>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/purchasing" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>กลับ
        </a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="form-card h-100">
            <h6 class="fw-bold mb-3">ข้อมูล PO</h6>
            <dl class="row mb-0 small">
                <dt class="col-5 text-muted">Vendor</dt>
                <dd class="col-7"><?= htmlspecialchars($po['vendor_name'] ?? '-') ?></dd>
                <dt class="col-5 text-muted">เลขผู้เสียภาษี</dt>
                <dd class="col-7"><?= htmlspecialchars($po['vendor_tax_id'] ?? '-') ?></dd>
                <dt class="col-5 text-muted">ผู้ขอซื้อ</dt>
                <dd class="col-7"><?= htmlspecialchars($po['requested_by_name'] ?? '-') ?></dd>
                <dt class="col-5 text-muted">ผู้อนุมัติ</dt>
                <dd class="col-7"><?= htmlspecialchars($po['approved_by_name'] ?? '-') ?></dd>
                <dt class="col-5 text-muted">วันที่สร้าง</dt>
                <dd class="col-7"><?= date('d/m/Y', strtotime($po['created_at'])) ?></dd>
                <dt class="col-5 text-muted">วันที่คาด</dt>
                <dd class="col-7"><?= $po['expected_date'] ? date('d/m/Y', strtotime($po['expected_date'])) : '-' ?></dd>
                <dt class="col-5 text-muted">หมายเหตุ</dt>
                <dd class="col-7"><?= htmlspecialchars($po['notes'] ?? '-') ?></dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-card h-100">
            <h6 class="fw-bold mb-3">สรุปยอดเงิน</h6>
            <dl class="row mb-0">
                <dt class="col-6 text-muted">ยอดก่อน VAT</dt>
                <dd class="col-6 text-end">฿<?= number_format($po['subtotal'], 2) ?></dd>
                <dt class="col-6 text-muted">VAT 7%</dt>
                <dd class="col-6 text-end">฿<?= number_format($po['vat_amount'], 2) ?></dd>
                <dt class="col-6 fw-bold fs-5">ยอดรวม</dt>
                <dd class="col-6 text-end fw-bold fs-5 text-primary">฿<?= number_format($po['total'], 2) ?></dd>
            </dl>
        </div>
    </div>
</div>

<!-- Items with 3-Way Matching -->
<div class="table-card mb-3">
    <div class="px-3 pt-3 pb-2">
        <h6 class="fw-bold mb-0">รายการสินค้า (3-Way Matching)</h6>
    </div>
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>รหัส</th>
                <th>สินค้า</th>
                <th class="text-end">สั่งซื้อ</th>
                <th class="text-end">รับแล้ว</th>
                <th class="text-end">คงค้าง</th>
                <th class="text-end">ราคา/หน่วย</th>
                <th class="text-end">ยอดรวม</th>
                <th>สถานะ</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($po['items'] as $item): ?>
        <?php
            $received = (int)$item['qty_received_total'];
            $ordered  = (int)$item['quantity_ordered'];
            $pending  = $ordered - $received;
            if ($received >= $ordered)      $matchBadge = '<span class="badge bg-success">รับครบ</span>';
            elseif ($received > 0)          $matchBadge = '<span class="badge bg-warning text-dark">รับบางส่วน</span>';
            else                            $matchBadge = '<span class="badge bg-secondary">รอรับ</span>';
        ?>
        <tr>
            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($item['product_code'] ?? '-') ?></span></td>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td class="text-end"><?= number_format($ordered) ?></td>
            <td class="text-end text-success fw-bold"><?= number_format($received) ?></td>
            <td class="text-end <?= $pending > 0 ? 'text-danger' : '' ?>"><?= number_format($pending) ?></td>
            <td class="text-end">฿<?= number_format($item['unit_cost'], 2) ?></td>
            <td class="text-end fw-medium">฿<?= number_format($item['subtotal'], 2) ?></td>
            <td><?= $matchBadge ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- GR History -->
<?php if (!empty($po['receipts'])): ?>
<div class="table-card">
    <div class="px-3 pt-3 pb-2">
        <h6 class="fw-bold mb-0">ประวัติการรับสินค้า (Goods Receipts)</h6>
    </div>
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr><th>GR Number</th><th>วันที่รับ</th><th>ผู้รับ</th><th>หมายเหตุ</th><th>สถานะ</th></tr>
        </thead>
        <tbody>
        <?php foreach ($po['receipts'] as $gr): ?>
        <tr>
            <td class="fw-medium"><?= htmlspecialchars($gr['gr_number']) ?></td>
            <td><?= date('d/m/Y', strtotime($gr['receipt_date'])) ?></td>
            <td><?= htmlspecialchars($gr['received_by_name'] ?? '-') ?></td>
            <td class="text-muted small"><?= htmlspecialchars($gr['notes'] ?? '-') ?></td>
            <td><span class="badge bg-success">เสร็จสิ้น</span></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

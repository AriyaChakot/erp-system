<?php
$statusLabel = ['draft'=>'ร่าง','approved'=>'อนุมัติแล้ว','sent'=>'ส่งแล้ว','partial'=>'รับบางส่วน','received'=>'รับครบ','cancelled'=>'ยกเลิก'];
$statusColor = ['draft'=>'secondary','approved'=>'primary','sent'=>'info','partial'=>'warning','received'=>'success','cancelled'=>'danger'];
?>
<div class="page-header">
    <h4><i class="bi bi-cart-plus me-2"></i>ใบสั่งซื้อ (Purchase Orders)</h4>
    <a href="<?= BASE_URL ?>/purchasing/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>สร้าง PO ใหม่
    </a>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['label'=>'PO ทั้งหมด',   'val'=>$stats['total'],    'color'=>'primary',  'icon'=>'cart3'],
        ['label'=>'รออนุมัติ',    'val'=>$stats['draft'],    'color'=>'secondary','icon'=>'hourglass-split'],
        ['label'=>'รับบางส่วน',   'val'=>$stats['partial'],  'color'=>'warning',  'icon'=>'box-arrow-in-down-left'],
        ['label'=>'รับครบแล้ว',   'val'=>$stats['received'], 'color'=>'success',  'icon'=>'check-circle'],
    ];
    foreach ($cards as $c):
    ?>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-<?= $c['color'] ?> bg-opacity-10 text-<?= $c['color'] ?>">
                    <i class="bi bi-<?= $c['icon'] ?> fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small"><?= $c['label'] ?></div>
                    <div class="fs-4 fw-bold"><?= number_format($c['val']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>PO Number</th>
                <th>Vendor</th>
                <th>ผู้ขอ</th>
                <th>สินค้า</th>
                <th>วันที่คาด</th>
                <th class="text-end">มูลค่า</th>
                <th>สถานะ</th>
                <th class="text-end">จัดการ</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($pos)): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">ยังไม่มีใบสั่งซื้อ</td></tr>
        <?php else: ?>
            <?php foreach ($pos as $po): ?>
            <tr>
                <td><a href="<?= BASE_URL ?>/purchasing/view/<?= $po['id'] ?>" class="fw-bold text-decoration-none"><?= htmlspecialchars($po['po_number']) ?></a></td>
                <td><?= htmlspecialchars($po['vendor_name'] ?? '-') ?></td>
                <td class="text-muted small"><?= htmlspecialchars($po['requested_by_name'] ?? '-') ?></td>
                <td class="text-muted small"><?= $po['item_count'] ?> รายการ</td>
                <td class="text-muted small"><?= $po['expected_date'] ? date('d/m/Y', strtotime($po['expected_date'])) : '-' ?></td>
                <td class="text-end fw-bold">฿<?= number_format($po['total'], 2) ?></td>
                <td><span class="badge bg-<?= $statusColor[$po['status']] ?? 'secondary' ?>"><?= $statusLabel[$po['status']] ?? $po['status'] ?></span></td>
                <td class="text-end">
                    <a href="<?= BASE_URL ?>/purchasing/view/<?= $po['id'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

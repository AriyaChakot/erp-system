<?php
$sc = ['open'=>'primary','processing'=>'warning','closed'=>'success'];
$sl = ['open'=>'เปิด','processing'=>'กำลังประมวลผล','closed'=>'ปิดแล้ว'];
$months = ['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
?>
<div class="page-header">
    <h4><i class="bi bi-wallet2 me-2"></i>เงินเดือน (Payroll)</h4>
    <a href="<?= BASE_URL ?>/hr/create-period" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>สร้าง Period ใหม่
    </a>
</div>

<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>Period</th>
                <th>ปี</th>
                <th>เดือน</th>
                <th>ช่วงเวลา</th>
                <th>วันจ่าย</th>
                <th class="text-end">จำนวนพนักงาน</th>
                <th class="text-end">รวมจ่าย (บาท)</th>
                <th>สถานะ</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($periods)): ?>
            <tr><td colspan="9" class="text-center text-muted py-4">ยังไม่มี Payroll Period</td></tr>
        <?php else: ?>
            <?php foreach ($periods as $p): ?>
            <tr>
                <td class="fw-semibold"><?= htmlspecialchars($p['period_name']) ?></td>
                <td><?= $p['year'] ?></td>
                <td><?= $months[$p['month']] ?? $p['month'] ?></td>
                <td class="text-muted small">
                    <?= date('d/m/Y', strtotime($p['start_date'])) ?> —
                    <?= date('d/m/Y', strtotime($p['end_date'])) ?>
                </td>
                <td class="text-muted small"><?= date('d/m/Y', strtotime($p['pay_date'])) ?></td>
                <td class="text-end"><?= number_format($p['slip_count'] ?? 0) ?></td>
                <td class="text-end fw-bold">฿<?= number_format($p['total_net'] ?? 0, 2) ?></td>
                <td><span class="badge bg-<?= $sc[$p['status']] ?? 'secondary' ?>"><?= $sl[$p['status']] ?? $p['status'] ?></span></td>
                <td>
                    <a href="<?= BASE_URL ?>/hr/period/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> ดู
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

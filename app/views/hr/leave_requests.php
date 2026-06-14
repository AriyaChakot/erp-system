<?php
$sc = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','cancelled'=>'secondary'];
$sl = ['pending'=>'รออนุมัติ','approved'=>'อนุมัติ','rejected'=>'ปฏิเสธ','cancelled'=>'ยกเลิก'];
$isMyView = !empty($myView);
?>
<div class="page-header">
    <h4><i class="bi bi-calendar-check me-2"></i><?= $isMyView ? 'วันลาของฉัน' : 'การลาทั้งหมด' ?></h4>
    <a href="<?= BASE_URL ?>/hr/leave" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>ขอลา
    </a>
</div>

<?php if ($isMyView && !empty($balances)): ?>
<div class="row g-3 mb-4">
    <?php foreach ($balances as $b): ?>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted small"><?= htmlspecialchars($b['name']) ?></div>
            <div class="fw-bold">
                <span class="text-success"><?= $b['remaining'] ?? ($b['days_per_year'] ?: '∞') ?></span>
                <span class="text-muted small"> / <?= $b['days_per_year'] ?: '∞' ?> วัน</span>
            </div>
            <div class="progress mt-1" style="height:4px">
                <?php $pct = $b['days_per_year'] > 0 ? min(100, (($b['used'] ?? 0) / $b['days_per_year']) * 100) : 0; ?>
                <div class="progress-bar bg-primary" style="width:<?= $pct ?>%"></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <?php if (!$isMyView): ?><th>พนักงาน</th><?php endif; ?>
                <th>เลขที่</th>
                <th>ประเภทลา</th>
                <th>ตั้งแต่</th>
                <th>ถึง</th>
                <th class="text-end">วัน</th>
                <th>เหตุผล</th>
                <th>สถานะ</th>
                <?php if (!$isMyView && ($currentUser['role'] ?? '') === 'admin'): ?><th>จัดการ</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($requests)): ?>
            <tr><td colspan="9" class="text-center text-muted py-4">ไม่มีข้อมูล</td></tr>
        <?php else: ?>
            <?php foreach ($requests as $r): ?>
            <tr>
                <?php if (!$isMyView): ?><td><?= htmlspecialchars($r['employee_name']) ?><div class="text-muted small"><?= htmlspecialchars($r['department'] ?? '') ?></div></td><?php endif; ?>
                <td class="text-muted small"><?= htmlspecialchars($r['request_number']) ?></td>
                <td><?= htmlspecialchars($r['leave_type_name']) ?></td>
                <td class="text-muted small"><?= date('d/m/Y', strtotime($r['start_date'])) ?></td>
                <td class="text-muted small"><?= date('d/m/Y', strtotime($r['end_date'])) ?></td>
                <td class="text-end fw-bold"><?= $r['days_count'] ?></td>
                <td class="text-muted small"><?= htmlspecialchars(mb_strimwidth($r['reason'] ?? '', 0, 30, '...')) ?></td>
                <td><span class="badge bg-<?= $sc[$r['status']] ?? 'secondary' ?>"><?= $sl[$r['status']] ?? $r['status'] ?></span></td>
                <?php if (!$isMyView && ($currentUser['role'] ?? '') === 'admin'): ?>
                <td>
                    <?php if ($r['status'] === 'pending'): ?>
                    <form method="post" action="<?= BASE_URL ?>/hr/approve-leave/<?= $r['id'] ?>" class="d-inline">
                        <button class="btn btn-xs btn-outline-success btn-sm" title="อนุมัติ"><i class="bi bi-check"></i></button>
                    </form>
                    <form method="post" action="<?= BASE_URL ?>/hr/reject-leave/<?= $r['id'] ?>" class="d-inline">
                        <button class="btn btn-xs btn-outline-danger btn-sm" title="ปฏิเสธ"><i class="bi bi-x"></i></button>
                    </form>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

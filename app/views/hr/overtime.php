<?php
$sc = ['pending'=>'warning','approved'=>'success','rejected'=>'danger'];
$sl = ['pending'=>'รออนุมัติ','approved'=>'อนุมัติ','rejected'=>'ปฏิเสธ'];
$tc = ['weekday'=>'secondary','weekend'=>'info','holiday'=>'danger'];
$tl = ['weekday'=>'วันทำงาน (1.5x)','weekend'=>'วันหยุด (2.0x)','holiday'=>'นักขัตฤกษ์ (3.0x)'];
?>
<div class="page-header">
    <h4><i class="bi bi-clock-history me-2"></i>ล่วงเวลา (OT)</h4>
    <a href="<?= BASE_URL ?>/hr/ot" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>ขอ OT
    </a>
</div>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>เลขที่</th>
                <th>พนักงาน</th>
                <th>วันที่</th>
                <th>ช่วงเวลา</th>
                <th class="text-end">ชั่วโมง</th>
                <th>ประเภท</th>
                <th>เหตุผล</th>
                <th>สถานะ</th>
                <?php if (($currentUser['role'] ?? '') === 'admin'): ?><th>จัดการ</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($requests)): ?>
            <tr><td colspan="9" class="text-center text-muted py-4">ไม่มีข้อมูล</td></tr>
        <?php else: ?>
            <?php foreach ($requests as $r): ?>
            <tr>
                <td class="text-muted small"><?= htmlspecialchars($r['request_number']) ?></td>
                <td>
                    <?= htmlspecialchars($r['employee_name']) ?>
                    <div class="text-muted small"><?= htmlspecialchars($r['department'] ?? '') ?></div>
                </td>
                <td class="text-muted small"><?= date('d/m/Y', strtotime($r['ot_date'])) ?></td>
                <td class="text-muted small"><?= substr($r['start_time'],0,5) ?> — <?= substr($r['end_time'],0,5) ?></td>
                <td class="text-end fw-bold"><?= number_format($r['ot_hours'],1) ?></td>
                <td><span class="badge bg-<?= $tc[$r['ot_type']] ?? 'secondary' ?>"><?= $tl[$r['ot_type']] ?? $r['ot_type'] ?></span></td>
                <td class="text-muted small"><?= htmlspecialchars(mb_strimwidth($r['reason'] ?? '', 0, 30, '...')) ?></td>
                <td><span class="badge bg-<?= $sc[$r['status']] ?? 'secondary' ?>"><?= $sl[$r['status']] ?? $r['status'] ?></span></td>
                <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                <td>
                    <?php if ($r['status'] === 'pending'): ?>
                    <form method="post" action="<?= BASE_URL ?>/hr/approve-ot/<?= $r['id'] ?>" class="d-inline">
                        <button class="btn btn-outline-success btn-sm" title="อนุมัติ"><i class="bi bi-check"></i></button>
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

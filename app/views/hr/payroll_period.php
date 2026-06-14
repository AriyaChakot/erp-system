<?php
$months = ['','มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
$sc = ['draft'=>'secondary','approved'=>'success','paid'=>'primary'];
$sl = ['draft'=>'ร่าง','approved'=>'อนุมัติ','paid'=>'จ่ายแล้ว'];
?>
<div class="page-header">
    <h4><i class="bi bi-wallet2 me-2"></i><?= htmlspecialchars($period['period_name']) ?></h4>
    <div class="d-flex gap-2">
        <?php if ($period['status'] === 'open' || $period['status'] === 'processing'): ?>
        <form method="post" action="<?= BASE_URL ?>/hr/process/<?= $period['id'] ?>">
            <button class="btn btn-warning btn-sm"><i class="bi bi-cpu me-1"></i>คำนวณเงินเดือน</button>
        </form>
        <?php endif; ?>
        <?php if ($period['status'] === 'processing' && !empty($slips)): ?>
        <form method="post" action="<?= BASE_URL ?>/hr/approve-payroll/<?= $period['id'] ?>">
            <button class="btn btn-success btn-sm"><i class="bi bi-check-circle me-1"></i>อนุมัติทั้งหมด</button>
        </form>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/hr/payroll" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>ย้อนกลับ
        </a>
    </div>
</div>

<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Period Info -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted small">ช่วงเวลา</div>
            <div class="fw-bold"><?= date('d/m/Y', strtotime($period['start_date'])) ?> — <?= date('d/m/Y', strtotime($period['end_date'])) ?></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted small">วันจ่าย</div>
            <div class="fw-bold"><?= date('d/m/Y', strtotime($period['pay_date'])) ?></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted small">รวมจ่ายสุทธิ</div>
            <div class="fw-bold text-success fs-5">฿<?= number_format(array_sum(array_column($slips, 'net_salary')), 2) ?></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted small">สถานะ</div>
            <div><?php
                $psc = ['open'=>'primary','processing'=>'warning','closed'=>'success'];
                $psl = ['open'=>'เปิด','processing'=>'กำลังประมวลผล','closed'=>'ปิดแล้ว'];
                echo '<span class="badge bg-'.($psc[$period['status']]??'secondary').'">'.$psl[$period['status']].'</span>';
            ?></div>
        </div>
    </div>
</div>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>พนักงาน</th>
                <th>แผนก</th>
                <th class="text-end">เงินเดือนฐาน</th>
                <th class="text-end">OT</th>
                <th class="text-end">รวมก่อนหัก</th>
                <th class="text-end">ประกันสังคม</th>
                <th class="text-end">ภาษีหัก ณ ที่จ่าย</th>
                <th class="text-end">รับสุทธิ</th>
                <th>สถานะ</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($slips)): ?>
            <tr><td colspan="10" class="text-center text-muted py-4">ยังไม่มีข้อมูล — กดปุ่ม "คำนวณเงินเดือน" เพื่อเริ่ม</td></tr>
        <?php else: ?>
            <?php foreach ($slips as $s): ?>
            <tr>
                <td class="fw-semibold"><?= htmlspecialchars($s['employee_name']) ?></td>
                <td class="text-muted small"><?= htmlspecialchars($s['department'] ?? '-') ?></td>
                <td class="text-end">฿<?= number_format($s['base_salary'], 2) ?></td>
                <td class="text-end text-info">+฿<?= number_format($s['ot_amount'], 2) ?></td>
                <td class="text-end">฿<?= number_format($s['gross_salary'], 2) ?></td>
                <td class="text-end text-danger">-฿<?= number_format($s['social_security'], 2) ?></td>
                <td class="text-end text-danger">-฿<?= number_format($s['income_tax'], 2) ?></td>
                <td class="text-end fw-bold text-success">฿<?= number_format($s['net_salary'], 2) ?></td>
                <td><span class="badge bg-<?= $sc[$s['status']] ?? 'secondary' ?>"><?= $sl[$s['status']] ?? $s['status'] ?></span></td>
                <td>
                    <a href="<?= BASE_URL ?>/hr/slip/<?= $s['id'] ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
                        <i class="bi bi-receipt"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
        <?php if (!empty($slips)): ?>
        <tfoot class="table-light fw-bold">
            <tr>
                <td colspan="4" class="text-end">รวมทั้งหมด:</td>
                <td class="text-end">฿<?= number_format(array_sum(array_column($slips, 'gross_salary')), 2) ?></td>
                <td class="text-end text-danger">-฿<?= number_format(array_sum(array_column($slips, 'social_security')), 2) ?></td>
                <td class="text-end text-danger">-฿<?= number_format(array_sum(array_column($slips, 'income_tax')), 2) ?></td>
                <td class="text-end text-success">฿<?= number_format(array_sum(array_column($slips, 'net_salary')), 2) ?></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</div>

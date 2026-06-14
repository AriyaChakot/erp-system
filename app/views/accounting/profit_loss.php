<?php
$monthNames = ['1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน',
               '5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม',
               '9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม'];
?>
<div class="page-header">
    <h4><i class="bi bi-graph-up-arrow me-2"></i>กำไรขาดทุน (P&L)</h4>
</div>

<!-- Period Selector -->
<div class="form-card mb-4">
    <form method="get" class="d-flex gap-2 align-items-end">
        <div>
            <label class="form-label small mb-1">ปี</label>
            <select name="year" class="form-select form-select-sm">
                <?php for ($y = date('Y'); $y >= date('Y')-5; $y--): ?>
                <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div>
            <label class="form-label small mb-1">เดือน</label>
            <select name="month" class="form-select form-select-sm">
                <?php foreach ($monthNames as $k => $v): ?>
                <option value="<?= $k ?>" <?= $month == $k ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-outline-primary btn-sm">ดูรายงาน</button>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card border-start border-success border-4">
            <div class="text-muted small">รายได้รวม</div>
            <div class="fs-4 fw-bold text-success">฿<?= number_format($totalRevenue, 2) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card border-start border-danger border-4">
            <div class="text-muted small">ค่าใช้จ่ายรวม</div>
            <div class="fs-4 fw-bold text-danger">฿<?= number_format($totalExpense, 2) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card border-start border-<?= $netProfit >= 0 ? 'primary' : 'warning' ?> border-4">
            <div class="text-muted small">กำไร/ขาดทุนสุทธิ</div>
            <div class="fs-4 fw-bold <?= $netProfit >= 0 ? 'text-primary' : 'text-warning' ?>">
                <?= $netProfit < 0 ? '-' : '' ?>฿<?= number_format(abs($netProfit), 2) ?>
            </div>
        </div>
    </div>
</div>

<!-- Revenue -->
<div class="table-card mb-3">
    <div class="px-3 pt-3 pb-2 bg-success bg-opacity-10">
        <h6 class="fw-bold mb-0 text-success"><i class="bi bi-arrow-up-circle me-2"></i>รายได้</h6>
    </div>
    <table class="table mb-0">
        <thead class="table-light"><tr><th>รหัสบัญชี</th><th>ชื่อบัญชี</th><th class="text-end">จำนวน</th></tr></thead>
        <tbody>
        <?php if (empty($revenue)): ?>
            <tr><td colspan="3" class="text-center text-muted py-3">ไม่มีข้อมูลรายได้ในเดือนนี้</td></tr>
        <?php else: ?>
            <?php foreach ($revenue as $r): ?>
            <tr>
                <td><span class="badge bg-light text-dark"><?= $r['code'] ?></span></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td class="text-end text-success fw-bold">฿<?= number_format($r['net'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
        <tfoot class="table-light"><tr><td colspan="2" class="fw-bold text-end">รวมรายได้</td><td class="text-end fw-bold text-success">฿<?= number_format($totalRevenue, 2) ?></td></tr></tfoot>
    </table>
</div>

<!-- Expenses -->
<div class="table-card mb-3">
    <div class="px-3 pt-3 pb-2 bg-danger bg-opacity-10">
        <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-arrow-down-circle me-2"></i>ค่าใช้จ่าย</h6>
    </div>
    <table class="table mb-0">
        <thead class="table-light"><tr><th>รหัสบัญชี</th><th>ชื่อบัญชี</th><th class="text-end">จำนวน</th></tr></thead>
        <tbody>
        <?php if (empty($expenses)): ?>
            <tr><td colspan="3" class="text-center text-muted py-3">ไม่มีข้อมูลค่าใช้จ่ายในเดือนนี้</td></tr>
        <?php else: ?>
            <?php foreach ($expenses as $e): ?>
            <tr>
                <td><span class="badge bg-light text-dark"><?= $e['code'] ?></span></td>
                <td><?= htmlspecialchars($e['name']) ?></td>
                <td class="text-end text-danger fw-bold">฿<?= number_format(abs($e['net']), 2) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
        <tfoot class="table-light"><tr><td colspan="2" class="fw-bold text-end">รวมค่าใช้จ่าย</td><td class="text-end fw-bold text-danger">฿<?= number_format($totalExpense, 2) ?></td></tr></tfoot>
    </table>
</div>

<div class="form-card d-flex justify-content-between align-items-center">
    <h5 class="mb-0 fw-bold">กำไร/ขาดทุนสุทธิ</h5>
    <div class="fs-3 fw-bold <?= $netProfit >= 0 ? 'text-primary' : 'text-danger' ?>">
        <?= $netProfit < 0 ? '(' : '' ?>฿<?= number_format(abs($netProfit), 2) ?><?= $netProfit < 0 ? ')' : '' ?>
    </div>
</div>

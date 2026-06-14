<div class="page-header">
    <h4><i class="bi bi-people-fill me-2"></i>HR Dashboard</h4>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/hr/leave" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-calendar-plus me-1"></i>ขอลา
        </a>
        <a href="<?= BASE_URL ?>/hr/ot" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-clock-history me-1"></i>ขอ OT
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people fs-4"></i></div>
                <div>
                    <div class="text-muted small">พนักงานทั้งหมด</div>
                    <div class="fs-4 fw-bold"><?= number_format($headcount) ?> คน</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-calendar-x fs-4"></i></div>
                <div>
                    <div class="text-muted small">ขอลารออนุมัติ</div>
                    <div class="fs-4 fw-bold"><?= $pendingLeaves ?> คำขอ</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-clock-history fs-4"></i></div>
                <div>
                    <div class="text-muted small">OT รออนุมัติ</div>
                    <div class="fs-4 fw-bold"><?= $pendingOT ?> คำขอ</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-wallet2 fs-4"></i></div>
                <div>
                    <div class="text-muted small">เงินเดือน</div>
                    <div class="fs-5 fw-bold"><a href="<?= BASE_URL ?>/hr/payroll" class="text-decoration-none">จัดการ →</a></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row g-3 mb-4">
    <?php
    $links = [
        ['href'=>'/hr/leaves','icon'=>'calendar-check','label'=>'การลาทั้งหมด','color'=>'primary'],
        ['href'=>'/hr/my-leaves','icon'=>'person-check','label'=>'วันลาของฉัน','color'=>'info'],
        ['href'=>'/hr/overtime','icon'=>'clock-history','label'=>'ล่วงเวลา','color'=>'warning'],
        ['href'=>'/hr/payroll','icon'=>'wallet2','label'=>'Payroll','color'=>'success'],
    ];
    foreach ($links as $l):
    ?>
    <div class="col-6 col-lg-3">
        <a href="<?= BASE_URL ?><?= $l['href'] ?>" class="btn btn-outline-<?= $l['color'] ?> w-100">
            <i class="bi bi-<?= $l['icon'] ?> me-2"></i><?= $l['label'] ?>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<!-- Recent Leave Requests -->
<div class="table-card">
    <div class="d-flex align-items-center justify-content-between px-3 pt-3 pb-2">
        <h6 class="fw-bold mb-0">คำขอลาล่าสุด</h6>
        <a href="<?= BASE_URL ?>/hr/leaves" class="btn btn-sm btn-outline-secondary">ดูทั้งหมด</a>
    </div>
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr><th>พนักงาน</th><th>ประเภทลา</th><th>ช่วงเวลา</th><th class="text-end">วัน</th><th>สถานะ</th></tr>
        </thead>
        <tbody>
        <?php if (empty($recentLeaves)): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">ไม่มีข้อมูล</td></tr>
        <?php else: ?>
            <?php
            $sc = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','cancelled'=>'secondary'];
            $sl = ['pending'=>'รออนุมัติ','approved'=>'อนุมัติ','rejected'=>'ปฏิเสธ','cancelled'=>'ยกเลิก'];
            foreach ($recentLeaves as $r):
            ?>
            <tr>
                <td><?= htmlspecialchars($r['employee_name']) ?></td>
                <td><?= htmlspecialchars($r['leave_type_name']) ?></td>
                <td class="text-muted small"><?= date('d/m/Y', strtotime($r['start_date'])) ?> — <?= date('d/m/Y', strtotime($r['end_date'])) ?></td>
                <td class="text-end"><?= $r['days_count'] ?></td>
                <td><span class="badge bg-<?= $sc[$r['status']] ?? 'secondary' ?>"><?= $sl[$r['status']] ?? $r['status'] ?></span></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

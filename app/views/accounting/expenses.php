<div class="page-header">
    <h4><i class="bi bi-credit-card-2-back me-2"></i>ค่าใช้จ่าย</h4>
    <a href="<?= BASE_URL ?>/accounting/expense" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>บันทึกค่าใช้จ่าย
    </a>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted small">รายการทั้งหมด</div>
            <div class="fs-4 fw-bold"><?= number_format($stats['total']) ?></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted small">รออนุมัติ</div>
            <div class="fs-4 fw-bold text-warning"><?= number_format($stats['pending']) ?></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted small">อนุมัติแล้ว</div>
            <div class="fs-4 fw-bold text-success"><?= number_format($stats['approved']) ?></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted small">มูลค่าที่อนุมัติ</div>
            <div class="fs-5 fw-bold">฿<?= number_format($stats['total_amount'], 0) ?></div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="mb-3 d-flex gap-2">
    <?php
    $statusTabs = [''=> 'ทั้งหมด','pending'=>'รออนุมัติ','approved'=>'อนุมัติแล้ว','paid'=>'จ่ายแล้ว','rejected'=>'ปฏิเสธ'];
    foreach ($statusTabs as $k => $v):
    ?>
    <a href="<?= BASE_URL ?>/accounting/expenses<?= $k ? '?status='.$k : '' ?>" class="btn btn-sm <?= $status === $k ? 'btn-primary' : 'btn-outline-secondary' ?>"><?= $v ?></a>
    <?php endforeach; ?>
</div>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>เลขที่</th>
                <th>หมวด</th>
                <th>รายละเอียด</th>
                <th>วันที่</th>
                <th>ผู้บันทึก</th>
                <th class="text-end">จำนวน</th>
                <th>สถานะ</th>
                <th class="text-end">จัดการ</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($expenses)): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">ไม่พบข้อมูล</td></tr>
        <?php else: ?>
            <?php
            $sc = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','paid'=>'primary'];
            $sl = ['pending'=>'รออนุมัติ','approved'=>'อนุมัติ','rejected'=>'ปฏิเสธ','paid'=>'จ่ายแล้ว'];
            foreach ($expenses as $e):
            ?>
            <tr>
                <td class="small text-muted"><?= htmlspecialchars($e['expense_number']) ?></td>
                <td><span class="badge bg-light text-dark"><?= htmlspecialchars($categories[$e['category']] ?? $e['category']) ?></span></td>
                <td><?= htmlspecialchars($e['description']) ?></td>
                <td class="text-muted small"><?= date('d/m/Y', strtotime($e['expense_date'])) ?></td>
                <td class="text-muted small"><?= htmlspecialchars($e['created_by_name'] ?? '-') ?></td>
                <td class="text-end fw-bold">฿<?= number_format($e['amount'], 2) ?></td>
                <td><span class="badge bg-<?= $sc[$e['status']] ?? 'secondary' ?>"><?= $sl[$e['status']] ?? $e['status'] ?></span></td>
                <td class="text-end">
                    <?php if ($e['status'] === 'pending' && ($currentUser['role'] ?? '') === 'admin'): ?>
                    <form method="post" action="<?= BASE_URL ?>/accounting/approve-expense/<?= $e['id'] ?>" class="d-inline">
                        <button class="btn btn-sm btn-outline-success" title="อนุมัติ"><i class="bi bi-check-lg"></i></button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

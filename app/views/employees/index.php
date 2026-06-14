<div class="page-header">
    <h4><i class="bi bi-person-badge me-2"></i>พนักงาน</h4>
    <a href="<?= BASE_URL ?>/employees/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>เพิ่มพนักงาน
    </a>
</div>

<div class="table-card">
    <div class="p-3 border-bottom">
        <form method="get" class="d-flex gap-2" style="max-width:400px">
            <input type="text" name="search" class="form-control" placeholder="ค้นหาพนักงาน..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-primary px-3"><i class="bi bi-search"></i></button>
            <?php if ($search): ?><a href="<?= BASE_URL ?>/employees" class="btn btn-outline-secondary">ล้าง</a><?php endif; ?>
        </form>
    </div>
    <table class="table table-hover">
        <thead>
            <tr><th>ชื่อ</th><th>แผนก</th><th>ตำแหน่ง</th><th>อีเมล</th><th>โทรศัพท์</th><th>เงินเดือน</th><th>สถานะ</th><th>จัดการ</th></tr>
        </thead>
        <tbody>
        <?php foreach ($employees as $e): ?>
        <tr>
            <td><?= htmlspecialchars($e['name']) ?></td>
            <td><?= htmlspecialchars($e['department'] ?? '-') ?></td>
            <td><?= htmlspecialchars($e['position'] ?? '-') ?></td>
            <td><?= htmlspecialchars($e['email']) ?></td>
            <td><?= htmlspecialchars($e['phone'] ?? '-') ?></td>
            <td><?= number_format($e['salary'], 2) ?></td>
            <td>
                <span class="badge bg-<?= $e['status'] === 'active' ? 'success' : 'secondary' ?>">
                    <?= $e['status'] === 'active' ? 'ทำงาน' : 'ลาออก' ?>
                </span>
            </td>
            <td>
                <a href="<?= BASE_URL ?>/employees/edit/<?= $e['id'] ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="<?= BASE_URL ?>/employees/delete/<?= $e['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete ms-1">
                    <i class="bi bi-trash"></i>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($employees)): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">ไม่พบข้อมูลพนักงาน</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

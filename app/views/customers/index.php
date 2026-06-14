<div class="page-header">
    <h4><i class="bi bi-people me-2"></i>ลูกค้า</h4>
    <a href="<?= BASE_URL ?>/customers/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>เพิ่มลูกค้า
    </a>
</div>

<div class="table-card">
    <div class="p-3 border-bottom">
        <form method="get" class="d-flex gap-2" style="max-width:400px">
            <input type="text" name="search" class="form-control" placeholder="ค้นหาลูกค้า..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-primary px-3"><i class="bi bi-search"></i></button>
            <?php if ($search): ?><a href="<?= BASE_URL ?>/customers" class="btn btn-outline-secondary">ล้าง</a><?php endif; ?>
        </form>
    </div>
    <table class="table table-hover">
        <thead>
            <tr><th>ชื่อ</th><th>บริษัท</th><th>อีเมล</th><th>โทรศัพท์</th><th>สถานะ</th><th>จัดการ</th></tr>
        </thead>
        <tbody>
        <?php foreach ($customers as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= htmlspecialchars($c['company'] ?? '-') ?></td>
            <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
            <td><?= htmlspecialchars($c['phone'] ?? '-') ?></td>
            <td>
                <span class="badge bg-<?= $c['status'] === 'active' ? 'success' : 'secondary' ?>">
                    <?= $c['status'] === 'active' ? 'ใช้งาน' : 'ปิดใช้งาน' ?>
                </span>
            </td>
            <td>
                <a href="<?= BASE_URL ?>/customers/edit/<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="<?= BASE_URL ?>/customers/delete/<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete ms-1">
                    <i class="bi bi-trash"></i>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($customers)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">ไม่พบข้อมูลลูกค้า</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

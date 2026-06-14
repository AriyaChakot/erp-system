<div class="page-header">
    <h4><i class="bi bi-box-seam me-2"></i>สินค้า</h4>
    <a href="<?= BASE_URL ?>/products/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>เพิ่มสินค้า
    </a>
</div>

<div class="table-card">
    <div class="p-3 border-bottom">
        <form method="get" class="d-flex gap-2" style="max-width:400px">
            <input type="text" name="search" class="form-control" placeholder="ค้นหาสินค้า..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-primary px-3"><i class="bi bi-search"></i></button>
            <?php if ($search): ?><a href="<?= BASE_URL ?>/products" class="btn btn-outline-secondary">ล้าง</a><?php endif; ?>
        </form>
    </div>
    <table class="table table-hover">
        <thead>
            <tr><th>รหัส</th><th>ชื่อสินค้า</th><th>หมวดหมู่</th><th>ราคาขาย</th><th>ต้นทุน</th><th>คงเหลือ</th><th>สถานะ</th><th>จัดการ</th></tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($p['code']) ?></span></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['category']) ?></td>
            <td><?= number_format($p['price'], 2) ?></td>
            <td><?= number_format($p['cost'], 2) ?></td>
            <td class="<?= $p['stock'] <= 10 ? 'low-stock' : '' ?>">
                <?= number_format($p['stock']) ?> <?= htmlspecialchars($p['unit']) ?>
            </td>
            <td>
                <span class="badge bg-<?= $p['status'] === 'active' ? 'success' : 'secondary' ?>">
                    <?= $p['status'] === 'active' ? 'ใช้งาน' : 'ปิดใช้งาน' ?>
                </span>
            </td>
            <td>
                <a href="<?= BASE_URL ?>/products/edit/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="<?= BASE_URL ?>/products/delete/<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete ms-1">
                    <i class="bi bi-trash"></i>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">ไม่พบข้อมูลสินค้า</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

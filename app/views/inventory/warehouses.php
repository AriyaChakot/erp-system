<div class="page-header">
    <h4><i class="bi bi-building me-2"></i>จัดการคลังสินค้า</h4>
    <a href="<?= BASE_URL ?>/inventory/warehouse/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>เพิ่มคลังใหม่
    </a>
</div>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>รหัส</th>
                <th>ชื่อคลัง</th>
                <th>ที่ตั้ง</th>
                <th>ผู้ดูแล</th>
                <th>สถานะ</th>
                <th class="text-end">จัดการ</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($warehouses)): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">ยังไม่มีข้อมูลคลัง</td></tr>
        <?php else: ?>
            <?php foreach ($warehouses as $w): ?>
            <tr>
                <td><span class="badge bg-light text-dark fw-bold"><?= htmlspecialchars($w['code']) ?></span></td>
                <td class="fw-medium"><?= htmlspecialchars($w['name']) ?></td>
                <td class="text-muted"><?= htmlspecialchars($w['location'] ?? '-') ?></td>
                <td><?= htmlspecialchars($w['manager_name'] ?? '-') ?></td>
                <td>
                    <span class="badge bg-<?= $w['status'] === 'active' ? 'success' : 'secondary' ?>">
                        <?= $w['status'] === 'active' ? 'ใช้งาน' : 'ปิดการใช้งาน' ?>
                    </span>
                </td>
                <td class="text-end">
                    <a href="<?= BASE_URL ?>/inventory/warehouse/edit/<?= $w['id'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <a href="<?= BASE_URL ?>/inventory/stock?warehouse_id=<?= $w['id'] ?>" class="btn btn-sm btn-outline-secondary ms-1">
                        <i class="bi bi-box-seam"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

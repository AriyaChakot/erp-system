<div class="page-header">
    <h4><i class="bi bi-building me-2"></i>Vendors</h4>
    <a href="<?= BASE_URL ?>/purchasing/vendor" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>เพิ่ม Vendor
    </a>
</div>

<div class="table-card mb-3">
    <form method="get" class="p-3 d-flex gap-2" style="max-width:400px">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="ค้นหา vendor..." value="<?= htmlspecialchars($search ?? '') ?>">
        <button class="btn btn-outline-primary btn-sm px-3"><i class="bi bi-search"></i></button>
        <?php if ($search): ?>
        <a href="<?= BASE_URL ?>/purchasing/vendors" class="btn btn-outline-secondary btn-sm">ล้าง</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>รหัส</th>
                <th>ชื่อบริษัท</th>
                <th>ผู้ติดต่อ</th>
                <th>โทรศัพท์</th>
                <th>เครดิต (วัน)</th>
                <th class="text-end">PO ทั้งหมด</th>
                <th class="text-end">มูลค่าซื้อรวม</th>
                <th>สถานะ</th>
                <th class="text-end">จัดการ</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($vendors)): ?>
            <tr><td colspan="9" class="text-center text-muted py-4">ไม่พบข้อมูล</td></tr>
        <?php else: ?>
            <?php foreach ($vendors as $v): ?>
            <tr>
                <td><span class="badge bg-light text-dark"><?= htmlspecialchars($v['code']) ?></span></td>
                <td class="fw-medium"><?= htmlspecialchars($v['name']) ?></td>
                <td class="text-muted small"><?= htmlspecialchars($v['contact_person'] ?? '-') ?></td>
                <td class="text-muted small"><?= htmlspecialchars($v['phone'] ?? '-') ?></td>
                <td class="text-center"><?= $v['payment_terms'] ?></td>
                <td class="text-end"><?= number_format($v['po_count'] ?? 0) ?></td>
                <td class="text-end">฿<?= number_format($v['total_value'] ?? 0, 2) ?></td>
                <td><span class="badge bg-<?= $v['status'] === 'active' ? 'success' : 'secondary' ?>"><?= $v['status'] === 'active' ? 'ใช้งาน' : 'ปิด' ?></span></td>
                <td class="text-end">
                    <a href="<?= BASE_URL ?>/purchasing/vendor/edit/<?= $v['id'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

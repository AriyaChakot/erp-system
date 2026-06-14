<div class="page-header">
    <h4><i class="bi bi-exclamation-triangle me-2 text-warning"></i>สินค้าใกล้หมด / ต้องสั่งซื้อ</h4>
    <a href="<?= BASE_URL ?>/purchasing/create" class="btn btn-primary btn-sm">
        <i class="bi bi-cart-plus me-1"></i>สร้างใบสั่งซื้อ
    </a>
</div>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>รหัสสินค้า</th>
                <th>ชื่อสินค้า</th>
                <th>คลัง</th>
                <th class="text-end">Stock ปัจจุบัน</th>
                <th class="text-end">ขั้นต่ำ (Reorder)</th>
                <th class="text-end">ต้องสั่งเพิ่ม</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($items)): ?>
            <tr>
                <td colspan="6" class="text-center py-5">
                    <i class="bi bi-check-circle text-success fs-1 d-block mb-2"></i>
                    <div class="text-muted">สินค้าทุกรายการมี Stock เพียงพอ</div>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
            <tr class="table-warning">
                <td><span class="badge bg-light text-dark"><?= htmlspecialchars($item['product_code']) ?></span></td>
                <td class="fw-medium"><?= htmlspecialchars($item['product_name']) ?></td>
                <td class="text-muted small"><?= htmlspecialchars($item['warehouse_name']) ?></td>
                <td class="text-end">
                    <span class="fw-bold text-danger">
                        <?= number_format($item['quantity']) ?> <?= htmlspecialchars($item['unit']) ?>
                    </span>
                </td>
                <td class="text-end text-muted"><?= number_format($item['min_quantity']) ?></td>
                <td class="text-end text-primary fw-bold">
                    +<?= number_format(max(0, $item['min_quantity'] * 2 - $item['quantity'])) ?>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>/purchasing/create?product_id=<?= $item['product_id'] ?>"
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-cart-plus"></i> สั่งซื้อ
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

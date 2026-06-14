<div class="page-header">
    <h4><i class="bi bi-box-seam me-2"></i>ภาพรวม Stock</h4>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/inventory/adjust" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus-slash-minus me-1"></i>ปรับ Stock
        </a>
        <a href="<?= BASE_URL ?>/inventory/transfer" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left-right me-1"></i>โอนคลัง
        </a>
    </div>
</div>

<!-- Filter -->
<div class="table-card mb-3">
    <form method="get" class="p-3 d-flex gap-2 align-items-end flex-wrap">
        <div>
            <label class="form-label small mb-1">คลังสินค้า</label>
            <select name="warehouse_id" class="form-select form-select-sm" style="min-width:160px">
                <option value="0">ทุกคลัง</option>
                <?php foreach ($warehouses as $wh): ?>
                <option value="<?= $wh['id'] ?>" <?= $selectedWH == $wh['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($wh['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-outline-primary btn-sm">กรอง</button>
        <?php if ($selectedWH): ?>
        <a href="<?= BASE_URL ?>/inventory/stock" class="btn btn-outline-secondary btn-sm">ล้าง</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>รหัสสินค้า</th>
                <th>ชื่อสินค้า</th>
                <th>คลัง</th>
                <th class="text-end">คงเหลือ</th>
                <th class="text-end">ขั้นต่ำ</th>
                <th class="text-end">ต้นทุนต่อหน่วย</th>
                <th>อัพเดทล่าสุด</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($items)): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">ไม่พบข้อมูล</td></tr>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
            <?php $isLow = $item['quantity'] <= $item['min_quantity']; ?>
            <tr class="<?= $isLow ? 'table-warning' : '' ?>">
                <td><span class="badge bg-light text-dark"><?= htmlspecialchars($item['product_code']) ?></span></td>
                <td class="fw-medium"><?= htmlspecialchars($item['product_name']) ?></td>
                <td class="text-muted small"><?= htmlspecialchars($item['warehouse_name']) ?></td>
                <td class="text-end fw-bold <?= $isLow ? 'text-danger' : '' ?>">
                    <?= number_format($item['quantity']) ?> <?= htmlspecialchars($item['unit']) ?>
                    <?php if ($isLow): ?><i class="bi bi-exclamation-triangle-fill text-warning ms-1"></i><?php endif; ?>
                </td>
                <td class="text-end text-muted"><?= number_format($item['min_quantity']) ?></td>
                <td class="text-end text-muted small">฿<?= number_format($item['product_cost'], 2) ?></td>
                <td class="text-muted small"><?= date('d/m/Y', strtotime($item['last_updated'])) ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/inventory/movements?product_id=<?= $item['product_id'] ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-clock-history"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

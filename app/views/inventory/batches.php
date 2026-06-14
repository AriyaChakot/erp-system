<div class="page-header">
    <h4><i class="bi bi-layers me-2"></i>Batch Tracking</h4>
</div>

<?php if (!empty($expiring)): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <div>มี <strong><?= count($expiring) ?></strong> batch ที่จะหมดอายุภายใน 30 วัน</div>
</div>
<?php endif; ?>

<!-- Filter -->
<div class="table-card mb-3">
    <form method="get" class="p-3 d-flex gap-2 align-items-end flex-wrap">
        <div>
            <label class="form-label small mb-1">สินค้า</label>
            <select name="product_id" class="form-select form-select-sm" style="min-width:200px">
                <option value="">ทุกสินค้า</option>
                <?php foreach ($products as $p): ?>
                <option value="<?= $p['id'] ?>" <?= ($filters['product_id'] ?? 0) == $p['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label small mb-1">คลัง</label>
            <select name="warehouse_id" class="form-select form-select-sm" style="min-width:140px">
                <option value="">ทุกคลัง</option>
                <?php foreach ($warehouses as $wh): ?>
                <option value="<?= $wh['id'] ?>" <?= ($filters['warehouse_id'] ?? 0) == $wh['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($wh['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-outline-primary btn-sm">กรอง</button>
        <?php if (!empty($filters)): ?>
        <a href="<?= BASE_URL ?>/inventory/batches" class="btn btn-outline-secondary btn-sm">ล้าง</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>Batch No.</th>
                <th>สินค้า</th>
                <th>คลัง</th>
                <th class="text-end">จำนวน</th>
                <th class="text-end">ต้นทุน/หน่วย</th>
                <th>วันรับสินค้า</th>
                <th>วันหมดอายุ</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($batches)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">ไม่พบข้อมูล</td></tr>
        <?php else: ?>
            <?php foreach ($batches as $b): ?>
            <?php
                $isExpiring = $b['expiry_date'] && strtotime($b['expiry_date']) <= strtotime('+30 days');
                $isExpired  = $b['expiry_date'] && strtotime($b['expiry_date']) < strtotime('today');
            ?>
            <tr class="<?= $isExpired ? 'table-danger' : ($isExpiring ? 'table-warning' : '') ?>">
                <td><code><?= htmlspecialchars($b['batch_number']) ?></code></td>
                <td>
                    <div class="fw-medium"><?= htmlspecialchars($b['product_name']) ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($b['product_code']) ?></div>
                </td>
                <td class="text-muted small"><?= htmlspecialchars($b['warehouse_name']) ?></td>
                <td class="text-end fw-bold"><?= number_format($b['quantity']) ?> <?= htmlspecialchars($b['unit'] ?? '') ?></td>
                <td class="text-end text-muted">฿<?= number_format($b['unit_cost'], 2) ?></td>
                <td class="small"><?= date('d/m/Y', strtotime($b['received_date'])) ?></td>
                <td class="small">
                    <?php if ($b['expiry_date']): ?>
                        <?php if ($isExpired): ?>
                            <span class="badge bg-danger">หมดอายุแล้ว</span>
                        <?php elseif ($isExpiring): ?>
                            <span class="badge bg-warning text-dark"><?= date('d/m/Y', strtotime($b['expiry_date'])) ?></span>
                        <?php else: ?>
                            <?= date('d/m/Y', strtotime($b['expiry_date'])) ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

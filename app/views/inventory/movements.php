<?php
$typeLabel = ['in'=>'รับเข้า','out'=>'จ่ายออก','transfer'=>'โอนคลัง','adjustment'=>'ปรับ Stock'];
$typeColor = ['in'=>'success','out'=>'danger','transfer'=>'info','adjustment'=>'warning'];
?>
<div class="page-header">
    <h4><i class="bi bi-arrow-left-right me-2"></i>Stock Movements</h4>
</div>

<!-- Filter -->
<div class="table-card mb-3">
    <form method="get" class="p-3 d-flex gap-2 align-items-end flex-wrap">
        <div>
            <label class="form-label small mb-1">ประเภท</label>
            <select name="type" class="form-select form-select-sm">
                <option value="">ทุกประเภท</option>
                <?php foreach ($typeLabel as $k => $v): ?>
                <option value="<?= $k ?>" <?= ($filters['type'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
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
        <div>
            <label class="form-label small mb-1">ตั้งแต่</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
        </div>
        <div>
            <label class="form-label small mb-1">ถึง</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
        </div>
        <button class="btn btn-outline-primary btn-sm">กรอง</button>
        <?php if (!empty($filters)): ?>
        <a href="<?= BASE_URL ?>/inventory/movements" class="btn btn-outline-secondary btn-sm">ล้าง</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>วันที่/เวลา</th>
                <th>ประเภท</th>
                <th>สินค้า</th>
                <th>คลัง</th>
                <th>อ้างอิง</th>
                <th class="text-end">จำนวน</th>
                <th class="text-end">คงเหลือ</th>
                <th>หมายเหตุ</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($movements)): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">ไม่พบข้อมูล</td></tr>
        <?php else: ?>
            <?php foreach ($movements as $m): ?>
            <tr>
                <td class="text-muted small text-nowrap"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                <td>
                    <span class="badge bg-<?= $typeColor[$m['movement_type']] ?? 'secondary' ?>">
                        <?= $typeLabel[$m['movement_type']] ?? $m['movement_type'] ?>
                    </span>
                </td>
                <td>
                    <div><?= htmlspecialchars($m['product_name']) ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($m['product_code']) ?></div>
                </td>
                <td class="small">
                    <?= htmlspecialchars($m['warehouse_name']) ?>
                    <?php if ($m['dest_warehouse_name'] ?? null): ?>
                        <i class="bi bi-arrow-right text-muted"></i>
                        <?= htmlspecialchars($m['dest_warehouse_name']) ?>
                    <?php endif; ?>
                </td>
                <td class="text-muted small"><?= htmlspecialchars($m['reference_type'] ?? '-') ?></td>
                <td class="text-end fw-bold <?= $m['quantity'] < 0 ? 'text-danger' : 'text-success' ?>">
                    <?= $m['quantity'] > 0 ? '+' : '' ?><?= number_format($m['quantity']) ?>
                </td>
                <td class="text-end"><?= number_format($m['balance_after'] ?? 0) ?></td>
                <td class="text-muted small"><?= htmlspecialchars(mb_strimwidth($m['notes'] ?? '', 0, 40, '...')) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

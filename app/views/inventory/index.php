<div class="page-header">
    <h4><i class="bi bi-boxes me-2"></i>คลังสินค้า</h4>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/inventory/adjust" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus-slash-minus me-1"></i>ปรับ Stock
        </a>
        <a href="<?= BASE_URL ?>/inventory/transfer" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left-right me-1"></i>โอนคลัง
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-box-seam fs-4"></i></div>
                <div>
                    <div class="text-muted small">รายการสินค้า (SKU)</div>
                    <div class="fs-4 fw-bold"><?= number_format($totalSKU) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-currency-dollar fs-4"></i></div>
                <div>
                    <div class="text-muted small">มูลค่าสินค้าคงคลัง</div>
                    <div class="fs-4 fw-bold">฿<?= number_format($totalValue, 2) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle fs-4"></i></div>
                <div>
                    <div class="text-muted small">สินค้าใกล้หมด</div>
                    <div class="fs-4 fw-bold"><?= number_format($lowStockCount) ?> รายการ</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row g-3 mb-4">
    <?php foreach ($warehouses as $wh): ?>
    <div class="col-md-6 col-lg-3">
        <a href="<?= BASE_URL ?>/inventory/stock?warehouse_id=<?= $wh['id'] ?>" class="text-decoration-none">
            <div class="form-card text-center py-3">
                <i class="bi bi-building text-primary fs-3"></i>
                <div class="fw-bold mt-1"><?= htmlspecialchars($wh['name']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars($wh['location'] ?? '') ?></div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
    <div class="col-md-6 col-lg-3">
        <a href="<?= BASE_URL ?>/inventory/warehouses" class="text-decoration-none">
            <div class="form-card text-center py-3 border-dashed">
                <i class="bi bi-plus-circle text-muted fs-3"></i>
                <div class="text-muted mt-1 small">จัดการคลัง</div>
            </div>
        </a>
    </div>
</div>

<!-- Recent Movements -->
<div class="table-card">
    <div class="d-flex align-items-center justify-content-between px-3 pt-3 pb-2">
        <h6 class="fw-bold mb-0">การเคลื่อนไหวล่าสุด</h6>
        <a href="<?= BASE_URL ?>/inventory/movements" class="btn btn-sm btn-outline-secondary">ดูทั้งหมด</a>
    </div>
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>วันที่</th>
                <th>ประเภท</th>
                <th>สินค้า</th>
                <th>คลัง</th>
                <th class="text-end">จำนวน</th>
                <th class="text-end">คงเหลือ</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($recentMovements)): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">ยังไม่มีข้อมูลการเคลื่อนไหว</td></tr>
        <?php else: ?>
            <?php
            $typeLabel = ['in'=>'รับเข้า','out'=>'จ่ายออก','transfer'=>'โอนคลัง','adjustment'=>'ปรับ'];
            $typeColor = ['in'=>'success','out'=>'danger','transfer'=>'info','adjustment'=>'warning'];
            foreach ($recentMovements as $m):
            ?>
            <tr>
                <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                <td><span class="badge bg-<?= $typeColor[$m['movement_type']] ?? 'secondary' ?>"><?= $typeLabel[$m['movement_type']] ?? $m['movement_type'] ?></span></td>
                <td><?= htmlspecialchars($m['product_name']) ?></td>
                <td><?= htmlspecialchars($m['warehouse_name']) ?></td>
                <td class="text-end <?= $m['quantity'] < 0 ? 'text-danger' : 'text-success' ?>">
                    <?= $m['quantity'] > 0 ? '+' : '' ?><?= number_format($m['quantity']) ?>
                </td>
                <td class="text-end"><?= number_format($m['balance_after'] ?? 0) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

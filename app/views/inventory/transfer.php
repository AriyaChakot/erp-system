<div class="page-header">
    <h4><i class="bi bi-arrow-left-right me-2"></i>โอน Stock ระหว่างคลัง</h4>
    <a href="<?= BASE_URL ?>/inventory" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>กลับ
    </a>
</div>

<div class="form-card" style="max-width:600px">
    <form method="post" action="<?= BASE_URL ?>/inventory/transfer">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-medium">สินค้า <span class="text-danger">*</span></label>
                <select name="product_id" class="form-select" required>
                    <option value="">— เลือกสินค้า —</option>
                    <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['code'] . ' - ' . $p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-medium">คลังต้นทาง <span class="text-danger">*</span></label>
                <select name="from_warehouse_id" class="form-select" required>
                    <option value="">— เลือกคลัง —</option>
                    <?php foreach ($warehouses as $wh): ?>
                    <option value="<?= $wh['id'] ?>"><?= htmlspecialchars($wh['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end justify-content-center pb-1">
                <i class="bi bi-arrow-right fs-4 text-muted"></i>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-medium">คลังปลายทาง <span class="text-danger">*</span></label>
                <select name="to_warehouse_id" class="form-select" required>
                    <option value="">— เลือกคลัง —</option>
                    <?php foreach ($warehouses as $wh): ?>
                    <option value="<?= $wh['id'] ?>"><?= htmlspecialchars($wh['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">จำนวนที่โอน <span class="text-danger">*</span></label>
                <input type="number" name="quantity" class="form-control" min="1" required placeholder="0">
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">หมายเหตุ</label>
                <input type="text" name="notes" class="form-control" placeholder="เหตุผลการโอนคลัง...">
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>ยืนยันการโอน
            </button>
            <a href="<?= BASE_URL ?>/inventory" class="btn btn-outline-secondary">ยกเลิก</a>
        </div>
    </form>
</div>

<div class="page-header">
    <h4><i class="bi bi-plus-slash-minus me-2"></i>ปรับ Stock</h4>
    <a href="<?= BASE_URL ?>/inventory/stock" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>กลับ
    </a>
</div>

<div class="form-card" style="max-width:560px">
    <form method="post" action="<?= BASE_URL ?>/inventory/adjust">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-medium">สินค้า <span class="text-danger">*</span></label>
                <select name="product_id" class="form-select" required id="productSelect">
                    <option value="">— เลือกสินค้า —</option>
                    <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['code'] . ' - ' . $p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">คลังสินค้า <span class="text-danger">*</span></label>
                <select name="warehouse_id" class="form-select" required>
                    <option value="">— เลือกคลัง —</option>
                    <?php foreach ($warehouses as $wh): ?>
                    <option value="<?= $wh['id'] ?>"><?= htmlspecialchars($wh['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">ทิศทาง <span class="text-danger">*</span></label>
                <select name="direction" class="form-select" required>
                    <option value="+">+ เพิ่ม</option>
                    <option value="-">- ลด</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">จำนวน <span class="text-danger">*</span></label>
                <input type="number" name="quantity" class="form-control" min="1" required placeholder="0">
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">หมายเหตุ / เหตุผล</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="ระบุเหตุผลการปรับ Stock..."></textarea>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>บันทึกการปรับ
            </button>
            <a href="<?= BASE_URL ?>/inventory/stock" class="btn btn-outline-secondary">ยกเลิก</a>
        </div>
    </form>
</div>

<div class="page-header">
    <h4><i class="bi bi-box-seam me-2"></i><?= $product ? 'แก้ไขสินค้า' : 'เพิ่มสินค้า' ?></h4>
    <a href="<?= BASE_URL ?>/products" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>กลับ
    </a>
</div>

<div class="form-card" style="max-width:720px">
    <form method="post" action="<?= BASE_URL ?>/products/<?= $product ? 'update/'.$product['id'] : 'store' ?>">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">รหัสสินค้า *</label>
                <input type="text" name="code" class="form-control" required
                    value="<?= htmlspecialchars($product['code'] ?? '') ?>" placeholder="P001">
            </div>
            <div class="col-md-8">
                <label class="form-label">ชื่อสินค้า *</label>
                <input type="text" name="name" class="form-control" required
                    value="<?= htmlspecialchars($product['name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">หมวดหมู่</label>
                <input type="text" name="category" class="form-control"
                    value="<?= htmlspecialchars($product['category'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">หน่วย</label>
                <input type="text" name="unit" class="form-control"
                    value="<?= htmlspecialchars($product['unit'] ?? 'unit') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">ราคาขาย *</label>
                <input type="number" name="price" class="form-control" step="0.01" min="0" required
                    value="<?= $product['price'] ?? '0' ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">ราคาต้นทุน</label>
                <input type="number" name="cost" class="form-control" step="0.01" min="0"
                    value="<?= $product['cost'] ?? '0' ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">จำนวนคงเหลือ</label>
                <input type="number" name="stock" class="form-control" min="0"
                    value="<?= $product['stock'] ?? '0' ?>">
            </div>
            <div class="col-12">
                <label class="form-label">คำอธิบาย</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">สถานะ</label>
                <select name="status" class="form-select">
                    <option value="active" <?= ($product['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>ใช้งาน</option>
                    <option value="inactive" <?= ($product['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>ปิดใช้งาน</option>
                </select>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check-lg me-1"></i>บันทึก
            </button>
            <a href="<?= BASE_URL ?>/products" class="btn btn-outline-secondary">ยกเลิก</a>
        </div>
    </form>
</div>

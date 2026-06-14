<div class="page-header">
    <h4><i class="bi bi-building me-2"></i><?= $vendor ? 'แก้ไข Vendor' : 'เพิ่ม Vendor' ?></h4>
    <a href="<?= BASE_URL ?>/purchasing/vendors" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>กลับ
    </a>
</div>

<div class="form-card" style="max-width:720px">
    <form method="post" action="<?= BASE_URL ?>/purchasing/vendor<?= $vendor ? '/edit/' . $vendor['id'] : '' ?>">
        <div class="row g-3">
            <?php if (!$vendor): ?>
            <div class="col-md-4">
                <label class="form-label fw-medium">รหัส Vendor <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control text-uppercase" required
                    value="<?= htmlspecialchars($nextCode ?? '') ?>">
            </div>
            <?php endif; ?>
            <div class="col-md-<?= $vendor ? '6' : '8' ?>">
                <label class="form-label fw-medium">ชื่อบริษัท/ผู้จำหน่าย <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required
                    value="<?= htmlspecialchars($vendor['name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">ผู้ติดต่อ</label>
                <input type="text" name="contact_person" class="form-control"
                    value="<?= htmlspecialchars($vendor['contact_person'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">อีเมล</label>
                <input type="email" name="email" class="form-control"
                    value="<?= htmlspecialchars($vendor['email'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">โทรศัพท์</label>
                <input type="text" name="phone" class="form-control"
                    value="<?= htmlspecialchars($vendor['phone'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">เลขประจำตัวผู้เสียภาษี</label>
                <input type="text" name="tax_id" class="form-control"
                    value="<?= htmlspecialchars($vendor['tax_id'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">เครดิต (วัน)</label>
                <input type="number" name="payment_terms" class="form-control" min="0"
                    value="<?= htmlspecialchars($vendor['payment_terms'] ?? '30') ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">ที่อยู่</label>
                <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($vendor['address'] ?? '') ?></textarea>
            </div>
            <?php if ($vendor): ?>
            <div class="col-md-4">
                <label class="form-label fw-medium">สถานะ</label>
                <select name="status" class="form-select">
                    <option value="active"   <?= ($vendor['status'] ?? '') === 'active'   ? 'selected' : '' ?>>ใช้งาน</option>
                    <option value="inactive" <?= ($vendor['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>ปิดการใช้งาน</option>
                </select>
            </div>
            <?php endif; ?>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>บันทึก</button>
            <a href="<?= BASE_URL ?>/purchasing/vendors" class="btn btn-outline-secondary">ยกเลิก</a>
        </div>
    </form>
</div>

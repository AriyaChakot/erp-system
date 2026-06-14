<div class="page-header">
    <h4><i class="bi bi-building me-2"></i><?= $warehouse ? 'แก้ไขคลัง' : 'เพิ่มคลังใหม่' ?></h4>
    <a href="<?= BASE_URL ?>/inventory/warehouses" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>กลับ
    </a>
</div>

<div class="form-card" style="max-width:600px">
    <form method="post" action="<?= BASE_URL ?>/inventory/warehouse<?= $warehouse ? '/update/' . $warehouse['id'] : '/store' ?>">
        <div class="row g-3">
            <?php if (!$warehouse): ?>
            <div class="col-md-4">
                <label class="form-label fw-medium">รหัสคลัง <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" required
                    value="<?= htmlspecialchars($nextCode ?? '') ?>"
                    placeholder="เช่น WH-03">
            </div>
            <?php endif; ?>
            <div class="col-md-<?= $warehouse ? '12' : '8' ?>">
                <label class="form-label fw-medium">ชื่อคลัง <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required
                    value="<?= htmlspecialchars($warehouse['name'] ?? '') ?>"
                    placeholder="เช่น คลังกลาง">
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">ที่ตั้ง</label>
                <input type="text" name="location" class="form-control"
                    value="<?= htmlspecialchars($warehouse['location'] ?? '') ?>"
                    placeholder="เช่น อาคาร A ชั้น 1">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">ผู้ดูแลคลัง</label>
                <select name="manager_id" class="form-select">
                    <option value="">— ไม่ระบุ —</option>
                    <?php foreach ($employees as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= ($warehouse['manager_id'] ?? '') == $e['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($warehouse): ?>
            <div class="col-md-6">
                <label class="form-label fw-medium">สถานะ</label>
                <select name="status" class="form-select">
                    <option value="active"   <?= ($warehouse['status'] ?? '') === 'active'   ? 'selected' : '' ?>>ใช้งาน</option>
                    <option value="inactive" <?= ($warehouse['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>ปิดการใช้งาน</option>
                </select>
            </div>
            <?php endif; ?>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>บันทึก
            </button>
            <a href="<?= BASE_URL ?>/inventory/warehouses" class="btn btn-outline-secondary">ยกเลิก</a>
        </div>
    </form>
</div>

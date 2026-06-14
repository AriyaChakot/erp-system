<div class="page-header">
    <h4><i class="bi bi-person-badge me-2"></i><?= $employee ? 'แก้ไขพนักงาน' : 'เพิ่มพนักงาน' ?></h4>
    <a href="<?= BASE_URL ?>/employees" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>กลับ</a>
</div>

<div class="form-card" style="max-width:720px">
    <form method="post" action="<?= BASE_URL ?>/employees/<?= $employee ? 'update/'.$employee['id'] : 'store' ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">ชื่อ-นามสกุล *</label>
                <input type="text" name="name" class="form-control" required
                    value="<?= htmlspecialchars($employee['name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">อีเมล *</label>
                <input type="email" name="email" class="form-control" required
                    value="<?= htmlspecialchars($employee['email'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">โทรศัพท์</label>
                <input type="text" name="phone" class="form-control"
                    value="<?= htmlspecialchars($employee['phone'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">แผนก</label>
                <input type="text" name="department" class="form-control"
                    value="<?= htmlspecialchars($employee['department'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">ตำแหน่ง</label>
                <input type="text" name="position" class="form-control"
                    value="<?= htmlspecialchars($employee['position'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">เงินเดือน</label>
                <input type="number" name="salary" class="form-control" step="0.01" min="0"
                    value="<?= $employee['salary'] ?? '0' ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">วันที่เริ่มงาน</label>
                <input type="date" name="hire_date" class="form-control"
                    value="<?= $employee['hire_date'] ?? '' ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">สถานะ</label>
                <select name="status" class="form-select">
                    <option value="active" <?= ($employee['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>ทำงาน</option>
                    <option value="inactive" <?= ($employee['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>ลาออก</option>
                </select>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>บันทึก</button>
            <a href="<?= BASE_URL ?>/employees" class="btn btn-outline-secondary">ยกเลิก</a>
        </div>
    </form>
</div>

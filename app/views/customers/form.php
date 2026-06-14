<div class="page-header">
    <h4><i class="bi bi-people me-2"></i><?= $customer ? 'แก้ไขลูกค้า' : 'เพิ่มลูกค้า' ?></h4>
    <a href="<?= BASE_URL ?>/customers" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>กลับ</a>
</div>

<div class="form-card" style="max-width:640px">
    <form method="post" action="<?= BASE_URL ?>/customers/<?= $customer ? 'update/'.$customer['id'] : 'store' ?>">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">ชื่อ-นามสกุล / ชื่อกิจการ *</label>
                <input type="text" name="name" class="form-control" required
                    value="<?= htmlspecialchars($customer['name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">บริษัท</label>
                <input type="text" name="company" class="form-control"
                    value="<?= htmlspecialchars($customer['company'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">โทรศัพท์</label>
                <input type="text" name="phone" class="form-control"
                    value="<?= htmlspecialchars($customer['phone'] ?? '') ?>">
            </div>
            <div class="col-md-8">
                <label class="form-label">อีเมล</label>
                <input type="email" name="email" class="form-control"
                    value="<?= htmlspecialchars($customer['email'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">สถานะ</label>
                <select name="status" class="form-select">
                    <option value="active" <?= ($customer['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>ใช้งาน</option>
                    <option value="inactive" <?= ($customer['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>ปิดใช้งาน</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">ที่อยู่</label>
                <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($customer['address'] ?? '') ?></textarea>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>บันทึก</button>
            <a href="<?= BASE_URL ?>/customers" class="btn btn-outline-secondary">ยกเลิก</a>
        </div>
    </form>
</div>

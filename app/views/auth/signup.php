<div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="card shadow-sm" style="width:100%;max-width:440px">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <i class="bi bi-grid-3x3-gap-fill fs-2 text-primary"></i>
                <h4 class="mt-2 mb-0"><?= APP_NAME ?></h4>
                <p class="text-muted small">สมัครสมาชิกใหม่</p>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/signup">
                <div class="mb-3">
                    <label class="form-label">ชื่อ-นามสกุล</label>
                    <input type="text" name="name" class="form-control"
                           value="<?= $name ?? '' ?>" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">อีเมล</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= $email ?? '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">รหัสผ่าน <span class="text-muted small">(อย่างน้อย 6 ตัวอักษร)</span></label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">ยืนยันรหัสผ่าน</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus me-1"></i> สมัครสมาชิก
                </button>
            </form>

            <hr class="my-3">
            <p class="text-center small mb-0">
                มีบัญชีแล้ว?
                <a href="<?= BASE_URL ?>/login">เข้าสู่ระบบ</a>
            </p>
        </div>
    </div>
</div>

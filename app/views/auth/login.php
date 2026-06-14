<div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="card shadow-sm" style="width:100%;max-width:420px">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <i class="bi bi-grid-3x3-gap-fill fs-2 text-primary"></i>
                <h4 class="mt-2 mb-0"><?= APP_NAME ?></h4>
                <p class="text-muted small">เข้าสู่ระบบเพื่อดำเนินการต่อ</p>
            </div>

            <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show py-2">
                <?= htmlspecialchars($flash['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/login">
                <div class="mb-3">
                    <label class="form-label">อีเมล</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= $email ?? '' ?>" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">รหัสผ่าน</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบ
                </button>
            </form>

            <hr class="my-3">
            <p class="text-center small mb-0">
                ยังไม่มีบัญชี?
                <a href="<?= BASE_URL ?>/signup">สมัครสมาชิก</a>
            </p>
        </div>
    </div>
</div>

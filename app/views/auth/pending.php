<div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="card shadow-sm text-center" style="width:100%;max-width:460px">
        <div class="card-body p-5">
            <i class="bi bi-hourglass-split text-warning" style="font-size:3rem"></i>
            <h4 class="mt-3">รอการอนุมัติ</h4>
            <p class="text-muted">
                สวัสดี <strong><?= htmlspecialchars($name) ?></strong><br>
                บัญชีของคุณอยู่ระหว่างรอการอนุมัติจาก Admin<br>
                กรุณารอสักครู่แล้วลองเข้าสู่ระบบใหม่อีกครั้ง
            </p>
            <a href="<?= BASE_URL ?>/logout" class="btn btn-outline-secondary btn-sm mt-2">
                <i class="bi bi-box-arrow-left me-1"></i> ออกจากระบบ
            </a>
        </div>
    </div>
</div>

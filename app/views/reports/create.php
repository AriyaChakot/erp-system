<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-plus-circle me-2"></i>แจ้งปัญหาใหม่</h2>
    <a href="<?= BASE_URL ?>/report" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>กลับ
    </a>
</div>

<div class="card" style="max-width:680px">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/report/store">
            <div class="mb-3">
                <label class="form-label">หัวข้อปัญหา <span class="text-danger">*</span></label>
                <input type="text" name="subject" class="form-control" placeholder="ระบุหัวข้อสั้นๆ"
                       value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">รายละเอียดปัญหา <span class="text-danger">*</span></label>
                <textarea name="message" class="form-control" rows="6"
                          placeholder="อธิบายปัญหาที่พบโดยละเอียด..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i>ส่งรายงาน
                </button>
                <a href="<?= BASE_URL ?>/report" class="btn btn-outline-secondary">ยกเลิก</a>
            </div>
        </form>
    </div>
</div>

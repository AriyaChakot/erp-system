<div class="page-header">
    <h4><i class="bi bi-calendar-plus me-2"></i>ขอลา</h4>
    <a href="<?= BASE_URL ?>/hr/my-leaves" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>ย้อนกลับ
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="form-card">
            <form method="post" action="<?= BASE_URL ?>/hr/store-leave">
                <div class="mb-3">
                    <label class="form-label">เลขที่คำขอ</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($reqNumber) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">ประเภทการลา <span class="text-danger">*</span></label>
                    <select name="leave_type_id" class="form-select" required>
                        <option value="">— เลือกประเภท —</option>
                        <?php foreach ($leaveTypes as $lt): ?>
                        <option value="<?= $lt['id'] ?>" data-days="<?= $lt['days_per_year'] ?>">
                            <?= htmlspecialchars($lt['name']) ?>
                            (<?= $lt['days_per_year'] ? $lt['days_per_year'].' วัน/ปี' : 'ไม่จำกัด' ?>)
                            <?= $lt['is_paid'] ? '— มีเงิน' : '— ไม่มีเงิน' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">วันที่เริ่ม <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="startDate" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">วันที่สิ้นสุด <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" id="endDate" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="alert alert-info py-2 mb-0" id="daysPreview" style="display:none">
                        จำนวนวันลา (วันทำงาน): <strong id="daysCount">0</strong> วัน
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">เหตุผล</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="ระบุเหตุผลการลา..."></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>ส่งคำขอ</button>
                    <a href="<?= BASE_URL ?>/hr/my-leaves" class="btn btn-outline-secondary">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($balances)): ?>
    <div class="col-lg-4">
        <div class="form-card">
            <h6 class="fw-bold mb-3"><i class="bi bi-calendar2-check me-2"></i>วันลาคงเหลือ</h6>
            <?php foreach ($balances as $b): ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between small mb-1">
                    <span><?= htmlspecialchars($b['name']) ?></span>
                    <span class="fw-bold text-<?= ($b['remaining'] ?? $b['days_per_year']) > 0 ? 'success' : 'danger' ?>">
                        <?= $b['remaining'] ?? $b['days_per_year'] ?> / <?= $b['days_per_year'] ?: '∞' ?>
                    </span>
                </div>
                <?php if ($b['days_per_year'] > 0): ?>
                <div class="progress" style="height:6px">
                    <?php $pct = min(100, (($b['used'] ?? 0) / $b['days_per_year']) * 100); ?>
                    <div class="progress-bar bg-primary" style="width:<?= $pct ?>%"></div>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function countWorkdays(start, end) {
    if (!start || !end) return 0;
    let s = new Date(start), e = new Date(end), count = 0;
    while (s <= e) {
        const day = s.getDay();
        if (day !== 0 && day !== 6) count++;
        s.setDate(s.getDate() + 1);
    }
    return count;
}
document.getElementById('startDate').addEventListener('change', updateDays);
document.getElementById('endDate').addEventListener('change', updateDays);
function updateDays() {
    const s = document.getElementById('startDate').value;
    const e = document.getElementById('endDate').value;
    if (s && e) {
        const d = countWorkdays(s, e);
        document.getElementById('daysCount').textContent = d;
        document.getElementById('daysPreview').style.display = 'block';
    }
}
</script>

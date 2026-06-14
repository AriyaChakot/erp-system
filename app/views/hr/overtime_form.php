<div class="page-header">
    <h4><i class="bi bi-clock-history me-2"></i>ขอล่วงเวลา (OT)</h4>
    <a href="<?= BASE_URL ?>/hr/overtime" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>ย้อนกลับ
    </a>
</div>

<div class="row justify-content-center">
<div class="col-lg-7">
<div class="form-card">
    <form method="post" action="<?= BASE_URL ?>/hr/store-ot">
        <div class="mb-3">
            <label class="form-label">เลขที่คำขอ</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($reqNumber) ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">พนักงาน <span class="text-danger">*</span></label>
            <select name="employee_id" class="form-select" required>
                <option value="">— เลือกพนักงาน —</option>
                <?php foreach ($employees as $e): ?>
                <option value="<?= $e['id'] ?>" data-salary="<?= $e['salary'] ?>">
                    <?= htmlspecialchars($e['name']) ?> (<?= htmlspecialchars($e['department'] ?? '-') ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">วันที่ทำงานล่วงเวลา <span class="text-danger">*</span></label>
            <input type="date" name="ot_date" id="otDate" class="form-control" required>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">เวลาเริ่ม <span class="text-danger">*</span></label>
                <input type="time" name="start_time" id="startTime" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">เวลาสิ้นสุด <span class="text-danger">*</span></label>
                <input type="time" name="end_time" id="endTime" class="form-control" required>
            </div>
        </div>
        <div class="mb-3">
            <div class="alert alert-info py-2 mb-0" id="hoursPreview" style="display:none">
                จำนวนชั่วโมง OT: <strong id="hoursCount">0</strong> ชม.
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">ประเภท OT <span class="text-danger">*</span></label>
            <select name="ot_type" id="otType" class="form-select" required>
                <option value="weekday">วันทำงาน (อัตรา 1.5x)</option>
                <option value="weekend">วันหยุดประจำสัปดาห์ (อัตรา 2.0x)</option>
                <option value="holiday">วันหยุดนักขัตฤกษ์ (อัตรา 3.0x)</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">เหตุผล</label>
            <textarea name="reason" class="form-control" rows="3" placeholder="ระบุเหตุผล..."></textarea>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>ส่งคำขอ</button>
            <a href="<?= BASE_URL ?>/hr/overtime" class="btn btn-outline-secondary">ยกเลิก</a>
        </div>
    </form>
</div>
</div>
</div>

<script>
document.getElementById('otDate').addEventListener('change', function() {
    const d = new Date(this.value);
    const day = d.getDay();
    const sel = document.getElementById('otType');
    if (day === 0 || day === 6) sel.value = 'weekend';
    else sel.value = 'weekday';
});
document.getElementById('startTime').addEventListener('change', calcHours);
document.getElementById('endTime').addEventListener('change', calcHours);
function calcHours() {
    const s = document.getElementById('startTime').value;
    const e = document.getElementById('endTime').value;
    if (s && e) {
        const diff = (new Date('1970-01-01T'+e) - new Date('1970-01-01T'+s)) / 3600000;
        document.getElementById('hoursCount').textContent = diff.toFixed(1);
        document.getElementById('hoursPreview').style.display = 'block';
    }
}
</script>

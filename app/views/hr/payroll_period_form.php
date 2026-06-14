<div class="page-header">
    <h4><i class="bi bi-calendar-plus me-2"></i>สร้าง Payroll Period</h4>
    <a href="<?= BASE_URL ?>/hr/payroll" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>ย้อนกลับ
    </a>
</div>

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="form-card">
    <form method="post" action="<?= BASE_URL ?>/hr/store-period">
        <div class="mb-3">
            <label class="form-label fw-semibold">ชื่อ Period <span class="text-danger">*</span></label>
            <input type="text" name="period_name" class="form-control" placeholder="เช่น มกราคม 2567" required>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">ปี <span class="text-danger">*</span></label>
                <input type="number" name="year" class="form-control" value="<?= date('Y') ?>" min="2020" max="2099" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">เดือน <span class="text-danger">*</span></label>
                <select name="month" class="form-select" required>
                    <?php
                    $months = ['','มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
                    for ($m=1; $m<=12; $m++) echo "<option value='$m'".($m==(int)date('m')?' selected':'').">{$months[$m]}</option>";
                    ?>
                </select>
            </div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">วันที่เริ่มต้น <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-01') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">วันที่สิ้นสุด <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control" value="<?= date('Y-m-t') ?>" required>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">วันที่จ่ายเงินเดือน <span class="text-danger">*</span></label>
            <input type="date" name="pay_date" class="form-control" required>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>บันทึก</button>
            <a href="<?= BASE_URL ?>/hr/payroll" class="btn btn-outline-secondary">ยกเลิก</a>
        </div>
    </form>
</div>
</div>
</div>

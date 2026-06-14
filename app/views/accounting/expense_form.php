<div class="page-header">
    <h4><i class="bi bi-credit-card-2-back me-2"></i>บันทึกค่าใช้จ่าย</h4>
    <a href="<?= BASE_URL ?>/accounting/expenses" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>กลับ</a>
</div>
<div class="form-card" style="max-width:680px">
    <form method="post" action="<?= BASE_URL ?>/accounting/expense">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label fw-medium">เลขที่เอกสาร</label>
                <input type="text" name="expense_number" class="form-control" readonly value="<?= htmlspecialchars($expNumber) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">วันที่</label>
                <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">หมวดหมู่ <span class="text-danger">*</span></label>
                <select name="category" class="form-select" required>
                    <option value="">— เลือกหมวด —</option>
                    <?php foreach ($categories as $k => $v): ?>
                    <option value="<?= $k ?>"><?= htmlspecialchars($v) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">ผังบัญชี</label>
                <select name="account_id" class="form-select">
                    <option value="">— เลือกบัญชี —</option>
                    <?php foreach ($accounts as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['code'] . ' - ' . $a['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">รายละเอียด <span class="text-danger">*</span></label>
                <input type="text" name="description" class="form-control" required placeholder="อธิบายค่าใช้จ่าย...">
            </div>
            <div class="col-md-5">
                <label class="form-label fw-medium">จำนวนเงิน (฿) <span class="text-danger">*</span></label>
                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required placeholder="0.00">
            </div>
            <div class="col-md-7">
                <label class="form-label fw-medium">เลขที่ใบเสร็จ</label>
                <input type="text" name="receipt_no" class="form-control" placeholder="เลขที่ใบเสร็จ/ใบกำกับ">
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">หมายเหตุ</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>บันทึก</button>
            <a href="<?= BASE_URL ?>/accounting/expenses" class="btn btn-outline-secondary">ยกเลิก</a>
        </div>
    </form>
</div>

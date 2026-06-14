<div class="page-header">
    <h4><i class="bi bi-receipt me-2"></i>สร้าง Invoice <?= $type === 'sale' ? 'ขาย' : 'ซื้อ' ?></h4>
    <a href="<?= BASE_URL ?>/accounting/invoices" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>กลับ</a>
</div>

<form method="post" action="<?= BASE_URL ?>/accounting/invoice">
<input type="hidden" name="invoice_type" value="<?= htmlspecialchars($type) ?>">
<div class="row g-3">
    <div class="col-lg-8">
        <div class="form-card">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-medium">Invoice Number</label>
                    <input type="text" name="invoice_number" class="form-control" readonly value="<?= htmlspecialchars($invNumber) ?>">
                </div>
                <?php if ($type === 'sale'): ?>
                <div class="col-md-7">
                    <label class="form-label fw-medium">ชื่อลูกค้า</label>
                    <input type="text" name="customer_name" class="form-control" placeholder="ระบุชื่อลูกค้า">
                </div>
                <?php else: ?>
                <div class="col-md-7">
                    <label class="form-label fw-medium">Vendor</label>
                    <select name="vendor_id" class="form-select">
                        <option value="">— เลือก Vendor —</option>
                        <?php foreach ($vendors as $v): ?>
                        <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-md-4">
                    <label class="form-label fw-medium">วันที่ออก</label>
                    <input type="date" name="issue_date" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">ครบกำหนด</label>
                    <input type="date" name="due_date" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">VAT (%)</label>
                    <input type="number" name="vat_rate" class="form-control" value="7" min="0" max="100" step="0.01" id="vatRate">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">หมายเหตุ</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-card">
            <h6 class="fw-bold mb-3">สรุปยอด</h6>
            <div class="mb-2">
                <label class="form-label fw-medium small">ยอดก่อน VAT (฿) <span class="text-danger">*</span></label>
                <input type="number" name="subtotal" id="subtotal" class="form-control" step="0.01" min="0" required placeholder="0.00">
            </div>
            <dl class="row small mb-0 mt-3">
                <dt class="col-7 text-muted">VAT</dt>
                <dd class="col-5 text-end" id="dispVat">฿0.00</dd>
                <dt class="col-7 fw-bold">ยอดรวม</dt>
                <dd class="col-5 text-end fw-bold text-primary fs-5" id="dispTotal">฿0.00</dd>
            </dl>
        </div>
    </div>
</div>
<div class="mt-3 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>บันทึก</button>
    <a href="<?= BASE_URL ?>/accounting/invoices" class="btn btn-outline-secondary">ยกเลิก</a>
</div>
</form>

<script>
function recalc() {
    const sub  = parseFloat(document.getElementById('subtotal').value) || 0;
    const rate = parseFloat(document.getElementById('vatRate').value)  || 0;
    const vat  = sub * rate / 100;
    document.getElementById('dispVat').textContent   = '฿' + vat.toLocaleString('th-TH',{minimumFractionDigits:2});
    document.getElementById('dispTotal').textContent = '฿' + (sub+vat).toLocaleString('th-TH',{minimumFractionDigits:2});
}
document.getElementById('subtotal').addEventListener('input', recalc);
document.getElementById('vatRate').addEventListener('input', recalc);
</script>

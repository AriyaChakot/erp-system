<?php
$statusLabel = ['draft'=>'ร่าง','sent'=>'ส่งแล้ว','partial'=>'ชำระบางส่วน','paid'=>'ชำระครบ','overdue'=>'เกินกำหนด','cancelled'=>'ยกเลิก'];
$statusColor = ['draft'=>'secondary','sent'=>'primary','partial'=>'warning','paid'=>'success','overdue'=>'danger','cancelled'=>'dark'];
$methodLabel = ['cash'=>'เงินสด','bank_transfer'=>'โอนเงิน','cheque'=>'เช็ค','credit_card'=>'บัตรเครดิต'];
$balance     = $invoice['total'] - $invoice['paid_amount'];
?>
<div class="page-header">
    <div>
        <h4 class="mb-0"><?= htmlspecialchars($invoice['invoice_number']) ?></h4>
        <span class="badge bg-<?= $statusColor[$invoice['status']] ?? 'secondary' ?> mt-1">
            <?= $statusLabel[$invoice['status']] ?? $invoice['status'] ?>
        </span>
    </div>
    <div class="d-flex gap-2">
        <?php if (!in_array($invoice['status'], ['paid','cancelled']) && $balance > 0): ?>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#payModal">
            <i class="bi bi-cash me-1"></i>บันทึกชำระ
        </button>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/accounting/invoices" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>กลับ
        </a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="form-card h-100">
            <h6 class="fw-bold mb-3">ข้อมูล Invoice</h6>
            <dl class="row small mb-0">
                <dt class="col-5 text-muted">ประเภท</dt>
                <dd class="col-7"><span class="badge bg-<?= $invoice['invoice_type']==='sale' ? 'success' : 'info' ?>"><?= $invoice['invoice_type']==='sale' ? 'ขาย' : 'ซื้อ' ?></span></dd>
                <dt class="col-5 text-muted">คู่ค้า</dt>
                <dd class="col-7"><?= htmlspecialchars($invoice['invoice_type']==='sale' ? ($invoice['customer_name']??'-') : ($invoice['vendor_name']??'-')) ?></dd>
                <dt class="col-5 text-muted">วันที่ออก</dt>
                <dd class="col-7"><?= date('d/m/Y', strtotime($invoice['issue_date'])) ?></dd>
                <dt class="col-5 text-muted">ครบกำหนด</dt>
                <dd class="col-7 <?= $invoice['status']==='overdue' ? 'text-danger fw-bold' : '' ?>"><?= date('d/m/Y', strtotime($invoice['due_date'])) ?></dd>
                <dt class="col-5 text-muted">หมายเหตุ</dt>
                <dd class="col-7"><?= htmlspecialchars($invoice['notes'] ?? '-') ?></dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-card h-100">
            <h6 class="fw-bold mb-3">สรุปยอดเงิน</h6>
            <dl class="row mb-0">
                <dt class="col-6 text-muted">ก่อน VAT</dt>
                <dd class="col-6 text-end">฿<?= number_format($invoice['subtotal'], 2) ?></dd>
                <dt class="col-6 text-muted">VAT <?= $invoice['vat_rate'] ?>%</dt>
                <dd class="col-6 text-end">฿<?= number_format($invoice['vat_amount'], 2) ?></dd>
                <dt class="col-6 fw-bold">ยอดรวม</dt>
                <dd class="col-6 text-end fw-bold fs-5">฿<?= number_format($invoice['total'], 2) ?></dd>
                <dt class="col-6 text-success">ชำระแล้ว</dt>
                <dd class="col-6 text-end text-success">฿<?= number_format($invoice['paid_amount'], 2) ?></dd>
                <dt class="col-6 <?= $balance > 0 ? 'text-danger' : '' ?>">คงค้าง</dt>
                <dd class="col-6 text-end <?= $balance > 0 ? 'text-danger fw-bold' : '' ?>">฿<?= number_format($balance, 2) ?></dd>
            </dl>
        </div>
    </div>
</div>

<!-- Payment History -->
<div class="table-card">
    <div class="px-3 pt-3 pb-2">
        <h6 class="fw-bold mb-0">ประวัติการชำระเงิน</h6>
    </div>
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr><th>Payment No.</th><th>วันที่</th><th>ช่องทาง</th><th>เลขอ้างอิง</th><th class="text-end">จำนวน</th></tr>
        </thead>
        <tbody>
        <?php if (empty($invoice['payments'])): ?>
            <tr><td colspan="5" class="text-center text-muted py-3">ยังไม่มีการชำระเงิน</td></tr>
        <?php else: ?>
            <?php foreach ($invoice['payments'] as $p): ?>
            <tr>
                <td class="fw-medium"><?= htmlspecialchars($p['payment_number']) ?></td>
                <td><?= date('d/m/Y', strtotime($p['payment_date'])) ?></td>
                <td><?= $methodLabel[$p['payment_method']] ?? $p['payment_method'] ?></td>
                <td class="text-muted small"><?= htmlspecialchars($p['reference_no'] ?? '-') ?></td>
                <td class="text-end fw-bold text-success">฿<?= number_format($p['amount'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Payment Modal -->
<?php if (!in_array($invoice['status'], ['paid','cancelled']) && $balance > 0): ?>
<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">บันทึกชำระเงิน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="<?= BASE_URL ?>/accounting/payment/<?= $invoice['id'] ?>">
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label fw-medium">วันที่ชำระ</label>
                        <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-medium">จำนวนเงิน (฿) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" step="0.01" max="<?= $balance ?>" value="<?= $balance ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-medium">ช่องทาง</label>
                        <select name="payment_method" class="form-select">
                            <option value="cash">เงินสด</option>
                            <option value="bank_transfer">โอนเงิน</option>
                            <option value="cheque">เช็ค</option>
                            <option value="credit_card">บัตรเครดิต</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-medium">เลขอ้างอิง</label>
                        <input type="text" name="reference_no" class="form-control" placeholder="เลขโอน/เลขเช็ค">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium">หมายเหตุ</label>
                        <input type="text" name="notes" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-success">ยืนยันการชำระ</button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

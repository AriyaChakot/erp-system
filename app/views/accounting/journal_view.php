<div class="page-header">
    <h4><i class="bi bi-journal-text me-2"></i><?= htmlspecialchars($je['entry_number']) ?></h4>
    <a href="<?= BASE_URL ?>/accounting/journals" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>กลับ</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="form-card">
            <dl class="row small mb-0">
                <dt class="col-5 text-muted">วันที่</dt>
                <dd class="col-7"><?= date('d/m/Y', strtotime($je['entry_date'])) ?></dd>
                <dt class="col-5 text-muted">อ้างอิง</dt>
                <dd class="col-7"><?= htmlspecialchars($je['reference_type'] ?? '-') ?> #<?= $je['reference_id'] ?? '' ?></dd>
                <dt class="col-5 text-muted">รายละเอียด</dt>
                <dd class="col-7"><?= htmlspecialchars($je['description'] ?? '-') ?></dd>
                <dt class="col-5 text-muted">สถานะ</dt>
                <dd class="col-7">
                    <?php $sc=['draft'=>'secondary','posted'=>'success','voided'=>'danger']; ?>
                    <span class="badge bg-<?= $sc[$je['status']] ?? 'secondary' ?>"><?= $je['status'] ?></span>
                </dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-card">
            <dl class="row mb-0">
                <dt class="col-6 text-muted">รวม Debit</dt>
                <dd class="col-6 text-end fw-bold">฿<?= number_format($je['total_debit'], 2) ?></dd>
                <dt class="col-6 text-muted">รวม Credit</dt>
                <dd class="col-6 text-end fw-bold">฿<?= number_format($je['total_credit'], 2) ?></dd>
                <dt class="col-6">ผลต่าง</dt>
                <dd class="col-6 text-end <?= abs($je['total_debit'] - $je['total_credit']) > 0.01 ? 'text-danger fw-bold' : 'text-success' ?>">
                    <?= abs($je['total_debit'] - $je['total_credit']) <= 0.01 ? '✓ สมดุล' : '✗ ไม่สมดุล!' ?>
                </dd>
            </dl>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="px-3 pt-3 pb-2"><h6 class="fw-bold mb-0">รายการบัญชี</h6></div>
    <table class="table mb-0">
        <thead class="table-light">
            <tr>
                <th>รหัสบัญชี</th>
                <th>ชื่อบัญชี</th>
                <th>ประเภท</th>
                <th>รายละเอียด</th>
                <th class="text-end">Debit</th>
                <th class="text-end">Credit</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($je['lines'] as $line): ?>
        <tr>
            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($line['account_code']) ?></span></td>
            <td><?= htmlspecialchars($line['account_name']) ?></td>
            <td class="text-muted small"><?= htmlspecialchars($line['account_type']) ?></td>
            <td class="text-muted small"><?= htmlspecialchars($line['description'] ?? '') ?></td>
            <td class="text-end"><?= $line['debit']  > 0 ? '฿'.number_format($line['debit'], 2)  : '' ?></td>
            <td class="text-end"><?= $line['credit'] > 0 ? '฿'.number_format($line['credit'], 2) : '' ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot class="table-light fw-bold">
            <tr>
                <td colspan="4" class="text-end">รวม</td>
                <td class="text-end">฿<?= number_format($je['total_debit'], 2) ?></td>
                <td class="text-end">฿<?= number_format($je['total_credit'], 2) ?></td>
            </tr>
        </tfoot>
    </table>
</div>

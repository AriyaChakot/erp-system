<?php
$statusLabel = ['draft'=>'ร่าง','sent'=>'ส่งแล้ว','partial'=>'ชำระบางส่วน','paid'=>'ชำระครบ','overdue'=>'เกินกำหนด','cancelled'=>'ยกเลิก'];
$statusColor = ['draft'=>'secondary','sent'=>'primary','partial'=>'warning','paid'=>'success','overdue'=>'danger','cancelled'=>'dark'];
?>
<div class="page-header">
    <h4><i class="bi bi-receipt me-2"></i>Invoice</h4>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/accounting/invoice?type=sale" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Invoice ขาย
        </a>
        <a href="<?= BASE_URL ?>/accounting/invoice?type=purchase" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Invoice ซื้อ
        </a>
    </div>
</div>

<!-- Filter Tabs -->
<div class="mb-3 d-flex gap-2 flex-wrap">
    <a href="<?= BASE_URL ?>/accounting/invoices" class="btn btn-sm <?= !$type ? 'btn-primary' : 'btn-outline-secondary' ?>">ทั้งหมด</a>
    <a href="<?= BASE_URL ?>/accounting/invoices?type=sale" class="btn btn-sm <?= $type==='sale' ? 'btn-primary' : 'btn-outline-secondary' ?>">ขาย</a>
    <a href="<?= BASE_URL ?>/accounting/invoices?type=purchase" class="btn btn-sm <?= $type==='purchase' ? 'btn-primary' : 'btn-outline-secondary' ?>">ซื้อ</a>
    <a href="<?= BASE_URL ?>/accounting/invoices?status=overdue" class="btn btn-sm <?= $status==='overdue' ? 'btn-danger' : 'btn-outline-danger' ?>">เกินกำหนด</a>
</div>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>Invoice No.</th>
                <th>ประเภท</th>
                <th>คู่ค้า</th>
                <th>วันที่ออก</th>
                <th>ครบกำหนด</th>
                <th class="text-end">ยอดรวม</th>
                <th class="text-end">ชำระแล้ว</th>
                <th class="text-end">คงค้าง</th>
                <th>สถานะ</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($invoices)): ?>
            <tr><td colspan="10" class="text-center text-muted py-4">ไม่พบข้อมูล</td></tr>
        <?php else: ?>
            <?php foreach ($invoices as $inv): ?>
            <?php
                $party   = $inv['invoice_type'] === 'sale' ? ($inv['customer_name'] ?? '-') : ($inv['vendor_name'] ?? '-');
                $balance = $inv['total'] - $inv['paid_amount'];
            ?>
            <tr>
                <td><a href="<?= BASE_URL ?>/accounting/invoice/<?= $inv['id'] ?>" class="fw-bold text-decoration-none"><?= htmlspecialchars($inv['invoice_number']) ?></a></td>
                <td><span class="badge bg-<?= $inv['invoice_type']==='sale' ? 'success' : 'info' ?> bg-opacity-75"><?= $inv['invoice_type']==='sale' ? 'ขาย' : 'ซื้อ' ?></span></td>
                <td><?= htmlspecialchars($party) ?></td>
                <td class="text-muted small"><?= date('d/m/Y', strtotime($inv['issue_date'])) ?></td>
                <td class="text-muted small <?= $inv['status'] === 'overdue' ? 'text-danger fw-bold' : '' ?>"><?= date('d/m/Y', strtotime($inv['due_date'])) ?></td>
                <td class="text-end">฿<?= number_format($inv['total'], 2) ?></td>
                <td class="text-end text-success">฿<?= number_format($inv['paid_amount'], 2) ?></td>
                <td class="text-end <?= $balance > 0 ? 'text-danger fw-bold' : '' ?>">฿<?= number_format($balance, 2) ?></td>
                <td><span class="badge bg-<?= $statusColor[$inv['status']] ?? 'secondary' ?>"><?= $statusLabel[$inv['status']] ?? $inv['status'] ?></span></td>
                <td>
                    <a href="<?= BASE_URL ?>/accounting/invoice/<?= $inv['id'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

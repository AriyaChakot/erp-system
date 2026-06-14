<div class="page-header">
    <h4><i class="bi bi-graph-up me-2"></i>มูลค่าสินค้าคงคลัง (FIFO)</h4>
    <div class="text-muted small">คำนวณด้วยวิธี First-In First-Out</div>
</div>

<!-- Summary -->
<div class="form-card mb-4 d-flex align-items-center gap-4 flex-wrap">
    <div>
        <div class="text-muted small">มูลค่ารวมทั้งหมด</div>
        <div class="fs-3 fw-bold text-primary">฿<?= number_format($totalValue, 2) ?></div>
    </div>
    <div>
        <div class="text-muted small">จำนวนรายการสินค้า</div>
        <div class="fs-3 fw-bold"><?= count($report) ?></div>
    </div>
</div>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>รหัส</th>
                <th>ชื่อสินค้า</th>
                <th class="text-end">จำนวนคงคลัง</th>
                <th class="text-end">ต้นทุนเฉลี่ย/หน่วย</th>
                <th class="text-end">มูลค่ารวม</th>
                <th class="text-end">% ของทั้งหมด</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($report)): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">ไม่พบข้อมูล</td></tr>
        <?php else: ?>
            <?php foreach ($report as $r): ?>
            <tr>
                <td><span class="badge bg-light text-dark"><?= htmlspecialchars($r['code']) ?></span></td>
                <td class="fw-medium"><?= htmlspecialchars($r['name']) ?></td>
                <td class="text-end"><?= number_format($r['total_qty']) ?> <?= htmlspecialchars($r['unit']) ?></td>
                <td class="text-end text-muted">฿<?= number_format($r['avg_cost'], 2) ?></td>
                <td class="text-end fw-bold">฿<?= number_format($r['total_value'], 2) ?></td>
                <td class="text-end text-muted small">
                    <?= $totalValue > 0 ? number_format($r['total_value'] / $totalValue * 100, 1) : '0.0' ?>%
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
        <tfoot class="table-light fw-bold">
            <tr>
                <td colspan="4" class="text-end">รวมทั้งหมด</td>
                <td class="text-end text-primary">฿<?= number_format($totalValue, 2) ?></td>
                <td class="text-end">100%</td>
            </tr>
        </tfoot>
    </table>
</div>

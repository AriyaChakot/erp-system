<div class="page-header">
    <h4><i class="bi bi-journal-text me-2"></i>Journal Entries</h4>
</div>

<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>Entry No.</th>
                <th>วันที่</th>
                <th>อ้างอิง</th>
                <th>รายละเอียด</th>
                <th>ผู้บันทึก</th>
                <th class="text-end">ยอด Debit</th>
                <th>สถานะ</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($entries)): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">ไม่พบข้อมูล</td></tr>
        <?php else: ?>
            <?php
            $sc = ['draft'=>'secondary','posted'=>'success','voided'=>'danger'];
            $sl = ['draft'=>'ร่าง','posted'=>'โพสต์แล้ว','voided'=>'ยกเลิก'];
            foreach ($entries as $je):
            ?>
            <tr>
                <td><a href="<?= BASE_URL ?>/accounting/journal/<?= $je['id'] ?>" class="fw-bold text-decoration-none"><?= htmlspecialchars($je['entry_number']) ?></a></td>
                <td class="text-muted small"><?= date('d/m/Y', strtotime($je['entry_date'])) ?></td>
                <td class="text-muted small"><?= htmlspecialchars($je['reference_type'] ?? '-') ?></td>
                <td><?= htmlspecialchars(mb_strimwidth($je['description'] ?? '', 0, 50, '...')) ?></td>
                <td class="text-muted small"><?= htmlspecialchars($je['created_by_name'] ?? '-') ?></td>
                <td class="text-end">฿<?= number_format($je['total_debit'], 2) ?></td>
                <td><span class="badge bg-<?= $sc[$je['status']] ?? 'secondary' ?>"><?= $sl[$je['status']] ?? $je['status'] ?></span></td>
                <td>
                    <a href="<?= BASE_URL ?>/accounting/journal/<?= $je['id'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

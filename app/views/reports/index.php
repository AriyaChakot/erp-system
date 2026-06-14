<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-chat-dots me-2"></i>รายงานปัญหาของฉัน</h2>
    <a href="<?= BASE_URL ?>/report/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>แจ้งปัญหาใหม่
    </a>
</div>

<?php if (empty($reports)): ?>
<div class="card text-center py-5 text-muted">
    <i class="bi bi-inbox fs-1 mb-3"></i>
    <p class="mb-3">คุณยังไม่ได้แจ้งปัญหาใดๆ</p>
    <div>
        <a href="<?= BASE_URL ?>/report/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>แจ้งปัญหาแรก
        </a>
    </div>
</div>
<?php endif; ?>

<?php
$statusColor = ['open'=>'danger','in_progress'=>'warning','resolved'=>'success'];
$statusLabel = ['open'=>'รอดำเนินการ','in_progress'=>'กำลังดำเนิน','resolved'=>'แก้ไขแล้ว'];
foreach ($reports as $r):
?>
<div class="card mb-3 border-start border-4 border-<?= $statusColor[$r['status']] ?? 'secondary' ?>">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <h6 class="mb-0 fw-semibold"><?= htmlspecialchars($r['subject']) ?></h6>
            <span class="badge bg-<?= $statusColor[$r['status']] ?? 'secondary' ?>">
                <?= $statusLabel[$r['status']] ?? $r['status'] ?>
            </span>
        </div>
        <p class="small text-muted mb-2">
            <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?>
        </p>
        <p class="small mb-0 border rounded p-2 bg-light"><?= nl2br(htmlspecialchars($r['message'])) ?></p>

        <?php if ($r['admin_reply']): ?>
        <div class="alert alert-success py-2 mt-3 mb-0 small">
            <i class="bi bi-person-check-fill me-1"></i><strong>Admin ตอบกลับ:</strong>
            <?= nl2br(htmlspecialchars($r['admin_reply'])) ?>
            <div class="text-muted mt-1" style="font-size:.75rem">
                <?= $r['replied_at'] ? date('d/m/Y H:i', strtotime($r['replied_at'])) : '' ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>

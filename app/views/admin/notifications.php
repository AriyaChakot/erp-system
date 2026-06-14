<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-bell-fill me-2"></i>รายงานปัญหาจากผู้ใช้</h2>
    <a href="<?= BASE_URL ?>/admin" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>กลับ
    </a>
</div>

<?php if (empty($reports)): ?>
<div class="card body text-center py-5 text-muted">
    <i class="bi bi-check-circle fs-1 text-success mb-3"></i>
    <p>ไม่มีรายงานปัญหาในขณะนี้</p>
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
            <div>
                <h6 class="mb-0 fw-semibold"><?= htmlspecialchars($r['subject']) ?></h6>
                <small class="text-muted">
                    <i class="bi bi-person me-1"></i><?= htmlspecialchars($r['user_name']) ?>
                    (<?= htmlspecialchars($r['user_email']) ?>)
                    &nbsp;·&nbsp;
                    <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?>
                </small>
            </div>
            <span class="badge bg-<?= $statusColor[$r['status']] ?? 'secondary' ?>">
                <?= $statusLabel[$r['status']] ?? $r['status'] ?>
            </span>
        </div>

        <p class="mb-3 small border rounded p-2 bg-light"><?= nl2br(htmlspecialchars($r['message'])) ?></p>

        <?php if ($r['admin_reply']): ?>
        <div class="alert alert-success py-2 mb-3 small">
            <i class="bi bi-reply-fill me-1"></i><strong>ตอบกลับแล้ว:</strong>
            <?= nl2br(htmlspecialchars($r['admin_reply'])) ?>
            <div class="text-muted mt-1" style="font-size:.75rem">
                <?= $r['replied_at'] ? date('d/m/Y H:i', strtotime($r['replied_at'])) : '' ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Reply form -->
        <form method="POST" action="<?= BASE_URL ?>/admin/reply">
            <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
            <div class="row g-2 align-items-end">
                <div class="col-md-7">
                    <textarea name="reply" class="form-control form-control-sm" rows="2"
                              placeholder="พิมพ์ข้อความตอบกลับ..."><?= htmlspecialchars($r['admin_reply'] ?? '') ?></textarea>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="open"        <?= $r['status']==='open'        ? 'selected':'' ?>>รอดำเนินการ</option>
                        <option value="in_progress" <?= $r['status']==='in_progress' ? 'selected':'' ?>>กำลังดำเนิน</option>
                        <option value="resolved"    <?= $r['status']==='resolved'    ? 'selected':'' ?>>แก้ไขแล้ว</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-send me-1"></i>ส่ง
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>

<h2 class="mb-4"><i class="bi bi-shield-lock me-2"></i>Admin Dashboard</h2>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="stat-value"><?= $totalUsers ?></div>
                <div class="stat-label">ผู้ใช้ทั้งหมด</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="stat-value"><?= $pendingUsers ?></div>
                <div class="stat-label">รอการอนุมัติ</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
            </div>
            <div>
                <div class="stat-value"><?= $openReports ?></div>
                <div class="stat-label">ปัญหารอดำเนินการ</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-chat-dots-fill"></i>
            </div>
            <div>
                <div class="stat-value"><?= $totalReports ?></div>
                <div class="stat-label">รายงานทั้งหมด</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                <i class="bi bi-lightning-fill text-warning me-2"></i>ทางลัด
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="<?= BASE_URL ?>/admin/users" class="btn btn-outline-primary text-start">
                    <i class="bi bi-person-gear me-2"></i>จัดการผู้ใช้และ Role
                    <?php if ($pendingUsers > 0): ?>
                    <span class="badge bg-warning text-dark ms-2"><?= $pendingUsers ?> รอ</span>
                    <?php endif; ?>
                </a>
                <a href="<?= BASE_URL ?>/admin/notifications" class="btn btn-outline-danger text-start">
                    <i class="bi bi-bell-fill me-2"></i>รายงานปัญหาจากผู้ใช้
                    <?php if ($openReports > 0): ?>
                    <span class="badge bg-danger ms-2"><?= $openReports ?> ใหม่</span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                <i class="bi bi-people me-2"></i>ผู้ใช้ล่าสุด
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach (array_slice($recentUsers, 0, 5) as $u): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <div>
                            <div class="fw-medium small"><?= htmlspecialchars($u['name']) ?></div>
                            <div class="text-muted" style="font-size:.75rem"><?= htmlspecialchars($u['email']) ?></div>
                        </div>
                        <?php
                        $rc = ['admin'=>'danger','user'=>'success','pending'=>'warning'];
                        $rl = ['admin'=>'Admin','user'=>'User','pending'=>'Pending'];
                        ?>
                        <span class="badge bg-<?= $rc[$u['role']] ?? 'secondary' ?>">
                            <?= $rl[$u['role']] ?? $u['role'] ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                    <?php if (empty($recentUsers)): ?>
                    <li class="list-group-item text-center text-muted py-3">ยังไม่มีผู้ใช้</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

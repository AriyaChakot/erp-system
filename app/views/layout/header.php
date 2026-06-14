<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'ERP') ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>
<div class="wrapper d-flex">
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>
            <span><?= APP_NAME ?></span>
        </div>
        <ul class="sidebar-nav">
            <li>
                <a href="<?= BASE_URL ?>/" class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-section">จัดการข้อมูล</li>
            <li>
                <a href="<?= BASE_URL ?>/products" class="<?= ($activePage ?? '') === 'products' ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i> สินค้า
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/customers" class="<?= ($activePage ?? '') === 'customers' ? 'active' : '' ?>">
                    <i class="bi bi-people"></i> ลูกค้า
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/employees" class="<?= ($activePage ?? '') === 'employees' ? 'active' : '' ?>">
                    <i class="bi bi-person-badge"></i> พนักงาน
                </a>
            </li>
            <li class="nav-section">การขาย</li>
            <li>
                <a href="<?= BASE_URL ?>/orders" class="<?= ($activePage ?? '') === 'orders' ? 'active' : '' ?>">
                    <i class="bi bi-cart3"></i> คำสั่งซื้อ
                </a>
            </li>

            <li class="nav-section">คลังสินค้า</li>
            <li>
                <a href="<?= BASE_URL ?>/inventory" class="<?= ($activePage ?? '') === 'inventory' ? 'active' : '' ?>">
                    <i class="bi bi-archive"></i> ภาพรวมคลัง
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/inventory/stock" class="<?= ($activePage ?? '') === 'stock' ? 'active' : '' ?>">
                    <i class="bi bi-boxes"></i> สินค้าในคลัง
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/inventory/movements" class="<?= ($activePage ?? '') === 'movements' ? 'active' : '' ?>">
                    <i class="bi bi-arrow-left-right"></i> ความเคลื่อนไหว
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/inventory/low-stock">
                    <i class="bi bi-exclamation-triangle"></i> สินค้าใกล้หมด
                    <?php if (!empty($_lowStockCount) && $_lowStockCount > 0): ?>
                    <span class="badge bg-warning text-dark ms-1"><?= $_lowStockCount ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="nav-section">จัดซื้อ</li>
            <li>
                <a href="<?= BASE_URL ?>/purchasing" class="<?= ($activePage ?? '') === 'purchasing' ? 'active' : '' ?>">
                    <i class="bi bi-bag-check"></i> ใบสั่งซื้อ
                    <?php if (!empty($_pendingPOCount) && $_pendingPOCount > 0): ?>
                    <span class="badge bg-warning text-dark ms-1"><?= $_pendingPOCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/purchasing/vendors" class="<?= ($activePage ?? '') === 'vendors' ? 'active' : '' ?>">
                    <i class="bi bi-building"></i> ผู้จำหน่าย
                </a>
            </li>

            <li class="nav-section">บัญชี</li>
            <li>
                <a href="<?= BASE_URL ?>/accounting" class="<?= ($activePage ?? '') === 'accounting' ? 'active' : '' ?>">
                    <i class="bi bi-bar-chart-line"></i> ภาพรวมบัญชี
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/accounting/invoices" class="<?= ($activePage ?? '') === 'invoices' ? 'active' : '' ?>">
                    <i class="bi bi-receipt"></i> ใบแจ้งหนี้
                    <?php if (!empty($_overdueInvoiceCount) && $_overdueInvoiceCount > 0): ?>
                    <span class="badge bg-danger ms-1"><?= $_overdueInvoiceCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/accounting/expenses" class="<?= ($activePage ?? '') === 'expenses' ? 'active' : '' ?>">
                    <i class="bi bi-credit-card"></i> ค่าใช้จ่าย
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/accounting/pl">
                    <i class="bi bi-graph-up-arrow"></i> กำไร-ขาดทุน
                </a>
            </li>

            <li class="nav-section">HR & เงินเดือน</li>
            <li>
                <a href="<?= BASE_URL ?>/hr" class="<?= ($activePage ?? '') === 'hr' ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i> HR Dashboard
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/hr/leaves" class="<?= ($activePage ?? '') === 'leaves' ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check"></i> การลา
                    <?php if (!empty($_pendingLeaveCount) && $_pendingLeaveCount > 0): ?>
                    <span class="badge bg-warning text-dark ms-1"><?= $_pendingLeaveCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/hr/overtime" class="<?= ($activePage ?? '') === 'overtime' ? 'active' : '' ?>">
                    <i class="bi bi-clock-history"></i> ล่วงเวลา
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/hr/payroll" class="<?= ($activePage ?? '') === 'payroll' ? 'active' : '' ?>">
                    <i class="bi bi-wallet2"></i> เงินเดือน
                </a>
            </li>

            <!-- Report section (all users) -->
            <li class="nav-section">แจ้งปัญหา</li>
            <li>
                <a href="<?= BASE_URL ?>/report" class="<?= ($activePage ?? '') === 'report' ? 'active' : '' ?>">
                    <i class="bi bi-chat-dots"></i> รายงานปัญหา
                    <?php if (!empty($_myReportCount) && $_myReportCount > 0): ?>
                    <span class="badge bg-secondary ms-1"><?= $_myReportCount ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <!-- Admin section -->
            <?php if (isset($currentUser) && $currentUser && $currentUser['role'] === 'admin'): ?>
            <li class="nav-section">Admin</li>
            <li>
                <a href="<?= BASE_URL ?>/admin" class="<?= ($activePage ?? '') === 'admin' ? 'active' : '' ?>">
                    <i class="bi bi-shield-lock"></i> Admin Panel
                    <?php
                    $adminBadge = (int)($_pendingUserCount ?? 0) + (int)($_openReportCount ?? 0);
                    if ($adminBadge > 0):
                    ?>
                    <span class="badge bg-danger ms-1"><?= $adminBadge ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content flex-grow-1">
        <header class="topbar d-flex align-items-center justify-content-between px-4">
            <button class="btn btn-sm btn-outline-secondary" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small d-none d-sm-inline"><?= date('d/m/Y') ?></span>

                <!-- Notification bell (admin: show open reports + pending users) -->
                <?php
                $bellCount = 0;
                if (isset($currentUser) && $currentUser) {
                    if ($currentUser['role'] === 'admin') {
                        $bellCount = (int)($_pendingUserCount ?? 0) + (int)($_openReportCount ?? 0);
                    }
                }
                ?>
                <?php if ($bellCount > 0): ?>
                <a href="<?= BASE_URL ?>/admin/notifications" class="btn btn-sm btn-outline-secondary position-relative" title="แจ้งเตือน">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">
                        <?= $bellCount ?>
                    </span>
                </a>
                <?php endif; ?>

                <!-- User dropdown -->
                <?php if (isset($currentUser) && $currentUser): ?>
                <div class="dropdown">
                    <button class="btn p-0 border-0 d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <div class="avatar"><?= mb_strtoupper(mb_substr($currentUser['name'], 0, 1)) ?></div>
                        <span class="d-none d-md-inline small fw-medium"><?= htmlspecialchars($currentUser['name']) ?></span>
                        <i class="bi bi-chevron-down small text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text small text-muted">
                                <?= htmlspecialchars($currentUser['email']) ?>
                            </span>
                        </li>
                        <li>
                            <span class="dropdown-item-text small">
                                Role:
                                <?php $rc=['admin'=>'danger','user'=>'success','pending'=>'warning']; ?>
                                <span class="badge bg-<?= $rc[$currentUser['role']] ?? 'secondary' ?>">
                                    <?= ucfirst($currentUser['role']) ?>
                                </span>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <?php if ($currentUser['role'] === 'admin'): ?>
                        <li><a class="dropdown-item small" href="<?= BASE_URL ?>/admin"><i class="bi bi-shield-lock me-2"></i>Admin Panel</a></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item small" href="<?= BASE_URL ?>/report"><i class="bi bi-chat-dots me-2"></i>รายงานปัญหา</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item small text-danger" href="<?= BASE_URL ?>/logout">
                                <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
                            </a>
                        </li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </header>

        <div class="content-area p-4">
            <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flash['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

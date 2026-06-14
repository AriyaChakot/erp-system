<div class="page-header">
    <h4><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#dbeafe">
                    <i class="bi bi-box-seam text-primary"></i>
                </div>
                <div>
                    <div class="text-muted small">สินค้าทั้งหมด</div>
                    <div class="fs-3 fw-bold"><?= number_format($totalProducts) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#dcfce7">
                    <i class="bi bi-people text-success"></i>
                </div>
                <div>
                    <div class="text-muted small">ลูกค้าทั้งหมด</div>
                    <div class="fs-3 fw-bold"><?= number_format($totalCustomers) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fef9c3">
                    <i class="bi bi-person-badge text-warning"></i>
                </div>
                <div>
                    <div class="text-muted small">พนักงานทั้งหมด</div>
                    <div class="fs-3 fw-bold"><?= number_format($totalEmployees) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fce7f3">
                    <i class="bi bi-currency-dollar text-danger"></i>
                </div>
                <div>
                    <div class="text-muted small">รายได้รวม</div>
                    <div class="fs-3 fw-bold"><?= number_format($orderSummary['revenue'], 0) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Status Row -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">รอดำเนินการ</div>
                <div class="fs-4 fw-bold text-warning"><?= $orderSummary['pending'] ?></div>
                <div class="text-muted small">คำสั่งซื้อ</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">สำเร็จแล้ว</div>
                <div class="fs-4 fw-bold text-success"><?= $orderSummary['completed'] ?></div>
                <div class="text-muted small">คำสั่งซื้อ</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">ทั้งหมด</div>
                <div class="fs-4 fw-bold text-primary"><?= $orderSummary['total_orders'] ?></div>
                <div class="text-muted small">คำสั่งซื้อ</div>
            </div>
        </div>
    </div>
</div>

<!-- ERP Module Quick Links -->
<?php if (($pendingPOCount ?? 0) > 0 || ($lowStockCount ?? 0) > 0 || ($overdueInvoiceCount ?? 0) > 0 || ($pendingLeaveCount ?? 0) > 0): ?>
<div class="row g-3 mb-4">
    <?php if (($pendingPOCount ?? 0) > 0): ?>
    <div class="col-6 col-lg-3">
        <a href="<?= BASE_URL ?>/purchasing" class="text-decoration-none">
        <div class="card stat-card border-warning h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fef3c7"><i class="bi bi-bag-check text-warning fs-4"></i></div>
                <div>
                    <div class="text-muted small">PO รออนุมัติ</div>
                    <div class="fs-4 fw-bold text-warning"><?= $pendingPOCount ?></div>
                </div>
            </div>
        </div>
        </a>
    </div>
    <?php endif; ?>
    <?php if (($lowStockCount ?? 0) > 0): ?>
    <div class="col-6 col-lg-3">
        <a href="<?= BASE_URL ?>/inventory/low-stock" class="text-decoration-none">
        <div class="card stat-card border-danger h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fee2e2"><i class="bi bi-exclamation-triangle text-danger fs-4"></i></div>
                <div>
                    <div class="text-muted small">สินค้าใกล้หมด</div>
                    <div class="fs-4 fw-bold text-danger"><?= $lowStockCount ?></div>
                </div>
            </div>
        </div>
        </a>
    </div>
    <?php endif; ?>
    <?php if (($overdueInvoiceCount ?? 0) > 0): ?>
    <div class="col-6 col-lg-3">
        <a href="<?= BASE_URL ?>/accounting/invoices" class="text-decoration-none">
        <div class="card stat-card border-danger h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fee2e2"><i class="bi bi-receipt text-danger fs-4"></i></div>
                <div>
                    <div class="text-muted small">ใบแจ้งหนี้เกินกำหนด</div>
                    <div class="fs-4 fw-bold text-danger"><?= $overdueInvoiceCount ?></div>
                </div>
            </div>
        </div>
        </a>
    </div>
    <?php endif; ?>
    <?php if (($pendingLeaveCount ?? 0) > 0): ?>
    <div class="col-6 col-lg-3">
        <a href="<?= BASE_URL ?>/hr/leaves" class="text-decoration-none">
        <div class="card stat-card border-warning h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fef3c7"><i class="bi bi-calendar-check text-warning fs-4"></i></div>
                <div>
                    <div class="text-muted small">ขอลารออนุมัติ</div>
                    <div class="fs-4 fw-bold text-warning"><?= $pendingLeaveCount ?></div>
                </div>
            </div>
        </div>
        </a>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="row g-3">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="table-card">
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <h6 class="mb-0 fw-bold">คำสั่งซื้อล่าสุด</h6>
                <a href="<?= BASE_URL ?>/orders" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>เลขที่</th><th>ลูกค้า</th><th>ยอดรวม</th><th>สถานะ</th><th>วันที่</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach (array_slice($recentOrders, 0, 8) as $o): ?>
                <tr>
                    <td><a href="<?= BASE_URL ?>/orders/view/<?= $o['id'] ?>"><?= htmlspecialchars($o['order_number']) ?></a></td>
                    <td><?= htmlspecialchars($o['customer_name'] ?? '-') ?></td>
                    <td><?= number_format($o['total'], 2) ?></td>
                    <td><?php
                        $colors = ['pending'=>'warning','processing'=>'info','completed'=>'success','cancelled'=>'danger'];
                        $labels = ['pending'=>'รอดำเนินการ','processing'=>'กำลังดำเนิน','completed'=>'สำเร็จ','cancelled'=>'ยกเลิก'];
                        $c = $colors[$o['status']] ?? 'secondary';
                        $label = $labels[$o['status']] ?? $o['status'];
                        echo "<span class='badge bg-{$c}'>{$label}</span>";
                    ?></td>
                    <td><?= date('d/m/Y', strtotime($o['order_date'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recentOrders)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">ยังไม่มีคำสั่งซื้อ</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="col-lg-4">
        <div class="table-card">
            <div class="p-3 border-bottom">
                <h6 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle text-warning me-1"></i>สินค้าใกล้หมด</h6>
            </div>
            <div class="p-3">
            <?php if (empty($lowStock)): ?>
                <p class="text-muted text-center py-3 mb-0">สินค้าทุกชิ้นมีพอเพียง</p>
            <?php else: ?>
                <?php foreach ($lowStock as $p): ?>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <div class="fw-semibold small"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="text-muted" style="font-size:.75rem"><?= htmlspecialchars($p['code']) ?></div>
                    </div>
                    <span class="badge bg-danger"><?= $p['stock_qty'] ?> <?= htmlspecialchars($p['unit']) ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>

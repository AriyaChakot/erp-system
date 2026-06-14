<div class="page-header">
    <h4><i class="bi bi-cash-stack me-2"></i>ภาพรวมบัญชี</h4>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/accounting/invoice?type=sale" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>สร้าง Invoice
        </a>
        <a href="<?= BASE_URL ?>/accounting/expense" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-receipt me-1"></i>บันทึกค่าใช้จ่าย
        </a>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-arrow-down-circle fs-4"></i></div>
                <div>
                    <div class="text-muted small">ลูกหนี้คงค้าง</div>
                    <div class="fs-5 fw-bold">฿<?= number_format($stats['total_receivable'], 0) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-arrow-up-circle fs-4"></i></div>
                <div>
                    <div class="text-muted small">เจ้าหนี้คงค้าง</div>
                    <div class="fs-5 fw-bold">฿<?= number_format($stats['total_payable'], 0) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-currency-dollar fs-4"></i></div>
                <div>
                    <div class="text-muted small">รายได้ที่รับแล้ว</div>
                    <div class="fs-5 fw-bold">฿<?= number_format($stats['total_received'], 0) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-receipt fs-4"></i></div>
                <div>
                    <div class="text-muted small">ค่าใช้จ่ายรออนุมัติ</div>
                    <div class="fs-5 fw-bold"><?= $expStats['pending'] ?> รายการ</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Revenue Chart -->
<div class="form-card mb-4">
    <h6 class="fw-bold mb-3">รายได้รายเดือน <?= date('Y') ?></h6>
    <canvas id="revChart" height="80"></canvas>
</div>

<!-- Quick Links -->
<div class="row g-3">
    <?php
    $links = [
        ['href'=>'/accounting/invoices?type=sale','icon'=>'receipt','label'=>'Invoice ขาย','color'=>'primary'],
        ['href'=>'/accounting/invoices?type=purchase','icon'=>'cart3','label'=>'Invoice ซื้อ','color'=>'info'],
        ['href'=>'/accounting/expenses','icon'=>'credit-card-2-back','label'=>'ค่าใช้จ่าย','color'=>'warning'],
        ['href'=>'/accounting/pl','icon'=>'graph-up-arrow','label'=>'P&L Report','color'=>'success'],
        ['href'=>'/accounting/journals','icon'=>'journal-text','label'=>'Journal Entries','color'=>'secondary'],
    ];
    foreach ($links as $l):
    ?>
    <div class="col-6 col-lg-auto">
        <a href="<?= BASE_URL ?><?= $l['href'] ?>" class="btn btn-outline-<?= $l['color'] ?> w-100">
            <i class="bi bi-<?= $l['icon'] ?> me-2"></i><?= $l['label'] ?>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const months = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
const data   = <?= json_encode(array_values($monthlyRev)) ?>;
new Chart(document.getElementById('revChart'), {
    type: 'bar',
    data: { labels: months, datasets: [{ label: 'รายได้ (฿)', data, backgroundColor: '#3b9eff88', borderColor: '#3b9eff', borderWidth: 1 }] },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>

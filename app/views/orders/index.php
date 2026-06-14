<div class="page-header">
    <h4><i class="bi bi-cart3 me-2"></i>คำสั่งซื้อ</h4>
    <a href="<?= BASE_URL ?>/orders/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>สร้างคำสั่งซื้อ
    </a>
</div>

<div class="table-card">
    <table class="table table-hover">
        <thead>
            <tr><th>เลขที่</th><th>ลูกค้า</th><th>ยอดรวม</th><th>สถานะ</th><th>วันที่</th><th>จัดการ</th></tr>
        </thead>
        <tbody>
        <?php
        $colors = ['pending'=>'warning','processing'=>'info','completed'=>'success','cancelled'=>'danger'];
        $labels = ['pending'=>'รอดำเนินการ','processing'=>'กำลังดำเนิน','completed'=>'สำเร็จ','cancelled'=>'ยกเลิก'];
        foreach ($orders as $o): ?>
        <tr>
            <td><a href="<?= BASE_URL ?>/orders/view/<?= $o['id'] ?>"><?= htmlspecialchars($o['order_number']) ?></a></td>
            <td><?= htmlspecialchars($o['customer_name'] ?? '-') ?></td>
            <td><?= number_format($o['total'], 2) ?> บาท</td>
            <td><span class="badge bg-<?= $colors[$o['status']] ?? 'secondary' ?>"><?= $labels[$o['status']] ?? $o['status'] ?></span></td>
            <td><?= date('d/m/Y H:i', strtotime($o['order_date'])) ?></td>
            <td>
                <a href="<?= BASE_URL ?>/orders/view/<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="<?= BASE_URL ?>/orders/delete/<?= $o['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete ms-1">
                    <i class="bi bi-trash"></i>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">ยังไม่มีคำสั่งซื้อ</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

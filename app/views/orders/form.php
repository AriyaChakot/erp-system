<div class="page-header">
    <h4><i class="bi bi-cart3 me-2"></i>สร้างคำสั่งซื้อ</h4>
    <a href="<?= BASE_URL ?>/orders" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>กลับ</a>
</div>

<form method="post" action="<?= BASE_URL ?>/orders/store">
<div class="row g-3">
    <div class="col-lg-8">
        <!-- Order Info -->
        <div class="form-card mb-3">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">เลขที่คำสั่งซื้อ</label>
                    <input type="text" name="order_number" class="form-control" readonly
                        value="<?= htmlspecialchars($orderNumber) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">ลูกค้า</label>
                    <select name="customer_id" class="form-select">
                        <option value="">-- เลือกลูกค้า --</option>
                        <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">พนักงานขาย</label>
                    <select name="employee_id" class="form-select">
                        <option value="">-- เลือกพนักงาน --</option>
                        <?php foreach ($employees as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">หมายเหตุ</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="form-card">
            <h6 class="fw-bold mb-3">รายการสินค้า</h6>
            <div class="d-flex gap-2 mb-3">
                <select id="productSelect" class="form-select">
                    <option value="">-- เลือกสินค้า --</option>
                    <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>"
                        data-price="<?= $p['price'] ?>"
                        data-name="<?= htmlspecialchars($p['name']) ?>"
                        data-stock="<?= $p['stock'] ?>">
                        <?= htmlspecialchars($p['code']) ?> - <?= htmlspecialchars($p['name']) ?>
                        (คงเหลือ: <?= $p['stock'] ?> <?= htmlspecialchars($p['unit']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" id="addItem" class="btn btn-outline-primary text-nowrap">
                    <i class="bi bi-plus-lg me-1"></i>เพิ่ม
                </button>
            </div>

            <table class="table" id="order-items">
                <thead>
                    <tr>
                        <th>สินค้า</th>
                        <th style="width:100px">จำนวน</th>
                        <th style="width:110px">ราคา/หน่วย</th>
                        <th style="width:120px">รวม</th>
                        <th style="width:60px"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody"></tbody>
            </table>
        </div>
    </div>

    <!-- Summary -->
    <div class="col-lg-4">
        <div class="form-card">
            <h6 class="fw-bold mb-3">สรุปคำสั่งซื้อ</h6>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">ยอดรวม</span>
                <span class="fw-bold fs-5" id="orderTotal">0.00</span>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-check-circle me-1"></i>สร้างคำสั่งซื้อ
            </button>
        </div>
    </div>
</div>
</form>

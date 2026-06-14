<div class="page-header">
    <h4><i class="bi bi-cart-plus me-2"></i>สร้างใบสั่งซื้อใหม่</h4>
    <a href="<?= BASE_URL ?>/purchasing" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>กลับ
    </a>
</div>

<form method="post" action="<?= BASE_URL ?>/purchasing/store">
<div class="row g-3">
    <!-- Header -->
    <div class="col-lg-8">
        <div class="form-card">
            <h6 class="fw-bold mb-3">ข้อมูลใบสั่งซื้อ</h6>
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-medium">PO Number</label>
                    <input type="text" name="po_number" class="form-control" readonly value="<?= htmlspecialchars($poNumber) ?>">
                </div>
                <div class="col-md-7">
                    <label class="form-label fw-medium">Vendor <span class="text-danger">*</span></label>
                    <select name="vendor_id" class="form-select" required>
                        <option value="">— เลือก Vendor —</option>
                        <?php foreach ($vendors as $v): ?>
                        <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">ผู้ขอซื้อ</label>
                    <select name="requested_by" class="form-select">
                        <option value="">— ไม่ระบุ —</option>
                        <?php foreach ($employees as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">วันที่คาดว่าจะได้รับ</label>
                    <input type="date" name="expected_date" class="form-control" min="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">หมายเหตุ</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Side Summary -->
    <div class="col-lg-4">
        <div class="form-card">
            <h6 class="fw-bold mb-3">สรุปยอด</h6>
            <dl class="row mb-0 small">
                <dt class="col-7 text-muted">ยอดก่อน VAT</dt>
                <dd class="col-5 text-end fw-medium" id="summSubtotal">฿0.00</dd>
                <dt class="col-7 text-muted">VAT 7%</dt>
                <dd class="col-5 text-end fw-medium" id="summVat">฿0.00</dd>
                <dt class="col-7 fw-bold">ยอดรวม</dt>
                <dd class="col-5 text-end fw-bold text-primary fs-5" id="summTotal">฿0.00</dd>
            </dl>
        </div>
    </div>
</div>

<!-- Items -->
<div class="form-card mt-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0">รายการสินค้า</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" id="addItem">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มรายการ
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered mb-0" id="itemsTable">
            <thead class="table-light">
                <tr>
                    <th style="width:35%">สินค้า</th>
                    <th style="width:18%">รหัสสินค้า</th>
                    <th style="width:15%">จำนวน</th>
                    <th style="width:18%">ราคาต่อหน่วย (฿)</th>
                    <th style="width:14%" class="text-end">ยอดรวม</th>
                    <th style="width:5%"></th>
                </tr>
            </thead>
            <tbody id="itemsBody"></tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>บันทึกใบสั่งซื้อ
    </button>
    <a href="<?= BASE_URL ?>/purchasing" class="btn btn-outline-secondary">ยกเลิก</a>
</div>
</form>

<script>
const products = <?= json_encode(array_map(fn($p) => [
    'id'   => $p['id'],
    'name' => $p['name'],
    'code' => $p['code'],
    'cost' => (float)$p['cost'],
], $products)) ?>;

let idx = 0;
function addRow(preProductId = '', preName = '', preCode = '', preQty = 1, preCost = 0) {
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
        <td>
            <select name="product_id[]" class="form-select form-select-sm product-select">
                <option value="">— เลือกสินค้า —</option>
                ${products.map(p => `<option value="${p.id}" data-code="${p.code}" data-cost="${p.cost}" ${p.id == preProductId ? 'selected' : ''}>${p.name}</option>`).join('')}
            </select>
            <input type="hidden" name="product_name[]" class="item-name" value="${preName}">
        </td>
        <td><input type="text" name="product_code[]" class="form-control form-control-sm item-code" value="${preCode}" readonly></td>
        <td><input type="number" name="quantity_ordered[]" class="form-control form-control-sm item-qty" value="${preQty}" min="1" required></td>
        <td><input type="number" name="unit_cost[]" class="form-control form-control-sm item-cost" value="${preCost}" min="0" step="0.01" required></td>
        <td class="text-end fw-medium item-sub">฿0.00</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="bi bi-trash"></i></button></td>`;
    document.getElementById('itemsBody').appendChild(tr);

    tr.querySelector('.product-select').addEventListener('change', function() {
        const opt = this.selectedOptions[0];
        tr.querySelector('.item-code').value = opt.dataset.code || '';
        tr.querySelector('.item-name').value = opt.text || '';
        tr.querySelector('.item-cost').value = opt.dataset.cost || 0;
        recalc(tr);
    });
    tr.querySelector('.item-qty').addEventListener('input', () => recalc(tr));
    tr.querySelector('.item-cost').addEventListener('input', () => recalc(tr));
    tr.querySelector('.remove-item').addEventListener('click', () => { tr.remove(); updateSummary(); });

    // Trigger if pre-loaded
    if (preProductId) {
        const opt = tr.querySelector(`.product-select option[value="${preProductId}"]`);
        if (opt) { tr.querySelector('.item-name').value = opt.text; }
        recalc(tr);
    }
}

function recalc(tr) {
    const qty  = parseFloat(tr.querySelector('.item-qty').value) || 0;
    const cost = parseFloat(tr.querySelector('.item-cost').value) || 0;
    const sub  = qty * cost;
    tr.querySelector('.item-sub').textContent = '฿' + sub.toLocaleString('th-TH', {minimumFractionDigits:2});
    updateSummary();
}

function updateSummary() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(tr => {
        const qty  = parseFloat(tr.querySelector('.item-qty').value) || 0;
        const cost = parseFloat(tr.querySelector('.item-cost').value) || 0;
        subtotal  += qty * cost;
    });
    const vat   = subtotal * 0.07;
    const total = subtotal + vat;
    document.getElementById('summSubtotal').textContent = '฿' + subtotal.toLocaleString('th-TH',{minimumFractionDigits:2});
    document.getElementById('summVat').textContent      = '฿' + vat.toLocaleString('th-TH',{minimumFractionDigits:2});
    document.getElementById('summTotal').textContent    = '฿' + total.toLocaleString('th-TH',{minimumFractionDigits:2});
}

document.getElementById('addItem').addEventListener('click', () => addRow());
addRow(); // Start with one row
</script>

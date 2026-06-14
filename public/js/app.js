document.addEventListener('DOMContentLoaded', () => {
    // Sidebar toggle
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        });
    }

    // Order form: add/remove items
    const addItemBtn = document.getElementById('addItem');
    if (addItemBtn) {
        let itemIndex = document.querySelectorAll('.item-row').length;

        addItemBtn.addEventListener('click', () => {
            const tbody = document.getElementById('itemsBody');
            const productSelect = document.getElementById('productSelect');
            const option = productSelect.options[productSelect.selectedIndex];
            if (!option.value) return alert('กรุณาเลือกสินค้าก่อน');

            const price = parseFloat(option.dataset.price) || 0;
            const row = document.createElement('tr');
            row.className = 'item-row';
            row.innerHTML = `
                <td>
                    ${option.text}
                    <input type="hidden" name="product_id[]" value="${option.value}">
                    <input type="hidden" name="product_name[]" value="${option.dataset.name}">
                    <input type="hidden" name="price[]" class="item-price" value="${price}">
                </td>
                <td><input type="number" name="quantity[]" class="form-control form-control-sm item-qty"
                    value="1" min="1" max="${option.dataset.stock}" style="width:80px"></td>
                <td class="item-unit-price">${price.toLocaleString('th-TH', {minimumFractionDigits:2})}</td>
                <td class="item-subtotal">${price.toLocaleString('th-TH', {minimumFractionDigits:2})}</td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="bi bi-trash"></i></button></td>
            `;
            tbody.appendChild(row);
            itemIndex++;
            updateTotal();
        });

        document.getElementById('itemsBody').addEventListener('input', (e) => {
            if (e.target.classList.contains('item-qty')) updateTotal();
        });

        document.getElementById('itemsBody').addEventListener('click', (e) => {
            if (e.target.closest('.remove-item')) {
                e.target.closest('.item-row').remove();
                updateTotal();
            }
        });
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const qty = parseInt(row.querySelector('.item-qty').value) || 0;
            const sub = price * qty;
            total += sub;
            row.querySelector('.item-subtotal').textContent = sub.toLocaleString('th-TH', {minimumFractionDigits:2});
        });
        const el = document.getElementById('orderTotal');
        if (el) el.textContent = total.toLocaleString('th-TH', {minimumFractionDigits:2});
    }

    // Delete confirmation
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!confirm('ยืนยันการลบ?')) e.preventDefault();
        });
    });
});

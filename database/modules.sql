-- ============================================================
-- ERP Modules: Inventory, Purchasing, Accounting, HR & Payroll
-- Run: mysql -u root -p erp_db < database/modules.sql
-- ============================================================
USE erp_db;

-- ============================================================
-- MODULE: INVENTORY
-- ============================================================

CREATE TABLE IF NOT EXISTS warehouses (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    code        VARCHAR(20) UNIQUE NOT NULL,
    name        VARCHAR(100) NOT NULL,
    location    VARCHAR(200),
    manager_id  INT,
    status      ENUM('active','inactive') DEFAULT 'active',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (manager_id) REFERENCES employees(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stock_items (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    product_id   INT NOT NULL,
    warehouse_id INT NOT NULL,
    quantity     INT NOT NULL DEFAULT 0,
    min_quantity INT NOT NULL DEFAULT 5,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_product_warehouse (product_id, warehouse_id),
    FOREIGN KEY (product_id)   REFERENCES products(id)   ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stock_movements (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    movement_type    ENUM('in','out','transfer','adjustment') NOT NULL,
    reference_type   VARCHAR(30),
    reference_id     INT,
    product_id       INT NOT NULL,
    warehouse_id     INT NOT NULL,
    warehouse_dest_id INT,
    quantity         INT NOT NULL,
    unit_cost        DECIMAL(12,2) DEFAULT 0,
    balance_before   INT,
    balance_after    INT,
    notes            TEXT,
    created_by       INT,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id)        REFERENCES products(id)   ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id)      REFERENCES warehouses(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by)        REFERENCES employees(id)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stock_batches (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    product_id      INT NOT NULL,
    warehouse_id    INT NOT NULL,
    batch_number    VARCHAR(50) NOT NULL,
    received_date   DATE NOT NULL,
    expiry_date     DATE,
    quantity        INT NOT NULL DEFAULT 0,
    unit_cost       DECIMAL(12,2) NOT NULL DEFAULT 0,
    reference_gr_id INT,
    FOREIGN KEY (product_id)   REFERENCES products(id)   ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODULE: PURCHASING
-- ============================================================

CREATE TABLE IF NOT EXISTS vendors (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    code           VARCHAR(20) UNIQUE NOT NULL,
    name           VARCHAR(150) NOT NULL,
    contact_person VARCHAR(100),
    email          VARCHAR(100),
    phone          VARCHAR(30),
    address        TEXT,
    tax_id         VARCHAR(20),
    payment_terms  INT DEFAULT 30,
    status         ENUM('active','inactive') DEFAULT 'active',
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS purchase_orders (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    po_number     VARCHAR(20) UNIQUE NOT NULL,
    vendor_id     INT,
    requested_by  INT,
    approved_by   INT,
    status        ENUM('draft','approved','sent','partial','received','cancelled') DEFAULT 'draft',
    subtotal      DECIMAL(14,2) DEFAULT 0,
    vat_amount    DECIMAL(14,2) DEFAULT 0,
    total         DECIMAL(14,2) DEFAULT 0,
    expected_date DATE,
    received_date DATE,
    notes         TEXT,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id)    REFERENCES vendors(id)   ON DELETE SET NULL,
    FOREIGN KEY (requested_by) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by)  REFERENCES employees(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS purchase_order_items (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    po_id             INT NOT NULL,
    product_id        INT,
    product_name      VARCHAR(150) NOT NULL,
    product_code      VARCHAR(20),
    quantity_ordered  INT NOT NULL DEFAULT 1,
    quantity_received INT NOT NULL DEFAULT 0,
    unit_cost         DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal          DECIMAL(14,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (po_id)      REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS goods_receipts (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    gr_number    VARCHAR(20) UNIQUE NOT NULL,
    po_id        INT NOT NULL,
    received_by  INT,
    receipt_date DATE NOT NULL,
    status       ENUM('pending','completed') DEFAULT 'completed',
    notes        TEXT,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (po_id)       REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (received_by) REFERENCES employees(id)       ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS goods_receipt_items (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    gr_id             INT NOT NULL,
    po_item_id        INT NOT NULL,
    product_id        INT,
    quantity_received INT NOT NULL DEFAULT 0,
    unit_cost         DECIMAL(12,2) NOT NULL DEFAULT 0,
    warehouse_id      INT,
    FOREIGN KEY (gr_id)      REFERENCES goods_receipts(id)         ON DELETE CASCADE,
    FOREIGN KEY (po_item_id) REFERENCES purchase_order_items(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)               ON DELETE SET NULL,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)           ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODULE: ACCOUNTING
-- ============================================================

CREATE TABLE IF NOT EXISTS chart_of_accounts (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    code         VARCHAR(10) UNIQUE NOT NULL,
    name         VARCHAR(150) NOT NULL,
    account_type ENUM('asset','liability','equity','revenue','expense') NOT NULL,
    parent_id    INT,
    is_active    TINYINT(1) DEFAULT 1,
    FOREIGN KEY (parent_id) REFERENCES chart_of_accounts(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS journal_entries (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    entry_number   VARCHAR(20) UNIQUE NOT NULL,
    reference_type VARCHAR(30),
    reference_id   INT,
    entry_date     DATE NOT NULL,
    description    TEXT,
    status         ENUM('draft','posted','voided') DEFAULT 'draft',
    created_by     INT,
    posted_by      INT,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (posted_by)  REFERENCES employees(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS journal_lines (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    entry_id    INT NOT NULL,
    account_id  INT NOT NULL,
    description VARCHAR(255),
    debit       DECIMAL(14,2) DEFAULT 0,
    credit      DECIMAL(14,2) DEFAULT 0,
    FOREIGN KEY (entry_id)  REFERENCES journal_entries(id)    ON DELETE CASCADE,
    FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoices (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(20) UNIQUE NOT NULL,
    invoice_type   ENUM('sale','purchase') NOT NULL,
    reference_id   INT,
    customer_id    INT,
    vendor_id      INT,
    issue_date     DATE NOT NULL,
    due_date       DATE NOT NULL,
    subtotal       DECIMAL(14,2) DEFAULT 0,
    vat_rate       DECIMAL(5,2)  DEFAULT 7.00,
    vat_amount     DECIMAL(14,2) DEFAULT 0,
    total          DECIMAL(14,2) DEFAULT 0,
    paid_amount    DECIMAL(14,2) DEFAULT 0,
    status         ENUM('draft','sent','partial','paid','overdue','cancelled') DEFAULT 'draft',
    notes          TEXT,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (vendor_id)   REFERENCES vendors(id)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    payment_number VARCHAR(20) UNIQUE NOT NULL,
    invoice_id     INT NOT NULL,
    payment_date   DATE NOT NULL,
    amount         DECIMAL(14,2) NOT NULL,
    payment_method ENUM('cash','bank_transfer','cheque','credit_card') NOT NULL,
    reference_no   VARCHAR(50),
    notes          TEXT,
    created_by     INT,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id)   ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES employees(id)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS expenses (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    expense_number VARCHAR(20) UNIQUE NOT NULL,
    category       VARCHAR(50) NOT NULL,
    description    VARCHAR(255) NOT NULL,
    amount         DECIMAL(14,2) NOT NULL,
    expense_date   DATE NOT NULL,
    account_id     INT,
    approved_by    INT,
    status         ENUM('pending','approved','rejected','paid') DEFAULT 'pending',
    receipt_no     VARCHAR(50),
    notes          TEXT,
    created_by     INT,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id)  REFERENCES chart_of_accounts(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES employees(id)          ON DELETE SET NULL,
    FOREIGN KEY (created_by)  REFERENCES employees(id)          ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODULE: HR & PAYROLL
-- ============================================================

CREATE TABLE IF NOT EXISTS leave_types (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(50) NOT NULL,
    days_per_year INT DEFAULT 0,
    is_paid      TINYINT(1) DEFAULT 1,
    requires_doc TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leave_balances (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    employee_id   INT NOT NULL,
    leave_type_id INT NOT NULL,
    year          YEAR NOT NULL,
    allocated     DECIMAL(5,1) DEFAULT 0,
    used          DECIMAL(5,1) DEFAULT 0,
    UNIQUE KEY uq_emp_type_year (employee_id, leave_type_id, year),
    FOREIGN KEY (employee_id)   REFERENCES employees(id)   ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leave_requests (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    request_number VARCHAR(20) UNIQUE NOT NULL,
    employee_id    INT NOT NULL,
    leave_type_id  INT NOT NULL,
    start_date     DATE NOT NULL,
    end_date       DATE NOT NULL,
    days_count     DECIMAL(5,1) NOT NULL,
    reason         TEXT,
    status         ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending',
    approved_by    INT,
    approved_at    TIMESTAMP NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id)   REFERENCES employees(id)   ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by)   REFERENCES employees(id)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS overtime_requests (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    request_number VARCHAR(20) UNIQUE NOT NULL,
    employee_id    INT NOT NULL,
    ot_date        DATE NOT NULL,
    start_time     TIME NOT NULL,
    end_time       TIME NOT NULL,
    ot_hours       DECIMAL(5,2) NOT NULL,
    ot_type        ENUM('weekday','weekend','holiday') NOT NULL,
    ot_rate        DECIMAL(4,2) DEFAULT 1.50,
    reason         TEXT,
    status         ENUM('pending','approved','rejected') DEFAULT 'pending',
    approved_by    INT,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES employees(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payroll_periods (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    period_name VARCHAR(50) NOT NULL,
    year        YEAR NOT NULL,
    month       TINYINT NOT NULL,
    start_date  DATE NOT NULL,
    end_date    DATE NOT NULL,
    pay_date    DATE NOT NULL,
    status      ENUM('open','processing','closed') DEFAULT 'open',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payroll_slips (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    slip_number      VARCHAR(20) UNIQUE NOT NULL,
    period_id        INT NOT NULL,
    employee_id      INT NOT NULL,
    base_salary      DECIMAL(12,2) NOT NULL DEFAULT 0,
    ot_amount        DECIMAL(12,2) DEFAULT 0,
    allowance        DECIMAL(12,2) DEFAULT 0,
    bonus            DECIMAL(12,2) DEFAULT 0,
    gross_salary     DECIMAL(12,2) DEFAULT 0,
    social_security  DECIMAL(12,2) DEFAULT 0,
    income_tax       DECIMAL(12,2) DEFAULT 0,
    other_deductions DECIMAL(12,2) DEFAULT 0,
    total_deductions DECIMAL(12,2) DEFAULT 0,
    net_salary       DECIMAL(12,2) DEFAULT 0,
    working_days     INT DEFAULT 0,
    leave_days       DECIMAL(5,1) DEFAULT 0,
    ot_hours         DECIMAL(7,2) DEFAULT 0,
    status           ENUM('draft','approved','paid') DEFAULT 'draft',
    notes            TEXT,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (period_id)   REFERENCES payroll_periods(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id)       ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA
-- ============================================================

INSERT INTO leave_types (name, days_per_year, is_paid, requires_doc) VALUES
('ลาป่วย',       30, 1, 0),
('ลากิจ',         3, 1, 0),
('ลาพักร้อน',    10, 1, 0),
('ลาคลอด',       98, 1, 1),
('ลาไม่รับเงิน',  0, 0, 0);

INSERT INTO chart_of_accounts (code, name, account_type) VALUES
('1100', 'เงินสด',                   'asset'),
('1200', 'ลูกหนี้การค้า',            'asset'),
('1300', 'สินค้าคงคลัง',             'asset'),
('2100', 'เจ้าหนี้การค้า',           'liability'),
('2200', 'ภาษีมูลค่าเพิ่มค้างจ่าย', 'liability'),
('3000', 'ทุน',                      'equity'),
('4000', 'รายได้จากการขาย',          'revenue'),
('5000', 'ต้นทุนขาย',               'expense'),
('5100', 'ค่าใช้จ่ายในการขาย',      'expense'),
('5200', 'ค่าใช้จ่ายในการบริหาร',   'expense'),
('5300', 'เงินเดือนและค่าจ้าง',     'expense'),
('5400', 'ค่าล่วงเวลา',             'expense');

INSERT INTO warehouses (code, name, location) VALUES
('WH-01', 'คลังกลาง',   'อาคาร A ชั้น 1'),
('WH-02', 'คลังสาขา',   'อาคาร B ชั้น 2');

-- Seed stock_items: sync existing product stock to WH-01
INSERT INTO stock_items (product_id, warehouse_id, quantity, min_quantity)
SELECT id, 1, stock, 10 FROM products WHERE status = 'active'
ON DUPLICATE KEY UPDATE quantity = VALUES(quantity);

-- Seed vendor
INSERT INTO vendors (code, name, contact_person, email, phone, payment_terms) VALUES
('VEN-001', 'บริษัท ซัพพลาย ไทย จำกัด', 'คุณสมศักดิ์', 'supply@thai.com', '02-555-1111', 30),
('VEN-002', 'ห้างหุ้นส่วน เทคโน พาร์ท',   'คุณวิไล',    'techno@part.com', '02-555-2222', 15);

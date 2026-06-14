-- ERP Database Schema
CREATE DATABASE IF NOT EXISTS erp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE erp_db;

CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    department VARCHAR(50),
    position VARCHAR(50),
    salary DECIMAL(12,2) DEFAULT 0,
    hire_date DATE,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    company VARCHAR(100),
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock INT DEFAULT 0,
    unit VARCHAR(20) DEFAULT 'unit',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    customer_id INT,
    employee_id INT,
    total DECIMAL(12,2) DEFAULT 0,
    status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
    notes TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(150),
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Sample data
INSERT INTO employees (name, email, phone, department, position, salary, hire_date) VALUES
('สมชาย ใจดี', 'somchai@erp.com', '081-000-0001', 'IT', 'Developer', 35000, '2023-01-15'),
('สมหญิง รักงาน', 'somying@erp.com', '081-000-0002', 'Sales', 'Sales Manager', 45000, '2022-06-01'),
('วิชัย มานะ', 'wichai@erp.com', '081-000-0003', 'Accounting', 'Accountant', 30000, '2023-03-10');

INSERT INTO customers (name, email, phone, address, company) VALUES
('บริษัท ก ไก่ จำกัด', 'kakai@email.com', '02-111-1111', '123 ถ.สุขุมวิท กรุงเทพ', 'บริษัท ก ไก่ จำกัด'),
('ห้างหุ้นส่วน ข ไข่', 'khokhai@email.com', '02-222-2222', '456 ถ.พระราม 4 กรุงเทพ', 'ห้างหุ้นส่วน ข ไข่'),
('นาย ค ควาย', 'khokwai@email.com', '081-333-3333', '789 ถ.รัชดา กรุงเทพ', NULL);

INSERT INTO products (code, name, category, price, cost, stock, unit) VALUES
('P001', 'คอมพิวเตอร์ตั้งโต๊ะ', 'Electronics', 25000, 18000, 50, 'เครื่อง'),
('P002', 'จอมอนิเตอร์ 24 นิ้ว', 'Electronics', 6500, 4500, 80, 'จอ'),
('P003', 'แป้นพิมพ์ไร้สาย', 'Electronics', 1200, 800, 120, 'ชิ้น'),
('P004', 'เมาส์ออปติคัล', 'Electronics', 450, 280, 200, 'ชิ้น'),
('P005', 'กระดาษ A4 (รีม)', 'Stationery', 120, 80, 500, 'รีม');

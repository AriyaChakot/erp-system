# ERP System — PHP MVC

ระบบ ERP สำหรับธุรกิจขนาดกลาง-เล็ก พัฒนาด้วย PHP MVC ไม่ใช้ Framework ประกอบด้วย 8 โมดูลหลัก

## โมดูลที่มี

| โมดูล | URL | คำอธิบาย |
|-------|-----|----------|
| Dashboard | `/dashboard` | ภาพรวมตัวเลขสำคัญ |
| สินค้า | `/products` | จัดการสินค้าและราคา |
| ลูกค้า | `/customers` | จัดการข้อมูลลูกค้า |
| พนักงาน | `/employees` | จัดการข้อมูลพนักงาน |
| คำสั่งซื้อ | `/orders` | รับออเดอร์และติดตามสถานะ |
| คลังสินค้า | `/inventory` | Stock, การเคลื่อนไหว, โอนย้าย |
| จัดซื้อ | `/purchasing` | PO, Vendor, รับสินค้า (3-Way Matching) |
| บัญชี | `/accounting` | Invoice, ค่าใช้จ่าย, P&L, Journal |
| HR & เงินเดือน | `/hr` | ลา, OT, คำนวณเงินเดือน |

---

## การติดตั้ง (XAMPP)

### 1. วางโปรเจค
```
C:\xampp\htdocs\erp\
```

### 2. ตั้งค่า Database
```bash
# copy ไฟล์ตัวอย่าง
cp config/database.example.php config/database.php
```

แก้ไข `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');               // ใส่รหัสผ่านของคุณ
define('DB_NAME', 'erp_system');
define('DB_CHARSET', 'utf8mb4');
define('BASE_URL', 'http://localhost/erp');  // ปรับให้ตรงกับ port
define('APP_NAME', 'ERP System');
```

### 3. Import Database
เปิด phpMyAdmin สร้าง database ชื่อ `erp_system` แล้ว import ตามลำดับ:

```bash
mysql -u root -p erp_system < database/schema.sql
mysql -u root -p erp_system < database/modules.sql
```

หรือผ่าน phpMyAdmin: Import → เลือกไฟล์ → Go

### 4. เข้าใช้งาน
```
http://localhost/erp/login
```

**บัญชีเริ่มต้น:**
| Username | Password | Role |
|----------|----------|------|
| `admin` | `admin123` | Admin |

---

## โครงสร้างโปรเจค

```
erp/
├── index.php                   ← Front Controller + Router
├── .htaccess                   ← URL Rewriting
├── config/
│   ├── database.php            ← DB config (ไม่ commit ขึ้น Git)
│   └── database.example.php   ← Template สำหรับ config
├── database/
│   ├── schema.sql              ← ตาราง core (products, orders, employees...)
│   └── modules.sql             ← ตาราง ERP modules (inventory, HR, accounting...)
├── app/
│   ├── controllers/
│   │   ├── BaseController.php
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ProductController.php
│   │   ├── CustomerController.php
│   │   ├── EmployeeController.php
│   │   ├── OrderController.php
│   │   ├── InventoryController.php
│   │   ├── PurchasingController.php
│   │   ├── AccountingController.php
│   │   └── HRController.php
│   ├── models/
│   │   ├── BaseModel.php
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Customer.php
│   │   ├── Employee.php
│   │   ├── Order.php
│   │   ├── Warehouse.php
│   │   ├── StockItem.php
│   │   ├── StockMovement.php
│   │   ├── StockBatch.php
│   │   ├── Vendor.php
│   │   ├── PurchaseOrder.php
│   │   ├── Invoice.php
│   │   ├── JournalEntry.php
│   │   ├── Expense.php
│   │   ├── LeaveRequest.php
│   │   ├── OvertimeRequest.php
│   │   └── PayrollSlip.php
│   └── views/
│       ├── layout/
│       │   ├── header.php      ← Sidebar + Topbar
│       │   └── footer.php
│       ├── dashboard/
│       ├── products/
│       ├── customers/
│       ├── employees/
│       ├── orders/
│       ├── inventory/
│       ├── purchasing/
│       ├── accounting/
│       └── hr/
└── public/
    ├── css/style.css
    └── js/app.js
```

---

## เทคโนโลยี

- **Backend**: PHP 8.x, PDO MySQL
- **Pattern**: MVC — Front Controller, BaseController, BaseModel
- **Frontend**: Bootstrap 5, Bootstrap Icons
- **Database**: MySQL 5.7+
- **Auth**: PHP Sessions, `password_hash()` / `password_verify()` (bcrypt)

---

## Roles

| Role | สิทธิ์ |
|------|--------|
| `pending` | รอ admin อนุมัติ — เข้าระบบไม่ได้ |
| `user` | ใช้งาน ERP ได้ทุกโมดูล |
| `admin` | ทุกสิทธิ์ + อนุมัติ PO/ลา/เงินเดือน + จัดการ users |

---

## Business Logic สำคัญ

- **3-Way Matching**: PO → Goods Receipt → Invoice
- **FIFO Inventory**: บันทึก batch ทุก lot รับเข้า
- **Double-Entry Accounting**: ทุก transaction สร้าง journal entry อัตโนมัติ
- **Thai Payroll**: หักภาษีแบบก้าวหน้า + ประกันสังคม 5% (สูงสุด ฿750/เดือน)

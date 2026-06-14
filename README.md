# Simple ERP System — PHP MVC

## โครงสร้างโปรเจกต์

```
erp/
├── index.php               ← Front Controller + Router
├── setup_admin.php         ← สร้าง admin ครั้งแรก (ลบหลังใช้งาน!)
├── .htaccess               ← URL Rewriting
├── config/
│   └── database.php        ← DB config & constants
├── app/
│   ├── controllers/
│   │   ├── BaseController.php
│   │   ├── AuthController.php      ← login / logout / signup
│   │   ├── AdminController.php     ← จัดการ user & แจ้งปัญหา
│   │   ├── ReportController.php    ← แจ้งปัญหา (user)
│   │   ├── DashboardController.php
│   │   ├── ProductController.php
│   │   ├── CustomerController.php
│   │   ├── EmployeeController.php
│   │   └── OrderController.php
│   ├── models/
│   │   ├── BaseModel.php
│   │   ├── User.php
│   │   ├── IssueReport.php
│   │   ├── Product.php
│   │   ├── Customer.php
│   │   ├── Employee.php
│   │   └── Order.php
│   └── views/
│       ├── layout/
│       │   ├── header.php          ← sidebar + topbar (ใช้หลัง login)
│       │   ├── footer.php
│       │   ├── header_auth.php     ← layout เรียบสำหรับหน้า auth
│       │   └── footer_auth.php
│       ├── auth/
│       │   ├── login.php
│       │   ├── signup.php
│       │   └── pending.php         ← รอ admin อนุมัติ
│       ├── admin/
│       │   ├── index.php           ← Admin Dashboard
│       │   ├── users.php           ← จัดการ roles
│       │   └── notifications.php   ← ตอบรับแจ้งปัญหา
│       ├── reports/
│       │   ├── index.php           ← รายการแจ้งปัญหาของฉัน
│       │   └── create.php
│       ├── dashboard/
│       ├── products/
│       ├── customers/
│       ├── employees/
│       └── orders/
├── public/
│   ├── css/style.css
│   └── js/app.js
└── database/
    ├── schema.sql          ← ตาราง ERP หลัก + ข้อมูลตัวอย่าง
    └── auth.sql            ← ตาราง users + issue_reports
```

## การติดตั้ง

### 1. วาง project

Copy โฟลเดอร์ `erp/` ไปไว้ที่ `htdocs/erp/` (XAMPP)

### 2. แก้ไข config

`config/database.php`
```php
define('DB_HOST',    'localhost');
define('DB_NAME',    'erp_db');
define('DB_USER',    'root');
define('DB_PASS',    '');           // ใส่รหัสผ่านถ้ามี
define('DB_CHARSET', 'utf8mb4');
define('BASE_URL',   'http://localhost:8080/erp'); // ปรับ port ให้ตรง
```

### 3. Import Database

เปิด **phpMyAdmin** แล้ว import ตามลำดับ:

1. `database/schema.sql` — สร้างฐานข้อมูลและตาราง ERP
2. `database/auth.sql`   — สร้างตาราง `users` และ `issue_reports`

หรือรันผ่าน terminal:
```bash
mysql -u root -p erp_db < database/schema.sql
mysql -u root -p erp_db < database/auth.sql
```

### 4. สร้าง Admin คนแรก

เปิดเบราว์เซอร์ไปที่:
```
http://localhost:8080/erp/setup_admin.php
```
> **หมายเหตุ**: ต้องมี `.php` ต่อท้าย (ไฟล์นี้เข้าถึงตรงๆ ไม่ผ่าน router)

ระบบจะสร้าง admin ด้วย:
- Email: `admin@erp.local`
- Password: `admin1234`

**ลบไฟล์ `setup_admin.php` ทันทีหลังใช้งาน!**

### 5. เข้าใช้งาน

```
http://localhost:8080/erp/login
```

---

## ระบบ Roles

| Role | สิทธิ์ |
|------|--------|
| `pending` | สมัครแล้ว รอ admin อนุมัติ — เข้าระบบไม่ได้ |
| `user` | เข้าใช้งาน ERP ได้ทุก module, แจ้งปัญหาได้ |
| `admin` | ทุกสิทธิ์ + จัดการ users + ตอบรับแจ้งปัญหา |

---

## URL Routes

### Auth
| URL | Method | คำอธิบาย |
|-----|--------|----------|
| `/login` | GET/POST | เข้าสู่ระบบ |
| `/signup` | GET/POST | สมัครสมาชิก |
| `/logout` | GET | ออกจากระบบ |
| `/pending` | GET | หน้ารอการอนุมัติ |

### ERP Modules (ต้อง login + role ≠ pending)
| URL | คำอธิบาย |
|-----|----------|
| `/dashboard` | Dashboard ภาพรวม |
| `/products` | จัดการสินค้า |
| `/customers` | จัดการลูกค้า |
| `/employees` | จัดการพนักงาน |
| `/orders` | จัดการคำสั่งซื้อ |

### แจ้งปัญหา (user & admin)
| URL | คำอธิบาย |
|-----|----------|
| `/report` | รายการปัญหาของฉัน |
| `/report/create` | แจ้งปัญหาใหม่ |

### Admin
| URL | คำอธิบาย |
|-----|----------|
| `/admin` | Admin Dashboard |
| `/admin/users` | จัดการ users & roles |
| `/admin/notifications` | ดู/ตอบรับแจ้งปัญหา |

---

## เทคโนโลยีที่ใช้

- **Backend**: PHP 8.x, PDO (MySQL)
- **Pattern**: MVC (Model-View-Controller)
- **Auth**: PHP Sessions, `password_hash()` / `password_verify()`
- **Frontend**: Bootstrap 5, Bootstrap Icons
- **Database**: MySQL 5.7+

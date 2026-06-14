<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends BaseController {
    private User $model;

    public function __construct() {
        $this->model = new User();
    }

    public function login(): void {
        if (!empty($_SESSION['user_id'])) $this->redirect('/');
        $this->renderAuth('auth/login', ['pageTitle' => 'เข้าสู่ระบบ']);
    }

    public function loginPost(): void {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->model->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            $this->renderAuth('auth/login', [
                'pageTitle' => 'เข้าสู่ระบบ',
                'error'     => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
                'email'     => htmlspecialchars($email),
            ]);
            return;
        }

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'];

        if ($user['role'] === 'pending') $this->redirect('/pending');
        $this->redirect('/');
    }

    public function signup(): void {
        if (!empty($_SESSION['user_id'])) $this->redirect('/');
        $this->renderAuth('auth/signup', ['pageTitle' => 'สมัครสมาชิก']);
    }

    public function signupPost(): void {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        $errors = [];
        if (!$name)                                        $errors[] = 'กรุณากรอกชื่อ';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))   $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
        if (strlen($password) < 6)                        $errors[] = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
        if ($password !== $confirm)                        $errors[] = 'รหัสผ่านไม่ตรงกัน';
        if (!$errors && $this->model->emailExists($email)) $errors[] = 'อีเมลนี้ถูกใช้งานแล้ว';

        if ($errors) {
            $this->renderAuth('auth/signup', [
                'pageTitle' => 'สมัครสมาชิก',
                'errors'    => $errors,
                'name'      => htmlspecialchars($name),
                'email'     => htmlspecialchars($email),
            ]);
            return;
        }

        $this->model->insert([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => 'pending',
        ]);

        $this->setFlash('success', 'สมัครสมาชิกสำเร็จ! รอ Admin อนุมัติบัญชีของคุณก่อนเข้าใช้งาน');
        $this->redirect('/login');
    }

    public function logout(): void {
        session_destroy();
        header("Location: " . BASE_URL . "/login");
        exit;
    }

    public function pending(): void {
        if (empty($_SESSION['user_id'])) $this->redirect('/login');
        if ($_SESSION['user_role'] !== 'pending') $this->redirect('/');
        $this->renderAuth('auth/pending', [
            'pageTitle' => 'รอการอนุมัติ',
            'name'      => $_SESSION['user_name'],
        ]);
    }
}

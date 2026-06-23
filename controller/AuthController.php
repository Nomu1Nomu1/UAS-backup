<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../model/User.php';

class AuthController
{

    public function login(): void
    {
        if (is_logged_in()) {
            redirect('dashboard/index');
        }

        $error   = '';
        $success = '';

        if (isset($_SESSION['success'])) {
            $success = $_SESSION['success'];
            unset($_SESSION['success']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password']      ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Username dan password tidak boleh kosong.';
            } else {
                $userModel = new User();
                $user      = $userModel->findByUsername($username);

                if ($user && $userModel->verifyPassword($password, $user['password'])) {
                    $_SESSION['users'] = [
                        'id'       => $user['user_id'],
                        'nama'     => $user['nama'],
                        'role'     => $user['role'],
                        'username' => $user['username'],
                        'email'    => $user['email'] ?? '',
                    ];
                    redirect('dashboard/index');
                } else {
                    $error = 'Username atau password salah.';
                }
            }
        }

        $pageTitle = 'Login';
        require_once __DIR__ . '/../view/auth/login.php';
    }


    public function register(): void
    {
        if (is_logged_in()) {
            redirect('dashboard/index');
        }

        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama             = trim($_POST['nama']             ?? '');
            $username         = trim($_POST['username']         ?? '');
            $email            = trim($_POST['email']            ?? '') ?: null;
            $no_hp            = trim($_POST['no_hp']            ?? '') ?: null;
            $role             = trim($_POST['role']             ?? '');
            $password         = $_POST['password']              ?? '';
            $password_confirm = $_POST['password_confirm']      ?? '';

            $userModel = new User();

            if (empty($nama) || empty($username) || empty($role) || empty($password)) {
                $error = 'Nama, username, peran, dan password wajib diisi.';

            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $error = 'Username hanya boleh huruf, angka, dan underscore (tanpa spasi).';

            } elseif (strlen($password) < 8) {
                $error = 'Password minimal 8 karakter.';

            } elseif ($password !== $password_confirm) {
                $error = 'Konfirmasi password tidak cocok.';

            } elseif (!in_array($role, ['admin', 'kasir'])) {
                $error = 'Peran tidak valid.';

            } elseif ($userModel->usernameExists($username)) {
                $error = 'Username sudah digunakan, pilih yang lain.';

            } elseif ($email && $userModel->emailExists($email)) {
                $error = 'Email sudah terdaftar.';

            } else {
                if ($userModel->create($username, $password, $nama, $role, $email, $no_hp)) {
                    $_SESSION['success'] = 'Akun berhasil dibuat! Silakan login.';
                    redirect('auth/login');
                } else {
                    $error = 'Gagal menyimpan data. Coba lagi.';
                }
            }
        }

        $pageTitle = 'Register';
        require_once __DIR__ . '/../view/auth/register.php';
    }

    public function logout(): void
    {
        session_destroy();
        redirect('view/auth/login');
    }
}
<?php
require_once __DIR__ . '/../config/db.php';

class UserController
{
    public function index(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['admin']);

        $db    = getDB();
        $search = trim($_GET['search'] ?? '');
    $status = $_GET['status'] ?? '';

    $where  = [];
    $params = [];
    $types  = '';

    if ($search !== '') {
        $where[]  = "(nama LIKE ? OR username LIKE ?)";
        $like     = "%$search%";
        $params[] = $like;
        $params[] = $like;
        $types   .= 'ss';
    }

    if ($status !== '') {
        $where[]  = "is_active = ?";
        $params[] = $status;
        $types   .= 's';
    }

    $sql = "SELECT user_id, username, nama, role, email, no_hp, is_active, createdAt FROM users";

    if ($where) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY user_id DESC";

    if ($params) {
        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $users = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

        $pageTitle = 'Manajemen User';
        require_once __DIR__ . '/../view/user/index.php';
    }

    public function create(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password']  ?? '';
            $nama     = trim($_POST['nama']   ?? '');
            $role     = $_POST['role']        ?? '';
            $email    = trim($_POST['email']  ?? '') ?: null;
            $no_hp    = trim($_POST['no_hp']  ?? '') ?: null;
            $is_active = $_POST['is_active'] ?? 'Y';
            $valid_roles = ['owner', 'admin', 'kasir'];

            if (empty($username) || empty($password) || empty($nama) || !in_array($role, $valid_roles)) {
                $error = 'Semua field wajib diisi dengan benar.';
            } elseif (strlen($password) < 6) {
                $error = 'Password minimal 6 karakter.';
            } else {
                $db   = getDB();
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $now  = date('Y-m-d H:i:s');

                $stmt = $db->prepare(
                    "INSERT INTO users
                        (username, password, nama, role, email, no_hp, is_active, createdAt, updatedAt)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->bind_param('sssssssss', $username, $hash, $nama, $role, $email, $no_hp, $is_active, $now, $now);

                if ($stmt->execute()) {
                    redirect('user/index');
                } else {
                    $error = 'Username atau email sudah terdaftar.';
                }
            }
        }

        $pageTitle = 'Tambah User';
        require_once __DIR__ . '/../view/user/create.php';
    }

    public function edit(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $id = (int) ($_GET['id'] ?? 0);
        $db = getDB();

        $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) redirect('user/index');

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama     = trim($_POST['nama']   ?? '');
            $role     = $_POST['role']        ?? '';
            $email    = trim($_POST['email']  ?? '') ?: null;
            $no_hp    = trim($_POST['no_hp']  ?? '') ?: null;
            $password = $_POST['password']    ?? '';
            $is_active = $_POST['is_active'] ?? 'Y';

            $valid_roles = ['owner', 'admin', 'kasir'];

            if (empty($nama) || !in_array($role, $valid_roles)) {
                $error = 'Nama dan role wajib diisi dengan benar.';
            } elseif (!empty($password) && strlen($password) < 6) {
                $error = 'Password minimal 6 karakter.';
            } else {
                $now = date('Y-m-d H:i:s');

                if (!empty($password)) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare(
                        "UPDATE users
                         SET nama = ?, role = ?, email = ?, no_hp = ?, password = ?, is_active = ?, updatedAt = ?
                         WHERE user_id = ?"
                    );
                    $stmt->bind_param('sssssssi', $nama, $role, $email, $no_hp, $is_active, $hash, $now, $id);
                } else {
                    $stmt = $db->prepare(
                        "UPDATE users
                         SET nama = ?, role = ?, email = ?, no_hp = ?, is_active = ?, updatedAt = ?
                         WHERE user_id = ?"
                    );
                    $stmt->bind_param('ssssssi', $nama, $role, $email, $no_hp, $is_active, $now, $id);
                }

                $stmt->execute();
                redirect('user/index');
            }
        }

        $pageTitle = 'Edit User';
        require_once __DIR__ . '/../view/user/edit.php';
    }

    public function toggleAktif(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $id = (int) ($_GET['id'] ?? 0);

        if ($id === (int) $_SESSION['users']['id']) {
            $_SESSION['flash'] = 'Tidak dapat menonaktifkan akun Anda sendiri.';
            redirect('user/index');
        }

        $db   = getDB();
        $stmt = $db->prepare("SELECT is_active FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) redirect('user/index');

        $newStatus = $user['is_active'] === 'Y' ? 'N' : 'Y';
        $now       = date('Y-m-d H:i:s');

        $stmt = $db->prepare("UPDATE users SET is_active = ?, updatedAt = ? WHERE user_id = ?");
        $stmt->bind_param('ssi', $newStatus, $now, $id);
        $stmt->execute();

        $_SESSION['flash'] = 'Status user berhasil diperbarui.';
        redirect('user/index');
    }

    public function delete(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $id = (int) ($_GET['id'] ?? 0);

        if ($id === (int) $_SESSION['users']['id']) {
            $_SESSION['flash'] = 'Tidak dapat menghapus akun Anda sendiri.';
            redirect('user/index');
        }

        $db   = getDB();
        $stmt = $db->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $_SESSION['flash'] = 'User berhasil dihapus.';
        redirect('user/index');
    }
}

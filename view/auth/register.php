<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Register - UMKM Inventory</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/auth.css">
</head>

<body class="auth-page">

    <div class="container">

        <div class="row justify-content-center align-items-center min-vh-100 m-5">

            <div class="col-lg-11">

                <div class="card auth-card">

                    <div class="row g-0">

                        <div class="col-lg-7 auth-left">

                            <div class="auth-logo mb-4">
                                <i class="bi bi-person-plus"></i>
                            </div>

                            <h2 class="auth-title">
                                Buat Akun Baru
                            </h2>

                            <p class="auth-subtitle">
                                Lengkapi data berikut untuk mendaftar.
                            </p>

                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger">
                                    <?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="/?page=auth&action=register">

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama</label>
                                        <input type="text" name="nama" class="form-control"
                                            value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control"
                                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control"
                                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">No HP</label>
                                        <input type="tel" name="no_hp" class="form-control"
                                            value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Role</label>
                                        <select name="role" class="form-select" required>
                                            <option value="">Pilih Role</option>
                                            <option value="admin"  <?= (($_POST['role'] ?? '') === 'admin')  ? 'selected' : '' ?>>Admin</option>
                                            <option value="kasir"  <?= (($_POST['role'] ?? '') === 'kasir')  ? 'selected' : '' ?>>Kasir</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Konfirmasi Password</label>
                                        <input type="password" name="password_confirm" class="form-control" required>
                                    </div>

                                </div>

                                <button class="btn btn-primary btn-auth w-100">
                                    <i class="bi bi-person-check me-2"></i>
                                    Daftar Sekarang
                                </button>

                            </form>

                            <div class="auth-link">
                                Sudah punya akun?
                                <a href="/?page=auth&action=login">Login</a>
                            </div>

                        </div>

                        <div class="col-lg-5 auth-right d-flex flex-column justify-content-center">

                            <h2 class="fw-bold mb-3">
                                UMKM Inventory
                            </h2>

                            <p class="mb-4">
                                Sistem manajemen inventaris modern untuk UMKM.
                            </p>

                            <div class="feature-item">
                                <i class="bi bi-check-circle"></i>
                                <span>Kelola produk</span>
                            </div>

                            <div class="feature-item">
                                <i class="bi bi-check-circle"></i>
                                <span>Kelola transaksi</span>
                            </div>

                            <div class="feature-item">
                                <i class="bi bi-check-circle"></i>
                                <span>Kelola laporan</span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>
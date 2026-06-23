<?php

ob_start(); 
?>

//Formulir
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="/?page=user&action=index" class="text-secondary text-decoration-none mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <h1 class="fw-bold">Tambah</h1>
        <p class="text-secondary mb-0">Isi formulir di bawah untuk membuat akun user baru</p>
    </div>
</div>

<div class="card-section p-4" style="max-width: 600px;">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" style="border-radius:12px;" required placeholder="Contoh: Budi Santoso" value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>">
        </div>
        
        <div class="mb-3">
            <label class="form-label fw-semibold">Username</label>
            <input type="text" name="username" class="form-control" style="border-radius:12px;" required placeholder="Digunakan untuk login" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
        </div>
        
        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" style="border-radius:12px;" required placeholder="Minimal 6 karakter">
        </div>
        
        <div class="mb-3">
            <label class="form-label fw-semibold">Role / Jabatan</label>
            <select name="role" class="form-select" style="border-radius:12px;" required>
                <option value="">-- Pilih Jabatan --</option>
                <option value="admin" <?= (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                <option value="kasir" <?= (isset($_POST['role']) && $_POST['role'] == 'kasir') ? 'selected' : '' ?>>Kasir</option>
            </select>
        </div>
        
        <div class="mb-4">
            <label class="form-label fw-semibold">Status Akun</label>
            <select name="is_active" class="form-select" style="border-radius:12px;">
                 <option value="Y" <?= (isset($_POST['is_active']) && $_POST['is_active'] == 'Y') ? 'selected' : '' ?>>Aktif (Bisa Login)</option>
                 <option value="N" <?= (isset($_POST['is_active']) && $_POST['is_active'] == 'N') ? 'selected' : '' ?>>Non-Aktif (Tidak Bisa Login)</option>
            </select>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4" style="border-radius:12px; font-weight:600;">
                <i class="bi bi-save me-2"></i>Simpan 
            </button>
            <a href="/?page=user&action=index" class="btn btn-outline-secondary px-4" style="border-radius:12px;">Batal</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>
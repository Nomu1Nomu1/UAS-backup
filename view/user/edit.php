<?php

ob_start(); 
?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="/?page=user&action=index" class="text-secondary text-decoration-none mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <h1 class="fw-bold">Edit</h1>
        <p class="text-secondary mb-0">Perbarui data informasi akun user</p>
    </div>
</div>

<div class="card-section p-4" style="max-width: 600px;">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" style="border-radius:12px;" value="<?= htmlspecialchars($user['nama']) ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label fw-semibold">Username</label>
            <input type="text" name="username" class="form-control" style="border-radius:12px;" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>

        <div class="mb-3">
             <label class="form-label fw-semibold">Role</label>
             <select name="role" class="form-select">     
                
        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>
            Admin
        </option>

        <option value="kasir" <?= $user['role'] == 'kasir' ? 'selected' : '' ?>>
            Kasir
        </option>
        </select>
</div>
        
        <div class="mb-3">
            <label class="form-label fw-semibold">Password Baru <span class="text-secondary fw-normal">(Opsional)</span></label>
            <input type="password" name="password" class="form-control" style="border-radius:12px;" placeholder="Kosongkan jika tidak ingin diganti">
        </div>
        
        <div class="mb-4">
            <label class="form-label fw-semibold">Status Akun</label>
            <select name="is_active" class="form-select" style="border-radius:12px;">
                <option value="Y" <?= $user['is_active'] == 'Y' ? 'selected' : '' ?>>Aktif (Bisa Login)</option>
                <option value="N" <?= $user['is_active'] == 'N' ? 'selected' : '' ?>>Non-Aktif (Tidak Bisa Login)</option>
            </select>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning px-4" style="border-radius:12px; font-weight:600;">
                <i class="bi bi-check2-circle me-2"></i>Simpan Perubahan
            </button>
            <a href="/?page=user&action=index" class="btn btn-outline-secondary px-4" style="border-radius:12px;">Batal</a>
        </div>

        
    </form>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>
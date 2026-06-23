<?php ob_start(); ?>

<div class="mb-4">
    <a href="/?page=distributor&action=index" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left"></i> Kembali ke Data Distributor
    </a>
    <h1 class="fw-bold mt-2">Edit Distributor</h1>
    <p class="text-secondary mb-0">Ubah data distributor: <strong><?= htmlspecialchars($distributor['nama_distributor']) ?></strong></p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="card-section" style="max-width: 720px;">
    <form method="POST" action="/?page=distributor&action=edit&id=<?= $distributor['id'] ?>">

        <div class="mb-3">
            <label class="form-label fw-semibold small">
                Nama Distributor <span class="text-danger">*</span>
            </label>
            <input type="text" name="nama_distributor" class="form-control" style="border-radius:12px;"
                    placeholder="Nama Distributor/Supplier"
                    value="<?= htmlspecialchars($_POST['nama_distributor'] ?? $distributor['nama_distributor']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small">
                No. HP <span class="text-danger">*</span>
            </label>
            <input type="tel" name="no_hp" class="form-control" style="border-radius:12px;"
                    placeholder="08xxxxxxxxxx" maxlength="13"
                    value="<?= htmlspecialchars($_POST['no_hp'] ?? $distributor['no_hp']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small">Email</label>
            <input type="email" name="email" class="form-control" style="border-radius:12px;"
                    placeholder="Alamat Email (opsional)"
                    value="<?= htmlspecialchars($_POST['email'] ?? $distributor['email'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small">
                Alamat <span class="text-danger">*</span>
            </label>
            <textarea name="alamat" class="form-control" style="border-radius:12px;" rows="3"
                        placeholder="Alamat lengkap distributor" required><?= htmlspecialchars($_POST['alamat'] ?? $distributor['alamat']) ?></textarea>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold small">Keterangan</label>
            <textarea name="keterangan" class="form-control" style="border-radius:12px;" rows="2"
                        placeholder="Catatan tambahan (opsional)"><?= htmlspecialchars($_POST['keterangan'] ?? $distributor['keterangan'] ?? '') ?></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning px-4" style="border-radius:12px; font-weight:600;">
                <i class="bi bi-pencil-square me-2"></i>Update
            </button>
            <a href="/?page=distributor&action=index" class="btn btn-outline-secondary px-4" style="border-radius:12px;">
                Batal
            </a>
        </div>

    </form>
</div>

<div class="mt-2 ms-1">
    <small class="text-secondary"><span class="text-danger">*</span> Wajib diisi</small>
</div>

<?php
$content = ob_get_clean();
$title = 'Edit Distributor';
$pageTitle = 'Edit Distributor';
require_once __DIR__ . '/../layouts/main.php';
?>
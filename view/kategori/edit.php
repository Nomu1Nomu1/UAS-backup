<?php ob_start(); ?>

<div class="mb-4">
    <a href="/?page=kategori&action=index" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left"></i> Kembali ke Data Kategori
    </a>
    <h1 class="fw-bold mt-2">Edit Kategori</h1>
    <p class="text-secondary mb-0">Ubah data kategori: <strong><?= htmlspecialchars($kategori['nama_kategori']) ?></strong></p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="card-section" style="max-width: 720px;">
    <form method="POST" action="/?page=kategori&action=edit&id=<?= $kategori['id'] ?>">

        <div class="mb-3">
            <label class="form-label fw-semibold small">
                Nama Kategori <span class="text-danger">*</span>
            </label>
            <input type="text" name="nama_kategori" class="form-control" style="border-radius:12px;"
                    placeholder="Contoh: Sembako, Minuman, Alat Tulis"
                    value="<?= htmlspecialchars($_POST['nama_kategori'] ?? $kategori['nama_kategori']) ?>" required>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold small">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" style="border-radius:12px;" rows="3"
                        placeholder="Catatan/penjelasan kategori (opsional)"><?= htmlspecialchars($_POST['deskripsi'] ?? $kategori['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning px-4" style="border-radius:12px; font-weight:600;">
                <i class="bi bi-pencil-square me-2"></i>Update
            </button>
            <a href="/?page=kategori&action=index" class="btn btn-outline-secondary px-4" style="border-radius:12px;">
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
$title = 'Edit Kategori';
$pageTitle = 'Edit Kategori';
require_once __DIR__ . '/../layouts/main.php';
?>

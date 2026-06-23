<?php ob_start(); ?>

<div class="mb-4">
    <a href="/?page=product&action=index"
       class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left"></i>
        Kembali ke Data Produk
    </a>

    <h1 class="fw-bold mt-2">Tambah Produk</h1>
    <p class="text-secondary mb-0">
        Isi data produk inventaris baru
    </p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4">
        <i class="bi bi-exclamation-circle me-2"></i>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="card-section">

    <form method="POST"
          action="/?page=product&action=create"
          enctype="multipart/form-data">

        <div class="row g-3">

            <!-- <div class="col-md-4">
                <label class="form-label fw-semibold small">
                    Kode Barang 
                </label>
                <input type="text" name="kode_barang" class="form-control" style="border-radius:12px;"
                       value="Otomatis" readonly>
            </div> -->

            <div class="col-md-8">
                <label class="form-label fw-semibold small">
                    Nama Barang <span class="text-danger">*</span>
                </label>
                <input type="text" name="nama_barang" class="form-control" style="border-radius:12px;"
                       value="<?= htmlspecialchars($_POST['nama_barang'] ?? '') ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small">
                    Kategori <span class="text-danger">*</span>
                </label>
                <select name="kategori_id" class="form-select" style="border-radius:12px;" required>
                    <option value="" disabled selected>Pilih Kategori</option>
                    <?php foreach ($kategoris as $k): ?>
                        <option value="<?= $k['id'] ?>">
                            <?= htmlspecialchars($k['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small">
                    Distributor <span class="text-danger">*</span>
                </label>
                <select name="distributor_id" class="form-select" style="border-radius:12px;">
                    <option value="" disabled selected>Pilih Distributor</option>
                    <option value="0">Tanpa Distributor</option>
                    <?php foreach ($distribs as $d): ?>
                        <option value="<?= $d['id'] ?>">
                            <?= htmlspecialchars($d['nama_distributor']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold small">Stok Awal</label>
                <input type="number" name="stock" class="form-control" style="border-radius:12px;"
                       value="<?= htmlspecialchars($_POST['stock'] ?? 0) ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold small">Stok Minimum</label>
                <input type="number" name="stock_min" class="form-control" style="border-radius:12px;"
                       value="<?= htmlspecialchars($_POST['stock_min'] ?? 0) ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold small">Harga Beli</label>
                <input type="number" name="harga_beli" class="form-control" style="border-radius:12px;"
                       value="<?= htmlspecialchars($_POST['harga_beli'] ?? 0) ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold small">Harga Jual</label>
                <input type="number" name="harga_jual" class="form-control" style="border-radius:12px;"
                       value="<?= htmlspecialchars($_POST['harga_jual'] ?? 0) ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold small">
                    Satuan <span class="text-danger">*</span>
                </label>
                <input type="text" name="satuan" class="form-control" style="border-radius:12px;"
                       placeholder="pcs, box, kg, liter"
                       value="<?= htmlspecialchars($_POST['satuan'] ?? '') ?>" required>
            </div>

            <div class="col-md-8">
                <label class="form-label fw-semibold small">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"
                          style="border-radius:12px;"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
            </div>

            <!-- ===== FOTO PRODUK ===== -->
            <div class="col-12">
                <label class="form-label fw-semibold small">
                    <i class="bi bi-image me-1"></i> Foto Produk
                    <span class="text-secondary fw-normal">(JPG, PNG, WEBP — maks 2MB)</span>
                </label>

                <div class="d-flex align-items-start gap-3 flex-wrap">

                    <!-- Preview box -->
                    <div id="fotoPreviewWrapper"
                         style="width:120px; height:120px; border-radius:12px; border:2px dashed #cbd5e1;
                                background:#f8fafc; display:flex; align-items:center; justify-content:center;
                                overflow:hidden; flex-shrink:0;">
                        <img id="fotoPreviewImg" src="" alt="Preview"
                             style="width:100%; height:100%; object-fit:cover; display:none; border-radius:10px;">
                        <i id="fotoPreviewIcon" class="bi bi-image text-secondary" style="font-size:2.5rem;"></i>
                    </div>

                    <div class="flex-grow-1">
                        <input type="file" name="foto" id="fotoInput"
                               class="form-control" style="border-radius:12px;"
                               accept="image/jpeg,image/png,image/gif,image/webp"
                               onchange="previewFoto(this)">
                        <small class="text-secondary mt-1 d-block">
                            Upload foto produk agar tampil di halaman kasir
                        </small>
                    </div>

                </div>
            </div>
            <!-- ===== END FOTO ===== -->

        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4" style="border-radius:12px; font-weight:600;">
                <i class="bi bi-check-lg me-2"></i>Simpan
            </button>
            <a href="/?page=product&action=index"
               class="btn btn-outline-secondary px-4" style="border-radius:12px;">
                Batal
            </a>
        </div>

    </form>

</div>

<div class="mt-2 ms-1">
    <small class="text-secondary">
        <span class="text-danger">*</span> Wajib diisi
    </small>
</div>

<script>
function previewFoto(input) {
    const img  = document.getElementById('fotoPreviewImg');
    const icon = document.getElementById('fotoPreviewIcon');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.style.display = 'block';
            icon.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php
$content   = ob_get_clean();
$title     = 'Tambah Produk';
$pageTitle = 'Tambah Produk';
require_once __DIR__ . '/../layouts/main.php';
?>

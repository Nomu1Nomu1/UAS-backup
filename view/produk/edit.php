<?php ob_start(); ?>

<div class="mb-4">
    <a href="/?page=product&action=index"
       class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left"></i>
        Kembali ke Data Produk
    </a>

    <h1 class="fw-bold mt-2">Edit Produk</h1>
    <p class="text-secondary mb-0">Perbarui data produk inventaris</p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4">
        <i class="bi bi-exclamation-circle me-2"></i>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="card-section">

    <form method="POST" enctype="multipart/form-data">

        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label fw-semibold small">
                    Kode Barang <span class="text-danger">*</span>
                </label>
                <input type="text" name="kode_barang" class="form-control" style="border-radius:12px;"
                       value="<?= htmlspecialchars($_POST['kode_barang'] ?? $product['kode_barang']) ?>" required>
            </div>

            <div class="col-md-8">
                <label class="form-label fw-semibold small">
                    Nama Barang <span class="text-danger">*</span>
                </label>
                <input type="text" name="nama_barang" class="form-control" style="border-radius:12px;"
                       value="<?= htmlspecialchars($_POST['nama_barang'] ?? $product['nama_barang']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small">
                    Kategori <span class="text-danger">*</span>
                </label>
                <select name="kategori_id" class="form-select" style="border-radius:12px;" required>
                    <?php foreach ($kategoris as $k): ?>
                        <option value="<?= $k['id'] ?>"
                            <?= (($product['kategori_id'] ?? 0) == $k['id']) ? 'selected' : '' ?>>
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
                    <?php foreach ($distribs as $d): ?>
                        <option value="">Tanpa Distributor</option>
                        <option value="<?= $d['id'] ?>"
                            <?= (($product['distributor_id'] ?? 0) == $d['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['nama_distributor']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold small">Stok</label>
                <input type="number" name="stock" class="form-control" style="border-radius:12px;"
                       value="<?= htmlspecialchars($_POST['stock'] ?? $product['stock']) ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold small">Stok Minimum</label>
                <input type="number" name="stock_min" class="form-control" style="border-radius:12px;"
                       value="<?= htmlspecialchars($_POST['stock_min'] ?? $product['stock_min']) ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold small">Harga Beli</label>
                <input type="number" name="harga_beli" class="form-control" style="border-radius:12px;"
                       value="<?= htmlspecialchars($_POST['harga_beli'] ?? $product['harga_beli']) ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold small">Harga Jual</label>
                <input type="number" name="harga_jual" class="form-control" style="border-radius:12px;"
                       value="<?= htmlspecialchars($_POST['harga_jual'] ?? $product['harga_jual']) ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold small">
                    Satuan <span class="text-danger">*</span>
                </label>
                <input type="text" name="satuan" class="form-control" style="border-radius:12px;"
                       value="<?= htmlspecialchars($_POST['satuan'] ?? $product['satuan']) ?>" required>
            </div>

            <div class="col-md-8">
                <label class="form-label fw-semibold small">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="form-control"
                          style="border-radius:12px;"><?= htmlspecialchars($_POST['deskripsi'] ?? $product['deskripsi']) ?></textarea>
            </div>

            <!-- ===== FOTO PRODUK ===== -->
            <div class="col-12">
                <label class="form-label fw-semibold small">
                    <i class="bi bi-image me-1"></i> Foto Produk
                    <span class="text-secondary fw-normal">(JPG, PNG, WEBP — maks 2MB)</span>
                </label>

                <div class="d-flex align-items-start gap-3 flex-wrap">

                    <!-- Preview / foto saat ini -->
                    <div style="width:120px; height:120px; border-radius:12px; border:2px dashed #cbd5e1;
                                background:#f8fafc; overflow:hidden; flex-shrink:0; position:relative;">
                        <?php
                            $fotoAda = !empty($product['foto']);
                        ?>
                        <img id="fotoPreviewImg"
                             src="<?= $fotoAda ? '/uploads/produk/' . htmlspecialchars($product['foto']) : '' ?>"
                             alt="Foto Produk"
                             style="width:100%; height:100%; object-fit:cover;
                                    display:<?= $fotoAda ? 'block' : 'none' ?>; border-radius:10px;">
                        <i id="fotoPreviewIcon" class="bi bi-image text-secondary"
                           style="font-size:2.5rem; position:absolute; top:50%; left:50%;
                                  transform:translate(-50%,-50%);
                                  display:<?= $fotoAda ? 'none' : 'block' ?>;"></i>
                    </div>

                    <div class="flex-grow-1">
                        <input type="file" name="foto" id="fotoInput"
                               class="form-control mb-2" style="border-radius:12px;"
                               accept="image/jpeg,image/png,image/gif,image/webp"
                               onchange="previewFoto(this)">

                        <?php if ($fotoAda): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="hapus_foto" value="1" id="hapusFotoChk"
                                       onchange="toggleHapusFoto(this)">
                                <label class="form-check-label text-danger small" for="hapusFotoChk">
                                    <i class="bi bi-trash me-1"></i>Hapus foto saat ini
                                </label>
                            </div>
                        <?php endif; ?>

                        <small class="text-secondary mt-1 d-block">
                            Upload foto baru untuk mengganti foto yang ada
                        </small>
                    </div>

                </div>
            </div>
            <!-- ===== END FOTO ===== -->

        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-warning px-4" style="border-radius:12px; font-weight:600;">
                <i class="bi bi-pencil-square me-2"></i>Update Produk
            </button>
            <a href="/?page=product&action=index"
               class="btn btn-outline-secondary px-4" style="border-radius:12px;">
                Batal
            </a>
        </div>

    </form>

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

function toggleHapusFoto(chk) {
    const img  = document.getElementById('fotoPreviewImg');
    const icon = document.getElementById('fotoPreviewIcon');
    if (chk.checked) {
        img.style.opacity  = '0.3';
        img.style.filter   = 'grayscale(100%)';
    } else {
        img.style.opacity  = '1';
        img.style.filter   = 'none';
    }
}
</script>

<?php
$content   = ob_get_clean();
$title     = 'Edit Produk';
$pageTitle = 'Edit Produk';
require_once __DIR__ . '/../layouts/main.php';
?>

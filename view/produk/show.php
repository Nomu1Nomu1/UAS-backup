<?php ob_start(); ?>

<div class="mb-4">
    <a href="/?page=product&action=index"
       class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left"></i>
        Kembali ke Data Produk
    </a>

    <h1 class="fw-bold mt-2">Detail Produk</h1>
    <p class="text-secondary mb-0">Informasi lengkap produk inventaris</p>
</div>

<div class="card-section">

    <div class="row g-4">

        <!-- FOTO PRODUK -->
        <div class="col-12 col-md-3 text-center">
            <?php
                $fotoSrc = !empty($product['foto'])
                    ? '/uploads/produk/' . htmlspecialchars($product['foto'])
                    : null;
            ?>
            <?php if ($fotoSrc): ?>
                <img src="<?= $fotoSrc ?>" alt="Foto <?= htmlspecialchars($product['nama_barang']) ?>"
                     class="img-fluid rounded-4 shadow-sm"
                     style="max-height:220px; width:100%; object-fit:cover;">
            <?php else: ?>
                <div class="rounded-4 bg-light d-flex align-items-center justify-content-center"
                     style="height:180px; border:2px dashed #cbd5e1;">
                    <div class="text-center text-secondary">
                        <i class="bi bi-image" style="font-size:3rem;"></i>
                        <p class="mt-2 small mb-0">Belum ada foto</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-12 col-md-9">
            <div class="row g-4">

                <div class="col-md-5">
                    <label class="form-label text-secondary small">Kode Barang</label>
                    <div class="fw-semibold fs-6"><?= htmlspecialchars($product['kode_barang']) ?></div>
                </div>

                <div class="col-md-7">
                    <label class="form-label text-secondary small">Nama Barang</label>
                    <div class="fw-semibold fs-5"><?= htmlspecialchars($product['nama_barang']) ?></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-secondary small">Kategori</label>
                    <div>
                        <span class="badge bg-primary"><?= htmlspecialchars($product['nama_kategori']) ?></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-secondary small">Distributor</label>
                    <div class="fw-semibold"><?= htmlspecialchars($product['nama_distributor']) ?></div>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-secondary small">Stok Saat Ini</label>
                    <div>
                        <?php if ($product['stock'] <= $product['stock_min']): ?>
                            <span class="badge bg-danger fs-6">
                                <?= $product['stock'] ?> <?= htmlspecialchars($product['satuan']) ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-success fs-6">
                                <?= $product['stock'] ?> <?= htmlspecialchars($product['satuan']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-secondary small">Stok Minimum</label>
                    <div class="fw-semibold">
                        <?= $product['stock_min'] ?> <?= htmlspecialchars($product['satuan']) ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-secondary small">Harga Beli</label>
                    <div class="fw-semibold text-danger">
                        Rp <?= number_format($product['harga_beli'], 0, ',', '.') ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-secondary small">Harga Jual</label>
                    <div class="fw-semibold text-success">
                        Rp <?= number_format($product['harga_jual'], 0, ',', '.') ?>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-12">
            <label class="form-label text-secondary small">Deskripsi</label>
            <div class="p-3 rounded-3 bg-light">
                <?= !empty($product['deskripsi'])
                    ? nl2br(htmlspecialchars($product['deskripsi']))
                    : '<span class="text-muted fst-italic">Tidak ada deskripsi</span>' ?>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label text-secondary small">Dibuat Pada</label>
            <div><?= date('d F Y H:i', strtotime($product['createdAt'])) ?></div>
        </div>

        <div class="col-md-6">
            <label class="form-label text-secondary small">Terakhir Diubah</label>
            <div><?= date('d F Y H:i', strtotime($product['updatedAt'])) ?></div>
        </div>

    </div>

    <hr class="my-4">

    <div class="d-flex gap-2">
        <a href="/?page=product&action=edit&id=<?= $product['id'] ?>"
           class="btn btn-warning px-4" style="border-radius:12px;">
            <i class="bi bi-pencil-square me-2"></i>Edit Produk
        </a>
        <a href="/?page=product&action=index"
           class="btn btn-outline-secondary px-4" style="border-radius:12px;">
            Kembali
        </a>
    </div>

</div>

<?php
$content   = ob_get_clean();
$title     = 'Detail Produk';
$pageTitle = 'Detail Produk';
require_once __DIR__ . '/../layouts/main.php';
?>

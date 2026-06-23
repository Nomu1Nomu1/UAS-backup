<?php ob_start(); ?>

<?php
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>

<div class="mb-4">
    <div>
        <h1 class="fw-bold">Data Produk</h1>
        <p class="text-secondary">Kelola semua produk inventaris</p>
    </div>

    <a href="/?page=product&action=create"
       class="btn btn-primary px-4"
       style="border-radius:14px; font-weight:600;">
        <i class="bi bi-plus-lg me-2"></i>Tambah Produk
    </a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<!-- Filter -->
<div class="card-section mb-4">
    <form method="GET" action="/" class="row g-3 align-items-end">
        <input type="hidden" name="page" value="product">
        <input type="hidden" name="action" value="index">

        <div class="col-md-5">
            <label class="form-label fw-semibold small">Cari Produk</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" name="search" class="form-control border-start-0 ps-0"
                       placeholder="Nama atau kode barang..."
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                       style="border-radius:0 12px 12px 0;">
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold small">Kategori</label>
            <select name="kategori_id" class="form-select" style="border-radius:12px;">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategoris as $k): ?>
                    <option value="<?= $k['id'] ?>"
                        <?= ($_GET['kategori_id'] ?? '') == $k['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary" style="border-radius:12px; font-weight:600;">
                <i class="bi bi-funnel me-1"></i>Cari
            </button>
            <a href="/?page=product&action=index"
               class="btn btn-outline-secondary" style="border-radius:12px;">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </form>
</div>

<!-- Tabel -->
<div class="card-section">

    <?php if (empty($products)): ?>
        <div class="text-center py-5 text-secondary">
            <i class="bi bi-box-seam display-4 d-block mb-3 opacity-50"></i>
            <p class="mb-0">Belum ada data produk</p>
        </div>

    <?php else: ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                <thead>
                    <tr style="background:#f8fafc; color:#64748b; font-size:13px; font-weight:600;">
                        <th class="border-0 rounded-start ps-3 py-3">#</th>
                        <th class="border-0">Foto</th>
                        <th class="border-0">Kode</th>
                        <th class="border-0">Nama Produk</th>
                        <th class="border-0">Kategori</th>
                        <th class="border-0">Stok</th>
                        <th class="border-0">Harga Jual</th>
                        <th class="border-0">Distributor</th>
                        <th class="border-0 rounded-end pe-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($products as $i => $p): ?>
                    <?php
                    $fotoSrc = !empty($p['foto'])
                        ? '/uploads/produk/' . htmlspecialchars($p['foto'])
                        : null;
                    ?>
                    <tr>
                        <td class="ps-3 text-secondary"><?= $i + 1 ?></td>

                        <td>
                            <?php if ($fotoSrc): ?>
                                <img src="<?= $fotoSrc ?>"
                                     alt="<?= htmlspecialchars($p['nama_barang']) ?>"
                                     style="width:48px; height:48px; object-fit:cover;
                                            border-radius:8px; border:1px solid #e2e8f0;">
                            <?php else: ?>
                                <div style="width:48px; height:48px; background:#f1f5f9;
                                            border-radius:8px; border:1px solid #e2e8f0;
                                            display:flex; align-items:center; justify-content:center;">
                                    <i class="bi bi-image text-secondary"></i>
                                </div>
                            <?php endif; ?>
                        </td>

                        <td><span class="fw-semibold"><?= htmlspecialchars($p['kode_barang']) ?></span></td>

                        <td><?= htmlspecialchars($p['nama_barang']) ?></td>

                        <td><?= htmlspecialchars($p['nama_kategori']) ?></td>

                        <td>
                            <?php if ($p['stock'] <= $p['stock_min']): ?>
                                <span class="badge bg-danger">
                                    <?= $p['stock'] ?> <?= htmlspecialchars($p['satuan']) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success">
                                    <?= $p['stock'] ?> <?= htmlspecialchars($p['satuan']) ?>
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="fw-semibold text-success">
                            Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?>
                        </td>

                        <td><?= htmlspecialchars($p['nama_distributor'] ?? '-' ) ?></td>

                        <td class="text-center pe-3">
                            <a href="/?page=product&action=show&id=<?= $p['id'] ?>"
                               class="btn btn-sm btn-outline-info me-1" style="border-radius:8px;" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="/?page=product&action=edit&id=<?= $p['id'] ?>"
                               class="btn btn-sm btn-outline-warning me-1" style="border-radius:8px;" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="/?page=product&action=delete&id=<?= $p['id'] ?>"
                               class="btn btn-sm btn-outline-danger" style="border-radius:8px;" title="Hapus"
                               onclick="return confirm('Yakin hapus produk ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>

        <div class="mt-3 text-secondary small ps-2">
            Total: <strong><?= count($products) ?></strong> produk
        </div>

    <?php endif; ?>

</div>

<?php
$content   = ob_get_clean();
$title     = 'Data Produk';
$pageTitle = 'Data Produk';
require_once __DIR__ . '/../layouts/main.php';
?>

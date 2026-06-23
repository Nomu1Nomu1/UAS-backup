<?php
ob_start();

$db = getDB();

// Semua produk dengan info stok
$produk_list = $db->query(
    "SELECT p.kode_barang, p.nama_barang, p.stock, p.stock_min,
            p.satuan, p.harga_jual,
            k.nama_kategori,
            d.nama_distributor
     FROM product p
     JOIN kategori_product k ON p.kategori_id    = k.id
     JOIN distributors     d ON p.distributor_id = d.id
     ORDER BY p.stock ASC"
)->fetch_all(MYSQLI_ASSOC);

$total_produk = count($produk_list);
$stok_aman = array_filter($produk_list, fn($r) => $r['stock'] > $r['stock_min']);
$stok_menipis = array_filter($produk_list, fn($r) => $r['stock'] > 0 && $r['stock'] <= $r['stock_min']);
$stok_habis = array_filter($produk_list, fn($r) => $r['stock'] == 0);
?>

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="?page=laporan&action=index" class="text-muted text-decoration-none small">
                <i class="bi bi-arrow-left"></i> Kembali ke Laporan
            </a>
            <h2 class="fw-bold mb-1 mt-1">Laporan Stok</h2>
            <p class="text-muted mb-0">Kondisi stok barang saat ini</p>
        </div>
        <button onclick="window.print()" class="btn btn-success">
            <i class="bi bi-printer"></i> Cetak
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted mb-1 small">Total Produk</div>
                <h3 class="fw-bold text-primary mb-0"><?= $total_produk ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted mb-1 small">Stok Aman</div>
                <h3 class="fw-bold text-success mb-0"><?= count($stok_aman) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted mb-1 small">Stok Menipis</div>
                <h3 class="fw-bold text-warning mb-0"><?= count($stok_menipis) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted mb-1 small">Stok Habis</div>
                <h3 class="fw-bold text-danger mb-0"><?= count($stok_habis) ?></h3>
            </div>
        </div>
    </div>

    <!-- Tabel Stok -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">Daftar Stok Produk</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Distributor</th>
                            <th>Harga Jual</th>
                            <th>Stok Min</th>
                            <th>Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($produk_list)): ?>
                            <?php foreach ($produk_list as $i => $p): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><code><?= htmlspecialchars($p['kode_barang']) ?></code></td>
                                    <td><?= htmlspecialchars($p['nama_barang']) ?></td>
                                    <td><?= htmlspecialchars($p['nama_kategori']) ?></td>
                                    <td><?= htmlspecialchars($p['nama_distributor']) ?></td>
                                    <td>Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?></td>
                                    <td><?= $p['stock_min'] ?>         <?= $p['satuan'] ?></td>
                                    <td class="fw-bold"><?= $p['stock'] ?>         <?= $p['satuan'] ?></td>
                                    <td>
                                        <?php if ($p['stock'] == 0): ?>
                                            <span class="badge bg-danger">Habis</span>
                                        <?php elseif ($p['stock'] <= $p['stock_min']): ?>
                                            <span class="badge bg-warning text-dark">Menipis</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Aman</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Tidak ada data produk
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
$title = 'Laporan Stok';
$pageTitle = 'Laporan Stok';
require __DIR__ . '/../layouts/main.php';
?>
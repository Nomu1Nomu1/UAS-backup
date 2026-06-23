<?php
ob_start();

$db = getDB();
$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

// Penjualan harian
$stmt = $db->prepare(
    "SELECT DATE(tanggal_transaksi) AS tgl,
            COUNT(*) AS jumlah_trx,
            SUM(total_harga) AS total_penjualan
     FROM transaksi
     WHERE status = 'Selesai'
       AND DATE(tanggal_transaksi) BETWEEN ? AND ?
     GROUP BY DATE(tanggal_transaksi)
     ORDER BY tgl ASC"
);
$stmt->bind_param('ss', $dari, $sampai);
$stmt->execute();
$penjualanHarian = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Produk terlaris
$stmt2 = $db->prepare(
    "SELECT p.kode_barang, p.nama_barang, k.nama_kategori,
            SUM(dt.qty) AS total_terjual,
            SUM(dt.subtotal) AS total_pendapatan
     FROM detail_transaksi dt
     JOIN transaksi        t  ON dt.id_trx     = t.id
     JOIN product          p  ON dt.produk_id  = p.id
     JOIN kategori_product k  ON p.kategori_id = k.id
     WHERE t.status = 'Selesai'
       AND DATE(t.tanggal_transaksi) BETWEEN ? AND ?
     GROUP BY dt.produk_id
     ORDER BY total_terjual DESC
     LIMIT 10"
);
$stmt2->bind_param('ss', $dari, $sampai);
$stmt2->execute();
$produkTerlaris = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

// Summary
$stmt3 = $db->prepare(
    "SELECT COUNT(*) AS total_trx, COALESCE(SUM(total_harga),0) AS grand_total
     FROM transaksi
     WHERE status = 'Selesai'
       AND DATE(tanggal_transaksi) BETWEEN ? AND ?"
);
$stmt3->bind_param('ss', $dari, $sampai);
$stmt3->execute();
$summary = $stmt3->get_result()->fetch_assoc();

// Stok habis
$stokHabis = $db->query(
    "SELECT p.kode_barang, p.nama_barang, p.stock, k.nama_kategori, p.harga_jual
     FROM product p
     JOIN kategori_product k ON p.kategori_id = k.id
     WHERE p.stock <= p.stock_min
     ORDER BY p.stock ASC"
)->fetch_all(MYSQLI_ASSOC);
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="?page=laporan&action=index" class="text-muted text-decoration-none small">
                <i class="bi bi-arrow-left"></i> Kembali ke Laporan
            </a>
            <h2 class="fw-bold mb-1 mt-1">Laporan Penjualan</h2>
            <p class="text-muted mb-0">Ringkasan penjualan dan produk terlaris</p>
        </div>
        <button onclick="window.print()" class="btn btn-success">
            <i class="bi bi-printer"></i> Cetak
        </button>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="page" value="laporan">
                <input type="hidden" name="action" value="penjualan">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Dari Tanggal</label>
                    <input type="date" name="dari" class="form-control" value="<?= htmlspecialchars($dari) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Sampai Tanggal</label>
                    <input type="date" name="sampai" class="form-control" value="<?= htmlspecialchars($sampai) ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted mb-1">Total Transaksi</div>
                    <h2 class="fw-bold text-primary mb-0"><?= number_format($summary['total_trx'] ?? 0) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted mb-1">Total Penjualan</div>
                    <h2 class="fw-bold text-success mb-0">
                        Rp <?= number_format($summary['grand_total'] ?? 0, 0, ',', '.') ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Penjualan Harian -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">Penjualan Harian</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Tanggal</th>
                            <th>Jumlah Transaksi</th>
                            <th>Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($penjualanHarian)): ?>
                            <?php foreach ($penjualanHarian as $i => $row): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= date('d M Y', strtotime($row['tgl'])) ?></td>
                                    <td><?= number_format($row['jumlah_trx']) ?></td>
                                    <td>Rp <?= number_format($row['total_penjualan'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Tidak ada data penjualan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Produk Terlaris -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">10 Produk Terlaris</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Total Terjual</th>
                            <th>Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($produkTerlaris)): ?>
                            <?php foreach ($produkTerlaris as $i => $produk): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($produk['kode_barang']) ?></td>
                                    <td><?= htmlspecialchars($produk['nama_barang']) ?></td>
                                    <td><?= htmlspecialchars($produk['nama_kategori']) ?></td>
                                    <td><?= number_format($produk['total_terjual']) ?></td>
                                    <td>Rp <?= number_format($produk['total_pendapatan'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data penjualan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Stok Menipis/Habis -->
    

</div>

<?php
$content = ob_get_clean();
$title = 'Laporan Penjualan';
$pageTitle = 'Laporan Penjualan';
require __DIR__ . '/../layouts/main.php';
?>
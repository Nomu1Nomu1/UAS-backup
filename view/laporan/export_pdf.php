<?php
function fmt(float $n): string
{
    return 'Rp ' . number_format($n, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Export Laporan - <?= $label ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/export-pdf.css">
</head>

<body>

    <div class="no-print" style="margin-bottom:20px">
        <button onclick="window.print()"
            style="padding:8px 20px;background:#2563eb;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px">
            🖨️ Cetak / Simpan PDF
        </button>
        <a href="index.php" style="margin-left:10px;color:#2563eb">← Kembali</a>
    </div>

    <h1>Laporan UMKM Inventory</h1>
    <p class="meta">Periode: <?= $label ?> &nbsp;|&nbsp; Dicetak: <?= date('d M Y H:i') ?></p>

    <!-- Stats -->
    <div class="stats">
        <div class="stat-box">
            <div class="label">Total Penjualan</div>
            <div class="value"><?= fmt((float) $summary['total_penjualan']) ?></div>
        </div>
        <div class="stat-box">
            <div class="label">Jumlah Transaksi</div>
            <div class="value"><?= number_format((int) $summary['total_transaksi']) ?></div>
        </div>
        <div class="stat-box">
            <div class="label">Rata-rata per Transaksi</div>
            <div class="value"><?= fmt((float) $summary['rata_transaksi']) ?></div>
        </div>
    </div>

    <!-- Produk Terlaris -->
    <h2>Produk Terlaris</h2>
    <?php if (!empty($produkTerlaris)): ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Total Terjual (unit)</th>
                    <th>Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produkTerlaris as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($p['nama_barang']) ?></td>
                        <td><?= number_format($p['total_terjual']) ?></td>
                        <td><?= fmt((float) $p['total_pendapatan']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color:#888">Tidak ada data penjualan pada periode ini.</p>
    <?php endif; ?>

    <!-- Stok Menipis -->
    <h2>Produk Stok Menipis / Habis</h2>
    <?php if (!empty($stokMenipis)): ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Stok Min</th>
                    <th>Stok Saat Ini</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stokMenipis as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($p['kode_barang']) ?></td>
                        <td><?= htmlspecialchars($p['nama_barang']) ?></td>
                        <td><?= htmlspecialchars($p['nama_kategori']) ?></td>
                        <td><?= $p['stock_min'] ?></td>
                        <td><?= $p['stock'] ?></td>
                        <td>
                            <?php if ($p['stock'] == 0): ?>
                                <span class="badge-danger">Habis</span>
                            <?php else: ?>
                                <span class="badge-warning">Menipis</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color:#16a34a">✓ Semua produk memiliki stok yang cukup.</p>
    <?php endif; ?>

    <p class="footer-txt">— Laporan ini digenerate otomatis oleh sistem UMKM Inventory —</p>

</body>

</html>
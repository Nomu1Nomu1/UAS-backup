<?php ob_start(); ?>

<div class="mb-4">
    <h1 class="fw-bold">Dashboard</h1>
    <p class="text-secondary">Ringkasan sistem inventaris UMKM</p>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">

    <!-- Total Produk -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Total Produk</p>
                    <h3 class="fw-bold mb-1"><?= number_format($totalProduk) ?></h3>
                    <span class="text-success small">
                        <i class="bi bi-arrow-up"></i>
                        <?= $persenProdukAman ?>% produk aman
                    </span>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                    <i class="bi bi-box-seam fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Nilai Stok -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Nilai Stok</p>
                    <?php
                    // Hitung nilai stok dari seluruh produk
                    $allProducts = (new Product())->getAll();
                    $nilaiStok = array_sum(array_map(fn($p) => $p['stock'] * $p['harga_beli'], $allProducts));
                    $nilaiStokFormatted = $nilaiStok >= 1000000
                        ? 'Rp ' . number_format($nilaiStok / 1000000, 1) . 'M'
                        : 'Rp ' . number_format($nilaiStok, 0, ',', '.');
                    ?>
                    <h3 class="fw-bold mb-1"><?= $nilaiStokFormatted ?></h3>
                    <span class="text-success small"></i> Total nilai inventaris</span>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success rounded-3 p-3">
                    <i class="bi bi-currency-dollar fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Hari Ini -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Transaksi Hari Ini</p>
                    <h3 class="fw-bold mb-1"><?= $totalTRXHariIni ?></h3>
                    <span class="text-success small">Transaksi selesai</span>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                    <i class="bi bi-graph-up-arrow fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stok Menipis -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Stok Menipis</p>
                    <h3 class="fw-bold mb-1"><?= $stockMenipis ?></h3>
                    <?php
                    $persenStokMenipis = 0;

                    if ($totalProduk > 0) {
                        $persenStokMenipis = round(
                            ($stockMenipis / $totalProduk) * 100
                        );
                    }
                    ?>

                    <?php if ($stockMenipis > 0): ?>
                        <span class="text-danger small">
                            <i class="bi bi-arrow-down"></i>
                            <?= $persenStokMenipis ?>% produk perlu restock
                        </span>
                    <?php else: ?>
                        <span class="text-success small">
                            <i class="bi bi-check-circle"></i>
                            0% produk bermasalah
                        </span>
                    <?php endif; ?>
                </div>
                <div class="stat-icon bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                    <i class="bi bi-exclamation-circle fs-4"></i>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="row g-3">

    <!-- Transaksi Terbaru -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Transaksi Terbaru</h5>

                <?php if (empty($transaksiTerakhir)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
                        Belum ada transaksi hari ini
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach (array_slice($transaksiTerakhir, 0, 5) as $trx): ?>
                            <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-light">
                                <div>
                                    <p class="fw-semibold mb-0"><?= htmlspecialchars($trx['kasir'] ?? '-') ?></p>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($trx['no_trx']) ?>
                                        &bull;
                                        <?= date('H:i', strtotime($trx['tanggal_transaksi'])) ?>
                                    </small>
                                </div>
                                <span class="fw-bold text-success">
                                    Rp <?= number_format($trx['total_harga'], 0, ',', '.') ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($transaksiTerakhir) > 5): ?>
                        <div class="text-center mt-3">
                            <a href="/?page=transaksi&action=index" class="btn btn-sm btn-outline-primary">
                                Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stok Menipis -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3 text-danger">
                    <i class="bi bi-exclamation-circle me-1"></i> Stok Menipis
                </h5>

                <?php if (empty($listStockMenipis)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-check-circle fs-1 d-block mb-2 opacity-25 text-success"></i>
                        Semua stok dalam kondisi aman
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($listStockMenipis as $item): ?>
                            <?php
                            $pct = $item['stock_min'] > 0
                                ? min(100, round(($item['stock'] / $item['stock_min']) * 100))
                                : 0;
                            $barColor = $pct <= 30 ? 'bg-danger' : ($pct <= 60 ? 'bg-warning' : 'bg-success');
                            ?>
                            <div class="p-3 rounded-3 border border-danger border-opacity-25 bg-danger bg-opacity-10">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <p class="fw-semibold mb-0"><?= htmlspecialchars($item['nama_barang']) ?></p>
                                        <small class="text-muted"><?= htmlspecialchars($item['nama_kategori']) ?></small>
                                    </div>
                                    <span
                                        class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 fw-normal">
                                        Stok: <?= $item['stock'] ?>
                                    </span>
                                </div>
                                <div class="progress mt-2" style="height:6px; background:#f8d7da;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width:<?= $pct ?>%"
                                        aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">Minimal: <?= $item['stock_min'] ?>
                                    <?= htmlspecialchars($item['satuan']) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($listStockMenipis) > 4): ?>
                        <div class="text-center mt-3">
                            <a href="/?page=product&action=index" class="btn btn-sm btn-outline-danger">
                                Lihat Semua Stok <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>
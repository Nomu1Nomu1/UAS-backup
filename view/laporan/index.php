<?php
ob_start();

$db = getDB();

$periode = $_GET['periode'] ?? 'bulan_ini';

switch ($periode) {
    case 'hari_ini':
        $tgl_dari = date('Y-m-d');
        $tgl_sampai = date('Y-m-d');
        $label = 'Hari Ini';
        break;
    case 'minggu_ini':
        $tgl_dari = date('Y-m-d', strtotime('monday this week'));
        $tgl_sampai = date('Y-m-d', strtotime('sunday this week'));
        $label = 'Minggu Ini';
        break;
    case 'tahun_ini':
        $tgl_dari = date('Y-01-01');
        $tgl_sampai = date('Y-12-31');
        $label = 'Tahun Ini';
        break;
    default: // bulan_ini
        $tgl_dari = date('Y-m-01');
        $tgl_sampai = date('Y-m-t');
        $label = 'Bulan Ini';
        break;
}

$stmt = $db->prepare(
    "SELECT
        COALESCE(SUM(total_harga), 0)  AS total_penjualan,
        COUNT(*)                        AS total_transaksi,
        COALESCE(AVG(total_harga), 0)  AS rata_transaksi
     FROM transaksi
     WHERE status = 'Selesai'
       AND DATE(tanggal_transaksi) BETWEEN ? AND ?"
);
$stmt->bind_param('ss', $tgl_dari, $tgl_sampai);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();

$bulan6 = [];
for ($i = 5; $i >= 0; $i--) {
    $bulan6[] = date('Y-m', strtotime("-{$i} months"));
}

$grafik_data = [];
foreach ($bulan6 as $bln) {
    $dari_bln = $bln . '-01';
    $sampai_bln = date('Y-m-t', strtotime($dari_bln));

    $st = $db->prepare(
        "SELECT COALESCE(SUM(total_harga),0) AS total
         FROM transaksi
         WHERE status='Selesai'
           AND DATE(tanggal_transaksi) BETWEEN ? AND ?"
    );
    $st->bind_param('ss', $dari_bln, $sampai_bln);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();

    $grafik_data[] = [
        'label' => date('M', strtotime($dari_bln)),
        'total' => (float) $row['total'],
    ];
}

$max_grafik = max(array_column($grafik_data, 'total')) ?: 1;

$stmt2 = $db->prepare(
    "SELECT p.nama_barang,
            SUM(dt.qty)      AS total_terjual,
            SUM(dt.subtotal) AS total_pendapatan
     FROM detail_transaksi dt
     JOIN transaksi        t  ON dt.id_trx    = t.id
     JOIN product          p  ON dt.produk_id = p.id
     WHERE t.status = 'Selesai'
       AND DATE(t.tanggal_transaksi) BETWEEN ? AND ?
     GROUP BY dt.produk_id
     ORDER BY total_terjual DESC
     LIMIT 5"
);
$stmt2->bind_param('ss', $tgl_dari, $tgl_sampai);
$stmt2->execute();
$produk_terlaris = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

function fmt_short(float $n): string
{
    if ($n >= 1_000_000_000)
        return 'Rp ' . number_format($n / 1_000_000_000, 1) . 'B';
    if ($n >= 1_000_000)
        return 'Rp ' . number_format($n / 1_000_000, 1) . 'M';
    if ($n >= 1_000)
        return 'Rp ' . number_format($n / 1_000, 1) . 'K';
    return 'Rp ' . number_format($n, 0, ',', '.');
}

function fmt_rupiah(float $n): string
{
    return 'Rp ' . number_format($n, 0, ',', '.');
}
?>

<div class="container-fluid">

    <div class="report-header mb-4">
        <div>
            <h2 class="fw-bold mb-1">Laporan</h2>
            <p class="text-muted mb-0">Analisis penjualan dan inventaris</p>
        </div>

        <a href="/?page=laporan&action=exportPdf&periode=<?= urlencode($periode) ?>"
            class="btn btn-success d-flex align-items-center gap-2">
            <i class="bi bi-download"></i> Export PDF
        </a>
    </div>

    <div class="card-section mb-4">
        <form method="GET" class="d-flex align-items-center gap-3">
            <input type="hidden" name="page" value="laporan">
            <input type="hidden" name="action" value="index">
            <i class="bi bi-calendar3 text-muted fs-5"></i>
            <select name="periode" class="form-select report-select">
                <option value="bulan_ini" <?= $periode == 'bulan_ini' ? 'selected' : '' ?>>Bulan Ini</option>
                <option value="minggu_ini" <?= $periode == 'minggu_ini' ? 'selected' : '' ?>>Minggu Ini</option>
                <option value="hari_ini" <?= $periode == 'hari_ini' ? 'selected' : '' ?>>Hari Ini</option>
                <option value="tahun_ini" <?= $periode == 'tahun_ini' ? 'selected' : '' ?>>Tahun Ini</option>
            </select>
            <button type="submit" class="btn btn-primary px-4">Terapkan</button>
        </form>
    </div>

    <div class="row g-4 mb-4">

        <!-- Total Penjualan -->
        <div class="col-md-4">
            <div class="stat-card stat-blue">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Total Penjualan</span>
                    <i class="bi bi-graph-up fs-4"></i>
                </div>
                <h2 class="mb-1"><?= fmt_short((float) $summary['total_penjualan']) ?></h2>
                <small class="opacity-75">Periode: <?= $label ?></small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-green">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Transaksi</span>
                    <i class="bi bi-bar-chart fs-4"></i>
                </div>
                <h2 class="mb-1"><?= number_format((int) $summary['total_transaksi']) ?></h2>
                <small class="opacity-75">Periode: <?= $label ?></small>
            </div>
        </div>

        <!-- Rata-rata Transaksi -->
        <div class="col-md-4">
            <div class="stat-card stat-purple">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Rata-rata Transaksi</span>
                    <i class="bi bi-graph-up-arrow fs-4"></i>
                </div>
                <h2 class="mb-1"><?= fmt_short((float) $summary['rata_transaksi']) ?></h2>
                <small class="opacity-75">Per transaksi</small>
            </div>
        </div>

    </div>

    <!-- Grafik + Produk Terlaris -->
    <div class="row g-4 mb-4">

        <!-- Grafik 6 Bulan Terakhir -->
        <div class="col-lg-7">
            <div class="card-section h-100">
                <h5 class="fw-bold mb-4">Grafik Penjualan 6 Bulan Terakhir</h5>

                <?php foreach ($grafik_data as $item): ?>
                    <?php $pct = ($max_grafik > 0) ? ($item['total'] / $max_grafik * 100) : 0; ?>
                    <div class="report-bar">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted fw-medium"><?= $item['label'] ?></span>
                            <span class="text-primary fw-semibold"><?= fmt_short($item['total']) ?></span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: <?= round($pct, 1) ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>

        <!-- Produk Terlaris -->
        <div class="col-lg-5">
            <div class="card-section h-100">
                <h5 class="fw-bold mb-4">Produk Terlaris</h5>

                <?php if (!empty($produk_terlaris)): ?>
                    <?php foreach ($produk_terlaris as $i => $p): ?>
                        <div class="top-product">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rank fw-bold"><?= $i + 1 ?></div>
                                <div>
                                    <div class="fw-semibold"><?= htmlspecialchars($p['nama_barang']) ?></div>
                                    <small class="text-muted">Terjual: <?= number_format($p['total_terjual']) ?> unit</small>
                                </div>
                            </div>
                            <div class="price"><?= fmt_short((float) $p['total_pendapatan']) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Belum ada data penjualan pada periode ini
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>

    <!-- Jenis Laporan -->
    <div class="card-section">
        <h5 class="fw-bold mb-4">Jenis Laporan</h5>

        <div class="row g-3">

            <div class="col-md-4">
                <a href="/?page=laporan&action=penjualan"
                    class="report-type d-block text-decoration-none text-dark">
                    <i class="bi bi-bar-chart-line d-block"></i>
                    <div class="fw-semibold mt-2">Laporan Penjualan</div>
                    <small class="text-muted">Detail transaksi penjualan periode tertentu</small>
                </a>
            </div>

            <div class="col-md-4">
                <a href="/?page=laporan&action=stok" class="report-type d-block text-decoration-none text-dark">
                    <i class="bi bi-graph-up-arrow d-block"></i>
                    <div class="fw-semibold mt-2">Laporan Stok</div>
                    <small class="text-muted">Kondisi stok barang saat ini</small>
                </a>
            </div>

            <div class="col-md-4">
                <a href="/?page=pengadaan&action=index" class="report-type d-block text-decoration-none text-dark">
                    <i class="bi bi-download d-block"></i>
                    <div class="fw-semibold mt-2">Laporan Pengadaan</div>
                    <small class="text-muted">Riwayat pengadaan barang</small>
                </a>
            </div>

        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
$title = 'Laporan';
$pageTitle = 'Laporan';
require __DIR__ . '/../layouts/main.php';
?>
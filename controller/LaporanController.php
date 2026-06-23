<?php
require_once __DIR__ . '/../config/db.php';

class LaporanController
{
    public function index(): void
    {
        if (!is_logged_in())
            redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $db = getDB();

        // --- Periode ---
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
            default:
                $tgl_dari = date('Y-m-01');
                $tgl_sampai = date('Y-m-t');
                $label = 'Bulan Ini';
                break;
        }

        // --- Summary Cards ---
        $stmt = $db->prepare(
            "SELECT COALESCE(SUM(total_harga),0) AS total_penjualan,
                    COUNT(*) AS total_transaksi,
                    COALESCE(AVG(total_harga),0) AS rata_transaksi
             FROM transaksi
             WHERE status='Selesai'
               AND DATE(tanggal_transaksi) BETWEEN ? AND ?"
        );
        $stmt->bind_param('ss', $tgl_dari, $tgl_sampai);
        $stmt->execute();
        $summary = $stmt->get_result()->fetch_assoc();

        // --- Grafik 6 Bulan Terakhir ---
        $grafik_data = [];
        for ($i = 5; $i >= 0; $i--) {
            $bln = date('Y-m', strtotime("-{$i} months"));
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

        // --- Produk Terlaris ---
        $stmt2 = $db->prepare(
            "SELECT p.nama_barang,
                    SUM(dt.qty)      AS total_terjual,
                    SUM(dt.subtotal) AS total_pendapatan
             FROM detail_transaksi dt
             JOIN transaksi t ON dt.id_trx    = t.id
             JOIN product   p ON dt.produk_id = p.id
             WHERE t.status='Selesai'
               AND DATE(t.tanggal_transaksi) BETWEEN ? AND ?
             GROUP BY dt.produk_id
             ORDER BY total_terjual DESC
             LIMIT 5"
        );
        $stmt2->bind_param('ss', $tgl_dari, $tgl_sampai);
        $stmt2->execute();
        $produk_terlaris = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        $pageTitle = 'Laporan';
        require_once __DIR__ . '/../view/laporan/index.php';
    }

    public function penjualan(): void
    {
        if (!is_logged_in())
            redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $db = getDB();
        $dari = $_GET['dari'] ?? date('Y-m-01');
        $sampai = $_GET['sampai'] ?? date('Y-m-d');

        $stmt = $db->prepare(
            "SELECT DATE(tanggal_transaksi) AS tgl,
                    COUNT(*) AS jumlah_trx,
                    SUM(total_harga) AS total_penjualan
             FROM transaksi
             WHERE status='Selesai'
               AND DATE(tanggal_transaksi) BETWEEN ? AND ?
             GROUP BY DATE(tanggal_transaksi)
             ORDER BY tgl ASC"
        );
        $stmt->bind_param('ss', $dari, $sampai);
        $stmt->execute();
        $penjualanHarian = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt2 = $db->prepare(
            "SELECT p.kode_barang, p.nama_barang, k.nama_kategori,
                    SUM(dt.qty) AS total_terjual,
                    SUM(dt.subtotal) AS total_pendapatan
             FROM detail_transaksi dt
             JOIN transaksi        t  ON dt.id_trx     = t.id
             JOIN product          p  ON dt.produk_id  = p.id
             JOIN kategori_product k  ON p.kategori_id = k.id
             WHERE t.status='Selesai'
               AND DATE(t.tanggal_transaksi) BETWEEN ? AND ?
             GROUP BY dt.produk_id
             ORDER BY total_terjual DESC
             LIMIT 10"
        );
        $stmt2->bind_param('ss', $dari, $sampai);
        $stmt2->execute();
        $produkTerlaris = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt3 = $db->prepare(
            "SELECT COUNT(*) AS total_trx, COALESCE(SUM(total_harga),0) AS grand_total
             FROM transaksi
             WHERE status='Selesai'
               AND DATE(tanggal_transaksi) BETWEEN ? AND ?"
        );
        $stmt3->bind_param('ss', $dari, $sampai);
        $stmt3->execute();
        $summary = $stmt3->get_result()->fetch_assoc();

        $stokHabis = $db->query(
            "SELECT p.kode_barang, p.nama_barang, p.stock, k.nama_kategori, p.harga_jual
             FROM product p
             JOIN kategori_product k ON p.kategori_id = k.id
             WHERE p.stock <= p.stock_min
             ORDER BY p.stock ASC"
        )->fetch_all(MYSQLI_ASSOC);

        $pageTitle = 'Laporan Penjualan';
        require_once __DIR__ . '/../view/laporan/penjualan.php';
    }

    public function stok(): void
    {
        if (!is_logged_in())
            redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $db = getDB();

        $produk_list = $db->query(
            "SELECT p.kode_barang, p.nama_barang, p.stock, p.stock_min,
                    p.satuan, p.harga_jual, k.nama_kategori, d.nama_distributor
             FROM product p
             JOIN kategori_product k ON p.kategori_id    = k.id
             JOIN distributors     d ON p.distributor_id = d.id
             ORDER BY p.stock ASC"
        )->fetch_all(MYSQLI_ASSOC);

        $pageTitle = 'Laporan Stok';
        require_once __DIR__ . '/../view/laporan/stok.php';
    }

    public function pengadaan(): void
    {
        if (!is_logged_in())
            redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $db = getDB();
        $dari = $_GET['dari'] ?? date('Y-m-01');
        $sampai = $_GET['sampai'] ?? date('Y-m-d');

        $stmt = $db->prepare(
            "SELECT pg.no_pengadaan, pg.tanggal_pengadaan, pg.total_harga,
                    pg.status, d.nama_distributor, u.nama AS nama_user
             FROM pengadaan pg
             JOIN distributors d ON pg.distributor_id = d.id
             JOIN users        u ON pg.user_id         = u.user_id
             WHERE DATE(pg.tanggal_pengadaan) BETWEEN ? AND ?
             ORDER BY pg.tanggal_pengadaan DESC"
        );
        $stmt->bind_param('ss', $dari, $sampai);
        $stmt->execute();
        $listPengadaan = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $pageTitle = 'Laporan Pengadaan';
        require_once __DIR__ . '/../view/laporan/pengadaan.php';
    }

    public function stokHabis(): void
    {
        if (!is_logged_in())
            redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $db = getDB();

        $produkStokHabis = $db->query(
            "SELECT p.kode_barang, p.nama_barang, p.stock, p.stock_min,
                    p.satuan, k.nama_kategori, d.nama_distributor, d.no_hp AS dist_no_hp
             FROM product p
             JOIN kategori_product k ON p.kategori_id    = k.id
             JOIN distributors     d ON p.distributor_id = d.id
             WHERE p.stock <= p.stock_min
             ORDER BY p.stock ASC"
        )->fetch_all(MYSQLI_ASSOC);

        $pageTitle = 'Laporan Stok Menipis / Habis';
        require_once __DIR__ . '/../view/laporan/stok_habis.php';
    }

    public function exportPdf(): void
    {
        if (!is_logged_in()) {
            redirect('auth/login');
        }

        $db = getDB();

        $periode = $_GET['periode'] ?? 'bulan_ini';

        switch ($periode) {
            case 'hari_ini':
                $tgl_dari = date('Y-m-d');
                $tgl_sampai = date('Y-m-d');
                $label = 'Hari Ini (' . date('d M Y') . ')';
                break;

            case 'minggu_ini':
                $tgl_dari = date('Y-m-d', strtotime('monday this week'));
                $tgl_sampai = date('Y-m-d', strtotime('sunday this week'));
                $label = 'Minggu Ini';
                break;

            case 'tahun_ini':
                $tgl_dari = date('Y-01-01');
                $tgl_sampai = date('Y-12-31');
                $label = 'Tahun ' . date('Y');
                break;

            default:
                $tgl_dari = date('Y-m-01');
                $tgl_sampai = date('Y-m-t');
                $label = 'Bulan ' . date('F Y');
                break;
        }

        // Summary
        $stmt = $db->prepare("
        SELECT
            COALESCE(SUM(total_harga),0) AS total_penjualan,
            COUNT(*) AS total_transaksi,
            COALESCE(AVG(total_harga),0) AS rata_transaksi
        FROM transaksi
        WHERE status='Selesai'
        AND DATE(tanggal_transaksi) BETWEEN ? AND ?
    ");

        $stmt->bind_param('ss', $tgl_dari, $tgl_sampai);
        $stmt->execute();
        $summary = $stmt->get_result()->fetch_assoc();

        // Produk Terlaris
        $stmt2 = $db->prepare("
        SELECT
            p.nama_barang,
            SUM(dt.qty) AS total_terjual,
            SUM(dt.subtotal) AS total_pendapatan
        FROM detail_transaksi dt
        JOIN transaksi t ON dt.id_trx = t.id
        JOIN product p ON dt.produk_id = p.id
        WHERE t.status='Selesai'
        AND DATE(t.tanggal_transaksi) BETWEEN ? AND ?
        GROUP BY dt.produk_id
        ORDER BY total_terjual DESC
        LIMIT 10
    ");

        $stmt2->bind_param('ss', $tgl_dari, $tgl_sampai);
        $stmt2->execute();
        $produkTerlaris = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        // Stok Menipis
        $stokMenipis = $db->query("
        SELECT
            p.kode_barang,
            p.nama_barang,
            p.stock,
            p.stock_min,
            k.nama_kategori
        FROM product p
        JOIN kategori_product k ON p.kategori_id = k.id
        WHERE p.stock <= p.stock_min
        ORDER BY p.stock ASC
    ")->fetch_all(MYSQLI_ASSOC);

        require_once __DIR__ . '/../view/laporan/export_pdf.php';
    }
}
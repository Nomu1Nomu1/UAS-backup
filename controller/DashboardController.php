<?php
require_once __DIR__ . '/../model/Product.php';
require_once __DIR__ . '/../model/Transaksi.php';
require_once __DIR__ . '/../model/Pengadaan.php';

class DashboardController
{
    public function index()
    {
        $productModel = new Product();
        $transaksiModel = new Transaksi();
        $pengadaanModel = new Pengadaan();

        // Statistik hari ini
        $totalProduk = count($productModel->getAll());
        $stockMenipis = count($productModel->getStokMenipis());

        $persenProdukAman = 0;

        if ($totalProduk > 0) {
            $persenProdukAman = round(
                (($totalProduk - $stockMenipis) / $totalProduk) * 100
            );
        }

        $transaksiTerakhir = $transaksiModel->getAll('', date('Y-m-d'));
        $pengadaanPending = count(
            array_filter(
                $pengadaanModel->getAll(),
                fn($p) => $p['status'] === 'Pending'
            )
        );

        $totalTRXHariIni = count($transaksiTerakhir);
        $pendapatanHariIni = array_sum(
            array_column($transaksiTerakhir, 'total_harga')
        );

        $listStockMenipis = $productModel->getStokMenipis();

        $pageTitle = 'Dashboard';

        // Load view
        require_once __DIR__ . '/../view/dashboard/index.php';
    }
}

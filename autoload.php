<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/db.php';

require_once __DIR__ . '/model/User.php';
require_once __DIR__ . '/model/Product.php';
require_once __DIR__ . '/model/KategoriProduct.php';
require_once __DIR__ . '/model/Distributor.php';
require_once __DIR__ . '/model/Transaksi.php';
require_once __DIR__ . '/model/Pengadaan.php';

require_once __DIR__ . '/controller/AuthController.php';
require_once __DIR__ . '/controller/DashboardController.php';
require_once __DIR__ . '/controller/UserController.php';
require_once __DIR__ . '/controller/ProductController.php';
require_once __DIR__ . '/controller/KategoriController.php';
require_once __DIR__ . '/controller/DistributorController.php';
require_once __DIR__ . '/controller/TransaksiController.php';
require_once __DIR__ . '/controller/PengadaanController.php';
require_once __DIR__ . '/controller/LaporanController.php';

$page   = $_GET['page']   ?? 'auth';
$action = $_GET['action'] ?? 'login';

$controllerMap = [
    'auth'        => 'AuthController',
    'dashboard'   => 'DashboardController',
    'user'        => 'UserController',
    'product'     => 'ProductController',
    'kategori'    => 'KategoriController',
    'distributor' => 'DistributorController',
    'transaksi'   => 'TransaksiController',
    'pengadaan'   => 'PengadaanController',
    'laporan'     => 'LaporanController',
];

if (isset($controllerMap[$page])) {
    $className = $controllerMap[$page];
    $controller = new $className();

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        http_response_code(404);
        echo '<h1>404 – Halaman tidak ditemukan.</h1>';
    }
} else {
    redirect('auth/login');
}
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '');        
define('DB_HOST', 'sql206.infinityfree.com'); 
define('DB_USER', 'if0_42229422');           
define('DB_PASS', 'YqFhO98kWRu0'); 
define('DB_NAME', 'if0_42229422_uas');

function getDB(): mysqli
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die('Koneksi database gagal: ' . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

function redirect(string $path): void
{
    $base  = rtrim(BASE_URL, '/');
    $parts = explode('/', $path, 2);
    $page   = $parts[0] ?? '';
    $action = $parts[1] ?? 'index';
    header("Location: {$base}/?page={$page}&action={$action}");
    exit;
}

function is_logged_in(): bool
{
    return isset($_SESSION['users']['id']);
}

function allow_roles(array $roles): void
{
    $current = strtolower($_SESSION['users']['role'] ?? '');
    $allowed = array_map('strtolower', $roles);

    if (!in_array($current, $allowed)) {
        $_SESSION['flash'] = 'Anda tidak memiliki akses ke halaman ini.';
        redirect('dashboard/index');
    }
}

function format_rupiah(float $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
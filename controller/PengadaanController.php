<?php
require_once __DIR__ . '/../config/db.php';

class PengadaanController
{
    public function index(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['admin', 'owner']);

        $db     = getDB();
        $search = trim($_GET['search']  ?? '');
        $status = trim($_GET['status']  ?? '');
        $dari   = trim($_GET['dari']    ?? '');
        $sampai = trim($_GET['sampai']  ?? '');

        $sql    = "SELECT p.*, d.nama_distributor, u.nama AS petugas
                   FROM pengadaan p
                   JOIN distributors d ON p.distributor_id = d.id
                   JOIN users u ON p.user_id = u.user_id
                   WHERE 1=1";
        $params = [];
        $types  = '';

        if ($search !== '') {
            $like     = "%{$search}%";
            $sql     .= " AND (p.no_pengadaan LIKE ? OR d.nama_distributor LIKE ?)";
            $params[] = $like;
            $params[] = $like;
            $types   .= 'ss';
        }

        if ($status !== '') {
            $sql     .= " AND p.status = ?";
            $params[] = $status;
            $types   .= 's';
        }

        if ($dari !== '') {
            $sql     .= " AND DATE(p.tanggal_pengadaan) >= ?";
            $params[] = $dari;
            $types   .= 's';
        }

        if ($sampai !== '') {
            $sql     .= " AND DATE(p.tanggal_pengadaan) <= ?";
            $params[] = $sampai;
            $types   .= 's';
        }

        $sql .= " ORDER BY p.id DESC";

        if ($params) {
            $stmt = $db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $pengadaans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $pengadaans = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
        }

        $pageTitle = 'Data Pengadaan';
        require_once __DIR__ . '/../view/pengadaan/index.php';
    }
    public function create(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['admin', 'owner']);

        $db           = getDB();
        $distributors = $db->query("SELECT id, nama_distributor FROM distributors ORDER BY nama_distributor")
                           ->fetch_all(MYSQLI_ASSOC);
        $products     = $db->query(
            "SELECT p.id, p.kode_barang, p.nama_barang, p.harga_beli, p.satuan, k.nama_kategori
             FROM product p
             JOIN kategori_product k ON p.kategori_id = k.id
             ORDER BY p.nama_barang"
        )->fetch_all(MYSQLI_ASSOC);

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $distributor_id      = (int)   ($_POST['distributor_id']      ?? 0);
            $tanggal_pengadaan   = trim($_POST['tanggal_pengadaan']        ?? '');
            $keterangan          = trim($_POST['keterangan']               ?? '') ?: null;
            $items               = $_POST['items']                         ?? [];

            // Validasi dasar
            if ($distributor_id <= 0 || $tanggal_pengadaan === '' || empty($items)) {
                $error = 'Distributor, tanggal, dan minimal satu produk wajib diisi.';
            } else {
                $total_harga = 0;
                $valid_items = [];

                foreach ($items as $item) {
                    $produk_id   = (int)   ($item['produk_id']    ?? 0);
                    $qty         = (int)   ($item['qty']          ?? 0);
                    $harga_sat   = (float) ($item['harga_satuan'] ?? 0);

                    if ($produk_id <= 0 || $qty <= 0 || $harga_sat <= 0) continue;

                    $subtotal      = $qty * $harga_sat;
                    $total_harga  += $subtotal;
                    $valid_items[] = compact('produk_id', 'qty', 'harga_sat', 'subtotal');
                }

                if (empty($valid_items)) {
                    $error = 'Tidak ada item yang valid.';
                } else {
                    $user_id      = $_SESSION['users']['id'];
                    $now          = date('Y-m-d H:i:s');
                    $no_pengadaan = 'PGD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

                    $db->begin_transaction();
                    try {
                        // Insert header pengadaan
                        $stmt = $db->prepare(
                            "INSERT INTO pengadaan
                                (no_pengadaan, distributor_id, user_id, tanggal_pengadaan,
                                 total_harga, status, keterangan, createdAt, updatedAt)
                             VALUES (?, ?, ?, ?, ?, 'Pending', ?, ?, ?)"
                        );
                        $stmt->bind_param(
                            'siisdsss',
                            $no_pengadaan, $distributor_id, $user_id,
                            $tanggal_pengadaan, $total_harga, $keterangan, $now, $now
                        );
                        $stmt->execute();
                        $pengadaan_id = $db->insert_id;

                        // Insert detail
                        foreach ($valid_items as $vi) {
                            $stmt2 = $db->prepare(
                                "INSERT INTO detail_pengadaan
                                    (id_pengadaan, produk_id, qty, harga_satuan, subtotal)
                                 VALUES (?, ?, ?, ?, ?)"
                            );
                            $stmt2->bind_param(
                                'iiidd',
                                $pengadaan_id, $vi['produk_id'],
                                $vi['qty'], $vi['harga_sat'], $vi['subtotal']
                            );
                            $stmt2->execute();
                        }

                        $db->commit();
                        $_SESSION['flash'] = "Pengadaan <strong>{$no_pengadaan}</strong> berhasil dibuat.";
                        redirect('pengadaan/index');
                    } catch (Exception $e) {
                        $db->rollback();
                        $error = 'Gagal menyimpan pengadaan: ' . $e->getMessage();
                    }
                }
            }
        }

        $pageTitle = 'Tambah Pengadaan';
        require_once __DIR__ . '/../view/pengadaan/create.php';
    }

    public function detail(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['admin', 'owner']);

        $id = (int) ($_GET['id'] ?? 0);
        $db = getDB();

        $stmt = $db->prepare(
            "SELECT p.*, d.nama_distributor, d.no_hp AS dist_telp,
                    d.email AS dist_email, u.nama AS petugas
             FROM pengadaan p
             JOIN distributors d ON p.distributor_id = d.id
             JOIN users u ON p.user_id = u.user_id
             WHERE p.id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $pengadaan = $stmt->get_result()->fetch_assoc();

        if (!$pengadaan) redirect('pengadaan/index');

        $stmt2 = $db->prepare(
            "SELECT dp.*, pr.nama_barang, pr.kode_barang, pr.satuan
             FROM detail_pengadaan dp
             JOIN product pr ON dp.produk_id = pr.id
             WHERE dp.id_pengadaan = ?"
        );
        $stmt2->bind_param('i', $id);
        $stmt2->execute();
        $details = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        $pageTitle = 'Detail Pengadaan';
        require_once __DIR__ . '/../view/pengadaan/detail.php';
    }

    public function terima(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['admin', 'owner']);

        $id = (int) ($_GET['id'] ?? 0);
        $db = getDB();

        $stmt = $db->prepare("SELECT * FROM pengadaan WHERE id = ? AND status = 'Pending'");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $pengadaan = $stmt->get_result()->fetch_assoc();

        if (!$pengadaan) {
            $_SESSION['flash'] = 'Pengadaan tidak ditemukan atau sudah diproses.';
            redirect('pengadaan/index');
        }

        // Ambil detail
        $stmt2 = $db->prepare("SELECT produk_id, qty FROM detail_pengadaan WHERE id_pengadaan = ?");
        $stmt2->bind_param('i', $id);
        $stmt2->execute();
        $details = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        $now = date('Y-m-d H:i:s');
        $db->begin_transaction();
        try {
            // Ubah status pengadaan
            $stmt3 = $db->prepare("UPDATE pengadaan SET status = 'Diterima', updatedAt = ? WHERE id = ?");
            $stmt3->bind_param('si', $now, $id);
            $stmt3->execute();

            // Tambah stok produk
            foreach ($details as $d) {
                $stmt4 = $db->prepare("UPDATE product SET stock = stock + ?, updatedAt = ? WHERE id = ?");
                $stmt4->bind_param('isi', $d['qty'], $now, $d['produk_id']);
                $stmt4->execute();
            }

            $db->commit();
            $_SESSION['flash'] = 'Pengadaan diterima dan stok produk berhasil diperbarui.';
        } catch (Exception $e) {
            $db->rollback();
            $_SESSION['flash'] = 'Gagal menerima pengadaan: ' . $e->getMessage();
        }

        redirect('pengadaan/index');
    }

    public function batal(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['admin', 'owner']);

        $id = (int) ($_GET['id'] ?? 0);
        $db = getDB();

        $stmt = $db->prepare("SELECT id FROM pengadaan WHERE id = ? AND status = 'Pending'");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        if (!$stmt->get_result()->fetch_assoc()) {
            $_SESSION['flash'] = 'Pengadaan tidak ditemukan atau tidak bisa dibatalkan.';
            redirect('pengadaan/index');
        }

        $now  = date('Y-m-d H:i:s');
        $stmt2 = $db->prepare("UPDATE pengadaan SET status = 'Dibatalkan', updatedAt = ? WHERE id = ?");
        $stmt2->bind_param('si', $now, $id);

        if ($stmt2->execute()) {
            $_SESSION['flash'] = 'Pengadaan berhasil dibatalkan.';
        } else {
            $_SESSION['flash'] = 'Gagal membatalkan pengadaan.';
        }

        redirect('pengadaan/index');
    }
}
<?php
require_once __DIR__ . '/../config/db.php';

class ProductController
{
    public function index(): void
    {
        if (!is_logged_in()) redirect('auth/login');

        $db     = getDB();
        $search = trim($_GET['search'] ?? '');
        $katId  = (int) ($_GET['kategori_id'] ?? 0);

        $sql    = "SELECT p.*, k.nama_kategori, d.nama_distributor
                   FROM product p
                   JOIN kategori_product k ON p.kategori_id    = k.id
                   JOIN distributors     d ON p.distributor_id = d.id
                   WHERE 1=1";
        $params = [];
        $types  = '';

        if ($search !== '') {
            $like     = "%{$search}%";
            $sql     .= " AND (p.nama_barang LIKE ? OR p.kode_barang LIKE ?)";
            $params[] = $like;
            $params[] = $like;
            $types   .= 'ss';
        }

        if ($katId > 0) {
            $sql     .= " AND p.kategori_id = ?";
            $params[] = $katId;
            $types   .= 'i';
        }

        $sql .= " ORDER BY p.id DESC";

        if ($params) {
            $stmt = $db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $products = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
        }

        $kategoris = $db->query(
            "SELECT * FROM kategori_product ORDER BY nama_kategori"
        )->fetch_all(MYSQLI_ASSOC);

        $pageTitle = 'Data Produk';
        require_once __DIR__ . '/../view/produk/index.php';
    }

    public function show(): void
    {
        if (!is_logged_in()) redirect('auth/login');

        $id   = (int) ($_GET['id'] ?? 0);
        $db   = getDB();

        $stmt = $db->prepare(
            "SELECT p.*, k.nama_kategori, d.nama_distributor
             FROM product p
             JOIN kategori_product k ON p.kategori_id    = k.id
             JOIN distributors     d ON p.distributor_id = d.id
             WHERE p.id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if (!$product) redirect('product/index');

        $pageTitle = 'Detail Produk';
        require_once __DIR__ . '/../view/produk/show.php';
    }

    public function create(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $db        = getDB();
        $kategoris = $db->query(
            "SELECT * FROM kategori_product ORDER BY nama_kategori"
        )->fetch_all(MYSQLI_ASSOC);
        $distribs  = $db->query(
            "SELECT * FROM distributors ORDER BY nama_distributor"
        )->fetch_all(MYSQLI_ASSOC);
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $kode        = trim($_POST['kode_barang']   ?? '');
            $nama        = trim($_POST['nama_barang']   ?? '');
            $kategori_id = (int)   ($_POST['kategori_id']   ?? 0);
            $dist_id     = (int)   ($_POST['distributor_id'] ?? 0);
            $stock       = (int)   ($_POST['stock']          ?? 0);
            $stock_min   = (int)   ($_POST['stock_min']      ?? 0);
            $harga_beli  = (float) ($_POST['harga_beli']     ?? 0);
            $harga_jual  = (float) ($_POST['harga_jual']     ?? 0);
            $satuan      = trim($_POST['satuan']             ?? '');
            $deskripsi   = trim($_POST['deskripsi']          ?? '') ?: null;

            if (empty($kode) || empty($nama) || !$kategori_id || !$dist_id || empty($satuan)) {
                $error = 'Semua field wajib diisi.';
            } else {
                // Upload foto jika ada
                $foto = null;
                if (!empty($_FILES['foto']['tmp_name'])) {
                    require_once __DIR__ . '/../model/Product.php';
                    $foto = Product::uploadFoto($_FILES['foto']);
                    if ($foto === null) {
                        $error = 'Gagal upload foto. Pastikan format file adalah JPG, PNG, GIF, atau WEBP.';
                    }
                }

                if (empty($error)) {
                    $now  = date('Y-m-d H:i:s');
                    $stmt = $db->prepare(
                        "INSERT INTO product
                            (kode_barang, nama_barang, kategori_id, distributor_id,
                             stock, stock_min, harga_beli, harga_jual, satuan, deskripsi, foto, createdAt, updatedAt)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                    );
                    $stmt->bind_param(
                        'ssiiiiddsssss',
                        $kode, $nama, $kategori_id, $dist_id,
                        $stock, $stock_min, $harga_beli, $harga_jual,
                        $satuan, $deskripsi, $foto, $now, $now
                    );
                    if ($stmt->execute()) {
                        redirect('product/index');
                    } else {
                        $error = 'Kode barang sudah digunakan.';
                    }
                }
            }
        }

        $pageTitle = 'Tambah Produk';
        require_once __DIR__ . '/../view/produk/create.php';
    }

    public function edit(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $id = (int) ($_GET['id'] ?? 0);
        $db = getDB();

        $stmt = $db->prepare("SELECT * FROM product WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if (!$product) redirect('product/index');

        $kategoris = $db->query(
            "SELECT * FROM kategori_product ORDER BY nama_kategori"
        )->fetch_all(MYSQLI_ASSOC);
        $distribs  = $db->query(
            "SELECT * FROM distributors ORDER BY nama_distributor"
        )->fetch_all(MYSQLI_ASSOC);
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $kode        = trim($_POST['kode_barang']   ?? '');
            $nama        = trim($_POST['nama_barang']   ?? '');
            $kategori_id = (int)   ($_POST['kategori_id']   ?? 0);
            $dist_id     = (int)   ($_POST['distributor_id'] ?? 0);
            $stock       = (int)   ($_POST['stock']          ?? 0);
            $stock_min   = (int)   ($_POST['stock_min']      ?? 0);
            $harga_beli  = (float) ($_POST['harga_beli']     ?? 0);
            $harga_jual  = (float) ($_POST['harga_jual']     ?? 0);
            $satuan      = trim($_POST['satuan']             ?? '');
            $deskripsi   = trim($_POST['deskripsi']          ?? '') ?: null;
            $hapusFoto   = isset($_POST['hapus_foto']) && $_POST['hapus_foto'] === '1';

            if (empty($kode) || empty($nama) || !$kategori_id || !$dist_id || empty($satuan)) {
                $error = 'Semua field wajib diisi.';
            } else {
                require_once __DIR__ . '/../model/Product.php';
                $fotoLama = $product['foto'] ?? null;
                $fotoUpdate = null;

                if ($hapusFoto && $fotoLama) {
                    $uploadDir = __DIR__ . '/../uploads/produk/';
                    if (file_exists($uploadDir . $fotoLama)) @unlink($uploadDir . $fotoLama);
                    $fotoUpdate = '';
                }

                if (!empty($_FILES['foto']['tmp_name'])) {
                    $namaFotoBaru = Product::uploadFoto($_FILES['foto'], $fotoLama);
                    if ($namaFotoBaru === null) {
                        $error = 'Gagal upload foto. Pastikan format file adalah JPG, PNG, GIF, atau WEBP.';
                    } else {
                        $fotoUpdate = $namaFotoBaru;
                    }
                }

                if (empty($error)) {
                    $now  = date('Y-m-d H:i:s');

                    if ($fotoUpdate !== null) {
                        $fotoDb = $fotoUpdate === '' ? null : $fotoUpdate;
                        $stmt = $db->prepare(
                            "UPDATE product
                             SET kode_barang = ?, nama_barang = ?, kategori_id = ?, distributor_id = ?,
                                 stock = ?, stock_min = ?, harga_beli = ?, harga_jual = ?,
                                 satuan = ?, deskripsi = ?, foto = ?, updatedAt = ?
                             WHERE id = ?"
                        );
                        $stmt->bind_param(
                            'ssiiiiddssssi',
                            $kode, $nama, $kategori_id, $dist_id,
                            $stock, $stock_min, $harga_beli, $harga_jual,
                            $satuan, $deskripsi, $fotoDb, $now, $id
                        );
                    } else {
                        // Tidak ada perubahan foto
                        $stmt = $db->prepare(
                            "UPDATE product
                             SET kode_barang = ?, nama_barang = ?, kategori_id = ?, distributor_id = ?,
                                 stock = ?, stock_min = ?, harga_beli = ?, harga_jual = ?,
                                 satuan = ?, deskripsi = ?, updatedAt = ?
                             WHERE id = ?"
                        );
                        $stmt->bind_param(
                            'ssiiiiddsssi',
                            $kode, $nama, $kategori_id, $dist_id,
                            $stock, $stock_min, $harga_beli, $harga_jual,
                            $satuan, $deskripsi, $now, $id
                        );
                    }

                    if ($stmt->execute()) {
                        redirect('product/index');
                    } else {
                        $error = 'Kode barang sudah digunakan oleh produk lain.';
                    }
                }
            }
        }

        $pageTitle = 'Edit Produk';
        require_once __DIR__ . '/../view/produk/edit.php';
    }

    public function delete(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $id = (int) ($_GET['id'] ?? 0);
        $db = getDB();

        $stmt = $db->prepare("SELECT COUNT(*) AS c FROM detail_transaksi WHERE produk_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $used = (int) $stmt->get_result()->fetch_assoc()['c'];

        if ($used > 0) {
            $_SESSION['flash'] = 'Produk tidak dapat dihapus karena sudah ada di transaksi.';
        } else {
            $stmtFoto = $db->prepare("SELECT foto FROM product WHERE id = ?");
            $stmtFoto->bind_param('i', $id);
            $stmtFoto->execute();
            $row = $stmtFoto->get_result()->fetch_assoc();
            if ($row && $row['foto']) {
                $fotoPath = __DIR__ . '/../uploads/produk/' . $row['foto'];
                if (file_exists($fotoPath)) @unlink($fotoPath);
            }

            $stmt = $db->prepare("DELETE FROM product WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $_SESSION['flash'] = 'Produk berhasil dihapus.';
        }

        redirect('product/index');
    }
}

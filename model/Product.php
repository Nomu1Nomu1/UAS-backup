<?php
require_once __DIR__ . '/../config/db.php';

class Product
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll(string $search = '', int $kategori_id = 0): array
    {
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

        if ($kategori_id > 0) {
            $sql     .= " AND p.kategori_id = ?";
            $params[] = $kategori_id;
            $types   .= 'i';
        }

        $sql .= " ORDER BY p.id DESC";

        if ($params) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, k.nama_kategori, d.nama_distributor
             FROM product p
             JOIN kategori_product k ON p.kategori_id    = k.id
             JOIN distributors     d ON p.distributor_id = d.id
             WHERE p.id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ?: null;
    }

    public function getForSelect(bool $onlyInStock = false): array
    {
        $where = $onlyInStock ? "WHERE stock > 0" : "";
        return $this->db->query(
            "SELECT id, kode_barang, nama_barang, harga_jual, harga_beli, stock, satuan, foto
             FROM product {$where} ORDER BY nama_barang"
        )->fetch_all(MYSQLI_ASSOC);
    }

    public function getStokMenipis(): array
    {
        return $this->db->query(
            "SELECT p.kode_barang, p.nama_barang, p.stock, p.stock_min,
                    p.satuan, p.foto, k.nama_kategori, d.nama_distributor, d.no_hp AS dist_no_hp
             FROM product p
             JOIN kategori_product k ON p.kategori_id    = k.id
             JOIN distributors     d ON p.distributor_id = d.id
             WHERE p.stock <= p.stock_min
             ORDER BY p.stock ASC"
        )->fetch_all(MYSQLI_ASSOC);
    }

    public function tambahStok(int $id, int $qty): bool
    {
        $now  = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare(
            "UPDATE product SET stock = stock + ?, updatedAt = ? WHERE id = ?"
        );
        $stmt->bind_param('isi', $qty, $now, $id);
        return $stmt->execute();
    }

    public function kurangiStok(int $id, int $qty): bool
    {
        $now  = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare(
            "UPDATE product SET stock = stock - ?, updatedAt = ? WHERE id = ?"
        );
        $stmt->bind_param('isi', $qty, $now, $id);
        return $stmt->execute();
    }

    public function cekStok(int $id, int $qty): bool
    {
        $stmt = $this->db->prepare("SELECT stock FROM product WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row && (int) $row['stock'] >= $qty;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM product WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
    public static function uploadFoto(array $fileInput, ?string $fotoLama = null): ?string
    {
        if (empty($fileInput['tmp_name'])) return null;
        if ($fileInput['error'] !== UPLOAD_ERR_OK) return null;

        $allowed  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mime     = mime_content_type($fileInput['tmp_name']);
        if (!in_array($mime, $allowed)) return null;

        // Validasi ukuran maks 2MB
        if ($fileInput['size'] > 2 * 1024 * 1024) return null;

        $ext      = strtolower(pathinfo($fileInput['name'], PATHINFO_EXTENSION));
        $namaFile = uniqid('prod_', true) . '.' . $ext;
        $dir      = __DIR__ . '/../uploads/produk/';
        $tujuan   = $dir . $namaFile;

        if (!move_uploaded_file($fileInput['tmp_name'], $tujuan)) return null;

        // Hapus foto lama
        if ($fotoLama && file_exists($dir . $fotoLama)) {
            @unlink($dir . $fotoLama);
        }

        return $namaFile;
    }
    public static function fotoUrl(?string $foto): string
    {
        if (!empty($foto)) {
            return '/uploads/produk/' . htmlspecialchars($foto);
        }
        return '/assets/img/no-image.svg';
    }
}

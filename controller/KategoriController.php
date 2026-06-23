<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../model/KategoriProduct.php';

class KategoriController
{
    private KategoriProduct $model;
    public function __construct() {
        $this->model = new KategoriProduct();
    }
    public function index(): void
    {
        if (!is_logged_in()) redirect('auth/login');

        $search = trim($_GET['search'] ?? '');
        $kategoris = $this->model->getAll($search);

        $pageTitle = 'Kategori Produk';
        require_once __DIR__ . '/../view/kategori/index.php';
    }

    public function create(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama      = trim($_POST['nama_kategori'] ?? '');
            $deskripsi = trim($_POST['deskripsi']     ?? '') ?: null;

            if (empty($nama)) {
                $error = 'Nama kategori wajib diisi.';
            } else {
                $this->model->create($nama, $deskripsi);
                redirect('kategori/index');
            }
        }

        $pageTitle = 'Tambah Kategori';
        require_once __DIR__ . '/../view/kategori/create.php';
    }

    public function edit(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $id = (int) ($_GET['id'] ?? 0);
        $kategori = $this->model->findById($id);

        if (!$kategori) redirect('kategori/index');

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama      = trim($_POST['nama_kategori'] ?? '');
            $deskripsi = trim($_POST['deskripsi']     ?? '') ?: null;

            if (empty($nama)) {
                $error = 'Nama kategori wajib diisi.';
            } else {
                $this->model->update($id, $nama, $deskripsi);
                redirect('kategori/index');
            }
        }

        $pageTitle = 'Edit Kategori';
        require_once __DIR__ . '/../view/kategori/edit.php';
    }

    public function delete(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $id = (int) ($_GET['id'] ?? 0);

        if ($this->model->isUsed($id)) {
            $_SESSION['flash'] = 'Kategori tidak dapat dihapus karena masih digunakan oleh produk.';
        } else {
            $this->model->delete($id);
            $_SESSION['flash'] = 'Kategori berhasil dihapus.';
        }

        redirect('kategori/index');
    }

}

<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../model/Distributor.php';

class DistributorController
{
    private Distributor $model;

    public function __construct()
    {
        $this->model = new Distributor();
    }

    public function index(): void
    {
        if (!is_logged_in()) {
            redirect('auth/login');
        }

        $search      = trim($_GET['search'] ?? '');
        $distributors = $this->model->getAll($search);

        $pageTitle = 'Data Distributor';
        require_once __DIR__ . '/../view/distributor/index.php';
    }

    public function create(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama       = trim($_POST['nama_distributor'] ?? '');
            $alamat     = trim($_POST['alamat']           ?? '');
            $no_hp      = trim($_POST['no_hp']            ?? '');
            $email      = trim($_POST['email']            ?? '') ?: null;
            $keterangan = trim($_POST['keterangan']       ?? '') ?: null;

            if (empty($nama) || empty($alamat) || empty($no_hp)) {
                $error = 'Nama, alamat, dan no. HP wajib diisi.';
            } else {
                $this->model->create($nama, $alamat, $no_hp, $email, $keterangan);
                redirect('distributor/index');
            }
        }

        $pageTitle = 'Tambah Distributor';
        require_once __DIR__ . '/../view/distributor/create.php';
    }

    public function edit(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $id          = (int) ($_GET['id'] ?? 0);
        $distributor = $this->model->findById($id);

        if (!$distributor) redirect('distributor/index');

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama       = trim($_POST['nama_distributor'] ?? '');
            $alamat     = trim($_POST['alamat']           ?? '');
            $no_hp      = trim($_POST['no_hp']            ?? '');
            $email      = trim($_POST['email']            ?? '') ?: null;
            $keterangan = trim($_POST['keterangan']       ?? '') ?: null;

            if (empty($nama) || empty($alamat) || empty($no_hp)) {
                $error = 'Nama, alamat, dan no. HP wajib diisi.';
            } else {
                $this->model->update($id, $nama, $alamat, $no_hp, $email, $keterangan);
                redirect('distributor/index');
            }
        }

        $pageTitle = 'Edit Distributor';
        require_once __DIR__ . '/../view/distributor/edit.php';
    }

    public function delete(): void
    {
        if (!is_logged_in()) redirect('auth/login');
        allow_roles(['owner', 'admin']);

        $id = (int) ($_GET['id'] ?? 0);
        $this->model->delete($id);

        $_SESSION['flash'] = 'Distributor berhasil dihapus.';
        redirect('distributor/index');
    }
}

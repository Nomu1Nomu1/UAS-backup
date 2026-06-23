<?php ob_start(); ?>

<?php
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>

<div class="mb-4">
    <div>
        <h1 class="fw-bold">Data Pengadaan</h1>
        <p class="text-secondary">Kelola pengadaan barang dari distributor</p>
    </div>

    <a href="/?page=pengadaan&action=create"
       class="btn btn-primary px-4"
       style="border-radius:14px; font-weight:600;">
        <i class="bi bi-plus-lg me-2"></i>Tambah Pengadaan
    </a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= $flash ?>
    </div>
<?php endif; ?>

<!-- Filter -->
<div class="card-section mb-4">

    <form method="GET" action="/" class="row g-3 align-items-end">

        <input type="hidden" name="page" value="pengadaan">
        <input type="hidden" name="action" value="index">

        <div class="col-md-4">
            <label class="form-label fw-semibold small">
                Cari Pengadaan
            </label>

            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-secondary"></i>
                </span>

                <input
                    type="text"
                    name="search"
                    class="form-control border-start-0 ps-0"
                    placeholder="Nomor pengadaan atau distributor..."
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    style="border-radius:0 12px 12px 0;">
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold small">
                Status
            </label>

            <select name="status"
                    class="form-select"
                    style="border-radius:12px;">

                <option value="">Semua Status</option>

                <option value="Pending"
                    <?= ($_GET['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>
                    Pending
                </option>

                <option value="Diterima"
                    <?= ($_GET['status'] ?? '') === 'Diterima' ? 'selected' : '' ?>>
                    Diterima
                </option>

                <option value="Dibatalkan"
                    <?= ($_GET['status'] ?? '') === 'Dibatalkan' ? 'selected' : '' ?>>
                    Dibatalkan
                </option>

            </select>
        </div>

        <div class="col-md-4 d-flex gap-2">

            <button type="submit"
                    class="btn btn-primary"
                    style="border-radius:12px; font-weight:600;">
                <i class="bi bi-funnel me-1"></i>Cari
            </button>

            <a href="/?page=pengadaan&action=index"
               class="btn btn-outline-secondary"
               style="border-radius:12px;">
                <i class="bi bi-x-lg"></i>
            </a>

        </div>

    </form>

</div>

<!-- Tabel -->
<div class="card-section">

    <?php if (empty($pengadaans)): ?>

        <div class="text-center py-5 text-secondary">
            <i class="bi bi-cart-plus display-4 d-block mb-3 opacity-50"></i>
            <p class="mb-0">Belum ada data pengadaan</p>
        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0" style="font-size:14px;">

                <thead>
                    <tr style="background:#f8fafc; color:#64748b; font-size:13px; font-weight:600;">
                        <th class="border-0 rounded-start ps-3 py-3">#</th>
                        <th class="border-0">No Pengadaan</th>
                        <th class="border-0">Distributor</th>
                        <th class="border-0">Tanggal</th>
                        <th class="border-0">Total</th>
                        <th class="border-0">Status</th>
                        <th class="border-0 rounded-end pe-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($pengadaans as $i => $p): ?>

                        <tr>

                            <td class="ps-3 text-secondary">
                                <?= $i + 1 ?>
                            </td>

                            <td>
                                <span class="fw-semibold">
                                    <?= htmlspecialchars($p['no_pengadaan']) ?>
                                </span>
                            </td>

                            <td>
                                <?= htmlspecialchars($p['nama_distributor']) ?>
                            </td>

                            <td>
                                <?= date('d/m/Y', strtotime($p['tanggal_pengadaan'])) ?>
                            </td>

                            <td class="fw-semibold text-success">
                                Rp <?= number_format($p['total_harga'], 0, ',', '.') ?>
                            </td>

                            <td>

                                <?php if ($p['status'] === 'Pending'): ?>

                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>

                                <?php elseif ($p['status'] === 'Diterima'): ?>

                                    <span class="badge bg-success">
                                        Diterima
                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-danger">
                                        Dibatalkan
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td class="text-center pe-3">

                                <a href="/?page=pengadaan&action=detail&id=<?= $p['id'] ?>"
                                   class="btn btn-sm btn-outline-info me-1"
                                   style="border-radius:8px;"
                                   title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <?php if ($p['status'] === 'Pending'): ?>

                                    <a href="/?page=pengadaan&action=terima&id=<?= $p['id'] ?>"
                                       class="btn btn-sm btn-outline-success me-1"
                                       style="border-radius:8px;"
                                       title="Terima"
                                       onclick="return confirm('Terima pengadaan ini?')">
                                        <i class="bi bi-check-lg"></i>
                                    </a>

                                    <a href="/?page=pengadaan&action=batal&id=<?= $p['id'] ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       style="border-radius:8px;"
                                       title="Batalkan"
                                       onclick="return confirm('Batalkan pengadaan ini?')">
                                        <i class="bi bi-x-lg"></i>
                                    </a>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <div class="mt-3 text-secondary small ps-2">
            Total: <strong><?= count($pengadaans) ?></strong> pengadaan
        </div>

    <?php endif; ?>

</div>

<?php
$content = ob_get_clean();
$title = 'Data Pengadaan';
$pageTitle = 'Data Pengadaan';
require_once __DIR__ . '/../layouts/main.php';
?>
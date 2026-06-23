<?php ob_start(); ?>

<?php
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>

<div class="mb-4">
    <div>
    <h1 class="fw-bold">Data Distributor</h1>
    <p class="text-secondary">Kelola data distributor/supplier</p>
    </div>
    <a href="/?page=distributor&action=create" class="btn btn-primary px-4"
        style="border-radius:14px; font-weight:600;">
        <i class="bi bi-plus-lg me-2"></i>Tambah Distributor
    </a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<!-- Search -->
<div class="card-section mb-4">
    <form method="GET" action="/" class="row g-3 align-items-end">
        <input type="hidden" name="page" value="distributor">
        <input type="hidden" name="action" value="index">

        <div class="col-md-6">
            <label class="form-label fw-semibold small">Cari Distributor</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" name="search" class="form-control border-start-0 ps-0"
                        placeholder="Nama, alamat, atau no. HP..."
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        style="border-radius:0 12px 12px 0;">
            </div>
        </div>

        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary" style="border-radius:12px; font-weight:600;">
                <i class="bi bi-funnel me-1"></i>Cari
            </button>
            <a href="/?page=distributor&action=index" class="btn btn-outline-secondary" style="border-radius:12px;">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </form>
</div>

<!-- Tabel -->
<div class="card-section">
    <?php if (empty($distributors)): ?>
        <div class="text-center py-5 text-secondary">
            <i class="bi bi-truck display-4 d-block mb-3 opacity-50"></i>
            <p class="mb-0">Belum ada data distributor</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                <thead>
                    <tr style="background:#f8fafc; color:#64748b; font-size:13px; font-weight:600;">
                        <th class="border-0 rounded-start ps-3 py-3">#</th>
                        <th class="border-0">Nama Distributor</th>
                        <th class="border-0">No. HP</th>
                        <th class="border-0">Email</th>
                        <th class="border-0">Alamat</th>
                        <th class="border-0">Keterangan</th>
                        <th class="border-0 rounded-end pe-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($distributors as $i => $d): ?>
                        <tr>
                            <td class="ps-3 text-secondary"><?= $i + 1 ?></td>
                            <td>
                                <span class="fw-semibold"><?= htmlspecialchars($d['nama_distributor']) ?></span>
                            </td>
                            <td>
                                <i class="bi bi-telephone me-1 text-secondary"></i>
                                <?= htmlspecialchars($d['no_hp']) ?>
                            </td>
                            <td class="text-secondary">
                                <?= $d['email'] ? htmlspecialchars($d['email']) : '<span class="text-muted fst-italic">-</span>' ?>
                            </td>
                            <td class="text-secondary" style="max-width:180px;">
                                <span class="d-inline-block text-truncate" style="max-width:160px;"
                                        title="<?= htmlspecialchars($d['alamat']) ?>">
                                    <?= htmlspecialchars($d['alamat']) ?>
                                </span>
                            </td>
                            <td class="text-secondary" style="max-width:150px;">
                                <?php if ($d['keterangan']): ?>
                                    <span class="d-inline-block text-truncate" style="max-width:130px;"
                                            title="<?= htmlspecialchars($d['keterangan']) ?>">
                                        <?= htmlspecialchars($d['keterangan']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted fst-italic">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-3">
                                <a href="/?page=distributor&action=edit&id=<?= $d['id'] ?>"
                                    class="btn btn-sm btn-outline-warning me-1"
                                    style="border-radius:8px;" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/?page=distributor&action=delete&id=<?= $d['id'] ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    style="border-radius:8px;" title="Hapus"
                                    onclick="return confirm('Yakin hapus distributor <?= htmlspecialchars($d['nama_distributor']) ?>?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3 text-secondary small ps-2">
            Total: <strong><?= count($distributors) ?></strong> distributor
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$title = 'Data Distributor';
$pageTitle = 'Data Distributor';
require_once __DIR__ . '/../layouts/main.php';
?>
<?php ob_start(); ?>

<?php
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>

<div class="mb-4 d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h1 class="fw-bold">Data Kategori</h1>
        <p class="text-secondary">Kelola kategori produk</p>
    </div>
    <a href="/?page=kategori&action=create" class="btn btn-primary px-4"
        style="border-radius:14px; font-weight:600;">
        <i class="bi bi-plus-lg me-2"></i>Tambah Kategori
    </a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<div class="card-section mb-4">
    <form method="GET" action="/" class="row g-3 align-items-end">
        <input type="hidden" name="page" value="kategori">
        <input type="hidden" name="action" value="index">

        <div class="col-md-6">
            <label class="form-label fw-semibold small">Cari Kategori</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" name="search" class="form-control border-start-0 ps-0"
                        placeholder="Nama atau deskripsi kategori..."
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        style="border-radius:0 12px 12px 0;">
            </div>
        </div>

        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary" style="border-radius:12px; font-weight:600;">
                <i class="bi bi-funnel me-1"></i>Cari
            </button>
            <a href="/?page=kategori&action=index" class="btn btn-outline-secondary" style="border-radius:12px;">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </form>
</div>

<div class="card-section">
    <?php if (empty($kategoris)): ?>
        <div class="text-center py-5 text-secondary">
            <i class="bi bi-tags display-4 d-block mb-3 opacity-50"></i>
            <p class="mb-0">Belum ada data kategori</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                <thead>
                    <tr style="background:#f8fafc; color:#64748b; font-size:13px; font-weight:600;">
                        <th class="border-0 rounded-start ps-3 py-3">#</th>
                        <th class="border-0">Nama Kategori</th>
                        <th class="border-0">Deskripsi</th>
                        <th class="border-0 text-center">Jumlah Produk</th>
                        <th class="border-0 rounded-end pe-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kategoris as $i => $k): ?>
                        <tr>
                            <td class="ps-3 text-secondary"><?= $i + 1 ?></td>
                            <td>
                                <span class="fw-semibold"><?= htmlspecialchars($k['nama_kategori']) ?></span>
                            </td>
                            <td class="text-secondary" style="max-width:280px;">
                                <?php if (!empty($k['deskripsi'])): ?>
                                    <span class="d-inline-block text-truncate" style="max-width:260px;"
                                            title="<?= htmlspecialchars($k['deskripsi']) ?>">
                                        <?= htmlspecialchars($k['deskripsi']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted fst-italic">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill text-bg-light border" style="font-weight:600;">
                                    <?= (int) $k['jumlah_produk'] ?> produk
                                </span>
                            </td>
                            <td class="text-center pe-3">
                                <a href="/?page=kategori&action=edit&id=<?= $k['id'] ?>"
                                    class="btn btn-sm btn-outline-warning me-1"
                                    style="border-radius:8px;" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/?page=kategori&action=delete&id=<?= $k['id'] ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    style="border-radius:8px;" title="Hapus"
                                    onclick="return confirm('Yakin hapus kategori <?= htmlspecialchars($k['nama_kategori']) ?>?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3 text-secondary small ps-2">
            Total: <strong><?= count($kategoris) ?></strong> kategori
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$title = 'Data Kategori';
$pageTitle = 'Data Kategori';
require_once __DIR__ . '/../layouts/main.php';
?>
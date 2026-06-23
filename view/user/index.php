<?php ob_start(); ?>

<?php
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>

<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h1 class="fw-bold">Manajemen User</h1>
        <p class="text-secondary mb-0">Kelola semua akun yang bertugas di aplikasi</p>
    </div>
    <a href="/?page=user&action=create" class="btn btn-primary px-4"
       style="border-radius:14px; font-weight:600;">
        <i class="bi bi-plus-lg me-2"></i>Tambah
    </a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>


<!---FILTER--->
<div class="card-section mb-4">
    <form method="GET" action="/" class="row g-3 align-items-end">
        <input type="hidden" name="page" value="user">
        <input type="hidden" name="action" value="index">

        <div class="col-md-5">
            <label class="form-label fw-semibold small">Cari</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" name="search" class="form-control border-start-0 ps-0"
                       placeholder="Nama atau username"
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                       style="border-radius:0 12px 12px 0;">
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold small">Filter Status</label>
            <select name="status" class="form-select" style="border-radius:12px;">
                <option value="">Semua Status</option>
                <option value="Y" <?= (isset($_GET['status']) && $_GET['status'] == 'Y') ? 'selected' : '' ?>>Aktif</option>
                <option value="N" <?= (isset($_GET['status']) && $_GET['status'] == 'N') ? 'selected' : '' ?>>Non-Aktif</option>
            </select>
        </div>

        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill" style="border-radius:12px; font-weight:600;">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <a href="/?page=user&action=index" class="btn btn-outline-secondary" style="border-radius:12px;">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </form>
</div>


<!---Tabel--->
<div class="card-section">
    <?php if (empty($users)): ?>
        <div class="text-center py-5 text-secondary">
            <i class="bi bi-person-badge display-4 d-block mb-3 opacity-50"></i>
            <p class="mb-0">Belum ada data</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                <thead>
                    <tr style="background:#f8fafc; color:#64748b; font-size:13px; font-weight:600;">
                        <th class="border-0 rounded-start ps-3 py-3">No</th>
                        <th class="border-0">Nama</th>
                        <th class="border-0">Username</th>
                        <th class="border-0">Role</th>
                        <th class="border-0">Status</th>
                        <th class="border-0 rounded-end pe-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $i => $u): ?>
                        <tr>
                            <td class="ps-3 text-secondary"><?= $i + 1 ?></td>
                            <td>
                                <span class="fw-semibold text-primary"><?= htmlspecialchars($u['nama']) ?></span>
                            </td>
                            <td class="text-secondary"><?= htmlspecialchars($u['username']) ?></td>
                            <td class="text-secondary"><?= htmlspecialchars($u['role']) ?></td>
                            <td>
                                <?php if ($u['is_active'] == 'Y'): ?>
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                        <i class="bi bi-check-circle me-1"></i>Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">
                                        <i class="bi bi-x-circle me-1"></i>Non-Aktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-3">
                                <a href="/?page=user&action=edit&id=<?= $u['user_id'] ?>"
                                   class="btn btn-sm btn-outline-warning me-1"
                                   style="border-radius:8px;" title="Edit Kasir">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                <a href="/?page=user&action=delete&id=<?= $u['user_id'] ?>"
                                   onclick="return confirm('Yakin ingin menghapus <?= htmlspecialchars($u['nama']) ?>?')"
                                   class="btn btn-sm btn-outline-danger" style="border-radius:8px;">
                                     <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3 text-secondary small ps-2">
            Total: <strong><?= count($users) ?></strong> akun 
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>
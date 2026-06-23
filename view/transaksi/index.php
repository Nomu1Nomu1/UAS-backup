<?php ob_start(); ?>

<?php
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>

<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h1 class="fw-bold">Data Transaksi</h1>
        <p class="text-secondary mb-0">Riwayat semua transaksi penjualan</p>
    </div>
    <a href="/?page=transaksi&action=kasir" class="btn btn-primary px-4"
       style="border-radius:14px; font-weight:600;">
        <i class="bi bi-plus-lg me-2"></i>Transaksi Baru
    </a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<!-- Filter -->
<div class="card-section mb-4">
    <form method="GET" action="/" class="row g-3 align-items-end">
        <input type="hidden" name="page" value="transaksi">
        <input type="hidden" name="action" value="index">

        <div class="col-md-5">
            <label class="form-label fw-semibold small">Cari Nomor / Kasir</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" name="search" class="form-control border-start-0 ps-0"
                       placeholder="No. transaksi atau nama kasir..."
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                       style="border-radius:0 12px 12px 0;">
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold small">Filter Tanggal</label>
            <input type="date" name="tanggal" class="form-control"
                   value="<?= htmlspecialchars($_GET['tanggal'] ?? '') ?>"
                   style="border-radius:12px;">
        </div>

        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill" style="border-radius:12px; font-weight:600;">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <a href="/?page=transaksi&action=index" class="btn btn-outline-secondary" style="border-radius:12px;">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="card-section">
    <?php if (empty($transaksis)): ?>
        <div class="text-center py-5 text-secondary">
            <i class="bi bi-receipt display-4 d-block mb-3 opacity-50"></i>
            <p class="mb-0">Belum ada data transaksi</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                <thead>
                    <tr style="background:#f8fafc; color:#64748b; font-size:13px; font-weight:600;">
                        <th class="border-0 rounded-start ps-3 py-3">#</th>
                        <th class="border-0">No. Transaksi</th>
                        <th class="border-0">Kasir</th>
                        <th class="border-0">Tanggal</th>
                        <th class="border-0">Total</th>
                        <th class="border-0">Bayar</th>
                        <th class="border-0">Kembalian</th>
                        <th class="border-0">Status</th>
                        <th class="border-0 rounded-end pe-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaksis as $i => $t): ?>
                        <tr>
                            <td class="ps-3 text-secondary"><?= $i + 1 ?></td>
                            <td>
                                <span class="fw-semibold text-primary"><?= htmlspecialchars($t['no_trx']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($t['kasir']) ?></td>
                            <td class="text-secondary">
                                <?= date('d M Y, H:i', strtotime($t['tanggal_transaksi'])) ?>
                            </td>
                            <td class="fw-semibold">
                                Rp <?= number_format($t['total_harga'], 0, ',', '.') ?>
                            </td>
                            <td class="text-secondary">
                                Rp <?= number_format($t['bayar'], 0, ',', '.') ?>
                            </td>
                            <td class="text-success fw-semibold">
                                Rp <?= number_format($t['kembalian'], 0, ',', '.') ?>
                            </td>
                            <td>
                                <?php if ($t['status'] === 'Selesai'): ?>
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                        <i class="bi bi-check-circle me-1"></i>Selesai
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">
                                        <i class="bi bi-x-circle me-1"></i>Batal
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-3">
                                <a href="/?page=transaksi&action=detail&id=<?= $t['id'] ?>"
                                   class="btn btn-sm btn-outline-primary me-1"
                                   style="border-radius:8px;" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if ($t['status'] === 'Selesai'): ?>
                                    <a href="/?page=transaksi&action=batal&id=<?= $t['id'] ?>"
                                        class="btn btn-sm btn-outline-danger" style="border-radius:8px;" title="Batalkan"
                                        onclick="return confirm('Batalkan transaksi <?= htmlspecialchars($t['no_trx']) ?>? Stok akan dikembalikan.')">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="/?page=transaksi&action=hapus&id=<?= $t['id'] ?>
                                    "class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Hapus permanen riwayat transaksi ...? Tindakan ini tidak dapat dibatalkan.')">
                                    <i class="bi bi-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3 text-secondary small ps-2">
            Total: <strong><?= count($transaksis) ?></strong> transaksi
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>
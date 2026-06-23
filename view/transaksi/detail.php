<?php ob_start(); ?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h1 class="fw-bold">Detail Transaksi</h1>
        <p class="text-secondary mb-0">
            <span class="text-primary fw-semibold"><?= htmlspecialchars($transaksi['no_trx']) ?></span>
        </p>
    </div>
    <a href="/?page=transaksi&action=index" class="btn btn-outline-secondary" style="border-radius:12px;">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row g-4">
    <!-- Info Transaksi -->
    <div class="col-md-5">
        <div class="card-section h-100">
            <h6 class="fw-bold mb-4 text-secondary text-uppercase" style="font-size:12px; letter-spacing:.05em;">
                Informasi Transaksi
            </h6>

            <?php
            $rows = [
                ['label' => 'No. Transaksi', 'value' => $transaksi['no_trx'], 'bold' => true, 'color' => '#2563eb'],
                ['label' => 'Kasir', 'value' => $transaksi['kasir']],
                ['label' => 'Tanggal', 'value' => date('d M Y, H:i', strtotime($transaksi['tanggal_transaksi']))],
                ['label' => 'Keterangan', 'value' => $transaksi['keterangan'] ?: '-'],
            ];
            foreach ($rows as $r):
                ?>
                <div class="d-flex justify-content-between align-items-start py-3 border-bottom">
                    <span class="text-secondary" style="font-size:14px;"><?= $r['label'] ?></span>
                    <span style="font-size:14px; font-weight:<?= ($r['bold'] ?? false) ? '700' : '500' ?>;
                             color:<?= $r['color'] ?? '#1e293b' ?>; max-width:55%; text-align:right;">
                        <?= htmlspecialchars($r['value']) ?>
                    </span>
                </div>
            <?php endforeach; ?>

            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <span class="text-secondary" style="font-size:14px;">Status</span>
                <?php if ($transaksi['status'] === 'Selesai'): ?>
                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                        <i class="bi bi-check-circle me-1"></i>Selesai
                    </span>
                <?php else: ?>
                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </span>
                <?php endif; ?>
            </div>

            <!-- Ringkasan Pembayaran -->
            <div class="mt-4 p-4 rounded-4" style="background:#f8fafc;">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary" style="font-size:14px;">Total Belanja</span>
                    <span class="fw-bold" style="font-size:14px;">
                        Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?>
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary" style="font-size:14px;">Dibayar</span>
                    <span style="font-size:14px;">Rp <?= number_format($transaksi['bayar'], 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between pt-2 border-top">
                    <span class="fw-semibold text-success" style="font-size:14px;">Kembalian</span>
                    <span class="fw-bold text-success" style="font-size:15px;">
                        Rp <?= number_format($transaksi['kembalian'], 0, ',', '.') ?>
                    </span>
                </div>
            </div>

            <?php if ($transaksi['status'] === 'Selesai'): ?>
                <a href="/?page=transaksi&action=batal&id=<?= $transaksi['id'] ?>"
                    class="btn btn-outline-danger w-100 mt-4" style="border-radius:12px;"
                    onclick="return confirm('Batalkan transaksi ini? Stok akan dikembalikan.')">
                    <i class="bi bi-x-circle me-2"></i>Batalkan Transaksi
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Detail Item -->
    <div class="col-md-7">
        <div class="card-section">
            <h6 class="fw-bold mb-4 text-secondary text-uppercase" style="font-size:12px; letter-spacing:.05em;">
                Item yang Dibeli
            </h6>

            <?php if (empty($details)): ?>
                <div class="text-center py-4 text-secondary">
                    <i class="bi bi-bag display-5 d-block mb-2 opacity-50"></i>
                    <p>Tidak ada item</p>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($details as $d): ?>
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f8fafc;">
                            <div style="width:44px; height:44px; background:#dbeafe; border-radius:12px;
                                        display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="bi bi-box-seam text-primary"></i>
                            </div>
                            <div style="flex:1;">
                                <div class="fw-semibold" style="font-size:14px;">
                                    <?= htmlspecialchars($d['nama_barang']) ?>
                                </div>
                                <div class="text-secondary" style="font-size:12px;">
                                    <?= htmlspecialchars($d['kode_barang']) ?> &middot;
                                    <?= $d['qty'] ?>         <?= htmlspecialchars($d['satuan']) ?> &times;
                                    Rp <?= number_format($d['harga_satuan'], 0, ',', '.') ?>
                                </div>
                            </div>
                            <div class="fw-bold text-primary" style="font-size:15px;">
                                Rp <?= number_format($d['subtotal'], 0, ',', '.') ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Total -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <span class="fw-semibold">Total</span>
                    <span class="fw-bold" style="font-size:18px; color:#2563eb;">
                        Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>
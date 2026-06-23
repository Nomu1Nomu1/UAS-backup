<?php ob_start(); ?>

<div class="mb-4">
    <a href="?page=pengadaan&action=index"
       class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left"></i>
        Kembali ke Data Pengadaan
    </a>

    <h1 class="fw-bold mt-2">Detail Pengadaan</h1>

    <p class="text-secondary mb-0">
        Informasi lengkap pengadaan barang
    </p>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">

        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="text-muted small">No Pengadaan</label>
                <div class="fw-semibold">
                    <?= htmlspecialchars($pengadaan['no_pengadaan']) ?>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="text-muted small">Status</label>
                <div>
                    <span class="badge bg-primary">
                        <?= htmlspecialchars($pengadaan['status']) ?>
                    </span>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="text-muted small">Distributor</label>
                <div class="fw-semibold">
                    <?= htmlspecialchars($pengadaan['nama_distributor']) ?>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="text-muted small">Tanggal</label>
                <div class="fw-semibold">
                    <?= date('d/m/Y', strtotime($pengadaan['tanggal_pengadaan'])) ?>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="text-muted small">Petugas</label>
                <div class="fw-semibold">
                    <?= htmlspecialchars($pengadaan['petugas']) ?>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="text-muted small">Total Harga</label>
                <div class="fw-bold text-success">
                    Rp <?= number_format($pengadaan['total_harga'],0,',','.') ?>
                </div>
            </div>

        </div>

        <?php if (!empty($pengadaan['keterangan'])): ?>
            <hr>

            <label class="text-muted small">Keterangan</label>

            <div>
                <?= nl2br(htmlspecialchars($pengadaan['keterangan'])) ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<div class="card border-0 shadow-sm">

    <div class="card-body">

        <h5 class="fw-bold mb-3">
            Detail Produk
        </h5>

        <div class="table-responsive">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Satuan</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($details as $d): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($d['kode_barang']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($d['nama_barang']) ?>
                        </td>

                        <td>
                            <?= $d['qty'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($d['satuan']) ?>
                        </td>

                        <td>
                            Rp <?= number_format($d['harga_satuan'],0,',','.') ?>
                        </td>

                        <td>
                            Rp <?= number_format($d['subtotal'],0,',','.') ?>
                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Detail Pengadaan';
require_once __DIR__ . '/../layouts/main.php';
?>
<?php ob_start(); ?>

<div class="mb-4">
    <a href="<?= BASE_URL ?>/pengadaan/index" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left"></i> Kembali ke Data Pengadaan
    </a>

    <h1 class="fw-bold mt-2">Tambah Pengadaan</h1>
    <p class="text-secondary mb-0">
        Tambahkan data pengadaan barang dari distributor
    </p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="card-section">

    <form method="POST">

        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold small">
                    Distributor <span class="text-danger">*</span>
                </label>

                <select name="distributor_id"
                        class="form-select"
                        style="border-radius:12px;"
                        required>

                    <option value="">Pilih Distributor</option>

                    <?php foreach ($distributors as $d): ?>
                        <option value="<?= $d['id'] ?>">
                            <?= htmlspecialchars($d['nama_distributor']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold small">
                    Tanggal Pengadaan <span class="text-danger">*</span>
                </label>

                <input type="date"
                       name="tanggal_pengadaan"
                       class="form-control"
                       style="border-radius:12px;"
                       value="<?= date('Y-m-d') ?>"
                       required>
            </div>

        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold small">
                Keterangan
            </label>

            <textarea name="keterangan"
                      class="form-control"
                      rows="3"
                      style="border-radius:12px;"
                      placeholder="Catatan tambahan (opsional)"></textarea>
        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Daftar Produk</h5>

            <button type="button"
                    class="btn btn-success"
                    onclick="tambahBaris()">
                <i class="bi bi-plus-lg"></i>
                Tambah Produk
            </button>
        </div>

        <div class="table-responsive">

            <table class="table table-bordered align-middle" id="itemTable">

                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th width="120">Qty</th>
                        <th width="180">Harga Satuan</th>
                        <th width="180">Subtotal</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>

                        <td>
                            <select name="items[0][produk_id]"
                                    class="form-select produk-select">

                                <option value="">Pilih Produk</option>

                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>"
                                            data-harga="<?= $p['harga_beli'] ?>">
                                        <?= htmlspecialchars($p['kode_barang']) ?>
                                        -
                                        <?= htmlspecialchars($p['nama_barang']) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </td>

                        <td>
                            <input type="number"
                                   min="1"
                                   value="1"
                                   name="items[0][qty]"
                                   class="form-control qty"
                                   onchange="hitungSubtotal(this)">
                        </td>

                        <td>
                            <input type="number"
                                   min="0"
                                   name="items[0][harga_satuan]"
                                   class="form-control harga"
                                   onchange="hitungSubtotal(this)">
                        </td>

                        <td>
                            <input type="text"
                                   class="form-control subtotal"
                                   readonly>
                        </td>

                        <td>
                            <button type="button"
                                    class="btn btn-outline-danger"
                                    onclick="hapusBaris(this)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <div class="text-end mt-3">
            <h5>
                Total Pengadaan :
                Rp <span id="grandTotal">0</span>
            </h5>
        </div>

        <div class="d-flex gap-2 mt-4">

            <button type="submit"
                    class="btn btn-primary px-4"
                    style="border-radius:12px;font-weight:600;">
                <i class="bi bi-check-lg me-2"></i>
                Simpan
            </button>

            <a href="<?= BASE_URL ?>/?page=pengadaan&action=index"
               class="btn btn-outline-secondary px-4"
               style="border-radius:12px;">
                Batal
            </a>

        </div>

    </form>

</div>

<div class="mt-2 ms-1">
    <small class="text-secondary">
        <span class="text-danger">*</span> Wajib diisi
    </small>
</div>

<script>

let indexItem = 1;

function tambahBaris() {

    const tbody = document.querySelector('#itemTable tbody');

    const row = `
    <tr>

        <td>
            <select name="items[\${indexItem}][produk_id]"
                    class="form-select produk-select">

                <option value="">Pilih Produk</option>

                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>"
                            data-harga="<?= $p['harga_beli'] ?>">
                        <?= htmlspecialchars($p['kode_barang']) ?>
                        -
                        <?= htmlspecialchars($p['nama_barang']) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </td>

        <td>
            <input type="number"
                   min="1"
                   value="1"
                   name="items[\${indexItem}][qty]"
                   class="form-control qty"
                   onchange="hitungSubtotal(this)">
        </td>

        <td>
            <input type="number"
                   min="0"
                   name="items[\${indexItem}][harga_satuan]"
                   class="form-control harga"
                   onchange="hitungSubtotal(this)">
        </td>

        <td>
            <input type="text"
                   class="form-control subtotal"
                   readonly>
        </td>

        <td>
            <button type="button"
                    class="btn btn-outline-danger"
                    onclick="hapusBaris(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>

    </tr>
    `;

    tbody.insertAdjacentHTML('beforeend', row);

    indexItem++;
}

function hapusBaris(btn) {

    const rows = document.querySelectorAll('#itemTable tbody tr');

    if (rows.length > 1) {
        btn.closest('tr').remove();
        hitungTotal();
    } else {
        alert('Minimal harus ada satu produk.');
    }
}

function hitungSubtotal(el) {

    const row = el.closest('tr');

    const qty = parseFloat(
        row.querySelector('.qty').value || 0
    );

    const harga = parseFloat(
        row.querySelector('.harga').value || 0
    );

    const subtotal = qty * harga;

    row.querySelector('.subtotal').value = subtotal;

    hitungTotal();
}

function hitungTotal() {

    let total = 0;

    document.querySelectorAll('.subtotal').forEach(item => {
        total += parseFloat(item.value || 0);
    });

    document.getElementById('grandTotal').innerText =
        total.toLocaleString('id-ID');
}

document.addEventListener('change', function(e){

    if(e.target.classList.contains('produk-select')){

        const row = e.target.closest('tr');

        const option = e.target.options[e.target.selectedIndex];

        const harga = option.dataset.harga || 0;

        row.querySelector('.harga').value = harga;

        hitungSubtotal(row.querySelector('.harga'));
    }

});

</script>

<?php
$content = ob_get_clean();
$title = 'Tambah Pengadaan';
$pageTitle = 'Tambah Pengadaan';

require_once __DIR__ . '/../layouts/main.php';
?>
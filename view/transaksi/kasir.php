<?php ob_start(); ?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h1 class="fw-bold">Kasir / Transaksi</h1>
        <p class="text-secondary">Point of Sale sistem</p>
    </div>
    <a href="/?page=transaksi&action=index" class="btn btn-outline-secondary" style="border-radius:12px;">
        <i class="bi bi-list-ul me-1"></i> Riwayat Transaksi
    </a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-info alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-info-circle me-2"></i><?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<div class="kasir-layout">
    <!-- PRODUK PANEL -->
    <div>
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="Cari produk atau scan barcode...">
        </div>

        <div class="product-grid" id="productGrid">
            <?php foreach ($products as $p): ?>
                <?php
                    $fotoSrc = !empty($p['foto'])
                        ? '/uploads/produk/' . htmlspecialchars($p['foto'])
                        : null;
                ?>
                <div class="product-card <?= $p['stock'] <= 0 ? 'out-of-stock' : '' ?>" data-id="<?= $p['id'] ?>"
                    data-name="<?= htmlspecialchars($p['nama_barang']) ?>"
                    data-code="<?= htmlspecialchars($p['kode_barang']) ?>" data-price="<?= $p['harga_jual'] ?>"
                    data-stock="<?= $p['stock'] ?>" data-satuan="<?= htmlspecialchars($p['satuan']) ?>"
                    data-search="<?= strtolower(htmlspecialchars($p['nama_barang'] . ' ' . $p['kode_barang'])) ?>"
                    onclick="addToCart(this)">
                    <div class="product-img" style="overflow:hidden; background:#f1f5f9;">
                        <?php if ($fotoSrc): ?>
                            <img src="<?= $fotoSrc ?>"
                                 alt="<?= htmlspecialchars($p['nama_barang']) ?>"
                                 style="width:100%; height:100%; object-fit:cover; display:block;"
                                 loading="lazy">
                        <?php else: ?>
                            <i class="bi bi-box-seam" style="font-size:2rem; color:#94a3b8;"></i>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-name"><?= htmlspecialchars($p['nama_barang']) ?></div>
                        <div class="product-code"><?= htmlspecialchars($p['kode_barang']) ?></div>
                        <div class="product-footer">
                            <span class="product-price">
                                Rp <?= number_format($p['harga_jual'] / 1000, 0, ',', '.') ?>k
                            </span>
                            <span class="product-stock">Stok: <?= $p['stock'] ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- CART PANEL -->
    <div class="cart-panel">
        <div class="cart-header">
            <i class="bi bi-cart3"></i>
            <h5>Keranjang</h5>
            <span class="cart-badge" id="cartCount">0</span>
        </div>

        <div id="cartEmpty" class="cart-empty">
            <i class="bi bi-cart3"></i>
            <p>Keranjang kosong</p>
        </div>

        <div id="cartItems" class="cart-items" style="display:none;"></div>

        <div id="cartSummary" class="cart-summary" style="display:none;">
            <div class="summary-row"><span>Subtotal</span><span id="summarySubtotal">Rp 0</span></div>
            <div class="summary-row total"><span>Total</span><span id="summaryTotal">Rp 0</span></div>
        </div>

        <div id="cartPayment" class="cart-payment" style="display:none;">
            <span class="payment-label">Jumlah Bayar</span>
            <input type="number" class="payment-input" id="bayarInput" placeholder="Masukkan nominal..." min="0"
                oninput="hitungKembalian()">

            <div class="kembalian-box">
                <span class="kembalian-label"><i class="bi bi-arrow-left-right me-1"></i>Kembalian</span>
                <span class="kembalian-val" id="kembalianVal">Rp 0</span>
            </div>

            <textarea class="keterangan-input" id="keteranganInput" rows="2"
                placeholder="Keterangan (opsional)..."></textarea>

            <button class="btn-bayar" id="btnBayar" onclick="submitTransaksi()" disabled>
                <i class="bi bi-bag-check me-2"></i>Proses Pembayaran
            </button>
        </div>
    </div>
</div>

<!-- Hidden form for submit -->
<form id="trxForm" action="/?page=transaksi&action=create" method="POST" style="display:none;">
    <input type="hidden" name="bayar" id="formBayar">
    <input type="hidden" name="keterangan" id="formKeterangan">
    <div id="formItems"></div>
</form>

<script src="/assets/js/kasir.js"></script>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>
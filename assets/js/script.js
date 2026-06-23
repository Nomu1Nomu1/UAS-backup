"use strict";

/* ── Sidebar Toggle (mobile) ─────────────────────────────── */
(function () {
  const toggle = document.getElementById("sidebarToggle");
  const closeBtn = document.getElementById("sidebarClose");
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebarOverlay");
  if (!sidebar) return;

  function openSidebar() {
    sidebar.classList.add("open");
    if (overlay) overlay.classList.add("active");
    document.body.style.overflow = "hidden";
  }

  function closeSidebar() {
    sidebar.classList.remove("open");
    if (overlay) overlay.classList.remove("active");
    document.body.style.overflow = "";
  }

  if (toggle) {
    toggle.addEventListener("click", () => {
      sidebar.classList.contains("open") ? closeSidebar() : openSidebar();
    });
  }

  if (closeBtn) {
    closeBtn.addEventListener("click", closeSidebar);
  }

  if (overlay) {
    overlay.addEventListener("click", closeSidebar);
  }

  // Tutup sidebar saat navigasi (link diklik) di mobile
  sidebar.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      if (window.innerWidth <= 768) closeSidebar();
    });
  });

  // Reset saat resize ke desktop
  window.addEventListener("resize", () => {
    if (window.innerWidth > 768) {
      sidebar.classList.remove("open");
      if (overlay) overlay.classList.remove("active");
      document.body.style.overflow = "";
    }
  });
})();

/* ── Auto-dismiss flash alerts ───────────────────────────── */
(function () {
  const alerts = document.querySelectorAll(".alert.alert-dismissible");
  alerts.forEach((el) => {
    setTimeout(() => {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
      bsAlert.close();
    }, 1000);
  });
})();

/* ── Format Rupiah (utility) ─────────────────────────────── */
function formatRupiah(angka) {
  return "Rp " + Number(angka).toLocaleString("id-ID");
}

/* ── Confirm Delete ──────────────────────────────────────── */
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("[data-confirm]").forEach((el) => {
    el.addEventListener("click", (e) => {
      const msg = el.dataset.confirm || "Apakah Anda yakin?";
      if (!confirm(msg)) e.preventDefault();
    });
  });
});

/* ── Kasir / POS Logic ───────────────────────────────────── */
(function () {
  const cartBody = document.getElementById("cartBody");
  const totalEl = document.getElementById("cartTotal");
  const bayarInput = document.getElementById("bayarInput");
  const kembalianEl = document.getElementById("kembalianEl");
  const cartCount = document.getElementById("cartCount");
  const itemsInput = document.getElementById("itemsHidden");

  if (!cartBody) return; // not on kasir page

  let cart = {}; // { produkId: { nama, harga, qty, satuan } }

  // Add product from tile click
  document.querySelectorAll(".product-tile").forEach((tile) => {
    tile.addEventListener("click", () => {
      const id = tile.dataset.id;
      const nama = tile.dataset.nama;
      const harga = parseFloat(tile.dataset.harga);
      const stok = parseInt(tile.dataset.stok, 10);
      const satuan = tile.dataset.satuan;

      if (cart[id]) {
        if (cart[id].qty >= stok) {
          alert("Stok tidak mencukupi!");
          return;
        }
        cart[id].qty++;
      } else {
        cart[id] = { nama, harga, qty: 1, satuan, stok };
      }
      renderCart();
    });
  });

  function renderCart() {
    cartBody.innerHTML = "";
    let total = 0;
    let count = 0;

    Object.entries(cart).forEach(([id, item]) => {
      const subtotal = item.harga * item.qty;
      total += subtotal;
      count += item.qty;

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>
          <div class="fw-600" style="font-size:12px">${item.nama}</div>
          <div class="text-muted" style="font-size:11px">${formatRupiah(item.harga)} / ${item.satuan}</div>
        </td>
        <td style="width:90px">
          <div class="d-flex align-items-center gap-1">
            <button class="btn btn-outline-secondary btn-icon btn-sm qty-minus" data-id="${id}">−</button>
            <span class="px-1 fw-600">${item.qty}</span>
            <button class="btn btn-outline-secondary btn-icon btn-sm qty-plus" data-id="${id}" ${item.qty >= item.stok ? "disabled" : ""}>+</button>
          </div>
        </td>
        <td class="text-end fw-600" style="font-size:12px">${formatRupiah(subtotal)}</td>
        <td>
          <button class="btn btn-link text-danger btn-sm p-0 remove-item" data-id="${id}">
            <i class="bi bi-trash3"></i>
          </button>
        </td>
      `;
      cartBody.appendChild(tr);
    });

    // Empty state
    if (Object.keys(cart).length === 0) {
      cartBody.innerHTML = `
        <tr>
          <td colspan="4" class="text-center text-muted py-4" style="font-size:12px">
            <i class="bi bi-cart3 d-block mb-1" style="font-size:24px;opacity:.3"></i>
            Keranjang kosong
          </td>
        </tr>`;
    }

    if (totalEl) totalEl.textContent = formatRupiah(total);
    if (cartCount) cartCount.textContent = count > 0 ? count : "";
    updateKembalian(total);
    buildHiddenInputs(total);
  }

  function updateKembalian(total) {
    if (!bayarInput || !kembalianEl) return;
    const bayar = parseFloat(bayarInput.value.replace(/\D/g, "")) || 0;
    const kembalian = bayar - total;
    kembalianEl.textContent = formatRupiah(Math.max(0, kembalian));
    kembalianEl.classList.toggle("text-danger", kembalian < 0);
    kembalianEl.classList.toggle("text-success", kembalian >= 0);
  }

  function buildHiddenInputs(total) {
    if (!itemsInput) return;
    // Remove old hidden item inputs
    document.querySelectorAll(".cart-item-input").forEach((el) => el.remove());

    const form = document.getElementById("kasirForm");
    if (!form) return;

    Object.entries(cart).forEach(([id, item], idx) => {
      const fields = { produk_id: id, qty: item.qty, harga_satuan: item.harga };
      Object.entries(fields).forEach(([key, val]) => {
        const inp = document.createElement("input");
        inp.type = "hidden";
        inp.name = `items[${idx}][${key}]`;
        inp.value = val;
        inp.className = "cart-item-input";
        form.appendChild(inp);
      });
    });

    // Store total so bayar validation works
    if (itemsInput) itemsInput.value = total;
  }

  // Qty & remove buttons (delegated)
  cartBody.addEventListener("click", (e) => {
    const plus = e.target.closest(".qty-plus");
    const minus = e.target.closest(".qty-minus");
    const remove = e.target.closest(".remove-item");

    if (plus) {
      const id = plus.dataset.id;
      if (cart[id] && cart[id].qty < cart[id].stok) cart[id].qty++;
      renderCart();
    }
    if (minus) {
      const id = minus.dataset.id;
      if (cart[id]) {
        cart[id].qty--;
        if (cart[id].qty <= 0) delete cart[id];
      }
      renderCart();
    }
    if (remove) {
      delete cart[remove.dataset.id];
      renderCart();
    }
  });

  // Bayar input — live kembalian update
  if (bayarInput) {
    bayarInput.addEventListener("input", () => {
      const total = parseFloat(itemsInput?.value || "0");
      updateKembalian(total);
    });
  }

  // Prevent submit if cart empty
  const kasirForm = document.getElementById("kasirForm");
  if (kasirForm) {
    kasirForm.addEventListener("submit", (e) => {
      if (Object.keys(cart).length === 0) {
        e.preventDefault();
        alert("Tambahkan produk ke keranjang terlebih dahulu.");
      }
    });
  }

  renderCart(); // init
})();

/* ── Dynamic Pengadaan Item Rows ──────────────────────────── */
(function () {
  const container = document.getElementById("itemContainer");
  const addBtn = document.getElementById("addItemBtn");
  if (!container || !addBtn) return;

  let rowIndex = container.querySelectorAll(".item-row").length;

  addBtn.addEventListener("click", () => {
    const template = document.getElementById("itemRowTemplate");
    if (!template) return;
    const clone = template.content.cloneNode(true);
    // Update name attributes with current index
    clone.querySelectorAll("[data-name]").forEach((el) => {
      el.name = el.dataset.name.replace("__IDX__", rowIndex);
    });
    container.appendChild(clone);
    rowIndex++;
    bindRowEvents();
    updateRowNumbers();
  });

  function bindRowEvents() {
    container.querySelectorAll(".remove-row").forEach((btn) => {
      btn.onclick = () => {
        if (container.querySelectorAll(".item-row").length <= 1) return;
        btn.closest(".item-row").remove();
        updateRowNumbers();
        recalcTotal();
      };
    });

    container.querySelectorAll(".item-qty, .item-harga").forEach((el) => {
      el.oninput = recalcTotal;
    });

    container.querySelectorAll(".item-produk").forEach((sel) => {
      sel.onchange = function () {
        const opt = sel.options[sel.selectedIndex];
        const row = sel.closest(".item-row");
        const hEl = row?.querySelector(".item-harga");
        if (hEl && opt.dataset.harga) hEl.value = opt.dataset.harga;
        recalcTotal();
      };
    });
  }

  function recalcTotal() {
    let total = 0;
    container.querySelectorAll(".item-row").forEach((row) => {
      const qty = parseFloat(row.querySelector(".item-qty")?.value || 0);
      const harga = parseFloat(row.querySelector(".item-harga")?.value || 0);
      const sub = qty * harga;
      const subEl = row.querySelector(".item-subtotal");
      if (subEl) subEl.textContent = formatRupiah(sub);
      total += sub;
    });
    const totalEl = document.getElementById("grandTotal");
    if (totalEl) totalEl.textContent = formatRupiah(total);
  }

  function updateRowNumbers() {
    container.querySelectorAll(".item-row").forEach((row, i) => {
      const num = row.querySelector(".row-num");
      if (num) num.textContent = i + 1;
    });
  }

  bindRowEvents();
})();

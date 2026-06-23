const cart = {};

function formatRp(num) {
  return "Rp " + parseInt(num).toLocaleString("id-ID");
}

function addToCart(card) {
  const id = card.dataset.id;
  const name = card.dataset.name;
  const price = parseFloat(card.dataset.price);
  const stock = parseInt(card.dataset.stock);
  const code = card.dataset.code;

  if (cart[id]) {
    if (cart[id].qty >= stock) {
      alert("Stok tidak cukup!");
      return;
    }
    cart[id].qty++;
  } else {
    cart[id] = { id, name, price, stock, code, qty: 1 };
  }
  renderCart();
}

function changeQty(id, delta) {
  if (!cart[id]) return;
  cart[id].qty += delta;
  if (cart[id].qty <= 0) delete cart[id];
  else if (cart[id].qty > cart[id].stock) cart[id].qty = cart[id].stock;
  renderCart();
}

function removeFromCart(id) {
  delete cart[id];
  renderCart();
}

function renderCart() {
  const keys = Object.keys(cart);
  const count = keys.reduce((s, k) => s + cart[k].qty, 0);
  const total = keys.reduce((s, k) => s + cart[k].qty * cart[k].price, 0);

  document.getElementById("cartCount").textContent = count;

  if (keys.length === 0) {
    document.getElementById("cartEmpty").style.display = "";
    document.getElementById("cartItems").style.display = "none";
    document.getElementById("cartSummary").style.display = "none";
    document.getElementById("cartPayment").style.display = "none";
    return;
  }

  document.getElementById("cartEmpty").style.display = "none";
  document.getElementById("cartItems").style.display = "";
  document.getElementById("cartSummary").style.display = "";
  document.getElementById("cartPayment").style.display = "";

  let html = "";
  keys.forEach((id) => {
    const item = cart[id];
    html += `
        <div class="cart-item">
            <div style="flex:1">
                <div class="cart-item-name">${item.name}</div>
                <span class="cart-item-price">${formatRp(item.price)} / satuan</span>
            </div>
            <div class="qty-control">
                <button class="qty-btn" onclick="changeQty('${id}', -1)">−</button>
                <span class="qty-num">${item.qty}</span>
                <button class="qty-btn" onclick="changeQty('${id}', 1)">+</button>
            </div>
            <button class="cart-item-del" onclick="removeFromCart('${id}')">
                <i class="bi bi-trash3"></i>
            </button>
        </div>`;
  });
  document.getElementById("cartItems").innerHTML = html;

  document.getElementById("summarySubtotal").textContent = formatRp(total);
  document.getElementById("summaryTotal").textContent = formatRp(total);

  hitungKembalian();
}

function hitungKembalian() {
  const total = Object.keys(cart).reduce(
    (s, k) => s + cart[k].qty * cart[k].price,
    0,
  );
  const bayar = parseFloat(document.getElementById("bayarInput").value) || 0;
  const kembalian = bayar - total;
  const el = document.getElementById("kembalianVal");

  if (bayar >= total && total > 0) {
    el.textContent = formatRp(kembalian);
    el.style.color = "#16a34a";
    document.getElementById("btnBayar").disabled = false;
  } else {
    el.textContent = "Rp 0";
    el.style.color = "#ef4444";
    document.getElementById("btnBayar").disabled = true;
  }
}

function submitTransaksi() {
  const bayar = parseFloat(document.getElementById("bayarInput").value) || 0;
  const keterangan = document.getElementById("keteranganInput").value;

  document.getElementById("formBayar").value = bayar;
  document.getElementById("formKeterangan").value = keterangan;

  let itemsHtml = "";
  Object.keys(cart).forEach((id, i) => {
    const item = cart[id];
    itemsHtml += `
            <input type="hidden" name="items[${i}][produk_id]"    value="${item.id}">
            <input type="hidden" name="items[${i}][qty]"          value="${item.qty}">
            <input type="hidden" name="items[${i}][harga_satuan]" value="${item.price}">
        `;
  });
  document.getElementById("formItems").innerHTML = itemsHtml;
  document.getElementById("trxForm").submit();
}

document.getElementById("searchInput").addEventListener("input", function () {
  const q = this.value.toLowerCase().trim();
  document.querySelectorAll(".product-card").forEach((card) => {
    card.classList.toggle(
      "hidden",
      q !== "" && !card.dataset.search.includes(q),
    );
  });
});

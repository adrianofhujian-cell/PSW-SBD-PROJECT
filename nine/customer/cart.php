<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart — Larris</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="theme-beige">
<div class="page">

  <?php $active='cart'; include '_nav.php'; ?>

  <section class="cart-wrap">
    <h1>CART</h1>

    <div id="cart-list"></div>

    <div id="cart-empty" class="cart-empty" style="display:none">
      <p style="font-size:1.1rem">Keranjang kamu masih kosong 🛒</p>
      <a href="menu.php">Lihat Menu</a>
    </div>

    <div id="cart-body">
      <a href="menu.php" class="add-product">Add Product</a>

      <div class="cart-notes">
        <label for="catatan">Notes:</label>
        <textarea id="catatan" placeholder="Contoh: tidak pakai pedas, pisahkan kuah..."></textarea>
      </div>

      <div class="cart-notes" style="margin-top:14px">
        <label for="nama">Nama Kamu:</label>
        <textarea id="nama" style="min-height:auto;height:46px" placeholder="Masukkan nama untuk pesanan..."></textarea>
      </div>

      <div class="subtotal">
        <h3>Subtotal:</h3>
        <h3 id="subtotal">Rp 0</h3>
      </div>
    </div>

    <button class="checkout-bar" id="btn-checkout" onclick="prosesCheckout()">Checkout</button>
  </section>

  <?php include '_footer.php'; ?>
</div>

<script>
let cart = JSON.parse(localStorage.getItem('cartItems') || '[]');
if (!Array.isArray(cart)) cart = [];

const rupiah = n => 'Rp ' + (n||0).toLocaleString('id-ID');

function render() {
  const list  = document.getElementById('cart-list');
  const empty = document.getElementById('cart-empty');
  const body  = document.getElementById('cart-body');
  const btn   = document.getElementById('btn-checkout');

  if (cart.length === 0) {
    list.innerHTML = '';
    empty.style.display = 'block';
    body.style.display  = 'none';
    btn.style.display   = 'none';
    return;
  }
  empty.style.display = 'none';
  body.style.display  = 'block';
  btn.style.display   = 'block';

  let html = '', total = 0;
  cart.forEach((it, i) => {
    const line = (it.price || 0) * (it.qty || 1);
    total += line;
    const meta = [it.variant].concat(it.addons && it.addons.length ? it.addons : [])
                  .filter(Boolean).join(' · ');
    html += `
      <div class="cart-item">
        <div class="thumb"><img src="${it.image}" alt=""></div>
        <div>
          <h2>${it.food}</h2>
          <div class="meta">${meta}<br>${rupiah(it.price)} / pcs</div>
          <button class="remove" onclick="hapus(${i})">remove</button>
        </div>
        <div class="qty">
          <button onclick="ubah(${i},1)">+</button>
          <span>${it.qty || 1}</span>
          <button onclick="ubah(${i},-1)">-</button>
        </div>
        <div class="line-price">${rupiah(line)}</div>
      </div>`;
  });
  list.innerHTML = html;
  document.getElementById('subtotal').textContent = rupiah(total);
}

function simpan(){ localStorage.setItem('cartItems', JSON.stringify(cart)); }
function hapus(i){ cart.splice(i,1); simpan(); render(); }
function ubah(i,d){ cart[i].qty = (cart[i].qty||1)+d; if(cart[i].qty<1)cart[i].qty=1; simpan(); render(); }

async function prosesCheckout(){
  if (cart.length === 0){ alert('Keranjang kamu kosong!'); return; }
  const nama = document.getElementById('nama').value.trim();
  if (!nama){ alert('Isi nama kamu dulu ya!'); document.getElementById('nama').focus(); return; }

  const catatan = document.getElementById('catatan').value;
  let total = 0;
  cart.forEach(it => total += (it.price||0)*(it.qty||1));

  const btn = document.getElementById('btn-checkout');
  btn.disabled = true; btn.textContent = 'Memproses...';

  try {
    const res = await fetch('proses_checkout.php', {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body: JSON.stringify({ nama_pelanggan:nama, total, catatan, items:cart })
    });
    const data = await res.json();
    if (data.sukses){
      localStorage.removeItem('cartItems');
      window.location.href = 'selesai.php?id=' + data.id_pesanan;
    } else {
      alert('Gagal: ' + (data.pesan || 'coba lagi'));
      btn.disabled = false; btn.textContent = 'Checkout';
    }
  } catch(e){
    alert('Tidak bisa terhubung ke server. Pastikan database aktif.');
    btn.disabled = false; btn.textContent = 'Checkout';
  }
}

render();
</script>
</body>
</html>

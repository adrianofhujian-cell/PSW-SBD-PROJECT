<?php
require 'menu_data.php';
$MENU = larris_menu();

$slug = $_GET['food'] ?? '';
$item = $MENU[$slug] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $item ? htmlspecialchars($item['nama']) : 'Menu' ?> — Larris</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">

  <?php $active='menu'; include '_nav.php'; ?>

  <a href="menu.php" class="back">&larr;</a>

  <?php if (!$item): ?>
    <section class="detail-wrap">
      <p style="text-align:center;margin-top:60px">
        Menu tidak ditemukan. <a href="menu.php" style="text-decoration:underline">Kembali ke Menu</a>
      </p>
    </section>
  <?php else: ?>

  <section class="detail-wrap">
    <div class="detail-grid">

      <div class="detail-photo">
        <img src="<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama']) ?>">
      </div>

      <div class="detail-main">
        <h1><?= htmlspecialchars($item['nama']) ?></h1>

        <?php foreach ($item['sections'] as $si => $sec): ?>

          <?php if (!empty($sec['title'])): ?>
            <div class="section-title"><?= htmlspecialchars($sec['title']) ?>:</div>
          <?php endif; ?>

          <?php if ($sec['type'] === 'combo'): ?>
            <div class="combo-hint">Pilih maksimal <?= (int)$sec['max'] ?> item untuk paket ini.</div>
            <div class="opt-list opt-grid">
              <?php foreach ($sec['items'] as $ii => $opt): ?>
                <div class="opt" data-kind="combo" data-section="<?= $si ?>"
                     data-max="<?= (int)$sec['max'] ?>"
                     data-nama="<?= htmlspecialchars($opt['nama'], ENT_QUOTES) ?>"
                     data-harga="<?= (int)$opt['harga'] ?>">
                  <span class="label"><?= htmlspecialchars($opt['nama']) ?></span>
                  <span class="box">&#10003;</span>
                </div>
              <?php endforeach; ?>
            </div>

          <?php else: /* variant atau addon */ ?>
            <div class="opt-list">
              <?php foreach ($sec['items'] as $ii => $opt): ?>
                <div class="opt"
                     data-kind="<?= $sec['type'] ?>"
                     data-section="<?= $si ?>"
                     data-nama="<?= htmlspecialchars($opt['nama'], ENT_QUOTES) ?>"
                     data-harga="<?= (int)$opt['harga'] ?>">
                  <span class="label"><?= htmlspecialchars($opt['nama']) ?></span>
                  <span class="price"><?= number_format($opt['harga'], 0, ',', '.') ?></span>
                  <span class="box">&#10003;</span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

        <?php endforeach; ?>

        <div class="detail-actions">
          <button class="btn-checkout" id="addBtn">Add to Cart</button>
        </div>
      </div>
    </div>
  </section>

  <script>
  const FOOD  = <?= json_encode($item['nama']) ?>;
  const IMAGE = <?= json_encode($item['gambar']) ?>;

  // ── PEMILIHAN ──────────────────────────────────────────
  document.querySelectorAll('.opt').forEach(el => {
    el.addEventListener('click', () => {
      const kind = el.dataset.kind;

      if (kind === 'variant') {
        // radio: hanya satu varian boleh aktif (lintas semua section variant)
        document.querySelectorAll('.opt[data-kind="variant"]')
          .forEach(o => o.classList.remove('active'));
        el.classList.add('active');
      }
      else if (kind === 'addon') {
        el.classList.toggle('active');
      }
      else if (kind === 'combo') {
        const sec = el.dataset.section;
        const max = parseInt(el.dataset.max);
        const chosen = document.querySelectorAll(
          '.opt[data-kind="combo"][data-section="'+sec+'"].active');
        if (!el.classList.contains('active') && chosen.length >= max) {
          alert('Maksimal ' + max + ' item untuk paket ini.');
          return;
        }
        el.classList.toggle('active');
      }
    });
  });

  // ── ADD TO CART ────────────────────────────────────────
  document.getElementById('addBtn').addEventListener('click', () => {
    const variant = document.querySelector('.opt[data-kind="variant"].active');
    if (!variant) { alert('Pilih varian dulu ya!'); return; }

    let harga = parseInt(variant.dataset.harga) || 0;
    let extras = [];

    document.querySelectorAll('.opt[data-kind="addon"].active').forEach(a => {
      extras.push(a.dataset.nama);
      harga += parseInt(a.dataset.harga) || 0;
    });
    document.querySelectorAll('.opt[data-kind="combo"].active').forEach(c => {
      extras.push(c.dataset.nama);
      harga += parseInt(c.dataset.harga) || 0;
    });

    const cartItem = {
      food:    FOOD,
      variant: variant.dataset.nama,
      addons:  extras,
      price:   harga,                 // angka murni (rupiah)
      image:   IMAGE,
      qty:     1
    };

    let cart = JSON.parse(localStorage.getItem('cartItems') || '[]');
    if (!Array.isArray(cart)) cart = [];

    // gabung kalau item identik
    const same = JSON.stringify({f:cartItem.food,v:cartItem.variant,a:cartItem.addons});
    const idx = cart.findIndex(c =>
      JSON.stringify({f:c.food,v:c.variant,a:c.addons}) === same);
    if (idx >= 0) cart[idx].qty += 1;
    else cart.push(cartItem);

    localStorage.setItem('cartItems', JSON.stringify(cart));

    const btn = document.getElementById('addBtn');
    btn.textContent = '✓ Ditambahkan!';
    btn.classList.add('ok');
    setTimeout(() => { btn.textContent = 'Add to Cart'; btn.classList.remove('ok'); }, 1400);
  });
  </script>

  <?php endif; ?>

  <?php include '_footer.php'; ?>
</div>
</body>
</html>

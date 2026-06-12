<?php $id = isset($_GET['id']) ? (int)$_GET['id'] : 0; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Completed — Larris</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="theme-beige">
<div class="page">

  <a href="menu.php" class="back" style="margin-top:18px">&larr;</a>

  <section class="done-wrap">
    <svg class="done-check" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
      <path d="M12 1.5l2.3 1.9 3-.3 1.2 2.8 2.8 1.2-.3 3L23.5 12l-1.9 2.3.3 3-2.8 1.2-1.2 2.8-3-.3L12 22.5l-2.3-1.9-3 .3-1.2-2.8-2.8-1.2.3-3L1.5 12l1.9-2.3-.3-3 2.8-1.2L7.1 2.7l3 .3L12 1.5z"/>
      <path d="M10.6 15.6l-3-3 1.4-1.4 1.6 1.6 4-4 1.4 1.4-5.4 5.4z" fill="#F1E2D1"/>
    </svg>

    <h1>ORDER COMPLETED</h1>
    <p>Silahkan pickup dalam waktu 15 menit di kantin UIB lt.2</p>
    <?php if ($id): ?>
      <p class="order-no">No. Pesanan: #<?= $id ?></p>
    <?php endif; ?>

    <a href="menu.php"><button class="btn-order">Pesan Lagi</button></a>
  </section>

  <?php include '_footer.php'; ?>
</div>
</body>
</html>

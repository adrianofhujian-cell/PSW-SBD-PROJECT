<?php require 'menu_data.php'; $MENU = larris_menu(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu — Larris</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">

  <?php $active='menu'; include '_nav.php'; ?>

  <a href="index.php" class="back">&larr;</a>

  <section class="menu-wrap">
    <h1 class="menu-title">MENU</h1>

    <div class="menu-grid">
      <?php foreach ($MENU as $slug => $item): ?>
        <a href="detail.php?food=<?= urlencode($slug) ?>" class="menu-card">
          <img src="<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama']) ?>">
          <div class="cap"><?= htmlspecialchars($item['nama']) ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <?php include '_footer.php'; ?>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Larris — Campus Canteen</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="theme-beige">
<div class="page">

  <?php $active='home'; include '_nav.php'; ?>

  <section class="home">
    <div class="hero-circle">
      <img src="images/hero.jpg" alt="">
    </div>
    <div class="home-text">
      <h1>Larris</h1>
      <p>Delicious traditional food with modern taste</p>
      <a href="menu.php"><button class="btn-order">Order Now</button></a>
    </div>
  </section>

  <?php include '_footer.php'; ?>
</div>
</body>
</html>

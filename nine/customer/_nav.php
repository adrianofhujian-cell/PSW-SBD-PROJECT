<?php
/* _nav.php — navbar bersama.
   Pakai: $active = 'home'|'menu'|'about'|'cart';  include '_nav.php'; */
if (!isset($active)) $active = '';
?>
<nav class="navbar">
  <a href="index.php" class="brand">
    <img src="images/logo.jpg" alt="Larris">
    <span class="logo">Larris</span>
  </a>
  <ul class="nav-links">
    <li><a href="index.php"  class="<?= $active==='home' ?'active':'' ?>">Home</a></li>
    <li><a href="menu.php"   class="<?= $active==='menu' ?'active':'' ?>">Menu</a></li>
    <li><a href="About.php"  class="<?= $active==='about'?'active':'' ?>">About</a></li>
    <li><a href="cart.php"   class="<?= $active==='cart' ?'active':'' ?>">Cart</a></li>
  </ul>
  <a href="login.php" class="nav-user" title="Akun">&#128100;</a>
</nav>

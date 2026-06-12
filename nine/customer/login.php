<?php
session_start();
include "../Config/config.php";

$mode  = $_GET['mode'] ?? 'register';   // register | login
$pesan = '';

/* daftar kolom tabel pengguna yang benar-benar tersedia
   (supaya kode tetap jalan walau kolom npm/major/no_hp belum dibuat) */
$kolom = [];
$rk = mysqli_query($koneksi, "SHOW COLUMNS FROM pengguna");
if ($rk) while ($c = mysqli_fetch_assoc($rk)) $kolom[] = $c['Field'];
function punya($k){ global $kolom; return in_array($k, $kolom); }

/* ---------- REGISTER ---------- */
if (isset($_POST['register'])) {
  $nama  = mysqli_real_escape_string($koneksi, trim($_POST['nama'] ?? ''));
  $npm   = mysqli_real_escape_string($koneksi, trim($_POST['npm'] ?? ''));
  $major = mysqli_real_escape_string($koneksi, trim($_POST['major'] ?? ''));
  $hp    = mysqli_real_escape_string($koneksi, trim($_POST['hp'] ?? ''));
  $pw    = mysqli_real_escape_string($koneksi, $_POST['password'] ?? '');

  if (!$nama || !$npm || !$pw) {
    $pesan = "Nama, NPM, dan Password wajib diisi.";
  } else {
    // cek NPM (username) sudah terdaftar?
    $cek = mysqli_query($koneksi, "SELECT id_pengguna FROM pengguna WHERE username='$npm'");
    if ($cek && mysqli_num_rows($cek) > 0) {
      $pesan = "NPM ini sudah terdaftar. Silakan login.";
    } else {
      // bangun INSERT sesuai kolom yang ada
      $cols = ['nama','username','password','role','status'];
      $vals = ["'$nama'","'$npm'","'$pw'","'Customer'","'aktif'"];
      if (punya('email')) { $cols[]='email';  $vals[]="'".($hp ?: $npm)."'"; }
      if (punya('npm'))   { $cols[]='npm';    $vals[]="'$npm'"; }
      if (punya('major')) { $cols[]='major';  $vals[]="'$major'"; }
      if (punya('no_hp')) { $cols[]='no_hp';  $vals[]="'$hp'"; }
      $sql = "INSERT INTO pengguna (".implode(',', $cols).") VALUES (".implode(',', $vals).")";
      if (mysqli_query($koneksi, $sql)) {
        $_SESSION['customer'] = $nama;
        header("Location: menu.php"); exit;
      } else {
        $pesan = "Gagal mendaftar: " . mysqli_error($koneksi);
      }
    }
  }
}

/* ---------- LOGIN ---------- */
if (isset($_POST['login'])) {
  $npm = mysqli_real_escape_string($koneksi, trim($_POST['npm'] ?? ''));
  $pw  = mysqli_real_escape_string($koneksi, $_POST['password'] ?? '');
  $q = mysqli_query($koneksi,
    "SELECT * FROM pengguna WHERE username='$npm' AND password='$pw'");
  if ($q && mysqli_num_rows($q) > 0) {
    $u = mysqli_fetch_assoc($q);
    $_SESSION['customer'] = $u['nama'];
    header("Location: menu.php"); exit;
  } else {
    $pesan = "NPM atau Password salah.";
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $mode==='login' ? 'Login' : 'Daftar' ?> — Larris</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="auth">

    <div class="auth-left">
      <div class="circle"><img src="images/logo.jpg" alt="Larris"></div>
      <h2>Larris</h2>
    </div>

    <div class="auth-right">
      <div class="mark">
        <img src="images/logo_mark.png" alt="">
        <h1>Larris</h1>
      </div>
      <div class="sub"><?= $mode==='login'
            ? 'Masuk ke akun anda untuk melanjutkan'
            : 'Buat akun untuk mulai memesan' ?></div>

      <?php if ($pesan): ?>
        <div style="background:#fff;color:#810B38;padding:10px 14px;border-radius:10px;margin-bottom:14px;font-size:.88rem;text-align:center">
          <?= htmlspecialchars($pesan) ?>
        </div>
      <?php endif; ?>

      <?php if ($mode === 'login'): ?>
        <!-- ===== LOGIN ===== -->
        <form method="post">
          <div class="field">
            <label>NPM</label>
            <input type="text" name="npm" placeholder="2531245" required>
          </div>
          <div class="field">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
          </div>
          <div class="auth-row">
            <label><input type="checkbox"> Ingat saya</label>
            <a href="#">Lupa password?</a>
          </div>
          <button class="btn-login" type="submit" name="login">Login</button>
        </form>
        <div class="auth-switch">Belum punya akun?
          <a href="login.php?mode=register">Daftar</a></div>

      <?php else: ?>
        <!-- ===== REGISTER ===== -->
        <form method="post">
          <div class="field">
            <label>Name</label>
            <input type="text" name="nama" placeholder="Azlea Fryn" required>
          </div>
          <div class="field">
            <label>NPM</label>
            <input type="text" name="npm" placeholder="2531245" required>
          </div>
          <div class="field">
            <label>Major</label>
            <input type="text" name="major" placeholder="Information System">
          </div>
          <div class="field">
            <label>Phone Number</label>
            <input type="text" name="hp" placeholder="0897xxx">
          </div>
          <div class="field">
            <label>Password</label>
            <input type="password" name="password" placeholder="Azlea23" required>
          </div>
          <div class="auth-row">
            <label><input type="checkbox"> Ingat saya</label>
            <a href="#">Lupa password?</a>
          </div>
          <button class="btn-login" type="submit" name="register">Login</button>
        </form>
        <div class="auth-switch">Sudah punya akun?
          <a href="login.php?mode=login">Login</a></div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>

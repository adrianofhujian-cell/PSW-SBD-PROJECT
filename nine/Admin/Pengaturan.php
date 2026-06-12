<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include "../Config/config.php";

$pesan = "";

// Ambil data pengaturan (baris pertama)
$r   = mysqli_query($koneksi,"SELECT * FROM pengaturan LIMIT 1");
$set = mysqli_fetch_assoc($r);
$id  = $set['id_pengaturan'] ?? null;

// SIMPAN
if(isset($_POST['aksi']) && $_POST['aksi']=='simpan') {
  $nama   = mysqli_real_escape_string($koneksi,$_POST['nama_kantin']);
  $email  = mysqli_real_escape_string($koneksi,$_POST['email']);
  $telp   = mysqli_real_escape_string($koneksi,$_POST['telepon']);
  $alamat = mysqli_real_escape_string($koneksi,$_POST['alamat']);
  if($id) {
    $sql = "UPDATE pengaturan SET nama_kantin='$nama',email='$email',telepon='$telp',alamat='$alamat' WHERE id_pengaturan=$id";
  } else {
    $sql = "INSERT INTO pengaturan (nama_kantin,email,telepon,alamat) VALUES ('$nama','$email','$telp','$alamat')";
  }
  if(mysqli_query($koneksi,$sql)) {
    $pesan="sukses|Pengaturan berhasil disimpan!";
    $r=$koneksi; // refresh
    $r   = mysqli_query($koneksi,"SELECT * FROM pengaturan LIMIT 1");
    $set = mysqli_fetch_assoc($r);
    $id  = $set['id_pengaturan'] ?? null;
  } else $pesan="error|Gagal: ".mysqli_error($koneksi);
}

// GANTI PASSWORD
if(isset($_POST['aksi']) && $_POST['aksi']=='ganti_pw') {
  $pw_lama = $_POST['pw_lama'];
  $pw_baru = $_POST['pw_baru'];
  $pw_konfirm = $_POST['pw_konfirm'];
  if($pw_baru !== $pw_konfirm) {
    $pesan="error|Konfirmasi password tidak cocok!";
  } elseif(strlen($pw_baru) < 6) {
    $pesan="error|Password minimal 6 karakter!";
  } else {
    $aid = $_SESSION['admin_id'] ?? 0;
    $r2  = mysqli_query($koneksi,"SELECT password FROM pengguna WHERE id_pengguna=$aid");
    $u   = mysqli_fetch_assoc($r2);
    if($u && password_verify($pw_lama, $u['password'])) {
      $hash = password_hash($pw_baru, PASSWORD_DEFAULT);
      mysqli_query($koneksi,"UPDATE pengguna SET password='$hash' WHERE id_pengguna=$aid");
      $pesan="sukses|Password berhasil diubah!";
    } else {
      $pesan="error|Password saat ini salah!";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Pengaturan - Larris Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<link rel="stylesheet" href="Laporan.css">
<style>
.alert{padding:12px 18px;border-radius:10px;font-size:.88rem;margin-bottom:16px;display:flex;align-items:center;gap:10px}
.alert.sukses{background:#e8f5e9;color:#2e7d32;border:1px solid #c8e6c9}
.alert.error{background:#ffebee;color:#c62828;border:1px solid #ffcdd2}
</style>
</head>
<body>
<div class="dashboard">
<div class="sidebar" id="sidebar">
  <div>
    <div class="logo"><h2>Larris</h2><p>Admin Panel</p></div>
    <ul class="menu">
      <li><a href="Dashboard.php">Dashboard</a></li>
      <li><a href="Produk.php">Produk</a></li>
      <li><a href="kategori.php">Kategori</a></li>
      <li><a href="Stok.php">Stok</a></li>
      <li><a href="Pesanan.php">Pesanan</a></li>
      <li><a href="Transaksi.php">Transaksi</a></li>
      <li><a href="Laporan.php">Laporan</a></li>
      <li><a href="Pengguna.php">Pengguna</a></li>
      <li class="active"><a href="Pengaturan.php">Pengaturan</a></li>
    </ul>
  </div>
  <button class="logout-btn" onclick="openLogout()"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
</div>
<div class="main">
  <div class="topbar">
    <div class="top-left"><i class="fa-solid fa-bars menu-btn" onclick="toggleSidebar()"></i><h2>Pengaturan</h2></div>
    <div class="top-right"><div class="admin-profile"><div class="profile-circle">A</div><h4>Admin Larris</h4></div></div>
  </div>
  <div class="page-header">
    <div><h1>Pengaturan</h1><p>Kelola pengaturan sistem kantin</p></div>
  </div>

  <?php if($pesan): list($tipe,$teks)=explode('|',$pesan,2); ?>
  <div class="alert <?= $tipe ?>"><i class="fa-solid fa-<?= $tipe=='sukses'?'check-circle':'exclamation-circle' ?>"></i><?= $teks ?></div>
  <?php endif; ?>

  <div class="settings-grid">
    <!-- INFORMASI KANTIN -->
    <div class="box">
      <h3>Informasi Kantin</h3>
      <p class="sub">Atur informasi dasar kantin Anda</p>
      <form method="POST">
        <input type="hidden" name="aksi" value="simpan">
        <div class="form-group">
          <label>Nama Kantin</label>
          <input type="text" name="nama_kantin" value="<?= htmlspecialchars($set['nama_kantin']??'Larris') ?>" required>
        </div>
        <div class="form-group">
          <label>Email Kantin</label>
          <input type="email" name="email" value="<?= htmlspecialchars($set['email']??'') ?>">
        </div>
        <div class="form-group">
          <label>No. Telepon</label>
          <input type="text" name="telepon" value="<?= htmlspecialchars($set['telepon']??'') ?>">
        </div>
        <div class="form-group">
          <label>Alamat</label>
          <textarea name="alamat" rows="3"><?= htmlspecialchars($set['alamat']??'') ?></textarea>
        </div>
        <button type="submit" class="save-btn" style="margin-top:8px">
          <i class="fa-regular fa-floppy-disk"></i> Simpan Perubahan
        </button>
      </form>
    </div>

    <!-- GANTI PASSWORD -->
    <div class="box">
      <h3>Keamanan Akun</h3>
      <p class="sub">Ubah password akun admin</p>
      <form method="POST">
        <input type="hidden" name="aksi" value="ganti_pw">
        <div class="form-group">
          <label>Password Saat Ini</label>
          <input type="password" name="pw_lama" required placeholder="Password lama">
        </div>
        <div class="form-group">
          <label>Password Baru</label>
          <input type="password" name="pw_baru" required placeholder="Minimal 6 karakter">
        </div>
        <div class="form-group">
          <label>Konfirmasi Password Baru</label>
          <input type="password" name="pw_konfirm" required placeholder="Ulangi password baru">
        </div>
        <div class="secure-box" style="margin-top:16px">
          <div class="secure-top">
            <i class="fa-solid fa-lock"></i>
            <div><h4>Pastikan password baru kuat dan mudah diingat.</h4></div>
          </div>
          <button type="submit" class="password-btn">Ubah Password</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>

<div class="logout-modal" id="logoutModal">
  <div class="logout-box"><h2>Logout</h2><div class="logout-action">
    <button class="cancel-btn" onclick="closeLogout()">Batal</button>
    <a href="logout.php" class="confirm-btn">Ya, Logout</a>
  </div></div>
</div>
<script>
function openLogout(){document.getElementById('logoutModal').style.display='flex';}
function closeLogout(){document.getElementById('logoutModal').style.display='none';}
function toggleSidebar(){document.getElementById("sidebar").classList.toggle("hide");document.querySelector(".main").classList.toggle("full");}
</script>
</body></html>

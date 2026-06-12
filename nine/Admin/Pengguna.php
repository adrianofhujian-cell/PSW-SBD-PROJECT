<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include "../Config/config.php";

$pesan = "";

if(isset($_POST['aksi']) && $_POST['aksi']=='tambah') {
  $nama  = mysqli_real_escape_string($koneksi,$_POST['nama']);
  $user  = mysqli_real_escape_string($koneksi,$_POST['username']);
  $email = mysqli_real_escape_string($koneksi,$_POST['email']);
  $pw    = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role  = mysqli_real_escape_string($koneksi,$_POST['role']);
  $stat  = mysqli_real_escape_string($koneksi,$_POST['status']);
  $cek   = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_pengguna FROM pengguna WHERE username='$user' OR email='$email'"));
  if($cek > 0) $pesan="error|Username atau email sudah digunakan!";
  else {
    $sql = "INSERT INTO pengguna (nama,username,email,password,role,status) VALUES ('$nama','$user','$email','$pw','$role','$stat')";
    if(mysqli_query($koneksi,$sql)) $pesan="sukses|Pengguna berhasil ditambahkan!";
    else $pesan="error|Gagal: ".mysqli_error($koneksi);
  }
}

if(isset($_POST['aksi']) && $_POST['aksi']=='edit') {
  $id    = (int)$_POST['id_pengguna'];
  $nama  = mysqli_real_escape_string($koneksi,$_POST['nama']);
  $user  = mysqli_real_escape_string($koneksi,$_POST['username']);
  $email = mysqli_real_escape_string($koneksi,$_POST['email']);
  $role  = mysqli_real_escape_string($koneksi,$_POST['role']);
  $stat  = mysqli_real_escape_string($koneksi,$_POST['status']);
  $sql   = "UPDATE pengguna SET nama='$nama',username='$user',email='$email',role='$role',status='$stat' WHERE id_pengguna=$id";
  // Ganti password hanya kalau diisi
  if(!empty($_POST['password'])) {
    $pw  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $sql = "UPDATE pengguna SET nama='$nama',username='$user',email='$email',password='$pw',role='$role',status='$stat' WHERE id_pengguna=$id";
  }
  if(mysqli_query($koneksi,$sql)) $pesan="sukses|Pengguna berhasil diperbarui!";
  else $pesan="error|Gagal: ".mysqli_error($koneksi);
}

if(isset($_GET['hapus'])) {
  $id=(int)$_GET['hapus'];
  if($id == ($_SESSION['admin_id']??0)) { $pesan="error|Tidak bisa menghapus akun sendiri!"; }
  else { mysqli_query($koneksi,"DELETE FROM pengguna WHERE id_pengguna=$id"); header("Location: Pengguna.php?pesan=hapus"); exit; }
}
if(isset($_GET['pesan']) && $_GET['pesan']=='hapus') $pesan="sukses|Pengguna berhasil dihapus!";

$edit_data=null;
if(isset($_GET['edit'])) {
  $id=(int)$_GET['edit'];
  $r=mysqli_query($koneksi,"SELECT * FROM pengguna WHERE id_pengguna=$id");
  $edit_data=mysqli_fetch_assoc($r);
}

$total   = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_pengguna FROM pengguna"));
$admin   = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_pengguna FROM pengguna WHERE role='admin'"));
$kasir   = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_pengguna FROM pengguna WHERE role='kasir'"));
$nonaktif= mysqli_num_rows(mysqli_query($koneksi,"SELECT id_pengguna FROM pengguna WHERE status='nonaktif'"));

$cari  = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi,$_GET['cari']) : '';
$where = $cari ? "WHERE nama LIKE '%$cari%' OR username LIKE '%$cari%' OR email LIKE '%$cari%'" : "";
$query = mysqli_query($koneksi,"SELECT * FROM pengguna $where ORDER BY id_pengguna DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Pengguna - Larris Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<link rel="stylesheet" href="Laporan.css">
<style>
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;align-items:center;justify-content:center}
.modal-overlay.open{display:flex}
.modal{background:#fff;border-radius:16px;padding:32px;width:100%;max-width:500px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2)}
.modal h2{font-size:1.1rem;font-weight:600;margin-bottom:20px}
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:.82rem;font-weight:500;color:#555;margin-bottom:5px}
.form-group input,.form-group select{width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:10px;font-family:'Poppins',sans-serif;font-size:.88rem;outline:none}
.form-group input:focus{border-color:#4CAF50}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.modal-footer{display:flex;gap:10px;justify-content:flex-end;margin-top:20px}
.btn-batal{padding:10px 20px;border-radius:10px;border:1px solid #ddd;background:#fff;font-family:'Poppins',sans-serif;font-size:.85rem;cursor:pointer}
.btn-simpan{padding:10px 24px;border-radius:10px;border:none;background:#4CAF50;color:#fff;font-family:'Poppins',sans-serif;font-weight:600;font-size:.85rem;cursor:pointer}
.alert{padding:12px 18px;border-radius:10px;font-size:.88rem;margin-bottom:16px;display:flex;align-items:center;gap:10px}
.alert.sukses{background:#e8f5e9;color:#2e7d32;border:1px solid #c8e6c9}
.alert.error{background:#ffebee;color:#c62828;border:1px solid #ffcdd2}
.aksi-link{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;text-decoration:none;font-size:.85rem}
.aksi-link:hover{opacity:.75}
.aksi-edit{background:#e3f2fd;color:#1565c0}
.aksi-hapus{background:#ffebee;color:#c62828}
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
      <li class="active"><a href="Pengguna.php">Pengguna</a></li>
      <li><a href="Pengaturan.php">Pengaturan</a></li>
    </ul>
  </div>
  <button class="logout-btn" onclick="openLogout()"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
</div>
<div class="main">
  <div class="topbar">
    <div class="top-left"><i class="fa-solid fa-bars menu-btn" onclick="toggleSidebar()"></i><h2>Pengguna</h2></div>
    <div class="top-right"><div class="admin-profile"><div class="profile-circle">A</div><h4>Admin Larris</h4></div></div>
  </div>
  <div class="page-header">
    <div><h1>Pengguna</h1><p>Kelola semua pengguna sistem</p></div>
    <button class="add-btn" onclick="openModal('modal-tambah')"><i class="fa-solid fa-plus"></i> Tambah Pengguna</button>
  </div>
  <?php if($pesan): list($tipe,$teks)=explode('|',$pesan,2); ?>
  <div class="alert <?= $tipe ?>"><i class="fa-solid fa-<?= $tipe=='sukses'?'check-circle':'exclamation-circle' ?>"></i><?= $teks ?></div>
  <?php endif; ?>
  <div class="stats-grid">
    <div class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-users"></i></div><div><p>Total Pengguna</p><h2><?= $total ?></h2></div></div>
    <div class="stat-card"><div class="stat-icon blue"><i class="fa-regular fa-id-badge"></i></div><div><p>Admin</p><h2><?= $admin ?></h2></div></div>
    <div class="stat-card"><div class="stat-icon purple"><i class="fa-regular fa-user"></i></div><div><p>Kasir</p><h2><?= $kasir ?></h2></div></div>
    <div class="stat-card"><div class="stat-icon orange"><i class="fa-regular fa-user"></i></div><div><p>Tidak Aktif</p><h2><?= $nonaktif ?></h2></div></div>
  </div>
  <form method="GET">
    <div class="filter-box">
      <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="cari" placeholder="Cari nama, email, username..." value="<?= htmlspecialchars($cari) ?>"></div>
      <div class="filter-actions">
        <button type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
        <a href="Pengguna.php"><button type="button"><i class="fa-solid fa-rotate-left"></i> Reset</button></a>
      </div>
    </div>
  </form>
  <div class="table-container">
    <table>
      <thead><tr><th>No</th><th>Nama</th><th>Username</th><th>Email</th><th>Peran</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php $no=1; while($d=mysqli_fetch_assoc($query)):
        $inisial = strtoupper(substr($d['nama'],0,1));
        $colors  = ['green-bg','blue-bg','purple-bg','orange-bg','yellow-bg'];
        $warna   = $colors[($d['id_pengguna']-1) % count($colors)];
      ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><div class="user-info"><div class="avatar <?= $warna ?>"><?= $inisial ?></div><span><?= htmlspecialchars($d['nama']) ?></span></div></td>
        <td><?= htmlspecialchars($d['username']) ?></td>
        <td><?= htmlspecialchars($d['email']) ?></td>
        <td><span class="role <?= $d['role'] ?>"><?= ucfirst($d['role']) ?></span></td>
        <td><span class="status <?= $d['status']=='aktif'?'active':'nonaktif' ?>"><?= ucfirst($d['status']) ?></span></td>
        <td><div style="display:flex;gap:6px">
          <a href="Pengguna.php?edit=<?= $d['id_pengguna'] ?>" class="aksi-link aksi-edit"><i class="fa-solid fa-pen"></i></a>
          <a href="Pengguna.php?hapus=<?= $d['id_pengguna'] ?>" class="aksi-link aksi-hapus" onclick="return confirm('Hapus pengguna ini?')"><i class="fa-solid fa-trash"></i></a>
        </div></td>
      </tr>
      <?php endwhile; ?>
      <?php if(mysqli_num_rows($query)==0): ?>
      <tr><td colspan="7" style="text-align:center;padding:32px;color:#aaa">Tidak ada pengguna ditemukan</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal-overlay" id="modal-tambah">
  <div class="modal">
    <h2><i class="fa-solid fa-user-plus" style="color:#4CAF50;margin-right:8px"></i>Tambah Pengguna</h2>
    <form method="POST">
      <input type="hidden" name="aksi" value="tambah">
      <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" required placeholder="Nama lengkap"></div>
      <div class="form-row">
        <div class="form-group"><label>Username</label><input type="text" name="username" required placeholder="username"></div>
        <div class="form-group"><label>Role</label><select name="role"><option value="kasir">Kasir</option><option value="admin">Admin</option></select></div>
      </div>
      <div class="form-group"><label>Email</label><input type="email" name="email" required placeholder="email@contoh.com"></div>
      <div class="form-row">
        <div class="form-group"><label>Password</label><input type="password" name="password" required placeholder="Minimal 6 karakter"></div>
        <div class="form-group"><label>Status</label><select name="status"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-batal" onclick="closeModal('modal-tambah')">Batal</button>
        <button type="submit" class="btn-simpan">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDIT -->
<?php if($edit_data): ?>
<div class="modal-overlay open" id="modal-edit">
  <div class="modal">
    <h2><i class="fa-solid fa-pen" style="color:#1565c0;margin-right:8px"></i>Edit Pengguna</h2>
    <form method="POST">
      <input type="hidden" name="aksi" value="edit">
      <input type="hidden" name="id_pengguna" value="<?= $edit_data['id_pengguna'] ?>">
      <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" value="<?= htmlspecialchars($edit_data['nama']) ?>" required></div>
      <div class="form-row">
        <div class="form-group"><label>Username</label><input type="text" name="username" value="<?= htmlspecialchars($edit_data['username']) ?>" required></div>
        <div class="form-group"><label>Role</label><select name="role"><option value="kasir" <?= $edit_data['role']=='kasir'?'selected':'' ?>>Kasir</option><option value="admin" <?= $edit_data['role']=='admin'?'selected':'' ?>>Admin</option></select></div>
      </div>
      <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($edit_data['email']) ?>" required></div>
      <div class="form-row">
        <div class="form-group"><label>Password Baru <small style="color:#aaa">(kosongkan jika tidak diubah)</small></label><input type="password" name="password" placeholder="Password baru..."></div>
        <div class="form-group"><label>Status</label><select name="status"><option value="aktif" <?= $edit_data['status']=='aktif'?'selected':'' ?>>Aktif</option><option value="nonaktif" <?= $edit_data['status']=='nonaktif'?'selected':'' ?>>Nonaktif</option></select></div>
      </div>
      <div class="modal-footer">
        <a href="Pengguna.php"><button type="button" class="btn-batal">Batal</button></a>
        <button type="submit" class="btn-simpan">Perbarui</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="logout-modal" id="logoutModal">
  <div class="logout-box"><h2>Logout</h2><div class="logout-action">
    <button class="cancel-btn" onclick="closeLogout()">Batal</button>
    <a href="logout.php" class="confirm-btn">Ya, Logout</a>
  </div></div>
</div>
<script>
function openModal(id){document.getElementById(id).classList.add('open');}
function closeModal(id){document.getElementById(id).classList.remove('open');}
function openLogout(){document.getElementById('logoutModal').style.display='flex';}
function closeLogout(){document.getElementById('logoutModal').style.display='none';}
document.querySelectorAll('.modal-overlay').forEach(o=>{o.addEventListener('click',e=>{if(e.target===o)o.classList.remove('open');});});
function toggleSidebar(){document.getElementById("sidebar").classList.toggle("hide");document.querySelector(".main").classList.toggle("full");}
</script>
</body></html>

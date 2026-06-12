<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include "../Config/config.php";

$pesan = "";

if(isset($_POST['aksi']) && $_POST['aksi']=='tambah') {
  $nama   = mysqli_real_escape_string($koneksi,$_POST['nama_stok']);
  $kat    = mysqli_real_escape_string($koneksi,$_POST['kategori']);
  $sat    = mysqli_real_escape_string($koneksi,$_POST['satuan']);
  $jml    = (int)$_POST['jumlah'];
  $min    = (int)$_POST['stok_minimum'];
  $status = mysqli_real_escape_string($koneksi,$_POST['status']);
  $sql = "INSERT INTO stok (nama_stok,kategori,satuan,jumlah,stok_minimum,status) VALUES ('$nama','$kat','$sat',$jml,$min,'$status')";
  if(mysqli_query($koneksi,$sql)) $pesan="sukses|Stok berhasil ditambahkan!";
  else $pesan="error|Gagal: ".mysqli_error($koneksi);
}

if(isset($_POST['aksi']) && $_POST['aksi']=='edit') {
  $id     = (int)$_POST['id_stok'];
  $nama   = mysqli_real_escape_string($koneksi,$_POST['nama_stok']);
  $kat    = mysqli_real_escape_string($koneksi,$_POST['kategori']);
  $sat    = mysqli_real_escape_string($koneksi,$_POST['satuan']);
  $jml    = (int)$_POST['jumlah'];
  $min    = (int)$_POST['stok_minimum'];
  $status = mysqli_real_escape_string($koneksi,$_POST['status']);
  $sql = "UPDATE stok SET nama_stok='$nama',kategori='$kat',satuan='$sat',jumlah=$jml,stok_minimum=$min,status='$status' WHERE id_stok=$id";
  if(mysqli_query($koneksi,$sql)) $pesan="sukses|Stok berhasil diperbarui!";
  else $pesan="error|Gagal: ".mysqli_error($koneksi);
}

if(isset($_GET['hapus'])) {
  $id=(int)$_GET['hapus'];
  mysqli_query($koneksi,"DELETE FROM stok WHERE id_stok=$id");
  header("Location: Stok.php?pesan=hapus"); exit;
}
if(isset($_GET['pesan']) && $_GET['pesan']=='hapus') $pesan="sukses|Stok berhasil dihapus!";

$edit_data=null;
if(isset($_GET['edit'])) {
  $id=(int)$_GET['edit'];
  $r=mysqli_query($koneksi,"SELECT * FROM stok WHERE id_stok=$id");
  $edit_data=mysqli_fetch_assoc($r);
}

$total   = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_stok FROM stok"));
$menipis = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_stok FROM stok WHERE jumlah < stok_minimum AND jumlah > 0"));
$habis   = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_stok FROM stok WHERE jumlah = 0"));

$cari  = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi,$_GET['cari']) : '';
$where = $cari ? "WHERE nama_stok LIKE '%$cari%' OR kategori LIKE '%$cari%'" : "";
$query = mysqli_query($koneksi,"SELECT * FROM stok $where ORDER BY id_stok DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Stok - Larris Admin</title>
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
.form-group input,.form-group select{width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:10px;font-family:'Poppins',sans-serif;font-size:.88rem;outline:none;transition:border-color .2s}
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
      <li class="active"><a href="Stok.php">Stok</a></li>
      <li><a href="Pesanan.php">Pesanan</a></li>
      <li><a href="Transaksi.php">Transaksi</a></li>
      <li><a href="Laporan.php">Laporan</a></li>
      <li><a href="Pengguna.php">Pengguna</a></li>
      <li><a href="Pengaturan.php">Pengaturan</a></li>
    </ul>
  </div>
  <button class="logout-btn" onclick="openLogout()"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
</div>
<div class="main">
  <div class="topbar">
    <div class="top-left"><i class="fa-solid fa-bars menu-btn" onclick="toggleSidebar()"></i><h2>Stok</h2></div>
    <div class="top-right"><i class="fa-regular fa-bell notification"></i><div class="admin-profile"><div class="profile-circle">A</div><h4>Admin Larris</h4></div></div>
  </div>
  <div class="page-header">
    <div><h1>Stok</h1><p>Kelola semua stok bahan kantin</p></div>
    <button class="add-btn" onclick="openModal('modal-tambah')"><i class="fa-solid fa-plus"></i> Tambah Stok</button>
  </div>
  <?php if($pesan): list($tipe,$teks)=explode('|',$pesan,2); ?>
  <div class="alert <?= $tipe ?>"><i class="fa-solid fa-<?= $tipe=='sukses'?'check-circle':'exclamation-circle' ?>"></i><?= $teks ?></div>
  <?php endif; ?>
  <div class="cards">
    <div class="card"><div class="icon green"><i class="fa-solid fa-box"></i></div><div><p>Total Stok</p><h2><?= $total ?></h2><span>Semua item</span></div></div>
    <div class="card"><div class="icon orange"><i class="fa-solid fa-triangle-exclamation"></i></div><div><p>Stok Menipis</p><h2><?= $menipis ?></h2><span>Perlu restock</span></div></div>
    <div class="card"><div class="icon purple"><i class="fa-solid fa-cube"></i></div><div><p>Stok Habis</p><h2><?= $habis ?></h2><span class="red-text">Segera restock</span></div></div>
    <div class="card"><div class="icon blue"><i class="fa-solid fa-clipboard-list"></i></div><div><p>Stok Aman</p><h2><?= $total-$menipis-$habis ?></h2><span>Kondisi aman</span></div></div>
  </div>
  <form method="GET">
    <div class="filter-bar">
      <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="cari" placeholder="Cari stok..." value="<?= htmlspecialchars($cari) ?>"></div>
      <button type="submit" class="filter-btn"><i class="fa-solid fa-filter"></i> Filter</button>
      <a href="Stok.php"><button type="button" class="reset-btn"><i class="fa-solid fa-rotate-left"></i> Reset</button></a>
    </div>
  </form>
  <div class="table-container">
    <table>
      <tr><th>No</th><th>Nama Stok</th><th>Kategori</th><th>Satuan</th><th>Jumlah</th><th>Min. Stok</th><th>Status</th><th>Aksi</th></tr>
      <?php $no=1; while($d=mysqli_fetch_assoc($query)):
        $kondisi = $d['jumlah']==0 ? 'habis' : ($d['jumlah']<$d['stok_minimum'] ? 'menipis' : 'aman');
      ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($d['nama_stok']) ?></td>
        <td><span class="badge orange-bg"><?= htmlspecialchars($d['kategori']??'–') ?></span></td>
        <td><?= $d['satuan'] ?></td>
        <td style="color:<?= $d['jumlah']<$d['stok_minimum']?'#e53935':'inherit' ?>;font-weight:<?= $d['jumlah']<$d['stok_minimum']?'600':'400' ?>"><?= $d['jumlah'] ?></td>
        <td><?= $d['stok_minimum'] ?></td>
        <td><span class="status <?= $kondisi ?>"><?= ucfirst($kondisi) ?></span></td>
        <td><div style="display:flex;gap:6px">
          <a href="Stok.php?edit=<?= $d['id_stok'] ?>" class="aksi-link aksi-edit"><i class="fa-solid fa-pen"></i></a>
          <a href="Stok.php?hapus=<?= $d['id_stok'] ?>" class="aksi-link aksi-hapus" onclick="return confirm('Hapus stok ini?')"><i class="fa-solid fa-trash"></i></a>
        </div></td>
      </tr>
      <?php endwhile; ?>
      <?php if(mysqli_num_rows($query)==0): ?>
      <tr><td colspan="8" style="text-align:center;padding:32px;color:#aaa">Tidak ada data stok</td></tr>
      <?php endif; ?>
    </table>
  </div>
</div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal-overlay" id="modal-tambah">
  <div class="modal">
    <h2><i class="fa-solid fa-plus" style="color:#4CAF50;margin-right:8px"></i>Tambah Stok</h2>
    <form method="POST">
      <input type="hidden" name="aksi" value="tambah">
      <div class="form-group"><label>Nama Stok</label><input type="text" name="nama_stok" required placeholder="Nama bahan/stok"></div>
      <div class="form-group"><label>Kategori</label><input type="text" name="kategori" placeholder="Contoh: Bahan Makanan"></div>
      <div class="form-row">
        <div class="form-group"><label>Satuan</label><input type="text" name="satuan" placeholder="kg / liter / pcs"></div>
        <div class="form-group"><label>Status</label><select name="status"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Jumlah</label><input type="number" name="jumlah" min="0" required placeholder="0"></div>
        <div class="form-group"><label>Stok Minimum</label><input type="number" name="stok_minimum" min="0" required placeholder="0"></div>
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
    <h2><i class="fa-solid fa-pen" style="color:#1565c0;margin-right:8px"></i>Edit Stok</h2>
    <form method="POST">
      <input type="hidden" name="aksi" value="edit">
      <input type="hidden" name="id_stok" value="<?= $edit_data['id_stok'] ?>">
      <div class="form-group"><label>Nama Stok</label><input type="text" name="nama_stok" value="<?= htmlspecialchars($edit_data['nama_stok']) ?>" required></div>
      <div class="form-group"><label>Kategori</label><input type="text" name="kategori" value="<?= htmlspecialchars($edit_data['kategori']) ?>"></div>
      <div class="form-row">
        <div class="form-group"><label>Satuan</label><input type="text" name="satuan" value="<?= htmlspecialchars($edit_data['satuan']) ?>"></div>
        <div class="form-group"><label>Status</label><select name="status"><option value="aktif" <?= $edit_data['status']=='aktif'?'selected':'' ?>>Aktif</option><option value="nonaktif" <?= $edit_data['status']=='nonaktif'?'selected':'' ?>>Nonaktif</option></select></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Jumlah</label><input type="number" name="jumlah" value="<?= $edit_data['jumlah'] ?>" min="0" required></div>
        <div class="form-group"><label>Stok Minimum</label><input type="number" name="stok_minimum" value="<?= $edit_data['stok_minimum'] ?>" min="0" required></div>
      </div>
      <div class="modal-footer">
        <a href="Stok.php"><button type="button" class="btn-batal">Batal</button></a>
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

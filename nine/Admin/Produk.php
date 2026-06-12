<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include "../Config/config.php";

$pesan = "";

// ── TAMBAH ──
if(isset($_POST['aksi']) && $_POST['aksi']=='tambah') {
  $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
  $id_kat   = (int)$_POST['id_kategori'];
  $harga    = (int)$_POST['harga'];
  $stok     = (int)$_POST['stok'];
  $desk     = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
  $status   = mysqli_real_escape_string($koneksi, $_POST['status']);
  $gambar   = mysqli_real_escape_string($koneksi, $_POST['gambar']);
  $sql = "INSERT INTO produk (nama_produk,id_kategori,harga,stok,deskripsi,gambar,status)
          VALUES ('$nama',$id_kat,$harga,$stok,'$desk','$gambar','$status')";
  if(mysqli_query($koneksi,$sql)) $pesan="sukses|Produk berhasil ditambahkan!";
  else $pesan="error|Gagal: ".mysqli_error($koneksi);
}

// ── EDIT ──
if(isset($_POST['aksi']) && $_POST['aksi']=='edit') {
  $id       = (int)$_POST['id_produk'];
  $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
  $id_kat   = (int)$_POST['id_kategori'];
  $harga    = (int)$_POST['harga'];
  $stok     = (int)$_POST['stok'];
  $desk     = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
  $status   = mysqli_real_escape_string($koneksi, $_POST['status']);
  $gambar   = mysqli_real_escape_string($koneksi, $_POST['gambar']);
  $sql = "UPDATE produk SET nama_produk='$nama',id_kategori=$id_kat,harga=$harga,stok=$stok,
          deskripsi='$desk',gambar='$gambar',status='$status' WHERE id_produk=$id";
  if(mysqli_query($koneksi,$sql)) $pesan="sukses|Produk berhasil diperbarui!";
  else $pesan="error|Gagal: ".mysqli_error($koneksi);
}

// ── HAPUS ──
if(isset($_GET['hapus'])) {
  $id=(int)$_GET['hapus'];
  mysqli_query($koneksi,"DELETE FROM produk WHERE id_produk=$id");
  header("Location: Produk.php?pesan=hapus"); exit;
}
if(isset($_GET['pesan']) && $_GET['pesan']=='hapus') $pesan="sukses|Produk berhasil dihapus!";

// Data edit
$edit_data=null;
if(isset($_GET['edit'])) {
  $id=(int)$_GET['edit'];
  $r=mysqli_query($koneksi,"SELECT * FROM produk WHERE id_produk=$id");
  $edit_data=mysqli_fetch_assoc($r);
}

// Kategori untuk dropdown
$kategori_list = mysqli_query($koneksi,"SELECT * FROM kategori WHERE status='aktif' ORDER BY nama_Kategori");

// Statistik
$total    = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_produk FROM produk"));
$menipis  = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_produk FROM produk WHERE stok < 10 AND stok > 0"));
$kat_tot  = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_kategori FROM kategori"));
$nonaktif = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_produk FROM produk WHERE status='nonaktif'"));

// Tabel
$cari  = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi,$_GET['cari']) : '';
$fkat  = isset($_GET['fkat'])  ? (int)$_GET['fkat']  : 0;
$fstat = isset($_GET['fstat']) ? mysqli_real_escape_string($koneksi,$_GET['fstat']) : '';
$where = "WHERE 1";
if($cari)  $where .= " AND p.nama_produk LIKE '%$cari%'";
if($fkat)  $where .= " AND p.id_kategori=$fkat";
if($fstat) $where .= " AND p.status='$fstat'";
$query = mysqli_query($koneksi,"SELECT p.*,k.nama_Kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori=k.id_kategori $where ORDER BY p.id_produk DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Produk - Larris Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<link rel="stylesheet" href="Laporan.css">
<style>
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;align-items:center;justify-content:center}
.modal-overlay.open{display:flex}
.modal{background:#fff;border-radius:16px;padding:32px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2)}
.modal h2{font-size:1.1rem;font-weight:600;margin-bottom:20px}
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:.82rem;font-weight:500;color:#555;margin-bottom:5px}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:10px;font-family:'Poppins',sans-serif;font-size:.88rem;outline:none;transition:border-color .2s}
.form-group input:focus,.form-group select:focus{border-color:#4CAF50}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.modal-footer{display:flex;gap:10px;justify-content:flex-end;margin-top:20px}
.btn-batal{padding:10px 20px;border-radius:10px;border:1px solid #ddd;background:#fff;font-family:'Poppins',sans-serif;font-size:.85rem;cursor:pointer}
.btn-simpan{padding:10px 24px;border-radius:10px;border:none;background:#4CAF50;color:#fff;font-family:'Poppins',sans-serif;font-weight:600;font-size:.85rem;cursor:pointer}
.alert{padding:12px 18px;border-radius:10px;font-size:.88rem;margin-bottom:16px;display:flex;align-items:center;gap:10px}
.alert.sukses{background:#e8f5e9;color:#2e7d32;border:1px solid #c8e6c9}
.alert.error{background:#ffebee;color:#c62828;border:1px solid #ffcdd2}
.aksi-link{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;text-decoration:none;font-size:.85rem;transition:opacity .2s;border:none;cursor:pointer}
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
      <li class="active"><a href="Produk.php">Produk</a></li>
      <li><a href="kategori.php">Kategori</a></li>
      <li><a href="Stok.php"></i> Stok</a></li>
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
    <div class="top-left"><i class="fa-solid fa-bars menu-btn" onclick="toggleSidebar()"></i><h2>Produk</h2></div>
    <div class="top-right"><i class="fa-regular fa-bell"></i><div class="admin-profile"><div class="profile-circle">A</div><div><h4>Admin Larris</h4></div></div></div>
  </div>
  <div class="page-header">
    <div><h1>Produk</h1><p>Kelola semua produk jajanan kantin</p></div>
    <button class="add-product" onclick="openModal('modal-tambah')"><i class="fa-solid fa-plus"></i> Tambah Produk</button>
  </div>
  <?php if($pesan): list($tipe,$teks)=explode('|',$pesan,2); ?>
  <div class="alert <?= $tipe ?>"><i class="fa-solid fa-<?= $tipe=='sukses'?'check-circle':'exclamation-circle' ?>"></i><?= $teks ?></div>
  <?php endif; ?>
  <div class="product-cards">
    <div class="product-card"><div class="card-icon green"><i class="fa-solid fa-bag-shopping"></i></div><div><p>Total Produk</p><h2><?= $total ?></h2><span>Semua produk</span></div></div>
    <div class="product-card"><div class="card-icon orange"><i class="fa-solid fa-triangle-exclamation"></i></div><div><p>Stok Menipis</p><h2><?= $menipis ?></h2><span>Perlu restock</span></div></div>
    <div class="product-card"><div class="card-icon purple"><i class="fa-solid fa-cube"></i></div><div><p>Kategori</p><h2><?= $kat_tot ?></h2><span>Jenis kategori</span></div></div>
    <div class="product-card"><div class="card-icon blue"><i class="fa-solid fa-eye"></i></div><div><p>Nonaktif</p><h2><?= $nonaktif ?></h2><span>Tidak ditampilkan</span></div></div>
  </div>
  <form method="GET">
    <div class="filter-bar">
      <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="cari" placeholder="Cari produk..." value="<?= htmlspecialchars($cari) ?>"></div>
      <select name="fkat">
        <option value="">Semua Kategori</option>
        <?php $kl2=mysqli_query($koneksi,"SELECT * FROM kategori ORDER BY nama_Kategori"); while($k=mysqli_fetch_assoc($kl2)): ?>
        <option value="<?= $k['id_kategori'] ?>" <?= $fkat==$k['id_kategori']?'selected':'' ?>><?= $k['nama_Kategori'] ?></option>
        <?php endwhile; ?>
      </select>
      <select name="fstat">
        <option value="">Semua Status</option>
        <option value="aktif" <?= $fstat=='aktif'?'selected':'' ?>>Aktif</option>
        <option value="nonaktif" <?= $fstat=='nonaktif'?'selected':'' ?>>Nonaktif</option>
      </select>
      <button type="submit" class="filter-btn"><i class="fa-solid fa-filter"></i> Filter</button>
      <a href="Produk.php"><button type="button" class="reset-btn"><i class="fa-solid fa-rotate-left"></i> Reset</button></a>
    </div>
  </form>
  <div class="table-container">
    <table>
      <tr><th>No</th><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr>
      <?php $no=1; while($d=mysqli_fetch_assoc($query)): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td class="product-info"><?= htmlspecialchars($d['nama_produk']) ?></td>
        <td><span class="kategori"><?= htmlspecialchars($d['nama_Kategori']??'–') ?></span></td>
        <td>Rp <?= number_format($d['harga'],0,',','.') ?></td>
        <td style="color:<?= $d['stok']<10?'#e53935':'inherit' ?>;font-weight:<?= $d['stok']<10?'600':'400' ?>"><?= $d['stok'] ?></td>
        <td><span class="status <?= $d['status']=='aktif'?'aktif':'nonaktif' ?>"><?= ucfirst($d['status']) ?></span></td>
        <td><div style="display:flex;gap:6px">
          <a href="Produk.php?edit=<?= $d['id_produk'] ?>" class="aksi-link aksi-edit"><i class="fa-solid fa-pen"></i></a>
          <a href="Produk.php?hapus=<?= $d['id_produk'] ?>" class="aksi-link aksi-hapus" onclick="return confirm('Hapus produk ini?')"><i class="fa-solid fa-trash"></i></a>
        </div></td>
      </tr>
      <?php endwhile; ?>
      <?php if(mysqli_num_rows($query)==0): ?>
      <tr><td colspan="7" style="text-align:center;padding:32px;color:#aaa">Tidak ada produk ditemukan</td></tr>
      <?php endif; ?>
    </table>
  </div>
</div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal-overlay" id="modal-tambah">
  <div class="modal">
    <h2><i class="fa-solid fa-plus" style="color:#4CAF50;margin-right:8px"></i>Tambah Produk</h2>
    <form method="POST">
      <input type="hidden" name="aksi" value="tambah">
      <div class="form-group"><label>Nama Produk</label><input type="text" name="nama_produk" required placeholder="Nama produk"></div>
      <div class="form-group"><label>Kategori</label>
        <select name="id_kategori" required>
          <option value="">-- Pilih Kategori --</option>
          <?php mysqli_data_seek($kategori_list,0); while($k=mysqli_fetch_assoc($kategori_list)): ?>
          <option value="<?= $k['id_kategori'] ?>"><?= $k['nama_Kategori'] ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Harga (Rp)</label><input type="number" name="harga" min="0" required placeholder="0"></div>
        <div class="form-group"><label>Stok</label><input type="number" name="stok" min="0" required placeholder="0"></div>
      </div>
      <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="3" placeholder="Deskripsi produk..."></textarea></div>
      <div class="form-group"><label>Nama File Gambar</label><input type="text" name="gambar" placeholder="contoh: siomay.jpg"></div>
      <div class="form-group"><label>Status</label><select name="status"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></div>
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
    <h2><i class="fa-solid fa-pen" style="color:#1565c0;margin-right:8px"></i>Edit Produk</h2>
    <form method="POST">
      <input type="hidden" name="aksi" value="edit">
      <input type="hidden" name="id_produk" value="<?= $edit_data['id_produk'] ?>">
      <div class="form-group"><label>Nama Produk</label><input type="text" name="nama_produk" value="<?= htmlspecialchars($edit_data['nama_produk']) ?>" required></div>
      <div class="form-group"><label>Kategori</label>
        <select name="id_kategori" required>
          <?php mysqli_data_seek($kategori_list,0); while($k=mysqli_fetch_assoc($kategori_list)): ?>
          <option value="<?= $k['id_kategori'] ?>" <?= $edit_data['id_kategori']==$k['id_kategori']?'selected':'' ?>><?= $k['nama_Kategori'] ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Harga (Rp)</label><input type="number" name="harga" value="<?= $edit_data['harga'] ?>" min="0" required></div>
        <div class="form-group"><label>Stok</label><input type="number" name="stok" value="<?= $edit_data['stok'] ?>" min="0" required></div>
      </div>
      <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="3"><?= htmlspecialchars($edit_data['deskripsi']) ?></textarea></div>
      <div class="form-group"><label>Nama File Gambar</label><input type="text" name="gambar" value="<?= htmlspecialchars($edit_data['gambar']) ?>"></div>
      <div class="form-group"><label>Status</label>
        <select name="status">
          <option value="aktif" <?= $edit_data['status']=='aktif'?'selected':'' ?>>Aktif</option>
          <option value="nonaktif" <?= $edit_data['status']=='nonaktif'?'selected':'' ?>>Nonaktif</option>
        </select>
      </div>
      <div class="modal-footer">
        <a href="Produk.php"><button type="button" class="btn-batal">Batal</button></a>
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
function toggleSidebar(){document.getElementById('sidebar').classList.toggle('hide');document.querySelector('.main').classList.toggle('full');}
document.querySelectorAll('.modal-overlay').forEach(o=>{o.addEventListener('click',e=>{if(e.target===o)o.classList.remove('open');});});
</script>
</body></html>

<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include "../Config/config.php";

$pesan = "";

if(isset($_POST['aksi']) && $_POST['aksi']=='tambah') {
  $nama   = mysqli_real_escape_string($koneksi,$_POST['nama_pelanggan']);
  $tgl    = mysqli_real_escape_string($koneksi,$_POST['tanggal_pesanan']);
  $total  = (int)$_POST['total'];
  $status = mysqli_real_escape_string($koneksi,$_POST['status']);
  $sql = "INSERT INTO pesanan (nama_pelanggan,tanggal_pesanan,total,status) VALUES ('$nama','$tgl',$total,'$status')";
  if(mysqli_query($koneksi,$sql)) $pesan="sukses|Pesanan berhasil ditambahkan!";
  else $pesan="error|Gagal: ".mysqli_error($koneksi);
}

if(isset($_POST['aksi']) && $_POST['aksi']=='edit') {
  $id     = (int)$_POST['id_pesanan'];
  $nama   = mysqli_real_escape_string($koneksi,$_POST['nama_pelanggan']);
  $tgl    = mysqli_real_escape_string($koneksi,$_POST['tanggal_pesanan']);
  $total  = (int)$_POST['total'];
  $status = mysqli_real_escape_string($koneksi,$_POST['status']);
  $sql = "UPDATE pesanan SET nama_pelanggan='$nama',tanggal_pesanan='$tgl',total=$total,status='$status' WHERE id_pesanan=$id";
  if(mysqli_query($koneksi,$sql)) $pesan="sukses|Pesanan berhasil diperbarui!";
  else $pesan="error|Gagal: ".mysqli_error($koneksi);
}

if(isset($_GET['hapus'])) {
  $id=(int)$_GET['hapus'];
  // Hapus transaksi terkait dulu baru hapus pesanan
  mysqli_query($koneksi,"DELETE FROM transaksi WHERE id_pesanan=$id");
  mysqli_query($koneksi,"DELETE FROM pesanan WHERE id_pesanan=$id");
  header("Location: Pesanan.php?pesan=hapus"); exit;
}
if(isset($_GET['pesan']) && $_GET['pesan']=='hapus') $pesan="sukses|Pesanan berhasil dihapus!";

$edit_data=null;
if(isset($_GET['edit'])) {
  $id=(int)$_GET['edit'];
  $r=mysqli_query($koneksi,"SELECT * FROM pesanan WHERE id_pesanan=$id");
  $edit_data=mysqli_fetch_assoc($r);
}

$total_all = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_pesanan FROM pesanan"));
$pending   = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_pesanan FROM pesanan WHERE status='pending'"));
$proses    = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_pesanan FROM pesanan WHERE status='proses'"));
$selesai   = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_pesanan FROM pesanan WHERE status='selesai'"));
$batal     = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_pesanan FROM pesanan WHERE status='batal'"));

$cari  = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi,$_GET['cari']) : '';
$fstat = isset($_GET['fstat']) ? mysqli_real_escape_string($koneksi,$_GET['fstat']) : '';
$where = "WHERE 1";
if($cari)  $where .= " AND nama_pelanggan LIKE '%$cari%'";
if($fstat) $where .= " AND status='$fstat'";
$query = mysqli_query($koneksi,"SELECT * FROM pesanan $where ORDER BY id_pesanan DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Pesanan - Larris Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<link rel="stylesheet" href="Laporan.css">
<style>
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;align-items:center;justify-content:center}
.modal-overlay.open{display:flex}
.modal{background:#fff;border-radius:16px;padding:32px;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2)}
.modal h2{font-size:1.1rem;font-weight:600;margin-bottom:20px}
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:.82rem;font-weight:500;color:#555;margin-bottom:5px}
.form-group input,.form-group select{width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:10px;font-family:'Poppins',sans-serif;font-size:.88rem;outline:none}
.form-group input:focus{border-color:#4CAF50}
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
      <li class="active"><a href="Pesanan.php">Pesanan</a></li>
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
    <div class="top-left"><i class="fa-solid fa-bars menu-btn" onclick="toggleSidebar()"></i><h2>Pesanan</h2></div>
    <div class="top-right"><div class="admin-profile"><div class="profile-circle">A</div><h4>Admin Larris</h4></div></div>
  </div>
  <div class="page-header">
    <div><h1>Pesanan</h1><p>Kelola semua pesanan pelanggan</p></div>
    <button class="add-btn" onclick="openModal('modal-tambah')"><i class="fa-solid fa-plus"></i> Pesanan Baru</button>
  </div>
  <?php if($pesan): list($tipe,$teks)=explode('|',$pesan,2); ?>
  <div class="alert <?= $tipe ?>"><i class="fa-solid fa-<?= $tipe=='sukses'?'check-circle':'exclamation-circle' ?>"></i><?= $teks ?></div>
  <?php endif; ?>
  <div class="cards">
    <div class="card"><div class="card-icon green"><i class="fa-regular fa-clipboard"></i></div><div><p>Total</p><h2><?= $total_all ?></h2></div></div>
    <div class="card"><div class="card-icon orange"><i class="fa-regular fa-clock"></i></div><div><p>Menunggu</p><h2><?= $pending ?></h2></div></div>
    <div class="card"><div class="card-icon blue"><i class="fa-solid fa-box-open"></i></div><div><p>Diproses</p><h2><?= $proses ?></h2></div></div>
    <div class="card"><div class="card-icon purple"><i class="fa-regular fa-circle-check"></i></div><div><p>Selesai</p><h2><?= $selesai ?></h2></div></div>
    <div class="card"><div class="card-icon red"><i class="fa-regular fa-circle-xmark"></i></div><div><p>Dibatalkan</p><h2><?= $batal ?></h2></div></div>
  </div>
  <form method="GET">
    <div class="filter-box">
      <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="cari" placeholder="Cari nama pelanggan..." value="<?= htmlspecialchars($cari) ?>"></div>
      <select name="fstat">
        <option value="">Semua Status</option>
        <option value="pending" <?= $fstat=='pending'?'selected':'' ?>>Pending</option>
        <option value="proses" <?= $fstat=='proses'?'selected':'' ?>>Diproses</option>
        <option value="selesai" <?= $fstat=='selesai'?'selected':'' ?>>Selesai</option>
        <option value="batal" <?= $fstat=='batal'?'selected':'' ?>>Batal</option>
      </select>
      <button type="submit" class="filter-btn"><i class="fa-solid fa-filter"></i> Filter</button>
      <a href="Pesanan.php"><button type="button" class="reset-btn"><i class="fa-solid fa-rotate-left"></i> Reset</button></a>
    </div>
  </form>
  <div class="table-container">
    <table>
      <tr><th>No.</th><th>ID Pesanan</th><th>Pelanggan</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
      <?php $no=1; while($d=mysqli_fetch_assoc($query)):
        $st_class = ['pending'=>'waiting','proses'=>'process','selesai'=>'success','batal'=>'cancelled'];
        $cls = $st_class[$d['status']] ?? 'waiting';
      ?>
      <tr>
        <td><?= $no++ ?></td>
        <td style="font-weight:600;color:#555">#<?= $d['id_pesanan'] ?></td>
        <td><?= htmlspecialchars($d['nama_pelanggan']) ?></td>
        <td><?= $d['tanggal_pesanan'] ? date('d M Y H:i', strtotime($d['tanggal_pesanan'])) : '–' ?></td>
        <td>Rp <?= number_format($d['total'],0,',','.') ?></td>
        <td><span class="status <?= $cls ?>"><?= ucfirst($d['status']) ?></span></td>
        <td><div style="display:flex;gap:6px">
          <a href="Pesanan.php?edit=<?= $d['id_pesanan'] ?>" class="aksi-link aksi-edit"><i class="fa-solid fa-pen"></i></a>
          <a href="Pesanan.php?hapus=<?= $d['id_pesanan'] ?>" class="aksi-link aksi-hapus" onclick="return confirm('Hapus pesanan ini?')"><i class="fa-solid fa-trash"></i></a>
        </div></td>
      </tr>
      <?php endwhile; ?>
      <?php if(mysqli_num_rows($query)==0): ?>
      <tr><td colspan="7" style="text-align:center;padding:32px;color:#aaa">Tidak ada pesanan</td></tr>
      <?php endif; ?>
    </table>
  </div>
</div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal-overlay" id="modal-tambah">
  <div class="modal">
    <h2><i class="fa-solid fa-plus" style="color:#4CAF50;margin-right:8px"></i>Pesanan Baru</h2>
    <form method="POST">
      <input type="hidden" name="aksi" value="tambah">
      <div class="form-group"><label>Nama Pelanggan</label><input type="text" name="nama_pelanggan" required placeholder="Nama pelanggan"></div>
      <div class="form-group"><label>Tanggal Pesanan</label><input type="datetime-local" name="tanggal_pesanan" value="<?= date('Y-m-d\TH:i') ?>"></div>
      <div class="form-group"><label>Total (Rp)</label><input type="number" name="total" min="0" required placeholder="0"></div>
      <div class="form-group"><label>Status</label>
        <select name="status">
          <option value="pending">Pending</option>
          <option value="proses">Diproses</option>
          <option value="selesai">Selesai</option>
          <option value="batal">Batal</option>
        </select>
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
    <h2><i class="fa-solid fa-pen" style="color:#1565c0;margin-right:8px"></i>Edit Pesanan</h2>
    <form method="POST">
      <input type="hidden" name="aksi" value="edit">
      <input type="hidden" name="id_pesanan" value="<?= $edit_data['id_pesanan'] ?>">
      <div class="form-group"><label>Nama Pelanggan</label><input type="text" name="nama_pelanggan" value="<?= htmlspecialchars($edit_data['nama_pelanggan']) ?>" required></div>
      <div class="form-group"><label>Tanggal Pesanan</label><input type="datetime-local" name="tanggal_pesanan" value="<?= date('Y-m-d\TH:i', strtotime($edit_data['tanggal_pesanan'])) ?>"></div>
      <div class="form-group"><label>Total (Rp)</label><input type="number" name="total" value="<?= $edit_data['total'] ?>" min="0" required></div>
      <div class="form-group"><label>Status</label>
        <select name="status">
          <?php foreach(['pending','proses','selesai','batal'] as $s): ?>
          <option value="<?= $s ?>" <?= $edit_data['status']==$s?'selected':'' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="modal-footer">
        <a href="Pesanan.php"><button type="button" class="btn-batal">Batal</button></a>
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

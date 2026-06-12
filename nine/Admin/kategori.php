<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include "../Config/config.php";

// ── PROSES FORM ──────────────────────────────────────────
$pesan = "";

if(isset($_POST['aksi'])) {
  $nama   = mysqli_real_escape_string($koneksi, $_POST['nama_Kategori']);
  $desk   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
  $status = mysqli_real_escape_string($koneksi, $_POST['status']);

  if($_POST['aksi'] == 'tambah') {
    $sql = "INSERT INTO kategori (nama_Kategori, deskripsi, status) VALUES ('$nama','$desk','$status')";
    if(mysqli_query($koneksi, $sql)) $pesan = "sukses|Kategori berhasil ditambahkan!";
    else $pesan = "error|Gagal: " . mysqli_error($koneksi);
  }

  if($_POST['aksi'] == 'edit') {
    $id  = (int)$_POST['id_kategori'];
    $sql = "UPDATE kategori SET nama_Kategori='$nama', deskripsi='$desk', status='$status' WHERE id_kategori=$id";
    if(mysqli_query($koneksi, $sql)) $pesan = "sukses|Kategori berhasil diperbarui!";
    else $pesan = "error|Gagal: " . mysqli_error($koneksi);
  }
}

if(isset($_GET['hapus'])) {
  $id = (int)$_GET['hapus'];
  mysqli_query($koneksi, "DELETE FROM kategori WHERE id_kategori=$id");
  header("Location: kategori.php?pesan=hapus");
  exit;
}

if(isset($_GET['pesan']) && $_GET['pesan']=='hapus') $pesan = "sukses|Kategori berhasil dihapus!";

// Data edit
$edit_data = null;
if(isset($_GET['edit'])) {
  $id = (int)$_GET['edit'];
  $r  = mysqli_query($koneksi, "SELECT id_kategori, nama_Kategori as nama_kategori, deskripsi, status FROM kategori WHERE id_kategori=$id");
  $edit_data = mysqli_fetch_assoc($r);
}

// Statistik
$total    = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_kategori FROM kategori"));
$aktif    = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_kategori FROM kategori WHERE status='aktif'"));
$nonaktif = $total - $aktif;
$tot_prod = mysqli_num_rows(mysqli_query($koneksi,"SELECT id_produk FROM produk"));

// Tabel
$cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi,$_GET['cari']) : '';
$where = $cari ? "WHERE nama_Kategori LIKE '%$cari%' OR deskripsi LIKE '%$cari%'" : "";
$query = mysqli_query($koneksi,"SELECT id_kategori, nama_Kategori as nama_kategori, deskripsi, status FROM kategori $where ORDER BY id_kategori DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Kategori - Larris Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<link rel="stylesheet" href="Laporan.css">
<style>
/* ── MODAL ── */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;align-items:center;justify-content:center}
.modal-overlay.open{display:flex}
.modal{background:#fff;border-radius:16px;padding:32px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.2)}
.modal h2{font-size:1.1rem;font-weight:600;margin-bottom:20px}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:.82rem;font-weight:500;color:#555;margin-bottom:6px}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:10px;font-family:'Poppins',sans-serif;font-size:.88rem;outline:none;transition:border-color .2s}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:#4CAF50}
.form-group textarea{resize:vertical;min-height:80px}
.modal-footer{display:flex;gap:10px;justify-content:flex-end;margin-top:20px}
.btn-batal{padding:10px 20px;border-radius:10px;border:1px solid #ddd;background:#fff;font-family:'Poppins',sans-serif;font-size:.85rem;cursor:pointer}
.btn-simpan{padding:10px 24px;border-radius:10px;border:none;background:#4CAF50;color:#fff;font-family:'Poppins',sans-serif;font-weight:600;font-size:.85rem;cursor:pointer}
/* ── ALERT ── */
.alert{padding:12px 18px;border-radius:10px;font-size:.88rem;margin-bottom:20px;display:flex;align-items:center;gap:10px}
.alert.sukses{background:#e8f5e9;color:#2e7d32;border:1px solid #c8e6c9}
.alert.error{background:#ffebee;color:#c62828;border:1px solid #ffcdd2}
/* ── AKSI ── */
.aksi-link{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;text-decoration:none;font-size:.85rem;transition:opacity .2s}
.aksi-link:hover{opacity:.75}
.aksi-edit{background:#e3f2fd;color:#1565c0}
.aksi-hapus{background:#ffebee;color:#c62828}
</style>
</head>
<body>
<div class="dashboard">

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <div>
    <div class="logo"><h2>Larris</h2><p>Admin Panel</p></div>
    <ul class="menu">
      <li><a href="Dashboard.php">Dashboard</a></li>
      <li><a href="Produk.php">Produk</a></li>
      <li class="active"><a href="kategori.php">Kategori</a></li>
      <li><a href="Stok.php">Stok</a></li>
      <li><a href="Pesanan.php">Pesanan</a></li>
      <li><a href="Transaksi.php">Transaksi</a></li>
      <li><a href="Laporan.php">Laporan</a></li>
      <li><a href="Pengguna.php">Pengguna</a></li>
      <li><a href="Pengaturan.php">Pengaturan</a></li>
    </ul>
  </div>
  <button class="logout-btn" onclick="openLogout()"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
</div>

<!-- MAIN -->
<div class="main">
  <div class="topbar">
    <div class="top-left"><i class="fa-solid fa-bars menu-btn" onclick="toggleSidebar()"></i><h2>Kategori</h2></div>
    <div class="top-right">
      <i class="fa-regular fa-bell"></i>
      <div class="admin-profile"><div class="profile-circle">A</div><h4>Admin Larris</h4></div>
    </div>
  </div>

  <div class="page-header">
    <div><h1>Kategori</h1><p>Kelola semua kategori jajanan kantin</p></div>
    <button class="add-btn" onclick="openModal('modal-tambah')">
      <i class="fa-solid fa-plus"></i> Tambah Kategori
    </button>
  </div>

  <!-- ALERT -->
  <?php if($pesan): list($tipe,$teks) = explode('|',$pesan,2); ?>
  <div class="alert <?= $tipe ?>"><i class="fa-solid fa-<?= $tipe=='sukses'?'check-circle':'exclamation-circle' ?>"></i><?= $teks ?></div>
  <?php endif; ?>

  <!-- CARDS -->
  <div class="cards">
    <div class="card"><div class="card-icon green"><i class="fa-solid fa-table-cells"></i></div><div><p>Total Kategori</p><h2><?= $total ?></h2><span>Semua kategori</span></div></div>
    <div class="card"><div class="card-icon purple"><i class="fa-solid fa-tag"></i></div><div><p>Kategori Aktif</p><h2><?= $aktif ?></h2><span>Kategori aktif</span></div></div>
    <div class="card"><div class="card-icon orange"><i class="fa-regular fa-eye"></i></div><div><p>Nonaktif</p><h2><?= $nonaktif ?></h2><span>Tidak ditampilkan</span></div></div>
    <div class="card"><div class="card-icon blue"><i class="fa-solid fa-cube"></i></div><div><p>Total Produk</p><h2><?= $tot_prod ?></h2><span>Dalam kategori</span></div></div>
  </div>

  <!-- FILTER -->
  <form method="GET" action="">
    <div class="filter-box">
      <div class="sefarch-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="cari" placeholder="Cari kategori..." value="<?= htmlspecialchars($cari) ?>">
      </div>
      <div class="filter-buttons">
        <button type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
        <a href="kategori.php"><button type="button"><i class="fa-solid fa-rotate-left"></i> Reset</button></a>
      </div>
    </div>
  </form>

  <!-- TABLE -->
  <div class="table-box">
    <table>
      <tr><th>No</th><th>Kategori</th><th>Deskripsi</th><th>Status</th><th>Aksi</th></tr>
      <?php $no=1; while($data=mysqli_fetch_assoc($query)): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($data['nama_kategori'] ?? $data['nama_Kategori'] ?? '') ?></td>
        <td><?= htmlspecialchars($data['deskripsi']) ?: '–' ?></td>
        <td><span class="status <?= strtolower($data['status']) ?>"><?= $data['status'] ?></span></td>
        <td>
          <div style="display:flex;gap:6px">
            <a href="kategori.php?edit=<?= $data['id_kategori'] ?>" class="aksi-link aksi-edit" title="Edit"><i class="fa-solid fa-pen"></i></a>
            <a href="kategori.php?hapus=<?= $data['id_kategori'] ?>" class="aksi-link aksi-hapus" title="Hapus" onclick="return confirm('Yakin hapus kategori ini?')"><i class="fa-solid fa-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endwhile; ?>
      <?php if(mysqli_num_rows($query)==0): ?>
      <tr><td colspan="5" style="text-align:center;padding:32px;color:#aaa">Tidak ada data kategori</td></tr>
      <?php endif; ?>
    </table>
  </div>
</div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal-overlay" id="modal-tambah">
  <div class="modal">
    <h2><i class="fa-solid fa-plus" style="color:#4CAF50;margin-right:8px"></i>Tambah Kategori</h2>
    <form method="POST">
      <input type="hidden" name="aksi" value="tambah">
      <div class="form-group"><label>Nama Kategori</label><input type="text" name="nama_Kategori" required placeholder="Contoh: Makanan Berat"></div>
      <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" placeholder="Deskripsi singkat..."></textarea></div>
      <div class="form-group"><label>Status</label><select name="status"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></div>
      <div class="modal-footer">
        <button type="button" class="btn-batal" onclick="closeModal('modal-tambah')">Batal</button>
        <button type="submit" class="btn-simpan">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDIT (muncul otomatis kalau ada ?edit=) -->
<?php if($edit_data): ?>
<div class="modal-overlay open" id="modal-edit">
  <div class="modal">
    <h2><i class="fa-solid fa-pen" style="color:#1565c0;margin-right:8px"></i>Edit Kategori</h2>
    <form method="POST">
      <input type="hidden" name="aksi" value="edit">
      <input type="hidden" name="id_kategori" value="<?= $edit_data['id_kategori'] ?>">
      <div class="form-group"><label>Nama Kategori</label><input type="text" name="nama_Kategori" value="<?= htmlspecialchars($edit_data['nama_Kategori'] ?? $edit_data['nama_kategori'] ?? '') ?>" required></div>
      <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi"><?= htmlspecialchars($edit_data['deskripsi']) ?></textarea></div>
      <div class="form-group"><label>Status</label>
        <select name="status">
          <option value="aktif" <?= $edit_data['status']=='aktif'?'selected':'' ?>>Aktif</option>
          <option value="nonaktif" <?= $edit_data['status']=='nonaktif'?'selected':'' ?>>Nonaktif</option>
        </select>
      </div>
      <div class="modal-footer">
        <a href="kategori.php"><button type="button" class="btn-batal">Batal</button></a>
        <button type="submit" class="btn-simpan">Perbarui</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- LOGOUT MODAL -->
<div class="logout-modal" id="logoutModal">
  <div class="logout-box">
    <h2>Logout</h2>
    <div class="logout-action">
      <button class="cancel-btn" onclick="closeLogout()">Batal</button>
      <a href="logout.php" class="confirm-btn">Ya, Logout</a>
    </div>
  </div>
</div>

<script>
function toggleSidebar(){document.getElementById("sidebar").classList.toggle("hide");document.querySelector(".main").classList.toggle("full");}
function openModal(id){ document.getElementById(id).classList.add('open'); }
function closeModal(id){ document.getElementById(id).classList.remove('open'); }
function openLogout(){ document.getElementById('logoutModal').style.display='flex'; }
function closeLogout(){ document.getElementById('logoutModal').style.display='none'; }
document.querySelectorAll('.modal-overlay').forEach(o=>{
  o.addEventListener('click',e=>{ if(e.target===o) o.classList.remove('open'); });
});
<?php if($pesan): list($t)=explode('|',$pesan); if($t=='sukses'): ?>
setTimeout(()=>{ document.querySelector('.alert')?.remove(); }, 3000);
<?php endif; endif; ?>
</script>
</body>
</html>

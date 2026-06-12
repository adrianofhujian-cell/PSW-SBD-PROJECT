<?php
session_start();

if(!isset($_SESSION['admin']))
{
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Larris Admin</title>

  <!-- FONT -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- ICON -->
  <link rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

  <link rel="stylesheet" href="Laporan.css">
</head>

<body>

<div class="dashboard">

  <!-- SIDEBAR -->
  <div class="sidebar" id="sidebar">

    <div>

      <div class="logo">
        <h2>Larris</h2>
        <p>Admin Panel</p>
      </div>

      <ul class="menu">

  <li class="active">
  <a href="Dashboard.php">
    Dashboard
  </a>
</li>
  </li>

  <li>
    <a href="Produk.php">
      Produk
    </a>
  </li>

  <li>
    <a href="Kategori.php">
      Kategori
    </a>
  </li>

  <li>
    <a href="Stok.php">
      Stok
    </a>
  </li>

  <li>
    <a href="Pesanan.php">
      Pesanan
    </a>
  </li>

  <li>
    <a href="Transaksi.php">
      Transaksi
    </a>
  </li>

  <li>
    <a href="Laporan.php">
      Laporan
    </a>
  </li>

  <li>
    <a href="Pengguna.php">>
      Pengguna
    </a>
  </li>

  <li>
    <a href="Pengaturan.php">
      Pengaturan
    </a>
  </li>

</ul>

    </div>

    <button class="logout-btn" onclick="openLogout()">
    <i class="fa-solid fa-right-from-bracket"></i>
    Logout
</button>

  </div>

  <!-- MAIN -->
  <div class="main">

    <!-- TOPBAR -->
    <div class="topbar">

      <div class="top-left">

        <i class="fa-solid fa-bars menu-btn"
        onclick="toggleSidebar()"></i>

        <h2>Dashboard</h2>

      </div>

      <div class="top-right">

        <i class="fa-regular fa-bell"></i>

        <div class="admin-profile">

          <div class="profile-circle">
            A
          </div>

          <div>
            <h4>Admin Larris</h4>
          </div>

        </div>

      </div>

    </div>

    <!-- WELCOME -->
    <div class="welcome-box">

      <h1>Selamat datang, Admin Larris!</h1>
      <p>Kelola jajanan kantin dengan mudah.</p>

    </div>

    <!-- CARDS -->
    <div class="cards">

      <div class="card">

        <div class="card-icon">
          <i class="fa-regular fa-square"></i>
        </div>

        <div>
          <p>Total Produk</p>
          <h2>45</h2>
          <span>+3 dari kemarin</span>
        </div>

      </div>

      <div class="card">

        <div class="card-icon">
          <i class="fa-solid fa-triangle-exclamation"></i>
        </div>

        <div>
          <p>Stok Menipis</p>
          <h2>8</h2>
          <span>Perlu restock</span>
        </div>

      </div>

      <div class="card">

        <div class="card-icon">
          <i class="fa-solid fa-cart-shopping"></i>
        </div>

        <div>
          <p>Pesanan Hari Ini</p>
          <h2>23</h2>
          <span>+5 dari kemarin</span>
        </div>

      </div>

      <div class="card">

        <div class="card-icon">
          <i class="fa-solid fa-sack-dollar"></i>
        </div>

        <div>
          <p>Total Penjualan</p>
          <h2>Rp 350.000</h2>
          <span>Hari ini</span>
        </div>

      </div>

    </div>



    <!-- BOTTOM -->
    <div class="bottom-grid">
        <!-- PESANAN -->
<div class="table-box">

  <div class="box-header">
    <h3>Pesanan Terbaru</h3>
    <button>Lihat semua</button>
  </div>

  <table>

    <tr>
      <th>No</th>
      <th>ID Pesanan</th>
      <th>Pelanggan</th>
      <th>Total</th>
      <th>Status</th>
      <th>Waktu</th>
    </tr>

    <tr>
      <td>1</td>
      <td>#PSN-0023</td>
      <td>Siswa Kelas 10A</td>
      <td>Rp 15.000</td>
      <td><span class="status selesai">Selesai</span></td>
      <td>10:30</td>
    </tr>

    <tr>
      <td>2</td>
      <td>#PSN-0022</td>
      <td>Siswa Kelas 11B</td>
      <td>Rp 12.000</td>
      <td><span class="status proses">Diproses</span></td>
      <td>10:15</td>
    </tr>

    <tr>
      <td>3</td>
      <td>#PSN-0021</td>
      <td>Siswa Kelas 12</td>
      <td>Rp 18.000</td>
      <td><span class="status batal">Dibatalkan</span></td>
      <td>09:50</td>
    </tr>

  </table>

</div>

<!-- STOK -->
<div class="stock-box">

  <div class="box-header">
    <h3>Stok Menipis</h3>
    <button>Lihat semua</button>
  </div>

  <div class="product-item">
    <span>Cilok</span>
    <span>Stok: 5</span>
  </div>

  <div class="product-item">
    <span>Sosis Bakar</span>
    <span>Stok: 7</span>
  </div>

  <div class="product-item">
    <span>Pudding</span>
    <span>Stok: 3</span>
  </div>

</div>

    </div>

  </div>

</div>
<div class="logout-modal" id="logoutModal">

    <div class="logout-box">

        <h2>Logout</h2>

        <p>Yakin ingin keluar dari dashboard?</p>

        <div class="logout-action">

            <button class="cancel-btn"
            onclick="closeLogout()">
                Batal
            </button>

            <a href="logout.php" class="confirm-btn">
                Ya, Logout
            </a>

        </div>

    </div>

</div>

<script>
window.onload = function(){

    window.openLogout = function(){
        document.getElementById("logoutModal").style.display = "flex";
    }

    window.closeLogout = function(){
        document.getElementById("logoutModal").style.display = "none";
    }

    window.toggleSidebar = function(){
        document.getElementById("sidebar").classList.toggle("hide");
        document.querySelector(".main").classList.toggle("full");
    }

}
</script>

</body>
</html>
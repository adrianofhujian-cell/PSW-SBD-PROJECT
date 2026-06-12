<?php
session_start();

if(!isset($_SESSION['admin']))
{
    header("Location: login.php");
    exit;
}

$page = "Laporan";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan - Admin Larris</title>

  <!-- FONT -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- ICON -->
  <link rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

  <link rel="stylesheet" href="laporan.css">
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

  <li>
    <a href="Dashboard.php">
      Dashboard
    </a>
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

  <li class="active">
    <a href="Laporan.php">
      Laporan
    </a>
  </li>

  <li>
    <a href="Pengguna.php">
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

    <button class="logout-btn">
      <i class="fa-solid fa-right-from-bracket"></i>
      Logout
    </button>

  </div>

  <!-- MAIN -->
  <div class="main">

    <!-- TOPBAR -->
    <div class="topbar">

      <div class="top-left">
        <i class="fa-solid fa-bars menu-btn" onclick="toggleSidebar()"></i>
        <h2>Laporan</h2>
      </div>

      <div class="top-right">

        <div class="notif">
          <i class="fa-regular fa-bell"></i>
          <span>3</span>
        </div>

        <div class="admin-profile">

          <div class="profile-circle">
            A
          </div>

          <h4>Admin Larris</h4>

        </div>

      </div>

    </div>

    <!-- HEADER -->
    <div class="page-header">

      <div>
        <h1>Laporan</h1>
        <p>Pantau ringkasan dan performa penjualan kantin</p>
      </div>

      <button class="export-btn">
        <i class="fa-solid fa-download"></i>
        Export Laporan
      </button>

    </div>

    <!-- STATS -->
    <div class="stats-grid">

      <div class="stat-card">
        <div class="icon green">
          <i class="fa-solid fa-bag-shopping"></i>
        </div>

        <div>
          <p>Total Penjualan</p>
          <h2>Rp 2.450.000</h2>
          <span>↗ 18.5% dari periode lalu</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="icon orange">
          <i class="fa-solid fa-cart-shopping"></i>
        </div>

        <div>
          <p>Total Transaksi</p>
          <h2>124</h2>
          <span>↗ 15.3% dari periode lalu</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="icon purple">
          <i class="fa-solid fa-cube"></i>
        </div>

        <div>
          <p>Total Produk Terjual</p>
          <h2>356</h2>
          <span>↗ 12.8% dari periode lalu</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="icon blue">
          <i class="fa-solid fa-users"></i>
        </div>

        <div>
          <p>Total Pelanggan</p>
          <h2>98</h2>
          <span>↗ 10.6% dari periode lalu</span>
        </div>
      </div>

    </div>

    <!-- FILTER -->
    <div class="filter-box">

      <div class="filter-item">
        <label>Pilih Periode</label>

        <div class="filter-input">
          <i class="fa-regular fa-calendar"></i>
          <span>20 Mei 2024 - 26 Mei 2024</span>
          <i class="fa-solid fa-chevron-down"></i>
        </div>
      </div>

      <div class="filter-item">
        <label>Tipe Laporan</label>

        <div class="filter-input">
          <span>Semua Laporan</span>
          <i class="fa-solid fa-chevron-down"></i>
        </div>
      </div>

      <div class="filter-item">
        <label>Kategori</label>

        <div class="filter-input">
          <span>Semua Kategori</span>
          <i class="fa-solid fa-chevron-down"></i>
        </div>
      </div>

      <button class="filter-btn">
        <i class="fa-solid fa-filter"></i>
        Filter
      </button>

      <button class="reset-btn">
        <i class="fa-solid fa-rotate-left"></i>
        Reset
      </button>

    </div>

    <!-- CHART -->
    <div class="chart-grid">

      <!-- LEFT -->
      <div class="chart-card">

        <div class="chart-header">
          <h3>Grafik Penjualan</h3>

          <button>Per Hari</button>
        </div>

        <div class="fake-chart">

          <div class="line-chart">
            <div class="dot d1"></div>
            <div class="dot d2"></div>
            <div class="dot d3"></div>
            <div class="dot d4"></div>
            <div class="dot d5"></div>
            <div class="dot d6"></div>
            <div class="dot d7"></div>
          </div>

        </div>

      </div>

      <!-- RIGHT -->
      <div class="pie-card">

        <div class="chart-header">
          <h3>Penjualan per Kategori</h3>

          <button>Periode ini</button>
        </div>

        <div class="pie-content">

          <div class="pie-chart"></div>

          <div class="pie-list">

            <div class="pie-item">
              <span class="circle green-bg"></span>
              Makanan
              <strong>45%</strong>
            </div>

            <div class="pie-item">
              <span class="circle blue-bg"></span>
              Minuman
              <strong>30%</strong>
            </div>

            <div class="pie-item">
              <span class="circle purple-bg"></span>
              Snack
              <strong>15%</strong>
            </div>

            <div class="pie-item">
              <span class="circle yellow-bg"></span>
              Dessert
              <strong>7%</strong>
            </div>

            <div class="pie-item">
              <span class="circle gray-bg"></span>
              Lainnya
              <strong>3%</strong>
            </div>

          </div>

        </div>

      </div>

    </div>

    <!-- BOTTOM -->
    <div class="bottom-grid">

      <!-- TABLE -->
      <div class="table-card">

        <div class="table-header">
          <h3>Ringkasan Penjualan</h3>
        </div>

        <table>

          <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>Total Transaksi</th>
            <th>Total Penjualan</th>
            <th>Produk Terjual</th>
            <th>Rata-rata per Transaksi</th>
            <th>Aksi</th>
          </tr>

          <tr>
            <td>1</td>
            <td>20 Mei 2024</td>
            <td>12</td>
            <td>Rp 280.000</td>
            <td>42</td>
            <td>Rp 23.333</td>
            <td>
              <button class="view-btn">
                <i class="fa-regular fa-eye"></i>
              </button>
            </td>
          </tr>

          <tr>
            <td>2</td>
            <td>21 Mei 2024</td>
            <td>18</td>
            <td>Rp 320.000</td>
            <td>48</td>
            <td>Rp 17.778</td>
            <td>
              <button class="view-btn">
                <i class="fa-regular fa-eye"></i>
              </button>
            </td>
          </tr>

          <tr>
            <td>3</td>
            <td>22 Mei 2024</td>
            <td>22</td>
            <td>Rp 450.000</td>
            <td>63</td>
            <td>Rp 20.455</td>
            <td>
              <button class="view-btn">
                <i class="fa-regular fa-eye"></i>
              </button>
            </td>
          </tr>

          <tr>
            <td>4</td>
            <td>23 Mei 2024</td>
            <td>16</td>
            <td>Rp 380.000</td>
            <td>51</td>
            <td>Rp 23.750</td>
            <td>
              <button class="view-btn">
                <i class="fa-regular fa-eye"></i>
              </button>
            </td>
          </tr>

          <tr>
            <td>5</td>
            <td>24 Mei 2024</td>
            <td>25</td>
            <td>Rp 550.000</td>
            <td>72</td>
            <td>Rp 22.000</td>
            <td>
              <button class="view-btn">
                <i class="fa-regular fa-eye"></i>
              </button>
            </td>
          </tr>

        </table>

      </div>

      <!-- REPORT -->
      <div class="report-card">

        <h3>Laporan Populer</h3>

        <div class="report-item">
          <div>
            <h4>Laporan Penjualan</h4>
            <p>Detail penjualan per periode</p>
          </div>

          <i class="fa-solid fa-chevron-right"></i>
        </div>

        <div class="report-item">
          <div>
            <h4>Laporan Produk</h4>
            <p>Produk terlaris dan tidak terjual</p>
          </div>

          <i class="fa-solid fa-chevron-right"></i>
        </div>

        <div class="report-item">
          <div>
            <h4>Laporan Stok</h4>
            <p>Stok menipis dan stok habis</p>
          </div>

          <i class="fa-solid fa-chevron-right"></i>
        </div>

        <div class="report-item">
          <div>
            <h4>Laporan Pelanggan</h4>
            <p>Pelanggan baru dan repeat order</p>
          </div>

          <i class="fa-solid fa-chevron-right"></i>
        </div>

        <button class="all-report-btn">
          Lihat Semua Laporan
        </button>

      </div>

    </div>

  </div>

</div>

<script>
function toggleSidebar(){
  document.getElementById("sidebar").classList.toggle("hide");
  document.querySelector(".main").classList.toggle("full");
}
</script>

</body>
</html>
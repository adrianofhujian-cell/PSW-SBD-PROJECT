<?php
/* proses_checkout.php — terima pesanan dari cart.php (JSON) lalu simpan ke DB.
   Skema mengikuti tabel admin: pesanan(nama_pelanggan, tanggal_pesanan, total, status)
   dan transaksi(id_pesanan, tanggal_transaksi, total, metode_pembayaran, status). */

include "../Config/config.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
  echo json_encode(["sukses"=>false, "pesan"=>"Data tidak diterima"]); exit;
}

$nama    = mysqli_real_escape_string($koneksi, $data['nama_pelanggan'] ?? 'Customer');
$total   = (int)($data['total'] ?? 0);
$catatan = mysqli_real_escape_string($koneksi, $data['catatan'] ?? '');
$items   = $data['items'] ?? [];
$tgl     = date('Y-m-d H:i:s');

/* 1. Simpan pesanan (status awal: pending agar muncul di dashboard admin) */
$sql = "INSERT INTO pesanan (nama_pelanggan, tanggal_pesanan, total, status)
        VALUES ('$nama', '$tgl', $total, 'pending')";
if (!mysqli_query($koneksi, $sql)) {
  echo json_encode(["sukses"=>false, "pesan"=>"Gagal simpan pesanan: ".mysqli_error($koneksi)]); exit;
}
$id_pesanan = mysqli_insert_id($koneksi);

/* 2. Simpan transaksi terkait */
$sql2 = "INSERT INTO transaksi (id_pesanan, tanggal_transaksi, total, metode_pembayaran, status)
         VALUES ($id_pesanan, '$tgl', $total, 'tunai', 'pending')";
mysqli_query($koneksi, $sql2);

/* 3. (opsional) simpan rincian item ke tabel detail_pesanan bila tabelnya ada.
      Aman: kalau tabel belum dibuat, baris ini diabaikan tanpa error fatal. */
$cek = mysqli_query($koneksi, "SHOW TABLES LIKE 'detail_pesanan'");
if ($cek && mysqli_num_rows($cek) > 0) {
  foreach ($items as $it) {
    $f = mysqli_real_escape_string($koneksi, $it['food'] ?? '');
    $v = mysqli_real_escape_string($koneksi, $it['variant'] ?? '');
    $a = mysqli_real_escape_string($koneksi, implode(', ', $it['addons'] ?? []));
    $h = (int)($it['price'] ?? 0);
    $q = (int)($it['qty'] ?? 1);
    mysqli_query($koneksi,
      "INSERT INTO detail_pesanan (id_pesanan, nama_produk, varian, addon, harga, qty)
       VALUES ($id_pesanan, '$f', '$v', '$a', $h, $q)");
  }
}

echo json_encode([
  "sukses"     => true,
  "id_pesanan" => $id_pesanan,
  "pesan"      => "Pesanan berhasil dikirim!"
]);
?>

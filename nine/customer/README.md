# Larris — Folder `customer` (Revisi sesuai Wireframe)

Folder ini menggantikan folder `nine/customer/` lama. Tinggal **timpa** folder
`customer` di proyek kamu dengan folder ini, lalu jalankan lewat XAMPP/Laragon.

## Cara pakai
1. Salin folder `customer/` ini ke dalam proyek (di samping folder `Admin/` dan `Config/`).
2. Pastikan database `kantin_db` aktif dan `Config/config.php` sudah benar.
3. Buka di browser: `http://localhost/nine/customer/index.php`
4. (Opsional) jalankan `larris_optional.sql` di phpMyAdmin untuk fitur tambahan
   (kolom mahasiswa di halaman daftar + tabel rincian item pesanan).

## Isi halaman (sesuai wireframe)
| File | Wireframe | Keterangan |
|------|-----------|-----------|
| `index.php` | Home | Hero "Larris" + tombol Order Now |
| `menu.php` | Menu | Grid 12 kategori |
| `detail.php` | Detail tiap menu | Varian (pilih 1), Add-on (boleh banyak), Paket Pilih-4 |
| `cart.php` | Cart | Qty +/-, hapus, notes, subtotal, checkout |
| `selesai.php` | Order Completed | Tampil setelah checkout berhasil |
| `About.php` | About Us | Information / Location / Contact Us |
| `login.php` | Login/Daftar | Name, NPM, Major, Phone, Password |
| `proses_checkout.php` | — | Menyimpan pesanan ke tabel `pesanan` + `transaksi` |
| `menu_data.php` | — | **Sumber data semua menu** (ubah harga/menu di sini) |

## Cara mengubah / menambah menu
Semua menu ada di **`menu_data.php`**. Tinggal tambah/ubah item, varian, add-on,
atau harga di situ — `menu.php` dan `detail.php` otomatis mengikuti.

## Foto menu
- Folder `images/` sudah berisi **12 foto asli** untuk tiap menu
  (siomay, batagor, pempek, model, pangsit, basreng, tekwan, tahu_walik,
  cireng, seblak, tahu_kocek, maklor) + `logo.jpg` (logo RR Larris) dan
  `hero.jpg` (lingkaran di halaman Home).
- Mau ganti foto? Cukup timpa file dengan nama yang sama, mis. `images/siomay.jpg`.

## Alur data
Cart disimpan di `localStorage` browser (multi-item). Saat **Checkout**,
data dikirim ke `proses_checkout.php` → masuk ke tabel `pesanan` (status
`pending`) sehingga langsung muncul di dashboard Admin untuk diproses.

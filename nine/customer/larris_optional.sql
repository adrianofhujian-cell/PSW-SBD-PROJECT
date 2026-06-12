-- ============================================================
--  Larris — SQL OPSIONAL (jalankan kalau mau fitur tambahan)
--  Database: kantin_db
-- ============================================================

-- 1) Kolom tambahan untuk data mahasiswa di halaman login/daftar.
--    Tanpa ini pun login.php tetap jalan (NPM disimpan di kolom username).
ALTER TABLE pengguna
  ADD COLUMN npm   VARCHAR(20)  NULL AFTER username,
  ADD COLUMN major VARCHAR(100) NULL AFTER email,
  ADD COLUMN no_hp VARCHAR(20)  NULL AFTER major;

-- 2) Tabel rincian item per pesanan.
--    Kalau tabel ini ada, proses_checkout.php otomatis menyimpan tiap item
--    (nama produk, varian, add-on, harga, qty) ke sini.
CREATE TABLE IF NOT EXISTS detail_pesanan (
  id_detail   INT AUTO_INCREMENT PRIMARY KEY,
  id_pesanan  INT NOT NULL,
  nama_produk VARCHAR(150),
  varian      VARCHAR(150),
  addon       VARCHAR(255),
  harga       INT,
  qty         INT,
  FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE
);

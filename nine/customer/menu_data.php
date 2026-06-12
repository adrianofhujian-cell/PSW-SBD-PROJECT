<?php
/* =====================================================================
   menu_data.php  —  SUMBER DATA MENU LARRIS (sesuai wireframe)
   ---------------------------------------------------------------------
   Semua kategori, varian, add-on, dan paket didefinisikan di sini.
   Diambil oleh menu.php (grid) dan detail.php (halaman detail).

   Struktur tiap kategori:
     'nama'   => judul kategori
     'gambar' => path foto
     'sections' => daftar bagian, tiap bagian:
        type  : 'variant' (pilih 1 / radio) | 'addon' (boleh banyak / checkbox)
                | 'combo' (paket pilih-N harga tetap)
        title : judul bagian (boleh null)
        max   : (khusus combo) jumlah maksimal yang boleh dipilih
        price : (khusus combo) harga paket tetap
        items : daftar item -> ['nama'=>..., 'harga'=>int]
   ===================================================================== */

function larris_menu() {
  return [

    'siomay' => [
      'nama' => 'Siomay',
      'gambar' => 'images/siomay.jpg',
      'sections' => [
        ['type'=>'variant', 'title'=>null, 'items'=>[
          ['nama'=>'Siomay',             'harga'=>18000],
          ['nama'=>'Siomay Jumbo',       'harga'=>23000],
          ['nama'=>'Siomay Mix Batagor', 'harga'=>18000],
        ]],
        ['type'=>'addon', 'title'=>'Add on', 'items'=>[
          ['nama'=>'Telur Ceplok', 'harga'=>4000],
          ['nama'=>'Telur Rebus',  'harga'=>4000],
          ['nama'=>'Telur Dadar',  'harga'=>4000],
        ]],
      ],
    ],

    'batagor' => [
      'nama' => 'Batagor',
      'gambar' => 'images/batagor.jpg',
      'sections' => [
        ['type'=>'variant', 'title'=>'Batagor Kering', 'items'=>[
          ['nama'=>'Batagor Kering - Medium', 'harga'=>15000],
          ['nama'=>'Batagor Kering - Large',  'harga'=>18000],
          ['nama'=>'Batagor Kering - Jumbo',  'harga'=>23000],
        ]],
        ['type'=>'variant', 'title'=>'Batagor Indomie', 'items'=>[
          ['nama'=>'Batagor Indomie - Medium', 'harga'=>20000],
          ['nama'=>'Batagor Indomie - Jumbo',  'harga'=>26000],
        ]],
        ['type'=>'variant', 'title'=>null, 'items'=>[
          ['nama'=>'Batagor Kuah', 'harga'=>18000],
        ]],
      ],
    ],

    'pempek' => [
      'nama' => 'Pempek',
      'gambar' => 'images/pempek.jpg',
      'sections' => [
        ['type'=>'variant', 'title'=>null, 'items'=>[
          ['nama'=>'Pempek Kapal Selam',  'harga'=>18000],
          ['nama'=>'Pempek Lenjer Besar', 'harga'=>23000],
          ['nama'=>'Pempek Lenggang',     'harga'=>18000],
          ['nama'=>'Pempek Campur Kecil', 'harga'=>4000],
          ['nama'=>'Pempek Campur Besar', 'harga'=>4000],
          ['nama'=>'Paket Pilih 4',       'harga'=>15000],
        ]],
        ['type'=>'combo', 'title'=>'Isi Paket Pilih 4', 'max'=>4, 'items'=>[
          ['nama'=>'Pempek Telor Kecil', 'harga'=>0],
          ['nama'=>'Pempek Adaan',       'harga'=>0],
          ['nama'=>'Pempek Lenjer Kecil','harga'=>0],
          ['nama'=>'Pempek Keriting',    'harga'=>0],
          ['nama'=>'Pempek Isi Tahu',    'harga'=>0],
        ]],
      ],
    ],

    'model' => [
      'nama' => 'Model',
      'gambar' => 'images/model.jpg',
      'sections' => [
        ['type'=>'variant', 'title'=>null, 'items'=>[
          ['nama'=>'Model', 'harga'=>18000],
        ]],
      ],
    ],

    'pangsit' => [
      'nama' => 'Pangsit',
      'gambar' => 'images/pangsit.jpg',
      'sections' => [
        ['type'=>'variant', 'title'=>null, 'items'=>[
          ['nama'=>'Pangsit Ayam Kuah Seblak Original', 'harga'=>23000],
          ['nama'=>'Pangsit Ayam Kuah Seblak Komplit (Mie, Dumpling, Sosis)', 'harga'=>28000],
        ]],
      ],
    ],

    'basreng' => [
      'nama' => 'Basreng',
      'gambar' => 'images/basreng.jpg',
      'sections' => [
        ['type'=>'variant', 'title'=>null, 'items'=>[
          ['nama'=>'Basreng Chilli Oil', 'harga'=>15000],
        ]],
      ],
    ],

    'tekwan' => [
      'nama' => 'Tekwan',
      'gambar' => 'images/tekwan.jpg',
      'sections' => [
        ['type'=>'variant', 'title'=>null, 'items'=>[
          ['nama'=>'Tekwan',         'harga'=>18000],
          ['nama'=>'Tekwan Indomie', 'harga'=>23000],
        ]],
      ],
    ],

    'tahu_walik' => [
      'nama' => 'Tahu Walik',
      'gambar' => 'images/tahu_walik.jpg',
      'sections' => [
        ['type'=>'variant', 'title'=>null, 'items'=>[
          ['nama'=>'Tahu Walik', 'harga'=>14000],
        ]],
      ],
    ],

    'cireng' => [
      'nama' => 'Cireng',
      'gambar' => 'images/cireng.jpg',
      'sections' => [
        ['type'=>'variant', 'title'=>null, 'items'=>[
          ['nama'=>'Cireng Pedas Isi Ayam',     'harga'=>15000],
          ['nama'=>'Cireng Pedas Kuah Kacang',  'harga'=>17000],
          ['nama'=>'Cireng Kuah Seblak',        'harga'=>23000],
        ]],
      ],
    ],

    'seblak' => [
      'nama' => 'Seblak',
      'gambar' => 'images/seblak.jpg',
      'sections' => [
        ['type'=>'variant', 'title'=>null, 'items'=>[
          ['nama'=>'Seblak Original', 'harga'=>15000],
        ]],
        ['type'=>'addon', 'title'=>'Add on', 'items'=>[
          ['nama'=>'Cuanki Lidah',  'harga'=>4000],
          ['nama'=>'Batagor Kering','harga'=>3000],
          ['nama'=>'Pilus Cikur',   'harga'=>3000],
          ['nama'=>'Extra Pedas',   'harga'=>3000],
        ]],
      ],
    ],

    'tahu_kocek' => [
      'nama' => 'Tahu Kocek atau Telor',
      'gambar' => 'images/tahu_kocek.jpg',
      'sections' => [
        ['type'=>'variant', 'title'=>null, 'items'=>[
          ['nama'=>'Tahu Kocek Sambal Geprek Original', 'harga'=>15000],
          ['nama'=>'Tahu Kocek Sambal Geprek Komplit',  'harga'=>20000],
          ['nama'=>'Tahu Telor',                        'harga'=>18000],
        ]],
      ],
    ],

    'maklor' => [
      'nama' => 'Maklor',
      'gambar' => 'images/maklor.jpg',
      'sections' => [
        ['type'=>'variant', 'title'=>null, 'items'=>[
          ['nama'=>'Maklor (Makaroni Telor)', 'harga'=>15000],
        ]],
      ],
    ],

  ];
}
?>

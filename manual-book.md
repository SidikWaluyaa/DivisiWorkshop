# 📘 User Manual Book — Divisi Gudang
## Sistem Shoe Workshop

**Versi Dokumen:** 1.0  
**Tanggal:** 27 Juli 2026  
**Disusun oleh:** Tim Pengembang Shoe Workshop

---

## Daftar Isi

1. [Pendahuluan & Peran Pengguna](#1-pendahuluan--peran-pengguna)
2. [Modul Belanja Gudang](#2-modul-belanja-gudang)
3. [Modul Barang Keluar](#3-modul-barang-keluar)
4. [Modul Riwayat Mutasi](#4-modul-riwayat-mutasi)
5. [Modul Penyimpanan Rak dan Manajemen Rak](#5-modul-penyimpanan-rak-dan-manajemen-rak)
6. [Modul Riwayat Pengambilan](#6-modul-riwayat-pengambilan)
7. [Modul Penerimaan dan Form QC](#7-modul-penerimaan-dan-form-qc)
8. [Modul Assessment](#8-modul-assessment)
9. [Modul Manifest](#9-modul-manifest)
10. [Modul Finish / Selesai](#10-modul-finish--selesai)
11. [Modul Pengiriman](#11-modul-pengiriman)
12. [Modul Manajemen Material](#12-modul-manajemen-material)

---

## 1. Pendahuluan & Peran Pengguna

### 1.1 Tentang Dokumen Ini

Dokumen ini merupakan panduan penggunaan resmi untuk **Divisi Gudang** pada sistem **Shoe Workshop**. Panduan ini ditujukan kepada seluruh staf gudang yang bertanggung jawab atas penerimaan barang, penyimpanan, pengecekan kualitas (QC), pengelolaan material, hingga pengiriman sepatu kepada pelanggan.

### 1.2 Peran Pengguna: Admin Gudang

Pengguna yang mengakses modul-modul di dalam Divisi Gudang memiliki peran **Admin Gudang**. Peran ini memiliki hak akses untuk:

| Hak Akses | Keterangan |
|---|---|
| Belanja Gudang | Mencatat pembelian material masuk dari vendor |
| Barang Keluar | Mendistribusikan material ke workshop |
| Riwayat Mutasi | Memantau seluruh log pergerakan stok material |
| Penyimpanan Rak | Mengelola lokasi penyimpanan sepatu pelanggan |
| Penerimaan & QC | Menerima sepatu pelanggan dan melakukan pemeriksaan awal |
| Assessment | Mengelola antrian pengecekan sepatu yang masuk |
| Manifest | Membuat batch pengiriman sepatu ke workshop |
| Finish / Selesai | Mengelola sepatu yang telah selesai diproses |
| Pengiriman | Mengatur logistik pengiriman kepada pelanggan |
| Manajemen Material | Mengelola data master material (stok, harga, tipe) |

### 1.3 Cara Mengakses Divisi Gudang

1. Login ke sistem Shoe Workshop menggunakan akun **Admin Gudang**.
2. Pada sidebar (panel navigasi kiri), cari bagian bertanda **DIVISI GUDANG**.
3. Klik menu yang ingin Anda akses (misalnya: *Stok Material*, *Belanja Gudang*, *Barang Keluar*, dll.).

### 1.4 Istilah Umum

| Istilah | Penjelasan |
|---|---|
| **SPK** | Surat Perintah Kerja — nomor unik yang diberikan kepada setiap pesanan sepatu pelanggan |
| **WH-IN** | Kode dokumen Warehouse-IN (Barang Masuk / Belanja Gudang) |
| **WH-OUT** | Kode dokumen Warehouse-OUT (Barang Keluar ke Workshop) |
| **QC** | Quality Control — pemeriksaan kualitas dan kelengkapan barang |
| **Manifest** | Dokumen batch pengiriman yang mengelompokkan beberapa item dalam satu kiriman |
| **Rak Inbound** | Rak transit untuk menyimpan sepatu yang baru diterima sebelum diproses |
| **Fast Track** | Prioritas pengerjaan cepat untuk pesanan mendesak |

---

## 2. Modul Belanja Gudang

### 2.1 Halaman Daftar Belanja

![Daftar Belanja Gudang](./Gudang/01_warehouse_purchase.jpeg)

**Tujuan:** Modul ini digunakan untuk mencatat dan memantau seluruh transaksi pembelian material yang masuk ke gudang dari vendor atau pemasok. Setiap pembelian akan tercatat sebagai dokumen **WH-IN** (Warehouse In).

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Total Transaksi** | Jumlah seluruh nota belanja yang pernah dibuat |
| **Menunggu Selesai** | Jumlah nota belanja yang masih berstatus *Pending* |
| **Total Nilai Selesai** | Akumulasi total rupiah dari seluruh nota belanja yang sudah selesai |
| **Kolom Pencarian** | Mencari berdasarkan nomor belanja, SPK, atau nama vendor |
| **Filter Tipe** | Menyaring transaksi berdasarkan tipe (Reguler, dll.) |

**Tabel Daftar Belanja menampilkan:**
- **Info Belanja:** Nomor dokumen (contoh: `WH-IN-20260725-001`) beserta tanggal transaksi.
- **Workflow:** Tipe prioritas (Reguler/Fast Track) dan status penyelesaian.
- **Target SPK:** Nomor SPK yang dituju oleh pembelian ini.
- **Financial:** Total nilai rupiah dan jumlah item material.
- **Manajemen:** Aksi kelola (edit, hapus, lihat detail).

**Langkah-Langkah Melihat Daftar Belanja:**

1. Pada sidebar, klik menu **Belanja Gudang** di bawah bagian *Divisi Gudang*.
2. Halaman **Manajemen Belanja** akan terbuka, menampilkan ringkasan statistik di bagian atas dan daftar transaksi di bawahnya.
3. Gunakan **kolom pencarian** untuk menemukan nota belanja tertentu berdasarkan nomor dokumen, SPK, atau nama vendor.
4. Gunakan dropdown **Semua Tipe** untuk menyaring transaksi berdasarkan tipe workflow.
5. Klik baris transaksi untuk melihat detail lengkapnya.

---

### 2.2 Membuat Nota Belanja Baru

![Form Baru Belanja](./Gudang/02_warehouse_purchase_create.jpeg)

**Tujuan:** Form ini digunakan untuk membuat nota belanja baru guna mencatat pembelian material dari vendor. Setiap nota belanja baru akan otomatis mendapatkan nomor dokumen unik (contoh: `WH-IN-20260727-001`).

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Nota Vendor** | Nomor nota/faktur dari vendor (opsional) |
| **Tanggal** | Tanggal transaksi pembelian |
| **Prioritas** | Tingkat prioritas pembelian (Reguler / Fast Track) |
| **Status** | Status nota (Pending / Selesai) |
| **Grup SPK** | Kelompok SPK yang dituju oleh pembelian ini |
| **+ Pilih Material** | Tombol untuk menambahkan item material ke dalam nota |
| **Grand Total** | Total keseluruhan nilai rupiah dari seluruh material yang dipilih |

**Langkah-Langkah Membuat Nota Belanja Baru:**

1. Pada halaman Daftar Belanja, klik tombol **+ Tambah Belanja** (warna hijau, pojok kanan atas).
2. Sistem akan membuka form **Baru Belanja** dengan nomor dokumen yang sudah terisi otomatis.
3. Isi kolom **Nota Vendor** dengan nomor faktur dari pemasok (jika ada).
4. Atur **Tanggal** transaksi pembelian.
5. Pilih **Prioritas** (Reguler atau Fast Track).
6. Pada bagian **Grup SPK**, klik tombol **+ Grup SPK** untuk menambahkan grup SPK tujuan, lalu ketikkan nomor SPK pada kolom yang tersedia.
7. Klik tombol **+ Pilih Material** untuk menambahkan material yang dibeli.
8. Isi kolom **Qty** (jumlah) dan **Harga** untuk setiap material yang dipilih. Sistem akan menghitung **Subtotal** secara otomatis.
9. Periksa kembali **Grand Total** di bagian bawah layar.
10. Klik tombol **Simpan** untuk menyimpan nota belanja.

> **Catatan Penting:** Nomor dokumen belanja (contoh: `WH-IN-20260727-001`) dibuat secara otomatis oleh sistem dan tidak dapat diubah.

---

## 3. Modul Barang Keluar

### 3.1 Halaman Daftar Barang Keluar

![Daftar Barang Keluar](./Gudang/03_warehouse_disbursement.jpeg)

**Tujuan:** Modul ini digunakan untuk mencatat distribusi material dari gudang ke workshop produksi. Setiap pengeluaran barang akan tercatat sebagai dokumen **WH-OUT** (Warehouse Out).

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Total Transaksi** | Jumlah seluruh dokumen barang keluar yang pernah dibuat |
| **Draft / Request** | Jumlah dokumen yang masih dalam status Draft atau Request |
| **Total Nilai Keluar** | Akumulasi total estimasi harga material yang telah dikeluarkan |
| **Kolom Pencarian** | Mencari berdasarkan nomor pengeluaran, SPK, atau catatan |

**Tabel Daftar Barang Keluar menampilkan:**
- **Dokumen Keluar:** Nomor dokumen (contoh: `WH-OUT-20260727-001`).
- **Status:** Status distribusi (Request / Completed).
- **Workshop / SPK:** Nomor SPK tujuan distribusi.
- **Financial:** Estimasi total nilai material yang dikeluarkan.
- **Manajemen:** Aksi kelola.

**Langkah-Langkah Melihat Daftar Barang Keluar:**

1. Pada sidebar, klik menu **Barang Keluar** di bawah bagian *Divisi Gudang*.
2. Halaman **Barang Keluar** akan terbuka dengan tagline "Distribusi Material ke Workshop".
3. Gunakan **kolom pencarian** untuk menemukan dokumen pengeluaran tertentu.
4. Klik baris transaksi untuk melihat detail lengkapnya.

---

### 3.2 Membuat Dokumen Barang Keluar Baru

![Form Baru Barang Keluar](./Gudang/04_warehouse_disbursement_create.jpeg)

**Tujuan:** Form ini digunakan untuk mencatat material apa saja yang akan dikeluarkan dari gudang dan didistribusikan ke workshop produksi untuk mengerjakan pesanan pelanggan.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **No. Referensi (Opsional)** | Nomor referensi internal tambahan |
| **Tanggal Keluar** | Tanggal pengeluaran material dari gudang |
| **Status Distribusi** | Status dokumen: *Request* (permintaan) atau *Selesai* (sudah dikeluarkan) |
| **Grup SPK Workshop** | Kelompok SPK yang membutuhkan material ini |
| **+ Pilih Material** | Tombol untuk menambahkan material yang akan dikeluarkan |
| **Jumlah Keluar** | Kuantitas material yang dikeluarkan untuk tiap item |
| **Est. Harga** | Estimasi harga material yang dikeluarkan |

**Langkah-Langkah Membuat Dokumen Barang Keluar:**

1. Pada halaman Daftar Barang Keluar, klik tombol **+ Tambah Pengeluaran** (warna hijau, pojok kanan atas).
2. Sistem akan membuka form **Baru Barang Keluar** dengan nomor dokumen otomatis (contoh: `WH-OUT-20260727-001`).
3. Isi **No. Referensi** jika diperlukan (opsional).
4. Atur **Tanggal Keluar**.
5. Pilih **Status Distribusi**: klik **Request** jika masih dalam tahap permintaan, atau **Selesai** jika material sudah diambil.
6. Pada bagian **Grup SPK Workshop**, klik tombol **+ Grup SPK** dan ketikkan nomor SPK tujuan.
7. Klik tombol **+ Pilih Material** untuk memilih material workshop yang akan dikeluarkan.
8. Isi **Jumlah Keluar** untuk setiap material.
9. Periksa ringkasan di footer hijau: **Total Item Keluar** dan **Grup SPK**.
10. Klik tombol **Simpan Semua Data** untuk menyimpan dokumen.

> **Catatan Penting:** Stok material akan otomatis berkurang dari gudang setelah dokumen disimpan dengan status **Selesai** (Completed).

---

## 4. Modul Riwayat Mutasi

### 4.1 Halaman Riwayat Mutasi

![Riwayat Mutasi](./Gudang/05_warehouse_history.jpeg)

**Tujuan:** Modul ini menampilkan log pergerakan (mutasi) seluruh material workshop secara kronologis. Setiap kali material masuk (belanja) atau keluar (distribusi), sistem secara otomatis mencatat jejak transaksinya di halaman ini.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Cari Catatan / SPK** | Kolom pencarian berdasarkan catatan atau nomor SPK |
| **Pilih Material** | Filter berdasarkan material tertentu |
| **Tipe Mutasi** | Filter tipe: **ALL** (semua), **IN** (masuk), **OUT** (keluar) |
| **Dari Tanggal / Sampai Tanggal** | Filter rentang waktu mutasi |
| **Total Log** | Jumlah total catatan mutasi yang ditemukan |

**Tabel Riwayat Mutasi menampilkan:**
- **Waktu & Tanggal:** Tanggal dan jam transaksi dilakukan.
- **Material Workshop:** Nama material dan satuannya (contoh: Alas Vans Gum — pcs/pasang).
- **Tipe:** Badge berwarna — **MASUK** (hijau) atau **KELUAR** (merah).
- **Kuantitas:** Jumlah material yang bergerak (contoh: `+1` untuk masuk, `-1` untuk keluar).
- **Keterangan Transaksi:** Detail transaksi, termasuk tautan dokumen sumber (contoh: `WH-IN-20260725-001`) yang dapat diklik langsung untuk membuka nota aslinya.
- **Operator:** Nama pengguna yang melakukan transaksi.

**Langkah-Langkah Menggunakan Riwayat Mutasi:**

1. Pada sidebar, klik menu **Riwayat Mutasi** di bawah bagian *Divisi Gudang*.
2. Halaman **Riwayat Mutasi** akan terbuka dengan judul "Log Pergerakan Material Workshop".
3. Gunakan filter **Tipe Mutasi** untuk melihat hanya barang masuk (**IN**), barang keluar (**OUT**), atau keduanya (**ALL**).
4. Pilih **Dari Tanggal** dan **Sampai Tanggal** untuk membatasi periode yang ingin dilihat.
5. Gunakan dropdown **Pilih Material** untuk memfilter mutasi material tertentu saja.
6. Klik tautan berwarna pada kolom **Keterangan Transaksi** (contoh: `WH-IN-20260725-001`) untuk langsung membuka dokumen belanja atau pengeluaran yang terkait.

> **Catatan Penting:** Data mutasi bersifat *read-only* (hanya baca) — tidak dapat diedit atau dihapus. Data ini otomatis tercatat setiap kali ada transaksi belanja atau barang keluar yang disimpan.

---

## 5. Modul Penyimpanan Rak dan Manajemen Rak

### 5.1 Halaman Penyimpanan Rak (Peta Visual Gudang)

![Penyimpanan Rak — Peta Visual Gudang](./Gudang/06_warehouse_penyimpananrak.jpeg)

**Tujuan:** Halaman ini menyediakan tampilan visual real-time dari seluruh rak penyimpanan gudang. Admin gudang dapat memantau kapasitas, ketersediaan, dan lokasi penyimpanan sepatu pelanggan secara sekilas menggunakan peta visual berwarna.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Kategori Rak** | Tab pilihan kategori: **Sepatu Finish**, **Aksesoris**, **Inbound Rack** |
| **Master Rak** | Tombol untuk membuka halaman Manajemen Rak |
| **Cari SPK / Customer** | Kolom pencarian untuk menemukan item di rak |
| **Total Item Stored** | Jumlah total barang yang saat ini tersimpan di rak |
| **Item Out / Retrieved** | Jumlah barang yang telah diambil dari rak |
| **Peringatan Overdue** | Jumlah barang yang sudah melewati batas waktu penyimpanan |
| **Rata-Rata Simpan** | Rata-rata durasi penyimpanan barang di gudang (dalam hari) |

**Peta Visual Gudang:**
- Setiap kotak mewakili satu rak (contoh: **A1**, **A2**, **B3**, dst.).
- **Warna hijau:** Rak terisi (ada barang tersimpan).
- **Warna abu-abu:** Rak kosong (tersedia).
- **Warna kuning/oranye:** Rak penuh atau hampir penuh.
- **Utilisasi Kapasitas Global:** Persentase total kapasitas gudang yang terpakai.

**Data Log Item:**
- Menampilkan riwayat penyimpanan real-time, termasuk nomor SPK, nama sepatu, customer/owner, dan posisi rak.
- Tombol **Export Data Rekap** untuk mengekspor data ke format Excel.

**Langkah-Langkah Menggunakan Penyimpanan Rak:**

1. Pada sidebar bagian *Operasional Gudang*, cari dan klik menu terkait penyimpanan rak.
2. Pilih kategori rak menggunakan tab di bagian atas: **Sepatu Finish**, **Aksesoris**, atau **Inbound Rack**.
3. Perhatikan kartu statistik di bagian atas untuk mengetahui jumlah barang tersimpan, yang sudah diambil, serta peringatan overdue.
4. Lihat **Peta Visual Gudang** untuk mengetahui rak mana yang terisi dan mana yang kosong.
5. Klik pada kotak rak tertentu untuk melihat detail barang yang ada di dalamnya.
6. Gulir ke bawah untuk melihat **Data Log Item** lengkap.
7. Klik **Export Data Rekap** untuk mengunduh rekap data penyimpanan dalam format Excel.

---

### 5.2 Halaman Manajemen Rak Gudang

![Manajemen Rak Gudang](./Gudang/07_warehouse_racks_manajemenrak.jpeg)

**Tujuan:** Halaman ini digunakan untuk mengelola data master rak gudang, termasuk membuat rak baru, mengubah kapasitas, menyinkronkan data, dan menghapus rak yang sudah tidak digunakan.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Tab Rak Sepatu** | Menampilkan daftar rak untuk penyimpanan sepatu |
| **Tab Rak Aksesoris** | Menampilkan daftar rak untuk penyimpanan aksesoris |
| **Tab Rak Inbound (Transit)** | Menampilkan daftar rak transit untuk barang yang baru masuk |
| **Sync Data** | Tombol untuk menyinkronkan data rak dengan sistem |
| **Liat Sampah** | Tombol untuk melihat rak yang telah dihapus (soft delete) |
| **+ Tambah Rak** | Tombol untuk membuat rak baru |

**Tabel Daftar Rak menampilkan:**
- **Kode Rak:** Kode identifikasi unik rak (contoh: A1, A2, B1, C3, D5).
- **Lokasi:** Deskripsi lokasi fisik (contoh: Lantai 1 - Area A, Lantai 2 - Area C).
- **Kapasitas:** Jumlah maksimum item yang dapat disimpan pada rak tersebut.
- **Isi (Item):** Jumlah item yang saat ini tersimpan / kapasitas maksimum (contoh: `1/20`).
- **Status:** Status rak (Active / Inactive).
- **Aksi:** Tombol **Cetak** (mencetak label rak), **Edit** (mengubah data rak), **Hapus** (menghapus rak).

**Langkah-Langkah Mengelola Rak:**

1. Pada halaman Penyimpanan Rak, klik tombol **Master Rak** di bagian header.
2. Halaman **Manajemen Rak Gudang** akan terbuka.
3. Pilih tab kategori rak: **Rak Sepatu**, **Rak Aksesoris**, atau **Rak Inbound (Transit)**.
4. Gunakan kolom pencarian untuk menemukan rak berdasarkan kode atau lokasi.
5. Untuk **menambah rak baru**: klik tombol **+ Tambah Rak** dan isi data kode, lokasi, dan kapasitas.
6. Untuk **mengubah data rak**: klik tombol **Edit** pada baris rak yang ingin diubah.
7. Untuk **mencetak label rak**: klik tombol **Cetak** pada baris rak yang diinginkan.
8. Untuk **menghapus rak**: klik tombol **Hapus** (berwarna merah). Rak yang dihapus akan masuk ke *Sampah* dan masih dapat dipulihkan.
9. Klik **Sync Data** untuk memastikan data rak tersinkronisasi dengan sistem secara real-time.

---

## 6. Modul Riwayat Pengambilan

### 6.1 Halaman Riwayat Pengambilan Sepatu

![Riwayat Pengambilan Sepatu](./Gudang/08_warehouse_pickup_history.jpeg)

**Tujuan:** Modul ini mencatat seluruh riwayat pengambilan sepatu oleh pelanggan atau kurir. Sistem merekam data lengkap termasuk waktu pengambilan, metode (Offline / PCP Express), serta detail layanan dan biaya logistik.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Hari Ini** | Jumlah sepatu yang diambil pada hari ini |
| **Minggu Ini** | Jumlah sepatu yang diambil pada minggu ini |
| **Bulan Ini** | Jumlah sepatu yang diambil pada bulan ini |
| **Total Keseluruhan** | Jumlah total sepatu yang pernah diambil |
| **Pencarian SPK / Customer** | Kolom pencarian berdasarkan nomor SPK, nama customer, atau merk sepatu |
| **Rentang Tanggal** | Filter berdasarkan periode waktu tertentu |
| **Metode Pengambilan** | Filter berdasarkan metode: Semua Metode, Offline, PCP Express |
| **Cetak** | Tombol untuk mencetak laporan riwayat pengambilan |

**Tabel Riwayat Pengambilan menampilkan:**
- **Data Sepatu:** Nomor SPK, merk, dan model sepatu (disertai gambar QR Code).
- **Customer:** Nama pelanggan dan nomor telepon.
- **Waktu Ambil:** Tanggal dan jam pengambilan.
- **Metode:** Cara pengambilan — *Offline* (diambil langsung) atau *PCP Express* (dikirim via kurir).
- **Logistik & Margin:** Rincian biaya ongkos kirim (*cost*), biaya retail, dan margin keuntungan.
- **Layanan:** Daftar jasa yang dilakukan pada sepatu (contoh: Deep Clean, Fast Clean, Sol Kulit, dll.).
- **Aksi:** Tombol **Detail** untuk melihat informasi pengambilan secara lengkap.

**Langkah-Langkah Menggunakan Riwayat Pengambilan:**

1. Pada sidebar bagian *Operasional Gudang* atau *Divisi Gudang*, cari dan klik menu terkait riwayat pengambilan.
2. Halaman **Riwayat Pengambilan Sepatu** akan terbuka.
3. Perhatikan kartu statistik berwarna di bagian atas untuk melihat ringkasan jumlah pengambilan.
4. Gunakan **kolom pencarian** untuk mencari berdasarkan nomor SPK, nama customer, atau merk sepatu.
5. Atur **Rentang Tanggal** untuk melihat data pada periode tertentu.
6. Pilih **Metode Pengambilan** (Offline / PCP Express) untuk menyaring hasil.
7. Klik tombol **Detail** pada baris tertentu untuk melihat informasi pengambilan lengkap.
8. Klik tombol **Cetak** (berwarna ungu, pojok kanan atas) untuk mencetak laporan.

---

## 7. Modul Penerimaan dan Form QC

### 7.1 Halaman Penerimaan — Tab SPK Masuk (Pending)

![Gudang Penerimaan — Tab SPK Masuk Pending](./Gudang/09_reception_tab_spkpending.jpeg)

**Tujuan:** Halaman ini merupakan pusat penerimaan sepatu pelanggan (**Reception & Quality Control Center**). Tab **SPK Masuk (Pending)** menampilkan daftar pesanan sepatu yang sudah didaftarkan oleh Customer Service (CS) dan menunggu untuk diterima secara fisik di gudang.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Import Data Customer & SPK** | Area untuk mengimpor data pesanan dari file Excel |
| **Download Template** | Mengunduh template Excel untuk format impor |
| **Tambah Order Manual** | Menambahkan pesanan secara manual (satu per satu) |
| **Export Excel** | Mengekspor data penerimaan ke file Excel |
| **Tab SPK Masuk (Pending)** | Daftar SPK yang menunggu penerimaan fisik |
| **Tab Diterima (Warehouse)** | Daftar SPK yang sudah diterima di gudang |
| **Tab Sudah Diproses** | Daftar SPK yang sudah masuk tahap proses |
| **Tempat Sampah** | Melihat data yang telah dihapus |

**Filter & Pencarian:**
- **Nomor SPK:** Kolom pencarian berdasarkan nomor SPK.
- **Nama / WhatsApp:** Kolom pencarian berdasarkan nama atau nomor WA pelanggan.
- **Dari Tanggal / Sampai Tanggal:** Filter rentang waktu.
- **Prioritas:** Filter berdasarkan tingkat prioritas (Reguler / Prioritas).
- **Pintasan:** Tombol cepat **2 Minggu** dan **1 Bulan** untuk filter waktu instan.
- **Filter Pending:** Tombol untuk menerapkan filter pencarian.
- **PDF / Excel:** Tombol untuk mengekspor hasil filter ke PDF atau Excel.

**Tabel Data Penerimaan menampilkan:**
- **SPK / Tanggal:** Nomor SPK beserta tanggal pendaftaran.
- **Customer:** Nama dan nomor telepon pelanggan.
- **Sepatu / Item:** Merk, warna, dan jenis sepatu.
- **CS:** Inisial Customer Service yang mendaftarkan pesanan.
- **Aksi:** Tombol **Terima Barang →** untuk memproses penerimaan sepatu.

**Langkah-Langkah Menerima Barang (dari Tab SPK Pending):**

1. Buka halaman **Gudang Penerimaan** melalui menu pada sidebar bagian *Operasional Gudang*.
2. Pastikan Anda berada pada tab **SPK Masuk (Pending)**.
3. Cari pesanan pelanggan menggunakan filter nomor SPK, nama, atau tanggal.
4. Temukan baris SPK yang ingin diterima.
5. Klik tombol **Terima Barang →** (berwarna kuning) pada baris tersebut.
6. Sistem akan membuka dialog **Penyimpanan Rak Inbound** (lihat sub-bab 7.2).

---

### 7.2 Dialog Penyimpanan Rak Inbound

![Dialog Penyimpanan Rak Inbound](./Gudang/10_reception_simpan_rakinbound.jpeg)

**Tujuan:** Dialog pop-up ini muncul setelah Anda menekan tombol **Terima Barang →** pada tab SPK Pending. Dialog ini digunakan untuk menentukan lokasi rak inbound (transit) tempat sepatu akan disimpan sementara sebelum diproses lebih lanjut.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **No. SPK** | Nomor SPK yang sedang diterima (terisi otomatis, tidak dapat diubah) |
| **Pilih Rak Inbound (Transit)** | Dropdown untuk memilih rak inbound tempat barang akan disimpan |
| **Batal** | Membatalkan proses penerimaan |
| **Konfirmasi Terima** | Mengkonfirmasi penerimaan dan menyimpan barang ke rak yang dipilih |

**Langkah-Langkah Menyimpan Barang ke Rak Inbound:**

1. Setelah dialog **Penyimpanan Rak Inbound** muncul, periksa **No. SPK** yang tertera untuk memastikan SPK yang benar.
2. Klik dropdown **Pilih Rak Inbound (Transit)** dan pilih rak transit yang tersedia.
3. Klik tombol **Konfirmasi Terima** (berwarna hijau) untuk menyelesaikan proses penerimaan.
4. Sepatu akan berpindah dari tab *SPK Masuk (Pending)* ke tab *Diterima (Warehouse)*.

> **Catatan Penting:** Rak Inbound adalah rak transit sementara. Sepatu yang sudah diterima di sini akan menunggu proses QC (Quality Control) sebelum dipindahkan ke rak penyimpanan utama.

---

### 7.3 Halaman Penerimaan — Tab Diterima (Warehouse)

![Tab Diterima (Warehouse)](./Gudang/11_reception_tab_received.jpeg)

**Tujuan:** Tab ini menampilkan daftar sepatu yang sudah berhasil diterima di gudang dan menunggu untuk diproses melalui formulir QC (Quality Control).

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Tab Diterima (Warehouse)** | Tab aktif yang menampilkan barang sudah diterima |
| **Total Pcs** | Jumlah total pasang sepatu yang sudah diterima |
| **Filter Pencarian** | Kolom pencarian, filter tanggal masuk, prioritas, dan status QC |
| **Status QC** | Filter berdasarkan status QC (Semua Status / Belum QC / Sudah QC) |

**Tabel Data Diterima menampilkan:**
- **Info & Waktu:** Nomor urut, tanggal diterima, dan estimasi pengerjaan. Badge **Reguler** atau **Prioritas** menunjukkan tingkat prioritas pesanan.
- **Order & Customer:** Nomor SPK (dapat diklik), nama pelanggan, nomor WA, dan email.
- **Item Barang:** Merk sepatu, jenis, warna, ukuran, serta badge **CS OK** (artinya data sudah diverifikasi oleh CS). Tombol **Ubah Detail** untuk mengoreksi data barang.
- **Data & QC:** Status fisik (Belum QC / Sudah QC) dan status kelengkapan aksesoris.
- **Handler:** Nama petugas gudang yang bertanggung jawab.
- **Status:** Status proses saat ini.
- **Aksi:** Tombol-tombol aksi penting:
  - **Proses (Form QC) →**: Membuka formulir pemeriksaan kualitas.
  - **To Manifest 📦**: Memindahkan langsung ke manifest pengiriman.
  - **Print SPK**: Mencetak dokumen SPK.
  - **Batal Terima 📋**: Membatalkan penerimaan dan mengembalikan ke tab Pending.

**Langkah-Langkah Memproses Barang yang Sudah Diterima:**

1. Pada halaman **Gudang Penerimaan**, klik tab **Diterima (Warehouse)**.
2. Gunakan filter pencarian untuk menemukan barang tertentu berdasarkan SPK, nama, tanggal, atau status QC.
3. Klik tombol **Proses (Form QC) →** (berwarna hijau) untuk membuka formulir pemeriksaan kualitas.
4. Sistem akan membuka halaman **Form QC** (lihat sub-bab 7.4).

---

### 7.4 Formulir QC (Quality Control) — Penerimaan Gudang

![Form QC Penerimaan Gudang](./Gudang/12_reception_form_qc.jpeg)

**Tujuan:** Formulir ini digunakan untuk melakukan pemeriksaan kualitas dan kelengkapan sepatu yang baru diterima di gudang. Seluruh data yang diinput di sini akan menjadi acuan bagi tim workshop dalam mengerjakan sepatu.

**Formulir ini terdiri dari 6 bagian utama:**

#### Bagian 1: Data Customer & Pengiriman
- **Nama Customer:** Nama lengkap pelanggan.
- **No. WhatsApp:** Nomor telepon pelanggan.
- **Email (Opsional):** Alamat email pelanggan.
- **Tanggal Masuk:** Tanggal sepatu diterima di gudang (terisi otomatis).
- **Estimasi Selesai:** Tanggal target penyelesaian pengerjaan.
- **Detail Alamat Pengiriman:** Alamat lengkap termasuk provinsi, kota, kecamatan, kelurahan, dan kode pos.

#### Bagian 2: Data Barang (Fisik)
- **Kategori Item:** Jenis barang (Sepatu, Aksesoris, dll.).
- **Brand:** Merk sepatu (contoh: Nike, Adidas).
- **Jenis / Model:** Model sepatu (contoh: Pegasus).
- **Ukuran:** Ukuran sepatu.
- **Warna:** Warna sepatu.
- **Target HK (Hari Kerja):** Estimasi hari kerja yang dibutuhkan untuk pengerjaan.
- **Status Garansi:** Toggle untuk menandai jika sepatu masih dalam masa garansi.
- **Data Terverifikasi CS:** Badge yang menandakan data sudah divalidasi oleh Customer Service.

#### Bagian 3: Kelengkapan Aksesoris
- **Tali:** Pilihan status — *Simpan*, *Nempel*, atau *Tidak Ada*.
- **Insole:** Pilihan status — *Simpan*, *Nempel*, atau *Tidak Ada*.
- **Box:** Pilihan status — *Simpan*, *Nempel*, atau *Tidak Ada*.
- **Aksesoris Lainnya (Opsional):** Catatan aksesoris tambahan (contoh: Kaos kaki, Pembersih, Tas, dll.).

#### Bagian 4: QC Gatekeeper (Pemeriksaan Awal)
- **Lolos QC Gudang ✓:** Klik jika sepatu dinyatakan layak diproses.
- **Reject / Tolak ✕:** Klik jika sepatu tidak memenuhi syarat untuk diproses.

#### Bagian 5: Rekap Layanan & Pesan CS
- **Layanan yang Disarankan CS:** Daftar jasa yang direkomendasikan oleh CS beserta harganya (contoh: Fast Clean Rp 35.000, Reglue Sol Rp 50.000).
- **Pesan untuk Workshop (dari CS):** Catatan khusus dari CS.
- **Customer Service:** Nama CS yang menangani, tanggal input, dan lokasi toko.
- **Catatan Tambahan Gudang:** Kolom untuk catatan khusus dari admin gudang.
- **Simpan Catatan:** Tombol untuk menyimpan catatan tambahan.

#### Bagian 6: Foto Kondisi Awal (Before)
- **+ Pilih Foto & Upload:** Tombol untuk mengunggah foto kondisi sepatu sebelum dikerjakan. Foto ini penting sebagai dokumentasi awal.

**Langkah-Langkah Mengisi Form QC:**

1. Pada tab *Diterima (Warehouse)*, klik **Proses (Form QC) →** pada baris SPK yang ingin diproses.
2. **Periksa Bagian 1:** Pastikan data pelanggan dan alamat pengiriman sudah lengkap dan benar. Isi **Estimasi Selesai** jika belum diisi.
3. **Periksa Bagian 2:** Verifikasi data fisik barang (brand, jenis, ukuran, warna). Atur **Target HK (Hari Kerja)** sesuai estimasi pengerjaan.
4. **Isi Bagian 3:** Centang kelengkapan aksesoris yang diterima bersama sepatu (tali, insole, box).
5. **Isi Bagian 4:** Klik **Lolos QC Gudang** jika sepatu layak diproses, atau **Reject / Tolak** jika tidak memenuhi syarat.
6. **Periksa Bagian 5:** Baca pesan dan rekomendasi layanan dari CS. Tambahkan catatan gudang jika diperlukan, lalu klik **Simpan Catatan**.
7. **Isi Bagian 6:** Unggah foto kondisi awal sepatu dengan mengklik **+ Pilih Foto & Upload**.
8. Klik tombol **✓ Simpan & Proses** (berwarna hijau, pojok kanan bawah) untuk menyelesaikan proses QC.
9. Atau klik **← Batal** untuk membatalkan dan kembali ke halaman sebelumnya.

> **Catatan Penting:** Tombol **Print SPK** di pojok kanan atas dapat digunakan kapan saja untuk mencetak dokumen SPK lengkap sebagai lampiran fisik yang menyertai sepatu selama proses pengerjaan di workshop.

---

## 8. Modul Assessment

### 8.1 Halaman Assessment Station

![Assessment Station](./Gudang/13_assesment.jpeg)

**Tujuan:** Modul ini menampilkan antrian sepatu yang sudah lolos QC dan menunggu untuk diperiksa lebih lanjut (*assessment*) oleh tim gudang sebelum dipindahkan ke workshop. Halaman ini membantu admin gudang memprioritaskan sepatu mana yang harus segera diproses.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Tanggal Hari Ini** | Tanggal saat ini ditampilkan di header (contoh: Monday, 27 July 2026) |
| **Cari SPK / Customer** | Kolom pencarian cepat |
| **Total** | Jumlah total item dalam antrian |
| **Filter Status Invoice** | Tab filter: **Semua**, **Lunas**, **DP / Cicil**, **Belum Bayar**, **Belum Ada Invoice** |

**Tabel Antrian Assessment menampilkan:**
- **Prioritas:** Badge prioritas — **FAST TRACK** (warna hijau, prioritas tinggi) atau **REGULER** (warna abu-abu, prioritas normal). Badge **PRIORITAS** (warna ungu) menandakan prioritas sangat tinggi.
- **SPK:** Nomor SPK pesanan (dapat diklik untuk detail).
- **Status Invoice:** Status pembayaran — *Lunas* (hijau), *DP/Cicil* (biru), *Belum Bayar* (merah).
- **Pelanggan:** Nama dan nomor telepon pelanggan beserta inisialnya.
- **Brand / Info:** Merk sepatu, warna, dan ukuran.
- **Masuk Sejak:** Durasi sejak barang masuk (contoh: 1 week, 5 days). Warna merah menandakan barang sudah terlalu lama menunggu.
- **Action:** Tombol **Mulai Cek →** untuk memulai proses assessment pada item tersebut.

**Langkah-Langkah Menggunakan Assessment Station:**

1. Buka halaman **Assessment Station** melalui menu di sidebar bagian *Operasional Gudang*.
2. Perhatikan jumlah total antrian di badge **Total** di pojok kanan atas.
3. Gunakan tab filter status invoice untuk memfokuskan pada item dengan status pembayaran tertentu (misalnya, prioritaskan yang sudah *Lunas*).
4. Perhatikan kolom **Masuk Sejak** — item yang berwarna merah sudah menunggu terlalu lama dan perlu segera ditangani.
5. Perhatikan badge **FAST TRACK** untuk item yang memiliki prioritas pengerjaan cepat.
6. Klik tombol **Mulai Cek →** (berwarna hijau) pada baris yang ingin diproses untuk memulai assessment.

> **Catatan Penting:** Baris yang berwarna kuning muda (highlighted) menandakan item dengan prioritas **FAST TRACK** yang membutuhkan perhatian segera.

---

## 9. Modul Manifest

### 9.1 Halaman Daftar Manifest

![Daftar Manifest — Logistik Manifest](./Gudang/14_daftar_manifest.jpeg)

**Tujuan:** Modul ini digunakan untuk mengelola pengiriman batch (*manifest*) sepatu dari gudang ke workshop. Manifest mengelompokkan beberapa item sepatu dalam satu dokumen pengiriman untuk efisiensi logistik.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Manifest OTW** | Jumlah manifest yang sedang dalam perjalanan (status: Active) |
| **Manifest Selesai** | Jumlah manifest yang sudah diterima di tujuan |
| **Total Item Terkirim** | Total keseluruhan pasang sepatu yang telah dikirim |
| **+ Buat Pengiriman** | Tombol untuk membuat manifest baru |

**Tabel Daftar Pengiriman Barang menampilkan:**
- **No. Manifest:** Nomor unik manifest yang digenerate otomatis oleh sistem (contoh: `MFST-20260718-6A5AFB86CE5B7`).
- **Logistik Info:** Nama penanggung jawab pengiriman dan tanggal/jam pembuatan manifest.
- **Batch Size:** Jumlah pasang sepatu dalam satu manifest.
- **Status:** Status manifest — **✓ Diterima** (hijau) berarti sudah sampai di tujuan.
- **Action:** Tombol detail untuk melihat isi manifest.

**Langkah-Langkah Melihat Daftar Manifest:**

1. Buka halaman **Logistik Manifest** melalui menu di sidebar.
2. Perhatikan ringkasan statistik di bagian atas: jumlah manifest aktif, selesai, dan total item terkirim.
3. Klik baris manifest tertentu atau tombol **>** (panah) di kolom Action untuk melihat detail isi manifest.
4. Klik tombol **+ Buat Pengiriman** untuk membuat manifest baru (lihat sub-bab 9.2).

---

### 9.2 Membuat Manifest Pengiriman Baru

![Buat Pengiriman Baru — Manifest](./Gudang/15_manifest_create.jpeg)

**Tujuan:** Form ini digunakan untuk membuat manifest pengiriman baru dengan memilih batch sepatu yang siap dikirim dari gudang ke workshop.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Antrian Siap Kirim** | Daftar sepatu yang sudah siap dikirim (status: Ready to Dispatch) |
| **Pilih Semua** | Checkbox untuk memilih seluruh item sekaligus |
| **Pencarian** | Kolom pencarian berdasarkan SPK, nama, atau brand |
| **Filter Tanggal** | Filter berdasarkan rentang tanggal (From — To) |
| **Shipment Config** | Panel konfigurasi pengiriman |
| **Catatan Pengiriman** | Kolom untuk catatan khusus (contoh: "Titip ke Driver Pak Andi, 2 Karung batch pagi...") |
| **Selected** | Jumlah item yang dipilih untuk manifest ini |
| **Generate Manifest** | Tombol untuk membuat manifest setelah item dipilih |

**Langkah-Langkah Membuat Manifest Baru:**

1. Pada halaman Daftar Manifest, klik tombol **+ Buat Pengiriman**.
2. Halaman **Buat Pengiriman Baru** akan terbuka dengan judul "Pilih batch sepatu yang siap dikirim ke Workshop Hijau".
3. Pada panel **Antrian Siap Kirim** (kiri), cari dan centang item-item sepatu yang akan dimasukkan ke dalam manifest ini. Gunakan kolom pencarian atau filter tanggal untuk mempersempit hasil.
4. Untuk memilih semua item, centang **Pilih Semua** di pojok kanan atas antrian.
5. Pada panel **Shipment Config** (kanan), periksa nama penanggung jawab (Gudang Logistik Dispatcher).
6. Isi **Catatan Pengiriman** dengan instruksi khusus jika diperlukan (contoh: nama driver, jumlah karung, waktu batch).
7. Periksa jumlah **Selected** untuk memastikan jumlah item yang benar.
8. Klik tombol **Generate Manifest** untuk membuat manifest pengiriman.
9. Setelah manifest berhasil dibuat, item yang dipilih akan otomatis berubah status menjadi **OTW Workshop**.

> **Catatan Penting:** Item yang masuk ke manifest otomatis berubah statusnya menjadi *OTW Workshop* dan tidak lagi muncul di antrian siap kirim.

---

## 10. Modul Finish / Selesai

### 10.1 Halaman Station: Barang Selesai & Pickup

![Station Barang Selesai & Pickup](./Gudang/16_finish.jpeg)

**Tujuan:** Modul ini mengelola sepatu yang telah selesai diproses oleh workshop dan siap dikembalikan kepada pelanggan. Halaman ini terbagi menjadi tiga bagian utama: sepatu yang menunggu disimpan di rak, sepatu yang sudah siap diambil di rak, dan riwayat pengambilan terakhir.

**Bagian 1: Menunggu Disimpan**
Area berwarna oranye/kuning di bagian atas menampilkan sepatu yang baru selesai dikerjakan oleh workshop tetapi belum disimpan ke rak gudang.

| Komponen | Keterangan |
|---|---|
| **Kartu Sepatu** | Menampilkan nomor SPK, status (Selesai/Belum Lunas), merk & warna, daftar layanan, dan info pelanggan |
| **Simpan ke Gudang** | Tombol untuk menyimpan sepatu ke rak penyimpanan finish |
| **Ambil Langsung** | Tombol jika pelanggan langsung mengambil tanpa disimpan dulu |
| **Ambil Langsung (Bypass)** | Tombol untuk mengambil langsung dengan melewati prosedur standar |
| **Ambil Pengiriman** | Tombol untuk memproses pengiriman ke pelanggan |
| **Ambil Pengiriman (Bypass)** | Tombol untuk memproses pengiriman dengan melewati prosedur standar |
| **Cetak Laporan** | Tombol untuk mencetak laporan area ini |

**Bagian 2: Siap Diambil (Di Rak)**
Area berwarna hijau di bagian tengah menampilkan sepatu yang sudah tersimpan di rak gudang dan siap diambil oleh pelanggan.

| Komponen | Keterangan |
|---|---|
| **Kartu Sepatu** | Menampilkan foto sepatu, nomor SPK, status pembayaran, merk & warna, daftar layanan, info pelanggan, dan posisi rak |
| **Lokasi** | Posisi rak tempat sepatu disimpan (contoh: A1, A3) |
| **Tag Rak** | Tombol untuk mencetak label tag rak |
| **Alamat** | Tombol untuk melihat alamat pengiriman pelanggan |
| **Lepas Tag** | Tombol untuk melepas tag dari rak |
| **Ambil (Retrieve)** | Tombol utama untuk mengambil sepatu dari rak |
| **Ambil Bypass** | Mengambil dengan melewati prosedur standar |
| **Ambil Pengiriman** | Memproses pengiriman untuk sepatu ini |
| **Ambil Pengiriman (Bypass)** | Memproses pengiriman dengan melewati prosedur standar |
| **Lapor** | Tombol untuk melaporkan masalah pada item ini |

**Bagian 3: Riwayat Pengambilan Terakhir**
Tabel di bagian bawah menampilkan riwayat pengambilan sepatu terbaru.

| Kolom | Keterangan |
|---|---|
| **Foto** | Foto sepatu |
| **SPK & Customer** | Nomor SPK, nama pelanggan, status pembayaran, dan prioritas |
| **Info Item** | Merk dan warna sepatu |
| **Layanan & Harga** | Daftar layanan yang dilakukan beserta harga total |
| **Waktu Ambil** | Tanggal dan jam pengambilan |
| **Status** | Status pengambilan (✓ Sudah Diambil) |

**Langkah-Langkah Mengelola Sepatu Selesai:**

1. Buka halaman **Station: Barang Selesai & Pickup** melalui menu di sidebar.
2. **Untuk menyimpan sepatu ke rak:**
   - Temukan kartu sepatu di area **Menunggu Disimpan** (berwarna oranye).
   - Klik tombol **Simpan ke Gudang** pada kartu yang sesuai.
   - Pilih rak tujuan penyimpanan.
3. **Untuk mengambil sepatu dari rak (pelanggan datang langsung):**
   - Temukan kartu sepatu di area **Siap Diambil (Di Rak)** (berwarna hijau).
   - Klik tombol **Ambil (Retrieve)**.
   - Sistem akan mencatat pengambilan dan memperbarui status.
4. **Untuk memproses pengiriman:**
   - Klik tombol **Ambil Pengiriman** pada kartu sepatu yang ingin dikirim.
   - Isi data pengiriman yang diperlukan.
5. Lihat **Riwayat Pengambilan Terakhir** di bagian bawah untuk memverifikasi aktivitas terbaru.

---

## 11. Modul Pengiriman

### 11.1 Halaman Antrian Pengiriman

![Antrian Pengiriman](./Gudang/17_shipping.jpeg)

**Tujuan:** Modul ini digunakan untuk mengelola proses pengiriman sepatu yang sudah selesai dikerjakan kepada pelanggan melalui jasa ekspedisi. Admin gudang dapat mengatur kategori pengiriman, memilih ekspedisi, menetapkan PIC gudang, dan mencatat nomor resi pengiriman secara real-time.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Kolom Pencarian** | Mencari berdasarkan nama, No HP, SPK, atau nomor resi |
| **Filter Verifikasi** | Menyaring berdasarkan status verifikasi (Semua Verifikasi) |
| **Filter Tanggal** | Menyaring berdasarkan rentang tanggal |
| **Filter** | Tombol untuk menerapkan filter |
| **Cetak Manifest** | Mencetak dokumen manifest pengiriman |
| **Label Custom** | Membuat label pengiriman dengan format khusus |

**Tabel Antrian Pengiriman menampilkan:**
- **ID:** Nomor urut pengiriman.
- **Info Kustomer:** Nama pelanggan, nomor telepon, alamat lengkap, dan tanggal masuk.
- **Nomor SPK:** Nomor SPK yang dikirim.
- **Kategori:** Dropdown untuk memilih kategori pengiriman.
- **Ekspedisi:** Dropdown untuk memilih jasa ekspedisi (JNE, J&T, SiCepat, dll.).
- **Verifikasi:** Toggle untuk menandai bahwa data pengiriman sudah diverifikasi.
- **PIC Gudang:** Dropdown untuk memilih penanggung jawab gudang yang menangani pengiriman.
- **Target Kirim:** Tanggal target pengiriman.
- **Resi Pengiriman:** Kolom input untuk mencatat nomor resi dari ekspedisi.
- **Status:** Status pengiriman saat ini.

**Langkah-Langkah Memproses Pengiriman:**

1. Buka halaman **Pengiriman** melalui menu di sidebar.
2. Halaman **Antrian Pengiriman** akan terbuka dengan deskripsi "Kelola konfirmasi, kategori, dan resi pengiriman kustomer secara real-time".
3. Temukan pesanan yang ingin dikirim menggunakan kolom pencarian atau filter.
4. Pada baris pesanan tersebut:
   - Pilih **Kategori** pengiriman dari dropdown.
   - Pilih **Ekspedisi** yang akan digunakan.
   - Aktifkan toggle **Verifikasi** setelah data terverifikasi.
   - Pilih **PIC Gudang** yang bertanggung jawab.
   - Atur **Target Kirim** (tanggal pengiriman).
   - Masukkan **Resi Pengiriman** setelah paket diserahkan ke ekspedisi.
5. Klik tombol **Cetak Manifest** untuk mencetak dokumen manifest pengiriman.
6. Klik tombol **Label Custom** jika memerlukan label pengiriman dengan format khusus.

> **Catatan Penting:** Pastikan semua kolom terisi dengan benar sebelum mengaktifkan toggle verifikasi. Nomor resi harus diinput segera setelah paket diserahkan kepada jasa ekspedisi agar pelanggan dapat melacak kiriman.

---

## 12. Modul Manajemen Material

### 12.1 Halaman Master Data Material

![Master Data Material](./Gudang/18_manajemen_material.jpeg)

**Tujuan:** Modul ini digunakan untuk mengelola seluruh data master material (bahan baku) yang digunakan oleh workshop. Admin gudang dapat menambah material baru, mengubah data, memantau stok, serta mengimpor dan mengekspor data material dalam format Excel.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Total Material** | Jumlah total jenis material yang terdaftar di sistem (contoh: 92) |
| **Kolom Pencarian** | Mencari material berdasarkan nama |
| **Filter Status** | Menyaring berdasarkan status material (Semua Status) |
| **Filter Tipe** | Menyaring berdasarkan tipe material (Semua Tipe) |
| **Sampah** | Tombol untuk melihat material yang telah dihapus |
| **Ikon Aksi Massal** | Tombol-tombol untuk: Cetak, Copy, Ekspor, dan Impor data |
| **+ Tambah Material** | Tombol untuk menambahkan material baru |

**Tabel Master Data Material menampilkan:**
- **Material:** Nama material beserta tag tipe (contoh: Material Sol) dan sub-tag nama (contoh: Sole Potong LNP Hitam). Informasi minimum stok juga ditampilkan (contoh: "Min: 2 pcs/pasang").
- **Ukuran:** Kategori ukuran material (Besar / Kecil / nomor ukuran seperti 43).
- **Stock:** Jumlah stok saat ini beserta satuan (contoh: 42 pcs/pasang).
- **Harga Beli:** Harga beli per satuan material (contoh: Rp 55.000).
- **Status & PIC:** Status material (**Ready** = tersedia) dan penanggung jawab.
- **Aksi:** Ikon-ikon aksi: **Detail** (melihat rincian material), **Edit** (mengubah data), **Hapus** (menghapus material).

**Navigasi Halaman:**
- Di bagian bawah tabel, terdapat informasi paginasi (contoh: "Showing 1 to 15 of 92 results") beserta tombol navigasi halaman (1, 2, 3, ..., 7).

**Langkah-Langkah Mengelola Material:**

1. Pada sidebar, klik menu **Stok Material** di bawah bagian *Divisi Gudang*.
2. Halaman **Master Data Material** akan terbuka.
3. **Untuk mencari material:** Ketik nama material di kolom pencarian, atau gunakan filter Status dan Tipe.
4. **Untuk menambah material baru:**
   - Klik tombol **+ Tambah Material** (berwarna hijau, pojok kanan atas).
   - Isi data: nama, tipe, ukuran, satuan, harga beli, dan stok awal.
   - Klik **Simpan**.
5. **Untuk mengubah data material:**
   - Klik ikon **Edit** (pensil) pada baris material yang ingin diubah.
   - Ubah data yang diperlukan.
   - Klik **Simpan** untuk menyimpan perubahan.
6. **Untuk menghapus material:**
   - Klik ikon **Hapus** (tempat sampah) pada baris material yang ingin dihapus.
   - Konfirmasi penghapusan. Material akan masuk ke *Sampah* dan masih bisa dipulihkan.
7. **Untuk mengimpor data dari Excel:**
   - Klik ikon **Impor** pada deretan tombol aksi massal.
   - Unggah file Excel sesuai template yang telah disediakan.
   - Sistem akan memvalidasi data dan menolak jika terdapat duplikasi.
8. **Untuk mengekspor data ke Excel:**
   - Klik ikon **Ekspor** pada deretan tombol aksi massal.
   - File Excel berisi seluruh data material akan terunduh otomatis.

> **Catatan Penting:**
> - Sistem secara otomatis mencegah duplikasi material. Jika Anda mencoba menambahkan material dengan kombinasi Nama, Tipe, Ukuran, dan Satuan yang sudah ada, sistem akan menolak dan menampilkan pesan error.
> - Saat melakukan impor Excel, sistem akan memvalidasi seluruh baris terlebih dahulu. Jika ditemukan baris duplikat (baik di dalam file Excel maupun dengan data di database), seluruh proses impor akan dibatalkan dan tidak ada data yang tersimpan (prinsip all-or-nothing).

---

## Lampiran

### A. Daftar Singkatan

| Singkatan | Kepanjangan |
|---|---|
| SPK | Surat Perintah Kerja |
| QC | Quality Control |
| CS | Customer Service |
| WH-IN | Warehouse In (Barang Masuk) |
| WH-OUT | Warehouse Out (Barang Keluar) |
| MFST | Manifest |
| PIC | Person in Charge (Penanggung Jawab) |
| PCP | Pick-up / Courier / Pengiriman |
| HK | Hari Kerja |
| DP | Down Payment (Uang Muka) |
| OTW | On The Way (Dalam Perjalanan) |

### B. Kontak Dukungan

Jika Anda mengalami kendala atau memiliki pertanyaan terkait penggunaan sistem, silakan hubungi:

- **Tim IT & Pengembangan Sistem:** Divisi Teknologi Informasi Shoe Workshop
- **Email:** support@shoeworkshop.id

---

*Dokumen ini bersifat rahasia dan hanya ditujukan untuk penggunaan internal Shoe Workshop.*  
*© 2026 Shoe Workshop — Seluruh Hak Dilindungi.*

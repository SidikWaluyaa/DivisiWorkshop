# 📋 Laporan Hasil Kerja & Fitur Baru Sistem Workshop
**Hari & Tanggal:** Kamis, 24 Juli 2026

Laporan ini memuat daftar pekerjaan yang telah diselesaikan hari ini.

---

## Daftar Pekerjaan Selesai

### 1. 🖨️ Cetak Label Alamat Polos
* **Kenapa dibuat:** Tim pengiriman butuh cetakan label alamat yang simpel dan hemat tinta. Cukup berisi Nama, Alamat, dan Nomor Telepon pelanggan saja, tanpa gambar, warna, atau hiasan apapun, supaya kurir bisa langsung baca dengan cepat.
* **Apa yang berubah:**
  * Ditambahkan tombol **"Print Alamat Polos"** di halaman detail pesanan. Tinggal klik, langsung keluar halaman cetak yang polos hitam putih di kertas A5.
  * Posisi teks alamat sudah disesuaikan agar pas di tengah area kertas, tidak terlalu atas dan tidak terlalu bawah.
  * Begitu halaman cetak terbuka, jendela print otomatis muncul, jadi tidak perlu tekan Ctrl+P lagi.

### 2. 🔍 Pencarian Cepat & Saran Jasa Otomatis di Halaman CX
* **Kenapa dibuat:** Saat CS ingin menambahkan jasa baru ke pesanan pelanggan, sebelumnya harus mencari dari daftar yang sangat panjang dan sering salah pilih atau salah harga.
* **Apa yang berubah:**
  * Sekarang ada **kotak pencarian** di dalam daftar jasa. CS cukup ketik kata kunci (misal: "Clean"), dan daftar langsung tersaring otomatis.
  * Jika teknisi di bengkel sudah merekomendasikan jasa tertentu, rekomendasi itu akan muncul sebagai **tombol saran** di bagian atas. Tinggal klik sekali, semua kolom (Kategori, Nama Jasa, Harga, dan Hari Kerja) langsung terisi otomatis. Tidak perlu ketik manual lagi.

### 3. 🔔 Tombol Panggil Pengambilan Sepatu (Lonceng Real-Time)
* **Kenapa dibuat:** Supaya CS atau admin bisa langsung memanggil petugas gudang untuk mengambil sepatu yang sudah selesai, tanpa harus teriak atau jalan ke gudang.
* **Apa yang berubah:**
  * Di halaman detail pesanan, ada tombol **"Panggil Pengambilan"** bergambar lonceng. Begitu diklik, panggilan langsung masuk ke sistem.
  * Di sisi petugas gudang (Sandi, Admin), browser mereka akan **langsung berbunyi bel terus-menerus** dan muncul jendela pop-up berisi detail lengkap pesanan: Nomor SPK, Nomor Invoice, Status Bayar, Nama & Telepon Pelanggan, Merek/Tipe/Warna/Ukuran Sepatu, Foto Sepatu, Lokasi Rak Penyimpanan, Status Pengerjaan, dan Catatan SPK.
  * Jika ada lebih dari 1 panggilan menunggu, akan muncul penanda **"+ X Antrean Lain"** agar petugas tahu masih ada panggilan berikutnya.
  * Lokasi rak ditampilkan terpisah: **Rak Inbound**, **Rak Aksesoris**, dan **Rak Finish**, supaya petugas langsung tahu harus ambil di mana. Jika sepatu belum ditempatkan di rak manapun, informasi rak otomatis disembunyikan.
  * Di pop-up juga ada tombol **"Buka Stasiun ➔"** yang langsung membuka halaman stasiun terkait di tab baru.
  * Begitu pop-up ditutup (dengan cara apapun: klik tombol Tutup, tekan Esc, klik tanda X, atau klik di luar), bel otomatis berhenti dan panggilan ditandai sudah dibaca. Status pesanan tidak terpengaruh sama sekali.

### 4. 👥 Penambahan Pilihan PIC Gudang di Halaman Pengiriman
* **Kenapa dibuat:** Sebelumnya, kolom pilihan PIC Gudang di halaman Pengiriman kosong karena belum ada nama yang terdaftar.
* **Apa yang berubah:**
  * Sekarang kolom PIC Gudang sudah berisi 3 nama petugas: **Indra**, **Elin**, dan **Sandi**. Tinggal pilih siapa yang bertanggung jawab untuk setiap pengiriman.

### 5. 🖨️ Perbaikan Tampilan Cetak Label Alamat
* **Kenapa diperbaiki:** Ada garis putus-putus yang muncul di bagian bawah kertas cetak label alamat. Garis ini tidak diperlukan dan mengganggu tampilan hasil cetak.
* **Apa yang berubah:**
  * Garis putus-putus sudah dihapus. Sekarang hasil cetak label alamat bersih tanpa garis apapun.

### 6. 🔌 Pembuatan API Baru untuk Sinkronisasi Data CX ke Google Sheets
* **Kenapa dibuat:** Supaya tim bisa memantau data kendala CX secara terpusat di Google Sheets operasional dan datanya bisa diperbarui secara otomatis (sistem Upsert) tanpa menimbulkan data ganda (duplikat).
* **Apa yang berubah:**
  * Membuat berkas API baru di [`public/api/sync_cx_all.php`](file:///c:/laragon/www/SistemWorkshop/public/api/sync_cx_all.php) yang menggabungkan data kendala CX dari **Workshop/Bengkel** (Prep, Sortir, Prod, QC Revisions) dengan info pesanan.
  * Data yang ditarik dibatasi hanya dari **bulan Mei 2026 sampai sekarang** (`created_at >= '2026-05-01'`).
  * Sesuai instruksi, info **Waktu Masuk Divisi CX** dan **Waktu Klik SEND (Tanggal Kirim)** diposisikan di urutan paling awal format data JSON agar otomatis tampil di kolom depan spreadsheet Anda.
  * API baru ini bersifat terpisah sehingga tidak mengganggu integrasi API lainnya yang sudah ada.

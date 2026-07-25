# 📋 Laporan Kerja - Sistem Workshop
**Hari & Tanggal:** Sabtu, 25 Juli 2026

Berikut adalah daftar pekerjaan yang dikerjakan hari ini:

---

## 🛠️ Pekerjaan yang Diselesaikan Hari Ini

### 1. ⚙️ Optimalisasi Keamanan Stok Gudang dari Race Condition (Bentrokan Data)
* **Masalah:** Validasi ketersediaan barang keluar sebelumnya dilakukan di luar transaksi database. Jika dua petugas menekan tombol keluar bersamaan untuk barang yang sama, sistem bisa memproses keduanya secara bersamaan sehingga stok di database berpotensi menjadi minus (negatif).
* **Solusi:** Memindahkan seluruh pengecekan stok ke dalam transaksi database (`DB::transaction`) dan menerapkan row-locking (`lockForUpdate`). Ketika stok sedang diverifikasi atau diubah, baris data material tersebut dikunci sementara di tingkat database agar transaksi lain mengantre secara aman.
* **Impact:** Mencegah terjadinya error stok negatif (minus) di database, menjamin keakuratan data stok 100%, dan membuat sistem antrean transaksi material menjadi sangat aman.

### 2. 🔗 Tautan Dokumen Transaksi pada Riwayat Mutasi Barang
* **Masalah:** Tabel Riwayat Mutasi sebelumnya hanya menampilkan catatan mutasi secara teks mentah, tanpa menampilkan nomor dokumen transaksi asal (seperti `WH-IN-...` atau `WH-OUT-...`) yang bisa diklik. Admin gudang kesulitan mencocokkan mutasi dengan nota pengadaan atau pengeluaran barang yang sebenarnya.
* **Solusi:** Memperbarui model data mutasi agar mendeteksi tipe transaksi secara otomatis dan menampilkan badge dokumen belanja (`📦 WH-IN-xxx`) atau barang keluar (`📄 WH-OUT-xxx`) di tabel Riwayat Mutasi. Badge tersebut dihubungkan langsung sebagai link aktif ke halaman detail transaksi asal.
* **Impact:** Memudahkan tim audit dan admin gudang untuk melakukan penelusuran balik (traceability) dokumen asal dari log mutasi secara instan hanya dengan sekali klik.

### 3. 🚨 Penanganan Duplikasi Nomor Urut Dokumen (Auto-Retry)
* **Masalah:** Nomor nota belanja dan barang keluar dibuat secara otomatis berdasarkan urutan transaksi hari itu. Jika ada dua petugas menyimpan transaksi di detik yang sama, sistem rentan mengalami error *duplicate key* karena mencoba menyimpan nomor nota yang sama.
* **Solusi:** Membungkus proses penyimpanan nota belanja dan pengeluaran dalam mekanisme loop `try-catch` (maksimal 3 kali percobaan). Jika terdeteksi bentrokan nomor nota unik, sistem secara otomatis me-regenerate nomor baru dengan hitungan terkini lalu menyimpannya kembali tanpa memunculkan error ke pengguna.
* **Impact:** Menghilangkan error crash halaman saat ada aktivitas penyimpanan transaksi gudang yang dilakukan secara bersamaan oleh beberapa admin.

### 4. 💰 Perekaman Harga Aktual Mutasi Bahan Baku
* **Masalah:** Mutasi masuk/keluar sebelumnya mencatat harga berdasarkan harga katalog umum material yang statis. Jika harga beli bahan baku berfluktuasi dari waktu ke waktu, total nilai aset mutasi keluar/masuk di log riwayat menjadi tidak akurat.
* **Solusi:** Memodifikasi pencatatan mutasi agar mengambil harga beli/keluar aktual yang diinputkan petugas pada detail item transaksi belanja (`WarehousePurchaseItem`) atau pengeluaran (`WarehouseDisbursementItem`).
* **Impact:** Laporan keuangan mutasi barang gudang menjadi sangat akurat karena mencerminkan harga riil transaksi saat kejadian, bukan harga perkiraan katalog.

### 5. 🧹 Pembersihan Master Data Material Duplikat
* **Masalah:** Terdapat 92 entri master data material ganda di database (di mana material dengan nama, ukuran, tipe, dan satuan yang sama terdaftar dengan ID berbeda). Ini membuat data stok terpecah, sehingga ketika belanja berhasil menambahkan stok pada satu ID, ID lainnya yang identik tetap menunjukkan stok 0 dan membingungkan pengguna.
* **Solusi:** Membuat dan menjalankan script konsolidasi database. Script mendeteksi 92 grup duplikasi, menggabungkan jumlah stok masing-masing ke entri utama (Primary ID), memperbarui semua relasi transaksi belanja/keluar yang merujuk ke ID duplikat, lalu menghapus entri duplikat secara permanen.
* **Impact:** Menghilangkan kebingungan data stok ganda, memastikan keakuratan pelaporan stok di dashboard, dan membersihkan database dari record sampah.

### 6. 🛡️ Sistem Pencegahan Duplikasi Data Master Material Baru
* **Masalah:** Formulir pembuatan dan pengeditan material sebelumnya tidak melakukan validasi keunikan kombinasi kolom. Akibatnya, admin dapat berulang kali mendaftarkan material dengan nama, tipe, ukuran, dan satuan yang sama, yang memicu duplikasi data di database dan mengacaukan perhitungan stok.
* **Solusi:**
  - **Validasi Formulir:** Menambahkan pengecekan duplikasi pada method `store` dan `update` di `MaterialController.php`. Jika material dengan nama, tipe, ukuran, dan satuan yang sama sudah ada, sistem akan membatalkan proses dan memunculkan pesan peringatan ramah pengguna.
  - **Unique Constraint Database:** Membuat migrasi database untuk menambahkan indeks unik (`UNIQUE INDEX`) pada kolom `name`, `size`, `type`, dan `unit` di tabel `materials` sebagai proteksi keamanan data lapis terakhir.
* **Impact:** Mencegah terjadinya duplikasi data master material baru 100% selamanya, baik yang dibuat melalui formulir web, API, maupun script lainnya.

### 7. 🐛 Perbaikan Harga Nol dan Sinkronisasi ID saat Import Excel Material
* **Masalah:**
  - Ketika pengguna mengekspor data material kemudian mengimpor kembali file tersebut, harga material berubah menjadi `Rp 0`. Hal ini disebabkan perbedaan header kolom: file ekspor menggunakan nama kolom `Price (Rp)` (yang di-slugifikasi menjadi `price_rp`), sedangkan script import hanya mencari nama kolom `price` dari template.
  - Proses import juga tidak memanfaatkan kolom `id` dari file ekspor untuk melakukan update langsung ke database, melainkan selalu mencari berdasarkan kombinasi nama, tipe, dan ukuran yang berpotensi memicu masalah jika ada kesamaan nama.
* **Solusi:**
  - **Dukungan Header Ganda:** Memperbarui `MaterialsImport.php` agar membaca harga baik dari kolom `price` (file template) maupun `price_rp` (file ekspor).
  - **Pencarian Berbasis ID:** Menambahkan logika pencarian data lama menggunakan kolom `id` terlebih dahulu (jika kolom `id` tersedia di file Excel) sebelum melakukan fallback ke pencarian berbasis kombinasi nama, tipe, dan ukuran.
* **Impact:** Proses ekspor-impor material berjalan 100% mulus tanpa merusak nilai harga (tidak menjadi 0 lagi), dan pembaruan data material lama terjamin akurat karena disinkronkan langsung berdasarkan ID unik material.

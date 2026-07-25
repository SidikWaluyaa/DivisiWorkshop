# 📋 Laporan Kerja - Sistem Workshop
**Hari & Tanggal:** Sabtu, 25 Juli 2026

Berikut adalah daftar pekerjaan yang dikerjakan hari ini:

---

## 🛠️ Pekerjaan yang Diselesaikan Hari Ini

### 1. ⚙️ Pencegahan Selisih Stok Akibat Transaksi Bersamaan
* **Masalah:** Sistem pengecekan stok sebelumnya kurang ketat. Jika dua orang petugas gudang mengurangi stok untuk barang yang sama di detik yang sama, sistem bisa bingung dan meloloskan keduanya. Hal ini berpotensi membuat stok barang di sistem menjadi minus (di bawah nol).
* **Solusi:** Memperbaiki cara sistem membaca stok dengan menerapkan metode "antrean terkunci". Ketika satu petugas sedang memeriksa dan mengurangi stok suatu barang, sistem akan mengunci data barang tersebut sekejap agar transaksi dari petugas lain mengantre secara rapi dan aman.
* **Dampak:** Tidak akan ada lagi stok barang yang bernilai minus di sistem, data stok dijamin 100% akurat, dan transaksi pengeluaran barang menjadi jauh lebih aman.

### 2. 🔗 Link Klik Dokumen pada Riwayat Aliran Barang
* **Masalah:** Halaman Riwayat Aliran Barang (Masuk/Keluar) sebelumnya hanya menampilkan nama barang secara teks biasa tanpa informasi nota transaksi yang jelas. Admin gudang kesulitan melacak nota mana yang menjadi asal usul masuk atau keluarnya barang tersebut.
* **Solusi:** Menambahkan tombol/tautan nota belanja (`📦 WH-IN-...`) atau nota pengeluaran (`📄 WH-OUT-...`) di sebelah riwayat barang. Ketika tombol ini diklik, admin akan langsung diarahkan ke halaman detail transaksi nota tersebut.
* **Dampak:** Memudahkan admin gudang atau tim pemeriksa keuangan untuk melacak asal usul barang secara instan hanya dengan sekali klik.

### 3. 🚨 Pencegahan Error Nomor Nota Kembar Secara Otomatis
* **Masalah:** Nomor nota belanja atau pengeluaran barang gudang dibuat berurutan secara otomatis. Jika ada dua petugas menyimpan nota di detik yang sama, sistem bisa mengalami error (layar mendadak putih/error) karena mencoba menyimpan nomor nota yang sama.
* **Solusi:** Menambahkan fitur deteksi otomatis. Jika sistem mendeteksi adanya bentrokan nomor nota karena disimpan bersamaan, sistem akan langsung membuat nomor baru secara otomatis di latar belakang dan menyimpannya kembali tanpa mengganggu petugas yang sedang bekerja.
* **Dampak:** Menghilangkan error layar macet (crash) saat banyak petugas gudang sedang membuat transaksi di waktu yang bersamaan.

### 4. 💰 Pencatatan Harga Asli Barang Masuk & Keluar
* **Masalah:** Riwayat barang masuk dan keluar sebelumnya selalu menggunakan harga standar katalog yang tidak berubah. Padahal, harga beli bahan baku aslinya bisa naik atau turun. Hal ini membuat nilai total aset gudang yang tercatat di laporan menjadi tidak akurat.
* **Solusi:** Mengubah sistem agar riwayat barang masuk/keluar mencatat harga beli asli yang dimasukkan oleh petugas saat transaksi belanja berlangsung, bukan harga katalog statis.
* **Dampak:** Nilai keuangan barang gudang yang dilaporkan menjadi sangat akurat karena menggunakan harga beli riil saat kejadian.

### 5. 🧹 Pembersihan Nama Barang yang Terdaftar Ganda
* **Masalah:** Di dalam sistem terdapat 92 data barang yang terdaftar ganda (misalnya ada dua baris untuk barang yang sama persis: nama, tipe, ukuran, dan satuannya sama, tetapi terdaftar dengan kode ID berbeda). Ini membuat jumlah stok terpecah dan membingungkan pengguna karena stok barang belanjaan masuk ke ID yang satu, sementara pengguna melihat ID lainnya yang stoknya tetap nol.
* **Solusi:** Menjalankan program pembersih database untuk mendeteksi 92 barang ganda tersebut, menggabungkan seluruh jumlah stoknya ke satu barang utama, memperbarui semua nota transaksi lama agar menunjuk ke barang utama tersebut, lalu menghapus data ganda secara permanen.
* **Dampak:** Menghilangkan kebingungan stok ganda, memastikan laporan stok di dashboard 100% akurat, dan merapikan database dari data sampah.

### 6. 🛡️ Sistem Pencegahan Input Barang Ganda di Masa Depan
* **Masalah:** Formulir untuk menambah dan mengubah data barang sebelumnya tidak memeriksa apakah barang tersebut sudah ada. Admin bisa mendaftarkan barang yang sama berkali-kali tanpa sengaja, yang kemudian memicu duplikasi data di database.
* **Solusi:**
  - **Pengecekan Formulir:** Menambahkan pemeriksaan otomatis saat admin menekan tombol Simpan. Jika barang dengan nama, tipe, ukuran, dan satuan yang sama sudah terdaftar, sistem akan membatalkan proses dan memunculkan pesan peringatan ramah pengguna.
  - **Kunci Database:** Memasang pengaman permanen di database agar database menolak jika ada data kembar yang mencoba masuk melalui jalur mana pun.
* **Dampak:** Mencegah terjadinya barang ganda baru selamanya, baik yang diinput lewat website maupun sistem impor lainnya.

### 7. 🐛 Perbaikan Harga Rp 0 saat Import Excel Barang
* **Masalah:** Ketika mengekspor data barang ke Excel lalu mengimpornya kembali, harga seluruh barang mendadak berubah menjadi `Rp 0`. Ini terjadi karena nama kolom harga di file ekspor berbeda dengan nama kolom harga yang dibaca oleh sistem impor. Selain itu, sistem impor tidak menggunakan kolom ID barang dari file Excel sehingga pembaruan data kurang akurat.
* **Solusi:**
  - **Dukungan Nama Kolom Ganda:** Memperbarui sistem impor agar bisa mengenali harga dari format template maupun dari format file hasil ekspor.
  - **Pencarian dengan ID:** Sistem impor kini mendeteksi kolom ID terlebih dahulu untuk memperbarui data barang yang sudah ada agar tidak terjadi kesalahan pembaruan.
* **Dampak:** Proses ekspor-impor barang berjalan lancar tanpa merusak nilai harga (harga tidak berubah menjadi Rp 0 lagi), dan pembaruan data barang lama menjadi sangat akurat.

### 8. 🩹 Pemulihan Harga Barang yang Sempat Rusak Menjadi Nol
* **Masalah:** Akibat proses impor Excel yang bermasalah sebelumnya, sebanyak 92 data barang di sistem sempat mengalami kerusakan data di mana harganya berubah menjadi `Rp 0`.
* **Solusi:** Melacak file hasil ekspor terakhir di komputer Anda dan memulihkan harga asli ke-92 barang tersebut di database dengan cara menyalin kembali harganya dari file Excel cadangan tersebut secara otomatis dan aman.
* **Dampak:** Semua data harga barang yang sempat rusak/menjadi nol berhasil dikembalikan ke nilai aslinya dengan selamat tanpa merusak jumlah stok barang saat ini.

### 9. 🛑 Pembatalan Impor Excel Otomatis Jika Ada Data Ganda
* **Masalah:** Proses impor Excel sebelumnya tetap berjalan meskipun ada data ganda di dalam file Excel atau ada data yang bertabrakan dengan barang di sistem. Sistem secara sepihak memperbarui data tersebut tanpa memberitahu pengguna letak kesalahannya.
* **Solusi:**
  - **Pemeriksaan Sebelum Menyimpan:** Mengubah cara kerja sistem agar memeriksa seluruh baris data di file Excel terlebih dahulu sebelum melakukan penyimpanan apa pun ke database.
  - **Deteksi Bentrokan:** Sistem akan memeriksa apakah ada baris yang kembar di dalam file Excel tersebut, atau apakah data tersebut sudah terdaftar di sistem.
  - **Pembatalan Total:** Jika ada satu saja baris yang bermasalah, proses impor akan dibatalkan secara keseluruhan (tidak ada data setengah masuk) dan sistem akan menampilkan daftar nomor baris beserta nama barang yang bermasalah di layar.
* **Dampak:** Data di sistem dijamin bersih dan aman dari data ganda hasil impor Excel, serta mempermudah petugas gudang untuk mengetahui baris mana saja yang perlu diperbaiki di Excel.

### 10. 🔌 Pembuatan Jalur Koneksi (API) Baru untuk Sinkronisasi Penjualan
* **Masalah:** Sistem belum memiliki jalur khusus (API) yang aman untuk mengirimkan data SPK (Surat Perintah Kerja) yang tertunda selama tepat 11 hari ke spreadsheet eksternal (Google Sheets).
* **Solusi:** Membuat file konektor aman bernama `sync_closing.php` di dalam folder `public/api/` yang menggunakan kueri basis data untuk mengelompokkan pesanan berdasarkan nomor telepon dan nama pelanggan, lalu mengirimkannya sebagai data terstruktur (JSON).
* **Dampak:** Proses pemantauan pesanan yang tertunda menjadi otomatis dan dapat disinkronkan langsung ke Google Sheets secara cepat dan aman menggunakan kunci pengaman (token).

### 11. 📦 Pembuatan Fitur Pembersih Data Ganda untuk Server (Seeder)
* **Masalah:** Saat menjalankan migrasi di server produksi (aaPanel), migrasi gagal karena database server masih memiliki data barang ganda yang bertabrakan dengan aturan indeks unik yang baru.
* **Solusi:** Membuat file pembersih database yang dinamai `CleanDuplicateMaterialsSeeder.php` di dalam folder `database/seeders/` untuk menyaring dan menggabungkan data ganda di server secara aman.
* **Dampak:** Proses pembersihan data ganda di database server dapat dijalankan secara instan dan aman menggunakan satu baris perintah di terminal server.

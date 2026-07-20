# 📋 Laporan Hasil Kerja & Fitur Baru Sistem Workshop
**Hari & Tanggal:** Senin, 20 Juli 2026

Laporan ini disusun dengan bahasa sederhana dan ramah agar mudah dipahami oleh seluruh tim operasional, admin, maupun manajemen.

---

## 1. 📦 Desain Baru Modal Pilih Material Belanja (`/warehouse/purchase/create`) & Barang Keluar

### 💡 Mengapa Fitur Ini Dibuat?
Sebelumnya, saat admin memilih material pada form pencatatan **Belanja Material** maupun **Barang Keluar**, modal pencarian hanya menampilkan nama material dan angka stok sederhana. Hal ini menyulitkan admin jika ada beberapa material dengan nama yang mirip tetapi jenis/kategori atau ukurannya berbeda (contoh: *Vans Gum Size 40* vs *Vans Gum Size 42*).

### 🌟 Ringkasan Perubahan & Manfaatnya:
1. **Penyajian Data Lengkap di Modal:**
   * **Nama Material:** Teks tebal & jelas di baris atas.
   * **🏷️ Badge Tipe/Jenis:** Label biru dengan ikon tag (contoh: `🏷️ Sol Rubber`, `🏷️ Upper`).
   * **📏 Badge Ukuran (Size):** Label ungu dengan ikon penggaris (contoh: `📏 Ukuran: 42` atau `Tanpa Size`).
   * **📦 Badge Stok Interaktif:** Kapsul indikator berwarna hijau terang jika stok tersedia (`Stok: 15`) dan berwarna merah jika stok kosong (`Stok: 0`).
   * **💰 Harga Acuan:** Menampilkan harga acuan barang jika ada.

2. **Pencarian Lebih Pintar & Cepat:**
   * Admin kini dapat mencari material berdasarkan **Nama**, **Tipe/Jenis**, maupun **Ukuran (Size)** sekaligus di dalam satu kolom pencarian (contoh: mengetik `"40"`, `"Vans"`, atau `"Sol"` akan langsung menampilkan barang yang pas).
   * Fitur pembersih pencarian (tombol silang `X`) untuk menghapus teks pencarian secara instan.

3. **Penyajian Rapi pada Tabel Barang Terpilih:**
   * Setelah material dipilih dan masuk ke dalam tabel daftar barang belanja/keluar, badge Tipe, Size, dan Stok tetap ditampilkan di bawah nama material agar tim gudang tidak keliru saat memeriksa nota belanja.

---

## 2. ⚡ Analisis Layanan Prioritas "Fast Track" di Dashboard V2

### 💡 Mengapa Fitur Ini Dibuat?
Layanan **Fast Track** adalah layanan kilat/prioritas dengan tarif khusus. Sebelumnya, tim kesulitan melihat secara cepat berapa total sepatu Fast Track yang masuk, berapa total uang yang didapatkan, dan SPK mana saja yang pengerjaannya terlambat atau bermasalah.

### 🌟 Ringkasan Fitur & Manfaatnya:
Di halaman utama **Dashboard V2**, sekarang terdapat **5 Kartu Informasi Warna-Warni** yang bisa **diklik langsung** untuk melihat daftar sepatu di dalamnya:

1. **🚀 Total Fast Track (Kartu Hijau):**
   * **Fungsi:** Menampilkan jumlah seluruh sepatu Fast Track yang sedang dikerjakan maupun yang sudah selesai.
   * **Detail Tambahan:** Langsung memperlihatkan rincian berapa sepatu yang *masih aktif dikerjakan* dan berapa yang *sudah selesai*.

2. **💰 Total Pendapatan Fast Track (Kartu Biru):**
   * **Fungsi:** Menjumlahkan total uang yang masuk khusus dari layanan Fast Track dalam rentang tanggal yang dipilih.

3. **🔴 Fast Track Gagal SLA / Terlambat (Kartu Merah):**
   * **Fungsi:** Otomatis mendeteksi sepatu Fast Track yang waktu pengerjaannya melebihi batas target di setiap ruangan/stasiun kerja (misal: di stasiun produksi lewat dari 4 hari).
   * **Detail Tambahan:** Saat diklik, muncul daftar SPK lengkap dengan tulisan merah yang menjelaskan persis di stasiun mana pengerjaan tersebut terlambat dan berapa hari keterlambatannya.

4. **🛠️ Fast Track Gagal Operasional (Kartu Oranye):**
   * **Fungsi:** Mengelompokkan sepatu Fast Track yang terhambat **bukan karena teknisi terlambat**, melainkan karena faktor lain seperti:
     * *Tambah Jasa Baru:* Sepatu diturunkan dari Fast Track ke pengerjaan biasa karena ada tambahan perawatan baru di tengah jalan.
     * *Menunggu Konfirmasi Pelanggan (CX FollowUp):* Pengerjaan terhenti sementara karena admin sedang menunggu jawaban/persetujuan dari pemilik sepatu.
     * *Batal/Donasi:* Sepatu dibatalkan oleh pelanggan.

5. **⏳ Fast Track Pending CS (Kartu Ungu Baru):**
   * **Fungsi:** Khusus menampilkan pesanan Fast Track yang baru dibuat oleh CS tetapi **belum masuk ke workshop** (masih menunggu pengecekan atau verifikasi fisik sepatu).
   * **Detail Tambahan:** Menampilkan estimasi calon uang/omzet yang akan didapatkan jika pesanan pending tersebut resmi masuk.

---

## 3. ⏱️ Perhitungan Waktu Keterlambatan (SLA) Berbasis Waktu Masuk Ruangan

### 💡 Mengapa Fitur Ini Dibuat?
Sebelumnya, sistem menghitung keterlambatan sepatu di stasiun Produksi berdasarkan tanggal nota dibuat. Hal ini tidak adil bagi teknisi di ruangan Produksi, karena sepatu bisa saja lama tertahan di stasiun pencucian (Preparation) sebelum sampai ke meja produksi.

### 🌟 Perubahan Baru:
* **Penghitungan Lebih Adil:** Waktu pengerjaan stasiun sekarang **mulai dihitung sejak sepatu secara fisik resmi masuk ke ruangan tersebut** (berdasarkan catatan riwayat perpindahan status).
  * *Batas Sortir:* Maksimal 3 Hari sejak masuk ruangan Sortir.
  * *Batas Preparation:* Maksimal 1 Hari untuk Fast Track.
  * *Batas Produksi:* Maksimal 4 Hari sejak masuk ruangan Produksi.
  * *Batas QC:* Maksimal 1 Hari untuk Fast Track.
* **Kotak Informasi SLA Transparan:** Jika rincian sepatu di papan kerja diklik, akan muncul kotak penjelasan transparan yang memuat:
  * Kapan nota dibuat.
  * Jam & tanggal pasti sepatu masuk ke ruangan saat ini.
  * Berapa hari & jam sepatu tersebut sudah dikerjakan di ruangan aktif.
  * Target maksimal pengerjaannya.

---

## 4. 🗑️ Tempat Sampah (Trash Bin) Data Bahan & Material

### 🌟 Ringkasan Fitur:
* **Tombol "Sampah" Baru:** Di halaman Data Material kini ada tombol merah bertuliskan **"Sampah"** dengan angka indikator berapa banyak bahan yang terhapus.
* **Fitur Pulihkan (Restore):** Bahan yang terhapus bisa dikembalikan lagi ke daftar utama dalam 1 kali klik.
* **Hapus Permanen Aman:** Bahan dapat dibersihkan selamanya dari database secara aman tanpa merusak riwayat transaksi lama.
* **Tombol Pemulihan Massal:** Admin bisa memulihkan atau menghapus puluhan data sampah sekaligus di semua halaman dalam satu kali tombol.

---

## 5. 📏 Kolom Ukuran (Size) Material & Input Form yang Lebih Luas

### 🌟 Ringkasan Perubahan:
* **Kolom Ukuran Terpisah:** Pada tabel data material, ukuran (Size) kini memiliki kolom tersendiri yang rapi sehingga tidak lagi menumpuk di bawah nama bahan.
* **Form Ukuran Selalu Terbuka:** Saat menambah atau mengubah data bahan jenis apa pun, kolom isi ukuran selalu tersedia secara opsional.

---

## 6. 📥 Kemudahan Import Excel Data Material

### 🌟 Ringkasan Perubahan:
* **Mendukung Ukuran Berbeda:** Jika mengunggah file Excel berisi bahan dengan nama yang sama tetapi ukurannya berbeda (contoh: *Sol Rubber Size 40* dan *Sol Rubber Size 42*), sistem akan otomatis mencatatnya sebagai dua barang yang terpisah dan tidak akan saling menimpa.
* **Membaca Format Rp Otomatis:** Tulisan harga di Excel seperti `"Rp 15.000"` atau `"15.000"` otomatis dibaca dan dirapikan oleh sistem menjadi angka bersih `15000`.

---

## 🔌 Sinkronisasi Data Garansi ke Google Sheets

* Skrip penghubung data garansi (`sync_warranties.php`) dan klaim garansi pelanggan (`sync_warranty_claim.php`) telah diselaraskan agar laporan di Google Sheets selalu otomatis terbarui dengan data terkini di sistem.

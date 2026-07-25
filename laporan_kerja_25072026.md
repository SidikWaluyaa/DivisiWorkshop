# 📋 Rencana Kerja Hari Ini - Sistem Workshop
**Hari & Tanggal:** Sabtu, 25 Juli 2026

Berikut adalah daftar pekerjaan yang direncanakan untuk dikerjakan hari ini guna meningkatkan kecepatan sistem, kerapian kode, dan kemudahan operasional tim:

---

## 🛠️ Daftar Pekerjaan Hari Ini

### 1. ⚙️ Pembuatan Template Script Google Sheets untuk API Baru
* **Kenapa dikerjakan:** Membantu tim operasional agar bisa langsung memakai jalur data baru (`sync_cx_all.php`) yang dibuat kemarin di Google Sheets mereka.
* **Rencana Pekerjaan:** 
  * Menuliskan petunjuk lengkap dan contoh kode **Google Apps Script** yang siap disalin (copy-paste) ke Google Sheets.
  * Kode ini akan otomatis mencocokkan data berdasarkan Nomor SPK (melakukan *Upsert*), sehingga data di Google Sheets selalu terupdate tanpa ada duplikasi data.

### 2. 🚀 Optimalisasi Popup Peringatan (SweetAlert2) agar Lebih Cepat & Mandiri
* **Kenapa dikerjakan:** Saat ini beberapa halaman masih mengambil file popup SweetAlert2 dari server luar (CDN online). Jika koneksi internet lambat, popup peringatan atau konfirmasi bisa gagal muncul atau loading lama.
* **Rencana Pekerjaan:** 
  * Memindahkan pengambilan file SweetAlert2 dari CDN online ke penyimpanan lokal server menggunakan Vite.
  * Ini akan membuat popup peringatan muncul instan dan tetap berfungsi meskipun server sedang dalam kondisi offline/lokal.

### 3. 🧹 Perbaikan & Perapian Kode Halaman Rak Penyimpanan (`/storage`)
* **Kenapa dikerjakan:** Kode pembuat popup (modal) di halaman rak penyimpanan masih menggunakan metode JavaScript lama (Vanilla JS), sementara halaman lainnya sudah menggunakan teknologi modern (Alpine.js). Perbedaan ini membuat kode sulit dirawat.
* **Rencana Pekerjaan:** 
  * Mengubah script JavaScript lama di halaman rak penyimpanan menjadi **Alpine.js** agar seluruh kode sistem seragam, rapi, dan lebih mudah jika ingin dikembangkan lagi di kemudian hari.

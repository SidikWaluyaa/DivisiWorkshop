# 📅 Rencana & Agenda Kerja Sistem Workshop
**Hari & Tanggal:** Jumat, 24 Juli 2026

Halo! Hari ini kita akan fokus melakukan evaluasi terhadap pembaruan fitur yang baru saja diselesaikan, serta mempersiapkan langkah operasional berikutnya agar sistem berjalan makin prima.

Berikut adalah daftar agenda kerja dan evaluasi untuk hari ini:

---

## 1. ✅ Evaluasi & Review Fitur yang Baru Selesai (Rabu - Kamis)
Sebelum memulai pengerjaan baru, kita perlu memastikan fitur-fitur penting yang baru dideploy berjalan dengan lancar tanpa kendala:
* **Perbaikan Collapsible & Dropdown Stasiun:** Memastikan halaman antrean di stasiun **Preparation**, **Production**, dan **QC** kini sudah 100% bebas dari error JavaScript di HP maupun PC teknisi (collapsible lancar dibuka-tutup).
* **Prioritas Tertinggi SPK "CX RESOLVED":** Memastikan SPK yang sudah disetujui pelanggan otomatis melompat ke urutan teratas (nomor 1) di antrean stasiun operasional untuk mengejar keterlambatan.
* **Sinkronisasi Kolom Baru ke Google Sheets:** Memverifikasi bahwa data estimasi waktu tambahan (`estimasi_tambahan`) sudah berhasil masuk ke Google Sheets operasional melalui jalur sinkronisasi API terbaru.

---

## 2. 📋 Agenda Rencana Kerja Hari Ini (Jumat, 24 Juli 2026)

### A. Pengujian & Validasi Langsung di Lapangan (Staging / Produksi)
* [ ] **Uji Coba Lapor Kendala Teknisi:** Mencoba membuat laporan kendala baru dari akun teknisi stasiun kerja, memilih rekomendasi tambah jasa, menentukan tambahan hari, dan mengirimkan laporan.
* [ ] **Uji Coba WhatsApp / CS Dashboard:** Memastikan Tim CS menerima laporan kendala tersebut di dashboard `/cx`, dan timestamp waktu kirim (`sent_at`) tercatat dengan presisi ketika CS mengubah status ke **SEND**.
* [ ] **Uji Coba Autocomplete Search:** Memastikan Tim CS dapat mencari nama jasa dengan cepat menggunakan kotak pencarian (search) dan dapat melakukan klik sekali pada badge saran dari teknisi untuk pengisian otomatis.

### B. Pemantauan Server & Kestabilan Database
* [ ] **Cek Log Error:** Memeriksa log error sistem setelah pembaruan database (migrasi) di server aaPanel selesai dilakukan, guna memastikan tidak ada query database yang bermasalah akibat perbedaan versi database MariaDB/MySQL.

### C. Diskusi Opsi Fitur Berikutnya (Mohon Masukan Anda)
Hari ini adalah momen yang tepat untuk merencanakan fitur berikutnya. Beberapa opsi area yang bisa kita tingkatkan:
1. **Peningkatan SLA (Target Waktu):** Mengoptimalkan kalkulasi target waktu pengerjaan sepatu agar secara otomatis memperhitungkan hari libur nasional atau jam operasional toko.
2. **Manajemen Stok Otomatis:** Menghubungkan material/bahan baku sol yang digunakan saat perbaikan (dari dropdown "Tambah Jasa") agar otomatis memotong stok barang di modul Gudang.
3. **Optimasi Desain Mobile:** Mempercantik halaman cetak SPK/Nota agar lebih ramah printer termal/kasir ukuran kecil.

---

> 💡 **Bagaimana menurut Anda?** Apakah ada kendala operasional lain yang Anda temukan hari ini di lapangan yang perlu kita prioritaskan terlebih dahulu? Silakan beri tahu saya agar bisa langsung kita eksekusi bersama!

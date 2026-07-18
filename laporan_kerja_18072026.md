# 📋 Rangkuman Perbaikan & Fitur Baru — Sabtu, 18 Juli 2026

Berikut adalah catatan pekerjaan hari ini yang ditulis dengan bahasa sederhana agar mudah dipahami oleh tim operasional (non-teknis).

---

## 1. 📅 Pengisian Hari Kerja (SLA) Tambahan & Penyesuaian Otomatis

*   **Masalah Sebelumnya:** Ketika ada penambahan jasa/cuci sepatu di tengah jalan, staf kesulitan memperbarui estimasi tanggal selesai. Angka hari kerja tidak bertambah, dan seringkali tanggal selesainya berbeda-beda antara invoice kasir dengan dashboard pengerjaan di workshop (bikin bingung pelanggan & kurir).
*   **Perubahan Baru (Solusi):**
    *   Sekarang, setiap kali menambah jasa pengerjaan, sudah disediakan kolom **Hari Kerja (HK)** yang terisi otomatis (dan bisa diubah secara bebas).
    *   Sistem akan **menghitung otomatis** tanggal selesainya berdasarkan hari kerja tersebut (hari Minggu otomatis dilewati agar hitungannya pas).
    *   Tanggal selesai di Invoice dan kartu pengerjaan sepatu di workshop sekarang **pasti sama persis** sehingga tidak ada lagi selisih informasi.
    *   Jika jasa tambahan dihapus, tanggal estimasi selesai otomatis kembali maju ke jadwal semula secara otomatis.

---

## 2. 📝 Cetakan SPK yang Bersih (Bebas Catatan Internal)

*   **Masalah Sebelumnya:** Ketika cetak kertas instruksi kerja (SPK) untuk tim cuci, catatan masalah internal dari tim Customer Experience (seperti detail kendala pelanggan, solusi alternatif) ikut tercetak di kertas. Hal ini membuat cetakan terlihat berantakan dan kurang profesional.
*   **Perubahan Baru (Solusi):**
    *   Kami telah membersihkan semua template kertas cetak SPK. Sekarang, **seluruh catatan internal komplain dari tim CX otomatis disembunyikan secara rapi** dari kertas print SPK. Kertas print hanya akan menampilkan instruksi kerja murni untuk pencuci/teknisi.

---

## 3. 📑 Excel Mode: Edit & Tambah Jasa Sekaligus (Massal)

*   **Masalah Sebelumnya:** Jika admin ingin mengubah harga atau menambah banyak jasa baru ke daftar katalog, admin harus mengklik dan menyimpan layanannya satu per satu. Ini sangat memakan waktu.
*   **Perubahan Baru (Solusi):**
    *   Kami membuat halaman baru bernama **Excel Mode**. Tampilannya mirip seperti tabel Microsoft Excel / Google Sheets di mana admin bisa langsung mengetik harga, nama, durasi, dan kategori jasa langsung di baris tabelnya.
    *   Admin juga bisa menambah banyak baris baru sekaligus dengan tombol `+ Tambah Baris` dan menyimpannya secara bersamaan dalam satu kali klik tombol **Simpan**.

---

## 4. 🔍 Fitur Pencarian Cepat & Format Titik Ribuan pada Jasa

*   **Masalah Sebelumnya:** Susah mencari jasa jika barisnya terlalu banyak, dan pengetikan angka harga tanpa titik (misal `275000`) sulit dibaca oleh mata admin.
*   **Perubahan Baru (Solusi):**
    *   **Pencarian Instan:** Disediakan kolom cari di halaman Excel Mode. Cukup ketik sepatah kata, maka daftar jasa langsung tersaring saat itu juga tanpa *loading* lemot. Ketikan yang belum disimpan tidak akan hilang saat Anda mencari data.
    *   **Format Titik Otomatis:** Saat mengetik harga (misal mengetik `250000`), sistem secara otomatis mengubahnya menjadi `250.000` dengan titik pemisah ribuan agar mata admin nyaman melihatnya. Saat disimpan, sistem di belakang layar otomatis membersihkan titik tersebut agar masuk ke database sebagai angka biasa.

---

## 5. 🔄 Perbaikan Alur Status Sepatu (Anti-Nyasar)

*   **Masalah Sebelumnya:** Kadang, sepatu yang sudah berada di stasiun kerja workshop (seperti tahap gosok/cuci) tiba-tiba terlempar kembali ke status "Pemeriksaan Awal (Assessment)" setelah admin melakukan tambah jasa. Hal ini membuat alur kerja tim teknis menjadi berantakan.
*   **Perubahan Baru (Solusi):**
    *   Sistem sekarang lebih pintar dalam mengenali riwayat sepatu. Jika sepatu sedang dikerjakan di workshop dan ada penambahan jasa, setelah disetujui, sepatu akan **kembali ke tempat pengerjaan asalnya** (tidak akan nyasar lagi ke tahap pemeriksaan awal).

---

## 6. 🔌 Fitur Baru: API Data Garansi untuk Google Sheets

*   **Masalah Sebelumnya:** Tim manajemen memerlukan cara mudah untuk memantau data garansi pengerjaan sepatu (`work_order_warranties`) di spreadsheet Google Sheets secara otomatis tanpa ekspor file manual setiap hari.
*   **Perubahan Baru (Solusi):**
    *   Kami membuat pintu masuk data khusus (**API**) di `/public/api/sync_warranties.php`.
    *   Pintu ini dilindungi oleh kunci rahasia (*Token*) agar tidak bisa diakses orang asing.
    *   Ketika ditarik oleh Google Sheets, data garansi akan disajikan lengkap beserta informasi nomor SPK asal, nama pemilik sepatu, merk, jenis sepatu, nama pembuat garansi, tanggal selesai, hingga **tautan link foto** yang siap diklik.

---

## 🗃️ Catatan Tambahan (Logistik Rak):
*   Riwayat penugasan rak sepatu di timeline aktivitas kini tercatat lengkap, termasuk kapan sepatu otomatis dilepas dari rak inbound saat dicuci, maupun ketika admin melepas rak secara manual.

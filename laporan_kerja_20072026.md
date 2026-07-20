# 📋 Rangkuman Perbaikan & Fitur Baru — Senin, 20 Juli 2026

Berikut adalah catatan pekerjaan hari ini yang ditulis dengan bahasa sederhana agar mudah dipahami oleh tim operasional (non-teknis).

---

## 1. 🚀 Fitur Baru: Modul Analisis & Interaksi Fast Track di Dashboard V2 (SLA & Non-SLA)

*   **Masalah Sebelumnya:** Halaman Dashboard V2 belum memiliki pemisahan analisis antara kegagalan akibat keterlambatan durasi stasiun (SLA) dan kegagalan akibat faktor operasional (seperti penambahan jasa di tengah jalan, penahanan CX FollowUp, atau pembatalan).
*   **Perubahan Baru (Solusi):**
    1.  **4 Kartu KPI Interaktif Baru:**
        *   **Total Fast Track:** Menghitung total order Fast Track dalam periode terpilih (Contoh: *🚀 7 Aktif, 3 Selesai*).
        *   **Pendapatan Fast Track:** Menjumlahkan total transaksi riil dari seluruh SPK Fast Track (Contoh: *💰 Berdasarkan 10 SPK*).
        *   **Fast Track Gagal SLA:** Menampilkan jumlah SPK Fast Track yang murni gagal memenuhi batas durasi waktu stasiun kerja (Prep: > 1 hari, Sortir: > 3 hari, Prod: > 4 hari, QC: > 1 hari).
        *   **Fast Track Gagal Operasional (Non-SLA):** Menampilkan jumlah SPK Fast Track yang gagal/terhenti akibat faktor operasional non-SLA, lengkap dengan subteks rincian jumlahnya (Contoh: *🛠️ 3 Tambah Jasa, 1 CX FollowUp, 0 Batal*).
    2.  **Modal Detail Klik-Untuk-Lihat:**
        *   Admin/Operator dapat mengklik salah satu dari 4 kartu KPI untuk memicu modal popup yang menampilkan tabel daftar SPK terkait secara instan.
        *   Khusus untuk modal **Gagal Operasional**, sistem menampilkan badge berwarna sesuai penyebab spesifiknya:
            *   `🔄 Downgrade: Penambahan Jasa Baru` (SPK yang diturunkan statusnya menjadi regular karena ada jasa baru yang ditambah).
            *   `💬 CX FollowUp: Menunggu Konfirmasi` (SPK yang terhenti karena menunggu respons pelanggan).
            *   `❌ Status Batal / Donasi` (SPK yang dibatalkan atau didonasikan).

---

## 2. ⏱️ Fitur Baru: Perhitungan SLA Stasiun Kerja Berbasis Tanggal Masuk Stasiun & Detail Transparan

*   **Masalah Sebelumnya:** Perhitungan keterlambatan stasiun bersifat kaku dan langsung menandai SPK terlambat di stasiun Produksi sejak hari pertama masuk hanya karena SPK-nya dibuat beberapa hari yang lalu (misal tertahan di pencucian). Hal ini tidak adil bagi teknisi stasiun aktif dan tidak mencerminkan waktu kerja yang sebenarnya.
*   **Perubahan Baru (Solusi):**
    1.  **SLA Dihitung Sejak Masuk Stasiun:**
        *   **Sortir:** Batas waktu **3 Hari** sejak status SPK berubah ke `SORTIR` (atau sejak dibuat jika tidak ada log).
        *   **Preparation:** Batas waktu **1 Hari** (Fast Track) sejak status SPK berubah ke `PREPARATION`.
        *   **Production:** Batas waktu **4 Hari** sejak status SPK berubah ke `PRODUCTION`.
        *   **QC:** Batas waktu **1 Hari** (Fast Track) sejak status SPK berubah ke `QC`.
        *   Jika pengerjaan berjalan di stasiun terkait melewati batas hari tersebut sejak waktu masuknya, sistem akan memunculkan badge merah tanda peringatan secara dinamis.
    2.  **Box Informasi SLA di Panel Collapsible:**
        *   Setiap kartu SPK yang diklik/dibuka detailnya kini menampilkan box **"Informasi SLA Stasiun"** yang transparan.
        *   Box ini memuat informasi:
            *   **SPK Dibuat (Created At):** Tanggal & waktu SPK pertama kali didaftarkan.
            *   **Masuk Stasiun Ini:** Tanggal & waktu riil ketika status SPK berubah masuk ke stasiun saat ini (berdasarkan log transisi status).
            *   **Durasi di Stasiun:** Menghitung persis berapa hari & jam sepatu tersebut telah dikerjakan di stasiun aktif berjalan saat ini.
            *   **Batas Target SLA:** Keterangan teks penjelasan batas aman pengerjaan dihitung sejak masuk stasiun.

---

## 3. 🗑️ Fitur Baru: Trash Bin (Tempat Sampah) Master Data Material

*   **Perubahan Baru (Solusi):**
    1.  **Tombol Pintasan Sampah:** Di halaman Master Data Material kini terdapat tombol merah bertuliskan **"Sampah"** di samping tombol eksport, lengkap dengan angka badge kecil yang menunjukkan berapa banyak material yang sedang terhapus.
    2.  **Halaman Tempat Sampah Baru:** Mengklik tombol tersebut akan mengarahkan admin ke halaman khusus `/admin/materials/trash` yang berisi daftar seluruh material yang sudah terhapus.
    3.  **Fungsi Pulihkan (Restore) & Hapus Permanen:** Admin dapat memulihkan material ke halaman utama secara instan atau menghapusnya secara permanen dari database secara aman (sistem otomatis membersihkan seluruh referensi agar tidak memicu error data).
    4.  **Aksi Massal & Bypass Pagination:** Admin dapat memulihkan atau menghapus permanen **seluruh data sampah di semua halaman sekaligus** dalam sekali klik tanpa perlu mencentang halaman satu per satu!

---

## 4. 📏 Penyempurnaan Kolom Ukuran (Size) & Input Size yang Selalu Terbuka

*   **Perubahan Baru (Solusi):**
    1.  **Kolom Dedikasi "Ukuran" Baru:** Halaman utama `/admin/materials` dan halaman Tempat Sampah `/admin/materials/trash` kini memiliki kolom **"Ukuran"** baru yang terpisah. Ukuran material sekarang ditampilkan rapi di kolom tersendiri sehingga sangat mudah dibaca dan dibandingkan.
    2.  **Input Size Selalu Terbuka:** Pembatasan form telah dihapus. Sekarang, kolom input **Size** akan selalu muncul secara default untuk semua jenis material saat melakukan Tambah Baru maupun Edit (tidak disembunyikan untuk non-Sol).

---

## 5. 📥 Perbaikan Import Excel Material: Pemisahan Berdasarkan Ukuran & Harga Rupiah

*   **Perubahan Baru (Solusi):**
    1.  **Pengecekan Unik Berbasis Ukuran (Size):** Logika pencarian data pada import Excel [MaterialsImport.php](file:///c:/laragon/www/SistemWorkshop/app/Imports/MaterialsImport.php) kini telah diperbarui agar menyertakan kolom **Ukuran (Size)**. Sekarang, material dengan nama sama tetapi ukuran berbeda akan di-import sebagai baris data yang mandiri dan tidak akan saling menimpa!
    2.  **Pemasukan Harga Rupiah Otomatis:** Sistem kini dilengkapi pembersih harga cerdas yang otomatis mendeteksi dan membuang tulisan `"Rp"`, spasi, serta mengubah tanda titik ribuan format Indonesia menjadi desimal bersih (contoh: teks `"Rp 12.000"` dari Excel otomatis dibaca dan disimpan sebagai angka desimal `12000` di database).

---

## 🔌 Pembaruan API Data Garansi & Claim untuk Google Sheets (GitHub Pushed)

*   **Perubahan Baru (Solusi):**
    *   Kami memperbaiki dan menyelaraskan dua skrip sinkronisasi data API ekspor:
        *   **`sync_warranties.php`** diselaraskan kembali untuk mengekspor data garansi utama dari tabel `work_order_warranties`.
        *   **`sync_warranty_claim.php`** diselaraskan untuk mengekspor data klaim garansi yang diajukan pelanggan dari tabel `warranty_claims` secara lengkap dengan relasi SPK asalnya.
    *   Kedua file API ini telah berhasil dicommit dan didorong (*push*) secara eksklusif ke repositori GitHub Anda demi perbaikan di server produksi.

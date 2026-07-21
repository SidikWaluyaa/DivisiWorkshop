# 📋 Laporan Hasil Kerja & Fitur Baru Sistem Workshop
**Hari & Tanggal:** Selasa, 21 Juli 2026

Laporan ini disusun dengan bahasa sederhana dan ramah agar mudah dipahami oleh seluruh tim operasional, admin, maupun manajemen.

---

## 1. ⚠️ Dokumentasi & Spesifikasi Database Modal "Lapor Kendala / Follow Up" (`cx_issues`)

### 💡 Mengapa Dokumentasi Ini Dibuat?
Modal **"⚠️ Lapor Kendala / Follow Up"** merupakan sarana penting bagi tim di workshop (Gudang, Preparation, Produksi, dan QC) untuk melaporkan kendala teknis, masalah bahan, penyesuaian waktu (*Overload*), maupun permohonan konfirmasi kepada pelanggan yang ditangani langsung oleh Tim Customer Experience (CX).

### 🌟 Tabel & Kolom yang Terlibat:

1. **Tabel Utama (`cx_issues`):**
   * `work_order_id`: Menghubungkan laporan dengan SPK/Order yang bermasalah.
   * `spk_number`, `customer_name`, `customer_phone`: Identitas cepat SPK dan pelanggan.
   * `category`: Kategori masalah (`TEKNIS`, `MATERIAL`, `OVERLOAD`, `KONFIRMASI`).
   * `kendala_1` & `kendala_2`: Isian detail masalah pertama dan kedua.
   * `opsi_solusi_1` & `opsi_solusi_2`: Pilihan solusi perbaikan yang ditawarkan ke customer.
   * `description`: Teks rangkuman kendala + solusi, atau tanggal estimasi selesai baru (jika Overload).
   * `photos`: Menyimpan array foto bukti dalam format JSON.
   * `source`: Asal ruangan/stasiun tempat laporan dibuat (`GUDANG`, `WORKSHOP_PREP`, `WORKSHOP_PROD`, `WORKSHOP_QC`).
   * `status`: Status laporan (`OPEN` = menunggu respons CS, `RESOLVED` = selesai ditangani).
   * `reported_by` & `resolved_by`: Identitas user pelapor dan petugas CS penanggung jawab.

2. **Tabel Master Dropdown:**
   * **`cx_master_issues`**: Menyimpan daftar master opsi **Detail Kendala / Topik Konfirmasi**.
   * **`cx_master_solutions`**: Menyimpan daftar master opsi **Opsi Solusi Perbaikan**.

3. **Tabel Relasi (`work_orders` & `users`):**
   * Saat laporan dikirim, status SPK pada `work_orders` otomatis berubah menjadi **`CX_FOLLOWUP`** agar pengerjaan fisik sepatu ditahan sementara hingga pelanggan memberikan persetujuan.

---

## 2. ⏱️ Penambahan Field Estimasi Waktu Tambahan (`estimasi_tambahan`) & Dropdown Master Jasa (`services`)

### 💡 Mengapa Fitur Ini Dibuat?
Saat teknisi di workshop menemukan kendala teknis atau bahan, pengerjaan fisik sepatu tentu memerlukan tambahan waktu pengerjaan. Sebelumnya, tidak ada kolom khusus untuk menentukan berapa hari durasi waktu tambahan tersebut. Selain itu, teknisi juga memerlukan tempat untuk memilih rekomendasi jasa perawatan/perbaikan tambahan resmi yang terintegrasi dengan daftar harga master.

### 🌟 Ringkasan Perubahan & Manfaatnya:

1. **⏱️ Input "Estimasi Waktu Tambahan" (Kolom Baru `estimasi_tambahan` - VARCHAR):**
   * Pada modal Lapor Kendala, kini tersedia **Dropdown Pilihan Estimasi Tambahan Waktu** dengan opsi cepat: `1 HARI`, `2 HARI`, `3 HARI`, `4 HARI`, `5 HARI`, `7 HARI`, `10 HARI`, `14 HARI`, maupun pilihan `Lainnya (Ketik Manual)`.
   * **Manfaat CS:** Informasi waktu tambahan (misal `"3 HARI"`) otomatis terangkum dalam laporan sehingga Tim CS bisa langsung menginfokan penundaan waktu yang pasti kepada pelanggan via WhatsApp.

2. **🛠️ Dropdown "Rekomendasi Tambah Jasa Baru" Terintegrasi Tabel `services` (`rec_service_1` & `rec_service_2`):**
   * Pilihan Rekomendasi Jasa 1 & 2 diubah dari kolom teks polos menjadi **Dropdown Interaktif yang terhubung langsung ke Master Data Jasa (`services` table)**.
   * **Menampilkan Harga Resmi:** Setiap opsi jasa di dropdown menampilkan nama jasa sekaligus tarif resminya (contoh: *"Fast Clean - Rp 50.000"*, *"Reglue Heavy - Rp 85.000"*).
   * **Manfaat CS & Teknisi:** Nama jasa seragam & standar tanpa typo, serta Tim CS langsung tahu tarif harga jasa tersebut untuk ditawarkan ke pelanggan.
   * **Tetap Fleksibel:** Menyediakan pilihan `"Lainnya (Ketik Manual)..."` jika ada perbaikan kustom yang belum terdaftar di tabel master `services`.

3. **🗃️ Migration Database & Controller:**
   * Berhasil menjalankan migration `2026_07_21_100000_add_estimasi_tambahan_to_cx_issues_table.php` untuk menambahkan kolom `estimasi_tambahan` bertipe `VARCHAR(50)`.
   * Memperbarui `CxIssueController` & Model `CxIssue` untuk memproses dan merangkum seluruh input baru secara otomatis.

---

## 3. 🖥️ Penyajian Informasi Lengkap pada Dashboard CX (`/cx`), Modal Edit, & Laporan Publik

### 💡 Mengapa Fitur Ini Dibuat?
Sebelumnya pada halaman utama **Dashboard CX (`/cx`)**, kolom **Detail Kendala (Issue)** hanya menampilkan kartu *Detail Kendala* dan *Opsi Solusi*. Informasi penting mengenai **Estimasi Waktu Tambahan** dan **Rekomendasi Tambah Jasa Baru** belum muncul secara langsung di baris tabel pesanan.

### 🌟 Ringkasan Perubahan & Manfaatnya:

1. **⏱️ Kartu Amber (Estimasi Waktu Tambahan):**
   * Di baris tabel `/cx`, kini tampil kartu berwarna amber dengan ikon jam yang dengan jelas memperlihatkan estimasi waktu tambahan pengerjaan (contoh: `⏱️ Estimasi Waktu Tambahan: 3 HARI`).

2. **🛠️ Kartu Ungu (Rekomendasi Tambah Jasa Baru):**
   * Di bawah opsi solusi pada baris tabel `/cx`, tampil kartu berwarna ungu yang merangkum daftar rekomendasi jasa 1 & 2 dari teknisi beserta tarif resminya (contoh: `1. Reglue Heavy (Rp 85.000)`).

3. **✏️ Dukungan pada Modal Edit CS & Laporan Publik:**
   * Modal Edit Issue Livewire (`edit-issue-modal`) dan Halaman Laporan Publik (`cx/issue-report.blade.php`) telah diperbarui agar dapat menampilkan dan memperbarui data `estimasi_tambahan` dan rekomendasi jasa secara konsisten.

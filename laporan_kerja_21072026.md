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

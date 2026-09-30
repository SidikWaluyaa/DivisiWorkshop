<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap');

  @media print {
    @page {
      size: A4;
      margin: 15mm 15mm 20mm 15mm;
    }
  }

  body {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    color: #0f172a;
    background: #ffffff;
    font-size: 10pt;
    line-height: 1.6;
  }

  h1 {
    font-size: 18pt;
    color: #1e1b4b;
    border-bottom: 3px solid #4f46e5;
    padding-bottom: 6px;
    margin-top: 0;
    margin-bottom: 12px;
    page-break-after: avoid;
  }

  h2 {
    font-size: 13pt;
    color: #0f172a;
    background: #f8fafc;
    border-left: 4px solid #4f46e5;
    padding: 6px 12px;
    margin-top: 22px;
    margin-bottom: 12px;
    border-radius: 0 6px 6px 0;
    page-break-after: avoid;
  }

  h3 {
    font-size: 11pt;
    color: #3730a3;
    margin-top: 16px;
    margin-bottom: 8px;
    page-break-after: avoid;
  }

  h4 {
    font-size: 10pt;
    color: #475569;
    border-left: 3px solid #cbd5e1;
    padding-left: 8px;
    margin-top: 12px;
    margin-bottom: 6px;
    page-break-after: avoid;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    margin-bottom: 16px;
    font-size: 9pt;
    page-break-inside: avoid;
  }

  tr {
    page-break-inside: avoid;
    page-break-after: auto;
  }

  thead {
    display: table-header-group;
  }

  th {
    background-color: #f1f5f9 !important;
    color: #0f172a !important;
    font-weight: 800;
    text-align: left;
    padding: 7px 10px;
    border: 1px solid #cbd5e1;
    -webkit-print-color-adjust: exact;
  }

  td {
    padding: 6px 10px;
    border: 1px solid #cbd5e1;
    vertical-align: top;
  }

  code {
    font-family: 'JetBrains Mono', monospace;
    font-size: 8.5pt;
    background-color: #f1f5f9;
    color: #334155;
    padding: 2px 5px;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
  }

  pre {
    background-color: #0f172a;
    color: #f8fafc;
    padding: 12px;
    border-radius: 8px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 8.5pt;
    line-height: 1.45;
    overflow-x: auto;
    margin-bottom: 14px;
    page-break-inside: avoid;
  }

  pre code {
    background: transparent;
    border: none;
    color: inherit;
    padding: 0;
  }

  .page-break {
    page-break-before: always;
  }

  .badge {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 4px;
    font-size: 8pt;
    font-weight: 700;
    -webkit-print-color-adjust: exact;
  }

  .badge-indigo { background-color: #e0e7ff !important; color: #3730a3 !important; }
  .badge-amber { background-color: #fef3c7 !important; color: #92400e !important; }
  .badge-emerald { background-color: #d1fae5 !important; color: #065f46 !important; }
  .badge-rose { background-color: #ffe4e6 !important; color: #9f1239 !important; }
  .badge-slate { background-color: #f1f5f9 !important; color: #334155 !important; }

  .callout {
    border-radius: 8px;
    padding: 12px 16px;
    margin: 12px 0 16px 0;
    border-left: 4px solid;
    page-break-inside: avoid;
  }

  .callout-info {
    background-color: #eff6ff;
    border-color: #3b82f6;
    color: #1e40af;
  }

  .callout-warning {
    background-color: #fffbeb;
    border-color: #f59e0b;
    color: #92400e;
  }

  .callout-success {
    background-color: #f0fdf4;
    border-color: #22c55e;
    color: #166534;
  }

  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 18px;
    page-break-inside: avoid;
  }

  .kpi-card {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 12px;
    background: #f8fafc !important;
    text-align: center;
    -webkit-print-color-adjust: exact;
  }

  .kpi-num {
    font-size: 16pt;
    font-weight: 900;
    color: #4f46e5;
    margin-top: 4px;
  }

  .kpi-label {
    font-size: 8pt;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
  }

  .signature-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 30px;
    page-break-inside: avoid;
  }

  .signature-box {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 14px;
    text-align: center;
    background: #ffffff;
  }
</style>

# 🎯 LAPORAN RENCANA KERJA STRATEGIS & ROADMAP OPERASIONAL — OKTOBER 2026
**Dokumen Referensi:** `docs/ALUR_DAN_ARSITEKTUR_SPK_RND.md` & `docs/ALUR_DAN_ARSITEKTUR_MANAJEMEN_BRAND.md`  
**Periode Pelaksanaan:** 1 Oktober 2026 s/d 31 Oktober 2026  
**Status Dokumen:** ✅ Disetujui untuk Eksekusi Sprint (*Ready for Implementation*)  
**Lead Architect & Developer:** AI Senior Full Stack Developer & Workshop Tech Lead (Big 4 Standard)  
**Target Modul:** Modul 1 — SPK Divisi R&D & Modul 2 — Manajemen Mitra Brand Sepatu B2B  

---

## 📊 1. RINGKASAN EKSEKUTIF & TARGET STRATEGIS BULAN OKTOBER 2026

Bulan Oktober 2026 ditetapkan sebagai fase eksekusi rekayasa perangkat lunak (*Software Engineering Execution Phase*) untuk merealisasikan dua pilar arsitektur master yang telah dirancang, diaudit, dan disepakati pada akhir September 2026. 

Kedua modul ini memperluas kapabilitas sistem dari yang sebelumnya berfokus pada **Reparasi Ritel Konsumen (B2C)** menjadi **Ekosistem Workshop Terpadu** yang mencakup **Riset Prototipe Produk (R&D)** serta **Pengerjaan Pesanan Partai Besar Pabrik / Brand (B2B)**.

<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-label">Target Durasi</div>
    <div class="kpi-num">4 Minggu</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Fokus Modul Utama</div>
    <div class="kpi-num">2 Modul Inti</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Estimasi User Story</div>
    <div class="kpi-num">28 Deliverables</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Toleransi Bug/Breaking</div>
    <div class="kpi-num">0% (Zero Risk)</div>
  </div>
</div>

### 🎯 4 Sasaran Strategis Utama:
1. **Penyelesaian Modul SPK R&D (Minggu 1 & 2)**:
   - Menerapkan tabel tracking progres non-destruktif `work_order_rnd_progress` tanpa merusak tabel inti `work_orders`.
   - Mengaktifkan isolasi antrean 5 stasiun kerja riset, penomoran `RND-YYMM-DD-XXXX`, lembar kerja fisik A4 Deep Indigo (`#4f46e5`), dan mekanisme evaluasi sampel akhir (*Approved / Revise / Rejected*).
2. **Penyelesaian Modul Manajemen Brand Sepatu B2B (Minggu 3 & 4)**:
   - Membangun master rekanan brand (`brand_partners`), kloter pesanan batch (`brand_orders`), dan integrasi otomatis ke `work_orders` child.
   - Menyediakan antarmuka **Smart Size Matrix Input** (sebaran ukuran 36–46), import spreadsheet Excel ratusan pasang, serta sistem cetak fisik hybrid (1 SPK Induk Batch A4 + Grid Stiker Barcode Satuan).
3. **Penjaminan Zero-Breaking Changes terhadap Modul Eksisting**:
   - Alur kasir/ritel CS (`customers`), integrasi pengadaan bahan Finlog (`finlog.shoeworkshop.id`), dan flow stasiun reparasi reguler tidak mengalami perubahan perilaku (*side-effects*).
4. **Proteksi Disiplin Logistik & Gudang**:
   - Penerapan status khusus `BRAND_FINISHED` dan isolasi kardus master B2B untuk memastikan sepatu kemitraan brand tidak bocor ke rak pengambilan kasir CS (*store pickup*).

---

<div class="page-break"></div>

## 🗺️ 2. ROADMAP EKSEKUSI 4 MINGGU (SPRINT BREAKDOWN)

Pelaksanaan pekerjaan dibagi secara tegas ke dalam 4 Sprint Mingguan terstruktur:

| Sprint | Periode Tanggal | Fokus Modul & Deliverables Utama | Output & Milestone Kunci |
| :--- | :--- | :--- | :--- |
| **Sprint 1** | 01 Okt – 09 Okt 2026 | **Modul SPK R&D (Backend, Database & Core Logic)** | Migrasi DB, Model `WorkOrderRndProgress`, Generator Nomor `RND-xxxx`, Service Alokasi Material Finlog R&D. |
| **Sprint 2** | 12 Okt – 16 Okt 2026 | **Modul SPK R&D (Frontend Livewire & Stasiun Kerja)** | Wizard Input R&D, UI Stasiun 5 Tahap, Cetak SPK A4 Indigo, Evaluasi QC R&D, UAT & Sign-off Modul 1. |
| **Sprint 3** | 19 Okt – 23 Okt 2026 | **Modul Manajemen Brand (Database & Batch Inbound)** | Migrasi `brand_partners` & `brand_orders`, Smart Size Matrix Engine, Import Excel Batch, Profil Brand 360°. |
| **Sprint 4** | 26 Okt – 31 Okt 2026 | **Modul Manajemen Brand (Lantai Bengkel & Dispatch)** | Generator `work_orders` child, Cetak Hybrid (SPK A4 Amber + Stiker Barcode), Status `BRAND_FINISHED`, Surat Jalan B2B, UAT Modul 2. |

---

### 📅 Rincian Rencana Kerja Harian Per Sprint:

#### 🔹 SPRINT 1 (Minggu 1: 01 – 09 Oktober 2026) — Fondasi Backend Modul SPK R&D
- **Hari 1 (Kamis, 1 Okt 2026)**:
  - Pembuatan database migration tabel pendamping `work_order_rnd_progress` (kolom: `work_order_id`, `sample_phase`, `upper_material`, `sole_material`, `target_weight_gram`, `flexibility_score`, `qc_eval_status`, `eval_notes`, `evaluator_id`).
  - Pembuatan model Eloquent `WorkOrderRndProgress` dan pendefinisian relasi `belongsTo` / `hasOne` pada model `WorkOrder`.
- **Hari 2 (Jumat, 2 Okt 2026)**:
  - Pembuatan enum / konstanta sistem khusus R&D: kategori jenis riset (`PROTOTYPE_NEW`, `MATERIAL_TRIAL`, `SOLE_BONDING_TEST`, `WEAR_TEST_SAMPLE`), fase stasiun, dan status evaluasi QC.
  - Implementasi generator nomor SPK otomatis `RND-YYMM-DD-XXXX` dengan sequential lock anti-duplikasi pada `WorkOrderService`.
- **Hari 3 (Senin, 5 Okt 2026)**:
  - Integrasi isolasi data: penambahan scope query `scopeRndOrders()` dan `scopeExcludeRnd()` pada model `WorkOrder` agar order riset otomatis terisolasi dari antrean reguler.
- **Hari 4 (Selasa, 6 Okt 2026)**:
  - Perancangan penyesuaian integrasi Finlog API khusus kategori material R&D (pemberian flag `is_rnd_material = true` saat *purchase requisition*).
- **Hari 5 (Rabu, 7 Okt 2026)**:
  - Pembuatan automated seeders untuk simulasi pengujian SPK R&D (`RndWorkOrderSeeder.php`).
- **Hari 6 (Kamis, 8 Okt 2026)**:
  - Penulisan Unit Test backend untuk alur validasi input, nomor SPK, dan relasi tabel progres.
- **Hari 7 (Jumat, 9 Okt 2026)**:
  - Sprint Review 1 & Code Audit internal backend SPK R&D.

---

#### 🔹 SPRINT 2 (Minggu 2: 12 – 16 Oktober 2026) — Antarmuka Livewire & Stasiun Kerja SPK R&D
- **Hari 8 (Senin, 12 Okt 2026)**:
  - Pembuatan komponen Livewire Form Pembuatan SPK R&D (`App\Livewire\Workshop\RndCreateOrder`).
  - Antarmuka wizard input: detail riset, spesifikasi material upper & sol, parameter teknis target bobot, serta tanggal uji coba.
- **Hari 9 (Selasa, 13 Okt 2026)**:
  - Pembuatan halaman navigasi & dashboard sentral antrean SPK R&D (`/workshop/rnd`) dengan filter jenis riset dan tab status aktif.
- **Hari 10 (Rabu, 14 Okt 2026)**:
  - Penyesuaian antarmuka stasiun kerja lantai bengkel khusus pengerjaan R&D (5 tahap: *1. Pattern / Pola*, *2. Cutting & Upper*, *3. Bottoming & Soling*, *4. Finishing*, *5. Evaluasi QC*).
  - Mekanisme input catatan teknis teknisi riset per tahap.
- **Hari 11 (Kamis, 15 Okt 2026)**:
  - Pembuatan halaman cetak lembar SPK R&D format A4 Portrait siap cetak (`/workshop/rnd/{id}/print`) bertema Deep Indigo (`#4f46e5`) lengkap dengan kolom parameter teknis dan lembar cek fisik laboratorium.
- **Hari 12 (Jumat, 16 Okt 2026)**:
  - Implementasi modal evaluasi akhir QC R&D (*Lulus Uji Produksi*, *Perlu Revisi Sampel*, *Gagal / Ditolak*).
  - User Acceptance Testing (UAT) internal untuk seluruh siklus hidup SPK R&D.

---

#### 🔹 SPRINT 3 (Minggu 3: 19 – 23 Oktober 2026) — Master Rekanan & Batch Inbound Brand B2B
- **Hari 13 (Senin, 19 Okt 2026)**:
  - Pembuatan migrasi database:
    1. Tabel master mitra brand: `brand_partners` (kode brand, nama rekanan, PIC, nomor kontak, email, alamat pabrik, logo, status).
    2. Tabel order batch masuk: `brand_orders` (nomor batch `BR-YYMM-DD-XXXX`, `brand_partner_id`, nama proyek/PO, total kuantitas, tanggal masuk, target selesai, status).
    3. Penambahan kolom relasi `brand_order_id` (nullable FK) pada tabel `work_orders`.
- **Hari 14 (Selasa, 20 Okt 2026)**:
  - Pembuatan model `BrandPartner`, `BrandOrder`, relasi Eloquent, serta Policy otorisasi RBAC (hanya Admin Workshop & Manager yang berhak).
  - Pembuatan komponen CRUD Master Mitra Brand (`/admin/brand-partners`) lengkap dengan fitur upload logo dan riwayat kerja sama.
- **Hari 15 (Rabu, 21 Okt 2026)**:
  - Pembuatan halaman Detail Mitra Brand 360° View: kartu profil perusahaan, metrik total pasang terproses, dan daftar riwayat seluruh batch PO.
- **Hari 16 (Kamis, 22 Okt 2026)**:
  - Pengembangan komponen **Smart Size Matrix Input Engine**:
    - User menginput nama artikel / model sepatu & layanan.
    - Grid matriks ukuran dinamis (size 36 s/d 46) dengan kalkulasi otomatis total pasang secara real-time.
- **Hari 17 (Jumat, 23 Okt 2026)**:
  - Integrasi fitur Impor Spreadsheet Excel untuk kiriman ratusan pasang sepatu batch brand via Maatwebsite Excel.
  - Sprint Review 3 & validasi integritas struktur master data brand.

---

#### 🔹 SPRINT 4 (Minggu 4: 26 – 31 Oktober 2026) — Lantai Produksi, Cetak Hybrid & Dispatch B2B
- **Hari 18 (Senin, 26 Okt 2026)**:
  - Implementasi generator pemecahan otomatis (*Batch-to-Individual Generator*):
    - Dari 1 `brand_orders` (misal 50 pasang), sistem membuat 50 baris record `work_orders` dengan nomor seri satuan `BR-YYMM-DD-XXXX-01` s/d `BR-YYMM-DD-XXXX-50`.
    - Pengisian otomatis atribut: `shoe_brand`, `shoe_type`, `shoe_size`, `current_location`, dan relasi `work_order_services`.
- **Hari 19 (Selasa, 27 Okt 2026)**:
  - Pengembangan antarmuka **Hybrid Printing System**:
    1. Lembar SPK Induk Batch A4 bertema Amber Gold (`#d97706`) untuk ditempel di kardus master / map kontrol proyek.
    2. Lembar Grid Stiker Barcode Satuan ukuran ringkas siap tempel pada badan sepatu untuk scanning di stasiun kerja teknisi.
- **Hari 20 (Rabu, 28 Okt 2026)**:
  - Integrasi stasiun kerja lantai bengkel (`Preparation` ➔ `Sortir` ➔ `Production` ➔ `QC`).
  - Barcode scanner otomatis mengenali nomor SPK satuan brand tanpa perlu penyesuaian hardware.
- **Hari 21 (Kamis, 29 Okt 2026)**:
  - Penerapan siklus hidup status khusus **`BRAND_FINISHED`**:
    - Sepatu yang lolos QC akhir dialihkan ke status `BRAND_FINISHED` dan langsung dialokasikan ke kardus master B2B (terisolasi dari rak pickup kasir CS).
    - Progres batch dihitung otomatis: ketika seluruh pasang sepatu mencapai status `BRAND_FINISHED`, status batch induk berubah menjadi `FINISHED`.
- **Hari 22 (Jumat, 30 Okt 2026)**:
  - Pembuatan fitur **Surat Jalan Pengembalian Barang B2B (Dispatch Outbound)** untuk penyerahan fisik kembali ke pabrik / kurir rekanan brand.
  - End-to-End User Acceptance Testing (UAT) bersama tim operasional bengkel.
- **Hari 23 (Sabtu, 31 Okt 2026)**:
  - Final Audit, Performance Query Optimization, kompilasi laporan akhir bulan Oktober 2026, dan Sign-Off.

---

<div class="page-break"></div>

## 🔬 3. SPESIFIKASI TEKNIS RENCANA MODUL 1: SPK DIVISI R&D

Sesuai dokumen arsitektur master `docs/ALUR_DAN_ARSITEKTUR_SPK_RND.md`, modul SPK R&D dirancang dengan spesifikasi teknis berikut:

### 3.1 Skema Database Non-Destruktif (`work_order_rnd_progress`)
Sistem tidak mengubah atau menambah kolom baru pada tabel inti `work_orders`, melainkan membuat tabel ekstensi terisolasi:

```sql
CREATE TABLE `work_order_rnd_progress` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `work_order_id` BIGINT UNSIGNED NOT NULL,
  `sample_code` VARCHAR(50) NOT NULL UNIQUE,
  `rnd_category` ENUM('PROTOTYPE_NEW', 'MATERIAL_TRIAL', 'SOLE_BONDING_TEST', 'WEAR_TEST_SAMPLE') NOT NULL,
  `upper_material_spec` TEXT NULL,
  `sole_material_spec` TEXT NULL,
  `target_weight_gram` INT NULL,
  `actual_weight_gram` INT NULL,
  `flexibility_score` TINYINT NULL,
  `bonding_strength_mpa` DECIMAL(4,2) NULL,
  `qc_eval_status` ENUM('IN_PROGRESS', 'APPROVED', 'REVISE', 'REJECTED') DEFAULT 'IN_PROGRESS',
  `eval_notes` TEXT NULL,
  `evaluator_id` BIGINT UNSIGNED NULL,
  `evaluated_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `fk_rnd_work_order` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3.2 Alur 5 Stasiun Kerja Fisik R&D (Terisolasi):
$$\text{Input Draft R\&D} \longrightarrow \text{Stasiun 1: Pattern/Pola} \longrightarrow \text{Stasiun 2: Cutting \& Upper} \longrightarrow \text{Stasiun 3: Soling \& Bonding} \longrightarrow \text{Stasiun 4: Finishing} \longrightarrow \text{Stasiun 5: Uji Laboratorium \& Evaluasi QC}$$

### 3.3 Penomoran SPK R&D:
Format penomoran: **`RND-YYMM-DD-XXXX`** (Contoh: `RND-2610-05-0001`).  
Tersedia filter isolasi ketat: antrean order ritel CS menggunakan `where('spk_number', 'not like', 'RND-%')`.

---

<div class="page-break"></div>

## 🏢 4. SPESIFIKASI TEKNIS RENCANA MODUL 2: MANAJEMEN MITRA BRAND B2B

Sesuai dokumen arsitektur master `docs/ALUR_DAN_ARSITEKTUR_MANAJEMEN_BRAND.md`, modul Manajemen Brand dirancang dengan arsitektur berjenjang (*Two-Tier B2B Architecture*):

### 4.1 Skema Database Master Mitra & Batch Order
```sql
-- 1. Tabel Master Rekanan Brand
CREATE TABLE `brand_partners` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `brand_code` VARCHAR(20) NOT NULL UNIQUE,
  `brand_name` VARCHAR(100) NOT NULL,
  `pic_name` VARCHAR(100) NOT NULL,
  `pic_phone` VARCHAR(30) NOT NULL,
  `pic_email` VARCHAR(100) NULL,
  `factory_address` TEXT NULL,
  `logo_path` VARCHAR(255) NULL,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabel Batch Order Masuk Brand
CREATE TABLE `brand_orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `batch_number` VARCHAR(50) NOT NULL UNIQUE,
  `brand_partner_id` BIGINT UNSIGNED NOT NULL,
  `po_number` VARCHAR(100) NULL,
  `project_title` VARCHAR(150) NOT NULL,
  `total_quantity` INT UNSIGNED NOT NULL DEFAULT 0,
  `completed_quantity` INT UNSIGNED NOT NULL DEFAULT 0,
  `entry_date` DATE NOT NULL,
  `target_completion_date` DATE NOT NULL,
  `status` ENUM('DRAFT', 'IN_PROGRESS', 'FINISHED', 'DISPATCHED') DEFAULT 'DRAFT',
  `notes` TEXT NULL,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `fk_brand_orders_partner` FOREIGN KEY (`brand_partner_id`) REFERENCES `brand_partners` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Penambahan FK pada work_orders
ALTER TABLE `work_orders` 
ADD COLUMN `brand_order_id` BIGINT UNSIGNED NULL AFTER `customer_id`,
ADD CONSTRAINT `fk_work_orders_brand` FOREIGN KEY (`brand_order_id`) REFERENCES `brand_orders` (`id`) ON DELETE CASCADE;
```

### 4.2 Formula Penomoran Batch & SPK Anak
- **SPK Induk Batch:** `BR-YYMM-DD-XXXX` (Contoh: `BR-2610-20-0001`).
- **SPK Anak Satuan Sepatu:** `BR-YYMM-DD-XXXX-01`, `-02`, s/d `-NN` (Contoh: `BR-2610-20-0001-25`).

### 4.3 Desain Cetak Hybrid
1. **SPK Induk A4 (Amber Gold `#d97706`)**: Menampilkan ringkasan PO, identitas pabrik, total kuantitas, sebaran matriks ukuran, dan barcode induk.
2. **Grid Label Stiker Barcode Satuan**: Lembar stiker perekat ukuran kecil berisi nomor seri anak dan QR/Barcode untuk penempelan langsung pada fisik sepatu selama bergerak di stasiun kerja bengkel.

---

<div class="page-break"></div>

## 🛡️ 5. MATRIKS RISIKO TEKNIS & RENCANA MITIGASI

Untuk menjamin kelancaran implementasi berstandar Big 4, seluruh potensi risiko teknis telah diidentifikasi beserta langkah mitigasinya:

| ID Risiko | Potensi Risiko & Dampak | Tingkat Risiko | Strategi & Tindakan Mitigasi Teknis |
| :--- | :--- | :---: | :--- |
| **RSK-01** | **Kebocoran Data ke Antrean CS**: SPK R&D atau Batch Brand muncul di halaman antrean CS ritel kasir. | **TINGGI** | Penerapan Global Scope kueri `whereNull('brand_order_id')` dan pengecualian prefix `RND-%` pada seluruh controller kasir CS ritel. |
| **RSK-02** | **Kinerja Database Lambat (N+1 Query)** saat membuat ratusan SPK anak brand secara bersamaan. | **SEDANG** | Menggunakan transaksi database (`DB::transaction`) dan `insert()` batch chunking (per 50 baris) dengan eager-loading relasi. |
| **RSK-03** | **Human Error Pengambilan Sepatu**: Sepatu milik pabrik/brand terambil oleh customer ritel di toko. | **TINGGI** | Status akhir dialihkan ke `BRAND_FINISHED` (bukan `SELESAI` ritel), ditempatkan pada palet/kardus master terisolasi, dan tombol "Ambil di Toko" otomatis di-disable. |
| **RSK-04** | **Duplikasi Nomor SPK saat Lonjakan Order**: Terjadi duplikasi nomor seri SPK R&D atau Brand pada detik yang sama. | **SEDANG** | Menerapkan `DB::raw("SELECT ... FOR UPDATE")` pada nomor urut harian untuk mencegah race condition. |
| **RSK-05** | **Gagal Impor Format Excel**: File template spreadsheet dari brand rusak atau format kolom tidak sesuai. | **RENDAH** | Menyiapkan template unduhan baku (*Download Official Template .xlsx*) dan validasi baris ketat sebelum komit ke database. |

---

<div class="page-break"></div>

## 🧪 6. QUALITY ASSURANCE (QA) & SKENARIO UJI VALIDASI

Setiap deliverable wajib melewati 4 tingkatan pengujian ketat sebelum dirilis ke lingkungan produksi:

```
[Level 1: Unit Testing] ➔ [Level 2: Feature Testing] ➔ [Level 3: Livewire Reactive UI] ➔ [Level 4: End-to-End UAT]
```

### Skenario Uji Kunci:
1. **Test Suite RND-01: Pembuatan & Penomoran SPK R&D**:
   - Memastikan nomor SPK berformat `RND-YYMM-DD-XXXX` berurutan tanpa jeda.
   - Memastikan record `work_order_rnd_progress` terbuat otomatis dan sinkron.
2. **Test Suite RND-02: Stasiun & Evaluasi Laboratorium**:
   - Memastikan teknisi dapat menginput parameter teknis (bobot aktual, kekuatan daya rekat sol).
   - Memverifikasi status evaluasi `APPROVED` mengubah status akhir SPK menjadi tuntas.
3. **Test Suite BRD-01: Smart Size Matrix & Child Generator**:
   - Menguji input 100 pasang sepatu dengan sebaran ukuran (size 38: 20 pasang, size 40: 50 pasang, size 42: 30 pasang).
   - Memverifikasi tepat 100 baris `work_orders` tergenerate dengan nomor `-01` s/d `-100` dan ukuran yang tepat.
4. **Test Suite BRD-02: Isolasi Kardus Master & Dispatch**:
   - Memverifikasi bahwa sepatu brand berstatus `BRAND_FINISHED` tidak dapat diproses oleh kasir toko ritel.
   - Memastikan Surat Jalan Pengembalian Barang B2B mencantumkan seluruh nomor seri sepatu batch terkait.

---

<div class="page-break"></div>

## ✍️ 7. LEMBAR PENGESAHAN & KOMITMEN EKSEKUSI

Dokumen Rencana Kerja Strategis periode **Oktober 2026** ini telah disusun berdasarkan kesepakatan desain arsitektural master, siap menjadi panduan kerja terpadu bagi seluruh tim teknis dan operasional Sistem Workshop.

<div class="signature-grid">
  <div class="signature-box">
    <strong>Disusun Oleh:</strong><br><br><br><br>
    <strong>AI Senior Full Stack Developer</strong><br>
    <span>Pair Programmer & Lead Engineer</span><br>
    <small>Tanggal: 30 September 2026</small>
  </div>
  <div class="signature-box">
    <strong>Diverifikasi Oleh:</strong><br><br><br><br>
    <strong>Head of Production / Workshop</strong><br>
    <span>Koordinator Operasional Bengkel</span><br>
    <small>Tanggal: 30 September 2026</small>
  </div>
  <div class="signature-box">
    <strong>Disetujui Oleh:</strong><br><br><br><br>
    <strong>Management & Executive Board</strong><br>
    <span>Director / VP of Technology</span><br>
    <small>Tanggal: 30 September 2026</small>
  </div>
</div>

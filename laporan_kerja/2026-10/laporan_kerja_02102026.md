# 📋 Laporan Kerja Harian — Jumat, 2 Oktober 2026

**Pengembang:** AI Senior Full Stack Developer & Pair Programmer (Big 4 Standard)  
**Tanggal:** Jumat, 2 Oktober 2026  
**Branch Aktif:** `main`  
**Status:** 🟢 *Siap & Aktif — Standby Pengembangan & Penyempurnaan Sistem Workshop*  

---

## 🎯 Fokus & Target Pekerjaan Hari Ini

1. **Inisialisasi & Kesiapan Kerja (Jumat, 2 Oktober 2026)**:
   - Menyiapkan lembar kerja harian resmi `laporan_kerja/2026-10/laporan_kerja_02102026.md`.
   - Melakukan verifikasi integritas status git repository lokal serta sinkronisasi remote GitHub.
   - Melanjutkan agenda pengerjaan **Sprint 1 (Minggu 1: 01 – 09 Oktober 2026)** sesuai dokumen [rencana_kerja_oktober_2026.md](file:///c:/laragon/www/SistemWorkshop/laporan_kerja/2026-10/rencana_kerja_oktober_2026.md).

2. **Eksekusi & Penyempurnaan Modul Sistem Berdasarkan Arahan Pengguna**:
   - Menjalankan setiap instruksi dan kebutuhan pengujian, penyempurnaan fitur, maupun penambahan logika bisnis baru dengan metodologi terarah (`/grill-me`, `/plan`), Clean Code, dan standar Big 4.
   - Standby penuh menerima arahan instruksi spesifik, feedback pengujian staging, atau diskusi teknis dari pengguna.

3. **Dokumentasi Berkelanjutan (Big 4 Standard)**:
   - Mencatat seluruh riwayat pengerjaan, investigasi bug, analisis arsitektur, dan perubahan kode secara berkala, terstruktur rapi, dan konsisten dengan format penomoran angka standar.

---

### 1. Inisialisasi & Status Environment Kerja
- **Status Repository Git**:
  - Branch: `main` (Sinkron 100% dengan `origin/main` pada GitHub).
  - Commit Terakhir: [`ce83af1`](https://github.com/SidikWaluyaa/DivisiWorkshop/commit/ce83af147c5539e490bc2529965daba6ebc23305) (`feat(rnd): implement end-to-end SPK R&D workflow, dedicated workshop station, mobile upload with WebP compression, and living report`).
  - Kondisi working tree: Bersih (*clean*).
- **Status Runtime & Server Lokal**:
  - Web Server & Database: Laragon (PHP 8.2+, MySQL / MariaDB) berjalan aktif.
  - Asset Bundler: `npm run dev` aktif berjalan.
- **Status Modul Utama Terkini**:
  - Modul SPK R&D: Fondasi database, antarmuka 3-Tab & Collapsible Table di Workshop PWA, form mobile upload ber-kompresi WebP, Living Report publik, dan penyelarasan alur sub-stasiun QC Jahit & Treatment telah selesai dan siap diuji coba.
  - Modul Reguler (CS, Penerimaan, Produksi, QC, Gudang, Finance): Beroperasi normal dan terisolasi dari SPK R&D.

---

### 2. Sinkronisasi Catatan QC Gudang (Opsional) ke Print SPK & Detail Order Admin (Ref: Issue User #10)
- **Konteks Masalah**:
  - Saat staf gudang mengisi input **"Catatan QC Gudang (Opsional)"** di halaman Penerimaan / QC Before (`/reception/{id}`), catatan tersebut tidak tampil di kartu **"Catatan Gudang (SPK)"** pada halaman Detail Order Admin (`/admin/orders/{id}`) dan tercetak sebagai `- BELUM ADA CATATAN -` pada lembar fisik **Print SPK**.
- **Akar Masalah (Root Cause Analysis)**:
  1. *Perbedaan Kolom Database*: Form penerimaan menyimpan data ke kolom `work_orders.warehouse_qc_notes`, sedangkan lembar cetak SPK (`print-spk-premium`, `print-bulk`, `pdf-spk-content`, `print-spk-a4`, `garansi/print-spk`) dan kartu Admin membaca dari `work_orders.technician_notes`.
  2. *Reset Null*: Pada [ReceptionService.php](file:///c:/laragon/www/SistemWorkshop/app/Services/ReceptionService.php#L306), penanganan penyimpanan mengeksekusi `'technician_notes' => $data['technician_notes'] ?? null;`. Karena form penerimaan hanya mengirim field `warehouse_qc_notes`, kolom `technician_notes` selalu di-reset menjadi `null` sehingga data catatan teknis/gudang terhapus atau tidak tersinkron.
  3. Terbukti pada data Order ID 53 (`N-2609-08-0068-CS`), nilai `warehouse_qc_notes` sudah tersimpan `"Sidik Waluya"`, namun `technician_notes` bernilai `null`.
- **Langkah Solusi & Implementasi**:
  1. **ReceptionService (`processReceptionQC`)**:
     - Memperbaiki kalkulasi catatan agar jika `technician_notes` tidak dikirim dalam form, nilainya otomatis mengambil dari `warehouse_qc_notes` (atau mempertahankan nilai `technician_notes` yang sudah ada jika tidak ada input baru).
     - Menjamin kedua kolom `warehouse_qc_notes` dan `technician_notes` terisi sinkron secara otomatis.
  2. **OrderController (`updateTechnicianNotes`) & ReceptionController (`quickSaveNotes`)**:
     - Menyelaraskan penyimpanan kedua kolom secara 2 arah saat Admin atau Gudang mengedit/quick-save catatan.
  3. **Blade Template Cetak SPK & View Order**:
     - Menambahkan fallback `$order->technician_notes ?: $order->warehouse_qc_notes` pada:
       - `resources/views/assessment/print-spk-premium.blade.php`
       - `resources/views/assessment/print-bulk.blade.php`
       - `resources/views/reception/pdf-spk-content.blade.php`
       - `resources/views/assessment/print-spk-a4.blade.php`
       - `resources/views/garansi/print-spk.blade.php`
       - `resources/views/admin/orders/show.blade.php`
       - `resources/views/reception/show.blade.php`
  4. **Data Backfill**:
     - Menjalankan migrasi/sinkronisasi instan terhadap 7 data pesanan lama (termasuk Order ID 53) di database yang memiliki `warehouse_qc_notes` namun `technician_notes`-nya kosong.
  5. **Verifikasi**:
     - Order ID 53 diverifikasi via tinker: `warehouse_qc_notes` = `"Sidik Waluya"`, `technician_notes` = `"Sidik Waluya"`.
     - Cache view berhasil di-refresh (`php artisan view:clear`).

---

### 3. Standarisasi Sistem Pengalihan SPK Biasa ke SPK R&D (Ref: Sesi /grill-me & /plan)
- **Konteks & Kesepakatan Desain**:
  - Menyepakati aturan arsitektur peralihan prioritas SPK Biasa (Reguler / Prioritas) ke SPK R&D (Research & Development) melalui segmented control 3-pill di halaman Detail Order (`/admin/orders/{id}`).
  - Menetapkan 5 pilar aturan bisnis:
    1. *Nomor SPK*: Tetap sama (tidak berubah prefix ke `RD-`) untuk menjaga integritas invoice, barcode fisik, dan pelacakan customer.
    2. *Otorisasi*: Role-based menggunakan Laravel Policy (`WorkOrderPolicy::updatePriority`), memberikan wewenang kepada role `admin`, `owner`, `spv`, serta whitelist akun khusus.
    3. *Token & Fitur R&D*: Menginisialisasi token upload WebP smartphone teknisi (`rnd_upload_token`) dan link Living Report publik secara otomatis.
    4. *Alur Stasiun*: Fleksibel tanpa me-reset status alur; antrean pengerjaan otomatis beralih ke dashboard stasiun khusus R&D (`/workshop/rnd`).
    5. *Tagihan & Finansial*: Nilai tagihan dan item jasa tetap sesuai kesepakatan awal tanpa perubahan otomatis.
- **Implementasi**:
  1. Menambahkan method `updatePriority` pada [WorkOrderPolicy.php](file:///c:/laragon/www/SistemWorkshop/app/Policies/WorkOrderPolicy.php).
  2. Memperbarui [OrderController.php](file:///c:/laragon/www/SistemWorkshop/app/Http/Controllers/Admin/OrderController.php) method `updatePriority` untuk mengadopsi policy `$this->authorize('updatePriority', WorkOrder::class)` dan auto-generate token R&D.
  3. Memperbarui [resources/views/admin/orders/show.blade.php](file:///c:/laragon/www/SistemWorkshop/resources/views/admin/orders/show.blade.php) agar pengecekan frontend menggunakan `$canEditPriority = auth()->user()?->can('updatePriority', WorkOrder::class)`.
- **Verifikasi**:
  - Policy terverifikasi via tinker untuk role `admin` bernilai `true`.
  - Cache view dibersihkan dengan `php artisan view:clear`.

---

### 4. Isolasi SPK R&D dari Seluruh Stasiun Pengerjaan Fisik Reguler (Ref: Issue User #12)
- **Konteks Masalah**:
  - Pengguna menemukan SPK R&D (`S-2609-20-4010-PND`) masih muncul di antrean stasiun pengerjaan reguler (`/preparation`), padahal seharusnya pengerjaan fisik R&D dipusatkan secara eksklusif di dashboard Stasiun Khusus R&D (`/workshop/rnd`).
- **Akar Masalah (Root Cause Analysis)**:
  - Sidebar bengkel (`sidebar.blade.php`) dan controller legacy sebenarnya sudah mengecualikan R&D (`withoutRnd`), namun komponen **Livewire 3** pada stasiun pengerjaan fisik reguler:
    - [PrepIndex.php](file:///c:/laragon/www/SistemWorkshop/app/Livewire/Preparation/PrepIndex.php) (`/preparation`)
    - [Index.php](file:///c:/laragon/www/SistemWorkshop/app/Livewire/Sortir/Index.php) (`/sortir`)
    - [StationIndex.php](file:///c:/laragon/www/SistemWorkshop/app/Livewire/Production/StationIndex.php) (`/production`)
    - [QcIndex.php](file:///c:/laragon/www/SistemWorkshop/app/Livewire/Qc/QcIndex.php) (`/qc`)
    - Serta widget heatmap & antrean mendesak (`WorkloadHeatmap.php`, `UrgentActionGrid.php`)
    masih menggunakan query mentah `WorkOrder::where('status', ...)` tanpa menyertakan scope `withoutRnd()`.
- **Langkah Solusi & Implementasi**:
  1. Menambahkan `->withoutRnd()` pada seluruh query antrean tabel, penghitungan tab badge (`counts()`), serta auto-assign teknisi di keempat komponen Livewire stasiun reguler.
  2. Menerapkan `withoutRnd()` pada widget statistik beban kerja bengkel (`WorkloadHeatmap` dan `UrgentActionGrid`).
- **Verifikasi**:
  - Pengecekan tinker:
    - `WorkOrder::withoutRnd()->where('id', 117)->exists()` menghasilkan `false` (sukses tersembunyi dari stasiun reguler).
    - `WorkOrder::onlyRnd()->where('id', 117)->exists()` menghasilkan `true` (eksklusif di stasiun R&D).
    - Hitungan Preparation reguler sinkron sempurna dengan badge sidebar (7 SPK).
  - Cache view dan application cache dibersihkan (`php artisan view:clear; php artisan cache:clear`).

---

### 5. Penyesuaian Field Email Menjadi Opsional pada Form Tambah Lead Baru CS (/cs/dashboard)
- **Konteks Kebutuhan**:
  - Customer Service (CS) seringkali menerima kontak lead awal melalui pesan instan WhatsApp atau walk-in tanpa customer langsung memberikan alamat email.
  - Sebelumnya, modal "LEAD BARU - TAMBAH DATA CUSTOMER" di dashboard CS (`/cs/dashboard`) mewajibkan field `customer_email` (`required` di HTML form serta aturan validasi `required|email|max:255` di backend `CsLeadController::store`).
- **Langkah Solusi & Implementasi**:
  1. **Frontend Modal (`create-modal.blade.php`)**:
     - Menghapus atribut HTML5 `required` pada input `customer_email`.
     - Mengubah label indikator dari `Email <span class="text-red-400">*</span>` menjadi `Email <span class="text-[10px] text-gray-400 font-medium normal-case">(opsional)</span>`.
     - Mengubah placeholder menjadi `nama@email.com (opsional)`.
     - Tetap mempertahankan shortcut tombol domain (`@gmail.com`, `@yahoo.com`, `@outlook.com`) untuk kenyamanan CS jika customer memang menyertakan email.
  2. **Backend Controller (`CsLeadController.php`)**:
     - Mengubah aturan validasi method `store()` dari `'customer_email' => 'required|email|max:255'` menjadi `'customer_email' => 'nullable|email|max:255'`.
     - Logika service `CsLeadService` dan downstream database (`cs_leads`, `customers`, SPK) sudah mendukung `null` secara default (`$data['customer_email'] ?? null`).
- **Verifikasi**:
  - Syntax check `php -l app/Http/Controllers/CsLeadController.php` berhasil (0 syntax errors).
  - Validasi controller kini seragam dengan method `update()` yang sudah berstatus `nullable`.

---

### 6. Penyusunan Alur & Arsitektur Sistem Label & Tier Membership Customer (Ref: Sesi /grill-me & /plan)
- **Konteks & Landasan Empiris**:
  - Berdasarkan audit transaksi 1 Juni s/d 30 September 2026 pada dokumen eksekutif `docs/proposal_program_membership_loyalty_shoe_workshop.md`, sebanyak 93,84% pelanggan (2.773 orang) hanya pernah 1x order, sementara 237 member elite (Gold + Platinum VIP) menyumbang 40,9% omset (Rp 748,1 Juta).
  - Pengguna mengajukan query SQL analitik untuk segmentasi pelanggan dan meminta blueprint arsitektur lengkap dalam format Markdown tanpa eksekusi kode terlebih dahulu.
- **Hasil Blueprint & Dokumen Desain**:
  1. Telah disusun dokumen blueprint arsitektur resmi di [docs/ALUR_DAN_ARSITEKTUR_SISTEM_LABEL_MEMBERSHIP_CUSTOMER.md](file:///c:/laragon/www/SistemWorkshop/docs/ALUR_DAN_ARSITEKTUR_SISTEM_LABEL_MEMBERSHIP_CUSTOMER.md) serta berkas PDF resmi di [docs/ALUR_DAN_ARSITEKTUR_SISTEM_LABEL_MEMBERSHIP_CUSTOMER.pdf](file:///c:/laragon/www/SistemWorkshop/docs/ALUR_DAN_ARSITEKTUR_SISTEM_LABEL_MEMBERSHIP_CUSTOMER.pdf).
  2. Menyepakati 3 level tier:
     - **Reguler Member** (< Rp 1.500.000): 0% diskon awal, garansi 30 hari.
     - **Gold Member** (Rp 1.500.000 s/d Rp 3.499.999): Diskon 15% Lem Jahit, stempel loyalty aktif.
     - **Platinum VIP** ($\ge$ Rp 3.500.000): Diskon 30% Lem Jahit, voucher Deep Clean Rp 60k.
  3. Menyepakati kalkulasi **100% Real-Time** melalui Eloquent Model Event `Invoice` (`saved`, `deleted`, `restored`) tanpa command manual harian.
  4. **Basis Perhitungan Finansial**: Akumulasi total belanja secara presisi menggunakan **`SUM(paid_amount)`** (nominal uang riil yang sudah dibayarkan customer), bukan tagihan kotor `total_amount`. Pada transaksi DP/Cicil, hanya nominal DP yang telah masuk yang dihitung; saldo otomatis bertambah penuh saat invoice dilunasi.
  5. **Kebijakan Masa Berlaku & Downgrade**: Status tier berlaku penuh selama **1 tahun (365 hari)** sejak tanggal kualifikasi / pembaruan (`tier_updated_at`), tanpa penurunan mendadak di tengah tahun berjalan demi perlindungan loyalitas pelanggan.
  6. Menyepakati mekanisme **Auto-Backfill saat Deploy ke Production** via migrasi database (`php artisan migrate`), sehingga 2.955+ pelanggan di database live langsung terisi nilai `total_spend` dan `membership_tier` seketika.
  7. Menetapkan 2 titik sentuh visual utama: **Detail Order Admin (`/admin/orders/{id}`)** dan **Master Pelanggan Admin (`/admin/customers`)** lengkap dengan segmented tab filter (`Semua`, `Platinum VIP`, `Gold Member`, `Reguler Member`).

### 7. Pembuatan Endpoint API Sinkronisasi SPK R&D ke Google Sheets (/public/api/sync_spk_rnd.php)
- **Konteks Kebutuhan**:
  - Tim operasional & manajemen membutuhkan penarikan data SPK khusus Research & Development (R&D) secara otomatis ke Google Sheets untuk pelaporan, monitoring lab riset, dan analisis perkembangan berkala.
  - Data yang wajib disinkronkan:
    1. Data SPK dari tabel `work_orders`: `customer_name`, `customer_phone`, `spk_number`, `status`.
    2. Data Jurnal Progres dari tabel `work_order_rnd_progress`: `stage_title`, `notes`, `report_url`.
- **Langkah Solusi & Implementasi**:
  1. **Pembuatan Endpoint Mandiri (`public/api/sync_spk_rnd.php`)**:
     - Mengikuti konvensi endpoint API Google Sheets yang sudah terstandarisasi di folder `/public/api/` (seperti `sync_taken_orders.php`, `sync_customers.php`, dan `sync_warehouse_qc.php`).
     - Membaca kredensial database dan token autentikasi secara dinamis dari file `.env` (`SYNC_API_TOKEN`, `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE`) dengan fallback yang aman.
     - Proteksi keamanan HTTP 401 Unauthorized jika parameter `?token=` tidak disertakan atau tidak cocok.
     - Header standar format JSON (`Content-Type: application/json; charset=utf-8`) dan CORS (`Access-Control-Allow-Origin: *`) agar dapat diakses langsung oleh Google Apps Script (`UrlFetchApp.fetch()`) maupun formula Google Sheets.
  2. **Query Relasi Data & Optimalisasi Tabular**:
     - Menggunakan relasi `LEFT JOIN` dari `work_orders` ke `work_order_rnd_progress` dengan filter presisi SPK R&D: `(wo.priority = 'R&D' OR wo.spk_number LIKE 'RD-%') AND wo.deleted_at IS NULL`.
     - Menyediakan mode output default berbentuk baris datar (**flat table rows**) yang langsung cocok di-mapping ke baris kolom spreadsheet, serta mode terkelompok (**`format=grouped`**) jika dibutuhkan data nested per SPK.
     - Menyediakan parameter filter opsional: `status` (filter status order), `start_date` & `end_date` (rentang tanggal SPK), dan `limit`.
- **Verifikasi & Hasil Pengujian**:
  - Akses tanpa token / token salah berhasil di-reject dengan HTTP 401 (`status: error, message: Unauthorized`).
  - Akses dengan token valid berhasil mengekspor seluruh data SPK R&D beserta riwayat jurnal progresnya, link Living Report publik (`report_url`), nama pelanggan, kontak WhatsApp, dan status pengerjaan.

---

### 8. Rincian Berkas yang Dibuat / Diubah Hari Ini

| No. | Nama Berkas | Aksi | Deskripsi |
| :---: | :--- | :---: | :--- |
| 1 | `laporan_kerja/2026-10/laporan_kerja_02102026.md` | `Ubah` | Dokumentasi berkala aktivitas harian Jumat, 2 Oktober 2026 dengan penomoran angka standar. |
| 2 | `public/api/sync_spk_rnd.php` | `Baru` | Endpoint API ekspor data SPK R&D & jurnal progres untuk sinkronisasi otomatis ke Google Sheets. |
| 3 | `docs/ALUR_DAN_ARSITEKTUR_SISTEM_LABEL_MEMBERSHIP_CUSTOMER.md` | `Baru` | Dokumen arsitektur lengkap sistem label & tier membership customer (RFM monetary engine) siap PDF. |
| 4 | `docs/ALUR_DAN_ARSITEKTUR_SISTEM_LABEL_MEMBERSHIP_CUSTOMER.pdf` | `Baru` | Berkas cetak fisik PDF resmi dokumen arsitektur label & tier membership (1,29 MB). |
| 5 | `docs/images/flowchart_membership_customer.svg` | `Baru` | Diagram alur SVG sistem kalkulasi real-time membership customer berbasis `paid_amount`. |
| 6 | `docs/images/erd_membership_customer.svg` | `Baru` | Entity Relationship Diagram (ERD) SVG skema database membership highlight `paid_amount`. |
| 7 | `docs/images/wireframe_customer_membership_ui.svg` | `Baru` | Wireframe SVG desain UI Detail Order dan Master Customer. |
| 8 | `app/Http/Controllers/CsLeadController.php` | `Ubah` | Mengubah validasi `customer_email` dari required menjadi nullable pada method `store`. |
| 9 | `resources/views/cs/leads/partials/create-modal.blade.php` | `Ubah` | Menghapus atribut required dan asterisk pada input email modal tambah lead baru CS. |
| 10 | `app/Services/ReceptionService.php` | `Ubah` | Sinkronisasi `warehouse_qc_notes` ke `technician_notes` saat proses penerimaan QC gudang. |
| 11 | `app/Http/Controllers/Admin/OrderController.php` | `Ubah` | Sinkronisasi catatan 2 arah & implementasi policy role-based pada `updatePriority`. |
| 12 | `app/Http/Controllers/ReceptionController.php` | `Ubah` | Sinkronisasi 2 arah `technician_notes` dan `warehouse_qc_notes` saat quick-save gudang. |
| 13 | `app/Policies/WorkOrderPolicy.php` | `Ubah` | Penambahan method otorisasi `updatePriority` untuk role Admin, Owner, SPV, dan akun khusus. |
| 14 | `app/Livewire/Preparation/PrepIndex.php` | `Ubah` | Penerapan `withoutRnd()` pada query antrean, counts, dan auto-assign stasiun Preparation. |
| 15 | `app/Livewire/Sortir/Index.php` | `Ubah` | Penerapan `withoutRnd()` pada query antrean, filter tab, dan metrik stasiun Sortir. |
| 16 | `app/Livewire/Production/StationIndex.php` | `Ubah` | Penerapan `withoutRnd()` pada query antrean pengerjaan & review stasiun Produksi (Reparasi). |
| 17 | `app/Livewire/Qc/QcIndex.php` | `Ubah` | Penerapan `withoutRnd()` pada query antrean QC & review stasiun Quality Control. |
| 18 | `app/Livewire/Workshop/Widgets/WorkloadHeatmap.php` | `Ubah` | Penerapan `withoutRnd()` pada hitungan beban kerja per stasiun. |
| 19 | `app/Livewire/Workshop/Widgets/UrgentActionGrid.php` | `Ubah` | Penerapan `withoutRnd()` pada antrean deadline pengerjaan bengkel reguler. |
| 20 | `resources/views/assessment/print-spk-premium.blade.php` | `Ubah` | Fallback catatan gudang `$order->technician_notes ?: $order->warehouse_qc_notes`. |
| 21 | `resources/views/assessment/print-bulk.blade.php` | `Ubah` | Fallback catatan gudang `$order->technician_notes ?: $order->warehouse_qc_notes`. |
| 22 | `resources/views/reception/pdf-spk-content.blade.php` | `Ubah` | Fallback catatan gudang `$order->technician_notes ?: $order->warehouse_qc_notes`. |
| 23 | `resources/views/assessment/print-spk-a4.blade.php` | `Ubah` | Fallback catatan gudang `$order->technician_notes ?: $order->warehouse_qc_notes`. |
| 24 | `resources/views/garansi/print-spk.blade.php` | `Ubah` | Fallback catatan gudang `$order->technician_notes ?: $order->warehouse_qc_notes`. |
| 25 | `resources/views/admin/orders/show.blade.php` | `Ubah` | Fallback catatan gudang & adopsi policy `updatePriority` pada segmented pill switcher. |
| 26 | `resources/views/reception/show.blade.php` | `Ubah` | Fallback catatan gudang pada input QC dan textarea quick-save penerimaan. |
| 27 | `resources/views/workshop/rnd/index.blade.php` | `Ubah` | Fallback catatan gudang pada tampilan collapsible card & target riset Workshop R&D. |

---

### 9. Kesimpulan & Rencana Selanjutnya
1. Catatan QC Gudang (Opsional) kini 100% tersimpan dan tersinkronisasi ke seluruh cetakan SPK dan kartu admin tanpa ada risiko terhapus atau kosong.
2. Sistem peralihan SPK Biasa ke R&D telah distandarisasi secara aman menggunakan Policy Laravel berbasis role (Admin, Owner, SPV) dengan nomor SPK yang tetap konsisten dan fitur R&D (token WebP & Living Report) aktif otomatis.
3. SPK R&D telah terisolasi 100% dari stasiun fisik reguler (Preparation, Sortir, Production, QC) dan terpusat eksklusif di dashboard Stasiun R&D (`/workshop/rnd`).
4. Form tambah lead baru CS Hub (`/cs/dashboard`) kini fleksibel dengan email bersifat opsional, mempercepat input data lead harian CS.
5. Cetak biru Alur & Arsitektur Sistem Label Membership Customer telah selesai disusun secara mendalam berbasis `paid_amount`, lengkap dengan berkas PDF resmi, ERD, Flowchart, dan Wireframe, siap dieksekusi kapan pun disetujui.
6. API sinkronisasi SPK R&D ke Google Sheets telah selesai dibuat di `/public/api/sync_spk_rnd.php`, mengembalikan data lengkap pelanggan, status pengerjaan, tahapan jurnal R&D, catatan riset, dan tautan laporan interaktif (`report_url`).

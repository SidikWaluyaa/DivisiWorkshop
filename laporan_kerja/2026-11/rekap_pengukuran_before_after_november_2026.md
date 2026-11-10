# 📊 BUKU CATATAN PENGUKURAN KUERI & KECEPATAN DATABASE (BEFORE VS AFTER)
**Proyek:** Optimasi Kecepatan Database & Perbaikan N+1 Query di Seluruh Divisi  
**Penyusun:** Sidik Waluya — Full Stack Developer (Divisi Data Analyst & IT)  
**Periode Pencatatan:** 2 November 2026 – 30 November 2026  
**Metode Pengukuran:** Laravel Debugbar (v4.4), `DB::listen()` Automated Profiler, & `Model::preventLazyLoading()` Strict Mode

---

## 📌 1. MENU NAVIGASI UMUM & KOMPONEN GLOBAL (TUGAS 1 — TUNTAS ✅)
*Tanggal Pengukuran & Optimasi: 3 November 2026*

| No | Halaman / Fitur | Route / Endpoint | Kueri Before | Kueri After | Waktu Before | Waktu After | Duplikat Before | Duplikat After | Temuan Utama & Solusi Perbaikan | Status |
| :-: | :--- | :--- | :-: | :-: | :-: | :-: | :-: | :-: | :--- | :-: |
| 1 | **Executive Dashboard Utama** + Sidebar Global | `GET /dashboard` | **104** | **58** | **2.100,86 ms** | **112,07 ms** | **20 jenis** *(31x ulang)* | **0 duplikat nyata** | • Sidebar dirender 2x (desktop + mobile drawer) ➔ Dibungkus `once()` memoization.<br>• 8 status SPK dihitung terpisah ➔ Digabung 1x `groupBy('status')`.<br>• `Material::all()->sum()` muat seluruh baris ke RAM ➔ Diubah ke SQL `SUM(stock * price)`. | ✅ **Selesai (18,7x Lebih Cepat)** |
| 2 | **Internal Tracking (Kondisi Awal)** | `GET /internal-tracking` | **0** | **0** | **605,81 ms** | **49,76 ms** | **0** | **0** | • Optimasi inisialisasi komponen Livewire. | ✅ **Selesai** |
| 3 | **Internal Tracking (Pencarian SPK)** | `Livewire Search` | **13** | **12** | **130,62 ms** | **52,64 ms** | **1** | **0** | • Method `WorkOrder::hasServiceCategory()` sebelumnya menembak kueri `exists` baru ke tabel `work_order_services` ➔ Diperbaiki agar membaca koleksi `workOrderServices` yang sudah di-*eager load* di memori (0 kueri tambahan). | ✅ **Selesai (2,5x Lebih Cepat)** |

---

## 📌 2. DIVISI CUSTOMER SERVICE / CS (TUGAS 2 — TUNTAS ✅)
*Tanggal Pengukuran & Optimasi: 4 November 2026*

| No | Halaman / Fitur CS | Route / Endpoint | Kueri Before | Kueri After | DB Time Before | DB Time After | Page Time Before | Page Time After | Duplikat Before | Duplikat After | Temuan Audit & Solusi Perbaikan | Status |
| :-: | :--- | :--- | :-: | :-: | :-: | :-: | :-: | :-: | :-: | :-: | :--- | :-: |
| 1 | **CS Dashboard (Pipeline)** | `GET /cs/dashboard` | **45** | **26** | 33,09 ms | **18,39 ms** | 6.990,04 ms | **110,55 ms** | **5 jenis** *(12 ekstra)* | **0 duplikat nyata** *(hanya 4 paginator)* | • Konsolidasi 7 kueri metrik global `CsLead` & `calculateConversionRate` menjadi 1 kueri `selectRaw` di `CsLeadController`.<br>• Konsolidasi 4 badge sidebar CS di `AppServiceProvider` menjadi 1 kueri.<br>• Perbaikan `CsLead::getLatestQuotation()` agar membaca koleksi `quotations` yang sudah di-*eager load*. | ✅ **Selesai (63x Lebih Cepat, -42% Kueri)** |
| 2 | **Laporan Performa (CS Analytics)** | `GET /cs/analytics` | **77** | **38** | 28,57 ms | **19,73 ms** | 950,85 ms | **54,09 ms** | **11 jenis** *(24 ekstra)* | **0** | • Pre-agregasi `GROUP BY status`, `GROUP BY channel`, dan `GROUP BY cs_id` di luar `foreach ($csUsers as $user)` pada `CsDashboardController`.<br>• Konsolidasi 8 kueri *Response Time* menjadi 1 kueri `selectRaw`.<br>• Pre-fetch `WorkOrder` periode laporan dalam 1 kueri tunggal (menggantikan ~20 kueri per CS). | ✅ **Selesai (17,5x Lebih Cepat, -51% Kueri)** |
| 3 | **Leads Konsultasi** | `GET /cs/leads-konsultasi` | **22** | **18** | 8,42 ms | **9,51 ms** | 512,79 ms | **31,59 ms** | **1 jenis** *(3 ekstra)* | **0** | • Konsolidasi 4 kueri badge sidebar `cs_leads` menjadi 1 kueri.<br>• Menghapus *eager load* relasi `activities` yang tidak ditampilkan di tabel. | ✅ **Selesai (16,2x Lebih Cepat, 0 Duplikat)** |
| 4 | **Leads Follow-up** | `GET /cs/leads-follow-up` | **23** | **18** | 13,38 ms | **10,76 ms** | 211,29 ms | **33,91 ms** | **1 jenis** *(3 ekstra)* | **0** | • Konsolidasi 4 kueri badge sidebar `cs_leads` menjadi 1 kueri.<br>• Menghapus *eager load* relasi `activities` & `quotations` yang tidak dirender di tabel. | ✅ **Selesai (6,2x Lebih Cepat, 0 Duplikat)** |
| 5 | **Leads Closing** | `GET /cs/leads-closing` | **19** | **16** | 7,14 ms | **7,77 ms** | 183,46 ms | **27,86 ms** | **1 jenis** *(3 ekstra)* | **0** | • Konsolidasi 4 kueri badge sidebar `cs_leads` menjadi 1 kueri di `AppServiceProvider`. | ✅ **Selesai (6,5x Lebih Cepat, 0 Duplikat)** |
| 6 | **Follow Up Closing (Kendala Gudang)** | `GET /cs/followup-closing` | **10** *(Crash N+1)* | **6** | 11,71 ms | **6,25 ms** | 1.150,83 ms | **42,90 ms** | **1 jenis** | **0** | 🚨 **Perbaikan N+1 Violation:** Menambahkan `->with('reporter')` pada relasi `cxIssues` di `FollowUpClosing.php`.<br>• Mengambil kategori dari koleksi `$masterServices` di memori & memakai `$data->total()` untuk `activeCount`. | ✅ **Selesai (0 N+1, 26,8x Lebih Cepat)** |
| 7 | **Kiriman SPK Pending** | `GET /cs/pending-monitoring` | **10** | **8** | 6,75 ms | **4,73 ms** | 1.113,36 ms | **25,01 ms** | **1 jenis** *(1 ekstra)* | **0** | • Menggabungkan 3 kueri `count()` (`all`, `in_transit`, `waiting`) di `PendingSpkMonitoring::counts()` menjadi 1 kueri `selectRaw`. | ✅ **Selesai (44,5x Lebih Cepat, 0 Duplikat)** |
| 8 | **Data SPK CS** | `GET /cs/spk-data` | **27** | **23** | 24,47 ms | **21,73 ms** | 739,69 ms | **68,40 ms** | **2 jenis** *(3 ekstra)* | **0** | • Mengambil `$totalSpk` langsung dari `$spks->total()` hasil *paginator* di `CsSpkController` & konsolidasi badge CS. | ✅ **Selesai (10,8x Lebih Cepat, 0 Duplikat)** |
| 9 | **Greeting Chat** | `GET /cs/greeting` | **22** *(Error Null)* | **18** | 1,71 ms | **11,38 ms** | 480,35 ms | **43,41 ms** | **1 jenis** *(3 ekstra)* | **0** | ⚠️ **Perbaikan Bug View:** Menggunakan *null-safe fallback* `($greeting->first_contact_at ?? $greeting->created_at)?->format(...)` pada `admin/cs/greeting/index.blade.php` & konsolidasi badge CS. | ✅ **Selesai (0 Error, 11x Lebih Cepat)** |
| 10 | **Galeri After Photo** | `GET /cs/after-photos` | **3** | **3** | 10,72 ms | **2,75 ms** | 472,50 ms | **16,14 ms** | **0** | **0** | • Sudah optimal sejak awal (3 kueri, 0 duplikat). | ✅ **Selesai (Optimal)** |
| 11 | **Detail Lead / Ruang Kerja CS** | `GET /cs/leads/{id}` & `POST /livewire/update` | **28** *(Crash N+1 saat klik modal)* | **23** | 15,06 ms | **13,84 ms** | 3.092,56 ms | **49,33 ms** | **2 jenis** *(3 ekstra)* | **0 duplikat bisnis** | • 🚨 **Perbaikan N+1 Livewire Update:** Menambahkan `$this->lead->loadMissing(['cs', 'activities.user', 'quotations.quotationItems', 'spk.customer', 'spk.items'])` pada `LeadDetailManager::render()` agar saat klik **+ Buat Penawaran** (`/livewire/update`) relasi `activities.user` tidak mengalami *lazy loading*.<br>• Perbaikan `getLatestQuotation()` & `getAcceptedQuotation()` di `CsLead.php`. | ✅ **Selesai (0 N+1, 62,6x Lebih Cepat)** |

---

## 📌 3. DIVISI GUDANG (TUGAS 3 — TUNTAS ✅)
*Tanggal Pengukuran & Optimasi: 10 November 2026*

| No | Halaman / Fitur Gudang | Route / Endpoint | Kueri Before | Kueri After | DB Time Before | DB Time After | Page Time Before | Page Time After | Duplikat Before | Duplikat After | Temuan Audit & Solusi Perbaikan | Status |
| :-: | :--- | :--- | :-: | :-: | :-: | :-: | :-: | :-: | :-: | :-: | :--- | :-: |
| 1 | **Penerimaan Gudang** | `GET /reception` | **30** | **26** | 18,25 ms | **16,11 ms** | 157,70 ms | **104,21 ms** | **1 jenis** *(1 ekstra)* | **0** | • Konsolidasi kueri `count()` tab non-aktif (`received` & `processed`) menjadi 1 kueri `selectRaw`.<br>• Konsolidasi pembacaan `StorageRack` (`accessories` & `before`) menjadi 1 kueri.<br>• Memoization `once()` pada `Service::orderBy('name')->get()` di `ReceptionService` & `AppServiceProvider`. | ✅ **Selesai (-13% Kueri, 0 Duplikat)** |
| 2 | **Assessment Fisik** | `GET /assessment` | **23** | **17** | 10,21 ms | **7,77 ms** | 446,89 ms | **33,36 ms** | **2 jenis** *(3 ekstra)* | **0** | • Menggabungkan **5 kueri `count()` terpisah** (`all`, `lunas`, `dp`, `belum_bayar`, `none`) di `AssessmentController@index` menjadi **1 kueri `LEFT JOIN invoices` + `selectRaw`**.<br>• Konsolidasi badge sidebar global di `AppServiceProvider`. | ✅ **Selesai (13,4x Lebih Cepat, -26% Kueri)** |
| 3 | **Penerimaan Outbound (QC ➔ Gudang)** | `GET /gudang/outbound-receipt` | **4** | **2** | 1,32 ms | **0,89 ms** | 845,97 ms | **18,83 ms** | **1 jenis** *(2 ekstra)* | **0** | • Menggabungkan **3 kueri `count()` terpisah** (`pendingCount`, `receivedTodayCount`, `totalReceivedCount`) di `OutboundReceiptIndex::render()` menjadi **1 kueri `selectRaw`**. | ✅ **Selesai (-50% Kueri, 0 Duplikat)** |
| 4 | **Logistik Manifest** | `GET /manifest` | **22** | **18** | 8,76 ms | **7,31 ms** | 662,50 ms | **33,36 ms** | **3 jenis** *(3 ekstra)* | **0 duplikat bisnis** | • Menggabungkan **3 kueri `count()` terpisah** (`countSent`, `countReceived`, `countAll`) di `WorkshopManifestController@index` menjadi **1 kueri `selectRaw`**.<br>• Menggabungkan 2 kueri badge manifest di `AppServiceProvider` menjadi 1 kueri. | ✅ **Selesai (19,8x Lebih Cepat, -18% Kueri)** |
| 5 | **Finish (Sepatu Selesai)** | `GET /finish` | **28** | **26** | 20,28 ms | **18,46 ms** | 1.198,76 ms | **81,22 ms** | **0** | **0** | • Menggabungkan perhitungan badge `finish` di `AppServiceProvider` ke dalam 1 kueri agregasi `WorkOrder` utama. | ✅ **Selesai (14,7x Lebih Cepat, 0 Duplikat)** |
| 6 | **Pengiriman Ekspedisi** | `GET /shipping` | **19** | **17** | 11,33 ms | **8,93 ms** | 306,70 ms | **30,61 ms** | **0** | **0** | • Turun 2 kueri berkat konsolidasi global badge sidebar (`WorkOrder` & `WorkshopManifest`) di `AppServiceProvider`. | ✅ **Selesai (10x Lebih Cepat, 0 Duplikat)** |
| 7 | **Riwayat Pengambilan** | `GET /warehouse/pickup-history` | **15** *(Crash N+1)* | **7** | 6,95 ms | **3,05 ms** | 256,83 ms | **12,01 ms** | **3 jenis** *(5 ekstra)* | **0** | 🚨 **Perbaikan N+1 Violation & Hidden N+1 Query Builder:**<br>1. Menambahkan `'invoice'` & `'photos'` pada `->with(['spkCoverPhoto', 'photos', 'invoice', 'workOrderServices.service'])` di `PickupHistory.php`.<br>2. Memperbaiki `WorkOrder::getSpkCoverPhotoAttribute()` & `getSpkCoverPhotoUrlAttribute()` agar memeriksa `$this->relationLoaded('spkCoverPhoto')` terlebih dahulu.<br>3. Menggabungkan 4 kueri `count()` di `PickupHistory::getStatsProperty()` menjadi **1 kueri `selectRaw`**. | ✅ **Selesai (0 N+1, -53% Kueri, 21,3x Lebih Cepat)** |
| 8 | **Dashboard Gudang (Control Center)** | `GET /warehouse/dashboard` | **144** | **58** | 75,92 ms | **42,92 ms** | 1.882,86 ms | **102,27 ms** | **7 jenis** *(60 ekstra!)* | **0 duplikat bisnis** | 🔥 **Eliminasi 86 Kueri (-60%) & 54x N+1 Query:**<br>1. Mengganti **54x N+1 Query `work_order_logs`** di dalam `foreach` pada `getSortirSummary()`, `getProductionSummary()`, dan `getQcSummary()` dengan *1x batch pre-fetch* `MAX(created_at) GROUP BY work_order_id`.<br>2. Mengganti N+1 `work_order_services` count per manifest di `getManifestSummary()` dengan *1x batch pre-fetch*.<br>3. Menggabungkan 18 kueri `getHeroMetrics()` menjadi 4 kueri `selectRaw` terpadu.<br>4. Preload `StorageRack::active()` 1x untuk `storageStats`, `rackStats`, `racksByCategory`, `heatmapData`, dan `availableRacks`.<br>5. Mengubah `Material::all()->sum()` ke SQL `GROUP BY category` & menurunkan `totalPiutangAmount` dari koleksi yang sudah dimuat. | ✅ **Selesai (18,4x Lebih Cepat, -60% Kueri)** |
| 9 | **Penyimpanan Rak** | `GET /warehouse` | **25** | **17** | 10,37 ms | **8,29 ms** | 468,68 ms | **31,10 ms** | **2 jenis** *(3 ekstra)* | **0** | • Memuat `StorageRack::active()` 1x di `StorageController@index` untuk `getRackUtilization()` serta pemisahan 3 kategori rak (`shoes`, `accessories`, `before`).<br>• Menggabungkan 4 kueri agregat di `StorageService::getStatistics()` menjadi **1 kueri `selectRaw`**. | ✅ **Selesai (15x Lebih Cepat, -32% Kueri)** |
| 10 | **Gudang Manual** | `GET /warehouse/manual` | **20** | **16** | 7,87 ms | **7,54 ms** | 250,33 ms | **29,40 ms** | **0** | **0** | • Menggabungkan 3 kueri statistik (`totalItems`, `todayIn`, `todayOut`) di `ManualStorageController@index` menjadi **1 kueri `selectRaw`**. | ✅ **Selesai (8,5x Lebih Cepat, -20% Kueri)** |
| 11 | **Rak Manual** | `GET /warehouse/manual/racks` | **16** | **14** | 7,64 ms | **6,29 ms** | 1.100,92 ms | **31,58 ms** | **0** | **0** | • Turun 2 kueri berkat konsolidasi global badge sidebar di `AppServiceProvider`. | ✅ **Selesai (Optimal, 0 Duplikat)** |
| 12 | **Belanja Gudang (WMS)** | `GET /warehouse/purchase` | **3** | **3** | 1,23 ms | **1,78 ms** | 181,98 ms | **9,13 ms** | **0** | **0** | • Sudah sangat optimal sejak awal (3 kueri, 0 duplikat). | ✅ **Selesai (Optimal)** |
| 13 | **Barang Keluar (WMS)** | `GET /warehouse/disbursement` | **3** | **3** | 1,87 ms | **1,19 ms** | 194,66 ms | **7,56 ms** | **0** | **0** | • Sudah sangat optimal sejak awal (3 kueri, 0 duplikat). | ✅ **Selesai (Optimal)** |
| 14 | **Riwayat Mutasi (WMS)** | `GET /warehouse/history` | **2** | **2** | 1,70 ms | **2,41 ms** | 169,80 ms | **11,84 ms** | **0** | **0** | • Sudah sangat optimal sejak awal (2 kueri, 0 duplikat). | ✅ **Selesai (Optimal)** |
| 15 | **Label Custom** | `GET /admin/custom-label` | **15** | **13** | 7,11 ms | **6,74 ms** | 152,34 ms | **25,31 ms** | **0** | **0** | • Turun 2 kueri berkat konsolidasi global badge sidebar di `AppServiceProvider`. | ✅ **Selesai (Optimal, 0 Duplikat)** |

## 📌 4. DIVISI WORKSHOP / BENGKEL (TUGAS 4 — JADWAL: 12–17 NOV 2026)
*(Akan diisi saat pengukuran baseline Tugas 4 dimulai)*

## 📌 5. DIVISI FINANCE / KEUANGAN (TUGAS 5 — JADWAL: 18–20 NOV 2026)
*(Akan diisi saat pengukuran baseline Tugas 5 dimulai)*

## 📌 6. DIVISI CUSTOMER EXPERIENCE / CX (TUGAS 6 — JADWAL: 21–24 NOV 2026)
*(Akan diisi saat pengukuran baseline Tugas 6 dimulai)*

## 📌 7. MASTER DATA & SYSTEM (TUGAS 7 — JADWAL: 25–27 NOV 2026)
*(Akan diisi saat pengukuran baseline Tugas 7 dimulai)*


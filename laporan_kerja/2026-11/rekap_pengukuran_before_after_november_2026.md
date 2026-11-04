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

## 📌 3. DIVISI GUDANG (TUGAS 3 — JADWAL: 7–11 NOV 2026)
*(Akan diisi saat pengukuran baseline Tugas 3 dimulai)*

## 📌 4. DIVISI WORKSHOP / BENGKEL (TUGAS 4 — JADWAL: 12–17 NOV 2026)
*(Akan diisi saat pengukuran baseline Tugas 4 dimulai)*

## 📌 5. DIVISI FINANCE / KEUANGAN (TUGAS 5 — JADWAL: 18–20 NOV 2026)
*(Akan diisi saat pengukuran baseline Tugas 5 dimulai)*

## 📌 6. DIVISI CUSTOMER EXPERIENCE / CX (TUGAS 6 — JADWAL: 21–24 NOV 2026)
*(Akan diisi saat pengukuran baseline Tugas 6 dimulai)*

## 📌 7. MASTER DATA & SYSTEM (TUGAS 7 — JADWAL: 25–27 NOV 2026)
*(Akan diisi saat pengukuran baseline Tugas 7 dimulai)*


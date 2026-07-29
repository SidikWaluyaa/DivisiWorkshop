# 📋 Laporan Kerja - Sistem Workshop
**Hari & Tanggal:** Selasa, 28 Juli 2026

Berikut adalah daftar pekerjaan yang dikerjakan hari ini:

---

## 🛠️ Pekerjaan yang Diselesaikan Hari Ini

### 1. 📖 Pembuatan Buku Panduan Pengguna (User Manual Book) Divisi Workshop
* **Masalah:** Sistem belum memiliki panduan tertulis resmi yang lengkap bagi staf workshop (Admin Workshop, Tim Persiapan, Tim Sortir, Teknisi Produksi, QC, dan Gudang Finish) untuk memahami alur kerja operasional secara detail.
* **Solusi:** Membuat berkas panduan pengguna komprehensif bernama `manual-book-WS.md` di direktori utama proyek. Penulisan manual book mengacu pada 17 screenshot aktual di folder `/Workshop` yang diurutkan secara runtut berdasarkan alur kerja sistem.
* **Dampak:**
  - Menyediakan panduan visual langkah demi langkah bagi setiap tim stasiun pengerjaan di workshop (Persiapan ➔ Sortir ➔ Produksi ➔ QC ➔ Revisi ➔ Garansi ➔ Info Keterlambatan).
  - Mengintegrasikan screenshot **`17_Teknisi.jpeg`** ke dalam 3 stasiun kerja (*Preparation*, *Production*, dan *QC*) untuk menjelaskan alur penugasan dan pelacakan penanggung jawab pengerjaan sepatu.
  - Dokumen ditulis dalam format Markdown (.md) yang rapi, informatif, dan siap dikonversi ke PDF/Word sewaktu-waktu.

### 2. 📊 Dokumentasi Lengkap Query & Logic Halaman Laporan Performa CS (`/cs/analytics`)
* **Masalah:** Belum ada dokumentasi teknis tertulis yang menjelaskan secara rinci seluruh query database, rumus perhitungan, dan alur data di balik halaman Laporan Performa CS (CS Performance Analytics). Hal ini menyulitkan proses audit, debugging, dan pengembangan fitur lanjutan.
* **Solusi:** Menelusuri seluruh kode sumber di `CsDashboardController.php` (836 baris), model `CsLead`, `CsActivity`, dan `WorkOrder`, serta template Blade `index.blade.php` untuk mendokumentasikan **9 section** lengkap ke dalam berkas `query-laporan-performa.md`.
* **Dampak:**
  - Menyediakan referensi teknis komprehensif yang mencakup: Filter Global, Global Overview Metrics, Closing Path Analysis, Pipeline Funnel, Response Time Analytics, Channel Performance, Lost Analysis, CS KPI Leaderboard, dan Insights Performa.
  - Setiap section didokumentasikan dengan format standar: query PHP/SQL, tabel & kolom terkait, rumus perhitungan, dan catatan tambahan.
  - Dilengkapi lampiran referensi model & konstanta serta peta alur data (data flow diagram) untuk memudahkan pemahaman arsitektur sistem.

### 3. 🏢 Dokumentasi Lengkap Query & Logic Halaman Pusat Kendali Gudang (`/warehouse/dashboard`)
* **Masalah:** Belum ada acuan teknis tertulis mengenai query database, model, dan rumus kalkulasi logistik yang menyokong halaman dashboard utama divisi gudang (Pusat Kendali Gudang).
* **Solusi:** Melakukan penelusuran kode pada Livewire Component `Dashboard.php` (gudang), `WarehouseDashboardApiService.php`, `StorageService.php`, serta view template Blade terkait untuk mendokumentasikan **14 section** utama ke dalam berkas `query-dashboard-gudang.md`.
* **Dampak:**
  - Menyediakan dokumentasi lengkap yang mencakup Filter & Periode, API Integration, Ringkasan Cards, Status Summary, 9 Metric Cards Utama (dengan rumus step-by-step mendetail), Peta Okupansi Rak, Grafik Laju Arus Keseimbangan, Tingkat Clearance, Tabel Audit Arus Harian, Tren Performa QC, Komposisi Hasil QC, SPK Pending, SPK Diterima, dan Pusat Pengiriman.
  - Setiap metrik clearance rate dan KPI visual lainnya dijelaskan dengan formula matematika serta makna arah alirannya (+/-) secara komprehensif.
  - Berkas dokumentasi disimpan secara lokal dalam format Markdown (.md) yang rapi dan terstruktur.

### 4. 🐛 Perbaikan Bug Ekspor PDF SPK Batal / Downgrade Fast Track
* **Masalah:** Terjadi error `InvalidArgumentException: The filename and the fallback cannot contain the "/" and "\" characters` di server production saat mengunduh PDF untuk kategori **Batal Fast Track**. Hal ini disebabkan karena nama file PDF di-generate menggunakan `$reportTitle` ('Laporan SPK Batal / Downgrade Fast Track') yang mengandung karakter garis miring (`/`), yang dilarang oleh komponen header Symfony.
* **Solusi:** Memperbarui fungsi `exportFastTrackPdf` di [WorkshopDashboardController.php](file:///c:/laragon/www/SistemWorkshop/app/Http/Controllers/WorkshopDashboardController.php) untuk secara otomatis mengganti karakter spasi (` `), garis miring (`/`), dan garis miring terbalik (`\`) dengan karakter underscore (`_`) sebelum membuat nama file PDF.
* **Dampak:**
  - Menghilangkan error `InvalidArgumentException` secara total.
  - Memastikan proses ekspor PDF untuk kategori "Batal Fast Track" dan semua kategori laporan Fast Track lainnya berjalan dengan lancar dan aman di server production.

### 5. 🛠️ Integrasi Modal Revisi Workshop dengan Sistem `WorkOrderRevision` Terpusat
* **Masalah:** Alur penolakan (reject/revisi) pada stasiun kerja QC dan Production sebelumnya bersifat lokal dan tidak tercatat di tabel `work_order_revisions` maupun halaman riwayat revisi `/revision`. Pengguna juga harus memilih stasiun target reset secara manual di modal yang kurang efisien secara UX.
* **Solusi:**
  - Menambahkan kolom `origin_status` di tabel `work_order_revisions` via file migrasi baru untuk mencatat stasiun asal revisi.
  - Memperbarui `WorkflowService::revise` agar otomatis membuat record `WorkOrderRevision` dengan status `'OPEN'`, serta mendukung penyimpanan data multiple photo (`photo_paths`).
  - Memperbarui komponen `revision-modal.blade.php` agar menyembunyikan input stasiun/tahap dan stasiun reset jika aktif pada stasiun QC atau Production (SPK langsung dipindahkan ke status `REVISI`).
  - Menghilangkan template alasan bawaan (seperti `Upper:`, `Sol:`) agar textarea kosong secara default sesuai permintaan user.
  - Mengimplementasikan sistem **Multiple File Upload** dengan **Image Preview Grid** menggunakan AlpineJS (`FileReader` loop) di modal revisi sehingga pengguna bisa mengunggah lebih dari 1 foto bukti dan melihat pratinjaunya secara interaktif.
  - Menambahkan **Kompresi Gambar Sisi Klien (Client-side Compression)** pada modal revisi menggunakan HTML5 Canvas API untuk mengecilkan resolusi gambar (maks 1000px) dan mengompres kualitas foto (JPEG 70%) di browser sebelum diunggah guna menghemat kuota internet dan mempercepat proses kirim revisi.
  - Memperbarui `QCController::reject`, `ProductionController::reject`, dan `RevisionController::request` untuk memproses seluruh file foto bukti secara iteratif, mengompresnya di sisi server via GD Intervention Image, dan menyimpannya ke tabel database terkait.
  - Memperbarui `RevisionController::complete` agar saat revisi selesai, SPK secara otomatis dikembalikan ke stasiun asalnya (`origin_status`) dan record revisi ditandai `'FINISHED'`.
  - Menampilkan kolom **"Sumber"** pada tabel halaman `/revision` (`index.blade.php`) dan panel detail metadata (`show.blade.php`) dengan desain badge status yang informatif.
  - Membuat 4 **Info Cards (Metric Cards)** di bagian atas halaman `/revision` yang menghitung secara dinamis total revisi aktif berdasarkan stasiun asalnya (`Revisi QC`, `Revisi Production`, `Revisi Selesai`, dan `Total Revisi Aktif`).
  - Memperbaiki isu gambar rusak (*broken images*) dengan menormalkan pembacaan path (menghilangkan prefiks ganda `storage/` secara otomatis menggunakan regex/substring pada getter model `WorkOrderRevision.php`).
  - Memperbaiki rendering foto revisi pada timeline **Workshop Activity Timeline** di halaman detail order admin ([show.blade.php](file:///c:/laragon/www/SistemWorkshop/resources/views/admin/orders/show.blade.php)) agar menggunakan helper `$event['raw_model']->photo_urls` terformat untuk menghindari duplikasi prefiks `/storage/storage/...`.
* **Dampak:**
  - Semua riwayat revisi internal workshop (QC Reject / Prod Reject) kini tercatat secara terpusat di database dan muncul pada menu Riwayat Revisi `/revision`.
  - UX modal revisi menjadi jauh lebih bersih, interaktif, dan modern berkat upload beberapa gambar sekaligus beserta tampilan preview.
  - Proses unggah foto bukti menjadi sangat cepat dan hemat kuota karena file foto berukuran besar dari kamera HP otomatis dikompres menjadi berukuran kecil sebelum dikirim ke server.
  - Pengguna kini dapat dengan jelas melihat asal stasiun pengaju revisi (`QC`, `PRODUCTION`, atau `SELESAI`) di halaman `/revision`.
  - Dashboard `/revision` dilengkapi dengan metrik ringkasan (*Summary Cards*) yang memberikan overview cepat total revisi dari tiap stasiun asal secara real-time.
  - Masalah gambar rusak (broken image) pada halaman riwayat revisi dan timeline detail order admin teratasi sepenuhnya.
  - Alur balik (Boomerang) berjalan otomatis begitu revisi diselesaikan.

# 📋 Laporan Kerja Harian
**Hari & Tanggal:** Selasa, 29 Juli 2026

---

## 🛠️ Apa Saja yang Dikerjakan Hari Ini?

### 1. 🚀 Upload Kode ke GitHub & Sinkronisasi Server

**Kenapa dikerjakan?**  
Semua perbaikan dan fitur baru yang sudah dikerjakan sebelumnya (perbaikan PDF Fast Track, fitur revisi foto, kompresi foto, info card sumber revisi) masih tersimpan di laptop saja. Belum dikirim ke GitHub dan belum masuk ke server yang dipakai user.

**Apa yang dilakukan?**  
- Mengirim (push) sebanyak **11 file** yang sudah diubah ke GitHub, supaya kodenya aman dan bisa diakses tim.
- Menyiapkan panduan singkat untuk tim admin agar bisa melakukan update di server production pakai aaPanel (tinggal `git pull` lalu jalankan migrasi database).

**Hasilnya?**  
- Kode terbaru sudah aman tersimpan di GitHub.
- Server production tinggal di-update untuk langsung bisa dipakai oleh user akhir.

---

### 2. 📘 Pembuatan Buku Panduan (Manual Book) Divisi Finance

**Kenapa dikerjakan?**  
Belum ada dokumentasi resmi yang menjelaskan cara menggunakan fitur-fitur di modul Divisi Finance pada sistem Shoe Workshop. Ini penting supaya tim finance bisa paham cara pakai setiap fitur tanpa harus bertanya-tanya.

**Apa yang dilakukan?**  
Membuat dokumen **User Manual Book** lengkap untuk seluruh modul Finance, meliputi:

| No | Modul | Apa Isinya? |
|----|-------|-------------|
| 1 | **Pendahuluan** | Penjelasan umum modul finance, peran pengguna, dan cara akses. |
| 2 | **Dashboard Finance** | Cara baca ringkasan keuangan: total tagihan, kas masuk, piutang, dan rasio penagihan. |
| 3 | **Waiting Payment** | Cara memantau SPK yang masih nunggu pembayaran dari pelanggan. |
| 4 | **Data Invoice** | Cara kelola invoice gabungan — mulai dari bikin baru, lihat detail, catat pembayaran, sampai kirim link tagihan via WhatsApp. |
| 5 | **Transaksi Batal** | Cara lihat laporan SPK yang dibatalkan beserta analisis kerugiannya. |
| 6 | **Input Pembayaran** | Cara catat pembayaran manual dan filter riwayat pembayaran. |

Setiap modul dilengkapi dengan:
- ✅ **Screenshot** tampilan layar yang sesuai
- ✅ **Langkah-langkah penggunaan** (bernomor, mudah diikuti)
- ✅ **Penjelasan istilah** yang muncul di layar
- ✅ **Daftar route** sebagai referensi teknis

**Hasilnya?**  
File `manual-book_finance.md` sudah jadi dan siap digunakan sebagai panduan resmi tim finance.

---

### 3. 📄 Fitur Cetak PDF untuk Analitik CS

**Kenapa dikerjakan?**  
Ada kebutuhan untuk mengunduh dan mencetak ringkasan metrik analitik CS (Global Overview Metrics & Closing Path Analysis) di halaman `/cs/analytics` dalam format PDF yang rapi, profesional, dan siap cetak untuk keperluan laporan berkala.

**Apa yang dilakukan?**  
- Membuat **tombol "Cetak PDF"** berwarna merah rose di samping tombol "Update" pada halaman analitik CS.
- Menambahkan **route** `/cs/analytics/export-pdf` dan method controller khusus untuk memproses ekspor PDF menggunakan library DomPDF.
- Mendesain **template halaman cetak PDF (A4 Portrait)** yang bersih, dengan informasi parameter filter tanggal dan CS yang aktif, serta ringkasan metrik dalam bentuk tabel grid yang elegan.

**Hasilnya?**  
Pengguna sekarang bisa langsung mencetak/menyimpan PDF performa analitik CS sesuai filter tanggal yang dipilih dengan sekali klik.

---

### 4. 📊 Fitur Ekspor Excel untuk Analitik CS

**Kenapa dikerjakan?**  
Selain cetak PDF, tim operasional membutuhkan data mentah metrik analitik CS dalam format spreadsheet (.xlsx) agar dapat diolah lebih lanjut atau dijadikan bahan presentasi/analisis internal.

**Apa yang dilakukan?**  
- Membuat **tombol "Ekspor Excel"** berwarna hijau emerald di samping tombol Cetak PDF.
- Menambahkan **route** `/cs/analytics/export-excel` dan method controller untuk mendownload file Excel.
- Membuat class export `CsAnalyticsExport.php` yang menyusun letak metrik (Global Overview & Closing Path) menjadi tabel ber-layout bersih dengan pewarnaan brand hijau `#22AF85`, format kolom auto-size, dan teks tebal pada judul.

**Hasilnya?**  
File Excel hasil ekspor tersusun rapi dan langsung dapat dibuka di Excel atau Google Sheets secara terstruktur.

---

### 5. 📦 Fitur Ekspor Excel Dinamis untuk Dashboard Gudang (Warehouse)

**Kenapa dikerjakan?**  
Mempermudah tim gudang dalam mengunduh metrik kinerja dan data rincian secara dinamis untuk kelima tab aktif yang ada di dashboard gudang: Summary, Manifest, Sortir, Produksi, dan QC. Sesuai instruksi Anda, judul laporan disederhanakan dan disesuaikan.

**Apa yang dilakukan?**  
- Memperbarui **tombol "Ekspor Excel"** pada halaman `/warehouse/dashboard` agar mengirimkan parameter `active_tab` beserta filter pencarian & filter tanggal aktif.
- Memperbarui method `exportExcel` di `WarehouseDashboardController.php` untuk memproses data dinamis menggunakan `switch ($activeTab)`.
- Membuat 4 class export baru yang menyajikan metrik ringkasan di bagian atas dan daftar tabel di bagian bawah secara rapi dengan format Rata Tengah (Center-Aligned) pada kolom data metrik:
  1. `WarehouseManifestExport.php` (judul: `SHOE WORKSHOP - LAPORAN MANIFEST`)
  2. `WarehouseSortirExport.php` (judul: `SHOE WORKSHOP - LAPORAN SORTIR`)
  3. `WarehouseProductionExport.php` (judul: `SHOE WORKSHOP - LAPORAN PRODUKSI`)
  4. `WarehouseQcExport.php` (judul: `SHOE WORKSHOP - LAPORAN QC`)
  5. `WarehouseAnalyticsExport.php` (laporan utama, metrik SPK Tertahan dikecualikan).

**Hasilnya?**  
Tim gudang kini bisa mengekspor laporan kinerja gudang yang relevan dengan tab yang sedang aktif dengan data yang akurat dan visual tabel yang rapi ke Excel.

---

### 6. 💰 Fitur Ekspor Excel & PDF untuk Tab Piutang (Before & After)

**Kenapa dikerjakan?**  
Mempermudah tim gudang dan keuangan dalam mengunduh data outstanding piutang baik sebelum pengerjaan selesai maupun setelah selesai (piutang after). Laporan ini disajikan secara rapi dengan judul yang disederhanakan sesuai permintaan Anda.

**Apa yang dilakukan?**  
- Mendaftarkan route baru di `routes/web.php` untuk ekspor PDF dan Excel pada tab Piutang After (Selesai).
- Memperbarui `PiutangBeforeExport.php` dengan layout premium (menyertakan Ringkasan Piutang di atas tabel, warna brand hijau, dan format Rata Tengah) dengan judul: `SHOE WORKSHOP - LAPORAN PIUTANG BEFORE`.
- Membuat class export baru `PiutangAfterExport.php` khusus untuk data piutang setelah selesai dengan judul: `SHOE WORKSHOP - LAPORAN PIUTANG AFTER`.
- Menambahkan method `exportPiutangAfterPdf` dan `exportPiutangAfterExcel` di `WarehouseDashboardController.php`, serta memperbarui method dynamic `exportExcel` untuk memetakan tab piutang ini.
- Membuat template PDF `piutang-after-report.blade.php` khusus untuk Laporan Piutang After.
- Menyisipkan tombol **Cetak PDF** dan **Export Excel** di tab `piutang` (Piutang After) pada view `dashboard.blade.php` sehingga pengguna dapat mencetak dan mengunduh laporan secara langsung.

**Hasilnya?**  
Tim gudang kini dapat mengunduh laporan Piutang Before dan Piutang After dalam format Excel maupun PDF secara lengkap dan rapi.

---

### 7. 💳 Fitur Ekspor Excel untuk Dashboard Keuangan (Finance Dashboard)

**Kenapa dikerjakan?**  
Memfasilitasi tim keuangan dalam mengunduh seluruh metrik utama, distribusi status tagihan, dan distribusi tipe pembayaran dari dashboard keuangan ke spreadsheet Excel.

**Apa yang dilakukan?**  
- Mendaftarkan route baru `/finance/dashboard/export-excel` di `routes/web.php`.
- Membuat class export `FinanceDashboardExport.php` untuk merender metrik, status pembayaran, dan tipe pembayaran ke Excel.
- Menambahkan method `exportExcel` di `FinanceReportController.php` untuk menyinkronkan data dengan filter tanggal aktif.
- Menambahkan helper `getExportExcelUrl()` di `app/Livewire/Finance/Dashboard.php` untuk memfasilitasi integrasi livewire.
- Memasang tombol **Export Excel** di view `dashboard.blade.php` milik modul finance.

**Hasilnya?**  
Tim keuangan kini memiliki akses penuh untuk mengekspor data dashboard analitik keuangan ke Excel secara real-time.

### 8. 🎯 Fitur Ekspor Excel & Pemisahan Metrik OTO (DEAL vs PROSPECT) pada CX Dashboard

**Kenapa dikerjakan?**  
Memfasilitasi tim Customer Experience (CX) dalam membedakan metrik OTO yang berstatus **DEAL (Accepted)** dengan **PROSPECT (All)**, baik pada tampilan visual dashboard maupun saat diunduh ke workbook Excel.

**Apa yang dilakukan?**  
- Mendaftarkan route baru `/cx/dashboard/export-excel` di `routes/web.php` di dalam middleware dashboard CX.
- Membuat class export `CxUpsellExport.php` (utama) beserta sheet exporter:
  1. `Sheets/CxTambahJasaSheet.php` (lembar kerja `Tambah Jasa Revenue`)
  2. `Sheets/CxOtoSheet.php` (lembar kerja `OTO Revenue`, memuat detail OTO DEAL & PROSPECT terpisah)
- Menambahkan method `exportExcel` di `CxDashboardController.php` untuk memproses data summary upsell dan memicu pengunduhan berkas Excel.
- Memasang tombol **Ekspor Excel** di view `index.blade.php` milik dashboard CX pada section header Financial Analytics.
- Membagi tampilan kartu OTO di dashboard menjadi 2 sub-card berdampingan (warna hijau untuk **OTO DEAL** dan warna oranye untuk **OTO PROSPECT**).

**Hasilnya?**  
Tim CX kini dapat melihat performa deal dan potensi prospek OTO secara presisi di dashboard dan mengekspornya ke Excel secara real-time.

---

## 📂 File yang Dibuat/Diubah Hari Ini

| File | Jenis | Keterangan Singkat |
|------|-------|--------------------|
| `manual-book_finance.md` | Markdown | Buku panduan lengkap modul Divisi Finance (baru dibuat hari ini) |
| `routes/web.php` | PHP | Mendaftarkan route ekspor PDF/Excel untuk CS, Gudang, Keuangan, dan CX |
| `CsDashboardController.php` | PHP | Method ekspor PDF & Excel pada analitik CS |
| `pdf.blade.php` | Blade | Template halaman cetak PDF analitik CS (A4 Portrait) |
| `index.blade.php` | Blade | Menambahkan tombol "Cetak PDF" & "Ekspor Excel" pada filter dashboard CS |
| `CsAnalyticsExport.php` | PHP | Class generator data Excel analitik CS |
| `WarehouseDashboardController.php` | PHP | Method ekspor dinamis & method cetak/ekspor untuk Piutang Before & After |
| `WarehouseAnalyticsExport.php` | PHP | Class generator Excel kinerja gudang (SPK Tertahan dikecualikan) |
| `WarehouseManifestExport.php` | PHP | Class generator Excel laporan manifest logistik |
| `WarehouseSortirExport.php` | PHP | Class generator Excel laporan sortir gudang |
| `WarehouseProductionExport.php` | PHP | Class generator Excel laporan produksi gudang |
| `WarehouseQcExport.php` | PHP | Class generator Excel laporan QC gudang |
| `PiutangBeforeExport.php` | PHP | Class generator Excel laporan Piutang Before (layout premium diperbarui) |
| `PiutangAfterExport.php` | PHP | Class generator Excel laporan Piutang After |
| `piutang-after-report.blade.php`| Blade | Template halaman cetak PDF Piutang After |
| `FinanceReportController.php` | PHP | Method `exportExcel` untuk memproses & mengunduh Excel dashboard keuangan |
| `FinanceDashboardExport.php` | PHP | Class generator Excel laporan ringkasan keuangan |
| `Dashboard.php` (Finance) | PHP | Method `getExportExcelUrl` untuk integrasi tombol ekspor Excel |
| `dashboard.blade.php` (Gudang) | Blade | Menambahkan tombol ekspor dinamis & tombol cetak/ekspor di tab Piutang After |
| `dashboard.blade.php` (Keuangan)| Blade | Menambahkan tombol "Export Excel" pada toolbar dashboard keuangan |
| `CxDashboardController.php` | PHP | Method `exportExcel` untuk memproses & mengunduh Excel Financial Analytics CX |
| `CxUpsellExport.php` | PHP | Class utama generator Excel multi-sheet CX |
| `CxTambahJasaSheet.php` | PHP | Class generator sheet Excel Tambah Jasa Revenue |
| `CxOtoSheet.php` | PHP | Class generator sheet Excel OTO Revenue |
| `index.blade.php` (CX) | Blade | Menambahkan tombol "Ekspor Excel" pada Financial Analytics di dashboard CX |

### File yang di-push ke GitHub sebelumnya (Commit `e4a3f38`):

| File | Keterangan Singkat |
|------|--------------------|
| `QCController.php` | Perbaikan upload banyak foto sekaligus |
| `ProductionController.php` | Perbaikan upload banyak foto sekaligus |
| `RevisionController.php` | Kompresi foto otomatis di server & label "SELESAI" |
| `WorkOrderRevision.php` | Perbaikan link foto yang dobel path (`storage/storage/`) |
| `WorkflowService.php` | Otomatis bikin record revisi baru & rapikan array path foto |
| `revision-modal.blade.php` | Tampilan upload banyak foto + preview + kompresi di browser |
| `revision/index.blade.php` | Tambah kolom "Sumber" & 4 kartu info di halaman revisi |
| `revision/show.blade.php` | Tambah info "Sumber Revisi" di detail revisi |
| `admin/orders/show.blade.php` | Perbaikan foto yang tidak muncul di timeline order admin |
| `add_origin_status_...php` | Migrasi database: tambah kolom `origin_status` di tabel revisi |
| `laporan_kerja_28072026.md` | Laporan kerja kemarin (28 Juli 2026) |

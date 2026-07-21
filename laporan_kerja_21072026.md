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

4. **🎨 Pembersihan Warning Tailwind CSS Linter:**
   * Merapikan dan menghapus atribut class `block` yang bentrok dengan `flex` pada komponen elemen `<label>` di `edit-issue-modal.blade.php` (baris 172, 211, dan 264) untuk menjamin kerapian kode sesuai standar.

---

## 4. 📱 Redesain Halaman Laporan Publik (`/cx-issue/{spk}/report`) Berbasis Mobile-First & Responsive Design

### 💡 Mengapa Redesain Ini Dibuat?
Halaman laporan publik (`/cx-issue/{spk}/report`) adalah halaman utama yang dibagikan oleh Tim CS kepada pelanggan melalui WhatsApp untuk mengonfirmasi kendala sepatu. Diperlukan tampilan yang sangat responsif, estetis, dan nyaman dibaca dari smartphone hingga layar desktop.

### 🌟 Ringkasan Perubahan & Manfaatnya:

1. **📱 Header Logo Branding & Tipografi Mobile:**
   * Menambahkan header logo branding seluler (`md:hidden`) dan menyesuaikan ukuran judul SPK (`text-2xl sm:text-3xl md:text-4xl`) agar tidak terpotong pada layar smartphone yang sempit.

2. **📐 Tata Letak Dual-Column Responsif (Laptop/Desktop):**
   * Mengatur susunan grid menjadi `lg:col-span-7` (Galeri Foto) dan `lg:col-span-5` (Detail Kendala & Solusi) sehingga pada layar tablet/laptop (>=1024px) langsung tampil simetris berdampingan 2 kolom.

3. **🖼️ Galeri Foto dengan Tap-Hint Mobile:**
   * Mengoptimalkan padding kartu dari `p-8` menjadi `p-4 sm:p-6 md:p-8` and menambahkan *tap-hint* badge melayang ("🔍 Ketuk foto memperbesar") khusus mobile agar pelanggan di smartphone mengetahui foto dapat diperbesar (*lightbox full screen*).

4. **💬 Sticky Bottom Mobile Action Bar (Tombol WhatsApp Melayang):**
   * Menambahkan tombol WhatsApp melayang yang menempel di bagian bawah layar smartphone (`md:hidden`), sehingga pelanggan atau CS dapat langsung menghubungi admin kapan saja tanpa perlu scroll ke paling bawah.

5. **🛠️ Perbaikan Parsing Description & Linter Warning:**
   * Menambahkan pengecekan karakter pemisah pipe (`|`) pada `issue-report.blade.php` agar deskripsi umum tidak secara salah masuk ke kategori `Upper/Midsole/Bawaan`, sehingga kartu kendala & solusi terstruktur tampil dengan rapi.
   * Menghapus deklarasi class `flex` ganda pada elemen overlay galeri foto untuk menyelesaikan warning Tailwind CSS linter.

---

## 5. 🏷️ Pemisahan Input Form "Nama Jasa" & "Harga Jasa (Rp)" pada Modal Lapor Kendala

### 💡 Mengapa Fitur Ini Dibuat?
Sebelumnya, nama jasa dan harga digabungkan ke dalam satu teks di dropdown. Hal ini membuat dropdown terlihat sangat panjang dan kurang rapi saat teknisi mengetik manual. Dengan memisahkan kolom **Nama Jasa** dan **Harga Jasa (Rp)**, form menjadi jauh lebih rapi, terstruktur, dan fleksibel.

### 🌟 Ringkasan Perubahan & Manfaatnya:

1. **✨ Form Terpisah & Rapi:**
   * **Dropdown Nama Jasa:** Menampilkan murni nama perbaikan/perawatan resmi dari master (contoh: *"Ganti BOA Lacing"*).
   * **Input Harga Jasa (Rp):** Input angka terpisah dengan prefix `Rp` untuk nominal estimasi biaya.

2. **🤖 Auto-Fill Harga Otomatis + Fleksibilitas Manual:**
   * Saat nama jasa dipilih dari dropdown master, input harga Rp **otomatis terisi nominal resminya** (misal: `600000`).
   * **Bisa Diubah:** Teknisi/CS tetap dapat menyesuaikan atau mengedit nominal harga secara bebas jika ada diskon/penyesuaian khusus.
   * **Manual Support:** Jika memilih `Lainnya (Ketik Manual)...`, pengguna dapat mengetik nama jasa kustom sekaligus menentukan harganya sendiri.

---

## 6. 📤 Pencatatan Timestamp Waktu Kirim Status Pengiriman (`sent_at`) pada Dashboard CX (`/cx`)

### 💡 Mengapa Fitur Ini Dibuat?
Manajemen dan Tim CS membutuhkan pencatatan waktu pasti kapan status pengiriman laporan WhatsApp diubah menjadi **SEND (✅)** untuk memantau kecepatan respons dan tindak lanjut laporan kendala.

### 🌟 Ringkasan Perubahan & Manfaatnya:

1. **⏱️ Migration & Timestamp Tracking (`sent_at`):**
   * Berhasil menjalankan migration `2026_07_21_110000_add_sent_at_to_cx_issues_table.php` untuk menambahkan kolom `sent_at` (TIMESTAMP) pada tabel `cx_issues`.
   * Logika `setShippingStatus()` di `app/Livewire/Cx/Index.php` diperbarui sehingga setiap kali status diubah ke `SEND`, tanggal dan jam saat itu otomatis dicatat (`sent_at = now()`).
   * **Realtime Update:** Jika status diubah kembali ke `HOLD` lalu diubah ke `SEND` lagi, `sent_at` otomatis ter-update ke **jam dan menit terbaru**.

2. **📌 Tampilan Visual pada Kolom Order Info (`/cx`):**
   * Tepat di bawah badge status awal `Pre: PRODUCTION` / `Pre: PREPARATION` pada kolom **Order Info**, kini tampil indikator timestamp berupa badge teal:
     `📤 Kirim CX: 21 Jul 10:48`

---

## 7. 🟢 Indikator Visual SPK Selesai Follow Up CX (`CX RESOLVED`) pada Stasiun Workshop

### 💡 Mengapa Fitur Ini Dibuat?
Teknisi/operator di stasiun pengerjaan fisik (Preparation, Sortir, Production, QC, dan Gudang) membutuhkan tanda yang jelas dan instruksi hasil resolusi setelah SPK tersebut selesai di-follow up oleh Tim CS agar mereka memahami keputusan pelanggan tanpa kebingungan.

### 🌟 Ringkasan Perubahan & Manfaatnya:

1. **🏷️ Badge Hijau "CX RESOLVED" pada Kolom Nomor SPK (Collapsed):**
   * Di sebelah nomor SPK, sistem kini secara dinamis menampilkan badge hijau emerald: `✅ CX RESOLVED`. Teknisi langsung paham secara visual saat melihat daftar antrean stasiun.

2. **📌 Banner Informasi & Keputusan Pelanggan (Expanded):**
   * Ketika baris SPK diklik/dibuka, tampil **Banner Kartu Emerald Rapi** di bagian atas detail stasiun yang merangkum:
     * **Kendala Awal:** Detail masalah yang sempat dilaporkan stasiun.
     * **Keputusan Akhir Pelanggan:** Solusi persetujuan yang didapatkan oleh CS.
     * **Badge Tambahan Jasa & Waktu:** Badge estimasi waktu tambahan (misal `⏱️ Tambahan Waktu: 3 HARI`) dan rekomendasi jasa baru (misal `🛠️ Jasa Tambahan: Reglue Heavy`).
     * **Nama CS & Tanggal Resolusi:** Jam penyelesaian dan petugas CS penanggung jawab.

---

## 8. 🔝 Pengurutan Prioritas SPK "CX RESOLVED" Paling Atas di Antrean Stasiun Workshop

### 💡 Mengapa Fitur Ini Dibuat?
SPK yang sempat ditahan untuk *Follow Up CS* telah kehilangan waktu pengerjaan berharga. Untuk mengejar keterlambatan tersebut, SPK yang berstatus **`CX RESOLVED` harus langsung diproses dengan prioritas tertinggi (paling atas)** oleh teknisi stasiun.

### 🌟 Ringkasan Perubahan & Manfaatnya:

1. **⚡ Urutan Antrean Teratas (Mengalahkan Fast Track & Prioritas):**
   * Memperbarui logika pengurutan antrean pada `PreparationController`, `SortirController`, `ProductionController`, dan `QCController`.
   * SPK yang memiliki laporan kendala CX dengan status `RESOLVED` otomatis didudukkan di **nomor 1 teratas dalam antrean stasiun**, diikuti oleh SPK prioritas biasa/Fast Track, baru kemudian SPK regular biasa.

---

## 9. 🔍 Autocomplete Search & Badges Quick-Click Rekomendasi Jasa pada Modal "Tambah Jasa" CX

### 💡 Mengapa Fitur Ini Dibuat?
CS membutuhkan antarmuka yang cepat untuk memilih layanan/jasa tambahan tanpa perlu scroll manual dropdown pilihan layanan yang panjang. Selain itu, CS juga memerlukan jalan pintas instan untuk memilih jasa yang sebelumnya sudah direkomendasikan secara tertulis oleh teknisi di stasiun.

### 🌟 Ringkasan Perubahan & Manfaatnya:

1. **🔍 Autocomplete Search Dropdown (Alpine.js):**
   * Mengganti select box konvensional pada modal "Tambah Jasa" dengan searchable select dropdown interaktif.
   * CS dapat mengetik kata kunci pada kotak pencarian (misal: *"Clean"*) untuk menyaring daftar jasa resmi secara realtime.
   * Pilihan *"✏️ JASA CUSTOM (KETIK MANUAL)"* tetap tersedia di bagian atas daftar untuk fleksibilitas tinggi.

2. **✨ Badges Saran Jasa dari Teknisi (Quick-Click):**
   * Jika SPK tersebut memiliki rekomendasi jasa dari teknisi stasiun (`rec_service_1` atau `rec_service_2`), modal otomatis menampilkan badge saran di bagian atas form.
   * **Auto-Fill Pintar:** Mengklik badge tersebut akan memicu pemanggilan metode `selectSuggestedService()` di backend Livewire untuk mencari kecocokan nama jasa di master data, lalu **otomatis mengisi Kategori, Harga resmi, dan Hari Kerja (HK)**.
   * Jika saran tersebut berupa jasa custom manual, sistem akan tetap mengisinya sebagai *Custom Service* dan mencoba mengekstrak nominal angka harga (contoh: *"Rp 85.000"*) untuk dimasukkan ke input harga secara otomatis.

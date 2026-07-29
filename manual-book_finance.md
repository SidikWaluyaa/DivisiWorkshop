# 📘 User Manual Book — Divisi Finance

**Aplikasi:** Shoe Workshop  
**Modul:** Divisi Finance (Sidebar: DIVISI FINANCE)  
**Versi Dokumen:** 1.0  
**Tanggal:** 29 Juli 2026  

---

## 1. Pendahuluan & Peran Pengguna

### 1.1 Tentang Modul Finance

Modul **Divisi Finance** pada aplikasi Shoe Workshop merupakan pusat pengelolaan keuangan yang mencakup pemantauan arus kas, penagihan (invoicing), pencatatan pembayaran, serta pelaporan transaksi batal. Modul ini dirancang untuk membantu tim finance dalam memastikan seluruh transaksi tercatat, terverifikasi, dan dapat dilacak secara transparan.

### 1.2 Peran Pengguna

| Peran | Deskripsi |
|-------|-----------|
| **Admin Finance** | Pengguna dengan akses penuh ke seluruh fitur Divisi Finance, termasuk membuat invoice, mencatat pembayaran, dan mengelola transaksi batal. |
| **Admin Gudang** | Pengguna dengan hak akses `admin` yang juga dapat mengakses fitur finance sesuai konfigurasi hak akses. |

### 1.3 Cara Mengakses Modul Finance

1. Login ke sistem Shoe Workshop menggunakan akun yang memiliki hak akses `finance`.
2. Pada sidebar kiri, klik menu **DIVISI FINANCE** (ikon hijau dengan simbol "$").
3. Sub-menu akan terbuka dan menampilkan seluruh modul finance yang tersedia.

### 1.4 Struktur Menu Sidebar Finance

| No | Menu | Keterangan |
|----|------|------------|
| 1 | Dashboard Finance | Ringkasan keuangan & statistik real-time |
| 2 | Waiting Payment | SPK yang menunggu konfirmasi pembayaran |
| 3 | Finance Transaksi | Daftar seluruh transaksi keuangan |
| 4 | Data Invoice | Manajemen invoice gabungan |
| 5 | Transaksi Batal | Laporan SPK yang dibatalkan |
| 6 | Audit Bayar CS | Audit pembayaran oleh CS |
| 7 | Input Pembayaran | Pencatatan pembayaran manual per invoice |
| 8 | Import Mutasi | Import data mutasi bank |
| 9 | Verifikasi Mutasi | Verifikasi pencocokan mutasi & pembayaran |

---

## 2. Modul Dashboard Finance

![Dashboard Finance](./Finance/01_Dashboard_Finance.png)

### 2.1 Tujuan Fitur

**Dashboard Finance** adalah halaman utama modul keuangan yang menyajikan ringkasan (summary) kondisi keuangan secara real-time. Dashboard ini memungkinkan tim finance untuk memantau total tagihan, kas masuk, piutang aktif, serta distribusi status dan tipe pembayaran dalam satu tampilan.

**Route:** `GET /finance/dashboard`

### 2.2 Komponen Dashboard

#### A. Kartu Ringkasan Atas (Summary Cards)

Di bagian atas dashboard, terdapat **4 kartu ringkasan** utama:

| Kartu | Label | Deskripsi |
|-------|-------|-----------|
| 🟢 | **Total Nilai Tagihan** | Jumlah total seluruh tagihan (invoice) pada periode aktif. |
| 🔵 | **Kas Masuk (Tervalidasi)** | Total dana yang sudah masuk dan terverifikasi (realisasi penerimaan). |
| 🟠 | **Sisa Piutang Aktif** | Selisih antara total tagihan dan kas masuk — jumlah piutang yang belum tertagih. |
| ✅ | **Rasio Penagihan (Collection)** | Persentase efektivitas cash flow, dihitung dari perbandingan kas masuk terhadap total tagihan. |

#### B. Distribusi Status Tagihan

Menampilkan **3 kategori status** tagihan beserta jumlah transaksi dan total nominal:

| Status | Keterangan |
|--------|------------|
| **Belum Bayar** | Invoice yang belum ada pembayaran sama sekali. |
| **DP/Cicil** | Invoice yang sudah dibayar sebagian (down payment atau cicilan). |
| **Lunas** | Invoice yang sudah dibayar penuh. |

Setiap kategori menampilkan:
- Jumlah transaksi (contoh: `10 Transaksi`)
- Total nominal (contoh: `Rp 2.410.000`)
- Progress bar visual (indikator persentase terhadap total)

#### C. Distribusi Tipe Pembayaran

Menampilkan pembagian pembayaran berdasarkan **tipe/tahapan** pembayaran:

| Tipe | Badge | Keterangan |
|------|-------|------------|
| **DP Awal** | `BEFORE` | Pembayaran down payment pertama. |
| **Pelunasan** | `AFTER` | Pembayaran pelunasan akhir. |
| **Tambah Jasa** | `+` | Pembayaran untuk penambahan jasa layanan. |
| **Lunas Awal** | — | Pembayaran langsung lunas di awal. |
| **Ongkir** | `ONGKIR` | Pembayaran biaya pengiriman. |
| **Pembayaran OTO** | `OTO` | Pembayaran otomatis dari sistem. |

Setiap tipe menampilkan jumlah transaksi dan total nominal.

#### D. Tabel Data (Data Invoices & Data Pembayaran)

Terdapat **2 tab** di bagian bawah dashboard:

- **📋 DATA INVOICES** — Menampilkan daftar seluruh invoice.
- **💳 DATA PEMBAYARAN** — Menampilkan daftar seluruh pembayaran.

**Filter Status** pada tab Data Invoices:

| Filter | Keterangan |
|--------|------------|
| **Semua** | Menampilkan seluruh invoice tanpa filter. |
| **Belum Bayar** | Hanya invoice yang belum ada pembayaran. |
| **DP/Cicil** | Hanya invoice dengan status DP atau cicilan. |
| **Lunas** | Hanya invoice yang sudah lunas. |

**Kolom tabel Data Invoices:**

| Kolom | Keterangan |
|-------|------------|
| # | Nomor urut. |
| No. Invoice | Nomor invoice unik (format: `INV-YYMMDD-XXXX`). |
| Customer | Nama pelanggan. |
| Total | Total tagihan invoice. |
| Ongkir | Biaya pengiriman. |
| Diskon | Potongan diskon jika ada. |
| Terbayar | Jumlah yang sudah dibayar. |
| Sisa | Sisa tagihan yang belum terbayar. |
| Status | Badge status (BL = Belum Bayar, DP = DP/Cicil, L = Lunas). |
| Tanggal | Tanggal pembuatan invoice. |

#### E. Integrasi Real-Time Finance API

Di bagian paling bawah dashboard, terdapat panel informasi **API endpoint** yang memungkinkan integrasi data keuangan ke aplikasi eksternal secara real-time.

| Parameter | Keterangan |
|-----------|------------|
| **Request Endpoint URL (GET)** | URL endpoint API untuk mengambil data dashboard. |
| **HTTP Header Key** | `X-API-KEY` — header key untuk autentikasi. |
| **HTTP Header Value (API Key)** | API key unik yang di-generate sistem. |
| **start_date** | Tanggal awal filter periode (format: YYYY-MM-DD). |
| **end_date** | Tanggal akhir filter periode (format: YYYY-MM-DD). |
| **api_key / key** | API key alternatif jika tidak dikirim via HTTP header. |

Terdapat tombol **📋 COPY URL** untuk menyalin endpoint dan tombol **ACTIVE ENDPOINT** sebagai indikator status.

#### F. Export PDF

Tombol **📤 EXPORT PDF** di kanan atas tabel memungkinkan pengguna mengekspor seluruh data dashboard ke format PDF untuk keperluan laporan.

### 2.3 Langkah-Langkah Penggunaan

1. Klik **Dashboard Finance** pada sidebar Divisi Finance.
2. Perhatikan **4 kartu ringkasan** di bagian atas untuk melihat kondisi keuangan saat ini.
3. Lihat bagian **Distribusi Status Tagihan** untuk mengetahui berapa banyak invoice yang belum bayar, DP/Cicil, dan lunas.
4. Lihat bagian **Distribusi Tipe Pembayaran** untuk melihat komposisi jenis pembayaran.
5. Gunakan tab **DATA INVOICES** atau **DATA PEMBAYARAN** untuk melihat rincian data.
6. Gunakan **Filter Status** (Semua, Belum Bayar, DP/Cicil, Lunas) untuk menyaring data invoice.
7. Klik tombol **📤 EXPORT PDF** untuk mengekspor data ke PDF jika diperlukan.
8. Periode data dapat diatur melalui **date picker** di pojok kanan atas (contoh: `01/07/2026 s/d 29/07/2026`).

### 2.4 Istilah Penting

| Pemahaman | Penjelasan |
|-----------|------------|
| **Piutang Aktif** | Sisa tagihan yang belum dilunasi oleh pelanggan. |
| **Rasio Penagihan (Collection)** | Efektivitas penagihan, dihitung: (Kas Masuk ÷ Total Tagihan) × 100%. |
| **DP/Cicil** | Down Payment — pembayaran sebagian sebelum lunas. |
| **Kas Masuk (Tervalidasi)** | Uang yang sudah masuk ke rekening dan telah diverifikasi. |
| **Gateway** | Kode jalur CS/sumber pesanan (contoh: `SW`). |

---

## 3. Modul Waiting Payment

![Waiting Payment](./Finance/02_Waiting_Payment.png)

### 3.1 Tujuan Fitur

**Waiting Payment** (Menunggu Pembayaran) adalah halaman yang menampilkan daftar SPK (Surat Perintah Kerja) yang sedang menunggu konfirmasi pembayaran dari pelanggan sebelum pesanan diproses lebih lanjut ke tahap logistik.

**Route:** `GET /finance/waiting-payment`

### 3.2 Komponen Halaman

#### A. Header

- **Breadcrumb:** `FINANCE > WAITING PAYMENT`
- **Judul:** "Menunggu Pembayaran"
- **Deskripsi:** "Pantau SPK yang menunggu konfirmasi pembayaran dari pelanggan sebelum masuk ke logistik."

#### B. Kartu Statistik

| Komponen | Keterangan |
|----------|------------|
| **Total SPK Menunggu** | Menampilkan jumlah total SPK yang saat ini berstatus "Waiting Payment". |

#### C. Kolom Pencarian

- **Placeholder:** "Cari SPK atau Nama..."
- Memungkinkan pencarian berdasarkan nomor SPK atau nama pelanggan.

#### D. Area Data

Jika tidak ada SPK yang menunggu pembayaran, ditampilkan pesan:

> *"TIDAK ADA SPK DENGAN STATUS WAITING PAYMENT"*

Jika ada data, akan ditampilkan dalam bentuk tabel/daftar SPK yang menunggu pembayaran.

### 3.3 Langkah-Langkah Penggunaan

1. Klik **Waiting Payment** pada sidebar Divisi Finance.
2. Periksa angka pada kartu **Total SPK Menunggu** untuk mengetahui berapa SPK yang belum dibayar.
3. Gunakan **kolom pencarian** (pojok kanan atas) untuk mencari SPK tertentu berdasarkan nomor SPK atau nama pelanggan.
4. Jika terdapat SPK dalam daftar, periksa detail masing-masing dan lakukan tindak lanjut (hubungi pelanggan atau konfirmasi pembayaran).

### 3.4 Istilah Penting

| Istilah | Penjelasan |
|---------|------------|
| **SPK** | Surat Perintah Kerja — dokumen perintah pengerjaan sepatu. |
| **Waiting Payment** | Status yang menandakan bahwa SPK sudah selesai dikerjakan namun belum ada konfirmasi pembayaran. |
| **Logistik** | Tahap pengiriman barang ke pelanggan setelah pembayaran dikonfirmasi. |

---

## 4. Modul Data Invoice

### 4.1 Halaman Daftar Invoice (Sentral Invoice)

![Data Invoice](./Finance/03_Data_Invoice.png)

#### 4.1.1 Tujuan Fitur

**Sentral Invoice** adalah pusat manajemen tagihan gabungan (grouped invoices) yang mengkonsolidasikan satu atau lebih SPK milik pelanggan yang sama ke dalam satu invoice. Fitur ini memungkinkan pengelolaan tagihan yang lebih terstruktur, terutama untuk pelanggan dengan banyak pesanan.

**Route:** `GET /finance/invoices`

#### 4.1.2 Komponen Halaman

##### A. Header

- **Badge:** `DATA ARSIP`
- **Judul:** "SENTRAL INVOICE"
- **Subtitle:** "MANAJEMEN TAGIHAN GABUNGAN TERINTEGRASI"
- **Tombol Aksi:** **🟡 BUAT INVOICE BARU +** (pojok kanan atas)

##### B. Filter & Pencarian

| Filter | Keterangan |
|--------|------------|
| **Semua Gateway** | Dropdown untuk memfilter berdasarkan kode gateway/CS (contoh: SW, BN, dll). |
| **Semua Pembayaran** | Dropdown untuk memfilter berdasarkan status pembayaran. |
| **🔍 Cari Nomor/Nama...** | Kolom pencarian untuk mencari invoice berdasarkan nomor invoice atau nama pelanggan. |

##### C. Tabel Data Invoice

| Kolom | Keterangan |
|-------|------------|
| **No. Invoice** | Nomor invoice unik beserta tanggal dan waktu pembuatan. Format: `INV-YYMMDD-XXXX`. |
| **Data Pelanggan** | Nama pelanggan dan nomor telepon. |
| **Rincian** | Jumlah pasang sepatu dan kode gateway (contoh: `1 Pasang Sepatu`, `SW`). |
| **Status SPK** | Status SPK terkait (contoh: `BELUM SELESAI`, `SELESAI`). |
| **Total Tagihan** | Rincian keuangan lengkap: Total nominal, terbayar, DP (%), penagihan full (%). |
| **Status** | Badge status pembayaran: `DP/CICIL` (kuning), `LUNAS` (hijau), `BELUM BAYAR` (merah). |
| **Estimasi** | Target tanggal estimasi selesai (contoh: `TARGET 26 AUG 2026`). |
| **Nota** | Ikon printer (🖨️) untuk mencetak nota gabungan. |

##### D. Informasi Tambahan pada Kolom Total Tagihan

Setiap baris invoice menampilkan rincian keuangan detail:

| Sub-kolom | Keterangan |
|-----------|------------|
| **Rp [jumlah]** | Total tagihan invoice. |
| **TERBAYAR: RP [jumlah]** | Jumlah yang sudah dibayar. |
| **DP ([%])** | Jumlah down payment dan persentasenya. |
| **PENAGIHAN FULL: Rp [jumlah] ([%])** | Total jumlah penagihan penuh beserta persentasenya. |

#### 4.1.3 Langkah-Langkah Penggunaan

1. Klik **Data Invoice** pada sidebar Divisi Finance.
2. Gunakan **filter Gateway** untuk memilih invoice dari jalur CS tertentu.
3. Gunakan **filter Pembayaran** untuk menyaring berdasarkan status pembayaran.
4. Gunakan **kolom pencarian** untuk mencari invoice atau pelanggan tertentu.
5. Klik baris invoice untuk melihat **detail rincian invoice**.
6. Klik ikon **🖨️** (Nota) di kolom terakhir untuk mencetak nota gabungan.
7. Klik tombol **🟡 BUAT INVOICE BARU +** untuk membuat invoice gabungan baru.

---

### 4.2 Halaman Buat Invoice Gabungan

![Create Data Invoice](./Finance/04_Create_Data_Invoice.png)

#### 4.2.1 Tujuan Fitur

Halaman **Buat Invoice Gabungan** memungkinkan tim finance untuk membuat invoice baru yang menggabungkan satu atau lebih SPK milik pelanggan yang sama. Sistem akan mencari data pelanggan terlebih dahulu sebelum menampilkan SPK yang bisa digabungkan.

**Route:** `GET /finance/invoices/create`

#### 4.2.2 Komponen Halaman

##### A. Header

- **Tombol kembali:** `<` (panah kembali ke halaman sebelumnya)
- **Badge:** `TRANSAKSI`
- **Judul:** "Buat Invoice Gabungan"
- **Subtitle:** "INVENTARISASI TAGIHAN BELUM TERBIT"

##### B. Form Pencarian Pelanggan

| Komponen | Keterangan |
|----------|------------|
| **Label:** | "CARI DATA PELANGGAN & CEK TAGIHAN" |
| **Input:** | Field input dengan placeholder "Nama atau Nomor HP..." |
| **Ikon:** | Ikon avatar pelanggan (🧑) di sebelah kiri field. |
| **Tombol:** | **🟡 CARI SEKARANG 🔍** — tombol untuk memulai pencarian. |

Setelah pencarian, sistem akan menampilkan daftar SPK milik pelanggan yang belum tergabung ke invoice manapun. Pengguna kemudian dapat memilih SPK mana saja yang akan digabungkan ke dalam satu invoice baru.

#### 4.2.3 Langkah-Langkah Penggunaan

1. Dari halaman **Data Invoice**, klik tombol **🟡 BUAT INVOICE BARU +**.
2. Pada halaman yang muncul, masukkan **nama** atau **nomor HP** pelanggan di kolom pencarian.
3. Klik tombol **🟡 CARI SEKARANG 🔍**.
4. Sistem akan menampilkan daftar SPK milik pelanggan yang belum memiliki invoice.
5. Centang/pilih SPK yang ingin digabungkan ke dalam satu invoice.
6. Klik tombol konfirmasi untuk **membuat invoice gabungan** baru.
7. Setelah berhasil, sistem akan mengarahkan ke halaman rincian invoice yang baru dibuat.

**Route POST:** `POST /finance/invoices/store` — digunakan secara internal saat form dikirim.

---

### 4.3 Halaman Rincian Invoice (Show)

![Show Data Invoice](./Finance/05_Show_Data_Invoice.png)

#### 4.3.1 Tujuan Fitur

Halaman **Rincian Invoice** menampilkan informasi lengkap dari sebuah invoice gabungan, termasuk data pelanggan, rincian pesanan (SPK terkait), rekapitulasi keuangan, riwayat pembayaran, serta fitur penagihan dan pencetakan nota.

**Route:** `GET /finance/invoices/{invoice}`

#### 4.3.2 Komponen Halaman

##### A. Header Invoice

| Komponen | Keterangan |
|----------|------------|
| **Judul:** | "RINCIAN INVOICE" |
| **Badge Status:** | `DP/CICIL` (kuning) atau `LUNAS` (hijau) — menunjukkan status pembayaran. |
| **Info Invoice:** | Nomor invoice, tanggal dibuat, dan waktu pembuatan. |
| **Tombol Cetak:** | Ikon **🖨️** (pojok kanan atas) untuk mencetak nota gabungan. |
| **Label:** | "CETAK NOTA GABUNGAN" |

##### B. Data Pelanggan

Menampilkan informasi pelanggan:

| Field | Keterangan |
|-------|------------|
| **Nama** | Nama lengkap pelanggan (contoh: `JAGA GUNARTO`). |
| **Telepon** | Nomor telepon pelanggan (contoh: `6276774915704`). |
| **Ikon:** | Avatar pelanggan (🧑) |

##### C. Rincian Pesanan Terkait (SPK)

Bagian ini menampilkan daftar SPK yang tergabung dalam invoice:

| Komponen | Keterangan |
|----------|------------|
| **Nomor SPK** | Nomor SPK (contoh: `S-2607-18-0090-SW`). |
| **Gateway** | Kode gateway (contoh: `GATEWAY: SW`). |
| **SLA** | Waktu target pengerjaan (contoh: `SLA: 10 HARI`). |
| **Produk** | Merk dan model sepatu (contoh: `ADIDAS - ASDA`). |
| **Daftar Jasa** | Rincian jasa layanan beserta harga masing-masing. |
| **Subtotal SPK** | Total biaya per SPK (contoh: `Rp 1.225.000`). |

Pada daftar jasa, setiap item menampilkan:
- Nama jasa (contoh: `GANTI LINING KULIT`)
- Badge tipe: `✅ JASA REGULER` atau `➕ JASA TAMBAHAN`
- Harga per jasa

**Tombol Aksi pada SPK:**
| Tombol | Keterangan |
|--------|------------|
| **❌** (Merah) | Melepas/hapus SPK dari invoice (`DELETE /finance/invoices/{invoice}/unlink-spk/{workOrder}`). |
| **🖨️** (Abu) | Cetak rincian SPK individual. |
| **➕ TAMBAH SPK** | Menambahkan SPK lain ke invoice yang sudah ada. |

##### D. Rekapitulasi Keuangan (Panel Kanan)

Panel di sisi kanan menampilkan ringkasan keuangan:

| Komponen | Keterangan |
|----------|------------|
| **Total Harga Layanan** | Jumlah total seluruh jasa (contoh: `Rp 1.225.000`). |
| **Biaya Pengiriman Global** | Ongkos kirim (contoh: `Rp 10.000`). |
| **Total Tagihan** | Harga layanan + ongkir (contoh: `Rp 1.235.000`). |
| **Input/Ubah Ongkir** | Tombol **➕** untuk menambah atau mengubah biaya ongkir (`POST /finance/invoices/{invoice}/shipping`). |
| **Estimasi Selesai** | Tanggal target selesai dengan tombol edit (📋) (`POST /finance/invoices/{invoice}/estimasi`). |
| **Total Terbayar** | Jumlah total yang sudah dibayar (contoh: `Rp 156.998`). |
| **Sisa Tagihan Akhir** | Selisih Total Tagihan − Total Terbayar. Ditampilkan dalam warna **hijau** dengan detail perhitungan. |
| **Tombol:** | **📋 CATAT PEMBAYARAN** — membuka modal untuk mencatat pembayaran baru. |

##### E. Riwayat Pembayaran & Verifikasi Mutasi

Bagian ini menampilkan daftar pembayaran yang sudah tercatat:

| Komponen | Keterangan |
|----------|------------|
| **Jumlah Pembayaran** | Nominal pembayaran (contoh: `Rp 156.998`). |
| **Tanggal** | Tanggal dan waktu pembayaran. |
| **Dicatat Oleh** | Nama user yang mencatat pembayaran. |
| **Status Verifikasi** | `● OTOMATIS TERVERIFIKASI` (hijau) — jika sudah diverifikasi otomatis melalui pencocokan mutasi. |
| **Keterangan** | Informasi metode pembayaran (contoh: `Pembayaran via BCA [Auto Verified by Admin]`). |
| **Tombol Aksi** | Ikon ✏️ (edit) dan 🗑️ (hapus) untuk mengelola data pembayaran. |

##### F. Penagihan Elite & Link Sharing

Fitur ini menyediakan **link pembayaran** yang dapat dibagikan ke pelanggan:

| Termin | Keterangan |
|--------|------------|
| **Termin 1: Down Payment (70%)** | Jumlah DP yang harus dibayar, beserta kode unik. Tersedia tombol **📋 SALIN LINK DP 70%** dan ikon **WhatsApp** (🟢) untuk langsung mengirim ke pelanggan. |
| **Termin 2: Pelunasan Sisa** | Sisa pembayaran yang harus dilunasi, beserta kode unik. Tersedia tombol **📋 SALIN LINK PELUNASAN** dan ikon **WhatsApp** (🟢). |

##### G. Sistem Keamanan Saldo Gabungan

Informasi keamanan yang menjelaskan bahwa seluruh rincian harga dan status pembayaran disinkronkan secara real-time dengan data dari setiap Nomor SPK Terkait. Nota yang dicetak akan secara otomatis melampirkan rincian lengkap untuk pelanggan.

#### 4.3.3 Langkah-Langkah Penggunaan

1. Dari halaman **Data Invoice**, klik salah satu baris invoice untuk membuka rinciannya.
2. Periksa **Data Pelanggan** untuk memastikan data sudah benar.
3. Lihat **Rincian Pesanan Terkait** untuk melihat SPK mana saja yang tergabung.
4. Klik **➕ TAMBAH SPK** jika ingin menambahkan SPK lain ke invoice ini.
5. Klik **❌** pada SPK setelah rincian untuk melepasnya dari invoice.
6. Pada panel kanan, klik tombol **➕** di samping "Input/Ubah Ongkir" untuk mengisi biaya pengiriman.
7. Klik ikon **📋** di samping "Estimasi Selesai" untuk memperbarui tanggal estimasi.
8. Klik tombol **📋 CATAT PEMBAYARAN** untuk mencatat pembayaran baru (lihat sub-bab 4.4).
9. Pada bagian **Penagihan Elite & Link Sharing**, klik **📋 SALIN LINK** untuk menyalin link pembayaran, atau klik ikon **WhatsApp** untuk langsung mengirim ke pelanggan.
10. Klik ikon **🖨️** di pojok kanan atas untuk mencetak nota gabungan.

---

### 4.4 Modal Catat Pembayaran Invoice

![Catat Pembayaran Invoice](./Finance/06_Catat_Pembayaran_Invoice.png)

#### 4.4.1 Tujuan Fitur

Modal **Catat Pembayaran** digunakan untuk mencatat pembayaran yang diterima dari pelanggan terhadap sebuah invoice. Data pembayaran yang dicatat akan langsung memperbarui status invoice dan sisa tagihan secara real-time.

**Route:** `POST /finance/invoices/{invoice}/payment`

#### 4.4.2 Komponen Modal

| Komponen | Keterangan |
|----------|------------|
| **Judul:** | "CATAT PEMBAYARAN" |
| **Sub-judul:** | Nomor invoice terkait (contoh: `INV-260718-BE36`). |

##### Form Input:

| Field | Keterangan | Wajib |
|-------|------------|-------|
| **Jumlah Pembayaran** | Input nominal pembayaran dalam Rupiah. Di bawah field ditampilkan batas maksimal (contoh: `MAKSIMAL: RP 1.078.315`). | ✅ Ya |
| **Tanggal Bayar** | Date picker untuk memilih tanggal pembayaran dilakukan. | ✅ Ya |
| **Metode Bayar** | Dropdown untuk memilih metode pembayaran (contoh: `Transfer BCA`, `Cash`, dll). | ✅ Ya |
| **Tipe Pembayaran** | Dropdown untuk memilih tipe pembayaran (contoh: `Pelunasan Pesanan`, `DP Awal`, `Tambah Jasa`, dll). | ✅ Ya |
| **Bukti Bayar (Opsional)** | Area upload gambar bukti pembayaran. Format: PNG, JPG. Maksimal: 5MB. | ❌ Opsional |
| **Catatan Tambahan** | Textarea untuk catatan tambahan (contoh: `Cth: Titip DP via WA istri...`). | ❌ Opsional |

##### Tombol Aksi:

| Tombol | Keterangan |
|--------|------------|
| **BATAL** | Menutup modal tanpa menyimpan data. |
| **✅ SIMPAN PEMBAYARAN** | Menyimpan data pembayaran dan memperbarui invoice. |

#### 4.4.3 Langkah-Langkah Penggunaan

1. Dari halaman **Rincian Invoice**, klik tombol **📋 CATAT PEMBAYARAN**.
2. Masukkan **jumlah pembayaran** yang diterima. Perhatikan batas maksimal yang ditampilkan.
3. Pilih **tanggal bayar** menggunakan date picker.
4. Pilih **metode bayar** dari dropdown (contoh: Transfer BCA).
5. Pilih **tipe pembayaran** dari dropdown (contoh: Pelunasan Pesanan).
6. *(Opsional)* Upload **bukti bayar** dengan klik area upload atau drag & drop gambar.
7. *(Opsional)* Tambahkan **catatan tambahan** pada textarea.
8. Klik tombol **✅ SIMPAN PEMBAYARAN** untuk menyimpan.
9. Sistem akan memperbarui status invoice dan sisa tagihan secara otomatis.

#### 4.4.4 Istilah Penting pada Modul Data Invoice

| Istilah | Penjelasan |
|---------|------------|
| **Invoice Gabungan** | Satu tagihan yang menggabungkan beberapa SPK dari pelanggan yang sama. |
| **Gateway** | Kode jalur CS/sumber pesanan (contoh: SW = Shoe Workshop, BN = Budi Nusantara). |
| **SLA** | Service Level Agreement — waktu target penyelesaian pesanan. |
| **Jasa Reguler** | Layanan standar yang termasuk dalam paket pengerjaan. |
| **Jasa Tambahan** | Layanan ekstra yang ditambahkan di luar paket standar. |
| **Termin** | Tahapan pembayaran (Termin 1 = DP, Termin 2 = Pelunasan). |
| **Kode Unik** | Angka unik yang ditambahkan ke nominal pembayaran untuk identifikasi otomatis. |
| **Verifikasi Mutasi** | Proses pencocokan data pembayaran dengan mutasi bank untuk validasi otomatis. |
| **Nota Gabungan** | Dokumen cetak yang berisi rincian seluruh SPK dan pembayaran dalam satu invoice. |

---

## 5. Modul Transaksi Batal

![Transaksi Batal](./Finance/07_Transaksi_Batal.png)

### 5.1 Tujuan Fitur

**Laporan Transaksi Batal** adalah halaman analitik yang menampilkan data SPK (Surat Perintah Kerja) yang telah dibatalkan. Fitur ini membantu tim finance dalam menghitung estimasi kerugian keuangan, memantau rasio pembatalan, dan menganalisis pola pembatalan untuk meningkatkan operasional.

**Route:** `GET /finance/cancelled-orders`

### 5.2 Komponen Halaman

#### A. Header

- **Ikon:** Segitiga peringatan merah (⚠️)
- **Judul:** "Laporan Transaksi Batal"
- **Subtitle:** "ANALITIK KERUGIAN KEUANGAN & OPERASIONAL"
- **Tombol:** **⬅ KEMBALI KE TRANSAKSI** — navigasi kembali ke halaman transaksi utama.

#### B. Kartu Statistik (4 Kartu)

| Kartu | Label | Ikon | Keterangan |
|-------|-------|------|------------|
| 🟫 (Dark) | **Total Kerugian Keuangan** | 💰 | Estimasi nilai total transaksi yang terhenti akibat pembatalan. |
| ⬜ | **Total SPK Batal** | 📋 | Jumlah unit SPK yang dibatalkan oleh admin. |
| ⬜ | **Rata-rata Kerugian** | 📊 | Nilai rata-rata kerugian per SPK yang dibatalkan. |
| ⬜ | **Rasio Pembatalan** | 📈 | Persentase tingkat pembatalan terhadap seluruh order. |

#### C. Filter & Pencarian

| Komponen | Keterangan |
|----------|------------|
| **Cari Transaksi Batal** | Kolom pencarian dengan placeholder: "Cari No SPK, Nama, Telepon, atau Alasan Batal..." |
| **Tanggal Dari** | Date picker untuk memfilter mulai tanggal tertentu. |
| **Tanggal Sampai** | Date picker untuk memfilter hingga tanggal tertentu. |
| **Tombol FILTER** | Tombol untuk menerapkan filter pencarian dan rentang tanggal. |

#### D. Area Data

Jika tidak ada data transaksi batal, ditampilkan pesan:

> **OPERASIONAL AMAN**  
> *"TIDAK ADA TRANSAKSI BATAL ATAU KERUGIAN KEUANGAN TERDETEKSI PADA FILTER SAAT INI."*

Jika ada data, akan ditampilkan tabel SPK yang dibatalkan beserta rincian: nomor SPK, nama pelanggan, nilai transaksi, tanggal pembatalan, dan alasan batal.

### 5.3 Langkah-Langkah Penggunaan

1. Klik **Transaksi Batal** pada sidebar Divisi Finance.
2. Periksa **4 kartu statistik** di bagian atas untuk melihat ringkasan kerugian keuangan.
3. Gunakan **kolom pencarian** untuk mencari transaksi batal tertentu berdasarkan nomor SPK, nama pelanggan, nomor telepon, atau alasan batal.
4. Tentukan **rentang tanggal** menggunakan date picker "Tanggal Dari" dan "Tanggal Sampai".
5. Klik tombol **FILTER** untuk menerapkan filter.
6. Analisis data yang ditampilkan untuk keperluan evaluasi operasional.
7. Klik tombol **⬅ KEMBALI KE TRANSAKSI** untuk kembali ke halaman transaksi utama.

### 5.4 Istilah Penting

| Istilah | Penjelasan |
|---------|------------|
| **SPK Batal** | Surat Perintah Kerja yang telah dibatalkan oleh admin, biasanya karena pelanggan membatalkan pesanan. |
| **Total Kerugian Keuangan** | Estimasi total nilai transaksi yang hilang akibat pembatalan (berdasarkan `total_transaksi` SPK). |
| **Rata-rata Kerugian** | Total kerugian dibagi jumlah SPK batal — menunjukkan rata-rata nilai kerugian per pembatalan. |
| **Rasio Pembatalan** | Persentase SPK batal terhadap total keseluruhan SPK. Formula: (Jumlah Batal ÷ Total Semua SPK) × 100%. |
| **OPERASIONAL AMAN** | Indikator bahwa tidak ada transaksi batal yang terdeteksi pada filter yang dipilih. |

---

## 6. Modul Input Pembayaran

![Input Pembayaran](./Finance/08_Modul_Input_Pembayaran.png)

### 6.1 Tujuan Fitur

**Input Pembayaran** adalah halaman untuk mengelola dan menampilkan daftar seluruh pembayaran manual yang telah dicatat ke dalam sistem invoice. Halaman ini memungkinkan tim finance untuk melihat riwayat pembayaran, memfilter berdasarkan tanggal dan status, serta mencatat pembayaran baru.

**Route:** `GET /finance/payments`

### 6.2 Komponen Halaman

#### A. Header

| Komponen | Keterangan |
|----------|------------|
| **Ikon:** | Ikon koin/pembayaran (🪙) |
| **Badge:** | `RIWAYAT` |
| **Judul:** | "INPUT PEMBAYARAN" |
| **Subtitle:** | "DAFTAR PEMBAYARAN MANUAL INVOICE" |

#### B. Filter & Aksi

| Komponen | Keterangan |
|----------|------------|
| **Dari** | Date picker untuk filter tanggal mulai (format: `dd/mm/tttt`). |
| **Sampai** | Date picker untuk filter tanggal akhir (format: `dd/mm/tttt`). |
| **Semua Status** | Dropdown untuk memfilter berdasarkan status verifikasi (`Semua Status`, `Verified`, `Unverified`). |
| **🖨️** | Tombol cetak — mengekspor daftar pembayaran ke PDF (`GET /finance/payments/print`). |
| **🔍 No. Invoice...** | Kolom pencarian berdasarkan nomor invoice atau nama pelanggan. |
| **🟡 INPUT PEMBAYARAN +** | Tombol untuk membuka halaman pencatatan pembayaran baru (`GET /finance/payments/create`). |

#### C. Tabel Data Pembayaran

| Kolom | Keterangan |
|-------|------------|
| **No. Invoice** | Nomor invoice terkait beserta nama pelanggan di bawahnya. |
| **Jumlah Bayar** | Nominal pembayaran yang dicatat (contoh: `Rp 156.998`). |
| **Tanggal** | Tanggal pembayaran (contoh: `18 JUL 2026`). |
| **Status** | Badge status verifikasi: `● VERIFIED` (hijau) atau `● UNVERIFIED` (merah). |
| **Dicatat Oleh** | Nama user yang mencatat pembayaran (contoh: `ADMIN GUDANG`). |
| **Catatan** | Keterangan pembayaran (contoh: `Pembayaran via BCA [Auto Verified ...]`). |

### 6.3 Langkah-Langkah Penggunaan

1. Klik **Input Pembayaran** pada sidebar Divisi Finance.
2. Periksa daftar pembayaran yang sudah tercatat pada tabel.
3. Gunakan filter **Dari** dan **Sampai** untuk memfilter berdasarkan rentang tanggal.
4. Gunakan dropdown **Semua Status** untuk memfilter berdasarkan status verifikasi (Verified/Unverified).
5. Gunakan **kolom pencarian** untuk mencari pembayaran berdasarkan nomor invoice atau nama pelanggan.
6. Klik tombol **🖨️** untuk mencetak/export daftar pembayaran ke PDF.
7. Klik tombol **🟡 INPUT PEMBAYARAN +** untuk mencatat pembayaran baru.
8. Pada halaman input pembayaran baru:
   - Cari invoice berdasarkan nomor invoice.
   - Masukkan jumlah pembayaran, tanggal, metode, dan tipe pembayaran.
   - *(Opsional)* Upload bukti bayar dan tambahkan catatan.
   - Klik tombol simpan untuk mencatat pembayaran.

### 6.4 Istilah Penting

| Istilah | Penjelasan |
|---------|------------|
| **Pembayaran Manual** | Pembayaran yang dicatat secara manual oleh tim finance ke dalam sistem (bukan otomatis dari payment gateway). |
| **Verified** | Status yang menandakan pembayaran sudah terverifikasi — baik secara otomatis (melalui pencocokan mutasi bank) maupun manual oleh admin. |
| **Unverified** | Status pembayaran yang belum diverifikasi — masih menunggu konfirmasi atau pencocokan dengan mutasi bank. |
| **Auto Verified** | Pembayaran yang diverifikasi secara otomatis oleh sistem setelah berhasil dicocokkan dengan data mutasi bank. |
| **Dicatat Oleh** | User yang melakukan pencatatan pembayaran ke dalam sistem. |

---

## Lampiran: Daftar Route Finance

| Method | Route | Keterangan |
|--------|-------|------------|
| GET | `/finance/dashboard` | Dashboard Finance (Livewire) |
| GET | `/finance/dashboard/export-pdf` | Export dashboard ke PDF |
| GET | `/finance/waiting-payment` | Waiting Payment (Livewire) |
| GET | `/finance/invoices` | Daftar semua invoice gabungan |
| GET | `/finance/invoices/create` | Form buat invoice gabungan baru |
| POST | `/finance/invoices/store` | Simpan invoice gabungan baru |
| GET | `/finance/invoices/{invoice}` | Detail/rincian invoice |
| POST | `/finance/invoices/{invoice}/payment` | Catat pembayaran pada invoice |
| POST | `/finance/invoices/{invoice}/shipping` | Update ongkir pada invoice |
| POST | `/finance/invoices/{invoice}/estimasi` | Update estimasi selesai |
| DELETE | `/finance/invoices/{invoice}` | Hapus invoice |
| DELETE | `/finance/invoices/{invoice}/unlink-spk/{workOrder}` | Lepas SPK dari invoice |
| DELETE | `/finance/invoice-payments/{payment}` | Hapus data pembayaran |
| PUT | `/finance/invoice-payments/{payment}` | Edit data pembayaran |
| GET | `/finance/cancelled-orders` | Laporan transaksi batal |
| GET | `/finance/payments` | Daftar pembayaran manual |
| GET | `/finance/payments/print` | Cetak laporan pembayaran |
| GET | `/finance/payments/create` | Form input pembayaran baru |
| POST | `/finance/payments/store` | Simpan pembayaran baru |

---

> **📌 Catatan Akhir:**  
> Dokumen ini disusun berdasarkan tampilan antarmuka aplikasi Shoe Workshop per Juli 2026. Fitur dan tampilan dapat berubah sesuai pembaruan sistem. Untuk pertanyaan atau dukungan teknis, silakan hubungi tim IT atau administrator sistem.

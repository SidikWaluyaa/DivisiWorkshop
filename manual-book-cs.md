# 📘 User Manual Book — Divisi Customer Service (CS)
## Sistem Shoe Workshop

**Versi Dokumen:** 1.0  
**Tanggal:** 27 Juli 2026  
**Disusun oleh:** Tim Pengembang Shoe Workshop

---

## Daftar Isi

1. [Pendahuluan & Peran Pengguna](#1-pendahuluan--peran-pengguna)
2. [Modul CS Dashboard (Sales Pipeline Monitoring)](#2-modul-cs-dashboard-sales-pipeline-monitoring)
   - [2.1 Dashboard Utama CS](#21-dashboard-utama-cs)
   - [2.2 Registrasi Lead Baru](#22-registrasi-lead-baru)
   - [2.3 Tahap Greeting (Penyambutan Klien)](#23-tahap-greeting-penyambutan-klien)
   - [2.4 Tahap Konsultasi & Pembuatan Penawaran Awal](#24-tahap-konsultasi--pembuatan-penawaran-awal)
   - [2.5 Detail Penawaran & Pemindahan Pipeline](#25-detail-penawaran--pemindahan-pipeline)
   - [2.6 Tahap Closing (Persetujuan Akhir)](#26-tahap-closing-persetujuan-akhir)
   - [2.7 Konfirmasi & Penerbitan SPK](#27-konfirmasi--penerbitan-spk)
   - [2.8 Panel Detail SPK](#28-panel-detail-spk)
   - [2.9 Serah Terima ke Workshop](#29-serah-terima-ke-workshop)
   - [2.10 Status Converted (Selesai Konversi)](#210-status-converted-selesai-konversi)
   - [2.11 Pemantauan SPK Pending (Gudang Penerimaan)](#211-pemantauan-spk-pending-gudang-penerimaan)
3. [Modul Laporan Performa (CS Performance Analytics)](#3-modul-laporan-performa-cs-performance-analytics)
4. [Modul Followup Closing (Gudang QC Reject Handling)](#4-modul-followup-closing-gudang-qc-reject-handling)
5. [Modul Gallery Before After](#5-modul-gallery-before-after)

---

## 1. Pendahuluan & Peran Pengguna

### 1.1 Tentang Dokumen Ini

Dokumen ini merupakan panduan operasional bagi divisi **Customer Service (CS)** pada sistem **Shoe Workshop**. Buku manual ini dirancang untuk membimbing petugas CS dalam mengelola prospek pelanggan (*leads*), memberikan konsultasi dan penawaran harga, menerbitkan Surat Perintah Kerja (SPK), hingga memantau statistik performa penjualan.

### 1.2 Alur Kerja Pipeline CS (End-to-End)

Divisi CS bekerja menggunakan sistem **Sales Pipeline Kanban** yang terbagi menjadi empat tahap utama sebelum sebuah pesanan resmi dikonversi menjadi pekerjaan produksi di workshop:

```
[Lead Baru] ➔ 1. Greeting ➔ 2. Konsultasi ➔ 3. Follow-up ➔ 4. Closing ➔ [Terbit SPK] ➔ [Workshop]
```

1. **Greeting:** Menerima pesan pertama pelanggan dari website, WhatsApp, atau media sosial lainnya.
2. **Konsultasi:** Mengidentifikasi keluhan sepatu, merekomendasikan jenis layanan, dan membuat estimasi penawaran harga.
3. **Follow-up:** Menindaklanjuti penawaran yang sudah dikirimkan agar pelanggan melakukan pembayaran uang muka (DP) atau pelunasan.
4. **Closing:** Pelanggan sepakat untuk memproses pengerjaan sepatu dan siap untuk dibuatkan SPK (Surat Perintah Kerja).

---

## 2. Modul CS Dashboard (Sales Pipeline Monitoring)

### 2.1 Dashboard Utama CS

![CS Dashboard Utama](./CS/01_cs_dashboard.jpeg)

**Tujuan:** Halaman utama **CS Hub** ini berfungsi sebagai pusat pemantauan seluruh tahapan prospek (*sales pipeline monitoring*) secara visual menggunakan kolom Kanban.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **New Leads Today** | Jumlah prospek baru yang masuk pada hari ini |
| **Hot Potential** | Jumlah prospek potensial (*Hot Leads*) yang memerlukan penanganan cepat |
| **Closing Today** | Jumlah pesanan yang berhasil mencapai kata sepakat (*deal*) hari ini |
| **Conversion Rate** | Persentase perbandingan antara *leads* yang masuk dengan yang sukses menjadi SPK |
| **Kolom Pencarian** | Mencari nama pelanggan atau nomor telepon |
| **Tombol Filter Khusus** | Menyaring data berdasarkan *Lost Leads*, *Hot Leads*, *Overdue*, atau tombol *Reset* |
| **Kolom Greeting** | Daftar kandidat prospek baru yang belum dihubungi lebih lanjut |
| **Kolom Konsultasi** | Daftar kandidat yang sedang berkonsultasi mengenai perbaikan sepatu |
| **Kolom Follow-up** | Daftar kandidat yang sudah diberi penawaran harga dan sedang dipantau responnya |
| **Kolom Closing** | Daftar kandidat yang sudah setuju (*deal*) dan siap diterbitkan nomor SPK-nya |

**Langkah-Langkah Menggunakan Dashboard:**

1. Pada sidebar kiri sistem, klik menu **CS Dashboard** di bawah bagian *Divisi CS*.
2. Dashboard utama akan tampil dengan empat kolom Kanban: *Greeting*, *Konsultasi*, *Follow-up*, dan *Closing*.
3. Anda dapat mencari nama pelanggan tertentu dengan mengetik di kolom **Cari Nama/HP Customer...**.
4. Klik tombol **Hot Leads** untuk memunculkan prospek dengan prioritas tinggi saja.
5. Klik kartu nama pelanggan di dalam kolom Kanban untuk masuk ke detail profil dan memproses alur kerja selanjutnya.

---

### 2.2 Registrasi Lead Baru

![Form Lead Baru](./CS/02_lead_baru.jpeg)

**Tujuan:** Dialog pop-up ini digunakan oleh petugas CS untuk mendaftarkan data profil pelanggan baru ke dalam sistem saat pertama kali pelanggan melakukan kontak.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Nama Customer** | Nama lengkap pelanggan (wajib diisi) |
| **No. Telepon** | Nomor telepon/WhatsApp aktif pelanggan |
| **Email** | Alamat surat elektronik pelanggan |
| **Pintasan Domain Email** | Tombol cepat `@gmail.com`, `@yahoo.com`, `@outlook.com` untuk mempercepat pengetikan |
| **Sumber Lead** | Dropdown asal masuknya prospek (contoh: WhatsApp, Website, Instagram, dll.) |
| **Tipe Lead** | Dropdown tipe komunikasi (Online / Offline) |
| **Prioritas** | Tingkat kedaruratan tindak lanjut: **HOT** (merah), **WARM** (kuning), atau **COLD** (biru) |
| **Catatan Awal** | Kolom untuk menuliskan pesan awal atau keluhan umum pelanggan |

**Langkah-Langkah Mendaftarkan Lead Baru:**

1. Klik tombol hijau **+ LEAD BARU** di bagian atas halaman CS Hub.
2. Dialog **LEAD BARU: TAMBAH DATA CUSTOMER** akan muncul.
3. Masukkan **Nama Lengkap Customer**.
4. Masukkan **No. Telepon** dan **Email** pelanggan (gunakan tombol pintasan domain email untuk mempermudah).
5. Pilih **Sumber Lead** dan **Tipe Lead** yang sesuai.
6. Tentukan tingkat **Prioritas** (pilih *HOT* jika pelanggan ingin pengerjaan segera).
7. Tulis keluhan awal sepatu mereka di kolom **Catatan Awal**.
8. Gulir ke bawah dan klik tombol **Simpan Data** untuk mendaftarkan prospek ke sistem. Data baru ini otomatis masuk ke kolom *Greeting*.

---

### 2.3 Tahap Greeting (Penyambutan Klien)

![Detail Greeting](./CS/03_Greeting.jpeg)

**Tujuan:** Halaman ini digunakan untuk menginisiasi percakapan awal dengan pelanggan yang baru masuk di kolom *Greeting*, memverifikasi alamat mereka, dan memulai langkah konsultasi.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Mulai Konsultasi** | Tombol untuk memindahkan status *leads* dari tahap *Greeting* ke *Konsultasi* |
| **Buat Penawaran** | Tombol di kanan atas untuk membuat rancangan harga perbaikan |
| **Detail Profil** | Rangkuman informasi kontak, prioritas, dan nama CS penanggung jawab (*CS Handler*) |
| **Informasi Pengiriman** | Form pengisian alamat lengkap pelanggan untuk keperluan logistik kirim balik |
| **Aktivitas Terakhir** | Kolom log riwayat percakapan atau janji temu dengan pelanggan |

**Langkah-Langkah Memproses Tahap Greeting:**

1. Klik kartu nama pelanggan yang berada di kolom **Greeting** pada dashboard utama.
2. Anda akan diarahkan ke halaman detail *Lead Management Console*.
3. Hubungi pelanggan via WhatsApp dengan nomor telepon yang tertera di panel **Detail Profil**.
4. Lengkapi data alamat pelanggan di kolom **Informasi Pengiriman** (masukkan Alamat Lengkap, Kota, dan Provinsi), kemudian klik **✓ Simpan Alamat**.
5. Jika sudah melakukan kontak awal, klik tombol hijau **Mulai Konsultasi** pada kolom kiri.
6. Status *leads* akan berpindah ke kolom *Konsultasi*. Selanjutnya, klik **Mulai Penawaran →** di sisi kanan untuk menginput detail sepatu.

---

### 2.4 Tahap Konsultasi & Pembuatan Penawaran Awal

![Mulai Penawaran](./CS/04_mulai_penawaran.jpeg)

**Tujuan:** Form dialog ini digunakan untuk mencatat detail fisik sepatu pelanggan serta jenis layanan perbaikan yang akan diajukan ke pelanggan selama masa konsultasi.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Pindah ke Follow Up** | Memindahkan prospek ke tahap tindak lanjut setelah penawaran dikirim |
| **Kategori Item (Rumus SPK)** | Pilihan jenis barang (Sepatu, Tas, Topi, Aksesoris, dll.) |
| **Brand** | Merk sepatu pelanggan |
| **Model / Tipe** | Tipe sepatu pelanggan |
| **Warna / Ukuran** | Detail warna dominan dan ukuran sepatu |

**Langkah-Langkah Membuat Penawaran:**

1. Di halaman detail konsultasi pelanggan, klik tombol **Mulai Penawaran →** di panel kanan.
2. Dialog **Mulai Penawaran** akan terbuka.
3. Tentukan **Kategori Item** (contoh: pilih *Sepatu (Prefix S)*).
4. Masukkan **Brand** (merk), **Model/Tipe**, **Warna**, dan **Ukuran** sepatu pelanggan.
5. Klik **Lanjut ke Detail Layanan** untuk menentukan jasa pembersihan/reparasi apa saja yang diambil.
6. Setelah selesai, simpan penawaran tersebut.

---

### 2.5 Detail Penawaran & Pemindahan Pipeline

![Detail Penawaran](./CS/05_simpanpenawaran_konsultasi.jpeg)

**Tujuan:** Menampilkan hasil estimasi harga sementara (*draft quotation*) dari sepatu yang telah diinput di tahap sebelumnya, dan menyediakan opsi pemindahan status ke tahap *Follow-up* atau langsung ke *Closing*.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Kartu Sepatu** | Info visual mengenai merk sepatu (contoh: Nike AF1, warna: Hitam, bagian pengerjaan: Toe Cap) |
| **Estimasi Durasi** | Jumlah hari kerja yang dihitung sistem (contoh: 10 HK) |
| **Status Garansi** | Menandai kelayakan garansi hasil reparasi |
| **Estimasi Nilai** | Total biaya yang diajukan ke pelanggan (contoh: Rp 75.000) |
| **Siap Closing** | Tombol untuk memindahkan prospek ke tahap closing jika pelanggan setuju dengan harga |
| **Pindah ke Follow Up** | Tombol untuk memindahkan prospek ke tahap tindak lanjut jika pelanggan masih ragu-ragu |

**Langkah-Langkah Mengirim Penawaran & Update Pipeline:**

1. Periksa rincian item sepatu dan estimasi harga di bagian kanan layar.
2. Kirim rincian estimasi biaya tersebut kepada pelanggan melalui nomor WhatsApp mereka.
3. **Jika pelanggan belum memberikan keputusan:** Klik tombol **Pindah ke Follow Up** agar data bergeser ke kolom pemantauan harian.
4. **Jika pelanggan langsung menyetujui harga:** Klik tombol biru **Siap Closing**.

---

### 2.6 Tahap Closing (Persetujuan Akhir)

![Terbitkan SPK](./CS/06_closing.jpeg)

**Tujuan:** Tahap ini digunakan ketika pelanggan sudah menyetujui detail penawaran serta estimasi harga, sehingga sistem siap untuk membekukan penawaran dan mencetak Surat Perintah Kerja (SPK).

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Terbitkan SPK** | Tombol hijau di panel kiri untuk memulai proses finalisasi SPK |
| **Terbitkan SPK (Kanan Atas)** | Tombol alternatif di bar header atas untuk mengonfirmasi penerbitan SPK |

**Langkah-Langkah Memproses Closing:**

1. Pada dashboard utama, masuk ke kolom **Closing** dan klik nama pelanggan yang telah disetujui.
2. Halaman detail konfirmasi closing akan terbuka.
3. Lakukan konfirmasi ulang seluruh detail sepatu dan rincian biaya kepada pelanggan.
4. Jika sudah benar-benar sesuai, klik tombol hijau **Terbitkan SPK** di panel sebelah kiri atau kanan atas.

---

### 2.7 Konfirmasi & Penerbitan SPK

![Dialog Terbitkan SPK](./CS/07_terbitkan_spk.jpeg)

**Tujuan:** Dialog konfirmasi akhir sebelum nomor SPK resmi diterbitkan. Sistem menampilkan pratinjau format nomor SPK (contoh: `F-2607-27-0092-CS`).

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Finalisasi SPK** | Judul form konfirmasi akhir |
| **Pratinjau Nomor SPK** | Kode unik SPK yang digenerate sistem berdasarkan tanggal dan divisi |
| **Batal** | Tombol untuk membatalkan proses penerbitan |

**Langkah-Langkah Menerbitkan SPK:**

1. Setelah tombol *Terbitkan SPK* ditekan, dialog **Finalisasi SPK** akan muncul.
2. Baca dan pastikan nomor pratinjau SPK sudah tertera dengan benar.
3. Klik tombol **Terbitkan & Konfirmasi** (atau tekan tombol hijau pada dialog) untuk meresmikan SPK.
4. Sistem akan mengubah status prospek dari *Closing* menjadi *Converted* (sukses dikonversi).

---

### 2.8 Panel Detail SPK

![Detail SPK Aktif](./CS/08_setelah_terbitkan_spk.jpeg)

**Tujuan:** Menampilkan dokumen SPK resmi yang telah diterbitkan secara lengkap. Halaman ini memuat rincian pembayaran, target selesai, opsi metode pengiriman balik, serta tombol untuk menyerahkan sepatu secara fisik ke tim workshop.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Badge SPK Aktif** | Indikator hijau di kanan atas bertuliskan nomor SPK resmi (contoh: `#F-2607-27-0092-SW`) |
| **Master SPK** | Nomor identitas SPK pada panel utama |
| **Status Workshop** | Status pergerakan fisik barang (contoh: *Dalam Antrian Menunggu Teknisi*) |
| **Serahkan ke Workshop** | Tombol hijau besar untuk mencatat serah terima sepatu ke area produksi |
| **Logika Pengiriman** | Metode pengiriman balik ke kustomer (contoh: Offline / Kurir) |
| **Jalur Prioritas** | Kategori prioritas penanganan (Normal / High Priority) |
| **Rincian Barang Produksi** | Informasi sepatu, warna, estimasi durasi (HK), dan area bagian sepatu (contoh: Toe Cap) |
| **Rincian Pembayaran SPK** | Detail keuangan (Total Sebelum Diskon, Diskon, Netto, dan Total Transaksi) |

**Langkah-Langkah Membaca Detail SPK:**

1. Setelah SPK terbit, layar otomatis berpindah ke tab **Detail SPK**.
2. Anda dapat melihat status pembayaran pelanggan dan status antrian teknisi di workshop.
3. Tunjukkan atau kirimkan salinan struk/nomor SPK ini kepada pelanggan sebagai tanda bukti penerimaan barang.
4. Klik tombol **Serahkan ke Workshop** untuk mengirim data fisik sepatu ini ke sistem antrian produksi.

---

### 2.9 Serah Terima ke Workshop

![Dialog Serah Terima Workshop](./CS/09_serahkan_ke_workshop.jpeg)

**Tujuan:** Dialog ini digunakan untuk mendokumentasikan kondisi fisik sepatu secara mendalam sebelum resmi masuk ke ruang produksi workshop. Ini penting untuk mengonfirmasi bahwa brand dan model sepatu sudah sesuai secara visual.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Brand Konfirmasi** | Kolom untuk mengetik ulang nama merk guna konfirmasi (contoh: Nike) |
| **Model Konfirmasi** | Kolom untuk mengetik ulang model sepatu (contoh: AF1) |
| **Foto Referensi (Opsional)** | Tombol untuk mengunggah foto kondisi fisik sepatu sesaat sebelum masuk workshop |
| **Jenis Item (Prefix SPK)** | Dropdown untuk memilih format penomoran SPK |
| **Konfirmasi & Kirim ke Produksi** | Tombol hijau untuk menyelesaikan serah terima |

**Langkah-Langkah Serah Terima:**

1. Ketika dialog **Serah Terima Workshop** terbuka, ketikkan nama brand pada kolom **Brand Konfirmasi**.
2. Ketikkan nama model pada kolom **Model Konfirmasi**.
3. Klik tombol **+ Tambah Foto** untuk mengunggah dokumentasi foto fisik sepatu sebelum dikerjakan.
4. Pilih **Jenis Item (Prefix SPK)** dari dropdown.
5. Klik tombol **Konfirmasi & Kirim ke Produksi**. Sepatu sekarang resmi dalam tanggung jawab teknisi workshop.

---

### 2.10 Status Converted (Selesai Konversi)

![Detail Converted](./CS/10_converted.jpeg)

**Tujuan:** Menampilkan status akhir dari proses administrasi CS. Badge di bagian atas *leads* kini telah berganti menjadi warna hijau bertuliskan **CONVERTED**, menandakan seluruh rangkaian pendaftaran, konsultasi, penawaran, dan penyerahan ke workshop telah selesai dengan sempurna.

**Langkah-Langkah Monitoring Status Converted:**

1. Masuk ke halaman **CS Dashboard** ➡️ Tab *Closing* atau gunakan kolom pencarian.
2. Cari nama pelanggan terkait. Status mereka di header detail kini bertuliskan **CONVERTED**.
3. Di tahap ini, CS tinggal memantau perkembangan pengerjaan sepatu pelanggan pada sistem pelacakan internal (*Internal Tracking*).

---

### 2.11 Pemantauan SPK Pending (Gudang Penerimaan)

![Daftar SPK Pending](./CS/11_spk_pending.jpeg)

**Tujuan:** Digunakan oleh CS untuk memantau status sepatu pelanggan yang data SPK-nya sudah diterbitkan oleh CS, namun sepatu fisiknya belum diserahkan atau masih dalam proses logistik menuju gudang penerimaan utama (*Pending Warehouse*).

**Langkah-Langkah Monitoring SPK Pending:**

1. Klik menu **Data SPK** atau masuk ke menu penerimaan barang.
2. Pada tabel penerimaan, perhatikan daftar baris SPK.
3. Item dengan tombol kuning bertuliskan **TERIMA BARANG →** menandakan sepatu fisik belum dipindai atau belum diterima oleh staf gudang.
4. Lakukan koordinasi dengan kurir atau staf gudang jika sepatu pelanggan belum berubah statusnya menjadi *Diterima* setelah beberapa hari.

---

## 3. Modul Laporan Performa (CS Performance Analytics)

![Laporan Performa CS](./CS/12_laporan_performa.jpeg)

**Tujuan:** Menyediakan visualisasi data analitik dan matriks penilaian kinerja (*KPI*) bagi seluruh tim Customer Service. Modul ini melacak efektivitas konversi prospek, kecepatan respon obrolan, analisis prospek gagal (*lost analysis*), serta peringkat performa CS.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Filter Periode Laporan** | Mengatur rentang tanggal data laporan (Start Date - End Date) |
| **Global Overview Metrics** | Ringkasan statistik utama: **Total Lead Intake** (total prospek masuk), **Total Closing** (jumlah deal), **Total Sepatu Masuk**, **Revenue Realization** (omset closing valid), dan **Avg Deal Value** (rata-rata nilai deal) |
| **Closing Path Analysis** | Analisis jalur deal: persentase closing langsung atau closing melalui proses follow-up |
| **Pipeline Funnel** | Grafik corong distribusi prospek berdasarkan tahapan (*Greeting, Konsultasi, Follow-up, Closing, Converted, Lost*) |
| **Response Time Analytics** | Mengukur kecepatan balas pesan: rata-rata kecepatan respon CS (dalam menit) |
| **CS KPI Leaderboard** | Peringkat individu petugas CS berdasarkan total intake, closing, jumlah sepatu, dan nilai omset penjualan |

**Langkah-Langkah Membaca Laporan Performa:**

1. Pada sidebar kiri sistem, klik menu **Laporan Performa** di bawah bagian *Divisi CS*.
2. Atur tanggal mulai dan tanggal akhir pada kolom **Periode Laporan**, lalu klik **Update**.
3. Baca kartu **Global Overview Metrics** untuk mengetahui pencapaian omset dan total closing periode tersebut.
4. Perhatikan grafik **Pipeline Funnel** untuk melihat di bagian tahapan mana prospek paling banyak berhenti/gagal.
5. Periksa **CS KPI Leaderboard** di bagian bawah untuk mengevaluasi kinerja masing-masing personel CS.

---

## 4. Modul Followup Closing (Gudang QC Reject Handling)

![Halaman Followup Closing](./CS/13_followup_closing.jpeg)

**Tujuan:** Modul ini sangat penting untuk menangani kasus di mana sepatu pelanggan **gagal lolos pemeriksaan kualitas awal (QC Reject)** saat pertama kali diperiksa oleh tim gudang. Halaman ini membantu CS menghubungi kustomer kembali untuk menawarkan layanan tambahan/perbaikan alternatif, melanjutkan proses, atau membatalkan pesanan.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Informasi SPK** | Menampilkan estimasi selesai, tanggal reject, dan nomor SPK (warna kuning) |
| **Data Pelanggan** | Nama pelanggan, nomor WhatsApp, merk sepatu, dan CS penanggung jawab |
| **Detail Penolakan Gudang** | Log alasan penolakan awal oleh admin gudang |
| **Catatan Kerusakan** | Foto dan detail bagian fisik yang rusak (contoh: Midsole & Outsole Jelek) |
| **Rekomendasi Tambahan** | Usulan jenis reparasi tambahan dari tim gudang (contoh: Lem Jahit Reguler Rp 190.000) |
| **Lanjutkan ke Assessment** | Tombol hijau untuk memaksa lanjut pengerjaan meskipun gagal QC |
| **Input Tambah Jasa (Upsell)** | Tombol kuning untuk menambahkan jasa reparasi baru berdasarkan rekomendasi gudang |
| **Batal / Cancel SPK** | Tombol merah untuk membatalkan transaksi secara keseluruhan |

**Langkah-Langkah Menangani Kasus QC Reject:**

1. Pada sidebar kiri, klik menu **Follow Up Closing**. Angka oranye pada menu menunjukkan adanya antrian kasus reject baru.
2. Cari dan temukan SPK pelanggan yang bermasalah.
3. Baca **Detail Penolakan Gudang** dan **Catatan Kerusakan** untuk memahami apa yang rusak pada sepatu.
4. Klik tombol **Hubungi via WA** untuk berbicara langsung dengan pelanggan. Jelaskan kerusakan yang ditemukan oleh tim gudang.
5. **Jika pelanggan setuju menambah perbaikan:** Klik **+ Input Tambah Jasa (Upsell)**, masukkan jenis jasa tambahan sesuai usulan gudang, lalu perbarui tagihan.
6. **Jika pelanggan bersikeras ingin lanjut pengerjaan awal saja:** Klik **Lanjutkan ke Assessment** untuk mengirim kembali sepatu ke ruang workshop.
7. **Jika pelanggan memutuskan membatalkan transaksi:** Klik **✕ Batal / Cancel SPK**.

---

## 5. Modul Gallery Before After

![Galeri Before After](./CS/14_gallery_before_after.jpeg)

**Tujuan:** Galeri ini menampilkan perbandingan visual foto sepatu sebelum (*Before*) dan sesudah (*After*) dilakukan proses pembersihan atau perbaikan. Galeri ini digunakan oleh CS sebagai bukti kualitas hasil pengerjaan kepada pelanggan atau bahan promosi media sosial.

**Komponen Utama di Layar:**

| Komponen | Keterangan |
|---|---|
| **Kolom Pencarian** | Mencari foto berdasarkan nomor SPK, nama pelanggan, atau jenis layanan |
| **Semua Jasa** | Dropdown untuk menyaring foto berdasarkan kategori jasa tertentu |
| **Merek Sepatu** | Dropdown filter berdasarkan merk sepatu |
| **Pilih Tanggal Selesai** | Membatasi tampilan foto berdasarkan tanggal penyelesaian |
| **Kartu Galeri** | Berisi info SPK, nama pelanggan, jenis layanan, tanggal selesai, serta perbandingan foto berdampingan |
| **Tombol Unduh** | Tombol di kanan bawah kartu galeri untuk mengunduh file foto ke perangkat |

**Langkah-Langkah Menggunakan Galeri Before After:**

1. Pada sidebar kiri sistem, klik menu **Galeri After Photo** atau **Galeri Before After**.
2. Galeri foto perbandingan akan terbuka.
3. Gunakan filter **Semua Jasa** atau **Merek Sepatu** untuk mencari jenis pengerjaan tertentu (misal: cari *Fast Clean* pada brand *Nike*).
4. Klik tombol **Unduh** (ikon panah bawah) di pojok kanan bawah kartu foto untuk menyimpan gambar ke komputer/HP Anda.
5. Kirimkan foto perbandingan tersebut kepada pelanggan melalui WhatsApp untuk menunjukkan bukti kualitas pengerjaan tim Shoe Workshop.

---

## Lampiran

### A. Panduan Singkat Status Prospek (Leads)

| Status | Makna | Tindakan CS |
|---|---|---|
| **GREETING** | Prospek baru masuk sistem | Hubungi kustomer dan sapa dengan sopan. |
| **KONSULTASI** | Sedang dalam proses tanya jawab | Catat detail sepatu dan buatkan draf penawaran harga. |
| **FOLLOW-UP** | Penawaran harga sudah dikirim | Tanya kelanjutan keputusan kustomer secara berkala. |
| **CLOSING** | Kustomer setuju transaksi | Siapkan administrasi untuk pembuatan nomor SPK. |
| **CONVERTED** | SPK telah resmi diterbitkan | Serahkan fisik sepatu ke tim workshop. |
| **LOST** | Transaksi dibatalkan | Isi alasan pembatalan untuk bahan evaluasi. |

---

*Dokumen ini bersifat rahasia dan hanya ditujukan untuk penggunaan internal Shoe Workshop.*  
*© 2026 Shoe Workshop — Seluruh Hak Dilindungi.*

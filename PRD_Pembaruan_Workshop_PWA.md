# DOKUMEN PERSYARATAN PRODUK (PRD)
## PEMBARUAN SISTEM WORKSHOP PWA & REKAYASA ULANG MANAJEMEN LOGISTIK
**Versi:** 1.0  
**Tanggal:** 4 Agustus 2026  
**Status:** Draf Diskusi  
**Penulis:** Sidik Waluya 

---

## 1. PENDAHULUAN & LATAR BELAKANG
Sistem manajemen Shoe Workshop saat ini menyatukan semua modul (CS, Gudang, Keuangan, dan Workshop) dalam satu layout dashboard yang sama. Seiring berkembangnya operasional, Divisi Workshop (Bengkel Kerja) membutuhkan sistem kerja yang lebih gesit (*agile*), ramah perangkat seluler (*mobile-friendly*), dan memiliki kontrol yang lebih ketat terhadap bahan baku serta spesialisasi teknisi.

Pembaruan ini bertujuan untuk:
1. **Memisahkan Layout & Sidebar Workshop:** Membuat tampilan khusus berbasis Progressive Web App (PWA) agar para teknisi dan admin workshop dapat bekerja lebih fokus menggunakan smartphone atau tablet di area kerja bengkel.
2. **Mengetatkan Kontrol Bahan Baku & Teknisi:** Menghilangkan penugasan manual yang tidak terstruktur, mendeteksi ketersediaan bahan baku sejak awal, serta mengotomatisasi daftar belanja bahan jika stok kosong.
3. **Menerapkan Validasi Manifest Ganda:** Menggunakan Manifest Inbound (barang masuk) dan Manifest Outbound (barang keluar) sebagai acuan performa bulanan dan keamanan serah terima barang.

---

## 2. PENGGUNA SISTEM (USER PERSONA)
1. **Admin Workshop (WS):** Pengendali utama operasional bengkel yang bertanggung jawab menyetujui barang masuk (ACC), menjadwalkan teknisi, serta sepenuhnya mengelola inventaris material workshop (Stok Material, Belanja/Pembelian, Barang Keluar, dan Riwayat Mutasi).
2. **Teknisi Workshop:** Pekerja lapangan yang melakukan pengerjaan fisik sepatu (Cuci, Jahit, Repaint, dll) berdasarkan spesialisasi jasa masing-masing yang telah dipetakan oleh Admin WS.

---

## 3. FITUR UTAMA & DIAGRAM ALUR (WORKFLOW)

### Fitur 1: Spesialisasi Jasa Teknisi (Technician-Service Mapping)
* **Deskripsi:** Setiap akun Teknisi wajib dikaitkan dengan jenis jasa perbaikan tertentu yang mereka kuasai (misalnya: Teknisi A hanya terhubung ke jasa "Repaint", Teknisi B ke jasa "Reparasi Sol").
* **Aturan Bisnis:**
  - Di halaman kelola pengguna, Admin WS dapat memilih daftar jasa untuk setiap user ber-role Teknisi.
  - Saat pembagian tugas SPK di Workshop, sistem hanya akan memunculkan nama teknisi yang spesialisasinya cocok dengan jasa pada SPK tersebut.

---

### Fitur 2: Alur Penerimaan Barang (Inbound Manifest & Halaman ACC)
* **Alur Lama:** Manifest dikirim dari gudang/toko -> Diterima -> SPK langsung masuk ke tahap Persiapan (Prep).
* **Alur Baru (Inbound Manifest):**
  1. Toko/Gudang mengirim Manifest.
  2. Begitu kurir tiba di Workshop, Manifest ditandai sebagai **Diterima**.
  3. **TETAPI**, SPK tidak langsung masuk ke halaman Persiapan (Prep). Semua SPK di dalam manifest tersebut ditahan di satu halaman staging baru: **"Halaman Tunggu ACC Admin WS"**.
  4. Admin WS membuka halaman ini untuk melakukan pengecekan fisik sepatu, kemudian menginput:
     - **Bahan Baku (Material)** yang akan digunakan untuk perbaikan tersebut.
     - **Teknisi** yang ditugaskan (sistem otomatis memfilter teknisi yang kompeten berdasarkan Jasa SPK).

---

### Fitur 3: Validasi Stok Bahan & Otomatisasi Daftar Belanja oleh Workshop
* **Deskripsi:** Menghentikan proses pengerjaan jika bahan baku di bengkel tidak mencukupi, untuk mencegah SPK mangkrak di tengah jalan. Pengelolaan belanja dilakukan sepenuhnya oleh internal Workshop.
* **Aturan Bisnis:**
  - Ketika Admin WS memilih bahan baku di halaman ACC:
    - **Skenario A (Bahan Tersedia):** SPK langsung disetujui (ACC) dan otomatis masuk ke halaman **Persiapan (Prep)** untuk mulai dikerjakan.
    - **Skenario B (Bahan Kosong/Kurang):** SPK ditahan di status pending logistik. Sistem secara otomatis membuat draft **"Daftar Belanja Workshop"** yang dikelompokkan berdasarkan manifest masuk tersebut.
    - Admin WS memproses belanja bahan tersebut. Begitu stok bahan baku tersebut diperbarui/diisi ulang oleh Workshop, SPK yang tertahan otomatis lolos ke tahap **Persiapan (Prep)**.

---

### Fitur 4: Alur Pengembalian Barang (Outbound Manifest & Staging QC)
* **Deskripsi:** Sepatu yang sudah selesai dikerjakan harus dikirim kembali ke toko/kasir utama menggunakan sistem manifest pengembalian.
* **Alur Pengerjaan:**
  1. SPK melewati siklus pengerjaan biasa: **Persiapan (Prep) ➡️ Sortir ➡️ Produksi (Prod) ➡️ Quality Control (QC)**.
  2. Setelah lolos QC, SPK tidak langsung berstatus "Selesai (Ready for Pickup)".
  3. SPK masuk ke halaman **"Antrean Kirim Kembali" (Staging Outbound)**.
  4. Admin WS membuat **Manifest Outbound (Selesai)** untuk mengirim batch sepatu kembali ke Gudang/Toko Utama.
  5. **Penanganan Ciri Khusus:**
     - **SPK OTO (One Time Offer):** Memiliki badge/label warna khusus (misal: ungu) untuk prioritas penanganan.
     - **SPK Revisi:** Memiliki badge merah menyala sebagai tanda pengerjaan ulang agar mendapat perhatian ekstra.
     - **SPK Garansi:** Memiliki badge kuning khusus. SPK Garansi **tidak boleh** langsung diselesaikan secara otomatis. SPK ini harus menempuh jalur Manifest Outbound khusus pasca-reparasi dan diproses dengan pengecekan ganda di sistem penerimaan toko sebelum diserahkan ke pelanggan.

---

### Fitur 5: Penginputan SPK Mandiri oleh Workshop (Rencana Pengembangan Selanjutnya / Standalone Module)
* **Deskripsi:** Konsep jangka panjang agar workshop dapat berdiri secara mandiri (misalnya menerima sepatu langsung dari pelanggan yang datang langsung ke lokasi workshop tanpa melalui kasir toko utama).
* **Aturan Bisnis:**
  - Layout PWA WS akan dibekali menu **"Input SPK Mandiri"** (pada pengembangan tahap berikutnya).
  - Admin WS dapat menginput data pelanggan, detail sepatu, jasa yang dipilih, dan pembayaran di tempat secara langsung.
  - SPK yang diinput mandiri ini akan langsung masuk ke halaman ACC internal WS tanpa memerlukan Manifest Inbound.

---

## 4. STRUKTUR MENU SIDEBAR PWA WORKSHOP (WORKSHOP LAYOUT)
Tampilan layout baru ini dirancang mobile-first (ringan, tombol besar, responsif) dengan sidebar khusus sebagai berikut:

```
├── 📊 Dashboard Workshop (Statistik pengerjaan & SLA harian)
├── 📥 Antrean Inbound
│   ├── Halaman Manifest Masuk (Daftar manifest dari toko)
│   └── Halaman Menunggu ACC (Staging input material & teknisi)
├── 🛠️ Proses Pengerjaan (Antrean Kerja Teknisi)
│   ├── 1. Persiapan / Cuci (Prep)
│   ├── 2. Sortir / Pemilahan (Sortir)
│   ├── 3. Produksi / Reparasi (Prod)
│   └── 4. Pemeriksaan Kualitas (QC)
├── 📦 Manajemen Material WS
│   ├── Stok Material (Kelola stok lem, sol, cat, dll)
│   ├── Belanja Gudang WS (Input pembelian bahan baku baru)
│   ├── Barang Keluar WS (Catat pengeluaran bahan untuk produksi)
│   └── Riwayat Mutasi (Jurnal mutasi masuk-keluar stok)
├── 📤 Antrean Outbound
│   ├── Halaman Siap Kirim (Staging SPK pasca-QC, terbagi: OTO, Revisi, Garansi)
│   └── Halaman Manifest Keluar (Pembuatan surat jalan kembali ke toko)
├── 📝 Mandiri WS (Rencana Pengembangan Selanjutnya)
│   └── Input SPK Baru (Pendaftaran pesanan langsung di bengkel)
└── 👥 Manajemen WS
    └── Pemetaan Teknisi (Atur spesialisasi jasa tiap teknisi)
```

---

## 5. ANALISIS ESTIMASI PERUBAHAN DATABASE (SKEMA TABEL)
Untuk mewujudkan fitur-fitur di atas tanpa merusak sistem lama, beberapa tabel perlu disesuaikan:

### A. Tabel Baru: `technician_services` (Jembatan Spesialisasi)
Menghubungkan user (teknisi) dengan master jasa yang mereka kuasai (Hubungan *Many-to-Many*).
* Kolom:
  - `id` (Primary Key)
  - `user_id` (Foreign Key ke tabel `users`, khusus role teknisi)
  - `service_id` (Foreign Key ke tabel `services`)

### B. Modifikasi Tabel: `work_orders`
* Tambahan kolom baru:
  - `material_approved_at` (Kapan bahan baku disetujui admin WS)
  - `workshop_manifest_in_id` (Foreign Key ke tabel `workshop_manifests` untuk pelacakan masuk)
  - `workshop_manifest_out_id` (Foreign Key ke tabel `workshop_manifests` untuk pelacakan keluar setelah selesai)

### C. Modifikasi Tabel: `workshop_manifests`
* Tambahan kolom baru:
  - `type` (Enum: `INBOUND` [Gudang ke WS], `OUTBOUND` [WS ke Gudang])
  - `manifest_month` (String/Date, untuk mempermudah filter laporan bulanan)

---

## 6. REKOMENDASI DESAIN AESTHETICS (PWA)
1. **Dark Mode & High Contrast:** Karena lingkungan kerja workshop seringkali terpapar air/sabun dan pencahayaan bengkel bervariasi, layout PWA harus memiliki opsi tema gelap (*dark mode*) dengan kontras tinggi agar teks mudah dibaca teknisi.
2. **Button-Friendly:** Ukuran tombol aksi (mulai kerja, selesai kerja) dibuat minimal 48px x 48px agar mudah ditekan oleh jemari teknisi saat menggunakan sarung tangan atau tangan dalam kondisi basah.
3. **Real-time Badges:** Menggunakan indikator badge warna dinamis di sidebar untuk menunjukkan jumlah antrean yang sedang menumpuk pada masing-masing tahap (misal: warna merah jika antrean QC > 10).

---

## 7. RENCANA TAHAPAN PENGEMBANGAN (ROADMAP)
* **Fase 1 (Fokus Utama): Manajemen User & Spesialisasi Teknisi (Fitur 1)**
  - Pemetaan user ber-role teknisi ke spesialisasi jasa.
  - Pembuatan kerangka layout khusus PWA Workshop.
* **Fase 2 (Fokus Utama): Gerbang Inbound & Staging ACC (Fitur 2 & Fitur 3)**
  - Penerimaan manifest inbound masuk ke staging ACC Admin WS.
  - Integrasi pencatatan material & penugasan teknisi otomatis.
  - Sistem penahanan SPK jika stok material workshop tidak mencukupi (Draft Belanja Otomatis).
* **Fase 3 (Fokus Utama): Gerbang Outbound & Ciri Khusus (Fitur 4)**
  - Staging SPK selesai QC ke antrean pengiriman kembali.
  - Pembuatan Manifest Outbound menuju toko utama.
  - Pelabelan dan rute verifikasi khusus untuk SPK OTO, Revisi, dan Garansi.
* **Fase 4 (Pengembangan Jangka Panjang): Modul Standalone & Input Mandiri (Fitur 5)**
  - Fitur registrasi SPK baru secara mandiri langsung dari bengkel workshop.

---
> [!TIP]
> Dokumen PRD ini dirancang dengan markdown standar sehingga siap untuk diekspor ke format PDF maupun Microsoft Word menggunakan perangkat konversi dokumen pilihan Anda. Dokumen ini hanya bersifat rancangan diskusi dan **belum diimplementasikan dalam kode program**.

# Sistem Manajemen Workshop Terintegrasi — Sortir & Produksi

**Progressive Web App (PWA) Mobile-First**
**PRD v1.1 + SRS v1.0 — Digabung menjadi Satu Dokumen Acuan**

| Field | Isi |
|---|---|
| Dokumen sumber 1 | Product Requirements Document (PRD) v1.1 — 8 Agustus 2026 |
| Dokumen sumber 2 | Software Requirements Specification (SRS) v1.0 — 10 Agustus 2026 |
| Dokumen induk | BRD Workshop Sortir & Produksi v5.1 — 7 Agustus 2026 |
| Status | Draft gabungan — untuk review Product Manager, Analyst & Developer |
| Product Manager | Pak Rozi |
| Analyst | Denata |
| Developer | Sidik |
| QA | Radit |
| Lead Workshop | Pak Dito |

Dokumen ini menggabungkan seluruh isi PRD v1.1 (kebutuhan produk, user story, acceptance criteria) dan SRS v1.0 (spesifikasi teknis, kontrak integrasi, pemetaan skema database) menjadi satu referensi tunggal yang runtut per modul. Bagian yang merupakan koreksi/revisi SRS terhadap PRD/BRD ditandai eksplisit agar tidak ada ambiguitas.

---

## Daftar Isi

1. Pendahuluan
2. Stakeholders & Pengguna Sistem
3. Ruang Lingkup Produk
4. Lifecycle Stage & Alur Aplikasi
5. Kebutuhan Fungsional per Modul (User Story, Acceptance Criteria & Spesifikasi Teknis)
6. Integrasi Eksternal — Kontrak Teknis Finlog
7. Kebutuhan Data & Struktur Basis Data
8. Kebutuhan Non-Fungsional
9. Ringkasan Gerbang Validasi Sistem (Gates)
10. Metrik Keberhasilan (Success Metrics)
11. Asumsi, Pertanyaan Terbuka & Koreksi Lapangan
12. Roadmap Pengembangan Selanjutnya
13. Glosarium

> **Cara membaca dokumen ini:** setiap sub-bagian ditandai label kecil **PRD** untuk konten dari Product Requirements Document dan **SRS** untuk konten dari Software Requirements Specification. Label **KOREKSI** menandai bagian di mana SRS merevisi/mengoreksi isi PRD atau BRD berdasarkan klarifikasi lapangan, dan **GAP** menandai temuan kesenjangan skema database yang perlu ditindaklanjuti Analyst/Developer.

---

## 1. Pendahuluan

### 1.1 Latar Belakang `PRD`

Workshop Sortir dan Workshop Produksi saat ini menjalankan proses sortir, perbaikan, dan pengembalian unit sepatu/barang secara manual atau semi-manual. Volume SPK (Surat Perintah Kerja) yang tinggi — saat ini sekitar 700 SPK aktif di workshop, mayoritas tertahan di antrean belanja Upper — menimbulkan bottleneck yang sulit dipantau tanpa sistem terpusat.

Produk yang dibangun adalah satu Progressive Web App (PWA) mobile-first dengan satu database dan satu backend bersama. Tampilan (UI) berbeda antara akun Workshop Sortir dan akun Workshop Produksi ditentukan otomatis dari field `workshop_type` saat login — bukan dua sistem terpisah.

**Prinsip Desain Utama — Admin-Only Input Model:** Teknisi tidak melakukan input apapun ke dalam sistem selama pengerjaan fisik. Seluruh pencatatan status, konfirmasi penyelesaian, dan perubahan tahap dilakukan oleh Admin Sortir atau Admin Produksi. Sistem dirancang agar tombol dan form input hanya tersedia di akun Admin.

### 1.2 Tujuan Produk & Tujuan Dokumen `PRD` `SRS`

**Tujuan Produk (PRD):**
- Mengotomasi alur inventaris SPK dari penerimaan batch inbound hingga pengembalian ke gudang/office (outbound).
- Memberikan visibilitas real-time atas status setiap SPK, termasuk lokasi rak, tahap pengerjaan, dan teknisi yang bertanggung jawab.
- Mengurangi bottleneck operasional, khususnya pada antrean belanja material (contoh: Upper) melalui indikator kapasitas rak dan integrasi pengadaan (Finlog).
- Menyediakan jejak audit (audit trail) yang lengkap untuk setiap keputusan: ACC, override urutan pengerjaan, dan Bypass Servis.
- Menjaga kualitas hasil kerja melalui mekanisme QC berjenjang (QC Produksi dan QC Akhir) dengan pembatasan rework yang terukur.

**Tujuan Dokumen (SRS):** menerjemahkan BRD v5.1 dan PRD v1.1 menjadi spesifikasi kebutuhan perangkat lunak yang lebih teknis — acuan bagi Analyst dan Developer dalam merancang skema database, API, dan modul aplikasi, serta acuan QA dalam menyusun test case. Dokumen gabungan ini menyatukan kedua tujuan tersebut dalam satu alur baca.

### 1.3 Ruang Lingkup Dokumen & Sistem `PRD` `SRS`

Sistem berupa satu PWA mobile-first dengan satu database dan satu backend bersama untuk seluruh modul internal Workshop (Sortir & Produksi) dan sistem pusat. Khusus integrasi dengan Finlog, sistem ini adalah aplikasi in-house terpisah dengan basis data sendiri (di luar Shared Database Architecture); komunikasi dan sinkronisasi data antara Workshop dan Finlog dilakukan sepenuhnya melalui REST API dan Webhook (detail kontrak teknis di Bagian 6).

Cakupan fase ini mencakup kebutuhan fungsional dan non-fungsional untuk fase peluncuran pertama, termasuk:

- Lifecycle stage baku: PREP → Sortir → PROD → QC → Post
- Penyederhanaan indikator kapasitas rak berbasis warna
- Integrasi Surat Pengajuan Belanja dengan Finlog (REST API + Webhook)
- Kolom alasan wajib pada Bypass Servis

Hak akses granular per role dan kapasitas rak presisi berbasis angka didokumentasikan sebagai roadmap pada Bagian 12.

---

## 2. Stakeholders & Pengguna Sistem

### 2.1 Tim Proyek `PRD`

| Role | PIC | Tanggung Jawab |
|---|---|---|
| Lead Workshop | Pak Dito | Arah strategis, kebijakan operasional, pengawasan performa produksi; eskalasi akhir untuk SPK gagal QC 3x |
| Product Manager | Pak Rozi | Prioritas fitur & pemantauan timeline |
| Analyst | Denata | Arsitektur teknis, desain API, koordinasi engineering |
| Developer | Sidik | Pengembangan PWA Workshop & implementasi skema database |
| QA | Radit | Pengujian fungsional dan performa sistem |

### 2.2 Persona Pengguna & Hak Akses `PRD` `SRS`

Untuk fase ini, seluruh user memakai skema akses yang sama (hak akses granular per role adalah roadmap fase berikutnya, lihat Bagian 12). Role Teknisi hanya menjadi subjek pencatatan (siapa yang mengerjakan, antrean SPK mereka), bukan aktor yang berinteraksi dengan form atau tombol di PWA.

| User Role | Lokasi | Akses & Tanggung Jawab Utama |
|---|---|---|
| Admin Sortir | Sortir | ACC manifest inbound & hasil pembagian teknisi; kelola pengecualian Rak FU; monitoring dashboard & kapasitas rak Sortir; catat konfirmasi penyelesaian PREP dan Sortir; catat klasifikasi Bongkar/Belanja. |
| Admin Produksi / Leader Produksi | Produksi | Monitoring antrean kerja Produksi; kelola manifest outbound; override manual urutan pengerjaan; ACC konfirmasi penyelesaian jasa; monitoring dashboard Produksi. |
| Staff Sortir | Sortir | Input klasifikasi Bongkar/Belanja SPK, antrean rak dan status SPK untuk keperluan koordinasi fisik; ajukan Surat Pengajuan Belanja via Finlog. (Seluruh input dieksekusi/dikonfirmasi via Admin Sortir.) |
| QC Produksi | Produksi | Verifikasi kualitas setelah SELURUH jasa dalam satu SPK selesai; keputusan Lolos atau Revisi dicatat oleh Admin Produksi. |
| QC Akhir | Sortir (Post) | Verifikasi akhir SPK yang kembali dari Produksi sebelum dinyatakan selesai; keputusan dicatat oleh Admin Sortir. |
| Teknisi Prep / Bongkar | Sortir | Pengerjaan fisik Cuci dan Bongkar. Tidak berinteraksi dengan sistem — penugasan dan konfirmasi selesai dikelola oleh Admin Sortir. |
| Teknisi Produksi | Produksi | Pengerjaan fisik Jahit dan Repaint. Tidak berinteraksi dengan sistem — penugasan dan konfirmasi dikelola oleh Admin Produksi. |
| Clean Up | Produksi | Membersihkan sisa lem/cat setelah seluruh jasa Produksi selesai. Tidak berinteraksi dengan sistem. |
| Support ("kernet") | Sortir & Produksi | Serbaguna; ditugaskan fleksibel sesuai kebutuhan harian. Tidak berinteraksi dengan sistem. |

**Catatan teknis (SRS 6.3):** seluruh Teknisi (Prep, Bongkar, Produksi) dan Clean Up/Support tidak memiliki akun login aktif pada fase ini — mereka tersimpan sebagai baris pada tabel `users` dengan `role='technician'`, namun kolom email/password/last_active_at pada baris tersebut adalah artefak skema bersama, bukan indikasi Teknisi benar-benar login. Ini konsisten dengan Admin-Only Input Model. Detail trade-off apakah data Teknisi tetap di tabel `users` atau dipisah ke tabel `technicians` baru dibahas di Bagian 7.3.

---

## 3. Ruang Lingkup Produk

### 3.1 Dalam Lingkup (In Scope — Fase Ini) `PRD`

- Modul Inbound: penerimaan batch dari gudang, auto-distribusi Teknisi Prep, ACC Admin Sortir.
- Lifecycle Stage PREP: Cuci — satu-satunya jasa fisik di tahap ini.
- Lifecycle Stage Sortir: klasifikasi material (Perlu Bongkar, Perlu Belanja) dan alokasi teknisi, diikuti pengerjaan fisik Bongkar setelah klasifikasi.
- Modul Bongkar & Belanja: Rak Tunggu Belanja, Surat Pengajuan Belanja terintegrasi Finlog.
- Lifecycle Stage PROD: multi-jasa (Soling, Upper, Treatment, Jahit, Repaint, dll.), sequencing otomatis + override manual, gate konfirmasi berjenjang ACC Admin Produksi, Clean Up.
- Lifecycle Stage QC: QC Produksi per-SPK setelah seluruh jasa selesai.
- Lifecycle Stage Post: verifikasi QC Akhir sebelum SPK dinyatakan selesai.
- Modul Outbound: Staging, Manifest Outbound, Original Batch Tagging untuk SPK OTO/Garansi/Revisi.
- Manajemen Rak: indikator kapasitas berbasis warna (merah jika melebihi kapasitas).
- Surat Jalan fisik pada 3 titik perpindahan: Sortir→Produksi, Produksi→Post, Post→Office.
- Bypass Servis dengan kolom alasan wajib — tercatat di audit trail.
- Dashboard operasional: Daily Throughput, Average Lead Time, Active Bottleneck Indicator, Rack Capacity Indicator.
- Pencatatan kerugian Revisi/Garansi dan lifecycle kendala.

### 3.2 Di Luar Lingkup (Out of Scope — Fase Ini) `PRD` `SRS`

- Input SPK mandiri oleh Workshop tanpa manifest inbound.
- Hak akses granular per role.
- Angka kapasitas maksimal presisi per kode rak (fase ini hanya indikator warna).
- Distribusi Terjadwal (Rounds) untuk penugasan teknisi Produksi.
- Akses input langsung oleh Teknisi ke sistem.

---

## 4. Lifecycle Stage & Alur Aplikasi

### 4.1 Lifecycle Stage Baku `PRD`

SPK dalam sistem melewati lima lifecycle stage secara berurutan. Perpindahan antar stage dicatat dan divalidasi oleh Admin, bukan oleh Teknisi.

```
INBOUND → [PREP] → [SORTIR] → [PROD] → [QC] → [POST] → OUTBOUND
```

| Stage | Lokasi Fisik | Kegiatan Utama | Aktor Input Sistem |
|---|---|---|---|
| PREP | Sortir | Cuci (washing) | Admin Sortir |
| SORTIR | Sortir | Klasifikasi material (Perlu Bongkar/Belanja), alokasi teknisi; diikuti pengerjaan fisik Bongkar oleh Teknisi Bongkar | Admin Sortir / Staff Sortir |
| PROD | Produksi | Multi-jasa (Soling, Upper, Jahit, Repaint, dll.), Clean Up | Admin Produksi / Leader Produksi |
| QC | Produksi | QC Produksi per-SPK; keputusan Lolos/Revisi | Admin Produksi / Leader Produksi |
| POST | Sortir (area Post) | QC Akhir; keputusan Lolos/Revisi; staging outbound | Admin Sortir |

**Catatan Bongkar:** secara konseptual Bongkar merupakan kelanjutan dari proses persiapan (sejenis PREP), namun secara operasional ia dikerjakan setelah Sortir — karena keputusan apakah perlu bongkar baru diketahui setelah klasifikasi material. Oleh karena itu, Bongkar ditempatkan sebagai sub-aktivitas dalam stage SORTIR (pasca-klasifikasi), bukan sebagai stage tersendiri.

### 4.2 Diagram 1 — Alur PREP & Sortir (Inbound hingga OTW Produksi) `PRD`

> *[Diagram alur — lihat dokumen PDF asli untuk visualisasi lengkap]*

Alur ringkas: Batch Inbound diterima dari Gudang → Admin Sortir input manifest & distribusi ke Teknisi Prep (ACC Inbound) → **Stage PREP**: Teknisi Prep Cuci (fisik) → Admin Sortir catat Cuci selesai → **Stage SORTIR**: Admin Sortir catat klasifikasi Perlu Bongkar?/Perlu Belanja? + tetapkan kategori SPK. Jika ada kendala verifikasi → masuk Rak Follow Up (timeout otomatis 5 hari, lalu resolve/auto-lanjut). Jika Perlu Bongkar=Ya → Teknisi Bongkar mengerjakan (fisik) → Admin Sortir catat Bongkar selesai. Jika Perlu Belanja=Ya → masuk Rak Tunggu Belanja (lihat Diagram 3) hingga stok terpenuhi/material diterima. Jika Bongkar=Tidak & Belanja=Tidak → langsung OTW ke Workshop Produksi (+ Surat Jalan Sortir→Produksi).

*Diagram 1. Alur PREP dan SORTIR: dari batch inbound hingga SPK berangkat (OTW) ke Workshop Produksi.*

**Poin kunci:**
- Gerbang ACC Inbound hanya mengecek kelengkapan manifest & hasil pembagian Teknisi Cuci — tidak memeriksa stok material.
- Kategori SPK ditetapkan oleh Admin Sortir / Staff Sortir, bukan oleh Teknisi.
- Rak Follow Up memiliki timeout otomatis 5 hari; setelah itu SPK auto-lanjut tanpa intervensi.
- SPK tidak dapat keluar dari stage SORTIR sebelum kedua field klasifikasi (Perlu Bongkar + Perlu Belanja) terisi.

### 4.3 Diagram 2 — Alur PROD, QC, dan Post `PRD`

> *[Diagram alur — lihat dokumen PDF asli untuk visualisasi lengkap]*

Alur ringkas: SPK tiba di Workshop Produksi (OTW selesai) → **Stage PROD**: Admin Produksi susun urutan jasa (default: Soling → Upper → Treatment) atau override manual → per jasa: assign teknisi → pengerjaan fisik → Admin Produksi catat selesai → ACC → cek "Masih ada jasa berikutnya?" (jika ya, ulangi; rework ≤2x kembali ke tahap ini) → jika tidak/semua selesai → Admin Produksi catat Clean Up selesai → **Stage QC**: QC Produksi memeriksa SPK (dicatat Admin Produksi) → jika Revisi: kembali ke teknisi terkait, rework counter QC Produksi +1, ke-3 kali → Rak FU + eskalasi; jika Lolos → SPK pindah ke Post (+ Surat Jalan Produksi→Post) → **Stage POST**: QC Akhir memeriksa SPK (dicatat Admin Sortir) → jika Revisi: kembali ke PROD, rework counter QC Akhir +1, ke-3 kali → Rak FU + eskalasi; jika Lolos → Staging Outbound, siap Manifest Outbound → OUTBOUND - Manifest & Surat Jalan Post→Office.

*Diagram 2. Alur PROD, QC, dan POST: multi-jasa Produksi hingga Outbound, dengan dua tahap QC terpisah.*

**Poin kunci:**
- Teknisi tidak mencatat apapun di sistem. Admin Produksi / Leader Produksi yang mencatat selesai jasa dan memberikan ACC.
- QC Produksi dan QC Akhir memiliki rework counter terpisah, masing-masing maksimal 2x (gagal ke-3 = kunci ke Rak FU).
- SPK wajib melewati stage POST sebelum bisa dimasukkan ke Manifest Outbound — tidak ada shortcut.
- Dua counter rework berjalan independen antara QC Produksi dan QC Akhir.

### 4.4 Diagram 3 — Alur Belanja Material & Integrasi Finlog `PRD`

> *[Diagram alur — lihat dokumen PDF asli untuk visualisasi lengkap]*

Alur ringkas: SPK di Rak Tunggu Belanja (Perlu Belanja = Ya) → Admin Sortir klik "Ajukan Belanja" di Web Workshop → Sistem otomatis kirim data ke Finlog (Integrasi REST API) → Status sinkron dari Finlog: Diajukan → Disetujui → Dibelanjakan → Material Diterima → Trigger otomatis saat status = "Material Diterima" → Material otomatis dipasangkan ke SPK terkait (auto-create SPK bila belum ada) → OTW ke Workshop Produksi.

*Diagram 3. Alur pengajuan belanja oleh Admin Sortir, terintegrasi Finlog, hingga material terpasang ke SPK.*

> **KOREKSI SRS terhadap diagram ini:** langkah terakhir pada diagram di atas menyebut "Material otomatis dipasangkan ke SPK terkait (auto-create SPK bila belum ada)" — ini sudah diklarifikasi lapangan dan direvisi oleh SRS (FR-4.5). SPK tidak dibuat otomatis dari Surat Pengajuan Belanja; SPK sudah tercatat di sistem sejak dari Office, sebelum barang masuk ke gudang/workshop. Payload REST API maupun Webhook selalu menyertakan `work_order_id` sebagai kunci penghubung ke SPK yang sudah ada, bukan mengenerate SPK baru. Istilah yang benar: **"auto-link & auto-lanjut status"**, bukan "auto-create SPK". Detail kontrak teknis REST API + Webhook lihat Bagian 6.

---

## 5. Kebutuhan Fungsional per Modul

Setiap modul di bawah ini menggabungkan tiga lapis informasi: user story & acceptance criteria (Given/When/Then) dari PRD, kebutuhan fungsional teknis bernomor FR-x.x dari SRS, serta catatan gap/koreksi skema database hasil inspeksi phpMyAdmin oleh SRS.

### 5.1 Modul Inbound & ACC

Menangani penerimaan batch dari gudang, distribusi otomatis SPK ke Teknisi Prep, dan ACC oleh Admin Sortir.

**User Stories PRD**
- Sebagai Sistem, saya ingin membagi rata SPK dalam satu batch ke Teknisi Prep secara otomatis, agar beban kerja merata.
- Sebagai Admin Sortir, saya ingin mengonfirmasi (ACC) manifest yang diterima dan hasil pembagian teknisi, agar SPK bisa mulai dikerjakan.

**Acceptance Criteria PRD**

| ID | Given | When | Then |
|---|---|---|---|
| AC1 | Sebuah batch/manifest inbound diterima dari gudang | Batch disimpan ke sistem | Sistem otomatis membagi rata seluruh SPK dalam batch ke Teknisi Prep yang tersedia |
| AC2 | Manifest inbound & hasil pembagian teknisi belum dikonfirmasi | Admin Sortir membuka halaman ACC | Tombol Approve/Release hanya aktif setelah manifest dan distribusi teknisi terkonfirmasi; tidak ada pengecekan stok material |

**Kebutuhan Fungsional Teknis SRS**

| ID | Deskripsi |
|---|---|
| FR-1.1 | Sistem WAJIB membagi rata (auto-distribute) seluruh SPK dalam satu batch/manifest inbound ke Teknisi Prep yang tersedia, segera setelah batch disimpan ke sistem. |
| FR-1.2 | Sistem WAJIB menonaktifkan tombol Approve/Release pada halaman ACC sampai Admin Sortir mengonfirmasi (a) manifest diterima dan (b) hasil pembagian otomatis teknisi. |
| FR-1.3 | Gerbang ACC Inbound TIDAK melibatkan pengecekan stok material. |

### 5.2 Modul PREP: Cuci

Stage PREP mencakup satu kegiatan saja: Cuci (washing). Tidak ada kegiatan lain di stage ini. Teknisi Prep melakukan pekerjaan fisik; Admin Sortir mencatat status di sistem.

**User Stories PRD**
- Sebagai Admin Sortir, saya ingin mencatat bahwa Teknisi Prep telah menyelesaikan Cuci untuk sebuah SPK, agar SPK bisa lanjut ke stage Sortir.

**Acceptance Criteria PRD**

| ID | Given | When | Then |
|---|---|---|---|
| AC1 | SPK sudah di-assign ke Teknisi Prep dan belum ditandai selesai Cuci | Admin Sortir membuka detail SPK dan menandai Cuci selesai | Status SPK berubah dan SPK masuk antrean stage Sortir |
| AC2 | SPK belum ditandai selesai Cuci | Sistem mengevaluasi SPK | SPK tidak dapat berpindah ke stage Sortir; tombol lanjut tidak aktif |
| AC3 | SPK sudah ditandai selesai Cuci | Sistem mengevaluasi SPK | Tombol lanjut aktif dan diklik Admin Sortir; SPK berpindah ke stage Sortir |

**Kebutuhan Fungsional Teknis SRS**

| ID | Deskripsi |
|---|---|
| FR-2.1 | Admin Sortir WAJIB dapat menandai status Cuci selesai untuk sebuah SPK yang sudah di-assign ke Teknisi Prep. |
| FR-2.2 | Sistem WAJIB mencegah SPK berpindah ke stage Sortir selama status Cuci belum ditandai selesai (tombol lanjut nonaktif). |

### 5.3 Modul Sortir: Klasifikasi & Bongkar

Stage Sortir mencakup dua aktivitas utama: (1) Klasifikasi — Admin Sortir / Staff Sortir mengisi field Perlu Bongkar dan Perlu Belanja, serta menetapkan kategori SPK; (2) Bongkar (jika Perlu Bongkar = Ya) — Teknisi Bongkar melakukan pekerjaan fisik, Admin Sortir mencatat selesai.

**User Stories PRD**
- Sebagai Admin Sortir / Staff Sortir, saya ingin mengisi field Perlu Bongkar dan Perlu Belanja secara independen untuk setiap SPK, agar SPK dirutekan sesuai kombinasi yang tepat.
- Sebagai Admin Sortir, saya ingin menetapkan kategori SPK (OTO/Priority/Garansi/Revisi/Fast Track/Regular), agar prioritas pengerjaan sesuai kebutuhan komersial dan SLA.
- Sebagai Admin Sortir / Staff Sortir, saya ingin mencatat bahwa Teknisi Bongkar telah menyelesaikan Bongkar, agar SPK bisa lanjut ke tahap berikutnya.

**Acceptance Criteria PRD**

| ID | Given | When | Then |
|---|---|---|---|
| AC1 | Staff Sortir telah menyelesaikan tahap Sortir untuk sebuah SPK | Field Perlu Bongkar dan Perlu Belanja belum diisi | Sistem menolak perpindahan status SPK keluar dari Sortir (hard-block) |
| AC2 | Kombinasi Bongkar=Ya & Belanja=Ya | Admin Sortir mencatat selesai Bongkar | SPK otomatis masuk Rak Tunggu Belanja |
| AC3 | Kombinasi Bongkar=Tidak & Belanja=Tidak | Klasifikasi disimpan | SPK langsung berstatus OTW Produksi |
| AC4 | Kombinasi Bongkar=Tidak & Belanja=Ya | Klasifikasi disimpan | SPK langsung berstatus OTW Produksi |
| AC5 | Kombinasi Bongkar=Ya & Belanja=Tidak | Klasifikasi disimpan | SPK otomatis masuk Rak Tunggu Belanja |
| AC6 | SPK berkategori OTO atau Priority | SPK masuk antrean kerja | SPK tidak mengikuti FIFO standar dan dapat dikerjakan paralel |
| AC7 | SPK berkategori Fast Track | SLA dipantau sistem | Sistem menandai pelanggaran jika melewati 10 hari |
| AC8 | Flag Revisi berasal dari komplain customer | Dibandingkan dengan rework internal QC | Sistem mencatat keduanya sebagai data terpisah |

**Kebutuhan Fungsional Teknis SRS**

| ID | Deskripsi |
|---|---|
| FR-3.1 | Sistem WAJIB menyediakan dua field independen pada SPK: Perlu Bongkar (Ya/Tidak) dan Perlu Belanja (Ya/Tidak), diisi manual oleh Staff/Admin Sortir. **GAP** kolom `perlu_bongkar` dan `perlu_belanja` belum ditemukan pada tabel `work_orders` saat ini. |
| FR-3.2 | Sistem WAJIB memblokir (hard-block) perpindahan status SPK keluar dari stage Sortir apabila kedua field klasifikasi belum diisi. |
| FR-3.3 | Sistem WAJIB merutekan SPK sesuai 4 kombinasi Bongkar × Belanja (lihat AC2–AC5 di atas). |
| FR-3.4 | Sistem WAJIB mendukung penetapan kategori SPK yang tidak saling eksklusif: OTO, Priority, Garansi, Revisi, Fast Track, Regular. |
| FR-3.5 | SPK berkategori OTO/Priority TIDAK mengikuti FIFO standar dan dapat dikerjakan paralel tanpa membatasi kuota teknisi. |
| FR-3.6 | Sistem WAJIB memantau SLA Fast Track (maksimal 10 hari) dan menandai pelanggaran bila terlampaui. |
| FR-3.7 | Sistem WAJIB mencatat flag Revisi (komplain customer) terpisah dari rework internal QC (lihat FR-6). |

> **GAP skema database:** kolom `perlu_bongkar` dan `perlu_belanja` perlu ditambahkan sebagai `tinyint(1)`/boolean pada tabel `work_orders`, atau dikonfirmasi apakah sudah diwakili field lain seperti `transaction_type`/`source_jasa` yang maknanya belum jelas.

### 5.4 Modul Rak Tunggu Belanja & Integrasi Finlog

SPK dengan Perlu Belanja = Ya ditahan di Rak Tunggu Belanja hingga material tersedia. Admin Sortir mengajukan Surat Pengajuan Belanja yang terhubung ke Finlog.

**User Story PRD**
- Sebagai Admin Sortir, saya ingin mengajukan Surat Pengajuan Belanja langsung ke Finlog, agar proses pengadaan lebih cepat dan tidak dicatat dua kali.

**Acceptance Criteria PRD**

| ID | Given | When | Then |
|---|---|---|---|
| AC1 | SPK di Rak Tunggu Belanja, Perlu Belanja=Ya, stok belum mencukupi | Admin mencoba memindahkan SPK ke OTW Produksi | Sistem menolak (hard-block) |
| AC2 | Surat Pengajuan Belanja berstatus 'Dibelanjakan' di Finlog | Status material berubah menjadi 'Diterima' | Sistem otomatis mengubah status SPK yang bersangkutan menjadi 'Material Tersedia' |
| AC3 | SPK existing di Rak Tunggu Belanja | Material telah diterima dan status Finlog = 'Material Diterima' | SPK otomatis lanjut OTW Produksi |

> **KOREKSI SRS — Keputusan Arsitektur Final:** Catatan Terbuka di PRD ("skema integrasi teknis dengan Finlog masih perlu dikonfirmasi") sudah diselesaikan di SRS. Finlog adalah aplikasi in-house terpisah dengan basis data sendiri (bukan Shared Database). Integrasi memakai pola hybrid: REST API (Workshop → Finlog) untuk mengirim pengajuan, dan Webhook (Finlog → Workshop) untuk sinkronisasi status. Kontrak teknis lengkap (endpoint, header, payload) ada di Bagian 6.

**Kebutuhan Fungsional Teknis SRS**

| ID | Deskripsi |
|---|---|
| FR-4.1 | Sistem WAJIB menolak perpindahan status SPK ke OTW Produksi selama klasifikasi Perlu Belanja = Ya dan material belum diperbarui mencukupi (gerbang Rak Tunggu Belanja). |
| FR-4.2 | Sistem WAJIB membuat draf "Daftar Belanja Workshop" otomatis, dikelompokkan per manifest masuk, untuk jalur belanja internal (Skenario B). |
| FR-4.3 | **REVISI** Saat Admin/Staff Sortir membuat Surat Pengajuan Belanja, backend Workshop WAJIB mengirim (POST) data pengajuan — `work_order_id`, daftar item, quantity — ke REST API endpoint Finlog secara sinkron/real-time saat pengajuan dibuat. |
| FR-4.4 | **REVISI** Sinkronisasi status (diajukan → disetujui → dibelanjakan → material diterima) WAJIB via Webhook: Finlog POST payload perubahan status ke endpoint Webhook Workshop. Backend WAJIB memvalidasi signature/secret, menyimpan payload, memperbarui status lokal. **GAP** kolom status pada `material_requests` saat ini hanya PENDING/APPROVED/REJECTED/PURCHASED/CANCELLED — perlu tambahan status RECEIVED. |
| FR-4.5 | **KOREKSI** terhadap BRD 4.5b/PRD — SPK TIDAK dibuat otomatis dari Surat Pengajuan Belanja. SPK sudah tercatat sejak dari Office sebelum barang masuk gudang/workshop. Payload REST API & Webhook WAJIB selalu menyertakan `work_order_id` sebagai kunci penghubung ke SPK yang sudah ada — bukan men-generate SPK baru. |
| FR-4.6 | **REVISI** Saat Webhook menerima status "material_received" untuk suatu `work_order_id`, backend WAJIB otomatis tanpa intervensi manual: (a) memperbarui status Surat Pengajuan Belanja lokal, (b) mengubah status SPK dari "Rak Tunggu Belanja" menjadi "OTW Produksi". Proses WAJIB idempotent dan tercatat di audit trail (pelaku = `system:finlog-webhook`). |

### 5.5 Modul PROD: Multi-Jasa, Sequencing, dan Override

Menyusun urutan pengerjaan jasa, gate konfirmasi berjenjang, dan kewenangan Admin Produksi untuk override. Seluruh pencatatan dilakukan oleh Admin Produksi.

**User Stories PRD**
- Sebagai Sistem, saya ingin menyusun urutan default jasa & teknisi sesuai spesialisasi, agar pengerjaan efisien tanpa perlu input manual di kondisi normal.
- Sebagai Admin Produksi, saya ingin mengedit urutan pengerjaan secara manual dan mengedit Teknisi yang mengerjakan SPK secara manual pada kondisi tertentu, agar kendala lapangan bisa diakomodasi.
- Sebagai Admin Produksi, saya ingin melihat antrean 'Menunggu ACC Penyelesaian' terpisah dari antrean kerja teknisi, agar proses ACC tidak tercampur dengan pekerjaan fisik.
- Sebagai Admin Produksi, saya ingin mencatat konfirmasi selesai jasa untuk setiap Teknisi, agar status SPK selalu akurat tanpa bergantung pada input Teknisi.

**Acceptance Criteria PRD**

| ID | Given | When | Then |
|---|---|---|---|
| AC1 | SPK memiliki kombinasi jasa Soling, Upper, dan Treatment | Sistem menyusun urutan default | Urutan mengikuti alur baku Soling → Upper → Treatment kecuali di-override manual |
| AC2 | Admin Produksi menandai satu jasa selesai | Konfirmasi disimpan | SPK berstatus "Menunggu ACC" dan tampil di antrean 'Menunggu ACC Penyelesaian' |
| AC3 | Admin Produksi melakukan override urutan pengerjaan | Perubahan disimpan | Sistem mencatat siapa, kapan, dan alasan perubahan di audit trail |
| AC4 | Admin Produksi membuka halaman assign teknisi | Memilih teknisi pengganti | Sistem hanya menampilkan teknisi yang kompeten sesuai stasiun yang dibutuhkan |

**Kebutuhan Fungsional Teknis SRS**

| ID | Deskripsi |
|---|---|
| FR-5.1 | Sistem WAJIB menyusun urutan pengerjaan jasa & menentukan teknisi sesuai spesialisasi secara otomatis sebagai default, mengikuti alur baku Soling → Upper → Treatment untuk kombinasi jasa terkait. |
| FR-5.2 | Sistem WAJIB menampilkan status per-jasa dalam satu SPK (selesai/sedang dikerjakan/belum dimulai) beserta teknisi terkait. |
| FR-5.3 | Setiap konfirmasi selesai satu jasa oleh teknisi WAJIB menempatkan SPK ke status menunggu ACC Admin Produksi, ditampilkan pada antrean tersendiri; berlaku untuk semua SPK termasuk yang hanya butuh satu jasa. |
| FR-5.4 | Admin Produksi WAJIB dapat mengedit urutan pengerjaan jasa secara manual (override); batasan override (teknisi/urutan stasiun/keduanya) dan kebutuhan approval tambahan perlu didetailkan lebih lanjut — lihat open question Bagian 11. |
| FR-5.5 | Setiap override manual WAJIB tercatat di audit trail (siapa, kapan, alasan). |
| FR-5.6 | Daftar jasa (services) WAJIB dikelola sebagai master data (bukan hardcoded). |
| FR-5.7 | Role Support dapat ditugaskan fleksibel ke jasa apa pun di luar pemetaan spesialisasi tetap. |
| FR-5.8 | Halaman ACC hanya menampilkan pilihan teknisi yang kompeten sesuai stasiun yang dibutuhkan SPK. |

> **GAP skema database:** tabel `services` sudah ada dan sesuai prinsip master data, namun kolom `category` bertipe bebas (varchar) belum tentu memetakan 1:1 ke stasiun Sol/Soling, Upper, Treatment, dan belum ada kolom eksplisit urutan default (mis. `default_sequence`). Disarankan menambahkan kolom tersebut atau memetakan `category` secara terkontrol via tabel referensi stasiun.

### 5.6 Modul QC Produksi & Clean Up

QC dilakukan per-SPK setelah seluruh jasa selesai dan ter-ACC, didahului Clean Up. Admin Produksi mencatat seluruh keputusan QC.

**User Stories PRD**
- Sebagai Admin Produksi, saya ingin mencatat bahwa Clean Up telah selesai, agar SPK bisa masuk QC Produksi.
- Sebagai Admin Produksi, saya ingin mencatat keputusan QC Produksi (Lolos/Revisi) untuk satu SPK secara utuh, agar kualitas dinilai menyeluruh.

**Acceptance Criteria PRD**

| ID | Given | When | Then |
|---|---|---|---|
| AC1 | SPK memiliki lebih dari satu jasa dan belum seluruhnya selesai/ter-ACC | Sistem mengevaluasi status SPK | SPK ditahan di "menunggu jasa lain" dan tidak memicu Clean Up atau QC Produksi |
| AC2 | Seluruh jasa dalam SPK selesai dan ter-ACC | SPK diproses | SPK masuk Clean Up lalu QC Produksi sebagai satu kesatuan |
| AC3 | QC Produksi memutuskan Revisi | Keputusan disimpan oleh Admin Produksi | SPK dikembalikan ke teknisi spesifik yang mengerjakan bagian bermasalah; rework QC Produksi +1 |
| AC4 | SPK lolos QC Produksi | Status diperbarui | SPK berpindah ke stage POST; Surat Jalan Produksi→Post diterbitkan |
| AC5 | SPK gagal QC Produksi untuk ketiga kalinya | Rework ketiga tercatat | Sistem otomatis mengunci SPK ke Rak FU untuk eskalasi ke Lead Workshop |

**Kebutuhan Fungsional Teknis SRS**

| ID | Deskripsi |
|---|---|
| FR-6.1 | Sistem WAJIB menahan SPK pada status "menunggu jasa lain" dan tidak memicu Clean Up/QC Produksi sampai seluruh jasa dalam urutan selesai DAN ter-ACC Admin Produksi. |
| FR-6.2 | Sistem WAJIB mengarahkan SPK ke tahap Clean Up setelah seluruh jasa selesai, sebelum masuk QC Produksi. |
| FR-6.3 | Sistem WAJIB mencatat keputusan QC Produksi: Lolos (berpindah ke status Post tanpa manifest/batch) atau Revisi (dikembalikan ke teknisi spesifik yang mengerjakan bagian bermasalah). |
| FR-6.4 | Sistem WAJIB menghitung rework QC Produksi terpisah dari rework QC Akhir, masing-masing maksimal 2 kali rework pada tahapnya sendiri. |
| FR-6.5 | Jika SPK gagal untuk ketiga kalinya pada QC yang sama, sistem WAJIB otomatis mengunci SPK ke Rak FU untuk dievaluasi Lead Workshop. |

> **GAP skema database:** tabel `work_order_revisions` mencatat revisi per SPK (status, origin_status, created_by, resolved_by) namun belum ada kolom eksplisit pembeda "QC Produksi" vs "QC Akhir". Disarankan menambahkan kolom `qc_stage` (ENUM: PRODUKSI, AKHIR) agar penghitungan rework 2x per tahap (FR-6.4/FR-7.2) bisa dihitung terpisah secara query, bukan asumsi dari `origin_status`.

### 5.7 Modul POST & QC Akhir

SPK yang tiba dari Produksi berada di area Post. QC Akhir dilakukan oleh Admin Sortir sebelum SPK dinyatakan selesai.

**User Story PRD**
- Sebagai Admin Sortir, saya ingin mencatat keputusan QC Akhir (Lolos/Revisi) untuk SPK yang kembali dari Produksi, agar hanya SPK yang layak dikembalikan ke gudang.

**Acceptance Criteria PRD**

| ID | Given | When | Then |
|---|---|---|---|
| AC1 | SPK berada di stage Post dan belum diproses QC Akhir | Admin mencoba memasukkan ke Manifest Outbound | Sistem menolak dengan error alert (hard-block) |
| AC2 | QC Akhir memutuskan Revisi | Keputusan disimpan oleh Admin Sortir | SPK dikembalikan ke PROD untuk perbaikan; rework QC Akhir +1 |
| AC3 | SPK gagal QC Akhir untuk ketiga kalinya | Rework ketiga tercatat | Sistem otomatis mengunci SPK ke Rak FU untuk eskalasi ke Lead Workshop |
| AC4 | SPK lolos QC Akhir | Status diperbarui | SPK masuk Staging Outbound; siap dimasukkan ke Manifest Outbound |

**Kebutuhan Fungsional Teknis SRS**

| ID | Deskripsi |
|---|---|
| FR-7.1 | Sistem WAJIB menolak (hard-block dengan error alert) upaya memasukkan SPK ke QC Akhir/Manifest Outbound selama SPK belum berstatus Post / QC Akhir belum PASS. |
| FR-7.2 | Keputusan QC Akhir: Revisi (dikirim balik ke Produksi, rework QC Akhir +1, terpisah dari counter QC Produksi) atau Lolos (masuk Staging Outbound). |
| FR-7.3 | SPK Garansi TIDAK BOLEH diselesaikan otomatis — wajib menempuh Manifest Outbound khusus pasca-reparasi dengan pengecekan ganda di sistem penerimaan toko. |

### 5.8 Modul Outbound Staging & Manifest

SPK yang lolos QC Akhir masuk Staging Outbound sebelum dikelompokkan ke Manifest Outbound. Dikelola sepenuhnya oleh Admin Produksi.

**User Stories PRD**
- Sebagai Admin Produksi, saya ingin mengelompokkan SPK yang lolos QC Akhir ke dalam Manifest Outbound, agar pengiriman kembali terorganisir.
- Sebagai Sistem, saya ingin mengikat SPK OTO/Garansi/Revisi ke ID Manifest Masuk aslinya, agar riwayat batch tetap terlacak.

**Acceptance Criteria PRD**

| ID | Given | When | Then |
|---|---|---|---|
| AC1 | SPK berkategori OTO/Garansi/Revisi dikirim bersama batch outbound lain | Pengiriman fisik dicatat | Sistem mencatat kembali data ke laporan historis batch asal dan memberi badge [Inbound: Batch X - Prioritas] |
| AC2 | SPK berkategori Garansi selesai reparasi | SPK masuk Outbound | SPK wajib menempuh Manifest Outbound khusus pasca-reparasi; tidak boleh selesai otomatis |

**Kebutuhan Fungsional Teknis SRS**

| ID | Deskripsi |
|---|---|
| FR-8.1 | Sistem WAJIB mengikat SPK berkategori OTO/Garansi/Revisi secara permanen ke ID Manifest Inbound pertama kali diterima (Original Batch Tagging). |
| FR-8.2 | Saat pengiriman fisik dilakukan bersama batch outbound lain, sistem WAJIB mencatat kembali data SPK ke laporan historis batch asal dan memberi badge "[Inbound: Batch X - Prioritas]" pada manifest maupun tampilan PWA. |

### 5.9 Manajemen Rak & Indikator Kapasitas

Rak Antri Pengerjaan, Rak Antri Teknisi, Rak Follow Up, dan Rak Tunggu Belanja ditampilkan dengan indikator kapasitas berbasis warna.

**Acceptance Criteria PRD**

| ID | Given | When | Then |
|---|---|---|---|
| AC1 | Okupansi sebuah kode rak melebihi ambang yang dikonfigurasi | Dashboard/rak ditampilkan | Rak ditampilkan berwarna merah; rak normal ditampilkan hijau |
| AC2 | SPK akan dimasukkan ke rak yang sudah mencapai/melebihi kapasitas | Admin melakukan aksi pemindahan | Sistem menampilkan peringatan (soft-warning), bukan penolakan keras — aksi tetap bisa dilanjutkan |

**Kebutuhan Fungsional Teknis SRS**

| ID | Deskripsi |
|---|---|
| FR-9.1 | Sistem WAJIB menghitung okupansi tiap kode rak secara real-time dan menampilkan indikator warna merah bila melebihi kapasitas, hijau bila normal — threshold pasti perlu dikonfirmasi ke tim workshop. |
| FR-9.2 | Ketika SPK akan dimasukkan ke rak yang sudah mencapai/melebihi kapasitas, sistem menampilkan peringatan (soft-warning) — bukan penolakan keras. |
| FR-9.3 | [Roadmap] Kapasitas maksimal presisi (angka) per kode rak — di luar lingkup fase ini. |
| FR-9.4 | Background job harian WAJIB mengecek timeout Rak FU (5 hari tanpa balasan customer) dan memicu auto-lanjut SPK sesuai jasa awal tanpa intervensi manual. |

> **GAP skema database:** tidak ada tabel/field kapasitas rak (nama rak, kapasitas, okupansi real-time) — hanya `storage_rack_code` (varchar bebas) di `work_orders`. Disarankan membuat tabel `racks` (kode rak, lokasi, kapasitas opsional) agar indikator warna dan gate kapasitas bisa dihitung dari data terstruktur.

### 5.10 Surat Jalan Antar Tahap

Dokumen pendamping serah-terima SPK pada tiga titik perpindahan fisik: Sortir→Produksi, Produksi→Post, dan Post→Office/Gudang.

**Acceptance Criteria PRD**

| ID | Given | When | Then |
|---|---|---|---|
| AC1 | SPK berpindah fisik pada salah satu dari tiga titik perpindahan | User meminta cetak/tampilkan Surat Jalan | Sistem menghasilkan dokumen dengan field minimal: Tanggal Pengiriman, Nomor SPK, Estimasi, Jasa terkait, Nama Pembawa (Teknisi/Kurir), dengan referensi yang bisa ditelusuri ke riwayat SPK |

**Kebutuhan Fungsional Teknis SRS**

| ID | Deskripsi |
|---|---|
| FR-10.1 | Sistem WAJIB dapat mencetak/menampilkan dokumen Surat Jalan pada tiap titik perpindahan fisik SPK, dengan field minimal: Tanggal Pengiriman, Nomor SPK, Estimasi (Est.), Jasa terkait, dan Nama Pembawa ("Mamang"). |
| FR-10.2 | Surat Jalan WAJIB memiliki referensi yang dapat ditelusuri kembali ke riwayat SPK terkait. |
| FR-10.3 | Surat Jalan adalah dokumen pendamping fisik dan TIDAK menggantikan mekanisme perpindahan status SPK real-time per unit (non-manifest) yang sudah berlaku. |

> **GAP skema database:** tidak ada tabel Surat Jalan. Disarankan membuat tabel `surat_jalan`: `work_order_id`, `titik_perpindahan` (ENUM), `tanggal_pengiriman`, `estimasi`, `nama_pembawa`, `dicetak_pada`.

### 5.11 Bypass Servis, Override, dan Audit Trail

Admin diberi kewenangan melewati satu tahap/jasa (Bypass Servis) atau mengubah urutan pengerjaan (Override), dengan syarat setiap aksi tercatat lengkap.

**Acceptance Criteria PRD**

| ID | Given | When | Then |
|---|---|---|---|
| AC1 | Admin melakukan Bypass Servis pada sebuah SPK | Form Bypass Servis diisi | Sistem mewajibkan pengisian kolom alasan sebelum aksi dapat disimpan; audit trail mencatat siapa, kapan, dan alasan |
| AC2 | Setiap perpindahan status penting terjadi (ACC, konfirmasi jasa, hasil QC, override, Bypass) | Aksi tersimpan | Sistem mencatat jejak waktu dan pelaku pada audit trail |

**Kebutuhan Fungsional Teknis SRS**

| ID | Deskripsi |
|---|---|
| FR-11.1 | **BARU V5.1** Sistem WAJIB mewajibkan pengisian kolom alasan (mandatory) pada setiap aksi Bypass Servis sebelum aksi dapat disimpan. |
| FR-11.2 | Sistem WAJIB mencatat audit trail untuk setiap Bypass Servis dan Override: siapa pelaku, kapan, dan alasan. |
| FR-11.3 | Sistem WAJIB mencatat jejak waktu dan pelaku untuk setiap perpindahan status penting (ACC, konfirmasi jasa, hasil QC, override, Bypass). |

Tabel `work_order_logs` (kolom: work_order_id, user_id, step, action, description) cukup fleksibel untuk mendukung audit trail ini, asalkan `action`/`step` diisi konsisten dari sisi aplikasi.

### 5.12 Dashboard & Notifikasi

Visibilitas operasional harian untuk Admin Sortir dan Admin Produksi, dengan sidebar yang disesuaikan `workshop_type`.

**Acceptance Criteria PRD**

| ID | Given | When | Then |
|---|---|---|---|
| AC1 | Antrean "Menunggu ACC" melebihi 15 SPK | Dashboard dimuat | Counter ditampilkan berwarna merah |
| AC2 | Item di Rak Tunggu Belanja tertahan lebih dari 5-7 hari | Dashboard dimuat | Counter ditampilkan berwarna kuning / merah |
| AC3 | User login dengan `workshop_type=Sortir` | PWA dimuat | Sidebar menampilkan modul: Antrean Inbound, PREP (Cuci), Sortir, Bongkar, Rak Tunggu Belanja, Post, Rak FU |
| AC4 | User login dengan `workshop_type=Produksi` | PWA dimuat | Sidebar menampilkan modul: Antrean Kerja Produksi, QC Produksi, Outbound |

**Kebutuhan Fungsional Teknis SRS**

| ID | Deskripsi |
|---|---|
| FR-12.1 | Sistem WAJIB menampilkan counter real-time per kategori rak dengan alert warna: merah bila antrean "Menunggu ACC" > 15 SPK; kuning/merah bila item Rak Tunggu Belanja tertahan > 24 jam (s.d. 5-7 hari sesuai kebijakan). |
| FR-12.2 | Sidebar PWA WAJIB otomatis menyesuaikan `workshop_type`: Sortir (Antrean Inbound, PREP/Cuci, Sortir, Bongkar, Rak Tunggu Belanja, Post, Rak FU) atau Produksi (Antrean Kerja Produksi, QC Produksi, Outbound). |
| FR-12.3 | Dashboard WAJIB menampilkan metrik: Daily Throughput, Average Lead Time per kategori, Active Bottleneck Indicator (jumlah SPK aktif di Rak FU dan Rak Tunggu Belanja), dan Rack Capacity Indicator berbasis warna. |

### 5.13 Pencatatan Kerugian & Lifecycle Kendala

Pencatatan wajib untuk SPK berflag Revisi/Garansi serta riwayat kendala operasional.

**Kebutuhan Fungsional Teknis SRS**

| ID | Deskripsi |
|---|---|
| FR-13.1 | Setiap SPK berflag Revisi atau Garansi WAJIB memiliki catatan kerugian dari sisi workshop. |
| FR-13.2 | Setiap kendala yang muncul sepanjang alur kerja WAJIB tercatat sebagai satu riwayat utuh: kapan muncul, penyebab, penanggung jawab, kapan & bagaimana selesai. |

---

## 6. Integrasi Eksternal — Kontrak Teknis Finlog `SRS`

Aplikasi Workshop dan aplikasi Finlog dikembangkan secara in-house dan memiliki basis data yang sepenuhnya terpisah untuk modul ini (bukan Shared Database). Integrasi ditetapkan menggunakan pola hybrid REST API + Webhook.

- **REST API (Workshop → Finlog):** Workshop bertindak sebagai client, memanggil endpoint API milik Finlog untuk mengirim Surat Pengajuan Belanja saat dibuat oleh Admin/Staff Sortir.
- **Webhook (Finlog → Workshop):** Finlog bertindak sebagai client, memanggil endpoint Webhook milik Workshop setiap kali status pengajuan berubah (diajukan, disetujui, dibelanjakan, material diterima).
- **Otomasi status SPK:** begitu Webhook Workshop menerima status "material_received", backend Workshop wajib otomatis memindahkan SPK dari Rak Tunggu Belanja ke OTW Produksi (FR-4.6) tanpa intervensi manual Admin.

### 6.1 REST API — Workshop mengirim Surat Pengajuan Belanja ke Finlog

| Field | Nilai |
|---|---|
| Method / Endpoint | `POST https://api.finlog.internal/v1/purchase-requests` |
| Arah | Workshop (client) → Finlog (server) |
| Dipanggil saat | Admin/Staff Sortir menekan "Ajukan Belanja" pada SPK dengan Perlu Belanja = Ya |
| Autentikasi | Bearer Token (OAuth2 client-credentials) pada header Authorization |
| Idempotency | Wajib menyertakan header `Idempotency-Key` agar retry tidak membuat pengajuan ganda di Finlog |

**Header wajib:**
```
Content-Type: application/json
Authorization: Bearer <access_token>
Idempotency-Key: <uuid-v4>
X-Source-System: workshop-app
X-Request-Id: <uuid-v4>
```

**Contoh Payload Request Body:**
```json
{
  "request_number": "REQ-2026-0001",
  "work_order_id": 8955,
  "spk_number": "SPK-2026-08-01234",
  "type": "SHOPPING",
  "requested_by": { "user_id": 40, "name": "Bunga", "role": "staff_sortir" },
  "items": [
    { "material_id": 112, "material_name": "Kulit Upper Sintetis Hitam",
      "specification": "Size 42, grade A", "quantity": 2, "unit": "pcs",
      "estimated_price": 85000.00 }
  ],
  "total_estimated_cost": 170000.00,
  "notes": "Untuk kebutuhan Upper - stok gudang kosong",
  "callback_webhook_url": "https://workshop.internal/api/webhooks/finlog/purchase-status",
  "requested_at": "2026-08-10T10:15:00+07:00"
}
```

**Contoh Response (201 Created) dari Finlog:**
```json
{
  "status": "success",
  "data": {
    "finlog_request_id": "FLG-REQ-778812",
    "request_number": "REQ-2026-0001",
    "status": "PENDING",
    "received_at": "2026-08-10T10:15:02+07:00"
  }
}
```

Backend Workshop WAJIB menyimpan `finlog_request_id` yang dikembalikan Finlog ke kolom baru pada tabel `material_requests` (mis. `finlog_request_id`) sebagai referensi silang untuk pencocokan payload Webhook yang masuk kemudian.

### 6.2 Webhook — Finlog mengirim update status ke Workshop

| Field | Nilai |
|---|---|
| Method / Endpoint | `POST https://workshop.internal/api/webhooks/finlog/purchase-status` |
| Arah | Finlog (client) → Workshop (server) |
| Dipanggil saat | Setiap kali status pengajuan berubah di Finlog: diajukan → disetujui → dibelanjakan → material diterima (juga untuk ditolak/dibatalkan) |
| Autentikasi | HMAC Signature pada header `X-Finlog-Signature` (secret dipertukarkan saat setup, mis. HMAC-SHA256 atas raw body) |
| Idempotency | Setiap payload menyertakan `event_id` unik; Workshop WAJIB menolak/mengabaikan `event_id` yang sudah pernah diproses |

Response wajib **200 OK** dengan body `{"received": true}` maksimal 2 detik; Finlog akan retry dengan backoff bila tidak menerima 2xx.

**Header wajib (dikirim oleh Finlog):**
```
Content-Type: application/json
X-Finlog-Signature: sha256=<hmac_hex_digest>
X-Finlog-Event-Id: <uuid-v4>
X-Finlog-Event-Type: purchase_request.status_changed
X-Finlog-Timestamp: 2026-08-10T14:32:10+07:00
```

**Contoh Payload — status "material diterima" (memicu FR-4.6):**
```json
{
  "event_id": "evt_9f2c1a7e",
  "event_type": "purchase_request.status_changed",
  "finlog_request_id": "FLG-REQ-778812",
  "request_number": "REQ-2026-0001",
  "work_order_id": 8955,
  "spk_number": "SPK-2026-08-01234",
  "previous_status": "PURCHASED",
  "status": "material_received",
  "status_label": "Material Diterima",
  "received_by": { "name": "Gudang Finlog", "location": "Gudang Pusat" },
  "items_received": [ { "material_id": 112, "quantity_received": 2, "unit": "pcs" } ],
  "changed_at": "2026-08-10T14:32:05+07:00"
}
```

**Daftar nilai status yang mungkin dikirim pada field `status`:**

| Nilai status (Finlog) | Pemetaan status lokal `material_requests` | Efek pada SPK |
|---|---|---|
| submitted | PENDING | Tidak ada — hanya update tampilan status |
| approved | APPROVED | Tidak ada — hanya update tampilan status |
| rejected | REJECTED | Admin Sortir dinotifikasi; SPK tetap di Rak Tunggu Belanja untuk tindak lanjut manual |
| purchased | PURCHASED | Tidak ada — menunggu konfirmasi material diterima |
| material_received | RECEIVED **(GAP)** | WAJIB otomatis: SPK pindah dari Rak Tunggu Belanja → OTW Produksi (FR-4.6) |
| cancelled | CANCELLED | Admin Sortir dinotifikasi untuk tindak lanjut manual |

**Contoh Response Workshop ke Finlog (200 OK):**
```json
{
  "received": true,
  "event_id": "evt_9f2c1a7e",
  "processed_at": "2026-08-10T14:32:06+07:00"
}
```

### 6.3 Ringkasan Alur End-to-End

1. Staff Sortir membuat Surat Pengajuan Belanja di Workshop → Workshop POST ke REST API Finlog (6.1).
2. Finlog memproses pengajuan secara internal (approval, procurement).
3. Setiap perubahan status, Finlog POST ke Webhook Workshop (6.2); Workshop update `material_requests.status` sesuai payload.
4. Saat status = `material_received` diterima via Webhook, backend Workshop otomatis menjalankan FR-4.6: SPK terkait (via `work_order_id` pada payload) berpindah dari Rak Tunggu Belanja ke OTW Produksi, tercatat di audit trail.
5. Kegagalan pengiriman (REST API maupun Webhook) WAJIB masuk mekanisme retry & dead-letter/alert ke tim teknis, agar tidak ada SPK yang "tersangkut" akibat kegagalan integrasi.

> **Catatan arsitektur (SRS 4.2):** Sistem menggunakan Shared Database Architecture HANYA untuk integrasi data internal dengan sistem pusat non-Finlog (mis. notifikasi CS untuk item unrepairable), yang berbagi database yang sama dengan Workshop. Integrasi dengan Finlog TIDAK termasuk dalam Shared Database Architecture ini — Finlog berkomunikasi murni melalui REST API dan Webhook sebagaimana dijabarkan di atas.

---

## 7. Kebutuhan Data & Struktur Basis Data

### 7.1 Entitas Utama (Konseptual) `PRD`

| Entitas | Deskripsi Ringkas |
|---|---|
| SPK | Unit kerja per sepatu/barang. Atribut kunci: nomor SPK, kategori (OTO/Priority/Garansi/Revisi/Fast Track/Regular), Perlu Bongkar, Perlu Belanja, lifecycle stage saat ini (PREP/SORTIR/PROD/QC/POST), ID Manifest Inbound asal, daftar jasa & urutan, rework counter QC Produksi, rework counter QC Akhir. |
| Manifest Inbound | Batch penerimaan dari gudang; sumber SPK awal dan referensi Original Batch Tagging. |
| Manifest Outbound | Pengelompokan SPK yang lolos QC Akhir untuk dikirim kembali ke unit asal. |
| Jasa (Service) | Master data jasa (Cuci, Bongkar, Soling, Upper, Treatment, Jahit, Repaint, dll.) — tidak di-hardcode. |
| SPK-Jasa (junction) | Relasi many-to-many SPK dan Jasa: status per-jasa, urutan, teknisi assigned, timestamp selesai dicatat Admin. |
| Teknisi | Data teknisi dengan pool (Sortir/Produksi), spesialisasi, dan status ketersediaan. Tidak memiliki akun login aktif untuk fase ini. |
| Rak | Kode rak, lokasi, kapasitas (indikator warna fase ini), okupansi real-time. |
| Surat Jalan | Dokumen serah-terima fisik: tanggal pengiriman, nomor SPK, estimasi, jasa terkait, nama pembawa, titik perpindahan. |
| Surat Pengajuan Belanja | Terintegrasi Finlog: status sinkron (diajukan/disetujui/dibelanjakan/material diterima), referensi SPK terkait. |
| Audit Trail | Log setiap aksi penting: pelaku (Admin), waktu, jenis aksi (ACC, override, bypass, hasil QC), alasan (untuk Bypass Servis). |
| Catatan Kerugian | Terhubung ke SPK berflag Revisi/Garansi; mencatat kerugian dari sisi workshop. |
| Lifecycle Kendala | Riwayat kendala: kapan muncul, penyebab, penanggung jawab, kapan & bagaimana selesai. |

### 7.2 Tabel Basis Data Eksisting (hasil inspeksi phpMyAdmin) `SRS`

Bagian ini memetakan entitas konseptual di atas terhadap skema tabel yang sudah ada, untuk mengidentifikasi kesenjangan (gap) yang perlu ditambahkan.

| Nama Tabel | Fungsi / Pemetaan terhadap Kebutuhan |
|---|---|
| materials | Master data material/bahan |
| material_requests | Pengajuan kebutuhan material (header) |
| material_request_items | Detail baris item per pengajuan material |
| material_reservations | Reservasi/penguncian stok material untuk SPK tertentu |
| material_transactions | Log transaksi keluar-masuk stok material |
| warranty_claims | Klaim garansi terkait SPK |
| workshop_manifests | Manifest inbound/outbound antar gudang & workshop |
| work_orders | SPK (Surat Perintah Kerja) — entitas utama |
| work_order_logs | Log/histori perubahan status SPK (mendukung audit trail) |
| work_order_materials | Relasi SPK dengan material yang digunakan |
| work_order_photos | Dokumentasi foto per SPK (before/after, bukti QC, dll.) |
| work_order_revisions | Riwayat revisi/rework per SPK (QC Produksi & QC Akhir) |
| work_order_services | Relasi SPK dengan jasa (services) — junction SPK-Jasa |
| work_order_warranties | Relasi SPK dengan klaim garansi |

#### 7.2.1 Catatan Struktur — Tabel Kunci

| Tabel | Catatan Struktur |
|---|---|
| work_orders | Tabel SPK utama, 131 kolom. Mencakup timestamp per tahap kerja (prep_washing_*, prod_sol_*, qc_jahit_*, dst.), flag komersial/kualitas terpisah (has_active_oto, is_warranty, is_revising, priority, fast_track_status, category_spk), storage_rack_code, workshop_manifest_id (link ke inbound). TIDAK ada kolom perlu_bongkar/perlu_belanja eksplisit. |
| work_order_services | Junction SPK↔Jasa, sudah punya kolom technician_id, service_id, status, cost, promotion_id. Mendukung FR-5.2/5.3 dengan baik. |
| work_order_logs | Log generik: work_order_id, user_id, step, action, description — cukup fleksibel untuk audit trail (FR-11.3), asalkan action/step diisi konsisten dari aplikasi. |
| work_order_revisions | Mencatat revisi per SPK: status (default OPEN), origin_status, created_by, resolved_by. Belum ada kolom eksplisit pembeda "QC Produksi" vs "QC Akhir". |
| services | Master jasa — sudah lengkap (id, name, category, price, allow_fast_track, duration_minutes, hk_days, unit, description). |
| material_requests | Header pengajuan belanja: type (SHOPPING/PRODUCTION_PO), status (PENDING/APPROVED/REJECTED/PURCHASED/CANCELLED), work_order_id (nullable, link ke SPK EKSISTING), oto_id, requested_by, approved_by. |
| material_request_items | Baris detail per item: material_request_id, work_order_id, material_id, quantity, unit, estimated_price. |
| material_reservations | Reservasi stok: material_id, work_order_id, oto_id, type (SOFT/HARD), status (ACTIVE/CONFIRMED/RELEASED/EXPIRED). |
| users | Menyimpan seluruh aktor sistem termasuk Teknisi (role='technician'): email, password, role (user/admin/technician), is_active, last_active_at, access_rights (JSON, admin), specialization (varchar, technician), phone. |

### 7.3 Teknisi: Tersimpan di Tabel `users` (role: technician)

Data Teknisi ternyata ada — bukan di tabel terpisah, melainkan sebagai baris pada tabel `users` dengan `role = 'technician'`. Kolom-kolom seperti `technician_production_id`, `prep_washing_by`, `work_order_services.technician_id` kemungkinan besar mereferensikan `users.id` dengan `role='technician'` — bukan tabel terpisah.

Klarifikasi tim: baris `role='technician'` di tabel `users` HANYA dipakai untuk pencatatan identitas & spesialisasi Teknisi — BUKAN untuk login/input ke sistem, konsisten dengan Admin-Only Input Model. Kolom email/password/last_active_at kemungkinan artefak skema bersama untuk semua role, bukan indikasi Teknisi benar-benar login — namun tetap disarankan dikonfirmasi ke Analyst agar tidak jadi celah keamanan (akun aktif tak terpakai).

**GAP yang masih tersisa pada `users`:**

| Kolom Tambahan | Tipe (saran) | Keterangan |
|---|---|---|
| pool | ENUM(sortir, produksi), nullable | Pool/lokasi kerja teknisi — dibutuhkan untuk memisahkan pool Teknisi Prep/Bongkar (Sortir) vs Teknisi Produksi (Produksi). |
| availability_status | ENUM(tersedia, sedang_mengerjakan, off), nullable | Status ketersediaan real-time — dibutuhkan untuk auto-distribusi Cuci (FR-1.1). |
| is_support | BOOLEAN, default false | Menandai role Support ("kernet") yang bisa ditugaskan lintas jasa (FR-5.7). |

#### 7.3.1 Rencana Alternatif — Tabel `technicians` Tersendiri

Karena Teknisi secara konsep bukan aktor sistem (tidak login), menyimpannya di `users` berpotensi mewariskan kolom tidak relevan (email, password, remember_token). Berikut trade-off kedua opsi:

| Aspek | Tetap di users | Pisah ke technicians |
|---|---|---|
| Effort implementasi | Rendah — tambah 3 kolom saja | Sedang-tinggi — migrasi tabel & FK di banyak tempat |
| Kebersihan skema | Kolom tak relevan (email, password) tetap terwarisi Teknisi | Skema bersih, hanya kolom relevan Teknisi |
| Risiko keamanan | Berpotensi ada akun Teknisi dengan email/password aktif tak sengaja | Tidak ada risiko — Teknisi tidak punya kredensial sama sekali |
| Kesiapan roadmap login Teknisi | Lebih mudah — infrastruktur login sudah ada | Perlu tabel/skema tambahan bila nanti dibutuhkan |
| Rekomendasi | Cocok untuk quick-win fase ini bila timeline ketat | Direkomendasikan jangka panjang, sejalan Admin-Only Input Model |

Jika dipisah, kolom FK pada `work_orders` (prep_washing_by, prod_sol_by, technician_production_id, dll.) dan `work_order_services.technician_id` perlu diarahkan ulang ke `technicians.id`; kolom milik Admin (created_by, warehouse_qc_by, qc_final_by, dll.) tetap mengarah ke `users.id`.

### 7.4 Ringkasan GAP Skema Database `SRS`

| Tabel Terkait | Temuan Gap | Rekomendasi |
|---|---|---|
| work_orders | Kolom `perlu_bongkar` dan `perlu_belanja` (boolean) belum ada. | Tambahkan 2 kolom tinyint(1), atau konfirmasi apakah sudah diwakili kolom lain. |
| material_requests | Status hanya sampai PURCHASED, belum ada status "material diterima". | Tambahkan status baru RECEIVED pada enum, atau gunakan `material_reservations.status` sebagai penanda — perlu keputusan Analyst. |
| work_order_revisions | Belum ada kolom eksplisit pembeda rework QC Produksi vs QC Akhir. | Tambahkan kolom `qc_stage` (ENUM: PRODUKSI, AKHIR). |
| users (role=technician) | Belum ada kolom `pool` dan `availability_status` eksplisit; bercampur dengan atribut login yang tidak relevan. | Opsi A: tambah kolom di users. Opsi B (direkomendasikan jangka panjang): pisah ke tabel technicians. |
| (tabel baru) | Tidak ada tabel/field kapasitas rak — hanya `storage_rack_code` (varchar bebas). | Buat tabel `racks` (kode rak, lokasi, kapasitas opsional). |
| (tabel baru) | Tidak ada tabel Surat Jalan untuk 3 titik perpindahan. | Buat tabel `surat_jalan` (work_order_id, titik_perpindahan, tanggal_pengiriman, estimasi, nama_pembawa, dicetak_pada). |

---

## 8. Kebutuhan Non-Fungsional `PRD` `SRS`

| Aspek | Kebutuhan |
|---|---|
| Security | Autentikasi OAuth2/token-based dengan pembedaan sesi berdasarkan `workshop_type` (Sortir/Produksi) sejak login. |
| Performance | Latensi API maksimal 2 detik. |
| Architecture | Shared Database Architecture untuk modul internal Workshop & sistem pusat non-Finlog. Integrasi Finlog terpisah sepenuhnya via REST API + Webhook (lihat Bagian 6). |
| Platform | Progressive Web App (PWA), mobile-first, optimal untuk tablet/smartphone. |
| Usability | Tombol aksi minimal 48×48px; dark mode kontras tinggi; Teknisi tidak menggunakan sistem — UI dirancang untuk Admin. |
| Reliability | Background job harian untuk mengecek timeout Rak FU (5 hari) dan memicu auto-lanjut SPK. |
| Auditability | Setiap aksi penting (ACC, konfirmasi jasa oleh Admin, hasil QC, override, Bypass Servis beserta alasannya) tercatat dengan jejak waktu dan pelaku. |
| Integration | Integrasi Finlog untuk Surat Pengajuan Belanja: REST API (Workshop→Finlog) untuk pengajuan, Webhook (Finlog→Workshop) untuk sinkronisasi status & trigger otomasi OTW Produksi. Kontrak endpoint/header/payload final — lihat Bagian 6. |

---

## 9. Ringkasan Gerbang Validasi Sistem (Gates) `PRD` `SRS`

Tabel berikut merangkum seluruh gerbang (gate) yang wajib diimplementasikan sebagai validasi keras (hard-block) maupun peringatan (soft-warning), konsisten antara PRD dan SRS.

| Gerbang | Aturan | Tipe | Catatan |
|---|---|---|---|
| ACC Inbound | Tombol Approve/Release aktif hanya setelah manifest & distribusi teknisi terkonfirmasi | HARD-BLOCK | Tidak memeriksa stok material |
| Selesai PREP (Cuci) | SPK tidak dapat masuk Sortir sebelum Cuci ditandai selesai oleh Admin Sortir | HARD-BLOCK | Admin-only |
| Klasifikasi Bongkar/Belanja | SPK tidak dapat keluar dari stage Sortir sebelum field Perlu Bongkar & Perlu Belanja terisi | HARD-BLOCK | — |
| Selesai Bongkar | Jika Perlu Bongkar=Ya, SPK tidak bisa lanjut sebelum Bongkar dicatat selesai oleh Admin Sortir | HARD-BLOCK | — |
| Rak Tunggu Belanja | Menolak OTW Produksi jika Perlu Belanja=Ya dan stok belum mencukupi | HARD-BLOCK | — |
| Kapasitas Rak | Peringatan saat SPK masuk rak yang sudah melebihi kapasitas | SOFT-WARNING | Bukan penolakan keras |
| ACC Jasa (PROD) | Setiap jasa selesai harus di-ACC Admin Produksi sebelum jasa berikutnya dimulai | HARD-BLOCK | Admin-only |
| QC Produksi | SPK hanya masuk QC Produksi setelah seluruh jasa selesai & ter-ACC | HARD-BLOCK | Tidak ada QC parsial per jasa |
| Status ke Post | SPK wajib berstatus POST sebelum diproses QC Akhir | HARD-BLOCK | Tidak ada shortcut |
| Outbound | Menolak jika SPK yang QC Akhir-nya belum PASS dimasukkan ke Manifest Outbound | HARD-BLOCK | Error alert |

---

## 10. Metrik Keberhasilan (Success Metrics) `PRD`

- **Daily Throughput** — jumlah SPK yang berhasil diselesaikan (kembali ke gudang/office) per hari.
- **Average Lead Time per kategori** — rata-rata waktu SPK dari inbound hingga selesai, dipecah per kategori.
- **Active Bottleneck Indicator** — jumlah SPK aktif di Rak FU dan Rak Tunggu Belanja; target penurunan signifikan dari kondisi saat ini (~700 SPK tertahan).
- **Rack Capacity Indicator** — proporsi rak berstatus merah dari total rak aktif.
- **Rework Rate** — persentase SPK yang mengalami rework QC Produksi dan/atau QC Akhir.
- **SLA Compliance Fast Track** — persentase SPK Fast Track selesai dalam 10 hari.
- **Waktu proses Surat Pengajuan Belanja** — dari diajukan hingga material diterima & material dimasukkan ke SPK yang diajukan untuk belanja.

---

## 11. Asumsi, Pertanyaan Terbuka & Koreksi Lapangan

### 11.1 Pertanyaan Terbuka yang Masih Berlaku `PRD` `SRS`

| Topik | Pertanyaan / Hal yang Perlu Diklarifikasi |
|---|---|
| Override Urutan Pengerjaan | Apakah override bisa mengubah teknisi assigned, urutan stasiun, atau keduanya? Apakah perlu approval tambahan? |
| Kapasitas Rak | Threshold pasti kapan indikator berubah merah perlu dikonfirmasi ke tim workshop. |
| Sizing Sistem | Jumlah pasti jasa dan teknisi yang tersedia saat ini perlu didata untuk sizing. |
| Bottleneck Upper | Kapasitas stasiun Upper sebagai bottleneck utama — perlu masuk sebagai prioritas evaluasi kapasitas rak. |
| Field Surat Jalan | Konfirmasi field "Estimasi" (estimasi apa?) dan kemungkinan ada field tambahan sesuai kebutuhan operasional. |
| Distribusi Terjadwal (Rounds) | Jumlah putaran per hari dan jadwal pasti penugasan teknisi Produksi masih perlu didetailkan. |
| Definisi "Selesai" untuk Teknisi | Karena Teknisi tidak mencatat sendiri — bagaimana Admin Sortir/Produksi memverifikasi bahwa pekerjaan fisik benar-benar selesai sebelum mencatat di sistem? Apakah ada SOP fisik pendamping? |
| Penanda "Material Diterima" | Perlu diputuskan mekanisme penanda: status baru RECEIVED di material_requests, atau memanfaatkan status material_reservations yang sudah ada. |
| Kolom Aktor pada work_orders | Perlu dikonfirmasi apakah kolom prep_washing_by, prod_sol_by, technician_production_id, dsb. mencatat ID Admin (sesuai Admin-Only Input Model) atau ID Teknisi — menentukan apakah perlu diarahkan ulang ke tabel technicians baru. |
| Tabel technicians vs kolom di users | Perlu keputusan Analyst/Product apakah data Teknisi tetap di users (quick-win) atau dipisah ke technicians (lebih bersih, sejalan Admin-Only Input Model) — lihat analisis trade-off Bagian 7.3.1. |

### 11.2 Topik yang Sudah Diselesaikan `SRS` (dahulu Open Question di PRD) — `KOREKSI`

**Integrasi Teknis Finlog — SELESAI.** PRD Bagian 10 masih menyebut skema integrasi (API/webhook/shared database) sebagai "perlu dibahas dengan tim Finlog". SRS telah menetapkan ini sebagai Keputusan Arsitektur Final: pola hybrid REST API (Workshop→Finlog) + Webhook (Finlog→Workshop), lengkap dengan kontrak endpoint, header, dan payload (lihat Bagian 6).

**"Auto-create SPK dari belanja" — DIKOREKSI.** BRD 4.5b dan Diagram 3 PRD menyebut SPK "auto-create bila belum ada" saat material diterima. Klarifikasi lapangan (SRS FR-4.5) menegaskan SPK tidak pernah dibuat otomatis dari Surat Pengajuan Belanja — SPK sudah tercatat sejak dari Office. Istilah yang benar: "auto-link & auto-lanjut status". Dokumen BRD/PRD sumber perlu diperbarui secara resmi agar tidak lagi menyebut "auto-create".

**Keberadaan data Teknisi — DIKOREKSI.** Draf SRS sebelumnya sempat mengasumsikan perlu tabel `technicians` baru dari nol. Setelah struktur tabel `users` diperiksa langsung, ternyata data Teknisi sudah ada sebagai baris `role='technician'` pada tabel `users` yang sama. Rancangan tabel `technicians` sebagai tabel wajib baru tidak lagi diperlukan — direvisi menjadi salah satu opsi (lihat Bagian 7.3.1), bukan keharusan.

---

## 12. Roadmap Pengembangan Selanjutnya `PRD` `SRS`

Item berikut disepakati berada di luar lingkup fase ini:

- Input SPK Mandiri oleh Workshop — pendaftaran pesanan langsung tanpa manifest inbound.
- Hak akses granular per role — menggantikan skema akses seragam fase ini.
- Akses sistem untuk Teknisi — jika di masa depan Teknisi perlu mencatat sendiri kemajuan pengerjaan.
- Angka kapasitas maksimal presisi per kode rak — melengkapi indikator warna fase ini.
- Detail Distribusi Terjadwal (Rounds) untuk penugasan teknisi Produksi.

---

## 13. Glosarium `PRD` `SRS`

| Istilah | Keterangan |
|---|---|
| SPK | Surat Perintah Kerja — dokumen kerja per unit sepatu/barang. |
| ACC | Approval/persetujuan oleh Admin Workshop untuk melepas SPK ke tahap kerja berikutnya. |
| FU / Rak FU | Follow Up — status/rak untuk SPK yang tertahan karena kendala teknis, gagal QC 3x, atau menunggu respons customer. |
| OTO | Kategori SPK prioritas komersial (One Time Offer). |
| Bongkar | Proses pembongkaran/pengecekan material fisik; dilakukan setelah klasifikasi Sortir, bukan saat kedatangan. |
| OTW | On the Way — status SPK dalam perpindahan fisik antar workshop. |
| Post | Area di Workshop Sortir yang menampung SPK hasil kiriman dari Produksi, sebelum QC Akhir. |
| Surat Jalan | Dokumen serah-terima fisik SPK antar tahap (Sortir/Produksi/Post). |
| Finlog | Sistem finansial-logistik eksternal untuk pengajuan dan realisasi belanja material; basis data terpisah, integrasi via REST API + Webhook. |
| PREP | Lifecycle stage pertama: mencakup Cuci saja. |
| SORTIR | Lifecycle stage kedua: klasifikasi material, alokasi teknisi, dan Bongkar (jika diperlukan). |
| PROD | Lifecycle stage ketiga: pengerjaan multi-jasa di Workshop Produksi. |
| QC | Lifecycle stage keempat: Quality Control Produksi per-SPK setelah seluruh jasa selesai. |
| POST | Lifecycle stage kelima: QC Akhir di area Post sebelum outbound. |
| Admin-Only Input | Prinsip desain: hanya Admin yang dapat melakukan input ke sistem; Teknisi mengerjakan fisik tanpa akses sistem. |
| RL / Sistem | Penanda alur pada dokumen sumber: RL = dikerjakan manusia (real life); Sistem = otomatis oleh aplikasi. |
| PWA | Progressive Web App. |
| SRS | Software Requirements Specification. |

**Referensi dokumen sumber:** BRD Workshop Sortir & Produksi v5.1 (7 Agustus 2026) · PRD Workshop Sortir & Produksi v1.1 (8 Agustus 2026) · SRS Workshop Sortir & Produksi v1.0 (10 Agustus 2026) · Skema database eksisting (phpMyAdmin) — grup tabel `material_*` dan `work_order_*`.

---

*Sistem Manajemen Workshop Terintegrasi — Sortir & Produksi | Dokumen Gabungan PRD + SRS*

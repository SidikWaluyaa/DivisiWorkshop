<style>
  @media print { @page { size: A4; margin: 15mm 15mm 20mm 15mm; } }
  body { font-family: 'Plus Jakarta Sans', 'Segoe UI', Roboto, Arial, sans-serif; color: #0f172a; font-size: 10pt; line-height: 1.6; }
  h1 { font-size: 18pt; color: #1e1b4b; border-bottom: 3px solid #4f46e5; padding-bottom: 6px; margin-top: 0; }
  h2 { font-size: 13pt; background: #f8fafc; border-left: 4px solid #4f46e5; padding: 6px 12px; margin-top: 22px; border-radius: 0 6px 6px 0; page-break-after: avoid; }
  h3 { font-size: 11pt; color: #3730a3; margin-top: 16px; page-break-after: avoid; }
  h4 { font-size: 10pt; color: #475569; border-left: 3px solid #cbd5e1; padding-left: 8px; page-break-after: avoid; }
  table { width: 100%; border-collapse: collapse; margin: 10px 0 16px; font-size: 9pt; page-break-inside: avoid; }
  th { background-color: #f1f5f9 !important; font-weight: 800; text-align: left; padding: 7px 10px; border: 1px solid #cbd5e1; -webkit-print-color-adjust: exact; }
  td { padding: 6px 10px; border: 1px solid #cbd5e1; vertical-align: top; }
  code { font-family: 'JetBrains Mono', monospace; font-size: 8.5pt; background: #f1f5f9; padding: 2px 5px; border-radius: 4px; }
  pre { background: #0f172a; color: #f8fafc; padding: 12px; border-radius: 8px; font-size: 8.5pt; page-break-inside: avoid; }
  pre code { background: transparent; color: inherit; padding: 0; }
  .page-break { page-break-before: always; }
  .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 18px; }
  .kpi-card { border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; background: #f8fafc !important; text-align: center; -webkit-print-color-adjust: exact; }
  .kpi-num { font-size: 16pt; font-weight: 900; color: #4f46e5; }
  .kpi-label { font-size: 8pt; font-weight: 700; color: #64748b; text-transform: uppercase; }
  .signature-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 30px; page-break-inside: avoid; }
  .signature-box { border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px; text-align: center; }
</style>

# 🎯 RENCANA KERJA PAPER PROJECT — NOVEMBER 2026
**Judul Proyek:** Optimasi Kecepatan Database & Perbaikan N+1 Query di Seluruh Divisi  
**Periode Pelaksanaan:** 2 November 2026 s/d 30 November 2026 (25 hari kerja, Senin–Sabtu)  
**Deadline:** 30 November 2026  
**Penanggung Jawab:** Sidik Waluya — Full Stack Developer, Divisi Data Analyst & IT  
**Dokumen Project Paper Resmi:** `docs/project_papers/PP_Sidik_OptimasiQueryDB_v1_2026-11-02.md` (dan versi PDF)  
**Status Dokumen:** 📝 Rancangan Disusun (Siap Eksekusi)

---

## 📊 1. LATAR BELAKANG & TUJUAN

Seiring bertambahnya modul (R&D, QR Scan Mobile, Manajemen Brand) dan data SPK, beberapa halaman di setiap divisi terasa makin lambat saat dibuka. Salah satu penyebab paling umum di aplikasi Laravel adalah **N+1 query**: halaman mengambil daftar data (misalnya 50 SPK) lalu menjalankan 1 query tambahan untuk **setiap baris** (customer, layanan, teknisi, foto, dan lainnya). Satu halaman bisa menjalankan ratusan query padahal cukup beberapa query saja.

**Kondisi awal (hasil pengecekan 2 Nov 2026):** sistem **belum memiliki alat pendeteksi query** sama sekali. Tidak ada `preventLazyLoading`, Laravel Debugbar, Telescope, maupun logging `DB::listen`. Artinya, jumlah query per halaman saat ini belum pernah diukur.

### 🎯 Tujuan
1. **Mengukur** jumlah query dan waktu muat setiap halaman utama per divisi (baseline *before*).
2. **Menemukan** seluruh N+1 query dan query berat di halaman-halaman tersebut.
3. **Memperbaiki** dengan eager loading, `withCount`, pemilihan kolom, dan index database **tanpa mengubah perilaku/tampilan halaman**.
4. **Membuktikan hasil** lewat laporan perbandingan *before vs after* per halaman.

<div class="kpi-grid">
  <div class="kpi-card"><div class="kpi-label">Durasi</div><div class="kpi-num">25 Hari</div></div>
  <div class="kpi-card"><div class="kpi-label">Breakdown</div><div class="kpi-num">8 Tugas</div></div>
  <div class="kpi-card"><div class="kpi-label">Cakupan</div><div class="kpi-num">6 Area</div></div>
  <div class="kpi-card"><div class="kpi-label">Perubahan Perilaku</div><div class="kpi-num">0</div></div>
</div>

---

## 🗺️ 2. BREAKDOWN 8 TUGAS (URUT SESUAI SIDEBAR)

| No | Tugas | Periode | Deadline | Bobot |
| :-: | :--- | :--- | :--- | :-: |
| 1 | **Setup Alat Deteksi & Baseline Pengukuran** | 2 – 3 Nov | **3 Nov** | 12,5% |
| 2 | **Optimasi Divisi Customer Service (CS)** | 4 – 6 Nov | **6 Nov** | 12,5% |
| 3 | **Optimasi Divisi Gudang** | 7 – 11 Nov | **11 Nov** | 12,5% |
| 4 | **Optimasi Divisi Workshop** (termasuk PWA, Mobile & R&D) | 12 – 17 Nov | **17 Nov** | 12,5% |
| 5 | **Optimasi Divisi Finance** | 18 – 20 Nov | **20 Nov** | 12,5% |
| 6 | **Optimasi Divisi Customer Experience (CX/CC)** | 21 – 24 Nov | **24 Nov** | 12,5% |
| 7 | **Optimasi Master Data & System** | 25 – 27 Nov | **27 Nov** | 12,5% |
| 8 | **Evaluasi, Laporan Before/After & Serah Terima** | 28 – 30 Nov | **30 Nov** | 12,5% |

> Progres dihitung dari tugas yang tuntas: setiap tugas selesai menambah **12,5%**. Workshop diberi waktu paling panjang (5 hari) karena memiliki halaman terbanyak dan paling sering dipakai.

---

<div class="page-break"></div>

## 🔍 3. METODOLOGI KERJA (BERLAKU UNTUK SETIAP DIVISI)

Setiap halaman diproses dengan 4 langkah yang sama:

```
[1. UKUR]  →  [2. TEMUKAN]  →  [3. PERBAIKI]  →  [4. BUKTIKAN]
 baseline      N+1 & query      eager loading,     ukur ulang &
 query + ms    berat            withCount, index   cek tampilan
```

### 3.1 Alat Deteksi (hanya di lingkungan lokal)
| Alat | Fungsi | Dampak ke Production |
| :--- | :--- | :--- |
| `Model::preventLazyLoading(! app()->isProduction())` | Memunculkan error di lokal setiap kali terjadi lazy loading (indikasi N+1) | **Tidak aktif** di production |
| Laravel Debugbar (`require-dev`) | Menampilkan jumlah query, query duplikat, dan waktu tiap query per halaman | **Tidak terpasang** di production |
| Lembar Baseline (`.md`) | Mencatat jumlah query & waktu muat sebelum dan sesudah perbaikan | — |

### 3.2 Pola Perbaikan yang Digunakan
| Masalah | Solusi |
| :--- | :--- |
| Relasi dipanggil di dalam loop/Blade | `->with([...])` / `->load([...])` |
| Menghitung jumlah relasi per baris | `->withCount([...])` / `->withSum([...])` |
| Mengambil seluruh kolom padahal hanya butuh sedikit | `->select([...])` & eager load dengan kolom spesifik |
| Query yang sama dijalankan berulang (Livewire render/poll) | `#[Computed]` / cache per request |
| Filter/sort lambat pada kolom tanpa index | Migration penambahan index (non-destruktif) |

### 3.3 Aturan Keamanan Perubahan
- **Tidak ada perubahan tampilan maupun logika bisnis.** Yang diubah hanya cara mengambil data.
- Setiap perbaikan dicek ulang: data yang tampil harus **sama persis** dengan sebelum perbaikan.
- Migration index bersifat **tambahan saja** (tidak mengubah/menghapus kolom) dan diuji di lokal sebelum dijalankan di production aaPanel.

---

<div class="page-break"></div>

## 📋 4. CAKUPAN HALAMAN PER DIVISI (BERDASARKAN SIDEBAR)

### Tugas 2 — Customer Service (CS)
CS Dashboard · Laporan Performa · Konsultasi · Follow-up · Closing · Follow Up Closing · Kiriman SPK Pending · Data SPK · Greeting Chat · Galeri After Photo

### Tugas 3 — Gudang
- **Gudang:** Penerimaan Outbound · Belanja Gudang · Barang Keluar · Riwayat Mutasi
- **Operasional:** Dashboard Gudang · Penyimpanan Rak · Riwayat Pengambilan · Penerimaan · Assessment · Logistik Manifest · Gudang Manual · Rak Manual · Finish · Pengiriman · Label Custom

### Tugas 4 — Workshop (PWA Workshop, Mobile Kontrol Teknisi & R&D)
- **Dashboard & Tracking:** Dashboard Utama · Fast Track · Lacak SPK Internal · Lacak Jasa Workshop
- **Inbound/Outbound:** Manifest Inbound Masuk · Staging & Manifest Outbound
- **Stasiun 1–6:** Persiapan · Sortir & Penilaian · SJ Sortir→Produksi · Produksi · SJ Produksi→QC · Quality Control
- **Layanan & Teknisi:** Asisten Data Teknisi · Manajemen Data Teknisi · Skill & Tarif Jasa · Katalog Layanan · Laporan Performa
- **Logistik:** Info Keterlambatan · Surat Jalan Workshop
- **Revisi & Garansi:** Revisi Teknik · Follow-up Kendala · Sistem Garansi · List Garansi · Stasiun OTO · Divisi R&D
- **Stok & Material:** Stok & Katalog · Permintaan · Pengeluaran · Riwayat Mutasi Stok
- **Mobile:** Pusat Kontrol Produksi (`/m/production`) · Detail Kontrol SPK (`/m/spk/{spk}`)

### Tugas 5 — Finance
Dashboard Finance · Waiting Payment · Finance Transaksi · Data Invoice · Transaksi Batal · Audit Bayar CS · Input Pembayaran · Import Mutasi · Verifikasi Mutasi

### Tugas 6 — Customer Experience (CX/CC)
CC Dashboard · Follow Up · History Resolusi · Kolam OTO · Konfirmasi After · Monitoring Kirim · Overdue SLA · Inbox Klaim Garansi · Alamat Terverifikasi · Analisis QC Awal · Komplain

### Tugas 7 — Master Data & System
Master Customer · Manajemen Promo · Rilis & Pengumuman · Pengguna · Log Aktivitas · Laporan · KPI · Kesehatan Data · Supply Chain Portal · Pengajuan Material · Pembelian

> Menu umum (Dashboard, Internal Tracking, Tracking Jasa) diukur pada Tugas 1 sebagai bagian dari baseline.

---

<div class="page-break"></div>

## 📅 5. RENCANA HARIAN

| Tanggal | Hari | Tugas | Rencana Kegiatan |
| :--- | :--- | :-: | :--- |
| 2 Nov | Senin | 1 | Penyusunan rancangan Paper Project, pengecekan alat deteksi yang ada, pemetaan halaman per divisi dari sidebar |
| 3 Nov | Selasa | 1 | Pasang Debugbar (dev) & `preventLazyLoading` (lokal), buat lembar baseline, ukur menu umum |
| 4 Nov | Rabu | 2 | Ukur baseline seluruh halaman CS, catat N+1 |
| 5 Nov | Kamis | 2 | Perbaikan N+1 halaman Leads (Konsultasi, Follow-up, Closing) & Data SPK |
| 6 Nov | Jumat | 2 | Perbaikan Dashboard/Analytics CS, ukur ulang, cek tampilan ✅ |
| 7 Nov | Sabtu | 3 | Ukur baseline seluruh halaman Gudang |
| 9 Nov | Senin | 3 | Perbaikan Penerimaan, Assessment, Penyimpanan Rak |
| 10 Nov | Selasa | 3 | Perbaikan Finish, Pengiriman, Manifest, Riwayat Pengambilan |
| 11 Nov | Rabu | 3 | Perbaikan Belanja/Barang Keluar/Mutasi, ukur ulang ✅ |
| 12 Nov | Kamis | 4 | Ukur baseline seluruh halaman Workshop & Mobile |
| 13 Nov | Jumat | 4 | Perbaikan Dashboard Workshop, Fast Track, Info Keterlambatan |
| 14 Nov | Sabtu | 4 | Perbaikan Stasiun 1–6 (Persiapan, Sortir, Produksi, QC) |
| 16 Nov | Senin | 4 | Perbaikan Mobile Kontrol Teknisi (polling 15 detik), R&D, Revisi & Garansi |
| 17 Nov | Selasa | 4 | Perbaikan Teknisi, Stok & Material, ukur ulang ✅ |
| 18 Nov | Rabu | 5 | Ukur baseline seluruh halaman Finance |
| 19 Nov | Kamis | 5 | Perbaikan Waiting Payment, Transaksi, Invoice, Audit Bayar CS |
| 20 Nov | Jumat | 5 | Perbaikan Dashboard & Mutasi, ukur ulang ✅ |
| 21 Nov | Sabtu | 6 | Ukur baseline seluruh halaman CX |
| 23 Nov | Senin | 6 | Perbaikan Follow Up, OTO, Konfirmasi After, Monitoring Kirim |
| 24 Nov | Selasa | 6 | Perbaikan Overdue SLA, Klaim Garansi, Komplain, ukur ulang ✅ |
| 25 Nov | Rabu | 7 | Ukur baseline Master Data & System |
| 26 Nov | Kamis | 7 | Perbaikan Master Customer, Pengguna, Log Aktivitas, Laporan, KPI |
| 27 Nov | Jumat | 7 | Perbaikan Supply Chain, Pembelian, Kesehatan Data, ukur ulang ✅ |
| 28 Nov | Sabtu | 8 | Uji regresi menyeluruh, kompilasi tabel before/after |
| 30 Nov | Senin | 8 | Deploy final ke production aaPanel, laporan akhir & serah terima ✅ |

---

## 🛡️ 6. RISIKO & MITIGASI

| ID | Risiko | Tingkat | Mitigasi |
| :-: | :--- | :-: | :--- |
| R-01 | Data yang tampil berubah/hilang setelah query diubah | TINGGI | Bandingkan tampilan & jumlah data sebelum/sesudah di setiap halaman |
| R-02 | `preventLazyLoading` ikut aktif di production dan memunculkan error ke user | TINGGI | Dikunci dengan `! app()->isProduction()`; Debugbar hanya `require-dev` |
| R-03 | Penambahan index memperlambat proses simpan data / mengunci tabel besar saat migrate | SEDANG | Index hanya untuk kolom filter/sort yang terbukti lambat; migrate di luar jam sibuk |
| R-04 | Eager loading berlebihan malah memperberat memori | RENDAH | Eager load hanya relasi yang benar-benar dipakai, dengan kolom spesifik |
| R-05 | Data lokal terlalu sedikit sehingga N+1 tidak terasa | SEDANG | Ukur berdasarkan **jumlah query** (bukan hanya waktu), karena jumlah query tetap terlihat meski datanya sedikit |

---

## ✅ 7. OUTPUT AKHIR YANG DISERAHKAN (30 NOV)
1. Lembar baseline & hasil: tabel jumlah query dan waktu muat **before vs after** untuk setiap halaman yang dioptimasi.
2. Daftar N+1 yang ditemukan dan cara perbaikannya per divisi.
3. Migration index tambahan (jika diperlukan) yang sudah dijalankan di production.
4. Alat deteksi `preventLazyLoading` tetap aktif di lokal sebagai pencegah N+1 baru di pengembangan berikutnya.

---

<div class="signature-grid">
  <div class="signature-box"><strong>Disusun Oleh:</strong><br><br><br><br><strong>Sidik Waluya</strong><br><span>Full Stack Developer</span><br><small>2 November 2026</small></div>
  <div class="signature-box"><strong>Diverifikasi Oleh:</strong><br><br><br><br><strong>________________</strong><br><span>Kepala Divisi Data Analyst & IT</span><br><small>Tanggal: ________</small></div>
  <div class="signature-box"><strong>Disetujui Oleh:</strong><br><br><br><br><strong>________________</strong><br><span>Manajemen</span><br><small>Tanggal: ________</small></div>
</div>


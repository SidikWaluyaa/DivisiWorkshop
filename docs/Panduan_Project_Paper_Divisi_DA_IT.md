# Panduan Penyusunan Project Paper

**Divisi Data Analyst & IT** · Versi 1.0 · 22 Oktober 2026
Disusun oleh: Vael (Full-Stack Developer)

> **Ringkasan satu menit**
>
> - Setiap proyek wajib punya satu project paper sebelum dikerjakan, maksimal 2 halaman.
> - Paper berisi 9 bagian yang urutannya sama untuk semua anggota tim.
> - Setiap tugas, kebutuhan, dan keputusan wajib punya nama PIC dan tanggal.
> - Ditulis dengan bahasa sehari-hari, karena pembacanya owner dan manajemen.
> - Paper disimpan sebagai PDF di Google Drive, dan link-nya dicantumkan di spreadsheet Paper Project.

## Daftar Isi

1. [Kenapa Ada Project Paper](#1-kenapa-ada-project-paper)
2. [Aturan Umum](#2-aturan-umum)
   - [Empat status yang dipakai](#empat-status-yang-dipakai)
   - [Penyimpanan dan penamaan file](#penyimpanan-dan-penamaan-file)
3. [Kriteria Prioritas](#3-kriteria-prioritas)
4. [Isi Project Paper: 9 Bagian](#4-isi-project-paper-9-bagian)
   1. [Identitas](#41-identitas)
   2. [Tim & Peran](#42-tim--peran)
   3. [Status Sekarang](#43-status-sekarang)
   4. [Tujuan & Dampak](#44-tujuan--dampak)
   5. [Prioritas](#45-prioritas)
   6. [Ruang Lingkup](#46-ruang-lingkup)
   7. [Breakdown Pekerjaan](#47-breakdown-pekerjaan)
   8. [Kriteria Selesai](#48-kriteria-selesai)
   9. [Keputusan & Kebutuhan dari Pihak Lain](#49-keputusan--kebutuhan-dari-pihak-lain)
5. [Checklist Sebelum Dikirim](#5-checklist-sebelum-dikirim)
6. [Lampiran: Template Kosong](#lampiran-template-kosong)

---

## 1. Kenapa Ada Project Paper

Pada meeting divisi Data Analyst & IT bersama owner tanggal 21 Oktober 2026, ditetapkan aturan baru: setiap proyek wajib dirancang dulu dalam project paper sebelum dikerjakan.

Tujuannya ada tiga:

- **Prioritas kerja jelas.** Semua orang tahu proyek mana yang dikerjakan sekarang, mana yang dijadwalkan, dan kenapa.
- **Report harian naik kelas.** Dari sekadar formalitas menjadi alat koordinasi dan pengambilan keputusan.
- **Setiap pembahasan menghasilkan PIC dan deadline.** Tidak ada pekerjaan atau keputusan yang menggantung tanpa penanggung jawab.

---

## 2. Aturan Umum

| Aturan | Penjelasan |
|---|---|
| **Satu proyek, satu paper** | Setiap proyek punya paper sendiri. Daftar semua proyek per orang cukup ada di Tabel 1 spreadsheet. |
| **Maksimal 2 halaman** | Paper harus bisa dibaca dalam 5 menit. Kalau lebih panjang, ringkas lagi. |
| **Bahasa non-teknis** | Hindari istilah seperti database, API, server, deploy, query. Kalau terpaksa, jelaskan dengan bahasa sehari-hari. |
| **Output, bukan aktivitas** | Tulis hasil yang bisa dilihat atau dipakai, bukan daftar kegiatan. |
| **Wajib PIC dan tanggal** | Setiap tugas, kebutuhan, dan keputusan harus punya nama PIC dan tanggal. Kalau tanggal belum pasti, tulis kapan tanggal itu akan dipastikan dan oleh siapa. |
| **Jangan mengarang** | Data yang belum diketahui ditulis "[perlu konfirmasi]" lalu dimasukkan ke bagian 9. |

### Empat status yang dipakai

Status di paper dan di spreadsheet harus memakai empat pilihan yang sama:

| Status | Artinya |
|---|---|
| **Backlog** | Sudah direncanakan tetapi belum mulai dikerjakan. |
| **Hold** | Pernah dikerjakan, lalu ditunda karena alasan tertentu. |
| **Sedang Dikerjakan** | Sedang berjalan saat ini. |
| **Selesai (Monitoring)** | Sudah memenuhi kriteria selesai (bagian 8) dan sekarang dipantau atau dirawat. |

### Penyimpanan dan penamaan file

- Paper disimpan sebagai PDF di Google Drive, dalam satu folder per anggota tim.
- Nama file: `PP_[Nama]_[NamaProyek]_v[nomor]_[tanggal].pdf`
  Contoh: `PP_Vael_WebUtama_v1_2026-10-22.pdf`
- Link PDF dicantumkan di Tabel 1 spreadsheet Paper Project pada baris proyeknya.
- Spreadsheet menjadi acuan status dan deadline terbaru. PDF diperbarui (nomor versi naik) setiap ada perubahan besar, misalnya ruang lingkup berubah atau deadline mundur.

---

## 3. Kriteria Prioritas

Posisi proyek di matriks prioritas (bagian 5 paper) ditentukan dengan kriteria yang sama untuk semua orang, bukan perasaan. Cukup satu kriteria terpenuhi.

| Kode | Important jika... |
|---|---|
| **I1** | Berdampak langsung ke pemasukan atau ke pelanggan |
| **I2** | Ditetapkan owner atau manajemen sebagai arah utama |
| **I3** | Menyangkut uang perusahaan atau keamanan data |
| **I4** | Ditunggu divisi lain agar pekerjaan mereka bisa berjalan |

| Kode | Urgent jika... |
|---|---|
| **U1** | Ada tenggat atau tanggal mulai pasti dalam 2 minggu ke depan |
| **U2** | Ada pelanggan atau divisi yang tertahan bila tidak dikerjakan |
| **U3** | Ada kerugian atau risiko yang bertambah setiap hari |

| Kuadran | Syarat | Artinya |
|---|---|---|
| **Kerjakan Sekarang** | Urgent dan important | Dikerjakan minggu ini |
| **Jadwalkan** | Important saja | Diberi jadwal, dikerjakan setelah yang mendesak |
| **Delegasikan** | Urgent saja | Dikerjakan cepat atau diserahkan ke orang lain |
| **Cadangan** | Tidak keduanya | Dikerjakan saat ada waktu luang atau ada pemicunya |

---

## 4. Isi Project Paper: 9 Bagian

Urutan bagian di bawah ini wajib sama untuk semua anggota tim, supaya owner bisa membaca paper siapa pun dengan cara yang sama.

### 4.1 Identitas

**Menjawab pertanyaan:** *Ini proyek apa, dari siapa, dan sekarang di mana posisinya?*

**Yang diisi:**

- **Nama proyek** dan **pemberi tugas**
- **Divisi terkait:** divisi mana yang terkena dampak atau ikut terlibat
- **Jenis:** eksternal (dipakai publik atau pelanggan) atau internal (dipakai karyawan)
- **Status:** salah satu dari empat status
- **Periode proyek:** tanggal mulai sampai target selesai
- **Tanggal update terakhir**
- **Cek duplikasi (satu baris):** pastikan tidak ada proyek, laporan, atau sistem lain (termasuk di ERP) yang fungsinya sama, lalu sebutkan proyek yang berkaitan dan pembagian kerjanya

**Contoh:**

> "Tidak ada duplikasi. Terkait dengan fitur lacak pesanan di ERP: ERP dipakai staf, web ini dipakai pelanggan, datanya dari sumber yang sama."

### 4.2 Tim & Peran

**Menjawab pertanyaan:** *Siapa saja yang terlibat, dan masing-masing mengerjakan bagian apa?*

**Yang diisi:**

- Tulis dalam tabel: Nama, Divisi, Peran, Bagian yang dikerjakan.
- Pilihan peran: **PIC** (penanggung jawab utama), **Pelaksana**, **Pemberi data/bahan**, **Penguji**, **Pengambil keputusan**.

**Kenapa penting:** Pembaca langsung tahu harus bertanya ke siapa untuk urusan apa. Satu orang boleh memegang lebih dari satu peran, tetapi PIC hanya satu orang.

**Contoh:**

| Nama | Divisi | Peran | Bagian yang dikerjakan |
|---|---|---|---|
| Vael | Data Analyst & IT | PIC | Memperbaiki error yang ditemukan di sistem |
| Bu Eva | Manajemen | Pengambil keputusan, Penguji | Memberi tugas, mencoba alur keuangan, menentukan prioritas perbaikan |

### 4.3 Status Sekarang

**Menjawab pertanyaan:** *Kalau owner hanya membaca satu bagian, apa kondisi proyek saat ini?*

**Yang diisi:**

- Satu sampai dua kalimat tentang hasil yang sudah bisa dilihat atau dipakai.
- Tulis output, bukan aktivitas, dan bukan persentase.

**Kenapa penting:** Owner harus bisa paham kondisi proyek dalam dua kalimat tanpa membuka tabel.

**Contoh:**

> ❌ **Salah:** "Sudah mengerjakan halaman depan 60%."
>
> ✅ **Benar:** "Bagian Layanan dan Tentang Kami sudah tampil di web; halaman depan versi baru belum."

### 4.4 Tujuan & Dampak

**Menjawab pertanyaan:** *Kenapa proyek ini dikerjakan, dan apa gunanya untuk bisnis?*

**Yang diisi:**

- **Masalah bisnis** yang ingin diselesaikan
- **Output akhir:** apa yang bisa dilihat atau dipakai saat proyek selesai
- **Dampak:** keputusan apa yang bisa diambil manajemen dari hasilnya

**Kenapa penting:** Kalau masalah bisnisnya tidak jelas, proyek ini perlu dipertanyakan sebelum dikerjakan.

**Contoh:**

> "Masalah: pelanggan harus bertanya ke CS untuk tahu posisi sepatunya. Output: halaman lacak pesanan. Dampak: pertanyaan rutin ke CS berkurang, CS bisa fokus ke closing."

### 4.5 Prioritas

**Menjawab pertanyaan:** *Proyek ini dikerjakan sekarang, dijadwalkan, didelegasikan, atau cadangan?*

**Yang diisi:**

- Kuadran (lihat [bagian 3](#3-kriteria-prioritas) panduan ini)
- Alasan dalam satu kalimat, sebutkan kode kriterianya
- Berubah jika...: kondisi yang membuat proyek naik atau turun prioritas

**Kenapa penting:** Kolom "berubah jika" menunjukkan kondisi yang membuat prioritas naik atau turun, sehingga owner tahu apa yang perlu terjadi agar proyek bergerak.

**Contoh:**

> "Jadwalkan. I1: sumber pemasukan baru; belum ada tenggat. Naik ke Kerjakan Sekarang jika klien menetapkan target waktu."

### 4.6 Ruang Lingkup

**Menjawab pertanyaan:** *Apa saja yang dikerjakan, dan apa yang tidak?*

**Yang diisi:**

- **Termasuk:** daftar pendek pekerjaan yang masuk proyek
- **Tidak termasuk:** daftar pendek hal yang sengaja tidak dikerjakan

**Kenapa penting:** Mencegah salah paham dan pekerjaan yang terus melebar. Daftar "tidak termasuk" sama pentingnya dengan daftar "termasuk".

**Contoh:**

> "Termasuk: tes dan perbaikan error Finlog. Tidak termasuk: membuat sistem Finlog baru untuk Shoepolice."

### 4.7 Breakdown Pekerjaan

**Menjawab pertanyaan:** *Apa saja yang dikerjakan, kapan, dan oleh siapa?*

**Yang diisi:**

- Tabel dengan kolom: No, Tanggal, Komponen/Tugas, Deskripsi, PIC, Deadline, Status, Keterangan.
- **Proyek 2 minggu atau kurang:** dirinci per hari.
- **Proyek lebih dari 2 minggu:** dirinci per minggu; rincian harian cukup untuk minggu yang sedang berjalan.
- Setiap baris wajib punya tanggal, PIC, dan deadline.

**Kenapa penting:** Ini inti paper dan jadwal kerja proyek. Kolom "Rencana dan Target Kerja Esok Hari" di daily report diambil dari baris yang deadline-nya besok.

**Contoh:**

| No | Tanggal | Komponen/Tugas | Deskripsi | PIC | Deadline | Status | Keterangan |
|---|---|---|---|---|---|---|---|
| 1 | 20–21 Okt | Bagian Layanan Kami | Kartu layanan dengan foto sebelum-sesudah | Vael | 21 Okt | Selesai (Monitoring) | – |
| 2 | 22–24 Okt | Halaman depan baru | Tampilan halaman depan lebih rapi | Vael | 24 Okt | Sedang Dikerjakan | Foto kantor dari Marketing |

### 4.8 Kriteria Selesai

**Menjawab pertanyaan:** *Kapan proyek ini boleh disebut "Selesai"?*

**Yang diisi:** Dua sampai empat patokan yang bisa dicek.

**Kenapa penting:** Tanpa kriteria ini, status "Selesai" hanya klaim. Tulis hal yang bisa dibuktikan atau dicek orang lain.

**Contoh:**

> ❌ **Salah:** "Sistem sudah stabil."
>
> ✅ **Benar:** "Semua alur utama dicoba tanpa error berat selama 2 minggu, dan Finance berhasil tutup buku satu periode."

### 4.9 Keputusan & Kebutuhan dari Pihak Lain

**Menjawab pertanyaan:** *Proyek ini menunggu apa, dari siapa, dan sampai kapan?*

**Yang diisi:**

- Tabel dengan kolom: Apa yang dibutuhkan, Dari siapa, Batas tanggal, Dampak kalau terlambat.
- Termasuk keputusan manajemen, data, bahan, atau persetujuan.

**Kenapa penting:** Bagian ini paling sering dipakai owner untuk mengetahui siapa yang perlu didorong. Tidak boleh ada baris tanpa nama dan tanggal.

**Contoh:**

| Apa yang dibutuhkan | Dari siapa | Batas tanggal | Dampak kalau terlambat |
|---|---|---|---|
| Hasil meeting dengan klien | Pak Dito | 24 Okt | Rancangan halaman lacak pesanan klien belum bisa dimulai |

---

## 5. Checklist Sebelum Dikirim

- [ ] Sembilan bagian terisi dan urutannya sesuai panduan.
- [ ] Panjang paper maksimal 2 halaman.
- [ ] Tidak ada istilah teknis tanpa penjelasan.
- [ ] Status sekarang berisi output, bukan aktivitas atau persentase.
- [ ] Setiap baris di breakdown dan di bagian 9 punya nama dan tanggal.
- [ ] Cek duplikasi sudah diisi.
- [ ] Status sama dengan yang tertulis di spreadsheet.
- [ ] PDF sudah diberi nama sesuai format dan link-nya dicantumkan di spreadsheet.

---

## Lampiran: Template Kosong

*Salin bagian ini untuk membuat paper baru.*

```markdown
# PROJECT PAPER – [Nama Proyek]

Penyusun: [Nama] · Versi [1] · Update terakhir: [tanggal]

## 1. Identitas

| | |
|---|---|
| **Nama proyek** | |
| **Pemberi tugas** | |
| **Divisi terkait** | |
| **Jenis** | Eksternal / Internal |
| **Status** | Backlog / Hold / Sedang Dikerjakan / Selesai (Monitoring) |
| **Periode proyek** | [tanggal mulai] – [target selesai] |
| **Cek duplikasi** | Tidak ada duplikasi. Terkait dengan [...], pembagiannya [...] |

## 2. Tim & Peran

| Nama | Divisi | Peran | Bagian yang dikerjakan |
|---|---|---|---|
| | | PIC | |
| | | | |

## 3. Status Sekarang

[1–2 kalimat output yang sudah bisa dilihat atau dipakai]

## 4. Tujuan & Dampak

| | |
|---|---|
| **Masalah bisnis** | |
| **Output akhir** | |
| **Dampak / keputusan** | |

## 5. Prioritas

| | |
|---|---|
| **Kuadran** | |
| **Alasan (kode kriteria)** | |
| **Berubah jika...** | |

## 6. Ruang Lingkup

| | |
|---|---|
| **Termasuk** | |
| **Tidak termasuk** | |

## 7. Breakdown Pekerjaan

| No | Tanggal | Komponen/Tugas | Deskripsi | PIC | Deadline | Status | Keterangan |
|---|---|---|---|---|---|---|---|
| 1 | | | | | | | |
| 2 | | | | | | | |
| 3 | | | | | | | |

## 8. Kriteria Selesai

- [patokan 1]
- [patokan 2]

## 9. Keputusan & Kebutuhan dari Pihak Lain

| Apa yang dibutuhkan | Dari siapa | Batas tanggal | Dampak kalau terlambat |
|---|---|---|---|
| | | | |
| | | | |
```

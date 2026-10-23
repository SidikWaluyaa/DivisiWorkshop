# PROJECT PAPER – QR Scan Mobile Kontrol Teknisi & Real-Time Tracking SPK

Penyusun: Sidik Waluya (Data Analyst & IT) · Versi 1 · Update terakhir: 22 Oktober 2026

## 1. Identitas

| Parameter | Keterangan |
|---|---|
| **Nama proyek** | QR Scan Mobile Kontrol Teknisi & Real-Time Tracking SPK |
| **Pemberi tugas** | Owner & Manajemen Workshop |
| **Divisi terkait** | Divisi Workshop (Produksi & Teknisi Reparasi) |
| **Jenis** | Internal (digunakan teknisi & supervisor di lantai bengkel) |
| **Status** | Sedang Dikerjakan |
| **Periode proyek** | 20 Oktober 2026 – 31 Oktober 2026 |
| **Cek duplikasi** | Tidak ada duplikasi. Terkait dengan menu produksi desktop `/production` di sistem bengkel; versi mobile ini dipakai langsung oleh teknisi di meja reparasi lewat scan kamera HP, datanya bersumber dan tersinkronisasi otomatis ke database yang sama. |

## 2. Tim & Peran

| Nama | Divisi | Peran | Bagian yang dikerjakan |
|---|---|---|---|
| **Sidik Waluya** | Data Analyst & IT | PIC & Pelaksana | Merancang alur sistem, tampilan mobile HP, dan logika pencatatan waktu otomatis |
| **Kepala Produksi** | Workshop | Pengambil keputusan & Penguji | Menyetujui perubahan template cetak SPK fisik dan mengawasi alur kerja teknisi di lantai bengkel |
| **Indra** | Workshop | Penguji & Pelaksana | Mencoba langsung kemudahan scan kamera HP dan eksekusi tombol mulai-selesai di meja reparasi |

## 3. Status Sekarang

Rancangan antarmuka mobile, peletakan QR code di kertas SPK fisik, dan logika pencatatan waktu otomatis sudah siap dieksekusi; modul kamera scanner HP dan halaman antrean saat ini sedang dalam tahap pembuatan kode.

## 4. Tujuan & Dampak

| Parameter | Penjelasan |
|---|---|
| **Masalah bisnis** | Teknisi di lantai bengkel harus berjalan bolak-balik ke meja komputer admin kasir untuk mencatat status mulai dan selesai reparasi sepatu. Akibatnya, pembaruan status sering tertunda hingga akhir shift, dan durasi kerja nyata per sepatu tidak pernah terukur secara akurat. |
| **Output akhir** | QR code khusus pada lembar cetak SPK fisik yang bisa di-scan kamera HP teknisi tanpa perlu login berulang kali, langsung memunculkan tombol kerja dan live digital stopwatch pengerjaan di layar HP. |
| **Dampak / keputusan** | Manajemen dan kepala produksi dapat melihat durasi pengerjaan nyata per teknisi, mendeteksi stasiun bengkel yang menumpuk (bottleneck), serta menetapkan standar waktu pengerjaan dan target harian yang adil. |

## 5. Prioritas

| Parameter | Penjelasan |
|---|---|
| **Kuadran** | **Kerjakan Sekarang** |
| **Alasan (kode kriteria)** | **I2** (ditetapkan manajemen sebagai arah prioritas operasional) dan **U2** (keterlambatan pencatatan manual menahan visibilitas antrean ratusan sepatu pelanggan yang sedang berjalan di bengkel). |
| **Berubah jika...** | Turun ke "Jadwalkan" bila terjadi kendala teknis mendesak pada server utama produksi bengkel yang memerlukan penanganan darurat. |

## 6. Ruang Lingkup

| Parameter | Penjelasan |
|---|---|
| **Termasuk** | 1. Penambahan QR code pada lembar cetak fisik SPK.<br>2. Halaman pengerjaan di HP saat scan QR (`/m/spk/...`) lengkap dengan tombol stasiun dan live digital stopwatch.<br>3. Dashboard pengawas mobile (`/m/production`) dengan 3 tab status (Berjalan, Antrean, Siap QC) dan tombol scan kamera HP.<br>4. Pencatatan waktu mulai, waktu selesai, dan nama teknisi secara otomatis. |
| **Tidak termasuk** | 1. Pembelian alat scanner terpisah (menggunakan kamera HP teknisi yang sudah ada).<br>2. Pembuatan tabel atau database baru (memanfaatkan kolom database yang sudah tersedia).<br>3. Pencatatan absensi kehadiran harian karyawan (hanya fokus pada pelacakan pengerjaan sepatu). |

## 7. Breakdown Pekerjaan

| No | Tanggal | Komponen/Tugas | Deskripsi | PIC | Deadline | Status | Keterangan |
|---|---|---|---|---|---|---|---|
| 1 | 20–21 Okt | Desain & Perancangan Arsitektur | Menyusun alur kerja lengkap, flowchart sistem, dan format dokumen perancangan teknis | Sidik | 21 Okt | Selesai (Monitoring) | Dokumen perancangan disetujui |
| 2 | 22–24 Okt | Halaman Mobile & Stopwatch | Membuat tampilan HP saat scan SPK beserta tombol mulai-selesai dan live digital stopwatch | Sidik | 24 Okt | Sedang Dikerjakan | Target selesai Sabtu |
| 3 | 25–27 Okt | Dashboard Pengawas & Kamera Scanner | Membuat halaman controlling supervisor dan scanner kamera HP di aplikasi mobile | Sidik | 27 Okt | Backlog | Memerlukan izin akses kamera |
| 4 | 28–30 Okt | Uji Coba Lapangan Bengkel | Mencoba scan fisik lembar SPK langsung bersama Indra di meja kerja reparasi | Sidik & Indra | 30 Okt | Backlog | Membutuhkan 10 lembar cetak SPK fisik |
| 5 | 31 Okt | Evaluasi & Serah Terima | Memastikan data waktu masuk ke komputer kantor, sosialisasi seluruh teknisi, dan laporan final | Sidik | 31 Okt | Backlog | Diserahkan ke Kepala Produksi & Manajemen |

## 8. Kriteria Selesai

- [ ] Kamera HP teknisi bisa memindai QR code pada lembar fisik SPK dan membuka halaman pengerjaan dalam waktu kurang dari 3 detik.
- [ ] Tombol "Mulai Kerja" dan "Selesaikan" di HP berhasil mencatat nama teknisi dan durasi waktu kerja nyata tanpa perlu login berulang-ulang.
- [ ] Status pekerjaan di layar komputer kantor/admin otomatis berubah secara langsung saat teknisi menekan tombol di HP.
- [ ] Telah dicoba langsung oleh Indra di lantai bengkel tanpa ada kendala operasional selama 3 hari berturut-turut.

## 9. Keputusan & Kebutuhan dari Pihak Lain

| Apa yang dibutuhkan | Dari siapa | Batas tanggal | Dampak kalau terlambat |
|---|---|---|---|
| 10 sampel lembar cetak SPK fisik untuk sesi uji coba lapangan | Admin Cetak / Kasir | 27 Okt | Pengujian scan di meja kerja bengkel tertunda |
| Waktu luang teknisi untuk pendampingan uji coba HP | Indra (Teknisi) | 28 Okt | Uji coba ergonomi dan stopwatch di lantai bengkel belum bisa dimulai |
| Persetujuan perubahan template cetak SPK fisik | Kepala Produksi | 29 Okt | Perubahan template cetak SPK resmi belum bisa dipasang di komputer kasir |

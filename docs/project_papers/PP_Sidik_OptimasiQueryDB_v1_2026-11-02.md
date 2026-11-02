# PROJECT PAPER – Optimasi Kecepatan Database & Perbaikan N+1 Query di Seluruh Divisi

Penyusun: Sidik Waluya (Data Analyst & IT) · Versi 1 · Update terakhir: 2 November 2026

## 1. Identitas

| Parameter | Keterangan |
|---|---|
| **Nama proyek** | Optimasi Kecepatan Database & Perbaikan N+1 Query di Seluruh Divisi |
| **Pemberi tugas** | Manajemen Workshop |
| **Divisi terkait** | Seluruh Divisi (Customer Service, Gudang, Workshop, Finance, Customer Experience, Master Data) & Divisi Data Analyst & IT |
| **Jenis** | Internal (Peningkatan Performa, Efisiensi Server & Keandalan Sistem Workshop) |
| **Status** | Sedang Dikerjakan |
| **Periode proyek** | 2 November 2026 – 30 November 2026 |
| **Cek duplikasi** | Tidak ada duplikasi. Fokus pada audit mendalam kueri Eloquent dan database di seluruh controller serta komponen Livewire yang mengalami N+1 query dan bottleneck pembacaan data, bertujuan menaikkan kecepatan load page secara drastis tanpa mengubah fungsi bisnis maupun tampilan antarmuka. |

## 2. Tim & Peran

| Nama | Divisi | Peran | Bagian yang dikerjakan |
|---|---|---|---|
| **Sidik Waluya** | Data Analyst & IT | PIC & Pelaksana Utama | Melakukan audit kueri, profiling N+1, eager loading, refactoring controller & Livewire, serta uji performa before vs after |
| **Kepala Produksi / SPV** | Workshop | Penguji & Validator Lapangan | Memastikan data operasional di stasiun bengkel tetap akurat, real-time, dan tidak ada glitch setelah kueri dioptimasi |
| **Head of Finance & Gudang** | Finance & Gudang | Penguji & Validator | Memverifikasi transaksi invoice, mutasi, stok material, dan pencatatan kasir berjalan normal serta lebih responsif |
| **Manajemen Workshop** | Manajemen | Pengambil Keputusan | Menyetujui implementasi indeks database dan mengevaluasi laporan hasil optimasi performa akhir |

## 3. Status Sekarang

Rancangan pemetaan halaman di 6 divisi (CS, Gudang, Workshop, Finance, CX, Master Data) telah selesai dipetakan berdasarkan menu sidebar aktif; konfigurasi lingkungan pengujian lokal (deteksi `preventLazyLoading` dan Laravel Debugbar) saat ini sedang disiapkan untuk mengukur baseline kueri sebelum masuk ke optimasi Divisi CS.

## 4. Tujuan & Dampak

| Parameter | Penjelasan |
|---|---|
| **Masalah bisnis** | Seiring bertambahnya ribuan data SPK, riwayat foto pengerjaan, dan log teknisi, halaman-halaman tabel di setiap divisi terasa makin lambat saat dibuka. Banyak komponen memanggil relasi di dalam perulangan loop/Blade (N+1 query), sehingga satu halaman bisa mengeksekusi puluhan hingga ratusan kueri terpisah ke database yang membebani server dan memperlambat kerja staf di jam sibuk. |
| **Output akhir** | Kode kueri Eloquent yang bersih dari N+1 query di seluruh divisi (menggunakan eager loading `with()`, `withCount()`, query scoping, index kolom yang presisi) serta tabel laporan komparasi performa (*Before vs After*) yang menunjukkan penurunan jumlah kueri dan durasi loading per halaman. |
| **Dampak / keputusan** | Waktu respons halaman sistem workshop menjadi jauh lebih cepat dan enteng, konsumsi CPU dan RAM server aaPanel berkurang drastis, risiko server hang/overload di jam puncak teratasi, serta produktivitas kerja seluruh staf operasional meningkat. |

## 5. Prioritas

| Parameter | Penjelasan |
|---|---|
| **Kuadran** | **Kerjakan Sekarang** |
| **Alasan (kode kriteria)** | **I2** (ditetapkan manajemen untuk menjaga stabilitas infrastruktur sistem) dan **U2** (volume transaksi dan pencatatan SPK November melonjak seiring berjalannya modul QR Scan Mobile dan R&D; jika kueri database tidak segera dirapikan, penumpukan data akan memperparah kelambatan sistem). |
| **Berubah jika...** | Turun ke "Jadwalkan" bila terjadi kendala teknis darurat atau bug kritis pada alur transaksi kasir/bengkel yang membutuhkan penanganan seketika. |

## 6. Ruang Lingkup

| Parameter | Penjelasan |
|---|---|
| **Termasuk** | 1. Audit dan perbaikan N+1 query pada 6 divisi: Customer Service, Gudang, Workshop (termasuk PWA Workshop, Mobile Kontrol Teknisi & R&D), Finance, Customer Experience (CX/CC), dan Master Data & System.<br>2. Pemasangan alat deteksi `preventLazyLoading` di environment development (lokal) dan Laravel Debugbar untuk pengukuran baseline jumlah kueri.<br>3. Penerapan pola optimasi kueri Eloquent (`with`, `withCount`, `select`, query scoping, dan composite index database non-destruktif jika diperlukan).<br>4. Penyusunan tabel audit metrik *Before vs After* (jumlah kueri dan waktu respons milidetik). |
| **Tidak termasuk** | 1. Perubahan alur bisnis, logika perhitungan tarif/biaya, maupun tata letak tampilan antarmuka pengguna (UI/UX).<br>2. Penggantian mesin database (tetap menggunakan MySQL/MariaDB yang berjalan).<br>3. Pembelian atau upgrade spesifikasi server fisik / VPS aaPanel. |

## 7. Breakdown Pekerjaan

| No | Tanggal | Komponen/Tugas | Deskripsi | PIC | Deadline | Status | Keterangan |
|---|---|---|---|---|---|---|---|
| 1 | 2–3 Nov | Setup Alat Deteksi & Baseline Pengukuran | Memasang preventLazyLoading (khusus dev/lokal), Laravel Debugbar, dan mengukur baseline kueri menu umum | Sidik | 3 Nov | Sedang Dikerjakan | Menyiapkan environment pengukuran |
| 2 | 4–6 Nov | Optimasi Divisi Customer Service (CS) | Audit & perbaikan N+1 pada CS Dashboard, Leads (Konsultasi, Follow-up, Closing), Kiriman SPK, dan Data SPK | Sidik | 6 Nov | Backlog | Target eliminasi kueri berulang relasi customer & SPK |
| 3 | 7–11 Nov | Optimasi Divisi Gudang | Audit & perbaikan N+1 pada Penerimaan, Penyimpanan Rak, Assessment, Finish, Shipping, dan Belanja Gudang | Sidik | 11 Nov | Backlog | Memeriksa kueri relasi stok material, rak, dan logistik |
| 4 | 12–17 Nov | Optimasi Divisi Workshop | Audit & perbaikan N+1 pada Dashboard Workshop, Stasiun 1–6, PWA Workshop, Mobile Kontrol Teknisi, dan R&D | Sidik | 17 Nov | Backlog | Area pengerjaan terluas dan frekuensi akses tertinggi |
| 5 | 18–20 Nov | Optimasi Divisi Finance | Audit & perbaikan N+1 pada Waiting Payment, Transaksi, Data Invoice, Audit Bayar CS, dan Verifikasi Mutasi | Sidik | 20 Nov | Backlog | Menjamin akurasi kalkulasi dan integritas data kasir |
| 6 | 21–24 Nov | Optimasi Divisi Customer Experience (CX) | Audit & perbaikan N+1 pada Follow Up, OTO, Konfirmasi After, Overdue SLA, Klaim Garansi, dan Analisis QC | Sidik | 24 Nov | Backlog | Menghindari duplikasi kueri status SPK dan riwayat resolusi |
| 7 | 25–27 Nov | Optimasi Master Data & System | Audit & perbaikan N+1 pada Master Customer, Pengguna, Log Aktivitas, Laporan, KPI, dan Supply Chain | Sidik | 27 Nov | Backlog | Merapikan kueri tabel master berbobot data besar |
| 8 | 28–30 Nov | Evaluasi, Laporan Before/After & Serah Terima | Pengujian menyeluruh seluruh alur, kompilasi laporan tabel before vs after, deploy ke aaPanel, dan serah terima | Sidik | 30 Nov | Backlog | Final sign-off manajemen tanpa ada breaking change |

## 8. Kriteria Selesai

- [ ] Alat deteksi lazy loading terpasang di environment lokal dan berhasil mendeteksi relasi yang belum di-eager load.
- [ ] Seluruh halaman utama di 6 divisi (CS, Gudang, Workshop, Finance, CX, Master Data) terbebas dari query berulang (N+1) pada setiap perulangan data.
- [ ] Tidak ada perubahan tampilan, fitur yang hilang, atau kesalahan data hasil perhitungan di seluruh halaman yang dioptimasi.
- [ ] Tersedia tabel dokumentasi perbandingan performa (*Before vs After*) yang menunjukkan penurunan signifikan pada jumlah query dan waktu muat halaman.
- [ ] Seluruh perbaikan kueri lolos uji regresi di server staging/aaPanel tanpa memicu error atau dampak negatif pada modul lain.

## 9. Keputusan & Kebutuhan dari Pihak Lain

| Apa yang dibutuhkan | Dari siapa | Batas tanggal | Dampak kalau terlambat |
|---|---|---|---|
| Konfirmasi sampel data transaksi dan riwayat SPK untuk uji beban lokal | Supervisor Kasir / Admin CS | 4 Nov | Pengujian kueri pada variasi data riil tertunda |
| Validasi keakuratan nominal invoice dan mutasi pasca-eager loading | Head of Finance | 19 Nov | Pengujian integritas modul keuangan terhambat |
| Waktu luang pengujian alur stasiun kerja bersama teknisi bengkel | Kepala Produksi | 16 Nov | Validasi modul workshop dan mobile kontrol di lantai bengkel tertunda |
| Persetujuan penambahan indeks database (migration) di server aaPanel | Manajemen Workshop | 28 Nov | Deployment final optimasi ke server live tertunda |


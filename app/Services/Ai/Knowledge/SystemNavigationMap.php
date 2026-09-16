<?php

namespace App\Services\Ai\Knowledge;

class SystemNavigationMap
{
    /**
     * Get all structured navigation items and routes in the workshop system.
     *
     * @return array
     */
    public static function getNavigationTree(): array
    {
        return [
            // =========================================================================
            // 0. TOP LEVEL NAVIGATION (GLOBAL)
            // =========================================================================
            [
                'division' => 'general',
                'division_label' => 'Umum & Dashboard',
                'feature_name' => 'Dashboard Utama',
                'sidebar_location' => 'Sidebar Atas > Dashboard',
                'route_name' => 'dashboard',
                'url_path' => '/dashboard',
                'permission' => 'dashboard (Semua Role)',
                'description' => 'Pusat ringkasan metriks workshop: jumlah SPK aktif, status workflow bengkel, performa harian, grafik pengerjaan, dan status antrean.',
                'how_to_use' => "1. Klik menu 'Dashboard' di bagian paling atas sidebar.\n2. Lihat widget kartu ringkasan untuk memantau beban kerja harian bengkel.",
                'keywords' => ['dashboard', 'beranda', 'home', 'ringkasan', 'statistik', 'overview']
            ],
            [
                'division' => 'general',
                'division_label' => 'Internal Tracking SPK',
                'feature_name' => 'Internal Tracking (Lacak SPK)',
                'sidebar_location' => 'Sidebar Atas > Internal Tracking',
                'route_name' => 'internal-tracking.index',
                'url_path' => '/internal-tracking',
                'permission' => 'internal-tracking',
                'description' => 'Mesin pencari utama SPK internal. Mencari SPK berdasarkan nomor SPK, nama pelanggan, merk/tipe sepatu, status pengerjaan, maupun nomor invoice.',
                'how_to_use' => "1. Klik menu 'Internal Tracking' di sidebar atas.\n2. Ketikkan kata kunci (No. SPK, nama customer, atau merk sepatu) pada bilah pencarian.\n3. Klik kartu SPK untuk melihat rincian detail, timeline logs, dan status QC/pembayaran.",
                'keywords' => ['tracking', 'lacak spk', 'cari spk', 'internal tracking', 'search spk', 'cari sepatu']
            ],
            [
                'division' => 'general',
                'division_label' => 'Internal Tracking Jasa',
                'feature_name' => 'Tracking Jasa',
                'sidebar_location' => 'Sidebar Atas > Tracking Jasa',
                'route_name' => 'internal-tracking.services',
                'url_path' => '/internal-tracking/services',
                'permission' => 'internal-tracking.services',
                'description' => 'Pelacakan khusus lini layanan jasa pengerjaan (Deep Clean, Repaint, Reglue, Unyellowing, Soling, dll.) yang sedang berjalan di workshop.',
                'how_to_use' => "1. Klik menu 'Tracking Jasa' di sidebar.\n2. Filter berdasarkan jenis tindakan servis untuk melihat beban kerja teknisi dan antrean jenis jasa tertentu.",
                'keywords' => ['tracking jasa', 'lacak jasa', 'layanan sepatu', 'servis', 'deep clean', 'repaint', 'reglue']
            ],

            // =========================================================================
            // 1. DIVISI CUSTOMER SERVICE (CS)
            // =========================================================================
            [
                'division' => 'cs',
                'division_label' => 'Divisi Customer Service (CS)',
                'feature_name' => 'CS Dashboard',
                'sidebar_location' => 'Sidebar > Divisi CS > CS Dashboard',
                'route_name' => 'cs.dashboard',
                'url_path' => '/cs/dashboard',
                'permission' => 'cs.dashboard (Role CS, Admin, Super Admin)',
                'description' => 'Pusat kendali CS untuk memantau performa pelayanan harian, response rate, total konsultasi masuk, dan konversi leads.',
                'how_to_use' => "1. Buka dropdown 'Divisi CS' di sidebar.\n2. Klik 'CS Dashboard' untuk melihat ringkasan performa dan antrean obrolan pelanggan.",
                'keywords' => ['cs dashboard', 'dashboard cs', 'customer service', 'layanan pelanggan']
            ],
            [
                'division' => 'cs',
                'division_label' => 'Divisi Customer Service (CS)',
                'feature_name' => 'Laporan Performa CS',
                'sidebar_location' => 'Sidebar > Divisi CS > Laporan Performa',
                'route_name' => 'cs.analytics',
                'url_path' => '/cs/analytics',
                'permission' => 'cs.analytics',
                'description' => 'Analitik mendalam pencapaian tim CS, kecepatan respon (SLA response time), volume konsultasi, dan konversi closing order.',
                'how_to_use' => "1. Masuk ke sidebar 'Divisi CS' > 'Laporan Performa'.\n2. Atur rentang tanggal analisis.\n3. Unduh atau tinjau grafik performa per-agent CS.",
                'keywords' => ['laporan performa cs', 'cs analytics', 'kecepatan respon cs', 'sla cs', 'kinerja cs']
            ],
            [
                'division' => 'cs',
                'division_label' => 'Divisi Customer Service (CS)',
                'feature_name' => 'Konsultasi & Leads',
                'sidebar_location' => 'Sidebar > Divisi CS > Konsultasi',
                'route_name' => 'cs.leads.konsultasi',
                'url_path' => '/cs/leads/konsultasi',
                'permission' => 'cs.leads.konsultasi',
                'description' => 'Manajemen konsultasi calon pelanggan baru yang masuk melalui WhatsApp / SleekFlow, pencatatan keluhan awal sepatu, dan estimasi biaya perbaikan sebelum SPK dibuat.',
                'how_to_use' => "1. Buka 'Divisi CS' > 'Konsultasi'.\n2. Pilih percakapan pelanggan yang pending.\n3. Catat keluhan sepatu dan rekomendasikan paket servis yang sesuai.",
                'keywords' => ['konsultasi', 'leads', 'chat masuk', 'tanya sepatu', 'estimasi', 'cs leads']
            ],

            // =========================================================================
            // 2. DIVISI GUDANG (WAREHOUSE & STORAGE)
            // =========================================================================
            [
                'division' => 'warehouse',
                'division_label' => 'Divisi Gudang',
                'feature_name' => 'Penerimaan Outbound (QC to Gudang)',
                'sidebar_location' => 'Sidebar > Divisi Gudang > Penerimaan Outbound',
                'route_name' => 'gudang.outbound-receipt',
                'url_path' => '/gudang/outbound-receipt',
                'permission' => 'gudang.outbound-receipt (Staf Gudang, Admin)',
                'description' => 'Penerimaan barang/sepatu yang telah selesai di-QC dari Workshop (Inbound Staging ke Gudang) melalui scan manifes keluar (MNF-OUT).',
                'how_to_use' => "1. Buka 'Divisi Gudang' > 'Penerimaan Outbound'.\n2. Scan barcode nomor manifes MNF-OUT dari kurir workshop.\n3. Verifikasi jumlah fisik sepatu dan konfirmasi penerimaan.",
                'keywords' => ['penerimaan outbound', 'qc to gudang', 'terima sepatu workshop', 'manifest outbound', 'mnf-out']
            ],
            [
                'division' => 'warehouse',
                'division_label' => 'Divisi Gudang',
                'feature_name' => 'Dashboard Gudang',
                'sidebar_location' => 'Sidebar > Divisi Gudang > Operasional Gudang > Dashboard Gudang',
                'route_name' => 'storage.dashboard',
                'url_path' => '/storage/dashboard',
                'permission' => 'storage.dashboard',
                'description' => 'Overview operasional gudang penyimpanan sepatu: kapasitas rak, sepatu siap kirim, sepatu siap ambil, dan perputaran barang.',
                'how_to_use' => "1. Klik 'Divisi Gudang' > 'Dashboard Gudang'.\n2. Pantau utilisasi rak penyimpanan dan antrean sepatu masuk/keluar.",
                'keywords' => ['dashboard gudang', 'storage dashboard', 'kapasitas gudang', 'rak gudang']
            ],
            [
                'division' => 'warehouse',
                'division_label' => 'Divisi Gudang',
                'feature_name' => 'Penyimpanan Rak (Storage Management)',
                'sidebar_location' => 'Sidebar > Divisi Gudang > Operasional Gudang > Penyimpanan Rak',
                'route_name' => 'storage.index',
                'url_path' => '/storage',
                'permission' => 'warehouse.storage',
                'description' => 'Alokasi dan penataan nomor rak fisik untuk setiap sepatu SPK yang selesai agar rapi dan mudah dicari saat customer datang atau siap diantar kurir.',
                'how_to_use' => "1. Buka 'Divisi Gudang' > 'Penyimpanan Rak'.\n2. Masukkan nomor SPK dan pilih nomor Rak (misal Rak A-01).\n3. Klik 'Simpan Alokasi' untuk mencatat lokasi penyimpanan.",
                'keywords' => ['penyimpanan rak', 'alokasi rak', 'rak sepatu', 'storage rak', 'lokasi simpan']
            ],
            [
                'division' => 'warehouse',
                'division_label' => 'Divisi Gudang',
                'feature_name' => 'Penerimaan Sepatu (Reception / Intake)',
                'sidebar_location' => 'Sidebar > Divisi Gudang > Penerimaan',
                'route_name' => 'reception.index',
                'url_path' => '/reception',
                'permission' => 'reception',
                'description' => 'Pintu masuk pertama sepatu pelanggan ke bengkel. Pembuatan SPK baru, pencatatan keluhan, foto kondisi fisik awal (foto before), dan cetak nota SPK.',
                'how_to_use' => "1. Masuk ke 'Divisi Gudang' > 'Penerimaan'.\n2. Klik 'Tambah Penerimaan Baru / Buat SPK'.\n3. Isi data customer, merk/tipe sepatu, foto kondisi awal, dan pilih paket treatment.\n4. Cetak struk/nota SPK untuk pelanggan.",
                'keywords' => ['penerimaan', 'reception', 'buat spk baru', 'intake', 'terima sepatu', 'input spk']
            ],
            [
                'division' => 'warehouse',
                'division_label' => 'Divisi Gudang',
                'feature_name' => 'Assessment Kondisi Sepatu',
                'sidebar_location' => 'Sidebar > Divisi Gudang > Assessment',
                'route_name' => 'assessment.index',
                'url_path' => '/assessment',
                'permission' => 'assessment',
                'description' => 'Pemeriksaan fisik mendalam terhadap material kulit/kanvas/midsole sepatu, identifikasi risiko pengerjaan, dan pencatatan instruksi khusus sebelum dikirim ke workshop.',
                'how_to_use' => "1. Buka 'Divisi Gudang' > 'Assessment'.\n2. Pilih SPK yang baru diterima.\n3. Berikan catatan penilaian kondisi sepatu dan simpan asesmen.",
                'keywords' => ['assessment', 'penilaian sepatu', 'cek kondisi', 'inspeksi material', 'cek risiko']
            ],
            [
                'division' => 'warehouse',
                'division_label' => 'Divisi Gudang',
                'feature_name' => 'Logistik Manifest (Pengiriman ke Workshop)',
                'sidebar_location' => 'Sidebar > Divisi Gudang > Logistik Manifest',
                'route_name' => 'manifest.index',
                'url_path' => '/manifest',
                'permission' => 'manifest.index',
                'description' => 'Pengelompokan kumpulan SPK sepatu ke dalam dokumen surat jalan logistik (Manifes) untuk dikirim dari front office gudang ke Workshop utama.',
                'how_to_use' => "1. Masuk ke 'Divisi Gudang' > 'Logistik Manifest'.\n2. Klik 'Buat Manifes Baru', pilih daftar SPK yang siap diberangkatkan.\n3. Cetak manifes dan serahkan kepada kurir pengantar.",
                'keywords' => ['manifes', 'logistik manifest', 'surat jalan gudang', 'kirim ke workshop', 'manifest']
            ],
            [
                'division' => 'warehouse',
                'division_label' => 'Divisi Gudang',
                'feature_name' => 'Gudang Finish (Sepatu Siap Diambil/Kirim)',
                'sidebar_location' => 'Sidebar > Divisi Gudang > Finish',
                'route_name' => 'finish.index',
                'url_path' => '/finish',
                'permission' => 'finish',
                'description' => 'Daftar sepatu yang telah selesai seluruh proses pengerjaan dan lolos QC akhir, menunggu diambil customer (pickup) atau dikirim via kurir.',
                'how_to_use' => "1. Buka 'Divisi Gudang' > 'Finish'.\n2. Cari nomor SPK saat customer datang mengambil barang.\n3. Lakukan serah terima dan perbarui status menjadi SELESAI/TERKIRIM.",
                'keywords' => ['finish', 'gudang finish', 'sepatu siap ambil', 'pickup customer', 'selesai pengerjaan']
            ],
            [
                'division' => 'warehouse',
                'division_label' => 'Divisi Gudang',
                'feature_name' => 'Pengiriman (Ekspedisi & Kurir)',
                'sidebar_location' => 'Sidebar > Divisi Gudang > Pengiriman',
                'route_name' => 'shipping.index',
                'url_path' => '/shipping',
                'permission' => 'shipping',
                'description' => 'Pengelolaan nomor resi pengiriman (JNE, J&T, SiCepat, Paxel, Lalamove, Gosend) untuk sepatu yang dikembalikan ke alamat customer.',
                'how_to_use' => "1. Buka 'Divisi Gudang' > 'Pengiriman'.\n2. Pilih SPK yang berstatus lunas dan siap kirim.\n3. Input nama ekspedisi dan nomor resi pengiriman.",
                'keywords' => ['pengiriman', 'shipping', 'resi pengiriman', 'ekspedisi', 'kirim sepatu', 'kurir']
            ],
            [
                'division' => 'warehouse',
                'division_label' => 'Divisi Gudang',
                'feature_name' => 'Belanja & Stok Material Gudang',
                'sidebar_location' => 'Sidebar > Divisi Gudang > Belanja Gudang / Barang Keluar',
                'route_name' => 'storage.purchase.index',
                'url_path' => '/storage/purchase',
                'permission' => 'storage.purchase',
                'description' => 'Manajemen persediaan barang/bahan habis pakai gudang: lem, benang, sol, cat, sikat, packaging box, silica gel, dan pencatatan mutasi keluar-masuk barang.',
                'how_to_use' => "1. Buka 'Divisi Gudang' > 'Belanja Gudang' atau 'Barang Keluar'.\n2. Catat pembelian baru atau pengeluaran stok untuk menjaga batas minimum inventaris.",
                'keywords' => ['belanja gudang', 'barang keluar', 'riwayat mutasi', 'stok barang', 'material gudang', 'inventori']
            ],

            // =========================================================================
            // 3. DIVISI WORKSHOP (PWA & PRODUCTION LINE)
            // =========================================================================
            [
                'division' => 'workshop',
                'division_label' => 'Divisi Workshop (PWA)',
                'feature_name' => 'Workshop PWA Dashboard V2',
                'sidebar_location' => 'Sidebar > DIVISI WORKSHOP (PWA Shortcut)',
                'route_name' => 'workshop.dashboard-v2',
                'url_path' => '/workshop/dashboard-v2',
                'permission' => 'workshop.dashboard / access-workshop',
                'description' => 'Pusat operasional teknisi bengkel dalam format antarmuka responsif (PWA) yang optimal untuk tablet & mobile. Menampilkan antrean stasiun, target harian teknisi, dan SPK kritis.',
                'how_to_use' => "1. Klik tombol hijau gradien 'DIVISI WORKSHOP PWA' di sidebar.\n2. Anda akan diarahkan ke antarmuka khusus stasiun teknisi.",
                'keywords' => ['workshop pwa', 'dashboard workshop', 'stasiun teknisi', 'pwa bengkel', 'produksi pwa']
            ],
            [
                'division' => 'workshop',
                'division_label' => 'Divisi Workshop (PWA)',
                'feature_name' => 'Fast Track Workshop',
                'sidebar_location' => 'Workshop PWA > Fast Track',
                'route_name' => 'workshop.fast-track.index',
                'url_path' => '/workshop/fast-track',
                'permission' => 'workshop.dashboard',
                'description' => 'Antrean prioritas tinggi untuk SPK kilat (Fast Track) dengan SLA ketat yang harus diselesaikan dalam waktu 1-2 hari.',
                'how_to_use' => "1. Buka Workshop PWA > Menu 'Fast Track'.\n2. Dahulukan SPK dalam daftar ini sebelum mengerjakan antrean reguler.",
                'keywords' => ['fast track', 'spk kilat', 'prioritas workshop', 'urgent', 'spk express']
            ],
            [
                'division' => 'workshop',
                'division_label' => 'Divisi Workshop (PWA)',
                'feature_name' => 'Stasiun Preparation (Cuci & Prep)',
                'sidebar_location' => 'Workshop PWA > Preparation',
                'route_name' => 'preparation.index',
                'url_path' => '/preparation',
                'permission' => 'preparation',
                'description' => 'Tahap pencucian awal, deep cleaning, pembongkaran sol (unsole), dan pembersihan kotoran sebelum sepatu disortir dan diperbaiki.',
                'how_to_use' => "1. Buka 'Preparation' di navigasi workshop.\n2. Pilih SPK, lakukan pencucian/prep sesuai SOP, lalu klik 'Finish Prep' untuk meloloskan ke stasiun sortir.",
                'keywords' => ['preparation', 'prep', 'cuci sepatu', 'deep clean', 'unsole', 'pembersihan']
            ],
            [
                'division' => 'workshop',
                'division_label' => 'Divisi Workshop (PWA)',
                'feature_name' => 'Stasiun Sortir & Kebutuhan Material',
                'sidebar_location' => 'Workshop PWA > Sortir',
                'route_name' => 'sortir.index',
                'url_path' => '/sortir',
                'permission' => 'sortir',
                'description' => 'Klasifikasi jenis perbaikan teknis, pencocokan warna cat, alokasi teknisi spesialis, dan pengajuan kebutuhan bahan/material sol atau kulit.',
                'how_to_use' => "1. Masuk ke 'Sortir'.\n2. Tentukan alokasi teknisi dan bahan yang dibutuhkan.\n3. Selesaikan sortir agar SPK masuk ke lini Produksi.",
                'keywords' => ['sortir', 'klasifikasi', 'alokasi teknisi', 'kebutuhan bahan', 'sortir spk']
            ],
            [
                'division' => 'workshop',
                'division_label' => 'Divisi Workshop (PWA)',
                'feature_name' => 'Stasiun Produksi & Late Info',
                'sidebar_location' => 'Workshop PWA > Production / Late Info',
                'route_name' => 'production.index',
                'url_path' => '/production',
                'permission' => 'production',
                'description' => 'Pengerjaan utama reparasi: reglue pres mesin, soling jahit sol, repaint/recolour, unyellowing, atau rekonstruksi upper.',
                'how_to_use' => "1. Buka 'Production'.\n2. Klik SPK yang sedang dikerjakan.\n3. Update stasiun pengerjaan dan upload foto progres jika diperlukan.",
                'keywords' => ['production', 'produksi', 'teknisi pengerjaan', 'late info', 'reglue', 'jahit sol', 'repaint']
            ],
            [
                'division' => 'workshop',
                'division_label' => 'Divisi Workshop (PWA)',
                'feature_name' => 'Quality Control (QC) & Manifes Outbound',
                'sidebar_location' => 'Workshop PWA > QC / Outbound',
                'route_name' => 'qc.index',
                'url_path' => '/qc',
                'permission' => 'qc',
                'description' => 'Pemeriksaan standar mutu hasil pengerjaan. Jika lolos (PASS), sepatu difoto after dan dibuatkan manifes outbound (MNF-OUT) untuk dikirim balik ke front office gudang.',
                'how_to_use' => "1. Buka menu 'QC'.\n2. Periksa kerapian lem, jahitan, dan cat.\n3. Klik 'Pass QC' jika sempurna, atau 'Reject' jika harus ada perbaikan ulang.",
                'keywords' => ['qc', 'quality control', 'cek mutu', 'qc pass', 'qc reject', 'outbound qc']
            ],

            // =========================================================================
            // 4. DIVISI FINANCE (KASIR, INVOICE & MUTASI)
            // =========================================================================
            [
                'division' => 'finance',
                'division_label' => 'Divisi Finance',
                'feature_name' => 'Dashboard Finance',
                'sidebar_location' => 'Sidebar > Divisi Finance > Dashboard Finance',
                'route_name' => 'finance.dashboard',
                'url_path' => '/finance/dashboard',
                'permission' => 'finance.dashboard (Role Finance, Kasir, Admin)',
                'description' => 'Ringkasan finansial harian: total omzet masuk, piutang tertahan (unpaid), pembayaran menunggu konfirmasi, dan rasio pelunasan.',
                'how_to_use' => "1. Buka 'Divisi Finance' > 'Dashboard Finance'.\n2. Tinjau total omset, status pembayaran hari ini, dan target keuangan.",
                'keywords' => ['finance dashboard', 'omset', 'keuangan', 'pendapatan bengkel', 'arus kas']
            ],
            [
                'division' => 'finance',
                'division_label' => 'Divisi Finance',
                'feature_name' => 'Waiting Payment (Menunggu Pelunasan)',
                'sidebar_location' => 'Sidebar > Divisi Finance > Waiting Payment',
                'route_name' => 'finance.waiting-payment',
                'url_path' => '/finance/waiting-payment',
                'permission' => 'finance.waiting-payment',
                'description' => 'Daftar SPK yang sudah selesai dikerjakan namun customer belum melunasi tagihan biaya perbaikan.',
                'how_to_use' => "1. Buka 'Divisi Finance' > 'Waiting Payment'.\n2. Klik SPK terkait untuk mengirim reminder tagihan WhatsApp atau melakukan pelunasan kasir.",
                'keywords' => ['waiting payment', 'belum lunas', 'menunggu pembayaran', 'piutang', 'tagihan spk']
            ],
            [
                'division' => 'finance',
                'division_label' => 'Divisi Finance',
                'feature_name' => 'Finance Transaksi & Kasir',
                'sidebar_location' => 'Sidebar > Divisi Finance > Finance Transaksi',
                'route_name' => 'finance.index',
                'url_path' => '/finance',
                'permission' => 'finance.transaction',
                'description' => 'Pusat kasir dan transaksi keuangan SPK: menerima pembayaran tunai/transfer, cetak kwitansi, dan penutupan kas harian.',
                'how_to_use' => "1. Masuk ke 'Divisi Finance' > 'Finance Transaksi'.\n2. Masukkan nomor SPK atau invoice.\n3. Input nominal pembayaran dan cetak receipt.",
                'keywords' => ['kasir', 'transaksi finance', 'bayar', 'pos', 'kwitansi', 'kasir bengkel']
            ],
            [
                'division' => 'finance',
                'division_label' => 'Divisi Finance',
                'feature_name' => 'Data Invoice',
                'sidebar_location' => 'Sidebar > Divisi Finance > Data Invoice',
                'route_name' => 'finance.invoices.index',
                'url_path' => '/finance/invoices',
                'permission' => 'finance.invoices',
                'description' => 'Arsip seluruh invoice resmi yang diterbitkan sistem (nomor invoice, tanggal jatuh tempo, rincian biaya treatment, dan status lunas/belum lunas).',
                'how_to_use' => "1. Buka 'Divisi Finance' > 'Data Invoice'.\n2. Filter berdasarkan nomor invoice (misal INV-260814-3868).\n3. Klik icon print untuk mencetak ulang atau unduh PDF invoice.",
                'keywords' => ['data invoice', 'faktur', 'cetak invoice', 'tagihan', 'inv', 'arsip invoice']
            ],
            [
                'division' => 'finance',
                'division_label' => 'Divisi Finance',
                'feature_name' => 'Audit Bayar CS (Verifikasi DP dari CS)',
                'sidebar_location' => 'Sidebar > Divisi Finance > Audit Bayar CS',
                'route_name' => 'finance.cs-verification',
                'url_path' => '/finance/cs-verification',
                'permission' => 'finance.cs-verification',
                'description' => 'Pengecekan dan rekonsiliasi bukti transfer DP atau pelunasan yang diinput oleh tim Customer Service agar uang benar-benar sudah masuk rekening bengkel sebelum disahkan.',
                'how_to_use' => "1. Masuk ke 'Divisi Finance' > 'Audit Bayar CS'.\n2. Cek bukti transfer dan mutasi bank.\n3. Klik 'Verifikasi / Approve' untuk mengesahkan pembayaran.",
                'keywords' => ['audit bayar cs', 'verifikasi cs', 'cek dp', 'validasi transfer', 'rekonsiliasi cs']
            ],
            [
                'division' => 'finance',
                'division_label' => 'Divisi Finance',
                'feature_name' => 'Import & Verifikasi Mutasi Bank',
                'sidebar_location' => 'Sidebar > Divisi Finance > Import Mutasi / Verifikasi Mutasi',
                'route_name' => 'finance.mutations.index',
                'url_path' => '/finance/mutations',
                'permission' => 'finance.mutations / finance.verifications',
                'description' => 'Pencocokan otomatis atau manual rekening koran bank (BCA, Mandiri, BRI, QRIS) dengan tagihan invoice pelanggan.',
                'how_to_use' => "1. Buka 'Divisi Finance' > 'Import Mutasi'.\n2. Unggah file CSV/Excel mutasi rekening.\n3. Lakukan matching otomatis di 'Verifikasi Mutasi'.",
                'keywords' => ['mutasi bank', 'import mutasi', 'verifikasi mutasi', 'rekening koran', 'bca', 'mandiri']
            ],

            // =========================================================================
            // 5. DIVISI CUSTOMER EXPERIENCE (CX & AFTER-SALES)
            // =========================================================================
            [
                'division' => 'cx',
                'division_label' => 'Divisi Customer Experience (CX)',
                'feature_name' => 'CC Dashboard (CX Analytics)',
                'sidebar_location' => 'Sidebar > Divisi CC > CC Dashboard',
                'route_name' => 'cx.dashboard',
                'url_path' => '/cx/dashboard',
                'permission' => 'cx.dashboard',
                'description' => 'Dashboard kepuasan pelanggan: indeks CSAT, rasio komplain, tingkat repeat order, dan rekap penanganan isu sepatu.',
                'how_to_use' => "1. Buka 'Divisi CC' di sidebar > 'CC Dashboard'.\n2. Tinjau skor kepuasan dan status tiket follow up.",
                'keywords' => ['cx dashboard', 'cc dashboard', 'customer experience', 'kepuasan pelanggan', 'csat']
            ],
            [
                'division' => 'cx',
                'division_label' => 'Divisi Customer Experience (CX)',
                'feature_name' => 'Follow Up Worklist',
                'sidebar_location' => 'Sidebar > Divisi CC > Follow Up',
                'route_name' => 'cx.index',
                'url_path' => '/cx',
                'permission' => 'cx.index',
                'description' => 'Daftar kerja tim CX untuk menghubungi customer terkait kendala approval treatment tambahan, approval biaya revisi, atau konfirmasi kondisi sepatu.',
                'how_to_use' => "1. Buka 'Divisi CC' > 'Follow Up'.\n2. Hubungi pelanggan yang memiliki tiket pending.\n3. Masukkan catatan hasil follow-up dan ubah status menjadi Resolved.",
                'keywords' => ['follow up cx', 'worklist cc', 'tiket pelanggan', 'kendala spk', 'hubungi customer']
            ],
            [
                'division' => 'cx',
                'division_label' => 'Divisi Customer Experience (CX)',
                'feature_name' => 'Kolam OTO (On-The-Order Upselling)',
                'sidebar_location' => 'Sidebar > Divisi CC > Kolam OTO (Upsell)',
                'route_name' => 'cx.oto.index',
                'url_path' => '/cx/oto',
                'permission' => 'cx.oto',
                'description' => 'Manajemen penawaran layanan tambahan (Upsell) saat sepatu sedang dikerjakan (misal: penambahan unyellowing, ganti insole, tali sepatu baru, atau nano water repellent).',
                'how_to_use' => "1. Buka 'Divisi CC' > 'Kolam OTO (Upsell)'.\n2. Tawarkan rekomendasi servis tambahan ke pelanggan via WhatsApp.\n3. Jika disetujui, klik 'Accept OTO' agar otomatis masuk invoice.",
                'keywords' => ['oto', 'kolam oto', 'upsell', 'layanan tambahan', 'rekomendasi treatmen']
            ],
            [
                'division' => 'cx',
                'division_label' => 'Divisi Customer Experience (CX)',
                'feature_name' => 'Konfirmasi After Service (Survey Kepuasan)',
                'sidebar_location' => 'Sidebar > Divisi CC > Konfirmasi After',
                'route_name' => 'cx.after-confirmation.index',
                'url_path' => '/cx/after-confirmation',
                'permission' => 'cx.after-confirmation',
                'description' => 'Follow up purna jual setelah sepatu diterima customer: menanyakan kepuasan hasil cuci/reparasi, mengajak review bintang 5, dan menampung masukan.',
                'how_to_use' => "1. Masuk ke 'Divisi CC' > 'Konfirmasi After'.\n2. Kirim pesan apresiasi dan link feedback kepada pelanggan.",
                'keywords' => ['konfirmasi after', 'after service', 'survey kepuasan', 'review', 'csat survey']
            ],
            [
                'division' => 'cx',
                'division_label' => 'Divisi Customer Experience (CX)',
                'feature_name' => 'Overdue SLA Monitoring',
                'sidebar_location' => 'Sidebar > Divisi CC > Overdue SLA',
                'route_name' => 'cx.overdue-dashboard',
                'url_path' => '/cx/overdue-dashboard',
                'permission' => 'cx.overdue',
                'description' => 'Peringatan dini untuk SPK yang pengerjaannya mendekati atau melewati estimasi tanggal selesai (Overdue), sehingga tim CX dapat meminta maaf & mengabarkan ke customer terlebih dahulu.',
                'how_to_use' => "1. Buka 'Divisi CC' > 'Overdue SLA'.\n2. Prioritaskan SPK dengan warna merah (terlambat).\n3. Hubungi customer untuk memberikan update status pengerjaan.",
                'keywords' => ['overdue sla', 'terlambat', 'spk telat', 'deadline spk', 'sla overdue']
            ],
            [
                'division' => 'cx',
                'division_label' => 'Divisi Customer Experience (CX)',
                'feature_name' => 'Inbox Klaim Garansi',
                'sidebar_location' => 'Sidebar > Divisi CC > Inbox Klaim Garansi',
                'route_name' => 'cx.warranty-claims.index',
                'url_path' => '/cx/warranty-claims',
                'permission' => 'cx.warranty-claims',
                'description' => 'Pusat penanganan klaim garansi resmi (reglue copot, cat luntur, dll.) yang diajukan oleh customer melalui portal publik.',
                'how_to_use' => "1. Buka 'Divisi CC' > 'Inbox Klaim Garansi'.\n2. Cek foto bukti keluhan dari pelanggan.\n3. Setujui klaim (APPROVE) untuk membuat SPK Garansi baru gratis, atau tolak dengan alasan jelas.",
                'keywords' => ['klaim garansi', 'garansi', 'inbox garansi', 'warranty claim', 'komplain garansi']
            ],
            [
                'division' => 'cx',
                'division_label' => 'Divisi Customer Experience (CX)',
                'feature_name' => 'Alamat Terverifikasi',
                'sidebar_location' => 'Sidebar > Divisi CC > Alamat Terverifikasi',
                'route_name' => 'cx.verified-addresses',
                'url_path' => '/cx/verified-addresses',
                'permission' => 'cx.verified-addresses',
                'description' => 'Daftar alamat pengiriman customer yang telah tervalidasi kodepos, kecamatan, dan kelurahannya agar pengiriman balik sepatu via ekspedisi tidak salah alamat.',
                'how_to_use' => "1. Masuk ke 'Divisi CC' > 'Alamat Terverifikasi'.\n2. Periksa alamat yang belum lengkap sebelum sepatu dikirim.",
                'keywords' => ['alamat terverifikasi', 'validasi alamat', 'alamat kirim', 'verifikasi alamat']
            ],
            [
                'division' => 'cx',
                'division_label' => 'Divisi Customer Experience (CX)',
                'feature_name' => 'Manajemen Komplain Pelanggan',
                'sidebar_location' => 'Sidebar > Divisi CC > Komplain',
                'route_name' => 'admin.complaints.index',
                'url_path' => '/admin/complaints',
                'permission' => 'admin.complaints',
                'description' => 'Pencatatan dan eskalasi seluruh keluhan pelanggan untuk audit evaluasi kualitas kerja teknisi dan perbaikan SOP.',
                'how_to_use' => "1. Buka 'Divisi CC' > 'Komplain'.\n2. Catat komplain masuk dan delegasikan ke kepala workshop.",
                'keywords' => ['komplain', 'keluhan', 'complaints', 'masalah servis', 'eskalasi komplain']
            ],

            // =========================================================================
            // 6. MASTER DATA & SYSTEM (ADMINISTRATOR)
            // =========================================================================
            [
                'division' => 'admin',
                'division_label' => 'Master Data & Sistem',
                'feature_name' => 'Master Customer',
                'sidebar_location' => 'Sidebar > Master Data > Master Customer',
                'route_name' => 'admin.customers.index',
                'url_path' => '/admin/customers',
                'permission' => 'admin.customers (Admin, Super Admin)',
                'description' => 'Database seluruh pelanggan ShoeWorkshop: nomor WhatsApp, riwayat order SPK, total spending/LTV, dan badge loyalitas.',
                'how_to_use' => "1. Masuk ke 'Master Data' > 'Master Customer'.\n2. Cari nama atau kontak pelanggan untuk mengedit data profil atau melihat riwayat pesanan terdahulu.",
                'keywords' => ['master customer', 'data pelanggan', 'database customer', 'profil pelanggan']
            ],
            [
                'division' => 'admin',
                'division_label' => 'Master Data & Sistem',
                'feature_name' => 'Manajemen Promo & Diskon',
                'sidebar_location' => 'Sidebar > Master Data > Manajemen Promo',
                'route_name' => 'admin.promotions.index',
                'url_path' => '/admin/promotions',
                'permission' => 'admin.promotions',
                'description' => 'Pengaturan voucher diskon, kode kupon promosi, cashback, dan promo musiman yang dapat diterapkan saat pembuatan invoice.',
                'how_to_use' => "1. Buka 'Master Data' > 'Manajemen Promo'.\n2. Klik 'Tambah Promo Baru', atur persentase potongan atau nominal diskon dan tanggal berlaku.",
                'keywords' => ['manajemen promo', 'promo', 'diskon', 'voucher', 'kupon', 'potongan harga']
            ],
            [
                'division' => 'admin',
                'division_label' => 'Master Data & Sistem',
                'feature_name' => 'Rilis & Pengumuman Internal',
                'sidebar_location' => 'Sidebar > Master Data > Rilis & Pengumuman',
                'route_name' => 'admin.announcements.index',
                'url_path' => '/admin/announcements',
                'permission' => 'admin.announcements',
                'description' => 'Broadcast pengumuman penting perusahaan, info update fitur sistem baru, atau memo operasional kepada seluruh staf bengkel.',
                'how_to_use' => "1. Buka 'Master Data' > 'Rilis & Pengumuman'.\n2. Buat pengumuman baru untuk ditampilkan pada banner dashboard staf.",
                'keywords' => ['pengumuman', 'rilis fitur', 'announcements', 'broadcast', 'info sistem']
            ],
            [
                'division' => 'admin',
                'division_label' => 'Master Data & Sistem',
                'feature_name' => 'Manajemen Pengguna (User Management)',
                'sidebar_location' => 'Sidebar > Master Data > Pengguna',
                'route_name' => 'admin.users.index',
                'url_path' => '/admin/users',
                'permission' => 'admin.users',
                'description' => 'Pengelolaan akun login karyawan: tambah user baru, reset password, nonaktifkan akun, dan penetapan role (CS, Teknisi, Kasir, Gudang, Admin).',
                'how_to_use' => "1. Masuk ke 'Master Data' > 'Pengguna'.\n2. Klik 'Tambah User' untuk staf baru atau edit hak akses role staf yang ada.",
                'keywords' => ['pengguna', 'user management', 'tambah akun', 'role user', 'hak akses', 'ganti password']
            ],
            [
                'division' => 'admin',
                'division_label' => 'Master Data & Sistem',
                'feature_name' => 'Log Aktivitas Sistem (Audit Trail)',
                'sidebar_location' => 'Sidebar > Master Data > Log Aktivitas',
                'route_name' => 'admin.activity-logs.index',
                'url_path' => '/admin/activity-logs',
                'permission' => 'admin.activity-logs',
                'description' => 'Rekaman jejak digital (Audit Log) setiap tindakan yang dilakukan user di sistem: siapa yang mengubah status SPK, input pembayaran, atau mengedit data.',
                'how_to_use' => "1. Buka 'Master Data' > 'Log Aktivitas'.\n2. Filter berdasarkan nama user atau tanggal untuk menelusuri riwayat perubahan data.",
                'keywords' => ['log aktivitas', 'audit log', 'activity logs', 'jejak audit', 'history sistem']
            ],
            [
                'division' => 'admin',
                'division_label' => 'Master Data & Sistem',
                'feature_name' => 'KPI & Performa Karyawan',
                'sidebar_location' => 'Sidebar > Master Data > KPI',
                'route_name' => 'admin.kpi.index',
                'url_path' => '/admin/kpi',
                'permission' => 'admin.performance',
                'description' => 'Evaluasi Key Performance Indicator (KPI) per-teknisi dan per-staf: jumlah sepatu yang selesai dikerjakan, kecepatan pengerjaan, dan tingkat revisi.',
                'how_to_use' => "1. Masuk ke 'Master Data' > 'KPI'.\n2. Tinjau skor produktivitas bulanan masing-masing staf bengkel.",
                'keywords' => ['kpi', 'performa staf', 'produktivitas teknisi', 'kinerja karyawan', 'skor kpi']
            ],
            [
                'division' => 'admin',
                'division_label' => 'Master Data & Sistem',
                'feature_name' => 'Kesehatan & Integritas Data',
                'sidebar_location' => 'Sidebar > Master Data > Kesehatan Data',
                'route_name' => 'admin.data-integrity.index',
                'url_path' => '/admin/data-integrity',
                'permission' => 'admin.data-integrity',
                'description' => 'Pemeriksaan integritas database: mendeteksi SPK tanpa status, invoice gantung, foto hilang, atau ketidaksinkronan saldo transaksi.',
                'how_to_use' => "1. Buka 'Master Data' > 'Kesehatan Data'.\n2. Klik tombol 'Scan Database' untuk menemukan inkonsistensi data dan lakukan perbaikan otomatis.",
                'keywords' => ['kesehatan data', 'data integrity', 'cek database', 'perbaiki data', 'inkonsistensi']
            ],

            // =========================================================================
            // 7. PORTAL PUBLIK (UNTUK PELANGGAN)
            // =========================================================================
            [
                'division' => 'public',
                'division_label' => 'Portal Publik',
                'feature_name' => 'Portal Tracking Publik',
                'sidebar_location' => 'Halaman Publik (Tanpa Login)',
                'route_name' => 'tracking.index',
                'url_path' => '/track',
                'permission' => 'Publik (Bisa dibuka siapa saja)',
                'description' => 'Halaman web yang dapat diakses oleh customer untuk melacak progres reparasi sepatunya cukup dengan memasukkan nomor SPK atau nomor WhatsApp.',
                'how_to_use' => "1. Bagikan link /track ke customer.\n2. Customer memasukkan No. SPK untuk melihat status pengerjaan secara transparan.",
                'keywords' => ['tracking publik', 'portal customer', 'lacak mandiri', 'cek resi spk']
            ],
            [
                'division' => 'public',
                'division_label' => 'Portal Publik',
                'feature_name' => 'Portal Klaim Garansi Publik',
                'sidebar_location' => 'Halaman Publik (Tanpa Login)',
                'route_name' => 'warranty.public-claim',
                'url_path' => '/klaim-garansi',
                'permission' => 'Publik (Bisa dibuka siapa saja)',
                'description' => 'Formulir online resmi bagi customer untuk mengajukan klaim garansi hasil pengerjaan lengkap dengan upload foto keluhan dan nomor SPK sebelumnya.',
                'how_to_use' => "1. Arahkan customer ke halaman /klaim-garansi.\n2. Customer mengisi nomor SPK lama, memilih jenis keluhan, dan melampirkan foto sepatu.",
                'keywords' => ['klaim garansi publik', 'form garansi', 'klaim online', 'garansi customer']
            ]
        ];
    }

    /**
     * Search and match features in navigation by keyword and optional division.
     *
     * @param string|null $query Keyword to search
     * @param string|null $division Specific division filter
     * @return array Matched navigation items
     */
    public static function search(?string $query, ?string $division = null): array
    {
        $all = self::getNavigationTree();
        $query = trim(strtolower($query ?? ''));
        $division = trim(strtolower($division ?? ''));

        // If no query and division is given, return all in that division
        if (empty($query) && !empty($division)) {
            $filtered = array_values(array_filter($all, function ($item) use ($division) {
                return strtolower($item['division']) === $division;
            }));
            return [
                'status' => 'success',
                'total' => count($filtered),
                'division' => $division,
                'items' => $filtered
            ];
        }

        // If both query and division are empty, return division summary
        if (empty($query)) {
            $divisions = [
                'general' => 'Umum & Tracking (Dashboard, Internal Tracking SPK, Tracking Jasa)',
                'cs' => 'Divisi Customer Service (CS Dashboard, Laporan Performa, Konsultasi & Leads)',
                'warehouse' => 'Divisi Gudang (Penerimaan, Outbound, Assessment, Penyimpanan Rak, Gudang Finish, Pengiriman, Belanja)',
                'workshop' => 'Divisi Workshop PWA (Dashboard V2, Fast Track, Prep, Sortir, Produksi, QC Outbound)',
                'finance' => 'Divisi Finance (Dashboard Finance, Waiting Payment, Kasir/Transaksi, Data Invoice, Audit CS, Mutasi)',
                'cx' => 'Divisi Customer Experience / CX (CC Dashboard, Follow Up, Kolam OTO, Konfirmasi After, Overdue SLA, Klaim Garansi, Komplain)',
                'admin' => 'Master Data & Sistem (Master Customer, Promo, Pengguna/User, Log Aktivitas, KPI, Kesehatan Data)',
                'public' => 'Portal Publik (Tracking Publik /track, Klaim Garansi /klaim-garansi)'
            ];

            return [
                'status' => 'success',
                'message' => 'Silakan sebutkan nama fitur atau menu yang ingin Anda ketahui lokasinya.',
                'divisions_available' => $divisions,
                'sample_query' => 'Gunakan query pencarian seperti "laporan performa cs", "kasir", "penyimpanan rak", atau "overdue sla".'
            ];
        }

        // Filter and rank items
        $results = [];
        $searchTerms = preg_split('/\s+/', $query);

        foreach ($all as $item) {
            // Check division match if provided
            if (!empty($division) && strtolower($item['division']) !== $division) {
                continue;
            }

            $score = 0;
            $haystack = strtolower($item['feature_name'] . ' ' . $item['sidebar_location'] . ' ' . $item['description'] . ' ' . implode(' ', $item['keywords']));

            // Exact phrase match
            if (strpos($haystack, $query) !== false) {
                $score += 20;
            }

            // Word-by-word match
            foreach ($searchTerms as $term) {
                if (empty($term)) continue;
                if (strpos($haystack, $term) !== false) {
                    $score += 5;
                }
                foreach ($item['keywords'] as $kw) {
                    if (strpos($kw, $term) !== false) {
                        $score += 8;
                    }
                }
            }

            if ($score > 0) {
                $results[] = [
                    'score' => $score,
                    'data' => $item
                ];
            }
        }

        // Sort descending by score
        usort($results, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $topItems = array_slice(array_map(function ($res) {
            return $res['data'];
        }, $results), 0, 5);

        return [
            'status' => 'success',
            'query' => $query,
            'division_filter' => $division ?: 'all',
            'total_found' => count($topItems),
            'items' => $topItems
        ];
    }
}

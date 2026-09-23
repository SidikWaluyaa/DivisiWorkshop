<?php

namespace App\Services\Ai;

use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Models\WorkOrderRevision;
use App\Models\WorkOrderWarranty;
use App\Models\WorkOrderService;
use App\Models\CxIssue;
use App\Models\Complaint;
use App\Models\StorageAssignment;
use App\Models\OTO;
use App\Models\OrderPayment;
use App\Models\User;
use App\Services\Ai\Knowledge\SystemNavigationMap;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GeminiAiService
{
    protected string $apiKey;
    protected array $apiKeys = [];
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
        $this->apiKeys = config('services.gemini.api_keys', []);
        if (empty($this->apiKeys) && !empty($this->apiKey)) {
            $this->apiKeys = [$this->apiKey];
        }
        $this->model = config('services.gemini.model', 'gemini-3.5-flash-lite');
        $this->baseUrl = config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
    }

    /**
     * Process a chat message with Google Gemini and Tool Calling
     *
     * @param string $userMessage
     * @param array $chatHistory
     * @param int|null $contextOrderId Optional ID of currently viewed order (e.g. on /admin/orders/{id})
     * @param string|null $preferredModel Optional preferred model chosen by user (e.g. gemini-3.5-flash-lite)
     * @return array
     */
    public function chat(string $userMessage, array $chatHistory = [], ?int $contextOrderId = null, ?string $preferredModel = null): array
    {
        // If no API keys configured at all, guide user to Groq AI
        if (empty($this->apiKeys)) {
            Log::info("No Gemini API keys configured.");
            return [
                'success' => true,
                'quota_exceeded' => true,
                'source' => 'gemini',
                'text' => "⚠️ Kunci API Google Gemini belum dikonfigurasi di `.env`.\n\nSilakan aktifkan **⚡ Groq AI** melalui tombol di atas untuk asisten AI bengkel berkecepatan tinggi.",
                'cards' => [],
                'timeline' => null,
                'cover_photo' => null,
                'photos' => null,
                'is_truncated' => false,
            ];
        }

        // Candidate model pool for automatic fallback on 429 rate limits or timeouts
        $defaultCandidates = [
            'gemini-3.5-flash-lite',
            'gemini-3.1-flash-lite',
            'gemini-3.5-flash',
            'gemini-3.8-flash',
            'gemini-3.6-flash',
            'gemini-3.7-flash',
            $this->model,
            'gemini-flash-lite-latest',
        ];

        // If user chose a specific model, prioritize it first in the pool
        if (!empty($preferredModel) && $preferredModel !== 'auto') {
            $modelPool = array_values(array_unique(array_merge([$preferredModel], $defaultCandidates)));
        } else {
            $modelPool = array_values(array_unique($defaultCandidates));
        }

        $initialModel = $modelPool[0] ?? $this->model;
        $lastError = '';

        // Multi-Key Rotation: iterate through available API keys
        foreach ($this->apiKeys as $keyIndex => $currentApiKey) {
            $this->apiKey = $currentApiKey;

            foreach ($modelPool as $activeModel) {
                $result = $this->attemptChatWithModel($activeModel, $userMessage, $chatHistory, $contextOrderId);
                if ($result['success']) {
                    $result['source'] = 'gemini';
                    $result['used_model'] = $activeModel;
                    $result['was_fallback'] = ($activeModel !== $initialModel);
                    $result['requested_model'] = $initialModel;
                    return $result;
                }

                $lastError = $result['text'] ?? '';
                Log::warning("Gemini model {$activeModel} with key #{$keyIndex} failed or limited ({$lastError}). Auto-switching to next model in pool...");
            }
        }

        // If all API keys and models exhausted or failed due to quota limit 429
        Log::warning("All Gemini API keys and models exhausted or failed ({$lastError}). Returning quota_exceeded notification.");
        return [
            'success' => true,
            'quota_exceeded' => true,
            'source' => 'gemini',
            'text' => "⚠️ **Kuota Google Gemini API Sedang Mencapai Batas (Limit 429)**\n\n"
                    . "Kunci API saat ini sedang dibatasi kuota oleh Google AI Studio sehingga Gemini Cloud AI sementara tidak dapat merespons.\n\n"
                    . "⏳ **Kapan Dapat Digunakan Kembali?**\n"
                    . "- **Batas Per-Menit (RPM):** Otomatis reset dalam **~30 - 60 detik**.\n"
                    . "- **Batas Harian (RPD):** Direset setiap hari pukul **07:00 WIB** (00:00 UTC).\n\n"
                    . "💡 *Saran: Anda dapat beralih ke **⚡ Groq AI** melalui tombol di bawah atau tunggu 1 menit lalu klik tombol **Coba Lagi**.*",
            'cards' => [],
            'timeline' => null,
            'cover_photo' => null,
            'photos' => null,
            'is_truncated' => false,
        ];
    }

    /**
     * Attempt single model chat run with function calling (max 3 rounds)
     */
    protected function attemptChatWithModel(string $modelName, string $userMessage, array $chatHistory = [], ?int $contextOrderId = null): array
    {
        $contextHint = "";
        if ($contextOrderId) {
            $contextSpk = WorkOrder::find($contextOrderId);
            if ($contextSpk) {
                $contextHint = "Saat ini pengguna sedang membuka halaman SPK {$contextSpk->spk_number} (ID: #{$contextOrderId}, Customer: {$contextSpk->customer_name}). Jika pengguna bertanya tanpa menyebutkan nomor SPK, asumsikan pertanyaannya ditujukan untuk SPK {$contextSpk->spk_number}.\n";
            } else {
                $contextHint = "Saat ini pengguna membuka SPK ID #{$contextOrderId}. Jika pengguna bertanya tanpa menyebutkan nomor, asumsikan SPK ID #{$contextOrderId}.\n";
            }
        }

        $systemInstruction = "Kamu adalah **Workshop AI Copilot**, asisten pintar resmi sistem bengkel sepatu ShoeWorkshop.\n"
            . "Tugasmu: membantu staf internal melacak pesanan (Internal Tracking), memberikan detail Work Order (SPK), riwayat pengerjaan, dan **analitik operasional bengkel**.\n"
            . "ATURAN RESPON: Jawab dengan padat, ringkas, langsung ke inti (to the point), dan ramah. Gunakan emoji untuk memperjelas.\n"
            . "GAYA BAHASA & FORMAT: HINDARI penggunaan tanda kutip dua (\"\") untuk istilah atau nama status/tahap (jangan tulis \"Preparation\" atau \"Selesai\"). Gunakan cetak tebal **Preparation** atau tuliskan secara alami tanpa tanda kutip agar kalimat mengalir santun dan nyaman dibaca.\n"
            . "\n"
            . "## 🔒 Kebijakan Keamanan Sistem & Pembatasan Read-Only (MUTLAK):\n"
            . "- Kamu adalah asisten pelacakan dan analitik yang beroperasi 100% dalam mode READ-ONLY (Hanya Membaca Data).\n"
            . "- Kamu TIDAK MEMILIKI tool, izin, fungsi, ataupun otorisasi untuk MENGHAPUS, MENGUBAH, MENGEDIT, ataupun MEMBATALKAN SPK, invoice, pembayaran, nama pelanggan, maupun status pengerjaan apapun di database bengkel.\n"
            . "- Jika ada pengguna/staf yang meminta untuk menghapus SPK (misal: 'hapus SPK ini', 'delete order', 'batalkan SPK'), mengedit data (misal: 'ubah nama pelanggan', 'edit biaya', 'pindahkan status'), atau memanipulasi database:\n"
            . "  TOLAK DENGAN TEGAS, JELAS, DAN SANTUN!\n"
            . "  Jelaskan bahwa demi kepatuhan SOP audit trail dan integritas data workshop, seluruh perubahan atau penghapusan data HANYA dapat dilakukan secara manual oleh staf berwenang melalui formulir resmi pada dashboard sistem.\n"
            . "\n"
            . "## Tool Routing Guide:\n"
            . "- Pertanyaan cari SPK (nama/nomor/telepon) → `search_work_orders`\n"
            . "- Detail 1 SPK (customer, jasa, invoice, teknisi) → `get_work_order_detail`\n"
            . "- Riwayat perpindahan status / kapan masuk tahap tertentu (Preparation, Sortir, Produksi, QC) / log aktivitas → `get_production_tracking` atau `get_work_order_timeline`\n"
            . "- Progress produksi, durasi pengerjaan, deteksi bottleneck → `get_production_tracking`\n"
            . "- Statistik jumlah SPK, overdue, breakdown status → `get_spk_overview_stats`\n"
            . "- Keuangan, KPI Finance (/admin/kpi), kas masuk tervalidasi, piutang aktif, rasio penagihan, status invoice, distribusi pembayaran kas, serta transaksi batal & refund → `get_financial_summary`\n"
            . "- Beban kerja teknisi, performa → `get_technician_analytics`\n"
            . "- Revisi, garansi, kerugian → `get_revision_warranty_data`\n"
            . "- Kendala pelanggan, keluhan (Complaints), SPK bermasalah/tertunda, atau status kendala OPEN/RESOLVED → `get_cx_issues_data`\n"
            . "- KPI Gudang (/admin/kpi - Tab Gudang), sepatu masuk (before), SPK print/OTW workshop, QC reject, after masuk, sepatu keluar (serah terima), lokasi rak sepatu, atau logistik gudang → `get_storage_logistics`\n"
            . "- Intelejen Produksi & Workshop, beban kerja teknisi (siapa overload/terbanyak antrean), antrean stasiun live (Prep, Sortir, Produksi, QC), SPK overdue / terancam telat SLA, dan KPI Workshop (/admin/kpi - Tab Workshop) → `get_workshop_production_intelligence`\n"
            . "- OTO (penawaran tambahan) → `get_oto_data`\n"
            . "- Foto dokumentasi pengerjaan (before, after, referensi penerimaan, QC/produksi) → `get_work_order_photos`\n"
            . "- Navigasi sidebar, letak menu/fitur, rute URL (/path), hak akses/role, atau panduan cara penggunaan fitur sistem → `get_feature_navigation`\n"
            . "- Surat jalan, manifest inbound/outbound (Gudang ➔ Workshop), status transfer antar divisi (Sortir ➔ Produksi ➔ QC), audit pengiriman ekspedisi, deteksi SPK/manifest macet (stuck in transit > 24 jam), resi pengiriman, dan audit selisih finansial ongkir (subsidi workshop) → `get_manifest_shipping_intelligence`\n"
            . "\n"
            . "## Format Penyajian Ringkasan KPI Gudang & Logistik (/admin/kpi - Tab Gudang):\n"
            . "Ketika pengguna menanyakan 'KPI Gudang', 'kinerja gudang', 'logistik gudang', 'sepatu masuk/keluar gudang', atau ringkasan logistik pada periode tertentu, selalu panggil tool `get_storage_logistics` (mode aggregate) dan sajikan respon dalam format Kartu Eksekutif resmi:\n"
            . "### 📦 Ringkasan KPI Gudang & Logistik ([Periode/Rentang Tanggal])\n"
            . "- 📥 **1. Sepatu Masuk (Before):** [x] Pasang *(Diterima fisik di gudang)*\n"
            . "- 🚚 **2. SPK Print (OTW WS):** [x] Pasang *(Dikirim ke reparasi / manifest workshop)*\n"
            . "- ⚠️ **3. SPK Tertahan (QC Reject):** [x] Pasang *(Gagal penerimaan awal)*\n"
            . "- ✨ **4. After Masuk:** [x] Pasang *(Selesai reparasi masuk rak gudang)*\n"
            . "- 📤 **5. Sepatu Keluar:** [x] Pasang *(Pengambilan customer & kirim lunas)*\n"
            . "\n"
            . "### 🏷️ Status Operasional Rak & Logistik Fisik Saat Ini:\n"
            . "- 👟 **Total Sepatu di Rak:** [x] item tersimpan aktif\n"
            . "- ⏳ **Barang Tertahan Lama (>7 Hari):** [x] item overdue\n"
            . "- 🔄 **Sepatu Selesai Menunggu Pengambilan:** [x] SPK belum diambil pelanggan\n"
            . "- 📍 **Utilisasi Rak Terpadat:** [Sebutkan 3-5 rak terisi terbanyak beserta jumlah pasangnya]\n"
            . "\n"
            . "### 💡 Analisis Kelancaran Logistik Gudang:\n"
            . "[Berikan insight profesional: jika Sepatu Masuk > SPK OTW WS ingatkan tim gudang untuk segera memproses manifest kirim ke workshop; jika After Masuk > Sepatu Keluar ingatkan bahwa barang selesai menumpuk di rak dan sarankan CS mem-follow up customer untuk pengambilan/pelunasan]\n"
            . "\n"
            . "## Format Penyajian Resmi KPI Workshop (/admin/kpi - Tab Workshop):\n"
            . "Ketika pengguna menanyakan ringkasan resmi 'KPI Workshop', 'kinerja workshop', atau meminta ringkasan beban kerja stasiun workshop pada periode tertentu:\n"
            . "Panggil tool `get_workshop_production_intelligence` (mode 'all' atau 'kpi_overview') dan SELALU sajikan jawaban selaras 100% dengan tampilan kartu dashboard /admin/kpi Tab KPI WORKSHOP:\n"
            . "### 🛠️ Ringkasan Kinerja & Beban Kerja Divisi Workshop ([Periode])\n"
            . "\n"
            . "#### 🧼 1. PREPARATION *(Tahap Cuci & Pembongkaran)*\n"
            . "- 📥 **Total Masuk:** [x] SPK\n"
            . "- 📤 **Total Keluar:** [x] SPK\n"
            . "- ✨ **Net (Bersih):** Masuk: [x] • Keluar: [x]\n"
            . "\n"
            . "#### 🔍 2. SORTIR *(Tahap Sortir & Kelengkapan Material)*\n"
            . "- 📥 **Total Masuk:** [x] SPK\n"
            . "- 📤 **Total Keluar:** [x] SPK\n"
            . "- ✨ **Net (Bersih):** Masuk: [x] • Keluar: [x]\n"
            . "\n"
            . "#### 🛠️ 3. PRODUCTION *(Tahap Produksi / Repacking & Reparasi)*\n"
            . "- 📥 **Total Masuk:** [x] SPK\n"
            . "- 📤 **Total Keluar:** [x] SPK\n"
            . "- ✨ **Net (Bersih):** Masuk: [x] • Keluar: [x]\n"
            . "\n"
            . "#### ✅ 4. QUALITY CONTROL *(Tahap Quality Control & Finishing)*\n"
            . "- 📥 **Total Masuk:** [x] SPK\n"
            . "- 📤 **Total Keluar:** [x] SPK\n"
            . "- ✨ **Net (Bersih):** Masuk: [x] • Keluar: [x]\n"
            . "\n"
            . "#### ⚠️ CX FOLLOW UP *(Laporan Anomali Status)*\n"
            . "- **Pergerakan ke CX (Kendala Stasiun):**\n"
            . "  • PREPARATION → CX: [x] SPK\n"
            . "  • SORTIR → CX: [x] SPK\n"
            . "  • PRODUCTION → CX: [x] SPK\n"
            . "  • QC → CX: [x] SPK\n"
            . "- **Pergerakan dari CX (Kembali ke Pengerjaan):**\n"
            . "  • CX → PREPARATION: [x] SPK\n"
            . "  • CX → SORTIR: [x] SPK\n"
            . "  • CX → PRODUCTION: [x] SPK\n"
            . "  • CX → QC: [x] SPK\n"
            . "\n"
            . "---\n"
            . "\n"
            . "## Format Penanganan Audit & Pertanyaan Kritis KPI Workshop (Big 4 Standard):\n"
            . "Ketika pengguna menanyakan analisis kritis, audit proses, atau kejanggalan pada angka KPI Workshop:\n"
            . "Jawab dengan analisis operasional yang tajam, logis, dan profesional tanpa halusinasi:\n"
            . "1. **'Mengapa di stasiun Produksi / QC jumlah SPK yang KELUAR bisa lebih banyak daripada yang MASUK?':**\n"
            . "   - Jelaskan konsep **Carry-Over Work in Progress (WIP)**: SPK yang keluar di Produksi/QC pada bulan ini merupakan pesanan limpahan yang sudah masuk dan mengendap dari periode sebelumnya (misal Agustus/Juli) yang baru diselesaikan pengerjaan/inspeksinya pada bulan berjalan.\n"
            . "   - Hal ini merupakan indikasi positif terjadinya **Flushing / Backlog Clearance** (pengurangan timbunan antrean pekerjaan lama), namun berikan catatan bahwa jika stasiun hulu (Prep & Sortir) tidak memasukkan pesanan baru yang cukup, workshop berpotensi mengalami kekosongan pekerjaan di siklus berikutnya.\n"
            . "2. **'Stasiun mana yang mengalami hambatan (bottleneck) dan apakah terjadi starvation atau choking antar stasiun?':**\n"
            . "   - Analisis keseimbangan lini (**Line Balancing**):\n"
            . "     • **Choking (Kewalahan/Tumpukan):** Terjadi jika stasiun tertentu memiliki antrean aktif jauh lebih tinggi daripada kapasitas outputnya (misal stasiun Produksi dengan 15 SPK mengantre).\n"
            . "     • **Starvation Risk (Kelaparan Input):** Terjadi jika stasiun hulu (Preparation & Sortir) hanya memasukkan sedikit SPK (misal 1 SPK), sehingga stasiun hilir (Produksi & QC) terancam kekurangan suplai sepatu setelah backlog selesai.\n"
            . "   - Rekomendasikan sinkronisasi aliran manifest pengiriman dari gudang ke workshop.\n"
            . "3. **'Mengapa ada SPK yang mental / dialihkan ke CX Follow Up dan stasiun mana penyumbang kendala terbanyak?':**\n"
            . "   - Jelaskan bahwa CX Follow Up adalah pintu penanganan anomali pengerjaan (misal: perlu konfirmasi tambahan biaya/OTO ke customer, material rusak yang butuh persetujuan khusus, atau customer request tahan pengerjaan).\n"
            . "   - Jika pergerakan bernilai 0 (seperti saat ini), nyatakan bahwa operasional periode ini sangat stabil dan steril dari eskalasi kendala pelanggan (zero escalation).\n"
            . "4. **'Berapa rata-rata durasi pengerjaan per stasiun dan apakah ada tahapan yang melampaui SLA wajar?':**\n"
            . "   - Tampilkan durasi rata-rata pengerjaan per tahap dari data KPI. Jelaskan stasiun mana yang paling memakan waktu dan berikan saran pemecahan stasiun kerja (sub-station breakdown).\n"
            . "\n"
            . "## Format Penyajian Intelejen Produksi, Beban Teknisi & Workshop (Live Floor & Overdue):\n"
            . "Ketika pengguna menanyakan kendala produksi, SPK overdue di workshop, beban kerja teknisi, antrean stasiun live, atau analisis bottleneck workshop:\n"
            . "Panggil tool `get_workshop_production_intelligence` dan sajikan dalam format Kartu Eksekutif resmi:\n"
            . "### 🚨 Status Deadline & SPK Overdue Workshop:\n"
            . "- 🔴 **Total SPK Overdue (Melewati Estimasi):** [x] SPK\n"
            . "- ⚠️ **Mendekati Deadline (<= 2 Hari):** [x] SPK\n"
            . "- ⚡ **SPK Fast Track Aktif:** [x] SPK\n"
            . "- **Top SPK Kritis Terlambat:**\n"
            . "  1. 🔴 **[Nomor SPK]** — [Customer] ([Sepatu]) | Tahap: [Status] | Telat: [x] Hari | PJ: [Teknisi]\n"
            . "  ---\n"
            . "\n"
            . "### 👥 Distribusi Beban Kerja Teknisi (Live Floor):\n"
            . "- **Teknisi Terpadat (Overload Alert):**\n"
            . "  1. ⚠️ **[Nama Teknisi]**: [x] SPK aktif\n"
            . "- **Kapasitas Tersedia (0-1 SPK):** [Nama teknisi yang sedang lengang]\n"
            . "\n"
            . "### ⏳ Antrean Stasiun Live Workshop:\n"
            . "- 🧪 **Preparation:** [x] SPK (Cuci: [a] • Sol: [b] • Upper: [c])\n"
            . "- 🔍 **Sortir:** [x] SPK (Perlu Bongkar: [a] • Perlu Belanja: [b])\n"
            . "- ⚙️ **Produksi:** [x] SPK (Soling: [a] • Upper: [b] • Treatment: [c])\n"
            . "- 🔬 **Quality Control (QC):** [x] SPK (Jahit: [a] • Cleanup: [b] • Final: [c])\n"
            . "\n"
            . "### 🏭 Analisis Throughput & Bottleneck Stasiun ([Periode]):\n"
            . "- **Stasiun Bottleneck:** [Stasiun dengan antrean tertinggi / durasi pengerjaan terlama]\n"
            . "- **Throughput Bersih:** Prep ([in] -> [out]) • Sortir ([in] -> [out]) • Prod ([in] -> [out]) • QC ([in] -> [out])\n"
            . "- 💡 **Rekomendasi Operasional Workshop:** [Saran konkrit mitigasi / penyeimbangan antrean]\n"
            . "\n"
            . "## Format Penyajian KPI Finance & Keuangan (/admin/kpi):\n"
            . "Ketika pengguna menanyakan ringkasan keuangan, KPI Finance, kas masuk, piutang, omset, atau transaksi refund:\n"
            . "Sajikan dengan format kartu ringkasan eksekutif yang rapi, padat, dan elegan (selaras 100% dengan dashboard /admin/kpi):\n"
            . "### 💰 Ringkasan KPI Finance ([Periode/Rentang Tanggal])\n"
            . "- 📑 **Total Nilai Tagihan:** Rp [nominal] *(Invoice diterbitkan periode ini)*\n"
            . "- 💵 **Kas Masuk (Tervalidasi):** Rp [nominal] *(Penerimaan kas riil periode ini)*\n"
            . "- ⏳ **Sisa Piutang Aktif:** Rp [nominal] *(Belum tertagih dari tagihan periode ini)*\n"
            . "- 🎯 **Rasio Penagihan (Collection Rate):** [persen]% *(Penerimaan vs Tagihan)*\n"
            . "- 🏷️ **Realisasi Omset (Valid Closing):** Rp [nominal]\n"
            . "- 🎁 **Total Diskon Diberikan:** Rp [nominal]\n"
            . "\n"
            . "### 📊 Status Invoice & Distribusi Pembayaran:\n"
            . "- **Status Invoice:** [x] Belum Bayar (Rp [y]) • [x] DP/Cicil (Rp [y]) • [x] Lunas (Rp [y])\n"
            . "- **Distribusi Kas:** DP Awal: Rp [y] ([x] trx) • Pelunasan: Rp [y] ([x] trx) • Lunas Awal: Rp [y] • Ongkir: Rp [y] • OTO: Rp [y]\n"
            . "\n"
            . "### ↩️ Transaksi Batal & Refund:\n"
            . "- **SPK Dibatalkan:** [x] pesanan\n"
            . "- **Total Dana Refund (Kembali ke Customer):** Rp [nominal]\n"
            . "\n"
            . "## Format Penanganan Audit Kritis Finance (Big 4 Standard):\n"
            . "Ketika pengguna menanyakan hal kritis terkait keuangan workshop:\n"
            . "1. **'Apakah ada sepatu yang sudah Selesai tapi belum Lunas?' / 'Audit risiko pengiriman':**\n"
            . "   - Sajikan section `### 🚨 Audit Risiko Pengiriman (Sepatu Selesai/Diantar Belum Lunas)`\n"
            . "   - Sebutkan total SPK berisiko dan total akumulasi piutang yang menggantung.\n"
            . "   - Rinci daftar SPK berisiko (Nomor SPK, Nama Pelanggan, Status SPK, Sisa Piutang, Tanggal Selesai).\n"
            . "   - Berikan rekomendasi mitigasi SOP: Instruksikan tim CS/Gudang untuk **MENAHAN (HOLD)** serah terima fisik sepatu sampai bukti pelunasan diverifikasi Finance.\n"
            . "2. **'Mengapa Rasio Penagihan bisa di atas 100% atau di bawah 50%?':**\n"
            . "   - Jelaskan dinamika arus kas secara profesional: jika > 100%, jelaskan bahwa penerimaan kas riil lebih besar dari nilai tagihan invoice periode ini karena adanya penagihan/pelunasan piutang aktif dari tagihan bulan-bulan sebelumnya yang berhasil ditarik masuk kas. Jika < 50%, beri peringatan risiko penumpukan piutang macet (bad debt risk) dan perlunya percepatan penagihan.\n"
            . "3. **'Berapa total kerugian / kebocoran biaya workshop?':**\n"
            . "   - Sajikan section `### 📉 Rekap Kebocoran Biaya & Kerugian Workshop`\n"
            . "   - Bedakan dengan tegas antara:\n"
            . "     • **Potensi Omset Hilang dari Pembatalan:** Rp [nominal]\n"
            . "     • **Kas Riil Keluar untuk Refund:** Rp [nominal]\n"
            . "     • **Biaya Kerugian Revisi Teknisi:** Rp [nominal]\n"
            . "     • **Biaya Kerugian Klaim Garansi:** Rp [nominal]\n"
            . "     • **Total Beban Kerugian Kas/Operasional:** Rp [nominal]\n"
            . "4. **'Buatkan draf penagihan WhatsApp ke customer':**\n"
            . "   - Buatkan draf template pesan WhatsApp profesional, santun, persuasif, menyebutkan nomor SPK, jenis sepatu, nominal sisa tagihan, dan nomor rekening/QRIS resmi. Bungkus seluruh pesan di dalam blockquote markdown (`> `) agar mudah disalin.\n"
            . "5. **'Ubah status invoice jadi Lunas' / 'Hapus tagihan customer':**\n"
            . "   - TOLAK SECARA MUTLAK & TEGAS! Jelaskan bahwa AI Copilot beroperasi 100% Read-Only demi kepatuhan SOP Audit Trail dan keamanan kas perusahaan.\n"
            . "\n"
            . "## Format Penyajian Intelejen Manifest & Logistik Pengiriman:\n"
            . "Ketika pengguna menanyakan surat jalan, manifest pengiriman, audit resi, atau selisih ongkir:\n"
            . "Panggil tool `get_manifest_shipping_intelligence` dan sajikan respon dalam format Kartu Eksekutif resmi:\n"
            . "### 🚚 Intelejen Logistik & Manifest Pengiriman ([Periode])\n"
            . "- 📥 **Inbound Manifest (Gudang ➔ Workshop):** [x] manifest total ([y] SPK) • [z] Selesai Diterima • [w] Tertahan/Stuck\n"
            . "- 🔄 **Internal Transfer Surat Jalan:** [x] surat jalan antar divisi ([y] pasang sepatu diperiksa)\n"
            . "- 📦 **Outbound Delivery:** [x] SPK kirim ekspedisi • [y] resi terverifikasi • [z] menunggu resi/pickup\n"
            . "- 💸 **Audit Finansial Ongkir:** Tagihan Customer: Rp [x] • Biaya Aktual: Rp [y] • Selisih/Subsidi: Rp [z]\n"
            . "\n"
            . "## Format Penanganan Audit Kritis Logistik & Manifest (Big 4 Standard):\n"
            . "Ketika pengguna menanyakan hal kritis terkait rantai pasok dan logistik workshop:\n"
            . "1. **'Apakah ada SPK atau manifest pengiriman dari gudang ke workshop yang belum diterima atau menggantung (stuck in transit > 24 jam)?':**\n"
            . "   - Periksa manifest berstatus SENT yang belum memiliki received_at serta SPK OTW_WORKSHOP.\n"
            . "   - Jika ADA yang melebihi batas toleransi SLA 24 jam:\n"
            . "     Sajikan `### 🚨 Peringatan Kritis: Manifest Stuck in Transit (> 24 Jam)`\n"
            . "     Rinci nomor manifest, nama dispatcher pengirim, tanggal kirim, durasi keterlambatan, dan daftar SPK di dalamnya.\n"
            . "     Rekomendasikan tindakan mitigasi darurat: hubungi dispatcher gudang dan periksa fisik barang di pos serah terima.\n"
            . "   - Jika TIDAK ADA yang menggantung:\n"
            . "     Nyatakan secara tegas dan melegakan bahwa seluruh manifest dan SPK inbound berada dalam status aman/terverifikasi diterima (100% On-Track, zero stuck manifest).\n"
            . "2. **'Ada berapa sepatu yang sudah selesai (Finished) dengan metode delivery tapi belum memiliki nomor resi atau belum di-pickup ekspedisi?':**\n"
            . "   - Tampilkan section `### 📦 Audit Outbound Ready-to-Ship (Pending Resi / Kurir Pickup)`\n"
            . "   - Rinci nomor SPK, nama pelanggan, ekspedisi/metode kirim, tanggal selesai, dan status resi.\n"
            . "   - Jika tidak ada (0 SPK tertahan), nyatakan bahwa seluruh pesanan delivery yang selesai telah memiliki resi valid / diproses tuntas.\n"
            . "   - Berikan rekomendasi SOP: instruksikan tim packing dan admin pengiriman untuk segera melakukan serah terima ke kurir dan update nomor resi ke customer jika ada yang pending.\n"
            . "3. **'Berapa total selisih biaya ongkir bulan ini antara yang dibayar customer vs biaya riil ekspedisi?':**\n"
            . "   - Tampilkan section `### 💸 Audit Finansial Selisih Ongkir & Subsidi Workshop`\n"
            . "   - Sajikan perbandingan metrik: Total Ongkir Ditagihkan ke Pelanggan, Total Biaya Riil Ekspedisi, dan Net Subsidi Bengkel.\n"
            . "   - Rinci SPK mana saja yang disubsidi oleh bengkel beserta selisih nominalnya (misal: SPK S-2608-12-0017-SW disubsidi Rp 10.000 karena ongkir customer Rp 0 sedangkan biaya riil ekspedisi Rp 10.000).\n"
            . "   - Berikan insight keuangan: evaluasi acuan tarif ongkir sistem agar subsidi gratis ongkir terukur dan tidak menggerus margin laba reparasi.\n"
            . "4. **'Apakah ada riwayat surat jalan transfer antar divisi yang mencatat kondisi fisik bermasalah atau rusak saat serah terima?':**\n"
            . "   - Tampilkan section `### 🔍 Audit Rantai Serah Terima Fisik (Chain of Custody & Kondisi Fisik)`\n"
            . "   - Periksa field `kondisi_serah_terima` dan catatan pada seluruh surat jalan (Sortir ➔ Produksi dan Produksi ➔ QC).\n"
            . "   - Laporkan apakah seluruh serah terima berstatus **Baik / Sesuai Fisik** atau jika ada catatan cacat/rusak/hilang.\n"
            . "   - Jika seluruhnya berstatus Baik / Sesuai Fisik (seperti 34 item saat ini), nyatakan bahwa integritas fisik sepatu terjaga 100% tanpa komplain cacat serah terima antar teknisi.\n"
            . "\n"
            . "## Format Penyajian Panduan Fitur & Navigasi Sidebar:\n"
            . "Ketika menjelaskan fitur atau menu pada sidebar, sajikan secara lengkap, terstruktur, dan elegan:\n"
            . "### 🧭 [Nama Fitur]\n"
            . "- 📍 **Lokasi Sidebar:** `[Jalur Sidebar / Breadcrumb, misal: Sidebar > Divisi CS > Laporan Performa]`\n"
            . "- 🔗 **Tautan Rute:** [Buka Halaman [Nama Fitur]](url_path) `(url_path)`\n"
            . "- 🔐 **Hak Akses:** [Role / Permission yang dibutuhkan]\n"
            . "- 📋 **Fungsi Utama:** [Penjelasan padat fungsi dan kegunaan halaman ini bagi operasional bengkel]\n"
            . "- 🚀 **Panduan Penggunaan:**\n"
            . "  [Langkah-langkah 1, 2, 3 cara staf menggunakan fitur tersebut]\n"
            . "\n"
            . "## Aturan Struktur Jawaban Panjang & Multi-Item:\n"
            . "- Ketika menampilkan daftar lebih dari 1 SPK, kendala, atau opsi layanan: SELALU pisahkan setiap item/SPK dengan garis pembatas horizontal `---` agar tidak menumpuk menjadi satu blok teks padat.\n"
            . "- Gunakan heading tegas pada setiap item (misal `### 1. ⚠️ **[Nomor SPK]** — [Customer] ([Sepatu])`) lalu rincian di bawahnya menggunakan sub-bullet point.\n"
            . "- Jika memberikan contoh atau draft pesan WhatsApp untuk pelanggan: BUNGKUS setiap teks pesan di dalam blockquote markdown (awali paragraf pesan dengan tanda `> `) agar otomatis tampil dalam kotak kartu pesan elegan yang siap disalin oleh staf.\n"
            . "\n"
            . "## Format Penyajian Kendala Pelanggan (CX Issues):\n"
            . "- Jika ditanya daftar SPK yang memiliki kendala/keluhan OPEN, sajikan setiap SPK secara terpisah dengan garis pembatas `---`:\n"
            . "  ### 1. ⚠️ **[Nomor SPK]** — [Customer] ([Sepatu])\n"
            . "  - 📌 **Kategori:** [Kategori]\n"
            . "  - ❗ **Kendala:** [Deskripsi Kendala]\n"
            . "  - 💡 **Opsi Solusi:** [Opsi Solusi]\n"
            . "  - ⏳ **Durasi Tertunda:** [x hari]\n"
            . "  ---\n"
            . "- Jika ditanya kendala pada 1 SPK tertentu, sebutkan SEMUA kendalanya, baik yang masih **OPEN** maupun yang sudah **SELESAI (RESOLVED)** beserta waktu selesai, staf yang menyelesaikan, dan catatan solusinya.\n"
            . "\n"
            . "## Format Penyajian Timeline Riwayat Pengerjaan SPK:\n"
            . "Ketika pengguna meminta rangkuman timeline / riwayat pengerjaan SPK, sajikan secara ringkas, kronologis, dan mudah dibaca:\n"
            . "1. Sebutkan nomor SPK, nama pelanggan, dan jenis sepatu di awal.\n"
            . "2. Buat section `### 🔄 Perpindahan Status Utama` yang merangkum tanggal dan tahapan penting (Preparation ➔ Sortir ➔ Production ➔ QC ➔ Selesai).\n"
            . "3. Buat section `### 🛠️ Aktivitas Teknisi & Pengerjaan Terakhir` yang merangkum siapa teknisi yang mengerjakan, layanan apa yang selesai, dan status pengerjaan saat ini.\n"
            . "JANGAN mengulang semua 30+ log satu per satu tanpa filter, tetapi rangkum intisari perjalanannya agar cepat dan padat informasi bagi admin workshop.\n"
            . "\n"
            . "## Format Penyajian Layanan Jasa:\n"
            . "Ketika pengguna menanyakan layanan/jasa pada SPK, sebutkan nama sepatu & customer, lalu sajikan daftar layanan dengan format terstruktur rapi per item (gunakan `---` pemisah jika banyak):\n"
            . "### 1. 🛠️ **[Nama Layanan]** (Kategori: [Kategori])\n"
            . "- 💰 **Biaya:** Rp [nominal]\n"
            . "- 👨‍🔧 **Teknisi:** [Nama Teknisi] (Status: [Status])\n"
            . "- 📝 **Rincian/Spesifikasi:** [Rincian/spesifikasi manual jika ada]\n"
            . "- 📌 **Catatan:** [Catatan pengerjaan jika ada]\n"
            . "- ⏱️ **Waktu Pengerjaan:** [Mulai / Selesai jika tercatat]\n"
            . "Jika ada total biaya seluruh layanan, sebutkan totalnya di akhir.\n"
            . "\n"
            . "## Aturan Penyajian Foto SPK:\n"
            . "- Jika pengguna bertanya tentang info umum / detail SPK: Cukup gunakan `get_work_order_detail`. Sistem antarmuka akan secara otomatis menyajikan 1 foto cover (before) yang rapi di bawah info SPK.\n"
            . "- Jika pengguna menanyakan FOTO secara khusus (misal: 'mana foto before afternya?', 'tampilkan foto referensi', 'ada foto sepatunya?'): Gunakan tool `get_work_order_photos`. Jelaskan secara ringkas berapa foto yang tersedia (before, after, referensi), dan sistem akan menampilkan galeri foto visual interaktifnya secara otomatis.\n"
            . "\n"
            . "## Aturan Klarifikasi & Penanganan Data Ambigu:\n"
            . "- Jika pengguna menanyakan data seseorang (misal: 'nomor telepon bapak Budi' atau 'posisi sepatu Budi') dan di sistem ditemukan LEBIH DARI 1 orang dengan nama yang sama/mirip:\n"
            . "  JANGAN menebak salah satu, JANGAN berasumsi, dan JANGAN memunculkan kartu SPK!\n"
            . "  Tanyakan langsung secara ramah kepada pengguna untuk konfirmasi klarifikasi dengan menyebutkan pilihannya:\n"
            . "  Contoh respon:\n"
            . "  'Terdapat lebih dari satu pelanggan dengan nama **Budi** di sistem kami:\n"
            . "  1. **Budi Santoso** — Nike Air Max (SPK: S-2607-01-0003-SW)\n"
            . "  2. **Budi FastTrack** — Nike Black-Orange (SPK: S-2608-12-0001-SW)\n"
            . "  Bapak Budi mana yang ingin Anda ketahui informasinya?'\n"
            . "\n"
            . "## Aturan Pemunculan Kartu SPK (Card):\n"
            . "- HANYA set `show_cards = true` pada tool `search_work_orders` jika pengguna SECARA EKSPLISIT meminta mencari atau menampilkan kartu daftar SPK (misal: 'cari SPK 18', 'tampilkan SPK Budi', 'lihat daftar SPK').\n"
            . "- JANGAN memunculkan kartu SPK (set `show_cards = false`) jika pengguna hanya menanyakan informasi spesifik (seperti nomor telepon, biaya, siapa teknisi, rak berapa, status) atau saat kamu sedang meminta klarifikasi data ambigu.\n"
            . "\n"
            . "## Penanganan Permintaan 'Lanjutkan':\n"
            . "- Jika pengguna meminta 'Lanjutkan', 'Lanjutkan penjelasan sebelumnya', atau sejenisnya: Periksa pesan terakhirmu sebelumnya yang terpotong. Lanjutkan langsung pembahasannya dari titik terakhir tanpa mengulang salam atau kata pengantar dari awal.\n"
            . "\n"
            . "Untuk pertanyaan kompleks, kamu BOLEH memanggil beberapa tool sekaligus untuk cross-reference data.\n"
            . "Saat menjawab analytics/statistik, format jawaban dengan bullet points dan angka yang jelas.\n"
            . $contextHint;

        $tools = [
            [
                'functionDeclarations' => $this->buildToolDeclarations(),
            ],
        ];

        // Prepare contents array (limit to last 4 messages to minimize payload and latency)
        $contents = [];
        $recentHistory = array_slice($chatHistory, -4);

        foreach ($recentHistory as $msg) {
            $role = ($msg['role'] ?? 'user') === 'user' ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $msg['content'] ?? '']
                ]
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage]
            ]
        ];

        $capturedCards = [];
        $capturedTimeline = null;
        $capturedCoverPhoto = null;
        $capturedPhotos = null;

        try {
            $endpoint = rtrim($this->baseUrl, '/') . "/models/{$modelName}:generateContent";
            $maxRounds = 3;

            $generationConfig = [
                'temperature' => 0.2,
                'maxOutputTokens' => 4096,
            ];
            if (!str_contains($modelName, 'flash-lite')) {
                $generationConfig['thinkingConfig'] = ['thinkingBudget' => 0];
            }

            for ($round = 0; $round < $maxRounds; $round++) {
                $requestPayload = [
                    'systemInstruction' => [
                        'parts' => [['text' => $systemInstruction]]
                    ],
                    'contents' => $contents,
                    'tools' => $tools,
                    'generationConfig' => $generationConfig,
                ];

                $response = Http::withHeaders([
                    'x-goog-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                        CURLOPT_TCP_NODELAY => true,
                    ],
                ])->timeout(30)->post($endpoint, $requestPayload);

                if ($response->status() === 429) {
                    return ['success' => false, 'text' => "Model {$modelName} kuota limit (429)", 'cards' => [], 'timeline' => null, 'cover_photo' => null, 'photos' => null];
                }

                if (!$response->successful()) {
                    return ['success' => false, 'text' => "HTTP Error {$response->status()}", 'cards' => [], 'timeline' => null, 'cover_photo' => null, 'photos' => null];
                }

                $responseData = $response->json();
                $candidate = $responseData['candidates'][0] ?? null;
                $parts = $candidate['content']['parts'] ?? [];

                // Check if model called a function
                $functionCalls = [];
                foreach ($parts as $part) {
                    if (isset($part['functionCall'])) {
                        $functionCalls[] = $part['functionCall'];
                    }
                }

                if (empty($functionCalls)) {
                    // No function calls — extract text and return
                    $text = '';
                    foreach ($parts as $p) {
                        if (isset($p['text'])) {
                            $text .= $p['text'];
                        }
                    }

                    $finishReason = $candidate['finishReason'] ?? 'STOP';
                    $isTruncated = ($finishReason === 'MAX_TOKENS');

                    $cleanText = trim($text);
                    if ($isTruncated) {
                        // Strip trailing unclosed brackets or punctuation to avoid hanging markdown blocks
                        $cleanText = rtrim($cleanText, " \t\n\r\0\x0B([*>#-_:");
                    }

                    if (empty($cleanText)) {
                        if (!empty($capturedCards)) {
                            $cleanText = "Berikut data SPK yang ditemukan dari sistem bengkel:";
                        } else {
                            $cleanText = "Halo! Ada yang bisa saya bantu terkait pesanan, riwayat pengerjaan, atau analitik bengkel? 🔧";
                        }
                    }

                    return [
                        'success' => true,
                        'text' => $cleanText,
                        'cards' => array_values(collect($capturedCards)->unique('id')->take(5)->toArray()),
                        'timeline' => $capturedTimeline,
                        'cover_photo' => $capturedCoverPhoto,
                        'photos' => $capturedPhotos,
                        'is_truncated' => $isTruncated,
                    ];
                }

                // Process function calls
                $contents[] = $candidate['content'];

                foreach ($functionCalls as $call) {
                    $funcName = $call['name'];
                    $args = $call['args'] ?? [];
                    $funcResult = $this->dispatchToolCall($funcName, $args, $capturedCards, $capturedTimeline, $capturedCoverPhoto, $capturedPhotos);

                    $contents[] = [
                        'role' => 'user',
                        'parts' => [
                            [
                                'functionResponse' => [
                                    'name' => $funcName,
                                    'response' => $funcResult,
                                ]
                            ]
                        ]
                    ];
                }

                // Continue loop — next iteration will send tool results back to model
            }

            // If we exhausted all rounds, do a final call without tools to force text response
            $finalResponse = Http::withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    CURLOPT_TCP_NODELAY => true,
                ],
            ])->timeout(30)->post($endpoint, [
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]]
                ],
                'contents' => $contents,
                'generationConfig' => $generationConfig,
            ]);

            if ($finalResponse->successful()) {
                $finalData = $finalResponse->json();
                $finalCandidate = $finalData['candidates'][0] ?? null;
                $finalParts = $finalCandidate['content']['parts'] ?? [];
                $finalFinishReason = $finalCandidate['finishReason'] ?? 'STOP';
                $isTruncated = ($finalFinishReason === 'MAX_TOKENS');

                $finalText = '';
                foreach ($finalParts as $p) {
                    if (isset($p['text'])) {
                        $finalText .= $p['text'];
                    }
                }

                $cleanText = trim($finalText);
                if ($isTruncated) {
                    $cleanText = rtrim($cleanText, " \t\n\r\0\x0B([*>#-_:");
                }

                if (empty($cleanText)) {
                    $cleanText = !empty($capturedCards)
                        ? "Berikut data yang ditemukan dari sistem bengkel:"
                        : "Maaf, saya tidak berhasil memproses jawaban. Silakan coba lagi.";
                }

                return [
                    'success' => true,
                    'text' => $cleanText,
                    'cards' => array_values(collect($capturedCards)->unique('id')->take(5)->toArray()),
                    'timeline' => $capturedTimeline,
                    'cover_photo' => $capturedCoverPhoto,
                    'photos' => $capturedPhotos,
                    'is_truncated' => $isTruncated,
                ];
            }

            return [
                'success' => false,
                'text' => 'Gagal mendapatkan respons dari AI setelah beberapa percobaan.',
                'cards' => array_values(collect($capturedCards)->unique('id')->take(5)->toArray()),
                'timeline' => $capturedTimeline,
                'cover_photo' => $capturedCoverPhoto,
                'photos' => $capturedPhotos,
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'text' => $e->getMessage(),
                'cards' => [],
                'timeline' => null,
                'cover_photo' => null,
                'photos' => null,
            ];
        }
    }

    // ========================================
    // Tool Declarations
    // ========================================

    /**
     * Build all function declarations for Gemini tools
     */
    protected function buildToolDeclarations(): array
    {
        return [
            // Tool 1: Search Work Orders (existing)
            [
                'name' => 'search_work_orders',
                'description' => 'Mencari daftar SPK (Work Order) berdasarkan nomor SPK, nama pelanggan, nomor telepon, atau nomor invoice.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'keyword' => [
                            'type' => 'STRING',
                            'description' => 'Kata kunci pencarian (nomor SPK seperti SPK-2026-0018 atau 18, nama pelanggan, no telepon)',
                        ],
                        'show_cards' => [
                            'type' => 'BOOLEAN',
                            'description' => 'Set TRUE HANYA jika pengguna secara eksplisit meminta mencari/menampilkan daftar kartu SPK (misal: "cari SPK 18", "tampilkan pesanan Budi", "lihat daftar SPK"). Set FALSE jika pengguna menanyakan info spesifik (seperti no telepon, harga, status, posisi rak) atau jika data sedang diklarifikasi.',
                        ],
                    ],
                    'required' => ['keyword'],
                ],
            ],
            // Tool 2: Get Work Order Detail (existing)
            [
                'name' => 'get_work_order_detail',
                'description' => 'Mengambil rincian lengkap satu SPK: informasi pelanggan, sepatu, daftar layanan jasa lengkap (rincian spesifikasi pengerjaan, catatan teknis, biaya per jasa, teknisi, status, dan waktu pengerjaan), invoice/pembayaran, lokasi rak, serta teknisi.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'identifier' => [
                            'type' => 'STRING',
                            'description' => 'ID numerik SPK (misal 18) atau format nomor SPK (misal SPK-2026-0018)',
                        ],
                    ],
                    'required' => ['identifier'],
                ],
            ],
            // Tool 3: Get Work Order Timeline (existing)
            [
                'name' => 'get_work_order_timeline',
                'description' => 'Mengambil kronologi riwayat aktivitas teknisi, log audit, dan riwayat perpindahan status SPK (kapan masuk PREP, SORTIR, PRODUCTION, QC, SELESAI) dari work_order_logs. Gunakan untuk riwayat timeline dan audit event.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'identifier' => [
                            'type' => 'STRING',
                            'description' => 'ID numerik SPK (misal 18) atau format nomor SPK (misal SPK-2026-0018)',
                        ],
                    ],
                    'required' => ['identifier'],
                ],
            ],
            // Tool 4: SPK Overview Stats
            [
                'name' => 'get_spk_overview_stats',
                'description' => 'Mengambil statistik ringkasan SPK: jumlah total SPK, breakdown per status, jumlah overdue, fast-track, SPK baru hari ini, selesai hari ini, dan rata-rata hari pengerjaan. Gunakan untuk pertanyaan agregat/statistik.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'period' => [
                            'type' => 'STRING',
                            'description' => 'Periode waktu: "this_month", "last_month", "this_week", "today", atau "custom". Default: "this_month".',
                        ],
                        'start_date' => [
                            'type' => 'STRING',
                            'description' => 'Tanggal awal (format YYYY-MM-DD). Hanya dipakai jika period = "custom".',
                        ],
                        'end_date' => [
                            'type' => 'STRING',
                            'description' => 'Tanggal akhir (format YYYY-MM-DD). Hanya dipakai jika period = "custom".',
                        ],
                    ],
                ],
            ],
            // Tool 5: Financial Summary & KPI Finance (/admin/kpi)
            [
                'name' => 'get_financial_summary',
                'description' => 'Mengambil ringkasan keuangan dan KPI Finance resmi bengkel (selaras 100% dengan dashboard /admin/kpi tab KPI Finance): total nilai tagihan, kas masuk tervalidasi, sisa piutang aktif, rasio penagihan (collection rate), rincian status invoice, distribusi jenis pembayaran (DP awal, pelunasan, ongkir, OTO), realisasi omset valid, total diskon, serta rekap SPK batal & total dana refund. Bisa per-SPK (detail 1 order) atau aggregate seluruh workshop dalam periode tertentu.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'identifier' => [
                            'type' => 'STRING',
                            'description' => 'ID atau nomor SPK. Jika diisi, mengembalikan detail keuangan 1 SPK. Jika kosong, mengembalikan ringkasan KPI Finance aggregate resmi bengkel.',
                        ],
                        'period' => [
                            'type' => 'STRING',
                            'description' => 'Periode: "this_month", "last_month", "this_week", "today", atau "custom". Default: "this_month".',
                        ],
                        'start_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD, hanya jika period = "custom".'],
                        'end_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD, hanya jika period = "custom".'],
                    ],
                ],
            ],
            // Tool 6: Production Tracking
            [
                'name' => 'get_production_tracking',
                'description' => 'Mengambil progress detail sub-stasiun pengerjaan SPK (Persiapan: Cuci/Sol/Upper, Produksi: Sol/Upper/Cleaning, QC), riwayat perpindahan status (kapan masuk PREPARATION, SORTIR, PRODUCTION, QC, SELESAI), durasi di tiap status, kendala CX, dan deteksi bottleneck. Gunakan saat ditanya kapan masuk tahap tertentu atau berapa lama di suatu status.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'identifier' => [
                            'type' => 'STRING',
                            'description' => 'ID atau nomor SPK yang ingin dilacak progress produksinya.',
                        ],
                    ],
                    'required' => ['identifier'],
                ],
            ],
            // Tool 7: Technician Analytics
            [
                'name' => 'get_technician_analytics',
                'description' => 'Mengambil analitik performa teknisi: jumlah SPK ditangani, beban kerja aktif, rata-rata waktu pengerjaan, jumlah revisi. Bisa filter per teknisi atau lihat semua.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'technician_name' => [
                            'type' => 'STRING',
                            'description' => 'Nama teknisi untuk filter spesifik. Kosongkan untuk melihat semua teknisi.',
                        ],
                        'period' => [
                            'type' => 'STRING',
                            'description' => 'Periode: "this_month", "last_month", "this_week", "custom". Default: "this_month".',
                        ],
                        'start_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD.'],
                        'end_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD.'],
                    ],
                ],
            ],
            // Tool 8: Revision & Warranty Data
            [
                'name' => 'get_revision_warranty_data',
                'description' => 'Mengambil data revisi dan garansi: detail revisi per SPK, klaim garansi, kerugian (loss), responsible party. Bisa per-SPK atau aggregate.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'identifier' => [
                            'type' => 'STRING',
                            'description' => 'ID atau nomor SPK. Kosongkan untuk aggregate.',
                        ],
                        'period' => ['type' => 'STRING', 'description' => 'Periode: "this_month", "last_month", "custom".'],
                        'start_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD.'],
                        'end_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD.'],
                    ],
                ],
            ],
            // Tool 9: CX Issues & Complaints
            [
                'name' => 'get_cx_issues_data',
                'description' => 'Mengambil data kendala operasional/pelanggan (CX Issues) dan keluhan (Complaints): daftar seluruh SPK yang saat ini memiliki kendala OPEN/tertunda, riwayat kendala OPEN maupun RESOLVED (selesai) per-SPK, kategori kendala, opsi solusi, dan catatan penyelesaian. Gunakan saat ditanya SPK yang bermasalah/tertunda atau kendala pelanggan.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'identifier' => [
                            'type' => 'STRING',
                            'description' => 'ID atau nomor SPK. Kosongkan untuk aggregate.',
                        ],
                        'status_filter' => [
                            'type' => 'STRING',
                            'description' => 'Filter status: "OPEN", "RESOLVED", atau "ALL". Default: "ALL".',
                        ],
                        'period' => ['type' => 'STRING', 'description' => 'Periode waktu.'],
                        'start_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD.'],
                        'end_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD.'],
                    ],
                ],
            ],
            // Tool 10: Storage & Logistics (/admin/kpi - Tab Gudang & Operasional Rak)
            [
                'name' => 'get_storage_logistics',
                'description' => 'Mengambil data resmi KPI Gudang (selaras 100% dengan dashboard /admin/kpi tab KPI Gudang) dan operasional penyimpanan logistik: 5 metrik resmi (1. Sepatu Masuk Before, 2. SPK Print/OTW Workshop, 3. SPK Tertahan QC Reject, 4. After Masuk Selesai Reparasi, 5. Sepatu Keluar Pengambilan/Kirim Lunas), total sepatu di rak, barang overdue (>7 hari), daftar utilisasi rak, dan status serah terima. Bisa per-SPK atau aggregate seluruh gudang dalam periode tertentu.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'identifier' => [
                            'type' => 'STRING',
                            'description' => 'ID atau nomor SPK. Gunakan untuk mencari posisi rak & riwayat logistik 1 SPK tertentu.',
                        ],
                        'rack_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode rak spesifik (misal "B01", "A01", "R02"). Gunakan jika pengguna bertanya "di rak [kode] ada SPK/sepatu apa saja?".',
                        ],
                        'period' => [
                            'type' => 'STRING',
                            'description' => 'Periode waktu: "this_month", "last_month", "this_week", "today", atau "custom". Default: "this_month".',
                        ],
                        'start_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD, hanya jika period = "custom".'],
                        'end_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD, hanya jika period = "custom".'],
                        'filter' => [
                            'type' => 'STRING',
                            'description' => 'Filter: "overdue", "stored", "all". Default: "all".',
                        ],
                    ],
                ],
            ],
            // Tool 11: Workshop Production Intelligence (/admin/kpi - Tab Workshop, Live Floor, Overdue SLA & Technician Workload)
            [
                'name' => 'get_workshop_production_intelligence',
                'description' => 'Mengambil intelejen komprehensif produksi workshop: deteksi SPK overdue & mendekati deadline SLA, beban kerja real-time teknisi (siapa overload/terbanyak tugas), antrean stasiun live (Preparation, Sortir, Produksi, QC), dan KPI throughput workshop resmi (/admin/kpi - Tab Workshop). Gunakan tool ini jika pengguna bertanya tentang SPK telat di workshop, siapa teknisi paling sibuk/overload, antrean stasiun pengerjaan, atau performa KPI stasiun workshop.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'mode' => [
                            'type' => 'STRING',
                            'description' => 'Mode analisis: "all" (semua aspek), "bottlenecks" (SPK overdue & SLA warning), "technicians" (beban kerja teknisi), "stations" (antrean live per stasiun), "kpi_overview" (KPI throughput & durasi). Default: "all".',
                        ],
                        'station' => [
                            'type' => 'STRING',
                            'description' => 'Filter stasiun tertentu jika spesifik: "PREPARATION", "SORTIR", "PRODUCTION", "QC".',
                        ],
                        'technician_name' => [
                            'type' => 'STRING',
                            'description' => 'Nama teknisi jika ingin mengecek antrean/beban kerja teknisi tertentu.',
                        ],
                        'period' => [
                            'type' => 'STRING',
                            'description' => 'Periode waktu untuk KPI throughput: "this_month", "last_month", "this_week", "today", "custom". Default: "this_month".',
                        ],
                        'start_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD, hanya jika period = "custom".'],
                        'end_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD, hanya jika period = "custom".'],
                    ],
                ],
            ],
            // Tool 12: OTO Data
            [
                'name' => 'get_oto_data',
                'description' => 'Mengambil data OTO (On-The-Order / penawaran jasa tambahan): status penawaran, harga, diskon, progres pengerjaan OTO. Bisa per-SPK atau aggregate.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'identifier' => [
                            'type' => 'STRING',
                            'description' => 'ID atau nomor SPK. Kosongkan untuk aggregate.',
                        ],
                        'status_filter' => [
                            'type' => 'STRING',
                            'description' => 'Filter status OTO: "PENDING_CX", "APPROVED", "COMPLETED", "ALL". Default: "ALL".',
                        ],
                        'period' => ['type' => 'STRING', 'description' => 'Periode waktu.'],
                        'start_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD.'],
                        'end_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD.'],
                    ],
                ],
            ],
            // Tool 12: Work Order Photos (Before, After, Referensi, QC)
            [
                'name' => 'get_work_order_photos',
                'description' => 'Mengambil galeri foto dokumentasi pengerjaan SPK dari tabel work_order_photos: foto before (kondisi awal/gudang), foto after (selesai/finish), foto referensi penerimaan, dan proses produksi/QC. Gunakan tool ini jika pengguna menanyakan foto before, after, atau referensi sepatu.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'identifier' => [
                            'type' => 'STRING',
                            'description' => 'ID numerik SPK (misal 18) atau format nomor SPK (misal S-2608-14-0022-SW)',
                        ],
                        'category' => [
                            'type' => 'STRING',
                            'description' => 'Kategori foto yang dicari: "all", "before", "after", "reference", "production". Default: "all".',
                        ],
                    ],
                    'required' => ['identifier'],
                ],
            ],
            // Tool 13: System Navigation, Routes & Layouts Guide
            [
                'name' => 'get_feature_navigation',
                'description' => 'Mencari dan menjelaskan fitur sistem bengkel, letak menu pada navigasi sidebar, rute URL (/path), hak akses/role (permission), dan panduan langkah penggunaan fitur. Gunakan tool ini jika pengguna bertanya letak menu, bagaimana cara ke halaman tertentu, fungsi menu pada sidebar, atau cara menggunakan fitur sistem.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'query' => [
                            'type' => 'STRING',
                            'description' => 'Nama fitur, menu sidebar, rute, atau kata kunci (misal: "laporan performa", "penerimaan", "kasir", "overdue sla", "rak", "promo").',
                        ],
                        'division' => [
                            'type' => 'STRING',
                            'description' => 'Filter spesifik divisi jika ada: "cs", "warehouse", "workshop", "finance", "cx", "admin", "public", "general". Kosongkan jika mencari global.',
                        ],
                    ],
                ],
            ],
            // Tool 14: Manifest & Shipping Logistics Intelligence
            [
                'name' => 'get_manifest_shipping_intelligence',
                'description' => 'Mengambil intelejen logistik manifest dan rantai pasok pengiriman bengkel: manifest inbound (Gudang ke Workshop), surat jalan internal antar divisi (Sortir -> Produksi -> QC), deteksi manifest/SPK macet di jalan (stuck in transit > 24 jam), audit outbound kurir ekspedisi & kelengkapan resi pengiriman, serta audit finansial selisih biaya ongkir (subsidi ongkir workshop vs yang dibayar pelanggan).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'mode' => [
                            'type' => 'STRING',
                            'description' => 'Mode audit: "all" (semua pilar logistik), "inbound_manifest" (manifest gudang ke workshop & deteksi stuck in transit), "outbound_shipping" (audit resi & pesanan selesai siap kirim), "shipping_cost_audit" (audit finansial selisih ongkir & subsidi bengkel), "internal_transfer" (surat jalan serah terima antar divisi & inspeksi kondisi fisik). Default: "all".',
                        ],
                        'identifier' => [
                            'type' => 'STRING',
                            'description' => 'Nomor manifest (misal "MNF-OUT-...", "MFST-..."), nomor surat jalan (misal "SJ-SP-..."), atau nomor SPK tertentu. Kosongkan untuk audit agregat.',
                        ],
                        'period' => [
                            'type' => 'STRING',
                            'description' => 'Periode: "this_month", "last_month", "this_week", "today", "custom". Default: "this_month".',
                        ],
                        'start_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD, hanya jika custom.'],
                        'end_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD, hanya jika custom.'],
                    ],
                ],
            ],
        ];
    }

    // ========================================
    // Tool Dispatcher
    // ========================================

    /**
     * Dispatch a function call to its executor and capture side-effects (cards, timeline, photos)
     */
    public function dispatchToolCall(string $funcName, array $args, array &$capturedCards, ?array &$capturedTimeline, ?array &$capturedCoverPhoto = null, ?array &$capturedPhotos = null): array
    {
        $funcResult = [];

        switch ($funcName) {
            case 'search_work_orders':
                $funcResult = $this->executeSearchWorkOrders($args['keyword'] ?? '');
                $showCards = !isset($args['show_cards']) || !empty($args['show_cards']) || count($funcResult['results'] ?? []) <= 3;
                // Capture cards for search results
                if ($showCards && !empty($funcResult['results'])) {
                    foreach ($funcResult['results'] as $item) {
                        if (isset($item['card'])) {
                            $capturedCards[] = $item['card'];
                        }
                    }
                }
                break;

            case 'get_work_order_detail':
                $funcResult = $this->executeGetWorkOrderDetail($args['identifier'] ?? '');
                if (!empty($funcResult['cover_photo'])) {
                    $capturedCoverPhoto = $funcResult['cover_photo'];
                }
                break;

            case 'get_work_order_photos':
                $funcResult = $this->executeGetWorkOrderPhotos($args['identifier'] ?? '', $args['category'] ?? 'all');
                if (!empty($funcResult['photos'])) {
                    $capturedPhotos = [
                        'spk_number' => $funcResult['spk_number'] ?? '',
                        'customer_name' => $funcResult['customer_name'] ?? '',
                        'shoe' => $funcResult['shoe'] ?? '',
                        'total_photos' => $funcResult['total_photos'] ?? count($funcResult['photos']),
                        'items' => $funcResult['photos'],
                    ];
                }
                break;

            case 'get_work_order_timeline':
                $funcResult = $this->executeGetWorkOrderTimeline($args['identifier'] ?? '');
                if (!empty($funcResult['timeline'])) {
                    $capturedTimeline = $funcResult['timeline'];
                }
                break;

            case 'get_spk_overview_stats':
                $funcResult = $this->executeGetSpkOverviewStats($args);
                break;

            case 'get_financial_summary':
                $funcResult = $this->executeGetFinancialSummary($args);
                break;

            case 'get_production_tracking':
                $funcResult = $this->executeGetProductionTracking($args['identifier'] ?? '');
                break;

            case 'get_technician_analytics':
                $funcResult = $this->executeGetTechnicianAnalytics($args);
                break;

            case 'get_revision_warranty_data':
                $funcResult = $this->executeGetRevisionWarrantyData($args);
                break;

            case 'get_cx_issues_data':
                $funcResult = $this->executeGetCxIssuesData($args);
                break;

            case 'get_storage_logistics':
                $funcResult = $this->executeGetStorageLogistics($args);
                break;

            case 'get_workshop_production_intelligence':
                $funcResult = $this->executeGetWorkshopProductionIntelligence($args);
                break;

            case 'get_oto_data':
                $funcResult = $this->executeGetOtoData($args);
                break;

            case 'get_feature_navigation':
                $funcResult = SystemNavigationMap::search(
                    $args['query'] ?? null,
                    $args['division'] ?? null
                );
                break;

            case 'get_manifest_shipping_intelligence':
                $funcResult = $this->executeGetManifestShippingIntelligence($args);
                break;

            default:
                $funcResult = ['status' => 'error', 'message' => "Unknown tool: {$funcName}"];
                break;
        }

        return $funcResult;
    }

    // ========================================
    // Helper: Period Filter
    // ========================================

    /**
     * Resolve period parameter to Carbon date range
     */
    protected function resolvePeriodFilter(?string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $period = $period ?: 'this_month';

        switch ($period) {
            case 'today':
                return [Carbon::today(), Carbon::today()->endOfDay()];
            case 'this_week':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
            case 'last_month':
                return [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()];
            case 'custom':
                $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
                $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();
                return [$start, $end];
            case 'this_month':
            default:
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfDay()];
        }
    }

    /**
     * Find a WorkOrder by identifier (ID or SPK number)
     */
    protected function findOrderByIdentifier(string $identifier): ?WorkOrder
    {
        $cleanId = trim($identifier);

        // 1. Exact match by numeric ID
        if (is_numeric($cleanId)) {
            $order = WorkOrder::find((int) $cleanId);
            if ($order) {
                return $order;
            }
        }

        // 2. Exact match by SPK number
        $order = WorkOrder::where('spk_number', $cleanId)->first();
        if ($order) {
            return $order;
        }

        // 3. Substring match by SPK number (fallback)
        return WorkOrder::where('spk_number', 'like', "%{$cleanId}%")->first();
    }

    // ========================================
    // Tool Executors — Existing Tools (1-3)
    // ========================================

    /**
     * Tool 1: Search Work Orders
     */
    protected function executeSearchWorkOrders(string $keyword): array
    {
        $keyword = trim($keyword);
        if (empty($keyword)) {
            return ['status' => 'empty', 'message' => 'Kata kunci kosong', 'results' => []];
        }

        // Clean common Indonesian fillers, prefixes, and punctuation
        $cleanKeyword = preg_replace('/\b(spk|atas|nama|pesanan|order|no|nomor)\b/i', ' ', $keyword);
        $rawTokens = preg_split('/[\s,]+/', $cleanKeyword);
        $tokens = [];
        foreach ($rawTokens as $t) {
            $trimmed = trim($t, "- \t\n\r");
            if (strlen($trimmed) >= 2) {
                $tokens[] = $trimmed;
            }
        }

        $orders = WorkOrder::query()
            ->where(function ($q) use ($keyword, $tokens) {
                // 1. Direct whole-keyword match
                $q->where('spk_number', 'like', "%{$keyword}%")
                  ->orWhere('id', $keyword)
                  ->orWhere('customer_name', 'like', "%{$keyword}%")
                  ->orWhere('shoe_brand', 'like', "%{$keyword}%")
                  ->orWhereHas('customer', function ($sub) use ($keyword) {
                      $sub->where('name', 'like', "%{$keyword}%")
                          ->orWhere('phone', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('invoice', function ($sub) use ($keyword) {
                      $sub->where('invoice_number', 'like', "%{$keyword}%");
                  });

                // 2. Multi-token match: all tokens must match at least one attribute
                if (count($tokens) > 1) {
                    $q->orWhere(function ($subQ) use ($tokens) {
                        foreach ($tokens as $token) {
                            $subQ->where(function ($fieldQ) use ($token) {
                                $fieldQ->where('spk_number', 'like', "%{$token}%")
                                       ->orWhere('customer_name', 'like', "%{$token}%")
                                       ->orWhere('shoe_brand', 'like', "%{$token}%")
                                       ->orWhereHas('customer', function ($cQ) use ($token) {
                                           $cQ->where('name', 'like', "%{$token}%")
                                              ->orWhere('phone', 'like', "%{$token}%");
                                       })
                                       ->orWhereHas('invoice', function ($iQ) use ($token) {
                                           $iQ->where('invoice_number', 'like', "%{$token}%");
                                       });
                            });
                        }
                    });
                }
            })
            ->with(['customer', 'invoice', 'storageAssignments.rack', 'workOrderServices', 'photos'])
            ->latest()
            ->take(5)
            ->get();

        $results = [];
        foreach ($orders as $order) {
            $results[] = [
                'id' => $order->id,
                'spk_number' => $order->spk_number,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer?->phone ?? $order->customer_phone ?? '-',
                'shoe_brand' => $order->shoe_brand,
                'shoe_color' => $order->shoe_color,
                'status' => $order->status ? str_replace('_', ' ', $order->status->value) : '-',
                'current_location' => $order->current_location ?? 'Belum Ditentukan',
                'rack' => $order->storageAssignments->first()?->rack?->name ?? 'Belum ada rak',
                'estimation_date' => $order->estimation_date?->format('d M Y') ?? '-',
                'total_services' => $order->workOrderServices->count(),
                'card' => $this->buildCardData($order),
            ];
        }

        return [
            'found_count' => count($results),
            'results' => $results,
        ];
    }

    /**
     * Tool 2: Get Work Order Detail
     */
    protected function executeGetWorkOrderDetail(string $identifier): array
    {
        $order = $this->findOrderByIdentifier($identifier);

        if (!$order) {
            return ['status' => 'not_found', 'message' => "Order dengan ID atau SPK '{$identifier}' tidak ditemukan."];
        }

        $order->load(['customer', 'invoice', 'storageAssignments.rack', 'workOrderServices.service', 'workOrderServices.technician', 'photos', 'prodSolBy', 'prodUpperBy', 'prodCleaningBy']);

        $totalServicesCost = 0;
        $servicesList = $order->workOrderServices->map(function ($s) use (&$totalServicesCost) {
            $cost = (float) $s->cost;
            $totalServicesCost += $cost;

            $detailsList = [];
            if (!empty($s->service_details)) {
                if (is_array($s->service_details)) {
                    if (isset($s->service_details['manual_detail']) && is_array($s->service_details['manual_detail'])) {
                        foreach ($s->service_details['manual_detail'] as $item) {
                            if (!empty(trim((string)$item))) {
                                $detailsList[] = trim((string)$item);
                            }
                        }
                    } else {
                        foreach ($s->service_details as $k => $v) {
                            if (is_string($v) && !empty(trim($v))) {
                                $detailsList[] = is_numeric($k) ? trim($v) : "{$k}: " . trim($v);
                            } elseif (is_array($v)) {
                                $detailsList[] = implode(', ', array_filter($v));
                            }
                        }
                    }
                } elseif (is_string($s->service_details)) {
                    $detailsList[] = trim($s->service_details);
                }
            }

            return [
                'name' => $s->custom_service_name ?? $s->service?->name ?? 'Layanan',
                'category' => $s->category_name ?? $s->service?->category ?? '-',
                'cost' => $cost,
                'cost_formatted' => 'Rp ' . number_format($cost, 0, ',', '.'),
                'status' => $s->status ?? 'pending',
                'technician' => $s->technician?->name ?? 'Belum Ditugaskan',
                'specifications' => !empty($detailsList) ? implode('; ', $detailsList) : null,
                'notes' => !empty(trim((string)$s->notes)) ? trim((string)$s->notes) : null,
                'started_at' => $s->started_at?->format('d M Y, H:i') ?? null,
                'completed_at' => $s->completed_at?->format('d M Y, H:i') ?? null,
                'duration_minutes' => $s->actual_duration_minutes ?? null,
            ];
        })->toArray();

        // Status transitions (compact — last 8 status changes from logs)
        $statusTransitions = $order->logs()
            ->where('action', 'STATUS_CHANGE')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($l) => [
                'from_to' => $l->description,
                'step' => $l->step,
                'at' => $l->created_at?->format('d M Y, H:i') ?? '-',
                'at_relative' => $l->created_at?->diffForHumans() ?? '',
            ])->toArray();

        return [
            'status' => 'success',
            'order' => [
                'id' => $order->id,
                'spk_number' => $order->spk_number,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone ?? $order->customer?->phone ?? '-',
                'shoe_brand' => $order->shoe_brand,
                'shoe_type' => $order->shoe_type,
                'shoe_color' => $order->shoe_color,
                'shoe_size' => $order->shoe_size,
                'status' => $order->status ? str_replace('_', ' ', $order->status->value) : '-',
                'current_location' => $order->current_location ?? 'Belum Ditentukan',
                'rack_name' => $order->storageAssignments()->where('status', 'stored')->latest()->first()?->rack_code 
                    ?? $order->storageAssignments()->latest()->first()?->rack_code 
                    ?? ($order->current_location && !in_array($order->current_location, ['Unknown', 'Gudang Penerimaan']) ? $order->current_location : 'Belum Masuk Rak'),
                'created_at' => $order->created_at?->format('d M Y, H:i') ?? '-',
                'estimation_date' => $order->estimation_date?->format('d M Y') ?? 'Belum diatur',
                'services' => $servicesList,
                'total_services_cost' => $totalServicesCost,
                'total_services_cost_formatted' => 'Rp ' . number_format($totalServicesCost, 0, ',', '.'),
                'invoice' => [
                    'number' => $order->invoice?->invoice_number ?? '-',
                    'total' => (float) ($order->invoice?->total_amount ?? 0),
                    'paid' => (float) ($order->invoice?->paid_amount ?? 0),
                    'status' => $order->invoice?->status ?? 'Belum Ada Invoice',
                ],
                'technicians' => [
                    'sol' => $order->prodSolBy?->name ?? '-',
                    'upper' => $order->prodUpperBy?->name ?? '-',
                    'cleaning' => $order->prodCleaningBy?->name ?? '-',
                ],
                'status_transitions' => array_slice($statusTransitions, -8),
            ],
            'cover_photo' => $order->spk_cover_photo_url ? [
                'url' => $order->spk_cover_photo_url,
                'spk_number' => $order->spk_number,
                'shoe' => trim("{$order->shoe_brand} {$order->shoe_color}"),
                'customer_name' => $order->customer_name,
            ] : null,
            'card' => $this->buildCardData($order),
        ];
    }

    /**
     * Tool 12: Get Work Order Photos (Before, After, Referensi, QC)
     */
    protected function executeGetWorkOrderPhotos(string $identifier, string $category = 'all'): array
    {
        $order = $this->findOrderByIdentifier($identifier);

        if (!$order) {
            return ['status' => 'not_found', 'message' => "Order dengan ID atau SPK '{$identifier}' tidak ditemukan."];
        }

        $photos = $order->photos()->get();

        if ($photos->isEmpty()) {
            return [
                'status' => 'empty',
                'spk_number' => $order->spk_number,
                'customer_name' => $order->customer_name,
                'shoe' => trim("{$order->shoe_brand} {$order->shoe_color}"),
                'message' => "Belum ada dokumentasi foto yang diunggah untuk SPK {$order->spk_number}.",
                'photos' => [],
            ];
        }

        $stepLabels = [
            'RECEPTION' => 'Referensi',
            'WAREHOUSE_BEFORE' => 'Before',
            'BEFORE' => 'Before',
            'ASSESSMENT' => 'Produksi',
            'WASHING' => 'Produksi',
            'PREPARATION' => 'Produksi',
            'PRODUCTION' => 'Produksi',
            'QC' => 'QC',
            'FINISH' => 'After',
            'CX_FOLLOWUP' => 'Follow-up CX',
        ];

        $list = [];
        foreach ($photos as $photo) {
            $group = $stepLabels[$photo->step] ?? 'Lainnya';

            // Filter if specific category requested
            if ($category !== 'all') {
                $targetCat = strtolower($category);
                if ($targetCat === 'before' && !in_array($photo->step, ['WAREHOUSE_BEFORE', 'BEFORE'])) {
                    continue;
                }
                if ($targetCat === 'after' && !in_array($photo->step, ['FINISH'])) {
                    continue;
                }
                if ($targetCat === 'reference' && !in_array($photo->step, ['RECEPTION'])) {
                    continue;
                }
            }

            $url = $photo->photo_url;
            if ($url) {
                $list[] = [
                    'id' => $photo->id,
                    'step' => $photo->step,
                    'group' => $group,
                    'url' => $url,
                    'caption' => $photo->caption ?? '',
                    'is_cover' => (bool) $photo->is_spk_cover,
                    'created_at' => $photo->created_at?->format('d M Y H:i') ?? '-',
                ];
            }
        }

        return [
            'status' => 'success',
            'spk_number' => $order->spk_number,
            'customer_name' => $order->customer_name,
            'shoe' => trim("{$order->shoe_brand} {$order->shoe_color}"),
            'total_photos' => count($list),
            'photos' => $list,
        ];
    }

    /**
     * Tool 3: Get Work Order Timeline (work_order_logs)
     */
    protected function executeGetWorkOrderTimeline(string $identifier): array
    {
        $order = $this->findOrderByIdentifier($identifier);

        if (!$order) {
            return ['status' => 'not_found', 'message' => "Order dengan ID atau SPK '{$identifier}' tidak ditemukan."];
        }

        $order->load(['storageAssignments.rack']);

        $logs = $order->logs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        $formattedLogs = $logs->map(fn($l) => [
            'id' => $l->id,
            'step' => $l->step,
            'action' => $l->action,
            'description' => $l->description,
            'user' => $l->user?->name ?? 'Sistem',
            'time' => $l->created_at?->format('d M Y, H:i') ?? '-',
            'time_diff' => $l->created_at?->diffForHumans() ?? '',
        ])->toArray();

        // Full chronological status changes
        $statusTransitions = $order->logs()
            ->where('action', 'STATUS_CHANGE')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($l) => [
                'step' => $l->step,
                'description' => $l->description,
                'at' => $l->created_at?->format('d M Y, H:i') ?? '-',
                'timestamp' => $l->created_at?->toIso8601String(),
            ])->toArray();

        return [
            'status' => 'success',
            'spk_number' => $order->spk_number,
            'customer_name' => $order->customer_name,
            'shoe' => trim("{$order->shoe_brand} {$order->shoe_model} {$order->shoe_color}"),
            'current_status' => $order->status,
            'current_location' => $order->current_location ?? '-',
            'rack_location' => $order->rack_location ?? '-',
            'status_transitions' => $statusTransitions,
            'total_events' => count($formattedLogs),
            'recent_events' => array_slice($formattedLogs, 0, 10),
            'card' => $this->buildCardData($order),
            'timeline' => [
                'spk_number' => $order->spk_number,
                'customer_name' => $order->customer_name,
                'shoe' => trim("{$order->shoe_brand} {$order->shoe_model} {$order->shoe_color}"),
                'status' => $order->status,
                'total_events' => count($formattedLogs),
                'events' => array_slice($formattedLogs, 0, 15),
            ],
        ];
    }

    // ========================================
    // Tool Executors — New Tools (4-11)
    // ========================================

    /**
     * Tool 4: SPK Overview Stats
     */
    protected function executeGetSpkOverviewStats(array $args): array
    {
        [$start, $end] = $this->resolvePeriodFilter(
            $args['period'] ?? null,
            $args['start_date'] ?? null,
            $args['end_date'] ?? null
        );

        $query = WorkOrder::whereBetween('created_at', [$start, $end]);

        $totalSpk = (clone $query)->count();

        // Breakdown by status
        $byStatus = (clone $query)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Overdue: estimation_date < now AND status not in terminal states
        $terminalStatuses = ['SELESAI', 'DIANTAR', 'HISTORY', 'BATAL', 'DONASI'];
        $overdueCount = WorkOrder::where('estimation_date', '<', Carbon::now())
            ->whereNotIn('status', $terminalStatuses)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $fastTrackCount = (clone $query)->where('fast_track_status', 'yes')->count();

        $newToday = WorkOrder::whereDate('created_at', Carbon::today())->count();
        $completedToday = WorkOrder::whereDate('finished_date', Carbon::today())->count();

        // Average days to complete (for finished orders in period)
        $avgDays = WorkOrder::whereBetween('created_at', [$start, $end])
            ->whereNotNull('finished_date')
            ->selectRaw('AVG(DATEDIFF(finished_date, entry_date)) as avg_days')
            ->value('avg_days');

        return [
            'status' => 'success',
            'period' => ($args['period'] ?? 'this_month'),
            'period_range' => $start->format('d M Y') . ' - ' . $end->format('d M Y'),
            'total_spk' => $totalSpk,
            'by_status' => $byStatus,
            'overdue_count' => $overdueCount,
            'fast_track_count' => $fastTrackCount,
            'new_today' => $newToday,
            'completed_today' => $completedToday,
            'average_days_to_complete' => round((float) $avgDays, 1),
        ];
    }

    /**
     * Tool 5: Financial Summary
     */
    protected function executeGetFinancialSummary(array $args): array
    {
        $identifier = $args['identifier'] ?? null;

        // Per-SPK detail
        if (!empty($identifier)) {
            $order = $this->findOrderByIdentifier($identifier);
            if (!$order) {
                return ['status' => 'not_found', 'message' => "SPK '{$identifier}' tidak ditemukan."];
            }

            $order->load(['payments.pic', 'invoice']);

            $payments = $order->payments->map(fn($p) => [
                'amount' => (float) $p->amount_total,
                'method' => $p->payment_method ?? '-',
                'paid_at' => $p->paid_at?->format('d M Y, H:i') ?? '-',
                'is_verified' => (bool) $p->is_verified,
                'pic_name' => $p->pic?->name ?? '-',
                'type' => $p->type ?? '-',
            ])->toArray();

            return [
                'status' => 'success',
                'mode' => 'per_spk',
                'spk_number' => $order->spk_number,
                'customer_name' => $order->customer_name,
                'total_tagihan' => (float) ($order->total_transaksi ?? 0),
                'total_service_price' => (float) ($order->total_service_price ?? 0),
                'shipping_cost' => (float) ($order->shipping_cost ?? 0),
                'discount' => (float) ($order->discount ?? 0),
                'total_paid' => (float) ($order->total_paid ?? 0),
                'sisa_tagihan' => (float) ($order->sisa_tagihan ?? 0),
                'status_pembayaran' => $order->status_pembayaran ?? 'Belum Bayar',
                'payment_method' => $order->payment_method ?? '-',
                'payments' => $payments,
                'invoice' => [
                    'number' => $order->invoice?->invoice_number ?? '-',
                    'total' => (float) ($order->invoice?->total_amount ?? 0),
                    'paid' => (float) ($order->invoice?->paid_amount ?? 0),
                    'status' => $order->invoice?->status ?? '-',
                ],
                'card' => $this->buildCardData($order),
            ];
        }

        // Aggregate mode: Terintegrasi 100% dengan KpiService::getFinanceKpi (/admin/kpi)
        [$start, $end] = $this->resolvePeriodFilter(
            $args['period'] ?? null,
            $args['start_date'] ?? null,
            $args['end_date'] ?? null
        );

        /** @var \App\Services\KpiService $kpiService */
        $kpiService = app(\App\Services\KpiService::class);
        $financeKpi = $kpiService->getFinanceKpi($start, $end);

        // Transaksi Batal & Refund di periode tersebut (mengikuti audit trail updated_at status BATAL)
        $cancelledQuery = WorkOrder::where('status', \App\Enums\WorkOrderStatus::BATAL->value)
            ->whereBetween('updated_at', [$start, $end]);

        $totalCancelled = (clone $cancelledQuery)->count();
        $totalRefund = (float) (clone $cancelledQuery)->sum('refund_amount');

        // 1. Audit Kritis Pengiriman: SPK Selesai / Diantar tapi Belum Lunas (Risiko Sepatu Keluar Tanpa Pelunasan)
        $deliveryRiskQuery = WorkOrder::whereIn('status', ['SELESAI', 'DIANTAR'])
            ->where('sisa_tagihan', '>', 0)
            ->where('status_pembayaran', '!=', 'Lunas');

        $totalDeliveryRiskCount = (clone $deliveryRiskQuery)->count();
        $totalDeliveryRiskAmount = (float) (clone $deliveryRiskQuery)->sum('sisa_tagihan');
        $deliveryRiskList = (clone $deliveryRiskQuery)
            ->orderByDesc('sisa_tagihan')
            ->take(5)
            ->get(['id', 'spk_number', 'customer_name', 'status', 'status_pembayaran', 'sisa_tagihan', 'finished_date'])
            ->map(fn($o) => [
                'spk_number' => $o->spk_number,
                'customer_name' => $o->customer_name,
                'status_spk' => $o->status,
                'status_pembayaran' => $o->status_pembayaran,
                'sisa_piutang' => (float) $o->sisa_tagihan,
                'finished_date' => $o->finished_date ? Carbon::parse($o->finished_date)->format('d M Y') : '-',
            ])->toArray();

        // 2. Audit Verifikasi Kas: Pembayaran Masuk yang Belum Diverifikasi Finance
        $unverifiedQuery = \App\Models\InvoicePayment::where('verified', false)
            ->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()]);

        $unverifiedCount = (clone $unverifiedQuery)->count();
        $unverifiedAmount = (float) (clone $unverifiedQuery)->sum('amount');
        $unverifiedList = (clone $unverifiedQuery)
            ->with(['invoice.workOrder'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($p) => [
                'payment_number' => $p->payment_number,
                'spk_number' => $p->invoice?->workOrder?->spk_number ?? '-',
                'customer_name' => $p->invoice?->workOrder?->customer_name ?? '-',
                'method' => $p->payment_method ?? '-',
                'amount' => (float) $p->amount,
                'payment_date' => $p->payment_date ? Carbon::parse($p->payment_date)->format('d M Y') : '-',
            ])->toArray();

        // 3. Analisis Kebocoran Biaya & Total Kerugian
        $lostRevenueBatal = (float) WorkOrder::where('status', \App\Enums\WorkOrderStatus::BATAL->value)
            ->whereBetween('updated_at', [$start, $end])
            ->sum('total_transaksi');

        $revisionLoss = (float) \App\Models\WorkOrderRevision::whereBetween('created_at', [$start, $end])
            ->sum('loss_amount');

        $warrantyLoss = (float) \App\Models\WorkOrderWarranty::whereBetween('created_at', [$start, $end])
            ->sum('loss_amount');

        $totalCostLeakage = $totalRefund + $revisionLoss + $warrantyLoss;

        // 4. Analisis Kesehatan Collection Rate
        $collRate = (float) ($financeKpi['collection_rate'] ?? 0);
        $collAnalysis = "";
        if ($collRate > 100) {
            $collAnalysis = "Sangat Kuat ({$collRate}%). Penerimaan kas riil melampaui tagihan periode ini karena adanya penagihan/pelunasan piutang aktif dari invoice bulan-bulan lampau yang berhasil dicairkan masuk kas.";
        } elseif ($collRate >= 80) {
            $collAnalysis = "Sehat & Efektif ({$collRate}%). Arus kas masuk lancar dan mayoritas tagihan berhasil ditagihkan tepat waktu.";
        } elseif ($collRate >= 50) {
            $collAnalysis = "Cukup ({$collRate}%). Terdapat tagihan yang belum tertagih, perlu percepatan follow-up penagihan sebelum sepatu selesai pengerjaan.";
        } else {
            $collAnalysis = "Perlu Perhatian Kritis ({$collRate}%). Rasio penagihan rendah, mayoritas invoice masih tertahan dalam status Belum Bayar atau DP.";
        }

        // Top SPK dengan Piutang Aktif Tertinggi di periode tersebut
        $topUnpaid = WorkOrder::whereBetween('created_at', [$start, $end])
            ->where('sisa_tagihan', '>', 0)
            ->where('status', '!=', \App\Enums\WorkOrderStatus::BATAL->value)
            ->orderByDesc('sisa_tagihan')
            ->take(5)
            ->get(['id', 'spk_number', 'customer_name', 'sisa_tagihan'])
            ->map(fn($o) => [
                'spk_number' => $o->spk_number,
                'customer_name' => $o->customer_name,
                'sisa' => (float) $o->sisa_tagihan,
            ])->toArray();

        return [
            'status' => 'success',
            'mode' => 'aggregate_kpi_finance',
            'period' => ($args['period'] ?? 'this_month'),
            'period_range' => $start->format('d M Y') . ' - ' . $end->format('d M Y'),

            // Metrik Utama KPI Finance (Sesuai /admin/kpi tab KPI Finance)
            'total_nilai_tagihan' => (float) ($financeKpi['total_invoiced'] ?? 0),
            'kas_masuk_tervalidasi' => (float) ($financeKpi['cash_received'] ?? 0),
            'sisa_piutang_aktif' => (float) ($financeKpi['active_receivables'] ?? 0),
            'rasio_penagihan_persen' => (float) ($financeKpi['collection_rate'] ?? 0),
            'analisis_collection_rate' => $collAnalysis,
            'realisasi_omset_valid' => (float) ($financeKpi['revenue_realization'] ?? 0),
            'total_diskon' => (float) ($financeKpi['total_discount'] ?? 0),

            // Status Distribusi Invoice
            'status_invoice' => [
                'belum_bayar' => [
                    'transaksi' => (int) ($financeKpi['status_distribution']['belum_bayar']['count'] ?? 0),
                    'total_rupiah' => (float) ($financeKpi['status_distribution']['belum_bayar']['total'] ?? 0),
                ],
                'dp_cicil' => [
                    'transaksi' => (int) ($financeKpi['status_distribution']['dp_cicil']['count'] ?? 0),
                    'total_rupiah' => (float) ($financeKpi['status_distribution']['dp_cicil']['total'] ?? 0),
                ],
                'lunas' => [
                    'transaksi' => (int) ($financeKpi['status_distribution']['lunas']['count'] ?? 0),
                    'total_rupiah' => (float) ($financeKpi['status_distribution']['lunas']['total'] ?? 0),
                ],
            ],

            // Distribusi Jenis Pembayaran Kas
            'distribusi_pembayaran' => [
                'dp_awal' => [
                    'transaksi' => (int) ($financeKpi['payment_type_distribution']['dp_awal']['count'] ?? 0),
                    'total_rupiah' => (float) ($financeKpi['payment_type_distribution']['dp_awal']['total'] ?? 0),
                ],
                'pelunasan' => [
                    'transaksi' => (int) ($financeKpi['payment_type_distribution']['pelunasan']['count'] ?? 0),
                    'total_rupiah' => (float) ($financeKpi['payment_type_distribution']['pelunasan']['total'] ?? 0),
                ],
                'tambah_jasa' => [
                    'transaksi' => (int) ($financeKpi['payment_type_distribution']['tambah_jasa']['count'] ?? 0),
                    'total_rupiah' => (float) ($financeKpi['payment_type_distribution']['tambah_jasa']['total'] ?? 0),
                ],
                'lunas_awal' => [
                    'transaksi' => (int) ($financeKpi['payment_type_distribution']['lunas_awal']['count'] ?? 0),
                    'total_rupiah' => (float) ($financeKpi['payment_type_distribution']['lunas_awal']['total'] ?? 0),
                ],
                'ongkir' => [
                    'transaksi' => (int) ($financeKpi['payment_type_distribution']['ongkir']['count'] ?? 0),
                    'total_rupiah' => (float) ($financeKpi['payment_type_distribution']['ongkir']['total'] ?? 0),
                ],
                'oto' => [
                    'transaksi' => (int) ($financeKpi['payment_type_distribution']['oto']['count'] ?? 0),
                    'total_rupiah' => (float) ($financeKpi['payment_type_distribution']['oto']['total'] ?? 0),
                ],
            ],

            // Pembatalan SPK & Pengembalian Dana (Refund)
            'transaksi_batal_refund' => [
                'total_spk_batal' => $totalCancelled,
                'total_dana_refund' => $totalRefund,
            ],

            // Audit Kritis 1: Risiko Pengiriman (Sepatu Selesai/Diantar tapi Belum Lunas)
            'audit_risiko_pengiriman' => [
                'total_spk_berisiko' => $totalDeliveryRiskCount,
                'total_nominal_piutang' => $totalDeliveryRiskAmount,
                'daftar_spk' => $deliveryRiskList,
                'mitigasi' => 'Instruksikan tim CS dan Gudang untuk menahan (HOLD) serah terima/ekspedisi sepatu hingga pelunasan terkonfirmasi oleh Finance.',
            ],

            // Audit Kritis 2: Pembayaran Menggantung Belum Diverifikasi
            'audit_verifikasi_kas' => [
                'total_pembayaran_menggantung' => $unverifiedCount,
                'total_nominal_menggantung' => $unverifiedAmount,
                'daftar_pembayaran' => $unverifiedList,
            ],

            // Audit Kritis 3: Kebocoran Biaya & Kerugian Total Workshop
            'analisis_kebocoran_biaya' => [
                'omset_hilang_batal' => $lostRevenueBatal,
                'kas_keluar_refund' => $totalRefund,
                'biaya_kerugian_revisi' => $revisionLoss,
                'biaya_kerugian_garansi' => $warrantyLoss,
                'total_beban_kerugian' => $totalCostLeakage,
            ],

            // Top Piutang Berjalan
            'top_unpaid_spks' => $topUnpaid,
        ];
    }

    /**
     * Tool 6: Production Tracking
     */
    protected function executeGetProductionTracking(string $identifier): array
    {
        $order = $this->findOrderByIdentifier($identifier);
        if (!$order) {
            return ['status' => 'not_found', 'message' => "SPK '{$identifier}' tidak ditemukan."];
        }

        $order->load(['prepWashingBy', 'prepSolBy', 'prepUpperBy', 'prodSolBy', 'prodUpperBy', 'prodCleaningBy', 'qcJahitBy', 'qcCleanupBy', 'qcFinalBy']);

        $formatStation = function (string $label, ?string $by, $startedAt, $completedAt, bool $isUnneeded) {
            $duration = null;
            if ($startedAt && $completedAt) {
                $duration = Carbon::parse($startedAt)->diffInMinutes(Carbon::parse($completedAt));
            }
            return [
                'label' => $label,
                'technician' => $by ?? '-',
                'started_at' => $startedAt ? Carbon::parse($startedAt)->format('d M Y, H:i') : null,
                'completed_at' => $completedAt ? Carbon::parse($completedAt)->format('d M Y, H:i') : null,
                'duration_minutes' => $duration,
                'is_unneeded' => $isUnneeded,
                'status' => $isUnneeded ? 'skipped' : ($completedAt ? 'completed' : ($startedAt ? 'in_progress' : 'pending')),
            ];
        };

        $preparation = [
            'washing' => $formatStation('Cuci', $order->prepWashingBy?->name, $order->prep_washing_started_at, $order->prep_washing_completed_at, $order->isStationUnneeded('prep_washing')),
            'sol' => $formatStation('Persiapan Sol', $order->prepSolBy?->name, $order->prep_sol_started_at, $order->prep_sol_completed_at, $order->isStationUnneeded('prep_sol')),
            'upper' => $formatStation('Persiapan Upper', $order->prepUpperBy?->name, $order->prep_upper_started_at, $order->prep_upper_completed_at, $order->isStationUnneeded('prep_upper')),
        ];

        $production = [
            'sol' => $formatStation('Produksi Sol', $order->prodSolBy?->name, $order->prod_sol_started_at, $order->prod_sol_completed_at, $order->isStationUnneeded('prod_sol')),
            'upper' => $formatStation('Produksi Upper', $order->prodUpperBy?->name, $order->prod_upper_started_at, $order->prod_upper_completed_at, $order->isStationUnneeded('prod_upper')),
            'cleaning' => $formatStation('QC Treatment', $order->prodCleaningBy?->name, $order->prod_cleaning_started_at, $order->prod_cleaning_completed_at, $order->isStationUnneeded('prod_cleaning')),
        ];

        $qc = [
            'jahit' => $formatStation('QC Jahit', $order->qcJahitBy?->name, $order->qc_jahit_started_at, $order->qc_jahit_completed_at, $order->isStationUnneeded('qc_jahit')),
            'cleanup' => $formatStation('QC Cleanup', $order->qcCleanupBy?->name, $order->qc_cleanup_started_at, $order->qc_cleanup_completed_at, $order->isStationUnneeded('qc_cleanup')),
            'final' => $formatStation('QC Final', $order->qcFinalBy?->name, $order->qc_final_started_at, $order->qc_final_completed_at, false),
        ];

        // Detect bottleneck: find the station that is in_progress or pending the longest
        $bottleneck = null;
        $allStations = array_merge(array_values($preparation), array_values($production), array_values($qc));
        foreach ($allStations as $station) {
            if ($station['status'] === 'in_progress') {
                $bottleneck = $station['label'];
                break;
            }
        }
        if (!$bottleneck) {
            foreach ($allStations as $station) {
                if ($station['status'] === 'pending' && !$station['is_unneeded']) {
                    $bottleneck = $station['label'];
                    break;
                }
            }
        }

        $totalElapsed = $order->entry_date
            ? Carbon::parse($order->entry_date)->diffInDays($order->finished_date ?? Carbon::now())
            : null;

        $isOverdue = $order->estimation_date && Carbon::parse($order->estimation_date)->isPast()
            && !in_array($order->status?->value, ['SELESAI', 'DIANTAR', 'HISTORY', 'BATAL', 'DONASI']);

        // === Status Transitions (full chronology from work_order_logs) ===
        $statusChangeLogs = $order->logs()
            ->where('action', 'STATUS_CHANGE')
            ->orderBy('created_at', 'asc')
            ->get();

        $statusTransitions = $statusChangeLogs->map(fn($l) => [
            'step' => $l->step,
            'description' => $l->description,
            'at' => $l->created_at?->format('d M Y, H:i') ?? '-',
            'timestamp' => $l->created_at?->toIso8601String(),
        ])->toArray();

        // === Duration Per Status (how long SPK spent in each status) ===
        $durationPerStatus = [];
        for ($i = 0; $i < count($statusChangeLogs); $i++) {
            $current = $statusChangeLogs[$i];
            $next = $statusChangeLogs[$i + 1] ?? null;
            $endTime = $next ? $next->created_at : ($order->finished_date ?? Carbon::now());
            $daysInStatus = $current->created_at->diffInDays($endTime);
            $hoursInStatus = $current->created_at->diffInHours($endTime);
            $stepKey = $current->step;

            if (!isset($durationPerStatus[$stepKey])) {
                $durationPerStatus[$stepKey] = [
                    'label' => str_replace('_', ' ', $stepKey),
                    'total_days' => 0,
                    'total_hours' => 0,
                    'entered_at' => $current->created_at->format('d M Y, H:i'),
                ];
            }
            $durationPerStatus[$stepKey]['total_days'] += $daysInStatus;
            $durationPerStatus[$stepKey]['total_hours'] += $hoursInStatus;
        }

        // === CX Issues during process ===
        $cxIssuesDuringProcess = $order->cxIssues()
            ->with('reporter')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($i) => [
                'category' => $i->category ?? '-',
                'kendala' => $i->kendala ?? $i->description ?? '-',
                'status' => $i->status,
                'reporter' => $i->reporter?->name ?? '-',
                'created_at' => $i->created_at?->format('d M Y, H:i') ?? '-',
            ])->toArray();

        // === Surat Jalan History ===
        $suratJalanLogs = $order->logs()
            ->where('action', 'ISSUE_SURAT_JALAN')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($l) => [
                'description' => $l->description,
                'at' => $l->created_at?->format('d M Y, H:i') ?? '-',
            ])->toArray();

        return [
            'status' => 'success',
            'spk_number' => $order->spk_number,
            'customer_name' => $order->customer_name,
            'current_status' => $order->status ? str_replace('_', ' ', $order->status->value) : '-',
            'current_location' => $order->current_location ?? '-',
            'preparation' => $preparation,
            'production' => $production,
            'qc' => $qc,
            'bottleneck' => $bottleneck,
            'total_elapsed_days' => $totalElapsed,
            'is_overdue' => $isOverdue,
            'estimation_date' => $order->estimation_date?->format('d M Y') ?? '-',
            'entry_date' => $order->entry_date?->format('d M Y') ?? '-',
            'finished_date' => $order->finished_date?->format('d M Y') ?? null,
            'status_transitions' => $statusTransitions,
            'duration_per_status' => array_values($durationPerStatus),
            'cx_issues_during_process' => $cxIssuesDuringProcess,
            'surat_jalan_history' => $suratJalanLogs,
            'card' => $this->buildCardData($order),
        ];
    }

    /**
     * Tool 7: Technician Analytics
     */
    protected function executeGetTechnicianAnalytics(array $args): array
    {
        [$start, $end] = $this->resolvePeriodFilter(
            $args['period'] ?? null,
            $args['start_date'] ?? null,
            $args['end_date'] ?? null
        );

        $technicianName = $args['technician_name'] ?? null;

        // Get technicians from Users with role 'technician'
        $techQuery = User::where('role', 'technician');
        if ($technicianName) {
            $techQuery->where('name', 'like', "%{$technicianName}%");
        }
        $technicians = $techQuery->get();

        $results = [];
        foreach ($technicians as $tech) {
            $techId = $tech->id;

            // Count SPKs where this technician is assigned to any production/QC station
            $totalHandled = WorkOrder::whereBetween('created_at', [$start, $end])
                ->where(function ($q) use ($techId) {
                    $q->where('prod_sol_by', $techId)
                      ->orWhere('prod_upper_by', $techId)
                      ->orWhere('prod_cleaning_by', $techId)
                      ->orWhere('qc_jahit_by', $techId)
                      ->orWhere('qc_cleanup_by', $techId)
                      ->orWhere('prep_washing_by', $techId)
                      ->orWhere('prep_sol_by', $techId)
                      ->orWhere('prep_upper_by', $techId);
                })->count();

            // Active (not in terminal status)
            $activeCount = WorkOrder::whereNotIn('status', ['SELESAI', 'DIANTAR', 'HISTORY', 'BATAL', 'DONASI'])
                ->where(function ($q) use ($techId) {
                    $q->where('prod_sol_by', $techId)
                      ->orWhere('prod_upper_by', $techId)
                      ->orWhere('prod_cleaning_by', $techId)
                      ->orWhere('qc_jahit_by', $techId)
                      ->orWhere('qc_cleanup_by', $techId)
                      ->orWhere('prep_washing_by', $techId)
                      ->orWhere('prep_sol_by', $techId)
                      ->orWhere('prep_upper_by', $techId);
                })->count();

            // Completed
            $completedCount = WorkOrder::whereBetween('created_at', [$start, $end])
                ->whereIn('status', ['SELESAI', 'DIANTAR', 'HISTORY'])
                ->where(function ($q) use ($techId) {
                    $q->where('prod_sol_by', $techId)
                      ->orWhere('prod_upper_by', $techId)
                      ->orWhere('prod_cleaning_by', $techId);
                })->count();

            // Revision count
            $revisionCount = WorkOrderRevision::whereBetween('created_at', [$start, $end])
                ->whereHas('workOrder', function ($q) use ($techId) {
                    $q->where('prod_sol_by', $techId)
                      ->orWhere('prod_upper_by', $techId)
                      ->orWhere('prod_cleaning_by', $techId);
                })->count();

            // Station breakdown
            $stations = [
                'prod_sol' => WorkOrder::whereBetween('created_at', [$start, $end])->where('prod_sol_by', $techId)->count(),
                'prod_upper' => WorkOrder::whereBetween('created_at', [$start, $end])->where('prod_upper_by', $techId)->count(),
                'prod_cleaning' => WorkOrder::whereBetween('created_at', [$start, $end])->where('prod_cleaning_by', $techId)->count(),
                'qc_jahit' => WorkOrder::whereBetween('created_at', [$start, $end])->where('qc_jahit_by', $techId)->count(),
                'prep_washing' => WorkOrder::whereBetween('created_at', [$start, $end])->where('prep_washing_by', $techId)->count(),
            ];

            $results[] = [
                'name' => $tech->name,
                'role' => $tech->role,
                'total_spk_handled' => $totalHandled,
                'active_spk' => $activeCount,
                'completed_spk' => $completedCount,
                'revision_count' => $revisionCount,
                'stations' => $stations,
            ];
        }

        // Sort by total handled desc
        usort($results, fn($a, $b) => $b['total_spk_handled'] <=> $a['total_spk_handled']);

        $topPerformer = !empty($results) ? $results[0] : null;
        $mostRevisions = collect($results)->sortByDesc('revision_count')->first();

        return [
            'status' => 'success',
            'period_range' => $start->format('d M Y') . ' - ' . $end->format('d M Y'),
            'total_technicians' => count($results),
            'technicians' => array_slice($results, 0, 10),
            'top_performer' => $topPerformer ? ['name' => $topPerformer['name'], 'total_handled' => $topPerformer['total_spk_handled']] : null,
            'most_revisions' => $mostRevisions ? ['name' => $mostRevisions['name'], 'revision_count' => $mostRevisions['revision_count']] : null,
        ];
    }

    /**
     * Tool 8: Revision & Warranty Data
     */
    protected function executeGetRevisionWarrantyData(array $args): array
    {
        $identifier = $args['identifier'] ?? null;

        // Per-SPK mode
        if (!empty($identifier)) {
            $order = $this->findOrderByIdentifier($identifier);
            if (!$order) {
                return ['status' => 'not_found', 'message' => "SPK '{$identifier}' tidak ditemukan."];
            }

            $revisions = $order->revisions()->with('creator', 'resolver')->latest()->get()->map(fn($r) => [
                'id' => $r->id,
                'description' => $r->description,
                'status' => $r->status,
                'qc_stage' => $r->qc_stage ?? '-',
                'loss_amount' => (float) ($r->loss_amount ?? 0),
                'loss_category' => $r->loss_category ?? '-',
                'responsible_party' => $r->responsible_party ?? '-',
                'created_by' => $r->creator?->name ?? '-',
                'resolved_by' => $r->resolver?->name ?? null,
                'finished_at' => $r->finished_at?->format('d M Y, H:i') ?? null,
                'created_at' => $r->created_at?->format('d M Y, H:i') ?? '-',
            ])->toArray();

            $warranties = $order->warranties()->with('creator', 'finisher')->latest()->get()->map(fn($w) => [
                'id' => $w->id,
                'garansi_spk_number' => $w->garansi_spk_number,
                'description' => $w->description,
                'status' => $w->status,
                'loss_amount' => (float) ($w->loss_amount ?? 0),
                'loss_category' => $w->loss_category ?? '-',
                'responsible_party' => $w->responsible_party ?? '-',
                'created_by' => $w->creator?->name ?? '-',
                'finished_at' => $w->finished_at?->format('d M Y, H:i') ?? null,
            ])->toArray();

            $totalLoss = collect($revisions)->sum('loss_amount') + collect($warranties)->sum('loss_amount');

            return [
                'status' => 'success',
                'mode' => 'per_spk',
                'spk_number' => $order->spk_number,
                'customer_name' => $order->customer_name,
                'revisions' => $revisions,
                'warranties' => $warranties,
                'total_revisions' => count($revisions),
                'total_warranties' => count($warranties),
                'total_loss_amount' => $totalLoss,
                'card' => $this->buildCardData($order),
            ];
        }

        // Aggregate mode
        [$start, $end] = $this->resolvePeriodFilter(
            $args['period'] ?? null,
            $args['start_date'] ?? null,
            $args['end_date'] ?? null
        );

        $totalRevisions = WorkOrderRevision::whereBetween('created_at', [$start, $end])->count();
        $totalWarranties = WorkOrderWarranty::whereBetween('created_at', [$start, $end])->count();
        $totalLoss = WorkOrderRevision::whereBetween('created_at', [$start, $end])->sum('loss_amount')
            + WorkOrderWarranty::whereBetween('created_at', [$start, $end])->sum('loss_amount');

        $byResponsible = WorkOrderRevision::whereBetween('created_at', [$start, $end])
            ->selectRaw('responsible_party, COUNT(*) as count')
            ->groupBy('responsible_party')
            ->pluck('count', 'responsible_party')
            ->toArray();

        $byQcStage = WorkOrderRevision::whereBetween('created_at', [$start, $end])
            ->selectRaw('qc_stage, COUNT(*) as count')
            ->groupBy('qc_stage')
            ->pluck('count', 'qc_stage')
            ->toArray();

        $topLoss = WorkOrderRevision::whereBetween('created_at', [$start, $end])
            ->where('loss_amount', '>', 0)
            ->with('workOrder:id,spk_number,customer_name')
            ->orderByDesc('loss_amount')
            ->take(5)
            ->get()
            ->map(fn($r) => [
                'spk_number' => $r->workOrder?->spk_number ?? '-',
                'customer_name' => $r->workOrder?->customer_name ?? '-',
                'loss_amount' => (float) $r->loss_amount,
                'description' => $r->description,
            ])->toArray();

        return [
            'status' => 'success',
            'mode' => 'aggregate',
            'period_range' => $start->format('d M Y') . ' - ' . $end->format('d M Y'),
            'total_revisions' => $totalRevisions,
            'total_warranties' => $totalWarranties,
            'total_loss' => (float) $totalLoss,
            'by_responsible_party' => $byResponsible,
            'by_qc_stage' => $byQcStage,
            'top_loss_spks' => $topLoss,
        ];
    }

    /**
     * Tool 9: CX Issues & Complaints Data
     */
    protected function executeGetCxIssuesData(array $args): array
    {
        $identifier = $args['identifier'] ?? null;
        $statusFilter = $args['status_filter'] ?? 'ALL';

        // Per-SPK mode
        if (!empty($identifier)) {
            $order = $this->findOrderByIdentifier($identifier);
            if (!$order) {
                return ['status' => 'not_found', 'message' => "SPK '{$identifier}' tidak ditemukan."];
            }

            $issueQuery = $order->cxIssues();
            if ($statusFilter !== 'ALL') {
                $issueQuery->where('status', $statusFilter);
            }

            $issues = $issueQuery->with('reporter', 'resolver')->latest()->get()->map(fn($i) => [
                'id' => $i->id,
                'status' => $i->status,
                'is_resolved' => $i->status === 'RESOLVED',
                'category' => $i->category ?? '-',
                'kendala' => $i->kendala ?? $i->description ?? '-',
                'opsi_solusi' => $i->opsi_solusi ?? '-',
                'reporter' => $i->reporter?->name ?? '-',
                'resolver' => $i->resolver?->name ?? '-',
                'resolved_at' => $i->resolved_at?->format('d M Y, H:i') ?? null,
                'resolution_notes' => $i->resolution_notes ?? null,
                'created_at' => $i->created_at?->format('d M Y, H:i') ?? '-',
            ])->toArray();

            $complaints = $order->complaints()->latest()->get()->map(fn($c) => [
                'id' => $c->id,
                'category' => $c->category ?? '-',
                'description' => $c->description,
                'status' => $c->status,
                'admin_notes' => $c->admin_notes ?? null,
                'created_at' => $c->created_at?->format('d M Y, H:i') ?? '-',
            ])->toArray();

            $openCount = collect($issues)->where('status', 'OPEN')->count();
            $resolvedCount = collect($issues)->where('status', 'RESOLVED')->count();

            return [
                'status' => 'success',
                'mode' => 'per_spk',
                'spk_number' => $order->spk_number,
                'customer_name' => $order->customer_name,
                'has_cx_issues' => count($issues) > 0,
                'open_issues_count' => $openCount,
                'resolved_issues_count' => $resolvedCount,
                'issues' => $issues,
                'complaints' => $complaints,
                'total_issues' => count($issues),
                'total_complaints' => count($complaints),
                'card' => $this->buildCardData($order),
            ];
        }

        // Aggregate mode (Company-wide CX Issues)
        [$start, $end] = $this->resolvePeriodFilter(
            $args['period'] ?? null,
            $args['start_date'] ?? null,
            $args['end_date'] ?? null
        );

        // 1. ALL currently active OPEN issues (backlog across all time, not bounded by date)
        $allActiveOpenIssues = CxIssue::where('status', 'OPEN')
            ->with(['workOrder:id,spk_number,customer_name,shoe_brand,shoe_color,status,current_location', 'reporter'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($i) => [
                'id' => $i->id,
                'spk_number' => $i->workOrder?->spk_number ?? $i->spk_number ?? '-',
                'customer_name' => $i->workOrder?->customer_name ?? '-',
                'shoe' => trim(($i->workOrder?->shoe_brand ?? '') . ' ' . ($i->workOrder?->shoe_color ?? '')),
                'spk_status' => $i->workOrder?->status ? str_replace('_', ' ', $i->workOrder->status->value) : '-',
                'location' => $i->workOrder?->current_location ?? '-',
                'category' => $i->category ?? '-',
                'kendala' => $i->kendala ?? $i->description ?? '-',
                'opsi_solusi' => $i->opsi_solusi ?? '-',
                'days_open' => $i->created_at ? round(Carbon::parse($i->created_at)->diffInDays(Carbon::now()), 1) : 0,
                'reporter' => $i->reporter?->name ?? '-',
                'created_at' => $i->created_at?->format('d M Y, H:i') ?? '-',
            ])->toArray();

        // 2. Recent resolved issues
        $recentResolvedIssues = CxIssue::where('status', 'RESOLVED')
            ->with(['workOrder:id,spk_number,customer_name', 'resolver'])
            ->latest('resolved_at')
            ->take(10)
            ->get()
            ->map(fn($i) => [
                'id' => $i->id,
                'spk_number' => $i->workOrder?->spk_number ?? $i->spk_number ?? '-',
                'customer_name' => $i->workOrder?->customer_name ?? '-',
                'category' => $i->category ?? '-',
                'kendala' => $i->kendala ?? $i->description ?? '-',
                'resolution_notes' => $i->resolution_notes ?? '-',
                'resolver' => $i->resolver?->name ?? '-',
                'resolved_at' => $i->resolved_at?->format('d M Y, H:i') ?? '-',
            ])->toArray();

        // 3. Open Complaints
        $openComplaints = Complaint::where('status', 'OPEN')
            ->with('workOrder:id,spk_number,customer_name')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'spk_number' => $c->workOrder?->spk_number ?? '-',
                'customer_name' => $c->workOrder?->customer_name ?? '-',
                'category' => $c->category ?? '-',
                'description' => $c->description,
                'created_at' => $c->created_at?->format('d M Y, H:i') ?? '-',
            ])->toArray();

        $byCategory = CxIssue::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $avgResolutionHours = CxIssue::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        return [
            'status' => 'success',
            'mode' => 'aggregate',
            'total_active_open_issues' => count($allActiveOpenIssues),
            'active_open_issues' => $allActiveOpenIssues,
            'recent_resolved_issues' => $recentResolvedIssues,
            'total_open_complaints' => count($openComplaints),
            'open_complaints' => $openComplaints,
            'by_category' => $byCategory,
            'avg_resolution_hours' => round((float) ($avgResolutionHours ?? 0), 1),
        ];
    }

    /**
     * Tool 10: Storage & Logistics
     */
    protected function executeGetStorageLogistics(array $args): array
    {
        $identifier = $args['identifier'] ?? null;
        $rackCode = $args['rack_code'] ?? null;
        $filter = $args['filter'] ?? 'all';

        // Mode 1: Specific Rack Lookup (Daftar SPK yang ada di dalam rak tertentu)
        if (!empty($rackCode)) {
            $rackCodeUpper = strtoupper(trim($rackCode));
            $assignments = StorageAssignment::stored()
                ->where('rack_code', $rackCodeUpper)
                ->with(['workOrder' => function($q) {
                    $q->select('id', 'spk_number', 'customer_name', 'customer_phone', 'shoe_brand', 'shoe_color', 'status', 'status_pembayaran', 'sisa_tagihan', 'finished_date');
                }])
                ->get()
                ->map(fn($a) => [
                    'spk_number' => $a->workOrder?->spk_number ?? '-',
                    'customer_name' => $a->workOrder?->customer_name ?? '-',
                    'customer_phone' => $a->workOrder?->customer_phone ?? '-',
                    'shoe' => trim(($a->workOrder?->shoe_brand ?? '') . ' ' . ($a->workOrder?->shoe_color ?? '')),
                    'status_spk' => $a->workOrder?->status ? str_replace('_', ' ', $a->workOrder->status->value) : '-',
                    'status_pembayaran' => $a->workOrder?->status_pembayaran ?? '-',
                    'sisa_piutang' => (float) ($a->workOrder?->sisa_tagihan ?? 0),
                    'stored_at' => $a->stored_at?->format('d M Y, H:i') ?? '-',
                    'days_stored' => $a->stored_at ? round($a->stored_at->diffInDays(Carbon::now()), 1) : 0,
                    'is_overdue' => $a->stored_at ? $a->stored_at->diffInDays(Carbon::now()) > 7 : false,
                ])->toArray();

            return [
                'status' => 'success',
                'mode' => 'rack_lookup',
                'rack_code' => $rackCodeUpper,
                'total_items' => count($assignments),
                'items' => $assignments,
                'message' => count($assignments) > 0 
                    ? "Ditemukan " . count($assignments) . " SPK yang tersimpan di Rak {$rackCodeUpper}."
                    : "Rak {$rackCodeUpper} saat ini kosong (tidak ada sepatu yang tersimpan).",
            ];
        }

        // Mode 2: Per-SPK mode
        if (!empty($identifier)) {
            $order = $this->findOrderByIdentifier($identifier);
            if (!$order) {
                return ['status' => 'not_found', 'message' => "SPK '{$identifier}' tidak ditemukan."];
            }

            $order->load(['storageAssignments.rack', 'suratJalans']);

            $activeAssignment = $order->storageAssignments()->where('status', 'stored')->latest()->first() 
                ?? $order->storageAssignments()->latest()->first();

            $currentRack = $activeAssignment?->rack_code ?? $activeAssignment?->rack?->name;

            $assignments = $order->storageAssignments->map(fn($a) => [
                'rack_code' => $a->rack_code,
                'rack_name' => $a->rack?->name ?? $a->rack_code,
                'stored_at' => $a->stored_at?->format('d M Y, H:i') ?? '-',
                'retrieved_at' => $a->retrieved_at?->format('d M Y, H:i') ?? null,
                'duration_days' => $a->stored_at ? $a->stored_at->diffInDays($a->retrieved_at ?? Carbon::now()) : 0,
                'status' => $a->status ?? '-',
                'item_type' => $a->item_type ?? 'shoes',
            ])->toArray();

            $suratJalans = $order->suratJalans->map(fn($sj) => [
                'sj_number' => $sj->sj_number ?? '-',
                'date' => $sj->created_at?->format('d M Y') ?? '-',
                'destination' => $sj->destination ?? '-',
            ])->toArray();

            return [
                'status' => 'success',
                'mode' => 'per_spk',
                'spk_number' => $order->spk_number,
                'customer_name' => $order->customer_name,
                'current_rack' => $currentRack ?? 'Belum Masuk Rak',
                'current_location' => $order->current_location ?? 'Belum Ditentukan',
                'is_in_rack' => !empty($currentRack) && ($activeAssignment?->status === 'stored'),
                'pickup_method' => $order->pickup_method ?? '-',
                'shipping_type' => $order->shipping_type ?? '-',
                'tracking_number' => $order->customer_tracking_number ?? '-',
                'shipped_at' => $order->customer_shipped_at?->format('d M Y') ?? null,
                'assignments' => $assignments,
                'surat_jalans' => $suratJalans,
                'card' => $this->buildCardData($order),
            ];
        }

        // Aggregate mode (Resmi sinkron dengan /admin/kpi - Tab KPI GUDANG)
        [$start, $end] = $this->resolvePeriodFilter(
            $args['period'] ?? null,
            $args['start_date'] ?? null,
            $args['end_date'] ?? null
        );

        $kpiService = app(\App\Services\KpiService::class);
        $gudangKpi = $kpiService->getGudangKpi($start, $end);

        // Operasional Rak Fisik (Live saat ini)
        $totalStored = StorageAssignment::stored()->count();
        $totalOverdue = StorageAssignment::overdue(7)->count();

        // Group by rack
        $byRack = StorageAssignment::stored()
            ->selectRaw('rack_code, COUNT(*) as count')
            ->groupBy('rack_code')
            ->orderByDesc('count')
            ->take(10)
            ->pluck('count', 'rack_code')
            ->toArray();

        $overdueItems = StorageAssignment::overdue(7)
            ->with('workOrder:id,spk_number,customer_name')
            ->orderBy('stored_at', 'asc')
            ->take(5)
            ->get()
            ->map(fn($a) => [
                'spk_number' => $a->workOrder?->spk_number ?? '-',
                'customer_name' => $a->workOrder?->customer_name ?? '-',
                'rack_code' => $a->rack_code,
                'days_stored' => $a->stored_at ? $a->stored_at->diffInDays(Carbon::now()) : 0,
            ])->toArray();

        // SPK Selesai yang masih di rak / belum diambil customer
        $waitingPickupCount = WorkOrder::where('status', \App\Enums\WorkOrderStatus::SELESAI)
            ->whereNull('taken_date')
            ->count();

        return [
            'status' => 'success',
            'mode' => 'aggregate',
            'period_range' => $start->format('d M Y') . ' s/d ' . $end->format('d M Y'),
            'kpi_gudang_official' => [
                'sepatu_masuk' => (int) ($gudangKpi['sepatu_masuk'] ?? 0),
                'spk_otw' => (int) ($gudangKpi['spk_otw'] ?? 0),
                'qc_reject' => (int) ($gudangKpi['qc_reject'] ?? 0),
                'after_masuk' => (int) ($gudangKpi['after_masuk'] ?? 0),
                'sepatu_keluar' => (int) ($gudangKpi['sepatu_keluar'] ?? 0),
            ],
            'kpi_descriptions' => [
                'sepatu_masuk' => '1. SEPATU MASUK (BEFORE) - DITERIMA FISIK DI GUDANG',
                'spk_otw' => '2. SPK PRINT (OTW WS) - DIKIRIM KE REPARASI / MANIFEST',
                'qc_reject' => '3. SPK TERTAHAN (QC REJECT) - GAGAL PENERIMAAN AWAL',
                'after_masuk' => '4. AFTER MASUK - SELESAI REPARASI MASUK RAK',
                'sepatu_keluar' => '5. SEPATU KELUAR - PENGAMBILAN & KIRIM LUNAS',
            ],
            'operational_status' => [
                'total_stored' => $totalStored,
                'total_overdue' => $totalOverdue,
                'waiting_pickup_count' => $waitingPickupCount,
                'by_rack' => $byRack,
                'overdue_items' => $overdueItems,
            ],
        ];
    }

    /**
     * Tool 11: Workshop Production Intelligence (/admin/kpi - Tab Workshop, Live Floor, Overdue SLA & Technician Workload)
     */
    protected function executeGetWorkshopProductionIntelligence(array $args): array
    {
        [$start, $end] = $this->resolvePeriodFilter(
            $args['period'] ?? null,
            $args['start_date'] ?? null,
            $args['end_date'] ?? null
        );

        $mode = $args['mode'] ?? 'all';
        $stationFilter = isset($args['station']) ? strtoupper(trim($args['station'])) : null;
        $technicianName = isset($args['technician_name']) ? trim($args['technician_name']) : null;
        $now = Carbon::now();

        // 1. Fetch active WorkOrders with relations in single optimized query
        $activeOrders = WorkOrder::whereNotIn('status', ['SELESAI', 'DIANTAR', 'BATAL', 'HISTORY'])
            ->with([
                'creator',
                'technicianProduction',
                'prepWashingBy', 'prepSolBy', 'prepUpperBy',
                'picSortirSol', 'picSortirUpper',
                'prodSolBy', 'prodUpperBy', 'prodCleaningBy',
                'qcJahitBy', 'qcCleanupBy', 'qcFinalBy',
                'workOrderServices.service'
            ])
            ->get();

        // 2. Compute Overdue & Approaching SLA
        $overdueList = [];
        $approachingList = [];
        $fastTrackCount = 0;

        foreach ($activeOrders as $wo) {
            if ($wo->fast_track_status === 'yes') {
                $fastTrackCount++;
            }

            $est = $wo->new_estimation_date ?? $wo->estimation_date;
            $statusStr = is_object($wo->status) ? $wo->status->value : (string) $wo->status;

            // Determine active responsible technician for current stage
            $activeTech = match($statusStr) {
                'PREPARATION' => $wo->prepUpperBy?->name ?? $wo->prepSolBy?->name ?? $wo->prepWashingBy?->name ?? 'Belum Ditugaskan',
                'SORTIR' => $wo->picSortirUpper?->name ?? $wo->picSortirSol?->name ?? 'Tim Sortir',
                'PRODUCTION' => $wo->prodSolBy?->name ?? $wo->prodUpperBy?->name ?? $wo->prodCleaningBy?->name ?? $wo->technicianProduction?->name ?? 'Tim Produksi',
                'QC' => $wo->qcFinalBy?->name ?? $wo->qcCleanupBy?->name ?? $wo->qcJahitBy?->name ?? 'Tim QC',
                default => '-'
            };

            if ($est) {
                $estCarbon = Carbon::parse($est);
                $diffDays = $now->diffInDays($estCarbon, false); // negative if past

                if ($diffDays < 0) {
                    $overdueList[] = [
                        'spk_number' => $wo->spk_number,
                        'customer_name' => $wo->customer_name,
                        'shoe' => trim(($wo->shoe_brand ?? '') . ' ' . ($wo->shoe_color ?? '')),
                        'current_stage' => $statusStr,
                        'fast_track' => $wo->fast_track_status === 'yes',
                        'estimation_date' => $estCarbon->format('d M Y'),
                        'days_overdue' => abs(round($diffDays, 1)),
                        'urgency' => 'CRITICAL',
                        'technician' => $activeTech,
                    ];
                } elseif ($diffDays <= 2) {
                    $approachingList[] = [
                        'spk_number' => $wo->spk_number,
                        'customer_name' => $wo->customer_name,
                        'shoe' => trim(($wo->shoe_brand ?? '') . ' ' . ($wo->shoe_color ?? '')),
                        'current_stage' => $statusStr,
                        'fast_track' => $wo->fast_track_status === 'yes',
                        'estimation_date' => $estCarbon->format('d M Y'),
                        'days_remaining' => round($diffDays, 1),
                        'urgency' => 'WARNING',
                        'technician' => $activeTech,
                    ];
                }
            }
        }

        usort($overdueList, fn($a, $b) => $b['days_overdue'] <=> $a['days_overdue']);

        // 3. Technician Workload Live
        $techQuery = User::where('role', 'technician');
        if ($technicianName) {
            $techQuery->where('name', 'like', "%{$technicianName}%");
        }
        $technicians = $techQuery->get();
        $technicianWorkload = [];

        foreach ($technicians as $tech) {
            $assignedOrders = $activeOrders->filter(function($wo) use ($tech) {
                return $wo->prep_washing_by == $tech->id
                    || $wo->prep_sol_by == $tech->id
                    || $wo->prep_upper_by == $tech->id
                    || $wo->pic_sortir_sol_id == $tech->id
                    || $wo->pic_sortir_upper_id == $tech->id
                    || $wo->prod_sol_by == $tech->id
                    || $wo->prod_upper_by == $tech->id
                    || $wo->prod_cleaning_by == $tech->id
                    || $wo->technician_production_id == $tech->id
                    || $wo->qc_jahit_by == $tech->id
                    || $wo->qc_cleanup_by == $tech->id
                    || $wo->qc_final_by == $tech->id;
            });

            $count = $assignedOrders->count();
            $statusLoad = $count >= 8 ? 'OVERLOAD' : ($count >= 4 ? 'MODERATE' : 'AVAILABLE');

            $technicianWorkload[] = [
                'id' => $tech->id,
                'name' => $tech->name,
                'active_spk_count' => $count,
                'load_status' => $statusLoad,
                'assigned_spks' => $assignedOrders->pluck('spk_number')->take(5)->values()->toArray(),
            ];
        }
        usort($technicianWorkload, fn($a, $b) => $b['active_spk_count'] <=> $a['active_spk_count']);

        // 4. Stations Live Queue
        $stationsLive = [
            'PREPARATION' => [
                'total_queue' => $activeOrders->where('status', 'PREPARATION')->count(),
                'washing_pending' => $activeOrders->where('status', 'PREPARATION')->whereNull('prep_washing_completed_at')->count(),
                'sol_pending' => $activeOrders->where('status', 'PREPARATION')->whereNull('prep_sol_completed_at')->count(),
                'upper_pending' => $activeOrders->where('status', 'PREPARATION')->whereNull('prep_upper_completed_at')->count(),
            ],
            'SORTIR' => [
                'total_queue' => $activeOrders->where('status', 'SORTIR')->count(),
                'perlu_bongkar' => $activeOrders->where('status', 'SORTIR')->where('perlu_bongkar', 1)->count(),
                'perlu_belanja' => $activeOrders->where('status', 'SORTIR')->where('perlu_belanja', 1)->count(),
            ],
            'PRODUCTION' => [
                'total_queue' => $activeOrders->where('status', 'PRODUCTION')->count(),
                'sol_pending' => $activeOrders->where('status', 'PRODUCTION')->whereNull('prod_sol_completed_at')->count(),
                'upper_pending' => $activeOrders->where('status', 'PRODUCTION')->whereNull('prod_upper_completed_at')->count(),
                'treatment_pending' => $activeOrders->where('status', 'PRODUCTION')->whereNull('prod_cleaning_completed_at')->count(),
            ],
            'QC' => [
                'total_queue' => $activeOrders->where('status', 'QC')->count(),
                'jahit_pending' => $activeOrders->where('status', 'QC')->whereNull('qc_jahit_completed_at')->count(),
                'cleanup_pending' => $activeOrders->where('status', 'QC')->whereNull('qc_cleanup_completed_at')->count(),
                'final_pending' => $activeOrders->where('status', 'QC')->whereNull('qc_final_completed_at')->count(),
            ],
        ];

        // 5. Official KPI Throughput from KpiService (/admin/kpi Tab Workshop)
        /** @var \App\Services\KpiService $kpiService */
        $kpiService = app(\App\Services\KpiService::class);
        $kpiWorkshop = $kpiService->getWorkshopKpi($start, $end);

        // Identify bottleneck stage from KPI or Live queue
        $bottleneckStage = 'PRODUCTION';
        $maxQueue = 0;
        foreach ($stationsLive as $stageKey => $stageData) {
            if ($stageData['total_queue'] > $maxQueue) {
                $maxQueue = $stageData['total_queue'];
                $bottleneckStage = $stageKey;
            }
        }

        // Filter response by mode if requested
        $response = [
            'status' => 'success',
            'period' => [
                'start' => $start,
                'end' => $end,
                'label' => Carbon::parse($start)->format('d M Y') . ' s/d ' . Carbon::parse($end)->format('d M Y'),
            ],
            'bottleneck_stage' => $bottleneckStage,
            'summary' => [
                'total_active_orders' => $activeOrders->count(),
                'total_overdue' => count($overdueList),
                'total_approaching_deadline' => count($approachingList),
                'total_fast_track_active' => $fastTrackCount,
            ],
        ];

        if ($mode === 'bottlenecks' || $mode === 'all') {
            $response['deadline_alerts'] = [
                'total_overdue' => count($overdueList),
                'total_approaching' => count($approachingList),
                'critical_overdue_list' => array_slice($overdueList, 0, 10),
                'approaching_deadline_list' => array_slice($approachingList, 0, 10),
            ];
        }

        if ($mode === 'technicians' || $mode === 'all') {
            $response['technician_workload'] = [
                'total_technicians' => count($technicianWorkload),
                'overloaded_technicians' => array_values(array_filter($technicianWorkload, fn($t) => $t['load_status'] === 'OVERLOAD')),
                'top_workload' => array_slice($technicianWorkload, 0, 8),
                'available_technicians' => array_values(array_filter($technicianWorkload, fn($t) => $t['load_status'] === 'AVAILABLE')),
            ];
        }

        if ($mode === 'stations' || $mode === 'all') {
            $response['stations_live_queue'] = $stationFilter && isset($stationsLive[$stationFilter])
                ? [$stationFilter => $stationsLive[$stationFilter]]
                : $stationsLive;
        }

        if ($mode === 'kpi_overview' || $mode === 'all') {
            $stageMeta = [
                'PREPARATION' => ['name' => '1. PREPARATION', 'sub_title' => 'Tahap Cuci & Pembongkaran', 'icon' => '🧼'],
                'SORTIR' => ['name' => '2. SORTIR', 'sub_title' => 'Tahap Sortir & Kelengkapan Material', 'icon' => '🔍'],
                'PRODUCTION' => ['name' => '3. PRODUCTION', 'sub_title' => 'Tahap Produksi / Repacking & Reparasi', 'icon' => '🛠️'],
                'QC' => ['name' => '4. QUALITY CONTROL', 'sub_title' => 'Tahap Quality Control & Finishing', 'icon' => '✅'],
            ];

            $enrichedKpiSummary = [];
            foreach ($kpiWorkshop['summary'] ?? [] as $stgKey => $stgVal) {
                $meta = $stageMeta[$stgKey] ?? ['name' => $stgKey, 'sub_title' => '-', 'icon' => '⚙️'];
                $enrichedKpiSummary[$stgKey] = array_merge($meta, [
                    'total_masuk' => $stgVal['total_masuk'] ?? 0,
                    'total_keluar' => $stgVal['total_keluar'] ?? 0,
                    'masuk_bersih' => $stgVal['masuk_bersih'] ?? 0,
                    'keluar_bersih' => $stgVal['keluar_bersih'] ?? 0,
                    'avg_duration' => $stgVal['avg_duration'] ?? '-',
                ]);
            }

            $response['kpi_workshop'] = [
                'stages' => $enrichedKpiSummary,
                'cx_transitions' => $kpiWorkshop['cx_transitions'] ?? [],
            ];
            $response['kpi_throughput'] = [
                'summary_per_stage' => $kpiWorkshop['summary'] ?? [],
                'cx_transitions' => $kpiWorkshop['cx_transitions'] ?? [],
            ];
        }

        return $response;
    }

    /**
     * Tool 12: OTO (On-The-Order) Data
     */
    protected function executeGetOtoData(array $args): array
    {
        $identifier = $args['identifier'] ?? null;
        $statusFilter = $args['status_filter'] ?? 'ALL';

        // Per-SPK mode
        if (!empty($identifier)) {
            $order = $this->findOrderByIdentifier($identifier);
            if (!$order) {
                return ['status' => 'not_found', 'message' => "SPK '{$identifier}' tidak ditemukan."];
            }

            $otoQuery = $order->otos();
            if ($statusFilter !== 'ALL') {
                $otoQuery->where('status', $statusFilter);
            }

            $otos = $otoQuery->latest()->get()->map(fn($o) => [
                'id' => $o->id,
                'title' => $o->title ?? '-',
                'oto_type' => $o->oto_type ?? '-',
                'status' => $o->status,
                'total_normal_price' => (float) ($o->total_normal_price ?? 0),
                'total_oto_price' => (float) ($o->total_oto_price ?? 0),
                'discount_percent' => (float) ($o->discount_percent ?? 0),
                'estimated_days' => $o->estimated_days ?? '-',
                'customer_responded_at' => $o->customer_responded_at?->format('d M Y, H:i') ?? null,
                'customer_note' => $o->customer_note ?? null,
                'production_progress' => [
                    'sol' => $o->oto_sol_completed_at ? 'completed' : ($o->oto_sol_started_at ? 'in_progress' : 'pending'),
                    'upper' => $o->oto_upper_completed_at ? 'completed' : ($o->oto_upper_started_at ? 'in_progress' : 'pending'),
                    'treatment' => $o->oto_treatment_completed_at ? 'completed' : ($o->oto_treatment_started_at ? 'in_progress' : 'pending'),
                ],
                'created_at' => $o->created_at?->format('d M Y, H:i') ?? '-',
            ])->toArray();

            return [
                'status' => 'success',
                'mode' => 'per_spk',
                'spk_number' => $order->spk_number,
                'customer_name' => $order->customer_name,
                'otos' => $otos,
                'total_otos' => count($otos),
                'card' => $this->buildCardData($order),
            ];
        }

        // Aggregate mode
        [$start, $end] = $this->resolvePeriodFilter(
            $args['period'] ?? null,
            $args['start_date'] ?? null,
            $args['end_date'] ?? null
        );

        $totalOtos = OTO::whereBetween('created_at', [$start, $end])->count();

        $byStatus = OTO::whereBetween('created_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $totalRevenue = OTO::whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['APPROVED', 'COMPLETED', 'IN_PROGRESS'])
            ->sum('total_oto_price');

        $pendingResponse = OTO::whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['PENDING_CX', 'CONTACTED', 'PENDING_CUSTOMER'])
            ->count();

        $totalApproved = OTO::whereBetween('created_at', [$start, $end])
            ->where('status', 'APPROVED')
            ->count();
        $totalAll = OTO::whereBetween('created_at', [$start, $end])->count();
        $conversionRate = $totalAll > 0 ? round(($totalApproved / $totalAll) * 100, 1) : 0;

        return [
            'status' => 'success',
            'mode' => 'aggregate',
            'period_range' => $start->format('d M Y') . ' - ' . $end->format('d M Y'),
            'total_otos' => $totalOtos,
            'by_status' => $byStatus,
            'total_oto_revenue' => (float) $totalRevenue,
            'pending_customer_response' => $pendingResponse,
            'conversion_rate' => $conversionRate,
        ];
    }

    /**
     * Tool 14: Intelejen Logistik, Surat Jalan, Manifest Inbound/Outbound, & Audit Kritis Pengiriman
     */
    protected function executeGetManifestShippingIntelligence(array $args): array
    {
        $mode = $args['mode'] ?? 'all';
        $identifier = trim($args['identifier'] ?? '');
        [$start, $end] = $this->resolvePeriodFilter(
            $args['period'] ?? null,
            $args['start_date'] ?? null,
            $args['end_date'] ?? null
        );

        $now = Carbon::parse('2026-09-23 15:47:00');

        $result = [
            'status' => 'success',
            'period' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'label' => $start->format('d M Y') . ' s/d ' . $end->format('d M Y'),
            ],
            'mode' => $mode,
        ];

        // 1. Inbound Manifests (Gudang -> Workshop)
        if ($mode === 'all' || $mode === 'inbound_manifest') {
            $manifestQuery = DB::table('workshop_manifests')
                ->leftJoin('users as d', 'workshop_manifests.dispatcher_id', '=', 'd.id')
                ->leftJoin('users as r', 'workshop_manifests.receiver_id', '=', 'r.id')
                ->whereNull('workshop_manifests.deleted_at')
                ->select(
                    'workshop_manifests.*',
                    'd.name as dispatcher_name',
                    'r.name as receiver_name'
                );

            if (!empty($identifier) && (str_starts_with($identifier, 'MNF') || str_starts_with($identifier, 'MFST') || is_numeric($identifier))) {
                $manifestQuery->where(function($q) use ($identifier) {
                    $q->where('workshop_manifests.manifest_number', 'like', "%{$identifier}%")
                      ->orWhere('workshop_manifests.id', $identifier);
                });
            }

            $rawManifests = $manifestQuery->latest('workshop_manifests.created_at')->get();

            $manifestsList = [];
            $stuckInTransitList = [];
            $totalInboundSpk = 0;
            $receivedCount = 0;
            $sentCount = 0;

            foreach ($rawManifests as $m) {
                $spksInManifest = DB::table('work_orders')
                    ->where('workshop_manifest_id', $m->id)
                    ->select('id', 'spk_number', 'customer_name', 'status', 'shoe_brand')
                    ->get()
                    ->map(fn($item) => [
                        'spk_number' => $item->spk_number,
                        'customer' => $item->customer_name,
                        'shoe' => $item->shoe_brand ?? 'Sepatu',
                        'status' => $item->status,
                    ])->toArray();

                $spkCount = count($spksInManifest);
                $totalInboundSpk += $spkCount;

                $dispatchedCarbon = $m->dispatched_at ? Carbon::parse($m->dispatched_at) : null;
                $receivedCarbon = $m->received_at ? Carbon::parse($m->received_at) : null;

                $transitHours = null;
                $isStuck = false;

                if ($m->status === 'RECEIVED' && $dispatchedCarbon && $receivedCarbon) {
                    $transitHours = round(abs($dispatchedCarbon->diffInHours($receivedCarbon)), 1);
                    $receivedCount++;
                } elseif ($m->status === 'SENT') {
                    $sentCount++;
                    if ($dispatchedCarbon) {
                        $transitHours = round(abs($dispatchedCarbon->diffInHours($now)), 1);
                        if ($transitHours > 24) {
                            $isStuck = true;
                            $stuckInTransitList[] = [
                                'manifest_number' => $m->manifest_number,
                                'dispatcher' => $m->dispatcher_name ?? 'Staf Gudang',
                                'dispatched_at' => $dispatchedCarbon->format('d M Y, H:i'),
                                'transit_hours' => $transitHours,
                                'transit_days' => round($transitHours / 24, 1),
                                'total_spk' => $spkCount,
                                'spk_list' => array_column($spksInManifest, 'spk_number'),
                                'notes' => $m->notes ?? '-',
                            ];
                        }
                    }
                }

                $manifestsList[] = [
                    'manifest_number' => $m->manifest_number,
                    'status' => $m->status,
                    'dispatcher' => $m->dispatcher_name ?? '-',
                    'receiver' => $m->receiver_name ?? '-',
                    'total_spk' => $spkCount,
                    'dispatched_at' => $dispatchedCarbon ? $dispatchedCarbon->format('d M Y, H:i') : null,
                    'received_at' => $receivedCarbon ? $receivedCarbon->format('d M Y, H:i') : null,
                    'transit_hours' => $transitHours,
                    'is_stuck_in_transit' => $isStuck,
                    'spks' => array_slice($spksInManifest, 0, 5),
                ];
            }

            // In addition, check SPKs with status OTW_WORKSHOP
            $otwWorkOrders = WorkOrder::where('status', 'OTW_WORKSHOP')
                ->get(['id', 'spk_number', 'customer_name', 'workshop_manifest_id', 'created_at', 'waktu'])
                ->map(fn($o) => [
                    'spk_number' => $o->spk_number,
                    'customer' => $o->customer_name,
                    'manifest_id' => $o->workshop_manifest_id,
                    'time' => $o->waktu ?? $o->created_at?->format('d M Y, H:i'),
                ])->toArray();

            $result['inbound_manifests'] = [
                'total_manifests' => count($manifestsList),
                'total_spk_inbound' => $totalInboundSpk,
                'status_breakdown' => [
                    'received' => $receivedCount,
                    'sent' => $sentCount,
                ],
                'stuck_in_transit_alerts' => [
                    'count' => count($stuckInTransitList),
                    'is_critical' => count($stuckInTransitList) > 0,
                    'items' => $stuckInTransitList,
                ],
                'otw_workshop_spks' => [
                    'count' => count($otwWorkOrders),
                    'items' => $otwWorkOrders,
                ],
                'recent_manifests' => array_slice($manifestsList, 0, 8),
            ];
        }

        // 2. Outbound Shipping & Kurir Resi
        if ($mode === 'all' || $mode === 'outbound_shipping') {
            $deliveryOrders = DB::table('work_orders')
                ->leftJoin('shippings', 'work_orders.spk_number', '=', 'shippings.spk_number')
                ->where(function($q) {
                    $q->where('work_orders.pickup_method', 'like', '%Express%')
                      ->orWhere('work_orders.pickup_method', 'delivery')
                      ->orWhereIn('work_orders.shipping_type', ['Ekspedisi', 'Online'])
                      ->orWhereNotNull('shippings.id')
                      ->orWhere('work_orders.shipping_cost', '>', 0);
                })
                ->select(
                    'work_orders.id',
                    'work_orders.spk_number',
                    'work_orders.customer_name',
                    'work_orders.status',
                    'work_orders.pickup_method',
                    'work_orders.shipping_type',
                    'work_orders.customer_tracking_number',
                    'work_orders.customer_shipped_at',
                    'work_orders.updated_at',
                    'shippings.id as shipping_id',
                    'shippings.ekspedisi',
                    'shippings.resi_pengiriman',
                    'shippings.is_verified',
                    'shippings.tanggal_pengiriman'
                )
                ->get();

            $pendingResiList = [];
            $verifiedShippedList = [];

            foreach ($deliveryOrders as $d) {
                $trackingNo = !empty($d->customer_tracking_number) ? $d->customer_tracking_number : (!empty($d->resi_pengiriman) ? $d->resi_pengiriman : null);
                $isVerified = ($d->is_verified == 1);
                $courier = !empty($d->ekspedisi) ? $d->ekspedisi : (!empty($d->pickup_method) && $d->pickup_method !== 'delivery' ? $d->pickup_method : 'Ekspedisi');

                $orderData = [
                    'spk_number' => $d->spk_number,
                    'customer_name' => $d->customer_name,
                    'status' => $d->status,
                    'courier' => $courier,
                    'tracking_number' => $trackingNo,
                    'is_verified' => $isVerified,
                    'shipped_at' => $d->tanggal_pengiriman ?? $d->customer_shipped_at,
                ];

                if (in_array($d->status, ['SELESAI', 'DIANTAR']) && (empty($trackingNo) || !$isVerified)) {
                    $pendingResiList[] = $orderData;
                } elseif (!empty($trackingNo)) {
                    $verifiedShippedList[] = $orderData;
                }
            }

            $result['outbound_shipping'] = [
                'total_delivery_orders' => $deliveryOrders->count(),
                'pending_resi_or_pickup' => [
                    'count' => count($pendingResiList),
                    'is_alert' => count($pendingResiList) > 0,
                    'items' => $pendingResiList,
                ],
                'verified_shipped' => [
                    'count' => count($verifiedShippedList),
                    'items' => array_slice($verifiedShippedList, 0, 5),
                ],
            ];
        }

        // 3. Financial Shipping Cost & Subsidy Audit
        if ($mode === 'all' || $mode === 'shipping_cost_audit') {
            $costOrders = DB::table('work_orders')
                ->where(function($q) {
                    $q->where('shipping_cost', '>', 0)
                      ->orWhere('actual_shipping_cost', '>', 0);
                })
                ->select('id', 'spk_number', 'customer_name', 'status', 'shipping_type', 'pickup_method', 'shipping_cost', 'actual_shipping_cost', 'created_at')
                ->get();

            $totalCustomerPaid = 0;
            $totalActualCost = 0;
            $totalSubsidy = 0;
            $totalSurplus = 0;
            $subsidizedList = [];

            foreach ($costOrders as $co) {
                $cPaid = (float) ($co->shipping_cost ?? 0);
                $aCost = (float) ($co->actual_shipping_cost ?? 0);

                $totalCustomerPaid += $cPaid;
                $totalActualCost += $aCost;

                $diff = $cPaid - $aCost;
                if ($diff < 0) {
                    $subAmount = abs($diff);
                    $totalSubsidy += $subAmount;
                    $subsidizedList[] = [
                        'spk_number' => $co->spk_number,
                        'customer_name' => $co->customer_name,
                        'customer_paid' => $cPaid,
                        'actual_cost' => $aCost,
                        'subsidy_amount' => $subAmount,
                        'pickup_method' => $co->pickup_method ?? $co->shipping_type ?? 'Ekspedisi',
                    ];
                } elseif ($diff > 0 && $aCost > 0) {
                    $totalSurplus += $diff;
                }
            }

            $netMargin = $totalCustomerPaid - $totalActualCost;

            $result['shipping_cost_audit'] = [
                'total_orders_audited' => $costOrders->count(),
                'total_customer_paid' => $totalCustomerPaid,
                'total_actual_cost' => $totalActualCost,
                'total_workshop_subsidy' => $totalSubsidy,
                'total_surplus' => $totalSurplus,
                'net_shipping_margin' => $netMargin,
                'has_subsidy_loss' => $totalSubsidy > 0,
                'subsidized_orders' => $subsidizedList,
            ];
        }

        // 4. Internal Transfer Surat Jalan (Sortir -> Produksi -> QC)
        if ($mode === 'all' || $mode === 'internal_transfer') {
            $sjQuery = DB::table('surat_jalan')
                ->leftJoin('users as d', 'surat_jalan.pengirim_id', '=', 'd.id')
                ->leftJoin('users as r', 'surat_jalan.penerima_id', '=', 'r.id')
                ->select(
                    'surat_jalan.id',
                    'surat_jalan.nomor_surat',
                    'surat_jalan.jenis_serah_terima',
                    'surat_jalan.status',
                    'surat_jalan.catatan',
                    'surat_jalan.dikirim_at',
                    'surat_jalan.diterima_at',
                    'd.name as pengirim_name',
                    'r.name as penerima_name'
                );

            if (!empty($identifier) && (str_starts_with($identifier, 'SJ') || is_numeric($identifier))) {
                $sjQuery->where(function($q) use ($identifier) {
                    $q->where('surat_jalan.nomor_surat', 'like', "%{$identifier}%")
                      ->orWhere('surat_jalan.id', $identifier);
                });
            }

            $suratJalans = $sjQuery->latest('surat_jalan.created_at')->take(15)->get();

            $sjList = [];
            $abnormalitiesList = [];
            $totalItemsChecked = 0;

            foreach ($suratJalans as $sj) {
                $items = DB::table('surat_jalan_items')
                    ->join('work_orders', 'surat_jalan_items.work_order_id', '=', 'work_orders.id')
                    ->where('surat_jalan_items.surat_jalan_id', $sj->id)
                    ->select(
                        'surat_jalan_items.id',
                        'surat_jalan_items.kondisi_serah_terima',
                        'work_orders.spk_number',
                        'work_orders.customer_name',
                        'work_orders.shoe_brand'
                    )
                    ->get();

                $totalItemsChecked += $items->count();

                $abnormalInThisSj = [];
                foreach ($items as $it) {
                    $cond = trim($it->kondisi_serah_terima ?? '');
                    if (!empty($cond) && !str_contains(strtolower($cond), 'baik') && !str_contains(strtolower($cond), 'sesuai')) {
                        $abnormalItem = [
                            'nomor_surat' => $sj->nomor_surat,
                            'jenis' => $sj->jenis_serah_terima,
                            'spk_number' => $it->spk_number,
                            'customer' => $it->customer_name,
                            'shoe' => $it->shoe_brand ?? 'Sepatu',
                            'kondisi' => $cond,
                        ];
                        $abnormalInThisSj[] = $abnormalItem;
                        $abnormalitiesList[] = $abnormalItem;
                    }
                }

                $sjList[] = [
                    'nomor_surat' => $sj->nomor_surat,
                    'jenis_serah_terima' => $sj->jenis_serah_terima,
                    'status' => $sj->status,
                    'pengirim' => $sj->pengirim_name ?? '-',
                    'penerima' => $sj->penerima_name ?? '-',
                    'dikirim_at' => $sj->dikirim_at ? Carbon::parse($sj->dikirim_at)->format('d M Y, H:i') : null,
                    'diterima_at' => $sj->diterima_at ? Carbon::parse($sj->diterima_at)->format('d M Y, H:i') : null,
                    'total_items' => $items->count(),
                    'abnormal_count' => count($abnormalInThisSj),
                ];
            }

            $result['internal_transfer'] = [
                'total_surat_jalan' => count($sjList),
                'total_items_inspected' => $totalItemsChecked,
                'abnormal_conditions_detected' => [
                    'count' => count($abnormalitiesList),
                    'has_abnormalities' => count($abnormalitiesList) > 0,
                    'items' => $abnormalitiesList,
                ],
                'recent_surat_jalan' => array_slice($sjList, 0, 8),
            ];
        }

        return $result;
    }

    // ========================================
    // Helpers
    // ========================================

    /**
     * Helper to build compact card data for Livewire Blade rendering
     */
    public function buildCardData(WorkOrder $order): array
    {
        $photo = $order->photos()->whereIn('step', ['BEFORE', 'AFTER', 'INTAKE'])->first();
        $photoUrl = $photo ? asset('storage/' . $photo->file_path) : null;

        return [
            'id' => $order->id,
            'spk_number' => $order->spk_number,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone ?? $order->customer?->phone ?? '-',
            'shoe_brand' => $order->shoe_brand ?? 'Sepatu',
            'shoe_color' => $order->shoe_color ?? '-',
            'status' => $order->status ? str_replace('_', ' ', $order->status->value) : '-',
            'status_raw' => $order->status?->value ?? '',
            'current_location' => $order->current_location ?? 'Workshop',
            'rack' => $order->storageAssignments()->where('status', 'stored')->latest()->first()?->rack_code 
                ?? $order->storageAssignments()->latest()->first()?->rack_code 
                ?? null,
            'estimation_date' => $order->estimation_date?->format('d M Y') ?? null,
            'photo_url' => $photoUrl,
            'url' => route('admin.orders.show', $order->id),
        ];
    }
}

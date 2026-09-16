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
     * @return array
     */
    public function chat(string $userMessage, array $chatHistory = [], ?int $contextOrderId = null): array
    {
        // If no API keys configured at all, guide user to Groq AI
        if (empty($this->apiKeys)) {
            Log::info("No Gemini API keys configured.");
            return [
                'success' => true,
                'quota_exceeded' => true,
                'source' => 'gemini',
                'text' => "⚠️ Kunci API Google Gemini belum dikonfigurasi di `.env`.\n\nSilakan aktifkan **⚡ Groq AI** melalui tombol di bawah untuk asisten AI bengkel berkecepatan tinggi.",
                'cards' => [],
                'timeline' => null,
                'cover_photo' => null,
                'photos' => null,
                'is_truncated' => false,
            ];
        }

        // Candidate model pool for automatic fallback on 429 rate limits or timeouts
        // Prioritize 15 RPM models (flash-lite) to avoid premature rate limits
        $modelPool = array_values(array_unique([
            'gemini-3.5-flash-lite',
            'gemini-3.1-flash-lite',
            'gemini-3.5-flash',
            $this->model,
            'gemini-3.8-flash',
            'gemini-flash-lite-latest',
        ]));

        $lastError = '';

        // Multi-Key Rotation: iterate through available API keys
        foreach ($this->apiKeys as $keyIndex => $currentApiKey) {
            $this->apiKey = $currentApiKey;

            foreach ($modelPool as $activeModel) {
                $result = $this->attemptChatWithModel($activeModel, $userMessage, $chatHistory, $contextOrderId);
                if ($result['success']) {
                    $result['source'] = 'gemini';
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
            . "- Keuangan (tagihan, pembayaran, piutang) → `get_financial_summary`\n"
            . "- Beban kerja teknisi, performa → `get_technician_analytics`\n"
            . "- Revisi, garansi, kerugian → `get_revision_warranty_data`\n"
            . "- Kendala pelanggan, keluhan (Complaints), SPK bermasalah/tertunda, atau status kendala OPEN/RESOLVED → `get_cx_issues_data`\n"
            . "- Lokasi rak, gudang, pengiriman → `get_storage_logistics`\n"
            . "- OTO (penawaran tambahan) → `get_oto_data`\n"
            . "- Foto dokumentasi pengerjaan (before, after, referensi penerimaan, QC/produksi) → `get_work_order_photos`\n"
            . "- Navigasi sidebar, letak menu/fitur, rute URL (/path), hak akses/role, atau panduan cara penggunaan fitur sistem → `get_feature_navigation`\n"
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
            // Tool 5: Financial Summary
            [
                'name' => 'get_financial_summary',
                'description' => 'Mengambil ringkasan keuangan: total tagihan, total terbayar, sisa piutang, riwayat pembayaran. Bisa per-SPK (detail 1 order) atau aggregate seluruh SPK dalam periode tertentu.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'identifier' => [
                            'type' => 'STRING',
                            'description' => 'ID atau nomor SPK. Jika diisi, mengembalikan detail keuangan 1 SPK. Jika kosong, mengembalikan aggregate.',
                        ],
                        'period' => [
                            'type' => 'STRING',
                            'description' => 'Periode: "this_month", "last_month", "this_week", "custom". Default: "this_month".',
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
            // Tool 10: Storage & Logistics
            [
                'name' => 'get_storage_logistics',
                'description' => 'Mengambil data penyimpanan gudang: lokasi rak, durasi penyimpanan, overdue items, dan info pengiriman (surat jalan). Bisa per-SPK atau aggregate seluruh gudang.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'identifier' => [
                            'type' => 'STRING',
                            'description' => 'ID atau nomor SPK. Kosongkan untuk aggregate gudang.',
                        ],
                        'filter' => [
                            'type' => 'STRING',
                            'description' => 'Filter: "overdue", "stored", "all". Default: "all".',
                        ],
                    ],
                ],
            ],
            // Tool 11: OTO Data
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

            case 'get_oto_data':
                $funcResult = $this->executeGetOtoData($args);
                break;

            case 'get_feature_navigation':
                $funcResult = SystemNavigationMap::search(
                    $args['query'] ?? null,
                    $args['division'] ?? null
                );
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
                'rack_name' => $order->storageAssignments->first()?->rack?->name ?? 'Belum Masuk Rak',
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

        // Aggregate mode
        [$start, $end] = $this->resolvePeriodFilter(
            $args['period'] ?? null,
            $args['start_date'] ?? null,
            $args['end_date'] ?? null
        );

        $query = WorkOrder::whereBetween('created_at', [$start, $end]);

        $totalRevenue = (clone $query)->sum('total_transaksi');
        $totalCollected = (clone $query)->sum('total_paid');
        $totalOutstanding = (clone $query)->sum('sisa_tagihan');

        $byPaymentStatus = (clone $query)
            ->selectRaw("status_pembayaran, COUNT(*) as count")
            ->groupBy('status_pembayaran')
            ->pluck('count', 'status_pembayaran')
            ->toArray();

        $topUnpaid = (clone $query)
            ->where('sisa_tagihan', '>', 0)
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
            'mode' => 'aggregate',
            'period_range' => $start->format('d M Y') . ' - ' . $end->format('d M Y'),
            'total_revenue' => (float) $totalRevenue,
            'total_collected' => (float) $totalCollected,
            'total_outstanding' => (float) $totalOutstanding,
            'by_payment_status' => $byPaymentStatus,
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
        $filter = $args['filter'] ?? 'all';

        // Per-SPK mode
        if (!empty($identifier)) {
            $order = $this->findOrderByIdentifier($identifier);
            if (!$order) {
                return ['status' => 'not_found', 'message' => "SPK '{$identifier}' tidak ditemukan."];
            }

            $order->load(['storageAssignments.rack', 'suratJalans']);

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
                'pickup_method' => $order->pickup_method ?? '-',
                'shipping_type' => $order->shipping_type ?? '-',
                'tracking_number' => $order->customer_tracking_number ?? '-',
                'shipped_at' => $order->customer_shipped_at?->format('d M Y') ?? null,
                'assignments' => $assignments,
                'surat_jalans' => $suratJalans,
                'card' => $this->buildCardData($order),
            ];
        }

        // Aggregate mode
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

        return [
            'status' => 'success',
            'mode' => 'aggregate',
            'total_stored' => $totalStored,
            'total_overdue' => $totalOverdue,
            'by_rack' => $byRack,
            'overdue_items' => $overdueItems,
        ];
    }

    /**
     * Tool 11: OTO (On-The-Order) Data
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
            'rack' => $order->storageAssignments->first()?->rack?->name ?? null,
            'estimation_date' => $order->estimation_date?->format('d M Y') ?? null,
            'photo_url' => $photoUrl,
            'url' => route('admin.orders.show', $order->id),
        ];
    }
}

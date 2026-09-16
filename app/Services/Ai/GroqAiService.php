<?php

namespace App\Services\Ai;

use App\Models\WorkOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqAiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', '');
        $this->model = config('services.groq.model', 'qwen/qwen3.8-27b');
        $this->baseUrl = config('services.groq.base_url', 'https://api.groq.com/openai/v1');
    }

    /**
     * Process a chat message with Groq API and Tool Calling (OpenAI-compatible)
     *
     * @param string $userMessage
     * @param array $chatHistory
     * @param int|null $contextOrderId
     * @return array
     */
    public function chat(string $userMessage, array $chatHistory = [], ?int $contextOrderId = null): array
    {
        if (empty($this->apiKey)) {
            Log::warning("Groq API key not configured.");
            return [
                'success' => false,
                'source' => 'groq',
                'text' => '⚠️ Kunci API Groq belum dikonfigurasi. Silakan tambahkan `GROQ_API_KEY` di file `.env`.',
                'cards' => [],
                'timeline' => null,
                'cover_photo' => null,
                'photos' => null,
            ];
        }

        $contextHint = "";
        if ($contextOrderId) {
            $contextSpk = WorkOrder::find($contextOrderId);
            if ($contextSpk) {
                $contextHint = "Saat ini pengguna sedang membuka halaman SPK {$contextSpk->spk_number} (ID: #{$contextOrderId}, Customer: {$contextSpk->customer_name}). Jika pengguna bertanya tanpa menyebutkan nomor SPK, asumsikan pertanyaannya ditujukan untuk SPK {$contextSpk->spk_number}.\n";
            } else {
                $contextHint = "Saat ini pengguna membuka SPK ID #{$contextOrderId}. Jika pengguna bertanya tanpa menyebutkan nomor, asumsikan SPK ID #{$contextOrderId}.\n";
            }
        }

        $systemInstruction = $this->buildSystemInstruction($contextHint);
        $tools = $this->buildOpenAiToolDeclarations();

        // Prepare messages array (OpenAI format)
        $messages = [];
        $messages[] = ['role' => 'system', 'content' => $systemInstruction];

        // Add recent history (last 4 messages)
        $recentHistory = array_slice($chatHistory, -4);
        foreach ($recentHistory as $msg) {
            $role = ($msg['role'] ?? 'user') === 'user' ? 'user' : 'assistant';
            $messages[] = ['role' => $role, 'content' => $msg['content'] ?? ''];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $capturedCards = [];
        $capturedTimeline = null;
        $capturedCoverPhoto = null;
        $capturedPhotos = null;

        /** @var GeminiAiService $geminiService */
        $geminiService = app(GeminiAiService::class);

        try {
            $endpoint = rtrim($this->baseUrl, '/') . '/chat/completions';
            $maxRounds = 3;

            for ($round = 0; $round < $maxRounds; $round++) {
                $requestPayload = [
                    'model' => $this->model,
                    'messages' => $messages,
                    'tools' => $tools,
                    'tool_choice' => 'auto',
                    'temperature' => 0.2,
                    'max_tokens' => 400,
                    'parallel_tool_calls' => false,
                ];

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($endpoint, $requestPayload);

                if ($response->status() === 429) {
                    Log::warning("Groq API rate limited (429).");
                    return [
                        'success' => true,
                        'quota_exceeded' => true,
                        'source' => 'groq',
                        'text' => "⚠️ **Kuota Groq AI Sedang Mencapai Batas (Rate Limit 429)**\n\n"
                                . "Kunci API Groq sedang dibatasi kuota token per menit (TPM) pada paket gratis.\n\n"
                                . "⏳ **Kapan Dapat Digunakan Kembali?**\n"
                                . "- Kuota Groq dihitung secara *rolling per menit* dan **otomatis pulih dalam ~30 - 60 detik**.\n\n"
                                . "💡 *Saran: Anda dapat menunggu ~30-60 detik lalu klik **Coba Lagi**, atau beralih kembali ke **🌐 Gemini AI**.*",
                        'cards' => [],
                        'timeline' => null,
                        'cover_photo' => null,
                        'photos' => null,
                    ];
                }

                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMsg = $errorBody['error']['message'] ?? "HTTP Error {$response->status()}";
                    Log::error("Groq API error: {$errorMsg}");
                    return [
                        'success' => false,
                        'source' => 'groq',
                        'text' => "⚠️ Groq API error: {$errorMsg}",
                        'cards' => [],
                        'timeline' => null,
                        'cover_photo' => null,
                        'photos' => null,
                    ];
                }

                $responseData = $response->json();
                $choice = $responseData['choices'][0] ?? null;
                $message = $choice['message'] ?? [];
                $finishReason = $choice['finish_reason'] ?? 'stop';

                // Check for tool calls
                $toolCalls = $message['tool_calls'] ?? [];

                if (empty($toolCalls)) {
                    // No tool calls — extract text response
                    $text = trim($message['content'] ?? '');
                    $isTruncated = ($finishReason === 'length');

                    if ($isTruncated) {
                        $text = rtrim($text, " \t\n\r\0\x0B([*>#-_:");
                    }

                    if (empty($text)) {
                        $text = !empty($capturedCards)
                            ? "Berikut data SPK yang ditemukan dari sistem bengkel:"
                            : "Halo! Ada yang bisa saya bantu terkait pesanan, riwayat pengerjaan, atau analitik bengkel? 🔧";
                    }

                    return [
                        'success' => true,
                        'source' => 'groq',
                        'text' => $text,
                        'cards' => array_values(collect($capturedCards)->unique('id')->take(5)->toArray()),
                        'timeline' => $capturedTimeline,
                        'cover_photo' => $capturedCoverPhoto,
                        'photos' => $capturedPhotos,
                        'is_truncated' => $isTruncated,
                    ];
                }

                // Process tool calls — add assistant message with tool_calls
                $messages[] = $message;

                foreach ($toolCalls as $call) {
                    $funcName = $call['function']['name'] ?? '';
                    $argsJson = $call['function']['arguments'] ?? '{}';
                    $toolCallId = $call['id'] ?? '';

                    $args = json_decode($argsJson, true) ?? [];

                    // Dispatch using GeminiAiService's shared tool executor
                    $funcResult = $geminiService->dispatchToolCall($funcName, $args, $capturedCards, $capturedTimeline, $capturedCoverPhoto, $capturedPhotos);

                    // Sanitize result for Groq context to minimize tokens (remove UI-heavy card objects)
                    $compactResult = $funcResult;
                    if (isset($compactResult['results']) && is_array($compactResult['results'])) {
                        foreach ($compactResult['results'] as &$item) {
                            unset($item['card']);
                        }
                        unset($item);
                    }

                    // Add tool response in OpenAI format
                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $toolCallId,
                        'content' => json_encode($compactResult, JSON_UNESCAPED_UNICODE),
                    ];
                }

                // Immediately request final synthesis response without tools to keep payload ultra-lean
                $finalPayload = [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.2,
                    'max_tokens' => 1000,
                ];

                $finalResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($endpoint, $finalPayload);

                if ($finalResponse->status() === 429) {
                    Log::warning("Groq API rate limited (429) during synthesis.");
                    return [
                        'success' => true,
                        'quota_exceeded' => true,
                        'source' => 'groq',
                        'text' => "⚠️ **Kuota Groq AI Sedang Mencapai Batas (Rate Limit 429)**\n\n"
                                . "Kunci API Groq sedang dibatasi kuota token per menit (TPM) pada paket gratis.\n\n"
                                . "⏳ **Kapan Dapat Digunakan Kembali?**\n"
                                . "- Kuota Groq dihitung secara *rolling per menit* dan **otomatis pulih dalam ~30 - 60 detik**.\n\n"
                                . "💡 *Saran: Anda dapat menunggu ~30-60 detik lalu klik **Coba Lagi**, atau beralih kembali ke **🌐 Gemini AI**.*",
                        'cards' => array_values(collect($capturedCards)->unique('id')->take(5)->toArray()),
                        'timeline' => $capturedTimeline,
                        'cover_photo' => $capturedCoverPhoto,
                        'photos' => $capturedPhotos,
                    ];
                }

                if ($finalResponse->successful()) {
                    $finalData = $finalResponse->json();
                    $finalChoice = $finalData['choices'][0] ?? null;
                    $finalMessage = $finalChoice['message'] ?? [];
                    $finalFinishReason = $finalChoice['finish_reason'] ?? 'stop';
                    $isTruncated = ($finalFinishReason === 'length');

                    $finalText = trim($finalMessage['content'] ?? '');
                    if ($isTruncated) {
                        $finalText = rtrim($finalText, " \t\n\r\0\x0B([*>#-_:");
                    }

                    if (empty($finalText)) {
                        $finalText = !empty($capturedCards)
                            ? "Berikut data yang ditemukan dari sistem bengkel:"
                            : "Maaf, saya tidak berhasil memproses jawaban. Silakan coba lagi.";
                    }

                    return [
                        'success' => true,
                        'source' => 'groq',
                        'text' => $finalText,
                        'cards' => array_values(collect($capturedCards)->unique('id')->take(5)->toArray()),
                        'timeline' => $capturedTimeline,
                        'cover_photo' => $capturedCoverPhoto,
                        'photos' => $capturedPhotos,
                        'is_truncated' => $isTruncated,
                    ];
                }
            }

            return [
                'success' => false,
                'source' => 'groq',
                'text' => 'Gagal mendapatkan respons dari Groq AI setelah beberapa percobaan.',
                'cards' => array_values(collect($capturedCards)->unique('id')->take(5)->toArray()),
                'timeline' => $capturedTimeline,
                'cover_photo' => $capturedCoverPhoto,
                'photos' => $capturedPhotos,
            ];

        } catch (\Throwable $e) {
            Log::error("Groq AI Exception: " . $e->getMessage());
            return [
                'success' => false,
                'source' => 'groq',
                'text' => 'Terjadi kendala saat menghubungi Groq AI: ' . $e->getMessage(),
                'cards' => [],
                'timeline' => null,
                'cover_photo' => null,
                'photos' => null,
            ];
        }
    }

    /**
     * Build the system instruction (100% equivalent to Gemini)
     */
    protected function buildSystemInstruction(string $contextHint = ''): string
    {
        return "Kamu adalah **Workshop AI Copilot**, asisten pintar resmi sistem bengkel sepatu ShoeWorkshop.\n"
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
            . "## Format Penyajian Keuangan & Invoice:\n"
            . "Ketika pengguna menanyakan rincian biaya, tagihan, atau invoice:\n"
            . "1. Sebutkan nomor SPK dan nama customer di awal.\n"
            . "2. Buat section `### 🧾 Rincian Layanan Jasa` yang merinci setiap jasa yang diambil beserta biaya satu per satu, teknisi penanggung jawab, dan total biaya jasa.\n"
            . "3. Buat section `### 💰 Status Invoice & Pembayaran` yang merinci nomor invoice, total tagihan invoice, jumlah terbayar, sisa piutang/kurang bayar, dan status (Lunas / Belum Lunas / DP).\n"
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
            . "JANGAN mengulang semua log satu per satu tanpa filter, tetapi rangkum intisari perjalanannya agar cepat dan padat informasi bagi admin workshop.\n"
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
            . "## Aturan Pemunculan Kartu SPK (Card):\n"
            . "- Set `show_cards = true` pada tool `search_work_orders` jika pengguna mencari atau ingin melihat SPK.\n"
            . "\n"
            . "## 🚫 Aturan Anti-Halusinasi & Integritas Data (MUTLAK):\n"
            . "- HANYA jawab berdasarkan data aktual yang dikembalikan oleh tool sistem bengkel.\n"
            . "- JIKA PENCARIAN KOSONG / TIDAK DITEMUKAN: Nyatakan dengan jujur dan sopan bahwa SPK/data tidak ditemukan. DILARANG KERAS MENGARANG nomor SPK fiktif, contoh palsu, atau nama rekaan seperti John Doe yang tidak ada dalam hasil pencarian database!\n"
            . "\n"
            . "Untuk pertanyaan kompleks, kamu BOLEH memanggil beberapa tool sekaligus untuk cross-reference data.\n"
            . "Saat menjawab analytics/statistik, format jawaban dengan bullet points dan angka yang jelas.\n"
            . $contextHint;
    }

    /**
     * Build OpenAI-compatible tool declarations from the same 12 tools
     */
    protected function buildOpenAiToolDeclarations(): array
    {
        return [
            $this->makeOpenAiTool('search_work_orders', 'Mencari daftar SPK (Work Order) berdasarkan nomor SPK, nama pelanggan, nomor telepon, atau nomor invoice.', [
                'keyword' => ['type' => 'string', 'description' => 'Kata kunci pencarian (nomor SPK, nama pelanggan, no telepon)'],
                'show_cards' => ['type' => 'boolean', 'description' => 'Set TRUE jika pengguna meminta menampilkan kartu daftar SPK. Set FALSE jika menanyakan info spesifik.'],
            ], ['keyword']),

            $this->makeOpenAiTool('get_work_order_detail', 'Mengambil rincian lengkap satu SPK: pelanggan, sepatu, layanan jasa, invoice/pembayaran, lokasi rak, teknisi.', [
                'identifier' => ['type' => 'string', 'description' => 'ID numerik SPK (misal 18) atau format nomor SPK (misal SPK-2026-0018)'],
            ], ['identifier']),

            $this->makeOpenAiTool('get_work_order_timeline', 'Mengambil kronologi riwayat aktivitas teknisi, log audit, dan riwayat perpindahan status SPK.', [
                'identifier' => ['type' => 'string', 'description' => 'ID numerik SPK atau format nomor SPK'],
            ], ['identifier']),

            $this->makeOpenAiTool('get_spk_overview_stats', 'Mengambil statistik ringkasan SPK: total SPK, breakdown per status, overdue, fast-track, SPK baru hari ini.', [
                'period' => ['type' => 'string', 'description' => 'Periode: "this_month", "last_month", "this_week", "today", "custom". Default: "this_month".'],
                'start_date' => ['type' => 'string', 'description' => 'YYYY-MM-DD. Hanya jika period = "custom".'],
                'end_date' => ['type' => 'string', 'description' => 'YYYY-MM-DD. Hanya jika period = "custom".'],
            ]),

            $this->makeOpenAiTool('get_financial_summary', 'Mengambil ringkasan keuangan: total tagihan, total terbayar, sisa piutang, riwayat pembayaran.', [
                'identifier' => ['type' => 'string', 'description' => 'ID/nomor SPK untuk detail 1 SPK. Kosongkan untuk aggregate.'],
                'period' => ['type' => 'string', 'description' => 'Periode waktu. Default: "this_month".'],
                'start_date' => ['type' => 'string', 'description' => 'YYYY-MM-DD.'],
                'end_date' => ['type' => 'string', 'description' => 'YYYY-MM-DD.'],
            ]),

            $this->makeOpenAiTool('get_production_tracking', 'Mengambil progress detail sub-stasiun pengerjaan SPK, riwayat perpindahan status, durasi per status, kendala CX, dan deteksi bottleneck.', [
                'identifier' => ['type' => 'string', 'description' => 'ID atau nomor SPK.'],
            ], ['identifier']),

            $this->makeOpenAiTool('get_technician_analytics', 'Mengambil analitik performa teknisi: jumlah SPK ditangani, beban kerja aktif, rata-rata waktu pengerjaan.', [
                'technician_name' => ['type' => 'string', 'description' => 'Nama teknisi untuk filter. Kosongkan untuk semua.'],
                'period' => ['type' => 'string', 'description' => 'Periode. Default: "this_month".'],
                'start_date' => ['type' => 'string', 'description' => 'YYYY-MM-DD.'],
                'end_date' => ['type' => 'string', 'description' => 'YYYY-MM-DD.'],
            ]),

            $this->makeOpenAiTool('get_revision_warranty_data', 'Mengambil data revisi dan garansi: detail revisi per SPK, klaim garansi, kerugian.', [
                'identifier' => ['type' => 'string', 'description' => 'ID/nomor SPK. Kosongkan untuk aggregate.'],
                'period' => ['type' => 'string', 'description' => 'Periode.'],
                'start_date' => ['type' => 'string', 'description' => 'YYYY-MM-DD.'],
                'end_date' => ['type' => 'string', 'description' => 'YYYY-MM-DD.'],
            ]),

            $this->makeOpenAiTool('get_cx_issues_data', 'Mengambil data kendala operasional/pelanggan (CX Issues) dan keluhan (Complaints).', [
                'identifier' => ['type' => 'string', 'description' => 'ID/nomor SPK. Kosongkan untuk aggregate.'],
                'status_filter' => ['type' => 'string', 'description' => 'Filter status: "OPEN", "RESOLVED", "ALL". Default: "ALL".'],
                'period' => ['type' => 'string', 'description' => 'Periode.'],
                'start_date' => ['type' => 'string', 'description' => 'YYYY-MM-DD.'],
                'end_date' => ['type' => 'string', 'description' => 'YYYY-MM-DD.'],
            ]),

            $this->makeOpenAiTool('get_storage_logistics', 'Mengambil data penyimpanan gudang: lokasi rak, overdue items, info pengiriman.', [
                'identifier' => ['type' => 'string', 'description' => 'ID/nomor SPK. Kosongkan untuk aggregate gudang.'],
                'filter' => ['type' => 'string', 'description' => 'Filter: "overdue", "stored", "all". Default: "all".'],
            ]),

            $this->makeOpenAiTool('get_oto_data', 'Mengambil data OTO (On-The-Order / penawaran jasa tambahan).', [
                'identifier' => ['type' => 'string', 'description' => 'ID/nomor SPK. Kosongkan untuk aggregate.'],
                'status_filter' => ['type' => 'string', 'description' => 'Filter: "PENDING_CX", "APPROVED", "COMPLETED", "ALL". Default: "ALL".'],
                'period' => ['type' => 'string', 'description' => 'Periode.'],
                'start_date' => ['type' => 'string', 'description' => 'YYYY-MM-DD.'],
                'end_date' => ['type' => 'string', 'description' => 'YYYY-MM-DD.'],
            ]),

            $this->makeOpenAiTool('get_work_order_photos', 'Mengambil galeri foto dokumentasi pengerjaan SPK: before, after, referensi, produksi/QC.', [
                'identifier' => ['type' => 'string', 'description' => 'ID numerik SPK atau format nomor SPK.'],
                'category' => ['type' => 'string', 'description' => 'Kategori foto: "all", "before", "after", "reference", "production". Default: "all".'],
            ], ['identifier']),

            $this->makeOpenAiTool('get_feature_navigation', 'Mencari dan menjelaskan fitur sistem bengkel, letak menu pada navigasi sidebar, rute URL (/path), hak akses/role, dan panduan langkah penggunaan fitur.', [
                'query' => ['type' => 'string', 'description' => 'Nama fitur, menu sidebar, rute, atau kata kunci (misal: "laporan performa", "penerimaan", "kasir", "overdue sla", "rak", "promo").'],
                'division' => ['type' => 'string', 'description' => 'Filter spesifik divisi jika ada: "cs", "warehouse", "workshop", "finance", "cx", "admin", "public", "general". Kosongkan jika mencari global.'],
            ]),
        ];
    }

    /**
     * Helper: build a single OpenAI-compatible tool declaration
     */
    protected function makeOpenAiTool(string $name, string $description, array $properties, array $required = []): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $name,
                'description' => $description,
                'parameters' => [
                    'type' => 'object',
                    'properties' => $properties,
                    'required' => $required,
                ],
            ],
        ];
    }
}

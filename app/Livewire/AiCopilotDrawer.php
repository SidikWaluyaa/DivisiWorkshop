<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WorkOrder;
use App\Services\Ai\GeminiAiService;
use App\Services\Ai\GroqAiService;

class AiCopilotDrawer extends Component
{
    public bool $isOpen = false;
    public string $currentInput = '';
    public array $messages = [];
    public bool $isLoading = false;
    public ?int $contextOrderId = null;
    public ?string $contextSpkNumber = null;
    public ?string $contextCustomerName = null;
    public bool $useLocalEngine = false;
    public bool $isKpiPage = false;
    public bool $isWorkshopPage = false;
    public string $selectedModel = 'gemini-3.5-flash-lite';
    public bool $showModelDropdown = false;

    protected $listeners = [
        'openAiCopilot' => 'handleOpenAiCopilot',
    ];

    public function getModelOptionsProperty(): array
    {
        return [
            'gemini-3.5-flash-lite' => [
                'name' => 'Gemini 3.5 Flash Lite',
                'short_name' => '🌐 3.5 Flash Lite',
                'category' => '🚀 Kuota Lega (Rekomendasi)',
                'quota' => '500 RPD • 15 RPM',
                'badge' => 'Prioritas',
                'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'desc' => 'Paling stabil, responsif, dan kuota harian besar (500 RPD).',
            ],
            'gemini-3.1-flash-lite' => [
                'name' => 'Gemini 3.1 Flash Lite',
                'short_name' => '🌐 3.1 Flash Lite',
                'category' => '🚀 Kuota Lega (Rekomendasi)',
                'quota' => '500 RPD • 15 RPM',
                'badge' => 'Cepat',
                'badge_color' => 'bg-teal-100 text-teal-800 border-teal-200',
                'desc' => 'Model lite alternatif dengan kecepatan tinggi dan kuota 500 RPD.',
            ],
            'gemini-3.5-flash' => [
                'name' => 'Gemini 3.5 Flash',
                'short_name' => '🧠 3.5 Flash',
                'category' => '🧠 Akurasi Tinggi & Analitik',
                'quota' => '20 RPD • 5 RPM',
                'badge' => 'Smart',
                'badge_color' => 'bg-blue-100 text-blue-800 border-blue-200',
                'desc' => 'Kapasitas penalaran tinggi untuk analitik kompleks.',
            ],
            'gemini-3.8-flash' => [
                'name' => 'Gemini 3.8 Flash',
                'short_name' => '🧠 3.8 Flash',
                'category' => '🧠 Akurasi Tinggi & Analitik',
                'quota' => '20 RPD • 5 RPM',
                'badge' => 'Terbaru',
                'badge_color' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                'desc' => 'Versi flash termutakhir dengan analisis penalaran mendalam.',
            ],
            'gemini-3.6-flash' => [
                'name' => 'Gemini 3.6 Flash',
                'short_name' => '🧠 3.6 Flash',
                'category' => '🧠 Akurasi Tinggi & Analitik',
                'quota' => '20 RPD • 5 RPM',
                'badge' => 'Alternatif',
                'badge_color' => 'bg-slate-100 text-slate-800 border-slate-200',
                'desc' => 'Model flash generasi 3.6 untuk cadangan cerdas.',
            ],
            'gemini-3.7-flash' => [
                'name' => 'Gemini 3.7 Flash',
                'short_name' => '🧠 3.7 Flash',
                'category' => '🧠 Akurasi Tinggi & Analitik',
                'quota' => '20 RPD • 5 RPM',
                'badge' => 'Alternatif',
                'badge_color' => 'bg-slate-100 text-slate-800 border-slate-200',
                'desc' => 'Model flash generasi 3.7 untuk cadangan cerdas.',
            ],
            'groq-qwen' => [
                'name' => '⚡ Groq AI (Qwen 27B)',
                'short_name' => '⚡ Groq Qwen',
                'category' => '⚡ Cadangan Eksternal',
                'quota' => 'Ultra Fast • Token Rolling',
                'badge' => 'Ultra Cepat',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-200',
                'desc' => 'Inference ultra-cepat (~1s) dari Groq Cloud untuk beban darurat.',
            ],
        ];
    }

    public function getActiveModelLabelProperty(): string
    {
        if ($this->useLocalEngine) {
            return '⚡ Groq Qwen';
        }
        $options = $this->modelOptions;
        return $options[$this->selectedModel]['short_name'] ?? '🌐 Gemini AI';
    }

    public function toggleModelDropdown()
    {
        $this->showModelDropdown = !$this->showModelDropdown;
    }

    public function closeModelDropdown()
    {
        $this->showModelDropdown = false;
    }

    public function selectModel(string $modelKey)
    {
        $this->showModelDropdown = false;
        $options = $this->modelOptions;

        if ($modelKey === 'groq-qwen') {
            $this->useLocalEngine = true;
            $this->selectedModel = 'groq-qwen';
            $label = '⚡ Groq AI (Qwen 27B)';
        } else {
            $this->useLocalEngine = false;
            $this->selectedModel = $modelKey;
            $label = $options[$modelKey]['name'] ?? $modelKey;
        }

        $this->messages[] = [
            'role' => 'model',
            'content' => "🤖 Model AI Copilot berhasil dialihkan ke **{$label}**.\n\nSistem siap memproses pertanyaan Anda menggunakan model ini. Jika model ini sewaktu-waktu mencapai kuota limit 429, sistem akan secara otomatis melakukan failover ke model cadangan lainnya.",
            'cards' => [],
            'timeline' => null,
            'cover_photo' => null,
            'photos' => null,
            'time' => now()->format('H:i'),
        ];

        $this->dispatch('scroll-ai-chat-bottom');
    }

    public function toggleEngineMode(?bool $forceMode = null)
    {
        if ($forceMode !== null) {
            $this->useLocalEngine = $forceMode;
        } else {
            $this->useLocalEngine = !$this->useLocalEngine;
        }

        if ($this->useLocalEngine) {
            $this->selectedModel = 'groq-qwen';
        } else {
            if ($this->selectedModel === 'groq-qwen') {
                $this->selectedModel = 'gemini-3.5-flash-lite';
            }
        }

        $modeName = $this->useLocalEngine ? 'Mode Cadangan (⚡ Groq AI)' : 'Cloud AI (🌐 Google Gemini)';
        $icon = $this->useLocalEngine ? '⚡' : '🌐';

        $this->messages[] = [
            'role' => 'model',
            'content' => "{$icon} Beralih ke **{$modeName}**.\n\n" . ($this->useLocalEngine 
                ? "Sekarang Anda terhubung ke **Groq Cloud AI** dengan model Qwen 27B & Function Calling terintegrasi. Anda mendapatkan respon super cepat (~1-2 detik) dengan akses database lengkap yang setara dengan Gemini untuk melacak SPK, timeline, foto, rak, kendala operasional, dan analitik KPI Finance." 
                : "Sekarang Anda kembali menggunakan komputasi kecerdasan **Google Gemini Cloud AI**."),
            'cards' => [],
            'timeline' => null,
            'cover_photo' => null,
            'photos' => null,
            'is_truncated' => false,
            'quota_exceeded' => false,
            'source' => $this->useLocalEngine ? 'groq' : 'gemini',
            'time' => now()->format('H:i'),
        ];

        $this->dispatch('scroll-ai-chat-bottom');
    }

    public function getUserInitialsProperty(): string
    {
        $name = auth()->user()?->name ?? 'User';
        $words = preg_split('/\s+/', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    public function getUserRoleProperty(): string
    {
        return auth()->user()?->role ?? 'Staff';
    }

    public function mount(?int $orderId = null)
    {
        // Auto-detect KPI page context
        $this->isKpiPage = request()->is('admin/kpi*') || request()->is('kpi*');

        // Auto-detect Workshop page context
        $this->isWorkshopPage = request()->is('admin/production*') || request()->is('admin/qc*') 
            || request()->is('admin/preparation*') || request()->is('admin/sortir*') 
            || request()->is('workshop*') || request()->is('production*') 
            || request()->is('preparation*') || request()->is('qc*') || request()->is('sortir*');

        // Auto-detect context from route parameter if available
        $routeId = request()->route('id') ?? request()->route('order') ?? $orderId;
        if ($routeId && is_numeric($routeId)) {
            $this->setContextOrder((int) $routeId);
        }

        $this->initWelcomeMessage();
    }

    public function initWelcomeMessage()
    {
        $welcomeText = "Halo! Saya **Workshop AI Copilot** 🤖🛠️\n\nAda yang bisa saya bantu hari ini? Saya bisa membantu Anda melacak SPK, memantau antrean workshop & teknisi, menganalisis data keuangan & KPI Finance, mencari lokasi rak sepatu, atau mendeteksi SPK overdue.";
        
        if ($this->contextSpkNumber) {
            $welcomeText .= "\n\n💡 *Saya mendeteksi Anda sedang membuka SPK **{$this->contextSpkNumber}** ({$this->contextCustomerName}). Anda bisa klik tombol pintas di bawah untuk analisis instan!*";
        } elseif ($this->isWorkshopPage) {
            $welcomeText .= "\n\n⚙️ *Saya mendeteksi Anda sedang berada di area **Produksi Workshop**. Anda bisa menanyakan SPK overdue, antrean stasiun live, beban kerja teknisi, maupun deteksi bottleneck pengerjaan secara instan!*";
        } elseif ($this->isKpiPage) {
            $welcomeText .= "\n\n📊 *Saya mendeteksi Anda sedang berada di halaman **Dashboard KPI**. Anda bisa menanyakan ringkasan **KPI Workshop & Bottleneck**, **KPI Gudang & Logistik**, maupun **KPI Finance** secara instan!*";
        }

        $this->messages = [
            [
                'role' => 'model',
                'content' => $welcomeText,
                'cards' => [],
                'timeline' => null,
                'time' => now()->format('H:i'),
            ]
        ];
    }

    public function setContextOrder(int $orderId)
    {
        $this->contextOrderId = $orderId;
        $order = WorkOrder::find($orderId);
        if ($order) {
            $this->contextSpkNumber = $order->spk_number;
            $this->contextCustomerName = $order->customer_name;
        }
    }

    public function focusOrder(int $orderId)
    {
        $this->setContextOrder($orderId);

        $this->messages[] = [
            'role' => 'model',
            'content' => "🎯 **Fokus dialihkan ke SPK {$this->contextSpkNumber} ({$this->contextCustomerName})**.\n\nSekarang Anda dapat menanyakan rincian pengerjaan sepatu ini secara mendalam. Silakan pilih tombol pertanyaan di bawah atau ketik pertanyaan langsung!",
            'cards' => [],
            'timeline' => null,
            'time' => now()->format('H:i'),
        ];

        $this->dispatch('scroll-ai-chat-bottom');
    }

    public function clearContextOrder()
    {
        $this->contextOrderId = null;
        $this->contextSpkNumber = null;
        $this->contextCustomerName = null;

        $this->messages[] = [
            'role' => 'model',
            'content' => "Konteks SPK telah dilepas. Anda kembali ke mode pencarian umum. Ada yang bisa dibantu melacak pesanan lain?",
            'cards' => [],
            'timeline' => null,
            'time' => now()->format('H:i'),
        ];

        $this->dispatch('scroll-ai-chat-bottom');
    }

    public function handleOpenAiCopilot(?int $orderId = null)
    {
        if ($orderId) {
            $this->setContextOrder($orderId);
            $this->initWelcomeMessage();
        }
        $this->isOpen = true;
        $this->dispatch('ai-drawer-opened');
    }

    public function toggleDrawer()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->dispatch('ai-drawer-opened');
        }
    }

    public function closeDrawer()
    {
        $this->isOpen = false;
    }

    public function clearChat()
    {
        $this->initWelcomeMessage();
        $this->dispatch('scroll-ai-chat-bottom');
    }

    public ?string $pendingUserMessage = null;

    public function sendQuickPrompt(string $prompt)
    {
        $this->currentInput = $prompt;
        $this->submitMessage();
    }

    /**
     * Re-submit the last user question automatically
     */
    public function retryLastMessage()
    {
        $lastPrompt = null;
        for ($i = count($this->messages) - 1; $i >= 0; $i--) {
            if (($this->messages[$i]['role'] ?? '') === 'user') {
                $lastPrompt = $this->messages[$i]['content'] ?? null;
                break;
            }
        }

        if (!empty($lastPrompt)) {
            $this->currentInput = $lastPrompt;
            $this->submitMessage();
        }
    }

    /**
     * Phase 1: Fast submission (<50ms). Immediately clears input, puts message into chat room,
     * sets loading state, and triggers client to call Phase 2.
     */
    public function submitMessage()
    {
        $input = trim($this->currentInput);
        if (empty($input) || $this->isLoading) {
            return;
        }

        // Add user turn to chat history immediately
        $this->messages[] = [
            'role' => 'user',
            'content' => $input,
            'cards' => [],
            'timeline' => null,
            'time' => now()->format('H:i'),
        ];

        $this->pendingUserMessage = $input;
        $this->currentInput = '';
        $this->isLoading = true;

        $this->dispatch('scroll-ai-chat-bottom');
        $this->dispatch('trigger-ai-process');
    }

    /**
     * Phase 2: Asynchronous AI analysis. Called immediately after Phase 1 renders.
     */
    public function processAiResponse()
    {
        if (!$this->isLoading || empty($this->pendingUserMessage)) {
            $this->isLoading = false;
            return;
        }

        $input = $this->pendingUserMessage;
        $this->pendingUserMessage = null;

        try {
            // Format previous history for service (exclude current message)
            $historyForService = array_slice($this->messages, 0, -1);

            if ($this->useLocalEngine) {
                /** @var GroqAiService $groqService */
                $groqService = app(GroqAiService::class);
                $response = $groqService->chat($input, $historyForService, $this->contextOrderId);
            } else {
                /** @var GeminiAiService $aiService */
                $aiService = app(GeminiAiService::class);
                $response = $aiService->chat($input, $historyForService, $this->contextOrderId, $this->selectedModel);
            }

            // Suppress repetitive cards of currently focused SPK to keep room chat clean
            $cards = $response['cards'] ?? [];
            if ($this->contextOrderId && !empty($cards)) {
                $cards = array_values(array_filter($cards, fn($c) => ($c['id'] ?? null) !== $this->contextOrderId));
            }

            $responseText = $response['text'] ?? 'Maaf, saya tidak dapat merespons permintaan tersebut.';
            if (!empty($response['was_fallback'])) {
                $requestedName = $this->modelOptions[$response['requested_model']]['name'] ?? ($response['requested_model'] ?? 'sebelumnya');
                $usedName = $this->modelOptions[$response['used_model']]['name'] ?? ($response['used_model'] ?? 'cadangan');
                $responseText .= "\n\n> ⚠️ *Info Failover: Model **{$requestedName}** sedang mencapai batas limit (429). Sistem secara otomatis mengalihkan analisis ke **{$usedName}** agar respon tetap tersaji lancar.*";
                
                // Auto-sync selected model to current active fallback
                if (isset($this->modelOptions[$response['used_model']])) {
                    $this->selectedModel = $response['used_model'];
                }
            }

            $this->messages[] = [
                'role' => 'model',
                'content' => $responseText,
                'cards' => $cards,
                'timeline' => $response['timeline'] ?? null,
                'cover_photo' => $response['cover_photo'] ?? null,
                'photos' => $response['photos'] ?? null,
                'is_truncated' => !empty($response['is_truncated']),
                'quota_exceeded' => !empty($response['quota_exceeded']),
                'source' => $response['source'] ?? ($this->useLocalEngine ? 'groq' : 'gemini'),
                'time' => now()->format('H:i'),
            ];

        } catch (\Throwable $e) {
            $this->messages[] = [
                'role' => 'model',
                'content' => 'Terjadi kendala saat menganalisis data: ' . $e->getMessage(),
                'cards' => [],
                'timeline' => null,
                'cover_photo' => null,
                'photos' => null,
                'time' => now()->format('H:i'),
            ];
        } finally {
            $this->isLoading = false;
            $this->dispatch('scroll-ai-chat-bottom');
        }
    }

    public function render()
    {
        return view('livewire.ai-copilot-drawer');
    }
}

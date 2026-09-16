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

    protected $listeners = [
        'openAiCopilot' => 'handleOpenAiCopilot',
    ];

    public function toggleEngineMode(?bool $forceMode = null)
    {
        if ($forceMode !== null) {
            $this->useLocalEngine = $forceMode;
        } else {
            $this->useLocalEngine = !$this->useLocalEngine;
        }

        $modeName = $this->useLocalEngine ? 'Mode Cadangan (⚡ Groq AI)' : 'Cloud AI (🌐 Google Gemini)';
        $icon = $this->useLocalEngine ? '⚡' : '🌐';

        $this->messages[] = [
            'role' => 'model',
            'content' => "{$icon} Beralih ke **{$modeName}**.\n\n" . ($this->useLocalEngine 
                ? "Sekarang Anda terhubung ke **Groq Cloud AI** dengan model Qwen 27B & Function Calling terintegrasi. Anda mendapatkan respon super cepat (~1-2 detik) dengan akses database lengkap yang setara dengan Gemini untuk melacak SPK, timeline, foto, rak, dan kendala operasional." 
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
        // Auto-detect context from route parameter if available
        $routeId = request()->route('id') ?? request()->route('order') ?? $orderId;
        if ($routeId && is_numeric($routeId)) {
            $this->setContextOrder((int) $routeId);
        }

        $this->initWelcomeMessage();
    }

    public function initWelcomeMessage()
    {
        $welcomeText = "Halo! Saya **Workshop AI Copilot** 🤖🛠️\n\nAda yang bisa saya bantu hari ini? Saya bisa membantu Anda melacak SPK, mencari lokasi rak sepatu, mengecek rincian biaya, atau merangkum timeline riwayat pengerjaan teknisi.";
        
        if ($this->contextSpkNumber) {
            $welcomeText .= "\n\n💡 *Saya mendeteksi Anda sedang membuka SPK **{$this->contextSpkNumber}** ({$this->contextCustomerName}). Anda bisa klik tombol pintas di bawah untuk analisis instan!*";
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
                $response = $aiService->chat($input, $historyForService, $this->contextOrderId);
            }

            // Suppress repetitive cards of currently focused SPK to keep room chat clean
            $cards = $response['cards'] ?? [];
            if ($this->contextOrderId && !empty($cards)) {
                $cards = array_values(array_filter($cards, fn($c) => ($c['id'] ?? null) !== $this->contextOrderId));
            }

            $this->messages[] = [
                'role' => 'model',
                'content' => $response['text'] ?? 'Maaf, saya tidak dapat merespons permintaan tersebut.',
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

<div class="space-y-6 p-6 min-h-screen bg-gray-50/50">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-100 pb-5">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                📊 Tracking Jasa
            </h1>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">
                Laporan & Analisis Penggunaan Jasa SPK Divisi Workshop
            </p>
        </div>

        <div class="flex items-center gap-3 self-start md:self-center">
            {{-- Print Laporan --}}
            <button onclick="window.print()" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-xs font-black rounded-xl transition-all shadow-lg shadow-indigo-600/20 hover:scale-[1.02]">
                🖨️ PRINT LAPORAN
            </button>

            {{-- Export CSV --}}
            @php
                $csvQuery = http_build_query([
                    'api_key' => config('app.dashboard_api_key'),
                    'search' => $search,
                    'category' => $category,
                    'start_date' => $date_start,
                    'end_date' => $date_end
                ]);
                $csvUrl = url('/api/v1/service-tracking-sync') . '?' . $csvQuery;
            @endphp
            <a href="{{ $csvUrl }}" target="_blank"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white text-xs font-black rounded-xl transition-all shadow-lg shadow-emerald-500/20 hover:scale-[1.02]">
                📊 EXPORT DATA
            </a>
        </div>
    </div>

    {{-- Metrics Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Card 1: Total Frekuensi --}}
        <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 relative overflow-hidden flex items-center gap-4">
            <div class="absolute -right-6 -bottom-6 text-7xl opacity-5 pointer-events-none">🛠️</div>
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-xl shadow-inner shrink-0">
                🛠️
            </div>
            <div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Total Frekuensi Jasa</span>
                <span class="text-2xl font-black text-gray-900 mt-1 block">{{ number_format($metrics['total_frequency']) }}x</span>
                <span class="text-[8px] font-bold text-gray-400 block mt-0.5">Total layanan jasa terhitung</span>
            </div>
        </div>

        {{-- Card 2: Total Pendapatan --}}
        <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 relative overflow-hidden flex items-center gap-4">
            <div class="absolute -right-6 -bottom-6 text-7xl opacity-5 pointer-events-none">💰</div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl shadow-inner shrink-0">
                💰
            </div>
            <div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Total Omset Jasa</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block">Rp {{ number_format($metrics['total_revenue'], 0, ',', '.') }}</span>
                <span class="text-[8px] font-bold text-gray-400 block mt-0.5">Akumulasi biaya layanan</span>
            </div>
        </div>

        {{-- Card 3: Rata-Rata Harga --}}
        <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 relative overflow-hidden flex items-center gap-4">
            <div class="absolute -right-6 -bottom-6 text-7xl opacity-5 pointer-events-none">📈</div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-xl shadow-inner shrink-0">
                📈
            </div>
            <div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Rata-rata Harga</span>
                <span class="text-2xl font-black text-gray-900 mt-1 block">Rp {{ number_format($metrics['avg_cost'], 0, ',', '.') }}</span>
                <span class="text-[8px] font-bold text-gray-400 block mt-0.5 font-sans">Per item tindakan layanan</span>
            </div>
        </div>
    </div>

    {{-- API Integration Developer Panel --}}
    <div class="bg-slate-900 rounded-[2rem] p-6 text-white relative overflow-hidden shadow-2xl border border-slate-800">
        <div class="absolute -right-16 -bottom-16 text-9xl opacity-10 pointer-events-none">🔌</div>
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-indigo-500/20 text-indigo-400 text-[8px] font-black rounded uppercase tracking-wider">Service Sync API</span>
                    <span class="px-2 py-0.5 bg-slate-800 text-slate-400 text-[8px] font-black rounded uppercase tracking-wider">v1.0</span>
                </div>
                <h4 class="text-sm font-black tracking-wide text-slate-100">🔌 API INTEGRATION: WORKSHOP SERVICES SYNC</h4>
                <p class="text-slate-400 text-[9px] font-bold">Sinkronisasi data penggunaan dan volume jasa SPK secara real-time dengan pihak ketiga.</p>
            </div>
            
            <div class="flex flex-col items-end gap-2 w-full lg:w-auto">
                <div class="flex items-center gap-3 w-full lg:w-auto" x-data="{ 
                    copied: false,
                    apiUrl: '{{ url('/api/v1/service-tracking-sync') . '?api_key=' . config('app.dashboard_api_key') }}',
                    copyToClipboard() {
                        try {
                            this.$refs.apiInputService.select();
                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                navigator.clipboard.writeText(this.apiUrl);
                            } else {
                                document.execCommand('copy');
                            }
                            this.copied = true;
                            setTimeout(() => this.copied = false, 2000);
                        } catch (err) {
                            console.error('Failed to copy: ', err);
                        }
                    }
                }">
                    <div class="relative flex-1 lg:flex-none">
                        <input x-ref="apiInputService" type="text" readonly :value="apiUrl" 
                               class="w-full lg:w-[480px] bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-2.5 text-[9px] font-mono text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <button @click="copyToClipboard()" class="px-5 py-2.5 bg-indigo-500 hover:bg-indigo-600 active:scale-95 text-white text-[10px] font-black rounded-xl transition-all shadow-lg shadow-indigo-500/20 flex items-center gap-2 shrink-0">
                        <span x-show="!copied">📋 COPY URL</span>
                        <span x-show="copied" x-cloak>✅ COPIED!</span>
                    </button>
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[8px] font-black text-slate-400 self-start lg:self-auto uppercase tracking-wider">
                    <span class="text-indigo-400">Parameter Opsional:</span>
                    <span>• start_date (YYYY-MM-DD)</span>
                    <span>• end_date (YYYY-MM-DD)</span>
                    <span>• search (kata kunci)</span>
                    <span>• category (kategori)</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
            {{-- Search Input --}}
            <div class="space-y-1.5 col-span-1 sm:col-span-2 md:col-span-1">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-wider block">Kata Kunci Jasa / SPK</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 text-xs">🔍</span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama jasa, no SPK, pelanggan..." 
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-9 pr-4 py-2.5 text-xs font-bold text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>
            </div>

            {{-- Kategori --}}
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-wider block">Kategori Jasa</label>
                <select wire:model.live="category" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Date Start --}}
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-wider block">Mulai Tanggal</label>
                <input wire:model.live="date_start" type="date" 
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>

            {{-- Date End --}}
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-wider block">Sampai Tanggal</label>
                <input wire:model.live="date_end" type="date" 
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>
        </div>

        {{-- Active Filters Info & Reset --}}
        @if($search || $category || $date_start || $date_end)
            <div class="mt-4 flex items-center justify-between border-t border-gray-50 pt-4">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-wider">Filter Aktif:</span>
                    @if($search)
                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-[9px] font-black rounded-lg border border-indigo-100 flex items-center gap-1.5">
                            Kata kunci: "{{ $search }}"
                            <button wire:click="$set('search', '')" class="text-indigo-400 hover:text-indigo-700 font-bold">×</button>
                        </span>
                    @endif
                    @if($category)
                        <span class="px-2.5 py-1 bg-teal-50 text-teal-700 text-[9px] font-black rounded-lg border border-teal-100 flex items-center gap-1.5">
                            Kategori: {{ $category }}
                            <button wire:click="$set('category', '')" class="text-teal-400 hover:text-teal-700 font-bold">×</button>
                        </span>
                    @endif
                    @if($date_start || $date_end)
                        <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-[9px] font-black rounded-lg border border-amber-100 flex items-center gap-1.5">
                            Periode: {{ $date_start ?: '*' }} s/d {{ $date_end ?: '*' }}
                            <button wire:click="$set('date_start', ''); wire:click='$set(\'date_end\', \'\')'" class="text-amber-400 hover:text-amber-700 font-bold">×</button>
                        </span>
                    @endif
                </div>

                <button wire:click="resetFilters" class="text-[10px] font-black text-rose-500 hover:text-rose-600 hover:underline uppercase tracking-wider">
                    🧹 RESET FILTER
                </button>
            </div>
        @endif
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">SPK / Order</th>
                        <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Pelanggan</th>
                        <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Detail Jasa</th>
                        <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Kategori</th>
                        <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-right">Biaya Jasa</th>
                        <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">PIC Teknisi</th>
                        <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Status SPK</th>
                        <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Tanggal Input</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($servicesList as $item)
                        @php
                            $wo = $item->workOrder;
                            $service = $item->service;
                            $statusVal = $wo?->status;
                            $statusString = ($statusVal instanceof \BackedEnum) ? $statusVal->value : (string) $statusVal;
                            
                            $statusColor = match($statusString) {
                                'SELESAI' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'ASSESSMENT' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'WAITING_PAYMENT' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'DONASI' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'BATAL' => 'bg-rose-50 text-rose-700 border-rose-200',
                                default => 'bg-gray-50 text-gray-700 border-gray-200',
                            };
                        @endphp
                        <tr class="hover:bg-indigo-50/10 transition-all duration-200">
                            {{-- SPK Number --}}
                            <td class="py-4 font-mono text-xs font-black text-gray-900">
                                @if($wo)
                                    <a href="{{ route('admin.orders.show', $wo->id) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                        {{ $wo->spk_number }}
                                    </a>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>

                            {{-- Pelanggan --}}
                            <td class="py-4">
                                <div class="text-xs font-black text-gray-900">{{ $wo->customer_name ?? 'N/A' }}</div>
                                <div class="text-[9px] font-bold text-gray-400 mt-0.5">{{ $wo->customer_phone ?? '-' }}</div>
                            </td>

                            {{-- Detail Jasa --}}
                            <td class="py-4 font-sans text-xs font-bold text-gray-800">
                                {{ $item->custom_service_name ?? ($service?->name ?? 'Custom Service') }}
                                @if($item->notes)
                                    <span class="block text-[9px] font-bold text-gray-400 italic mt-0.5">Note: {{ $item->notes }}</span>
                                @endif
                            </td>

                            {{-- Kategori --}}
                            <td class="py-4 text-center">
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 border border-gray-200 text-[9px] font-black rounded-lg uppercase tracking-wider">
                                    {{ $item->category_name ?? ($service?->category ?? '-') }}
                                </span>
                            </td>

                            {{-- Biaya Jasa --}}
                            <td class="py-4 text-right text-xs font-black text-gray-900 font-mono">
                                Rp {{ number_format($item->cost, 0, ',', '.') }}
                            </td>

                            {{-- PIC Teknisi --}}
                            <td class="py-4 text-center text-xs font-bold text-gray-700">
                                {{ $item->technician?->name ?? '-' }}
                            </td>

                            {{-- Status SPK --}}
                            <td class="py-4 text-center">
                                @if($wo)
                                    <span class="px-2 py-1 {{ $statusColor }} border text-[9px] font-black rounded-lg inline-block">
                                        {{ str_replace('_', ' ', $statusString) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            {{-- Tanggal Input --}}
                            <td class="py-4 text-center text-[10px] font-bold text-gray-500 font-sans">
                                {{ $item->created_at ? $item->created_at->format('d M Y H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400 text-[10px] font-black uppercase opacity-40">
                                📭 Tidak ada data jasa yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $servicesList->links() }}
        </div>
    </div>

    {{-- Print Layout Styles --}}
    <style>
        @media print {
            body {
                background-color: white !important;
                color: black !important;
                font-size: 10pt !important;
            }
            /* Hide non-printable elements */
            #sidebar-nav-container,
            .sidebar-logo-container,
            button,
            a,
            .bg-slate-900,
            .bg-white.rounded-\[2rem\]:has(select),
            nav,
            header,
            .flex-1.px-2 {
                display: none !important;
            }
            /* Main adjustments */
            main, .min-h-screen, .p-6 {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .grid {
                display: flex !important;
                justify-content: space-between !important;
                margin-bottom: 20px !important;
            }
            .grid > div {
                border: 1px solid #ccc !important;
                border-radius: 8px !important;
                padding: 10px !important;
                width: 30% !important;
                box-shadow: none !important;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-top: 20px !important;
            }
            th, td {
                border-bottom: 1px solid #ddd !important;
                padding: 8px !important;
                font-size: 8pt !important;
            }
            th {
                background-color: #f5f5f5 !important;
                font-weight: bold !important;
            }
            a {
                text-decoration: none !important;
                color: black !important;
            }
        }
    </style>
</div>

<x-app-layout>
<x-slot name="header">
    <div class="flex items-center gap-3">
        <a href="{{ route('material-requests.index') }}" class="w-8 h-8 rounded-xl flex items-center justify-center bg-slate-100 dark:bg-slate-700 text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-600 transition text-sm">←</a>
        <h2 class="font-black text-slate-800 dark:text-white text-sm uppercase tracking-wider">
            📋 Detail Nota Belanja
        </h2>
    </div>
</x-slot>

<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

    @if(session('success'))
        <div class="flex items-center gap-3 px-5 py-3.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-800 dark:text-emerald-300 text-sm font-bold">
            <span class="text-lg">✅</span> {{ session('success') }}
        </div>
    @endif

    @php
        $statusMeta = match($materialRequest->status) {
            'PENDING'   => ['label' => 'MENUNGGU REVIEW', 'icon' => '⏳', 'bg' => 'from-amber-500 to-orange-500',   'light' => 'bg-amber-50 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400', 'dot' => 'bg-amber-500', 'pulse' => 'animate-pulse'],
            'APPROVED'  => ['label' => 'DISETUJUI',       'icon' => '✅', 'bg' => 'from-emerald-500 to-teal-500',   'light' => 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400', 'dot' => 'bg-emerald-500', 'pulse' => ''],
            'REJECTED'  => ['label' => 'DITOLAK',         'icon' => '❌', 'bg' => 'from-red-500 to-rose-600',       'light' => 'bg-red-50 dark:bg-red-950/30 border-red-200 dark:border-red-800 text-red-700 dark:text-red-400', 'dot' => 'bg-red-500', 'pulse' => ''],
            'PURCHASED' => ['label' => 'SUDAH DIBELI',    'icon' => '🛍️', 'bg' => 'from-blue-500 to-indigo-600',   'light' => 'bg-blue-50 dark:bg-blue-950/30 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-400', 'dot' => 'bg-blue-500', 'pulse' => ''],
            'CANCELLED' => ['label' => 'DIBATALKAN',      'icon' => '🚫', 'bg' => 'from-slate-400 to-slate-500',   'light' => 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400', 'dot' => 'bg-slate-400', 'pulse' => ''],
            default     => ['label' => $materialRequest->status, 'icon' => '📄', 'bg' => 'from-slate-400 to-slate-500', 'light' => 'bg-slate-50 border-slate-200 text-slate-500', 'dot' => 'bg-slate-400', 'pulse' => ''],
        };

        $spkList = $materialRequest->items->pluck('workOrder')->filter()->unique('id');
        $totalItems = $materialRequest->items->count();
    @endphp

    {{-- ═══ Hero Header Card ═══ --}}
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-xl overflow-hidden">
        {{-- Gradient Top Bar --}}
        <div class="h-2 bg-gradient-to-r {{ $statusMeta['bg'] }}"></div>

        <div class="p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row items-start justify-between gap-5">
                <div class="flex-1 min-w-0">
                    {{-- Nomor Nota --}}
                    <div class="flex items-center gap-2.5 flex-wrap mb-1">
                        <span class="font-mono font-black text-2xl text-slate-900 dark:text-white">{{ $materialRequest->request_number }}</span>
                        @if($materialRequest->type === 'SHOPPING')
                            <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase bg-amber-100 dark:bg-amber-950/50 text-amber-800 dark:text-amber-400 border border-amber-200 dark:border-amber-800">🛒 Belanja</span>
                        @else
                            <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase bg-emerald-100 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">📦 PO Produksi</span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase {{ $statusMeta['light'] }} border">
                            <span class="w-1.5 h-1.5 rounded-full {{ $statusMeta['dot'] }} {{ $statusMeta['pulse'] }}"></span>
                            {{ $statusMeta['label'] }}
                        </span>
                    </div>

                    {{-- Meta Info Grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-8 gap-y-3 mt-5">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-0.5">Diminta Oleh</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $materialRequest->requestedBy->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-0.5">Tanggal Pengajuan</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $materialRequest->created_at->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-0.5">Jumlah Item</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $totalItems }} Item Material</p>
                        </div>
                        @if($materialRequest->approved_by)
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-0.5">Disetujui Oleh</p>
                            <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400">{{ $materialRequest->approvedBy->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-0.5">Tanggal Approval</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $materialRequest->approved_at?->translatedFormat('d M Y, H:i') ?? '-' }}</p>
                        </div>
                        @endif
                        @if($materialRequest->work_order_id)
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-0.5">Work Order</p>
                            <p class="text-sm font-bold text-indigo-700 dark:text-indigo-400 font-mono">{{ $materialRequest->workOrder->spk_number ?? 'N/A' }}</p>
                        </div>
                        @endif
                        @if($materialRequest->oto_id)
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-0.5">OTO</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $materialRequest->oto->oto_number ?? 'N/A' }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- SPK Terlibat Badges --}}
                    @if($spkList->count() > 0)
                        <div class="mt-4">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2">SPK Terlibat ({{ $spkList->count() }} SPK)</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($spkList as $wo)
                                    <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 text-[10px] font-black rounded-xl border border-indigo-100 dark:border-indigo-800">
                                        📦 {{ $wo->spk_number }} — {{ $wo->customer_name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($materialRequest->notes)
                        <div class="mt-4 px-4 py-3 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-600">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Catatan</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300 italic">"{{ $materialRequest->notes }}"</p>
                        </div>
                    @endif
                </div>

                {{-- Nilai Estimasi --}}
                <div class="shrink-0 text-right bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-950/40 dark:to-purple-950/40 border border-indigo-100 dark:border-indigo-900 rounded-2xl px-6 py-5 min-w-[180px]">
                    <p class="text-[10px] font-black uppercase tracking-wider text-indigo-400 mb-1">Total Estimasi Nilai</p>
                    <p class="text-2xl font-black text-indigo-700 dark:text-indigo-300">Rp {{ number_format($materialRequest->total_estimated_cost, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- ═══ LEFT: Material Items Table ═══ --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-700/30">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-white flex items-center gap-2">
                        📋 Daftar Material ({{ $totalItems }} Item)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="px-5 py-3.5 text-left">#</th>
                                <th class="px-5 py-3.5 text-left">Material & Spesifikasi</th>
                                <th class="px-5 py-3.5 text-center">Qty</th>
                                <th class="px-5 py-3.5 text-right">Harga Satuan</th>
                                <th class="px-5 py-3.5 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($materialRequest->items as $index => $item)
                                @php $wo = $item->workOrder ?? $materialRequest->workOrder; @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors">
                                    <td class="px-5 py-4 text-slate-400 font-bold text-xs">{{ $index + 1 }}</td>
                                    <td class="px-5 py-4">
                                        <span class="font-bold text-slate-900 dark:text-white block">{{ $item->material_name }}</span>
                                        @if($item->specification)
                                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $item->specification }}</span>
                                        @endif
                                        @if($item->isCustomMaterial())
                                            <span class="mt-1 inline-block px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 text-[9px] font-black rounded uppercase">Custom</span>
                                        @endif
                                        @if($wo)
                                            <span class="mt-1.5 inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 text-[10px] font-black rounded-md border border-indigo-100 dark:border-indigo-800">
                                                📦 {{ $wo->spk_number }} — {{ $wo->customer_name }}
                                            </span>
                                        @endif
                                        @if($item->notes)
                                            <span class="mt-1.5 block text-[10px] text-amber-600 dark:text-amber-400 italic">💬 {{ $item->notes }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-black rounded-lg">
                                            {{ $item->quantity }} {{ $item->unit ?? 'Unit' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm font-bold text-slate-600 dark:text-slate-400">
                                        Rp {{ number_format($item->estimated_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm font-black text-slate-900 dark:text-white">
                                        Rp {{ number_format($item->getSubtotal(), 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-indigo-50/50 dark:bg-indigo-950/20 border-t-2 border-indigo-100 dark:border-indigo-900">
                            <tr>
                                <td colspan="4" class="px-5 py-4 text-right text-xs font-black uppercase tracking-wider text-slate-400">Total Estimasi Nilai</td>
                                <td class="px-5 py-4 text-right text-xl font-black text-indigo-700 dark:text-indigo-400">
                                    Rp {{ number_format($materialRequest->total_estimated_cost, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- ═══ RIGHT: Action Sidebar ═══ --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- Action Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm p-6 sticky top-24">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-4">⚡ Aksi Nota</h3>

                <div class="space-y-3">
                    @if($materialRequest->status === 'PENDING')
                        @can('manageInventory')
                        {{-- APPROVE --}}
                        <form action="{{ route('material-requests.approve', $materialRequest) }}" method="POST">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Setujui pengajuan ini?')"
                                class="w-full px-4 py-3.5 rounded-2xl font-black text-white text-sm transition-all active:scale-95 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 shadow-md shadow-emerald-500/20 flex items-center justify-center gap-2">
                                <span class="text-lg">✅</span> Approve Pengajuan
                            </button>
                        </form>

                        {{-- REJECT --}}
                        <form action="{{ route('material-requests.reject', $materialRequest) }}" method="POST">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Tolak pengajuan ini?')"
                                class="w-full px-4 py-3.5 rounded-2xl font-black text-white text-sm transition-all active:scale-95 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 shadow-md shadow-red-500/20 flex items-center justify-center gap-2">
                                <span class="text-lg">❌</span> Reject Pengajuan
                            </button>
                        </form>

                        {{-- CANCEL --}}
                        <form action="{{ route('material-requests.cancel', $materialRequest) }}" method="POST">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Batalkan pengajuan ini?')"
                                class="w-full px-4 py-3.5 rounded-2xl font-black text-slate-600 dark:text-slate-300 text-sm transition-all active:scale-95 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 flex items-center justify-center gap-2">
                                <span>🚫</span> Batalkan
                            </button>
                        </form>
                        @endcan

                    @elseif($materialRequest->status === 'APPROVED')
                        @can('manageInventory')
                        <form action="{{ route('material-requests.mark-purchased', $materialRequest) }}" method="POST">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Tandai sebagai sudah dibeli?')"
                                class="w-full px-4 py-3.5 rounded-2xl font-black text-white text-sm transition-all active:scale-95 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 shadow-md shadow-amber-500/20 flex items-center justify-center gap-2">
                                <span class="text-lg">🛍️</span> Mark as Purchased
                            </button>
                        </form>
                        @endcan

                    @elseif($materialRequest->status === 'PURCHASED')
                        <div class="py-6 rounded-2xl bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900 text-center">
                            <p class="text-3xl mb-2">🛍️</p>
                            <p class="text-sm font-black text-blue-700 dark:text-blue-400">Material Sudah Dibeli!</p>
                            <p class="text-[11px] text-blue-500 mt-1">Proses selesai.</p>
                        </div>

                    @elseif($materialRequest->status === 'REJECTED')
                        <div class="py-6 rounded-2xl bg-red-50 dark:bg-red-950/30 border border-red-100 dark:border-red-900 text-center">
                            <p class="text-3xl mb-2">❌</p>
                            <p class="text-sm font-black text-red-700 dark:text-red-400">Pengajuan Ditolak</p>
                        </div>

                    @elseif($materialRequest->status === 'CANCELLED')
                        <div class="py-6 rounded-2xl bg-slate-50 dark:bg-slate-700/30 border border-slate-100 dark:border-slate-600 text-center">
                            <p class="text-3xl mb-2">🚫</p>
                            <p class="text-sm font-black text-slate-500 dark:text-slate-400">Pengajuan Dibatalkan</p>
                        </div>
                    @endif
                </div>

                {{-- Info Box --}}
                <div class="mt-5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/40 border border-slate-100 dark:border-slate-600">
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2">ℹ️ Informasi</p>
                    <ul class="text-[11px] text-slate-600 dark:text-slate-400 font-bold space-y-1">
                        @if($materialRequest->type === 'SHOPPING')
                            <li>• Material belanja tidak tergantung stok</li>
                            <li>• Setelah Approved → proses pembelian</li>
                            <li>• Update stok setelah barang datang</li>
                        @else
                            <li>• PO untuk kekurangan stok produksi</li>
                            <li>• Setelah Approved → proses pembelian</li>
                            <li>• Material bisa digunakan setelah Purchased</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>

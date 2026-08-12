<x-app-layout>
<x-slot name="header">
    <h2 class="font-black text-slate-800 dark:text-white text-sm uppercase tracking-wider">
        📋 Pengajuan Material
    </h2>
</x-slot>

<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

    @if(session('success'))
        <div class="flex items-center gap-3 px-5 py-3.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-800 dark:text-emerald-300 text-sm font-bold">
            <span class="text-lg">✅</span> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-5 py-3.5 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 rounded-2xl text-red-800 dark:text-red-300 text-sm font-bold">
            <span class="text-lg">❌</span> {{ session('error') }}
        </div>
    @endif

    {{-- ═══ Metric Overview Cards ═══ --}}
    @php
        $allReqs = \App\Models\MaterialRequest::query();
        $statPending   = (clone $allReqs)->where('status', 'PENDING')->count();
        $statApproved  = (clone $allReqs)->where('status', 'APPROVED')->count();
        $statPurchased = (clone $allReqs)->where('status', 'PURCHASED')->count();
        $statTotalNilai = (clone $allReqs)->whereIn('status', ['PENDING','APPROVED','PURCHASED'])->sum('total_estimated_cost');
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-amber-100 dark:border-amber-900/50 shadow-sm p-5 flex flex-col gap-1">
            <span class="text-[10px] font-black uppercase tracking-widest text-amber-500 dark:text-amber-400">⏳ Pending</span>
            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $statPending }}</span>
            <span class="text-[10px] text-slate-400 font-bold">Menunggu Review</span>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-emerald-100 dark:border-emerald-900/50 shadow-sm p-5 flex flex-col gap-1">
            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500 dark:text-emerald-400">✅ Approved</span>
            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $statApproved }}</span>
            <span class="text-[10px] text-slate-400 font-bold">Disetujui, Siap Beli</span>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-blue-100 dark:border-blue-900/50 shadow-sm p-5 flex flex-col gap-1">
            <span class="text-[10px] font-black uppercase tracking-widest text-blue-500 dark:text-blue-400">🛍️ Purchased</span>
            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $statPurchased }}</span>
            <span class="text-[10px] text-slate-400 font-bold">Sudah Dibeli</span>
        </div>
        <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl shadow-md shadow-indigo-500/20 p-5 flex flex-col gap-1">
            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-200">💰 Total Nilai</span>
            <span class="text-xl font-black text-white">Rp {{ number_format($statTotalNilai, 0, ',', '.') }}</span>
            <span class="text-[10px] text-indigo-200 font-bold">Estimasi Aktif</span>
        </div>
    </div>

    {{-- ═══ Filter Bar ═══ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-5">
        <form method="GET" action="{{ route('material-requests.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Cari Nota</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nomor request, nama..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-sm font-bold text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 transition">
            </div>
            <div class="min-w-[160px]">
                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Tipe</label>
                <select name="type" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-sm font-bold text-slate-800 dark:text-white focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 transition">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Semua Tipe</option>
                    <option value="SHOPPING" {{ request('type') == 'SHOPPING' ? 'selected' : '' }}>🛒 Belanja</option>
                    <option value="PRODUCTION_PO" {{ request('type') == 'PRODUCTION_PO' ? 'selected' : '' }}>📦 PO Produksi</option>
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Status</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-sm font-bold text-slate-800 dark:text-white focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 transition">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>✅ Approved</option>
                    <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>❌ Rejected</option>
                    <option value="PURCHASED" {{ request('status') == 'PURCHASED' ? 'selected' : '' }}>🛍️ Purchased</option>
                    <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>🚫 Cancelled</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-black transition-all active:scale-95 shadow-sm">
                    Filter
                </button>
                <a href="{{ route('material-requests.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-sm font-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ═══ Requests List ═══ --}}
    <div class="space-y-3">
        @forelse($requests as $req)
            @php
                $statusMeta = match($req->status) {
                    'PENDING'   => ['label' => 'PENDING',   'icon' => '⏳', 'bg' => 'bg-amber-50 dark:bg-amber-950/30',   'text' => 'text-amber-700 dark:text-amber-400',   'border' => 'border-amber-200 dark:border-amber-800',   'dot' => 'bg-amber-500', 'pulse' => 'animate-pulse'],
                    'APPROVED'  => ['label' => 'APPROVED',  'icon' => '✅', 'bg' => 'bg-emerald-50 dark:bg-emerald-950/30','text' => 'text-emerald-700 dark:text-emerald-400', 'border' => 'border-emerald-200 dark:border-emerald-800', 'dot' => 'bg-emerald-500', 'pulse' => ''],
                    'REJECTED'  => ['label' => 'REJECTED',  'icon' => '❌', 'bg' => 'bg-red-50 dark:bg-red-950/30',       'text' => 'text-red-700 dark:text-red-400',       'border' => 'border-red-200 dark:border-red-800',       'dot' => 'bg-red-500',   'pulse' => ''],
                    'PURCHASED' => ['label' => 'PURCHASED', 'icon' => '🛍️', 'bg' => 'bg-blue-50 dark:bg-blue-950/30',    'text' => 'text-blue-700 dark:text-blue-400',     'border' => 'border-blue-200 dark:border-blue-800',     'dot' => 'bg-blue-500',  'pulse' => ''],
                    'CANCELLED' => ['label' => 'CANCELLED', 'icon' => '🚫', 'bg' => 'bg-slate-50 dark:bg-slate-700/30',  'text' => 'text-slate-500 dark:text-slate-400',   'border' => 'border-slate-200 dark:border-slate-600',   'dot' => 'bg-slate-400', 'pulse' => ''],
                    default     => ['label' => $req->status,'icon' => '📄', 'bg' => 'bg-slate-50',                        'text' => 'text-slate-600',                       'border' => 'border-slate-200',                         'dot' => 'bg-slate-400', 'pulse' => ''],
                };

                $spkList = $req->items->pluck('workOrder.spk_number')->filter()->unique()->values();
                $spkCount = $spkList->count();
                $itemCount = $req->items->count();
            @endphp
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden group">
                <div class="p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        {{-- Left: Meta --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2.5 flex-wrap mb-2">
                                {{-- Nomor Nota --}}
                                <span class="font-mono font-black text-sm text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $req->request_number }}</span>

                                {{-- Tipe --}}
                                @if($req->type === 'SHOPPING')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-100 dark:bg-amber-950/50 text-amber-800 dark:text-amber-400 border border-amber-200 dark:border-amber-800">🛒 Belanja</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">📦 PO Produksi</span>
                                @endif

                                {{-- Status --}}
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $statusMeta['bg'] }} {{ $statusMeta['text'] }} border {{ $statusMeta['border'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusMeta['dot'] }} {{ $statusMeta['pulse'] }}"></span>
                                    {{ $statusMeta['label'] }}
                                </span>
                            </div>

                            <div class="flex flex-wrap gap-x-5 gap-y-1.5 text-xs text-slate-500 dark:text-slate-400 font-bold">
                                <span>👤 {{ $req->requestedBy->name ?? 'N/A' }}</span>
                                <span>📅 {{ $req->created_at->translatedFormat('d M Y') }}</span>
                                <span>📦 {{ $itemCount }} Item</span>
                                @if($spkCount > 0)
                                    <span class="text-indigo-600 dark:text-indigo-400">🔖 {{ $spkCount }} SPK Terlibat</span>
                                @endif
                            </div>

                            @if($spkList->count() > 0)
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    @foreach($spkList->take(4) as $spkNum)
                                        <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 text-[10px] font-black rounded-md border border-indigo-100 dark:border-indigo-800">{{ $spkNum }}</span>
                                    @endforeach
                                    @if($spkList->count() > 4)
                                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[10px] font-black rounded-md">+{{ $spkList->count() - 4 }} lainnya</span>
                                    @endif
                                </div>
                            @endif

                            @if($req->notes)
                                <p class="mt-2 text-[11px] text-slate-400 dark:text-slate-500 italic truncate max-w-lg">"{{ $req->notes }}"</p>
                            @endif
                        </div>

                        {{-- Right: Nilai + CTA --}}
                        <div class="flex sm:flex-col items-center sm:items-end gap-3 shrink-0">
                            <div class="text-right">
                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Estimasi Nilai</p>
                                <p class="text-lg font-black text-indigo-700 dark:text-indigo-400">Rp {{ number_format($req->total_estimated_cost, 0, ',', '.') }}</p>
                            </div>
                            <a href="{{ route('material-requests.show', $req) }}"
                               class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black transition-all active:scale-95 shadow-sm whitespace-nowrap">
                                Detail ➔
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Status Bar Bottom --}}
                <div class="h-1 {{ match($req->status) { 'PENDING' => 'bg-amber-400', 'APPROVED' => 'bg-emerald-500', 'REJECTED' => 'bg-red-500', 'PURCHASED' => 'bg-blue-500', default => 'bg-slate-300' } }}"></div>
            </div>
        @empty
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-16 text-center">
                <p class="text-4xl mb-3">📭</p>
                <p class="text-slate-400 font-bold text-sm">Tidak ada pengajuan material yang ditemukan.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($requests->hasPages())
        <div class="mt-4">{{ $requests->links() }}</div>
    @endif
</div>
</x-app-layout>

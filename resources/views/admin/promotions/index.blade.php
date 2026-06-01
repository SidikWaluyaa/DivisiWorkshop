<x-app-layout>
<div class="min-h-screen bg-[#F8FAFC]">

    {{-- ===== PREMIUM DARK HEADER ===== --}}
    <div class="bg-gray-900 pt-12 pb-28 relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/10 to-transparent mix-blend-overlay"></div>
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-[#FFC232]/10 rounded-full blur-[100px]"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-purple-500/10 rounded-full blur-[100px]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-8">
                <div>
                    <div class="flex items-center gap-4 mb-2">
                        <h1 class="text-5xl font-black text-white italic tracking-tighter leading-none uppercase">Manajemen Promo</h1>
                    </div>
                    <p class="text-white/40 font-black text-xs uppercase tracking-[0.4em] italic flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-[#FFC232]"></span>
                        Kelola promo, diskon & pantau biaya promosi
                    </p>
                </div>

                <a href="{{ route('admin.promotions.create') }}" class="group flex items-center gap-3 bg-[#FFC232] text-gray-900 px-8 py-4 rounded-2xl font-black italic text-sm uppercase tracking-widest shadow-[0_20px_40px_-10px_rgba(255,194,50,0.5)] hover:scale-105 transition-all active:scale-95 border-4 border-white/10">
                    <svg class="w-5 h-5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                    Buat Promo Baru
                </a>
            </div>
        </div>
    </div>

    {{-- ===== SUCCESS MESSAGE ===== --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-6 -mt-6 relative z-30 mb-4">
            <div class="bg-white rounded-2xl border border-emerald-200 p-5 shadow-xl flex items-center gap-4">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-sm font-black text-gray-900 italic tracking-tight uppercase">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- ===== SUMMARY CARDS ===== --}}
    <div class="max-w-7xl mx-auto px-6 -mt-16 relative z-20 mb-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Card: Promo Aktif --}}
            <div class="bg-white rounded-[2rem] p-7 shadow-2xl border border-gray-100 group hover:shadow-emerald-500/10 transition-all relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-500/5 rounded-bl-[3rem] -mr-4 -mt-4 group-hover:scale-150 transition-transform duration-700"></div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center mb-4 group-hover:-rotate-12 transition-transform">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] italic block mb-1">Promo Aktif</span>
                <div class="text-3xl font-black text-gray-900 italic tracking-tighter leading-none">{{ $activePromoCount }}</div>
            </div>

            {{-- Card: Total Penggunaan --}}
            <div class="bg-white rounded-[2rem] p-7 shadow-2xl border border-gray-100 group hover:shadow-blue-500/10 transition-all relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-blue-500/5 rounded-bl-[3rem] -mr-4 -mt-4 group-hover:scale-150 transition-transform duration-700"></div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center mb-4 group-hover:-rotate-12 transition-transform">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                </div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] italic block mb-1">Total Penggunaan</span>
                <div class="text-3xl font-black text-gray-900 italic tracking-tighter leading-none">{{ number_format($totalUsageCount) }}</div>
            </div>

            {{-- Card: Total Biaya Promosi --}}
            <div class="bg-white rounded-[2rem] p-7 shadow-2xl border border-gray-100 group hover:shadow-rose-500/10 transition-all relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-rose-500/5 rounded-bl-[3rem] -mr-4 -mt-4 group-hover:scale-150 transition-transform duration-700"></div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center mb-4 group-hover:-rotate-12 transition-transform">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[10px] font-black text-rose-500 uppercase tracking-[0.3em] italic block mb-1">Total Biaya Promosi</span>
                <div class="text-2xl font-black text-gray-900 italic tracking-tighter leading-none">Rp {{ number_format($totalDiscountGiven, 0, ',', '.') }}</div>
            </div>

            {{-- Card: Revenue Terdampak --}}
            <div class="bg-white rounded-[2rem] p-7 shadow-2xl border border-gray-100 group hover:shadow-amber-500/10 transition-all relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-amber-500/5 rounded-bl-[3rem] -mr-4 -mt-4 group-hover:scale-150 transition-transform duration-700"></div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center mb-4 group-hover:-rotate-12 transition-transform">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] italic block mb-1">Revenue Terdampak</span>
                <div class="text-2xl font-black text-gray-900 italic tracking-tighter leading-none">Rp {{ number_format($totalRevenueImpacted, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- ===== TAB NAVIGATION ===== --}}
    <div class="max-w-7xl mx-auto px-6" x-data="{ activeTab: '{{ request()->has('report_page') || request()->has('report_promo') || request()->has('report_from') ? 'laporan' : 'promo' }}' }">

        <div class="flex items-center gap-2 mb-8 bg-white rounded-2xl p-2 shadow-lg border border-gray-100 inline-flex">
            <button @click="activeTab = 'promo'" :class="activeTab === 'promo' ? 'bg-gray-900 text-white shadow-xl' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'" class="px-8 py-3 rounded-xl text-[11px] font-black uppercase tracking-[0.3em] italic transition-all flex items-center gap-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                Daftar Promo
            </button>
            <button @click="activeTab = 'laporan'" :class="activeTab === 'laporan' ? 'bg-gray-900 text-white shadow-xl' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'" class="px-8 py-3 rounded-xl text-[11px] font-black uppercase tracking-[0.3em] italic transition-all flex items-center gap-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Laporan Penggunaan
            </button>
        </div>

        {{-- ================================================ --}}
        {{-- TAB 1: DAFTAR PROMO --}}
        {{-- ================================================ --}}
        <div x-show="activeTab === 'promo'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

            {{-- Search & Filter --}}
            <div class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-100 mb-8">
                <form method="GET" action="{{ route('admin.promotions.index') }}" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama promo..." class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-[#FFC232]/50 focus:border-[#FFC232] transition-all italic placeholder:text-gray-300">
                    </div>
                    <select name="status" class="px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-[#FFC232]/50 italic">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-[0.3em] italic transition-all shadow-lg hover:shadow-xl active:scale-95">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.promotions.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-[0.3em] italic transition-all text-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Promo Cards Grid --}}
            <div class="space-y-5">
                @forelse($promotions as $promo)
                    @php
                        $now = now();
                        $isExpired = $now->gt($promo->valid_until);
                        $isNotStarted = $now->lt($promo->valid_from);
                        $isActive = $promo->is_active && !$isExpired && !$isNotStarted;
                        $daysLeft = $isActive ? $now->diffInDays($promo->valid_until) : 0;
                        $usagePercent = $promo->max_usage_total ? min(100, ($promo->current_usage_count / $promo->max_usage_total) * 100) : 0;
                        
                        $typeBadge = match($promo->type) {
                            'PERCENTAGE' => 'from-blue-500 to-indigo-600 text-white',
                            'FIXED' => 'from-emerald-500 to-teal-600 text-white',
                            'BUNDLE' => 'from-purple-500 to-violet-600 text-white',
                            'BOGO' => 'from-rose-500 to-pink-600 text-white',
                            default => 'from-gray-400 to-gray-500 text-white',
                        };

                        $statusColor = $isActive 
                            ? 'bg-emerald-500' 
                            : ($isExpired ? 'bg-gray-400' : ($isNotStarted ? 'bg-amber-500' : 'bg-red-500'));
                        $statusLabel = $isActive 
                            ? 'Aktif' 
                            : ($isExpired ? 'Kadaluarsa' : ($isNotStarted ? 'Belum Mulai' : 'Nonaktif'));
                    @endphp
                    <div class="bg-white rounded-[2rem] p-8 shadow-xl border border-gray-100 hover:shadow-2xl transition-all group relative overflow-hidden">
                        <div class="absolute inset-y-0 left-0 w-1.5 {{ $isActive ? 'bg-emerald-500' : ($isExpired ? 'bg-gray-300' : 'bg-amber-500') }} opacity-60 group-hover:opacity-100 transition-opacity"></div>

                        <div class="flex flex-col lg:flex-row justify-between gap-6">
                            {{-- Left: Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-3 mb-4">
                                    {{-- Promo Code --}}
                                    <span class="font-mono text-lg font-black text-gray-900 italic tracking-tight">{{ $promo->code }}</span>
                                    {{-- Type Badge --}}
                                    <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-[0.2em] bg-gradient-to-r {{ $typeBadge }} shadow-sm">{{ $promo->type }}</span>
                                    {{-- Status Dot --}}
                                    <span class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-[0.2em] italic {{ $isActive ? 'text-emerald-600' : ($isExpired ? 'text-gray-400' : 'text-amber-600') }}">
                                        <span class="w-2 h-2 rounded-full {{ $statusColor }} {{ $isActive ? 'animate-pulse' : '' }}"></span>
                                        {{ $statusLabel }}
                                    </span>
                                    @if($isActive && $daysLeft <= 7)
                                        <span class="px-2.5 py-0.5 rounded-lg text-[9px] font-black bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider italic">⏳ {{ $daysLeft }} Hari Lagi</span>
                                    @endif
                                </div>

                                <h3 class="text-xl font-black text-gray-900 italic tracking-tight mb-2">{{ $promo->name }}</h3>
                                @if($promo->description)
                                    <p class="text-xs text-gray-500 italic mb-4 leading-relaxed">{{ Str::limit($promo->description, 120) }}</p>
                                @endif

                                {{-- Meta Row --}}
                                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic">
                                    {{-- Discount Display --}}
                                    <span class="text-emerald-600">
                                        @if($promo->type === 'PERCENTAGE')
                                            Diskon {{ $promo->discount_percentage }}%
                                        @elseif($promo->type === 'FIXED')
                                            Diskon Rp {{ number_format($promo->discount_amount, 0, ',', '.') }}
                                        @elseif($promo->type === 'BOGO')
                                            Beli 1 Gratis 1
                                        @else
                                            Bundle Deal
                                        @endif
                                    </span>
                                    <span>{{ $promo->valid_from->format('d M Y') }} – {{ $promo->valid_until->format('d M Y') }}</span>
                                    @if($promo->creator)
                                        <span>oleh {{ $promo->creator->name }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Right: Stats + Actions --}}
                            <div class="flex flex-col items-end gap-4 min-w-[220px]">
                                {{-- Usage Progress --}}
                                <div class="w-full p-5 bg-gray-50 rounded-2xl border border-gray-100">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] italic">Penggunaan</span>
                                        <span class="text-sm font-black text-gray-900 italic tabular-nums">{{ $promo->current_usage_count }} / {{ $promo->max_usage_total ?? '∞' }}</span>
                                    </div>
                                    @if($promo->max_usage_total)
                                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-700 {{ $usagePercent >= 90 ? 'bg-rose-500' : ($usagePercent >= 50 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $usagePercent }}%"></div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex items-center gap-2">
                                    {{-- Toggle Active --}}
                                    <form action="{{ route('admin.promotions.toggle-active', $promo) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-10 h-10 rounded-xl flex items-center justify-center border transition-all shadow-sm hover:scale-110 active:scale-95 {{ $promo->is_active ? 'bg-emerald-50 border-emerald-200 text-emerald-600 hover:bg-emerald-100' : 'bg-gray-50 border-gray-200 text-gray-400 hover:bg-gray-100' }}" title="{{ $promo->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            @if($promo->is_active)
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                            @endif
                                        </button>
                                    </form>
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.promotions.edit', $promo) }}" class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 hover:bg-blue-100 hover:scale-110 transition-all shadow-sm active:scale-95" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    {{-- Delete --}}
                                    <form action="{{ route('admin.promotions.destroy', $promo) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus promo {{ $promo->code }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-500 hover:bg-rose-100 hover:scale-110 transition-all shadow-sm active:scale-95" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-[2rem] p-16 shadow-xl border border-gray-100 text-center">
                        <div class="text-6xl mb-4">🎁</div>
                        <div class="text-xl font-black text-gray-900 italic tracking-tight uppercase mb-2">Belum Ada Promo</div>
                        <p class="text-sm text-gray-500 italic mb-6">Buat promo pertama Anda untuk menarik lebih banyak customer!</p>
                        <a href="{{ route('admin.promotions.create') }}" class="inline-flex items-center gap-2 bg-[#FFC232] text-gray-900 px-8 py-3 rounded-xl font-black italic text-sm uppercase tracking-widest shadow-lg hover:scale-105 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                            Buat Promo Baru
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($promotions->hasPages())
                <div class="mt-8">{{ $promotions->withQueryString()->links() }}</div>
            @endif
        </div>

        {{-- ================================================ --}}
        {{-- TAB 2: LAPORAN PENGGUNAAN --}}
        {{-- ================================================ --}}
        <div x-show="activeTab === 'laporan'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">

            {{-- Report Filter --}}
            <div class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-100 mb-8">
                <form method="GET" action="{{ route('admin.promotions.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
                    <input type="hidden" name="report_page" value="1">
                    <div class="flex-1">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] italic block mb-2">Promo</label>
                        <select name="report_promo" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-[#FFC232]/50 italic">
                            <option value="">Semua Promo</option>
                            @foreach($allPromos as $p)
                                <option value="{{ $p->id }}" {{ request('report_promo') == $p->id ? 'selected' : '' }}>{{ $p->code }} — {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] italic block mb-2">Dari Tanggal</label>
                        <input type="date" name="report_from" value="{{ request('report_from') }}" class="px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-[#FFC232]/50 italic">
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] italic block mb-2">Sampai Tanggal</label>
                        <input type="date" name="report_to" value="{{ request('report_to') }}" class="px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-[#FFC232]/50 italic">
                    </div>
                    <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-[0.3em] italic transition-all shadow-lg active:scale-95">
                        Filter
                    </button>
                </form>
            </div>

            {{-- Breakdown Per Promo --}}
            @if($promoBreakdown->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.5em] italic flex items-center gap-4 mb-6">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Breakdown Per Promo
                    <div class="h-px flex-1 bg-gray-100"></div>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($promoBreakdown as $bd)
                        <div class="bg-white rounded-[2rem] p-7 shadow-xl border border-gray-100 hover:shadow-2xl transition-all group relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-16 h-16 bg-rose-500/5 rounded-bl-[3rem] -mr-4 -mt-4 group-hover:scale-150 transition-transform duration-700"></div>
                            <div class="flex items-center gap-3 mb-5">
                                <span class="font-mono text-sm font-black text-gray-900 italic">{{ $bd->promotion->code ?? '—' }}</span>
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-wider italic">{{ $bd->promotion->name ?? '' }}</span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] italic">Digunakan</span>
                                    <span class="text-sm font-black text-gray-900 italic tabular-nums">{{ $bd->usage_count }}x</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] italic">Revenue Asli</span>
                                    <span class="text-sm font-black text-gray-900 italic tabular-nums">Rp {{ number_format($bd->total_original, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                    <span class="text-[9px] font-black text-rose-500 uppercase tracking-[0.2em] italic">Biaya Promosi</span>
                                    <span class="text-lg font-black text-rose-600 italic tabular-nums tracking-tighter">- Rp {{ number_format($bd->total_discount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Usage Log Table --}}
            <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-8 pb-0">
                    <h2 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.5em] italic flex items-center gap-4">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Riwayat Penggunaan Detail
                        <div class="h-px flex-1 bg-gray-100"></div>
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="px-8 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Tanggal</th>
                                <th class="px-6 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Promo</th>
                                <th class="px-6 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Customer</th>
                                <th class="px-6 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] italic">SPK</th>
                                <th class="px-6 py-5 text-right text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Harga Asli</th>
                                <th class="px-6 py-5 text-right text-[9px] font-black text-rose-400 uppercase tracking-[0.3em] italic">Diskon</th>
                                <th class="px-6 py-5 text-right text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Harga Akhir</th>
                                <th class="px-8 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($usageLogs as $log)
                                <tr class="hover:bg-gray-50/50 transition-colors group/row">
                                    <td class="px-8 py-5">
                                        <span class="text-xs font-bold text-gray-700 italic">{{ $log->applied_at->format('d M Y') }}</span>
                                        <span class="text-[10px] text-gray-400 block italic">{{ $log->applied_at->format('H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="font-mono text-xs font-black text-gray-900 italic">{{ $log->promotion->code ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="text-xs font-bold text-gray-700 italic">{{ $log->customer_phone ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        @if($log->workOrder)
                                            <a href="{{ route('admin.orders.show', $log->workOrder->id) }}" class="text-xs font-black text-blue-600 italic hover:underline">{{ $log->workOrder->spk_number }}</a>
                                        @elseif($log->csLead && $log->csLead->spk)
                                            <a href="{{ route('cs.leads.show', $log->csLead->id) }}" class="text-xs font-black text-blue-600 italic hover:underline">{{ $log->csLead->spk->spk_number }}</a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <span class="text-xs font-bold text-gray-700 italic tabular-nums">Rp {{ number_format($log->original_amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <span class="text-xs font-black text-rose-600 italic tabular-nums">- Rp {{ number_format($log->discount_amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <span class="text-xs font-black text-gray-900 italic tabular-nums">Rp {{ number_format($log->final_amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-[10px] font-bold text-gray-500 italic">{{ $log->appliedBy->name ?? '—' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-8 py-16 text-center">
                                        <div class="text-5xl mb-3">📊</div>
                                        <div class="text-lg font-black text-gray-900 italic tracking-tight uppercase mb-1">Belum Ada Data</div>
                                        <p class="text-xs text-gray-500 italic">Data penggunaan promo akan muncul di sini setelah promo digunakan pada SPK</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Report Pagination --}}
                @if($usageLogs->hasPages())
                    <div class="p-6 border-t border-gray-100">{{ $usageLogs->withQueryString()->links() }}</div>
                @endif
            </div>

            {{-- Report Summary Footer --}}
            @if($usageLogs->isNotEmpty())
            <div class="mt-8 bg-gray-900 rounded-[2rem] p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute -right-16 -top-16 w-48 h-48 bg-rose-500/10 rounded-full blur-[80px]"></div>
                <div class="flex flex-col md:flex-row justify-between items-center gap-6 relative z-10">
                    <div>
                        <span class="text-[10px] font-black text-white/40 uppercase tracking-[0.4em] italic block mb-1">Rata-rata Diskon per Transaksi</span>
                        <span class="text-2xl font-black text-white italic tracking-tighter tabular-nums">Rp {{ number_format($avgDiscountPerTransaction, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black text-rose-400 uppercase tracking-[0.4em] italic block mb-1">Total Biaya Promosi (Keseluruhan)</span>
                        <span class="text-4xl font-black text-[#FFC232] italic tracking-tighter tabular-nums leading-none drop-shadow-lg">Rp {{ number_format($totalDiscountGiven, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>

    </div>{{-- End Tab Container --}}

    <div class="h-16"></div>
</div>
</x-app-layout>

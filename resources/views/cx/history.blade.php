<x-app-layout>
    <div class="min-h-screen bg-white pb-12">
        {{-- Clean Header with Brand Colors --}}
        <div class="relative bg-white pt-10 pb-20 px-4 border-b border-gray-100">
            {{-- Subtle decorative accent --}}
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#22AF85] via-[#FFC232] to-[#22AF85]"></div>
            
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-[#22AF85]/10 rounded-xl border border-[#22AF85]/20">
                                <svg class="w-5 h-5 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-[#22AF85] font-bold tracking-widest text-[11px] uppercase">Resolution Archive</span>
                        </div>
                        <h1 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">
                            History Resolusi CX
                        </h1>
                        <p class="text-gray-400 max-w-xl text-sm font-medium leading-relaxed">
                            Arsip seluruh kendala yang telah diselesaikan. Gunakan pencarian untuk melacak performa resolusi dimasa lampau.
                        </p>
                    </div>

                    {{-- Stat Card --}}
                    <div class="flex gap-3">
                        <div class="bg-[#22AF85]/5 border border-[#22AF85]/15 p-5 rounded-2xl min-w-[130px] text-center">
                            <div class="text-[10px] font-black text-[#22AF85] uppercase tracking-widest mb-1">Total Arsip</div>
                            <div class="text-3xl font-black text-gray-900">{{ $issues->total() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10">
            
            {{-- Filter Bar --}}
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-100/80 border border-gray-100 p-5 mb-6 relative z-10">
                <form action="{{ route('cx.history') }}" method="GET" class="space-y-4">
                    {{-- Row 1: Search & Main Actions --}}
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                        {{-- Search --}}
                        <div class="lg:col-span-8 xl:col-span-9 flex flex-col md:flex-row gap-3">
                            <div class="relative flex-grow">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border-gray-100 rounded-xl text-sm font-semibold text-gray-700 placeholder-gray-300 focus:bg-white focus:ring-2 focus:ring-[#22AF85]/20 focus:border-[#22AF85] transition-all"
                                       placeholder="Cari SPK, Nama Pelanggan, atau Kategori...">
                            </div>
                            
                            <div class="flex gap-2 shrink-0">
                                {{-- Sorting --}}
                                <select name="sort" class="w-full md:w-auto py-3 bg-amber-50 border border-amber-100 rounded-xl text-sm font-black text-amber-600 focus:bg-white focus:ring-2 focus:ring-amber-200 focus:border-amber-400 appearance-none px-6 pr-10" style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22 fill=%22none%22 viewBox=%220 0 20 20%22%3E%3Cpath stroke=%22%23b45309%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22m6 8 4 4 4-4%22%2F%3E%3C%2Fsvg%3E'); background-position: right 1rem center; background-repeat: no-repeat; background-size: 1.25rem auto;">
                                    <option value="asc" {{ request('sort', 'asc') == 'asc' ? 'selected' : '' }}>⏳ Terlama</option>
                                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>🔥 Terbaru</option>
                                </select>

                                <button type="submit" class="px-8 py-3 bg-[#FFC232] hover:bg-[#e6ae2b] text-gray-900 font-black rounded-xl transition-all shadow-md shadow-[#FFC232]/20 active:scale-95 text-sm tracking-wide flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                    FILTER
                                </button>
                            </div>
                        </div>

                        {{-- Reset (Desktop Right) --}}
                        <div class="hidden lg:flex lg:col-span-4 xl:col-span-3 justify-end pointer-events-auto">
                            @if(request()->anyFilled(['search', 'category', 'start_date', 'end_date']))
                                <a href="{{ route('cx.history') }}" class="px-5 py-3 bg-gray-50 hover:bg-gray-100 text-gray-400 font-bold rounded-xl transition-all flex items-center justify-center gap-2 border border-gray-100 group" title="Reset Filter">
                                    <svg class="w-4 h-4 transition-transform group-hover:rotate-180 duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span class="text-xs uppercase tracking-widest">Reset</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Row 2: Secondary Filters --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-4 pt-4 border-t border-gray-50">
                        {{-- Category --}}
                        <div class="relative group">
                            <label class="text-[9px] uppercase font-black tracking-[0.2em] text-[#22AF85] absolute -top-2 left-3 bg-white px-2 z-10">Kategori</label>
                            <select name="category" class="w-full py-3 pl-4 pr-10 bg-gray-50/50 border border-gray-100 rounded-xl text-xs font-bold text-gray-500 focus:bg-white focus:ring-2 focus:ring-[#22AF85]/20 focus:border-[#22AF85] hover:border-[#22AF85]/30 transition-all appearance-none" style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22 fill=%22none%22 viewBox=%220 0 20 20%22%3E%3Cpath stroke=%22%2322AF85%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22m6 8 4 4 4-4%22%2F%3E%3C%2Fsvg%3E'); background-position: right 0.75rem center; background-repeat: no-repeat; background-size: 1.1rem auto;">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date Range --}}
                        <div class="md:col-span-2 flex items-center gap-3">
                            <div class="relative flex-grow group">
                                <label class="text-[9px] uppercase font-black tracking-[0.2em] text-[#22AF85] absolute -top-2 left-3 bg-white px-2 z-10 font-bold">Mulai</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                                       class="w-full py-3 bg-gray-50/50 border border-gray-100 rounded-xl text-xs font-bold text-gray-500 focus:bg-white focus:ring-2 focus:ring-[#22AF85]/20 focus:border-[#22AF85] hover:border-[#22AF85]/30 transition-all">
                            </div>
                            <span class="text-gray-200 font-bold text-xs">s/d</span>
                            <div class="relative flex-grow group">
                                <label class="text-[9px] uppercase font-black tracking-[0.2em] text-[#22AF85] absolute -top-2 left-3 bg-white px-2 z-10 font-bold">Selesai</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                                       class="w-full py-3 bg-gray-50/50 border border-gray-100 rounded-xl text-xs font-bold text-gray-500 focus:bg-white focus:ring-2 focus:ring-[#22AF85]/20 focus:border-[#22AF85] hover:border-[#22AF85]/30 transition-all">
                            </div>
                        </div>

                        {{-- Mobile Reset --}}
                        @if(request()->anyFilled(['search', 'category', 'start_date', 'end_date']))
                            <div class="lg:hidden text-center pt-2">
                                <a href="{{ route('cx.history') }}" class="text-[10px] font-black text-gray-300 uppercase tracking-widest hover:text-red-400 transition-colors underline decoration-dotted">Hapus Filter</a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            {{-- History Table --}}
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-100/80 border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-[#22AF85]/10">
                                <th class="px-6 py-4 text-[10px] font-black text-[#22AF85] uppercase tracking-[0.15em] bg-[#22AF85]/[0.03]">SPK & Customer</th>
                                <th class="px-6 py-4 text-[10px] font-black text-[#22AF85] uppercase tracking-[0.15em] bg-[#22AF85]/[0.03]">Kendala & Kategori</th>
                                <th class="px-6 py-4 text-[10px] font-black text-[#22AF85] uppercase tracking-[0.15em] bg-[#22AF85]/[0.03]">Resolusi & Jawaban</th>
                                <th class="px-6 py-4 text-[10px] font-black text-[#22AF85] uppercase tracking-[0.15em] bg-[#22AF85]/[0.03]">Time & Resolver</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($issues as $issue)
                                <tr class="hover:bg-[#22AF85]/[0.02] transition-colors group">
                                    {{-- SPK & Customer --}}
                                    <td class="px-6 py-5 align-top">
                                        <div class="flex flex-col gap-1.5">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-[#22AF85]/10 text-[#22AF85] text-[10px] font-black border border-[#22AF85]/15 w-fit tracking-wide">
                                                {{ $issue->workOrder->spk_number ?? $issue->spk_number }}
                                            </span>
                                            <div class="font-bold text-gray-800 text-sm capitalize leading-tight">{{ $issue->workOrder->customer_name ?? $issue->customer_name }}</div>
                                            <div class="text-[10px] text-gray-400 font-bold tracking-wider">{{ $issue->workOrder->customer_phone ?? $issue->customer_phone }}</div>
                                        </div>
                                    </td>
                                    {{-- Kendala & Kategori --}}
                                    <td class="px-6 py-5 align-top">
                                        <div class="flex flex-col gap-2">
                                            <span class="inline-flex px-2.5 py-1 rounded-full bg-[#FFC232]/15 text-[#9a7200] text-[9px] font-black uppercase tracking-widest w-fit border border-[#FFC232]/30">
                                                {{ $issue->category }}
                                            </span>
                                            <div class="text-xs text-gray-500 font-medium leading-relaxed border-l-3 border-[#22AF85]/30 pl-3 py-1" style="border-left: 3px solid rgba(34,175,133,0.3)">
                                                "{{ $issue->description }}"
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[9px] text-gray-400 uppercase font-bold tracking-widest">Sumber:</span>
                                                <span class="text-[9px] font-bold text-gray-500 bg-gray-50 px-2 py-0.5 rounded-md border border-gray-100">{{ $issue->source }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Resolusi & Jawaban --}}
                                    <td class="px-6 py-5 align-top">
                                        <div class="p-4 bg-[#22AF85]/[0.04] rounded-xl border border-[#22AF85]/10 relative overflow-hidden group-hover:bg-[#22AF85]/[0.07] transition-all">
                                            {{-- Subtle check icon watermark --}}
                                            <div class="absolute top-2 right-2 opacity-[0.06]">
                                                <svg class="w-10 h-10 text-[#22AF85]" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                            </div>
                                            <div class="text-[9px] font-black text-[#22AF85] mb-2 uppercase tracking-[0.15em] flex items-center gap-2">
                                                <div class="w-1.5 h-1.5 rounded-full bg-[#22AF85]"></div>
                                                Final Jawaban
                                            </div>
                                            
                                            {{-- New: Resolution Type Badge --}}
                                            <div class="mb-3 flex flex-wrap gap-2">
                                                @php
                                                    $resType = $issue->resolution_type;
                                                    
                                                    // Smart detection for "Lanjut" but actually has services
                                                    $hasActualServices = $issue->workOrder ? $issue->workOrder->workOrderServices->whereBetween('created_at', [
                                                        $issue->resolved_at->copy()->subMinutes(60), 
                                                        $issue->resolved_at->copy()->addMinutes(60)
                                                    ])->count() > 0 : false;

                                                    $typeLabel = match($resType) {
                                                        'lanjut' => ($hasActualServices ? 'Lanjut + Tambah Jasa' : 'Lanjut (Resume)'),
                                                        'tambah_jasa' => 'Tambah Jasa',
                                                        'komplain' => 'Komplain',
                                                        'cancel' => 'Cancel Order',
                                                        default => ($hasActualServices ? 'Tambah Jasa' : 'N/A')
                                                    };

                                                    $typeColor = match($resType) {
                                                        'lanjut' => ($hasActualServices ? 'bg-blue-600' : 'bg-[#22AF85]'),
                                                        'tambah_jasa' => 'bg-blue-600',
                                                        'komplain' => 'bg-amber-500',
                                                        'cancel' => 'bg-red-600',
                                                        default => 'bg-gray-400'
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black text-white uppercase tracking-widest {{ $typeColor }} shadow-sm">
                                                    {{ $typeLabel }}
                                                </span>
                                            </div>
                                            <p class="text-xs font-medium text-gray-700 leading-relaxed">{{ $issue->resolution_notes ?: 'Tidak ada catatan resolusi detail.' }}</p>
                                        </div>
                                    </td>
                                    {{-- Time & Resolver --}}
                                    <td class="px-6 py-5 align-top">
                                        <div class="flex flex-col gap-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-[#22AF85]/10 flex items-center justify-center text-[11px] font-black text-[#22AF85] border border-[#22AF85]/15">
                                                    {{ $issue->resolver ? substr($issue->resolver->name, 0, 1) : '?' }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-[9px] text-gray-400 uppercase font-bold tracking-widest leading-none mb-1">Resolved By</span>
                                                    <span class="text-xs font-black text-gray-800 uppercase tracking-tight">{{ $issue->resolver->name ?? 'System' }}</span>
                                                </div>
                                            </div>
                                            <div class="flex flex-col bg-gray-50 p-3 rounded-xl border border-gray-100">
                                                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest leading-none mb-1.5">Waktu Resolusi</span>
                                                <span class="text-[11px] font-black text-gray-700 tracking-tight">{{ $issue->resolved_at ? $issue->resolved_at->format('d M Y • H:i') : '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-20 h-20 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-200 border border-gray-100">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="space-y-1">
                                                <h3 class="text-lg font-black text-gray-800 tracking-tight">Belum Ada Data</h3>
                                                <p class="text-gray-400 text-sm font-medium">Belum ada resolusi yang tersimpan di arsip ini.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($issues->hasPages())
                    <div class="px-6 py-6 bg-gray-50/50 border-t border-gray-100">
                        {{ $issues->links() }}
                    </div>
                @endif
            </div>

            {{-- Knowledge Base Insight Card --}}
            <div class="mt-8 p-6 bg-white rounded-2xl border border-gray-100 shadow-lg shadow-gray-100/80 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-5 text-center md:text-left">
                    <div class="w-14 h-14 bg-[#22AF85] rounded-2xl flex items-center justify-center text-white shadow-lg shadow-[#22AF85]/20 flex-shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-1">Knowledge Base Operasional</h4>
                        <p class="text-xs font-medium text-gray-400 leading-relaxed max-w-lg">Gunakan arsip ini untuk standarisasi jawaban CX dan menjaga kualitas layanan workshop.</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-100 flex-shrink-0">
                    <div class="flex -space-x-2">
                        @php $resolvers = $issues->whereNotNull('resolved_by')->unique('resolved_by')->take(5); @endphp
                        @forelse($resolvers as $iss)
                            <div class="w-8 h-8 rounded-xl bg-[#22AF85]/10 border-2 border-white flex items-center justify-center text-[10px] font-black text-[#22AF85]">
                                {{ substr($iss->resolver->name, 0, 1) }}
                            </div>
                        @empty
                            <div class="w-8 h-8 rounded-xl bg-gray-100 border-2 border-white flex items-center justify-center text-[10px] font-black text-gray-300">?</div>
                        @endforelse
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">Contributors</span>
                        <span class="text-[11px] font-bold text-[#22AF85]">Verified Resolvers</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

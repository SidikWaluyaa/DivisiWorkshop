<x-dynamic-component :component="$layout ?? 'app-layout'">
<div class="p-4 sm:p-6 space-y-6 max-w-7xl mx-auto">
    
    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-3xl shadow-xl">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-teal-900/20 flex-shrink-0">
                📦
            </div>
            <div>
                <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">
                    Logistik <span class="text-[#22AF85]">Manifest Inbound</span>
                </h1>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                    Manajemen pengiriman batch &amp; serah terima sepatu dari Toko/Gudang ke Workshop
                </p>
            </div>
        </div>

        @unless(optional(Auth::user())->role === 'admin_workshop' || optional(Auth::user())->role === 'technician')
        <div class="flex items-center gap-3">
            <a href="{{ route('manifest.create') }}" class="inline-flex items-center px-5 py-2.5 bg-[#FFC232] hover:bg-[#e6af2e] text-slate-950 font-black text-xs rounded-2xl uppercase tracking-wider transition-all shadow-lg shadow-amber-500/20 active:scale-95">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Pengiriman
            </a>
        </div>
        @endunless
    </div>

    {{-- Notifications --}}
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold flex items-center gap-3">
        <span class="text-lg">✅</span>
        <p>{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs font-bold flex items-center gap-3">
        <span class="text-lg">⚠️</span>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    {{-- Filter Tabs & Stats Bar --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('manifest.index', array_filter(['status' => 'SENT', 'mode' => request('mode')])) }}" 
           class="p-5 rounded-3xl border transition-all flex items-center justify-between shadow-lg
           {{ request('status') === 'SENT' ? 'bg-amber-500/10 border-amber-500/40 text-amber-400' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-400 hover:border-slate-700' }}">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold">
                    🚚
                </div>
                <div>
                    <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">Dalam Pengiriman</div>
                    <div class="text-xl font-black text-slate-900 dark:text-white mt-0.5">
                        {{ $countSent ?? $manifests->where('status', 'SENT')->count() }} Batch
                    </div>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-amber-400 text-slate-950 uppercase animate-pulse">SENT</span>
        </a>

        <a href="{{ route('manifest.index', array_filter(['status' => 'RECEIVED', 'mode' => request('mode')])) }}" 
           class="p-5 rounded-3xl border transition-all flex items-center justify-between shadow-lg
           {{ request('status') === 'RECEIVED' ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-400' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-400 hover:border-slate-700' }}">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                    ✅
                </div>
                <div>
                    <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">Sudah Diterima</div>
                    <div class="text-xl font-black text-slate-900 dark:text-white mt-0.5">
                        {{ $countReceived ?? $manifests->where('status', 'RECEIVED')->count() }} Batch
                    </div>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 uppercase">RECEIVED</span>
        </a>

        <a href="{{ route('manifest.index', array_filter(['mode' => request('mode')])) }}" 
           class="p-5 rounded-3xl border transition-all flex items-center justify-between shadow-lg
           {{ !request('status') ? 'bg-teal-500/10 border-teal-500/40 text-teal-400' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-400 hover:border-slate-700' }}">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-teal-500/20 text-teal-400 flex items-center justify-center font-bold">
                    📋
                </div>
                <div>
                    <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">Total Semua Manifest</div>
                    <div class="text-xl font-black text-slate-900 dark:text-white mt-0.5">
                        {{ $countAll ?? $manifests->total() }} Record
                    </div>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-slate-800 text-slate-300 uppercase">ALL</span>
        </a>
    </div>

    {{-- Main Content Table / Cards --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xl overflow-hidden">
        
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
            <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                Daftar Manifest Inbound
            </h2>
            <span class="text-xs font-bold text-slate-400">
                Menampilkan {{ $manifests->count() }} dari {{ $manifests->total() }} Manifest
            </span>
        </div>

        {{-- Desktop Table (≥ 768px) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">No. Manifest</th>
                        <th class="py-4 px-6">Pengirim &amp; Waktu</th>
                        <th class="py-4 px-6 text-center">Jumlah SPK</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200">
                    @forelse($manifests as $manifest)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            {{-- No Manifest --}}
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="font-mono font-black text-amber-500 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/50 px-3 py-1 rounded-xl inline-flex items-center gap-1.5 shadow-sm">
                                    <span>📄 {{ $manifest->manifest_number }}</span>
                                </div>
                            </td>

                            {{-- Pengirim & Waktu --}}
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="font-black text-slate-900 dark:text-white leading-tight">
                                    {{ $manifest->dispatcher->name ?? 'Gudang/Toko Utama' }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-semibold mt-0.5">
                                    {{ $manifest->created_at->format('d M Y, H:i') }}
                                </div>
                            </td>

                            {{-- Jumlah SPK --}}
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <span class="px-3 py-1 rounded-xl text-xs font-black bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-teal-600 dark:text-teal-300">
                                    {{ $manifest->work_orders_count }} SPK
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                @if($manifest->status === 'SENT')
                                    <span class="px-3 py-1 rounded-xl text-[10px] font-black bg-amber-500/20 text-amber-600 dark:text-amber-300 border border-amber-500/30 uppercase tracking-wider inline-flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                                        Dalam Pengiriman
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-xl text-[10px] font-black bg-emerald-500/20 text-emerald-600 dark:text-emerald-300 border border-emerald-500/30 uppercase tracking-wider inline-flex items-center gap-1">
                                        ✓ Diterima
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('manifest.show', array_filter([$manifest->id, 'mode' => request('mode')])) }}" 
                                       class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-700 transition-all">
                                        Detail
                                    </a>

                                    @if($manifest->status === 'SENT')
                                        <a href="{{ route('manifest.receive', array_filter([$manifest->id, 'mode' => request('mode')])) }}" 
                                           class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-emerald-600 hover:from-amber-400 hover:to-emerald-500 text-slate-950 font-black text-xs shadow-lg shadow-amber-900/20 active:scale-95 transition-all">
                                            📥 Terima Inbound
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 font-bold">
                                Tidak ada manifest inbound yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards (< 768px) --}}
        <div class="md:hidden divide-y divide-slate-200 dark:divide-slate-800">
            @forelse($manifests as $manifest)
                <div class="p-4 space-y-3 bg-white dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <span class="font-mono font-black text-amber-500 dark:text-amber-400 text-xs bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 px-2.5 py-1 rounded-xl">
                            📄 {{ $manifest->manifest_number }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-xl text-[10px] font-black bg-slate-100 dark:bg-slate-800 text-teal-600 dark:text-teal-300">
                            {{ $manifest->work_orders_count }} SPK
                        </span>
                    </div>

                    <div class="text-xs">
                        <div class="font-bold text-slate-900 dark:text-white">Pengirim: {{ $manifest->dispatcher->name ?? 'Gudang/Toko Utama' }}</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $manifest->created_at->format('d M Y, H:i') }}</div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800/60">
                        <div>
                            @if($manifest->status === 'SENT')
                                <span class="px-2 py-0.5 rounded-lg text-[9px] font-black bg-amber-500/20 text-amber-400 border border-amber-500/30 uppercase">
                                    Dalam Pengiriman
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-lg text-[9px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 uppercase">
                                    ✓ Diterima
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('manifest.show', array_filter([$manifest->id, 'mode' => request('mode')])) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700">
                                Detail
                            </a>
                            @if($manifest->status === 'SENT')
                                <a href="{{ route('manifest.receive', array_filter([$manifest->id, 'mode' => request('mode')])) }}" class="px-3.5 py-1.5 rounded-xl bg-amber-500 text-slate-950 font-black text-xs shadow-md">
                                    📥 Terima
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs font-bold">
                    Tidak ada manifest inbound yang ditemukan.
                </div>
            @endforelse
        </div>

        @if($manifests->hasPages())
        <div class="p-4 bg-slate-50 dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800">
            {{ $manifests->links() }}
        </div>
        @endif
    </div>

</div>
</x-dynamic-component>

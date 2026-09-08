<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('cs.dashboard') }}" class="font-bold tracking-wider text-teal-100 hover:text-white uppercase text-xs transition">Divisi CS</a>
            <span class="text-white/40">/</span>
            <span class="font-black text-white text-base tracking-wide">{{ __('Closing Leads') }}</span>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Executive Glassmorphism Hero Header --}}
            <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-white via-white/95 to-emerald-50/40 p-6 sm:p-8 border border-white/80 shadow-[0_20px_50px_rgba(34,175,133,0.08)] mb-8 backdrop-blur-xl">
                {{-- Decorative background glow --}}
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-20 -bottom-20 w-72 h-72 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    {{-- Left info --}}
                    <div class="flex items-start sm:items-center gap-4 sm:gap-5">
                        <div class="relative shrink-0">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 via-[#22AF85] to-teal-400 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30 ring-4 ring-white">
                                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500 border-2 border-white"></span>
                            </span>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wider uppercase bg-emerald-100 text-emerald-800 border border-emerald-200/60 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Stage 4: Closing & SPK Generation
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold text-gray-500 bg-gray-100 border border-gray-200/60 inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                                </span>
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                                Closing Leads Pipeline
                            </h1>
                            <p class="text-xs sm:text-sm text-gray-500 font-medium mt-0.5">
                                Data penawaran yang telah disetujui (Deal) dan siap diterbitkan Surat Perintah Kerja (SPK) resmi.
                            </p>
                        </div>
                    </div>

                    {{-- Right navigation / shortcuts --}}
                    <div class="flex flex-wrap items-center gap-3 lg:self-center">
                        <a href="{{ route('cs.dashboard') }}" 
                           class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-black text-xs uppercase tracking-widest text-gray-700 bg-white border border-gray-200 shadow-sm hover:bg-gray-50 hover:-translate-y-0.5 active:translate-y-0 transition-all">
                            <span>⬅️ CS Hub Dashboard</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100">
                <div class="p-8 border-b border-gray-50 flex flex-wrap items-center justify-between gap-4">
                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Final Stage Conversion</h3>
                    
                    <form action="{{ route('cs.leads.closing') }}" method="GET" class="flex-1 max-w-md relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama/HP..." class="w-full pl-12 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#22AF85] font-bold transition-all">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                <th class="px-8 py-4">Customer</th>
                                <th class="px-8 py-4">SPK Info</th>
                                <th class="px-8 py-4 text-center">Payment Status</th>
                                <th class="px-8 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($leads as $lead)
                            <tr class="hover:bg-gray-50/50 transition group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600 font-black text-xs">
                                            {{ substr($lead->customer_name ?? 'C', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-black text-gray-900">{{ $lead->customer_name ?? 'Guest' }}</div>
                                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $lead->customer_phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @if($lead->spk)
                                        <div class="text-sm font-bold text-gray-700">{{ $lead->spk->spk_number }}</div>
                                        <div class="text-[9px] text-gray-400 mt-1 uppercase font-black">Total: Rp {{ number_format($lead->spk->total_price, 0, ',', '.') }}</div>
                                    @else
                                        <div class="text-sm text-gray-400 italic">No SPK Generated Yet</div>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @if($lead->spk)
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest leading-none {{ $lead->spk->dp_status_badge_class }}">
                                            {{ $lead->spk->dp_status }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2 text-right">
                                        <a href="{{ route('cs.leads.show', $lead->id) }}" class="p-2 bg-gray-50 text-gray-400 hover:bg-green-50 hover:text-green-600 rounded-xl transition shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        <form action="{{ route('cs.leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-600 rounded-xl transition shadow-sm" title="Hapus Lead">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.859L4.764 7M16 17v-4m-4 4v-4m-4 4v-4m-6-6h14m2 0a2 2 0 002-2V7a2 2 0 00-2 2H3a2 2 0 00-2 2v.17c0 1.1.9 2 2 2h1M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <p class="font-black text-gray-400 uppercase tracking-widest text-xs">Ayo selesaikan negosiasi untuk closing!</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-8 bg-gray-50 border-t border-gray-100">
                    {{ $leads->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

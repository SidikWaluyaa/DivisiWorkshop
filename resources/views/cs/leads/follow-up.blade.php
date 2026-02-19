<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-lg bg-orange-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tight uppercase">
                        {{ __('Follow-up Konsultasi') }}
                    </h2>
                    <p class="text-xs font-bold text-gray-500 tracking-widest uppercase opacity-70">Lead Warm — Siap Ditindaklanjuti 🔥</p>
                </div>
            </div>
            <a href="{{ route('cs.dashboard') }}" class="bg-gray-100 text-gray-600 px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest shadow-sm hover:bg-gray-200 transition">
                ⬅️ Dashboard Hub
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100">
                <div class="p-8 border-b border-gray-50 flex flex-wrap items-center justify-between gap-4">
                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Follow-up Pipeline</h3>
                    
                    <form action="{{ route('cs.leads.follow-up') }}" method="GET" class="flex-1 max-w-md relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama/HP..." class="w-full pl-12 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-orange-500 font-bold transition-all">
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
                                <th class="px-8 py-4">Terakhir Update</th>
                                <th class="px-8 py-4 text-center">Status</th>
                                <th class="px-8 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($leads as $lead)
                            <tr class="hover:bg-gray-50/50 transition group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 font-black text-xs">
                                            {{ substr($lead->customer_name ?? 'C', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-black text-gray-900">{{ $lead->customer_name ?? 'Guest' }}</div>
                                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $lead->customer_phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-sm font-bold text-gray-700">{{ $lead->updated_at->diffForHumans() }}</div>
                                    <div class="text-[9px] text-gray-400 mt-1 uppercase font-black">PIC: {{ $lead->cs->name ?? '-' }}</div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest leading-none bg-orange-100 text-orange-700">
                                        FOLLOW-UP 🔥
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('cs.leads.show', $lead->id) }}" class="p-2 bg-gray-50 text-gray-400 hover:bg-orange-50 hover:text-orange-600 rounded-xl transition shadow-sm">
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
                                    <p class="font-black text-gray-400 uppercase tracking-widest text-xs">Belum ada lead di Follow-up. Pindahkan dari Konsultasi! 🎯</p>
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

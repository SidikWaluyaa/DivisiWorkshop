<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-lg bg-yellow-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tight uppercase">
                        {{ __('Konsultasi Leads') }}
                    </h2>
                    <p class="text-xs font-bold text-gray-500 tracking-widest uppercase opacity-70">Data Lead Dalam Tahap Negosiasi</p>
                </div>
            </div>
            <a href="{{ route('cs.dashboard') }}" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest shadow-sm transition">
                ⬅️ Dashboard Hub
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen" x-data="{ showBulkDelete: false }">
        <!-- Modal Bulk Delete -->
        <div x-show="showBulkDelete" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             x-on:open-bulk-delete.window="showBulkDelete = true"
             x-cloak>
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showBulkDelete = false"></div>

            <!-- Modal Wrapper -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100"
                     x-data="{
                         month: '{{ date('n') }}',
                         year: '{{ date('Y') }}',
                         confirmText: '',
                         targetCount: 0,
                         loading: false,
                         
                         async updateCount() {
                             this.loading = true;
                             try {
                                 let response = await fetch(`/cs/leads-konsultasi/bulk-delete/count?month=${this.month}&year=${this.year}`);
                                 let data = await response.json();
                                 this.targetCount = data.count;
                             } catch (error) {
                                 console.error('Error fetching count:', error);
                                 this.targetCount = 0;
                             } finally {
                                 this.loading = false;
                             }
                         }
                     }"
                     x-init="updateCount()"
                     @open-bulk-delete.window="updateCount()">
                    
                    <div class="bg-white px-6 pb-6 pt-8 sm:p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-red-50 text-red-600 shadow-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.859L4.764 7M16 17v-4m-4 4v-4m-4 4v-4m-6-6h14m2 0a2 2 0 002-2V7a2 2 0 00-2 2H3a2 2 0 00-2 2v.17c0 1.1.9 2 2 2h1M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" /></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Bulk Delete by Period</h3>
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Hapus data lead Konsultasi secara masal</p>
                            </div>
                        </div>

                        <form action="{{ route('cs.leads.bulk-delete') }}" method="POST" class="space-y-4">
                            @csrf
                            @method('DELETE')

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan</label>
                                    <select name="month" x-model="month" @change="updateCount()" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm font-bold bg-gray-50 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tahun</label>
                                    <select name="year" x-model="year" @change="updateCount()" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm font-bold bg-gray-50 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                        @for($y = date('Y'); $y >= 2024; $y--)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <!-- Live Count Indicator -->
                            <div class="p-4 rounded-2xl border transition-colors duration-300" 
                                 :class="targetCount > 0 ? 'bg-red-50/50 border-red-100 text-red-800' : 'bg-gray-50 border-gray-100 text-gray-500'">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl animate-bounce" x-show="!loading && targetCount > 0">⚠️</span>
                                    <span class="text-xl" x-show="!loading && targetCount === 0">ℹ️</span>
                                    <svg class="animate-spin h-5 w-5 text-red-500" x-show="loading" fill="none" viewBox="0 0 24 24" x-cloak>
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <div class="text-xs font-bold leading-relaxed">
                                        <span x-show="loading">Menghitung jumlah data...</span>
                                        <span x-show="!loading && targetCount > 0" x-cloak>
                                            Menemukan <span class="text-red-600 font-black text-sm" x-text="targetCount"></span> data lead Konsultasi pada periode ini yang akan terhapus.
                                        </span>
                                        <span x-show="!loading && targetCount === 0" x-cloak>
                                            Tidak ada data lead Konsultasi pada periode ini.
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Safety Confirmation -->
                            <div x-show="targetCount > 0" x-transition x-cloak>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                    Ketik frasa keselamatan <span class="text-red-600 font-black">HAPUS PERIODE</span> untuk konfirmasi:
                                </label>
                                <input type="text" name="confirm_text" x-model="confirmText" 
                                       placeholder="HAPUS PERIODE" 
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm font-mono font-bold text-center bg-gray-50 focus:ring-2 focus:ring-red-500">
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-50">
                                <button type="button" @click="showBulkDelete = false" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 text-xs font-bold uppercase tracking-widest transition active:scale-95">
                                    Batal
                                </button>
                                <button type="submit" 
                                        :disabled="confirmText.toUpperCase() !== 'HAPUS PERIODE' || targetCount === 0"
                                        :class="(confirmText.toUpperCase() !== 'HAPUS PERIODE' || targetCount === 0) ? 'opacity-50 cursor-not-allowed bg-red-600 text-white' : 'bg-red-600 text-white hover:bg-red-700 shadow-md active:scale-95'"
                                        class="px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition">
                                    Konfirmasi Hapus
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100">
                <div class="p-8 border-b border-gray-50 flex flex-wrap items-center justify-between gap-4">
                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Active Negotiation Pipeline</h3>
                    
                    <div class="flex items-center gap-3 flex-1 justify-end max-w-2xl">
                        <form action="{{ route('cs.leads.konsultasi') }}" method="GET" class="flex-1 max-w-md relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama/HP..." class="w-full pl-12 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-yellow-500 font-bold transition-all">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </div>
                        </form>
                        
                        @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
                            <button @click="$dispatch('open-bulk-delete')" 
                                    class="bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 px-5 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition flex items-center gap-2 h-[44px] shadow-sm">
                                🗑️ Bulk Delete
                            </button>
                        @endif
                    </div>
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
                                        <div class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-600 font-black text-xs">
                                            {{ substr($lead->customer_name ?? 'C', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-black text-gray-900">{{ $lead->customer_name ?? 'Guest' }}</div>
                                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $lead->customer_phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-sm font-bold text-gray-700">{{ $lead->updated_at->translatedFormat('d F Y') }}</div>
                                    <div class="text-[10px] font-bold text-gray-400 mt-0.5">({{ $lead->updated_at->diffForHumans() }})</div>
                                    <div class="text-[9px] text-gray-400 mt-1.5 uppercase font-black">PIC: {{ $lead->cs->name ?? '-' }}</div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest leading-none {{ $lead->status_badge_class }}">
                                        {{ $lead->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('cs.leads.show', $lead->id) }}" class="p-2 bg-gray-50 text-gray-400 hover:bg-yellow-50 hover:text-yellow-600 rounded-xl transition shadow-sm">
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
                                    <p class="font-black text-gray-400 uppercase tracking-widest text-xs">Kosong. Mari tarik data dari Greeting!</p>
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

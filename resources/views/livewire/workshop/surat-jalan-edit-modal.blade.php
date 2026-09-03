<div>
    {{-- Trigger Button --}}
    @if(auth()->user()?->email === 'admin@workshop.com')
        <button type="button" 
                wire:click="openModal"
                class="px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black rounded-xl text-xs shadow-md shadow-amber-500/20 transition-all active:scale-95 flex items-center gap-1.5 border border-amber-400 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            <span>⚡ Edit Muatan SPK (Super Admin)</span>
        </button>
    @endif

    {{-- Edit Modal Teleported to Body --}}
    @if($showModal && $suratJalan)
    <template x-teleport="body">
        <div class="fixed inset-0 z-[999999] overflow-y-auto flex items-center justify-center p-4 sm:p-6 bg-slate-950/80 backdrop-blur-md animate-fadeIn font-sans">
            <div class="bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100 max-w-4xl w-full max-h-[90vh] flex flex-col relative z-10">
                {{-- Modal Header --}}
                <div class="p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white flex justify-between items-center relative overflow-hidden border-b border-amber-400/20 shrink-0">
                    <div class="relative z-10 flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-amber-500/20 border border-amber-400/30 flex items-center justify-center text-amber-400 shadow-sm shrink-0 font-black text-lg">
                            👑
                        </div>
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-400/10 rounded-full text-[9px] font-black uppercase tracking-widest text-amber-300 border border-amber-400/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                SUPER ADMIN LIVE OVERRIDE
                            </div>
                            <h2 class="text-xl font-black text-white tracking-tight leading-tight">
                                Edit Muatan Surat Jalan #{{ $suratJalan->nomor_surat }}
                            </h2>
                        </div>
                    </div>

                    <button type="button" wire:click="closeModal" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white flex items-center justify-center transition-all cursor-pointer">
                        ✕
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 sm:p-7 space-y-6 overflow-y-auto flex-1">
                    {{-- 1. Metadata Info --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span>📝</span> Catatan &amp; Waktu Pengiriman
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Tanggal &amp; Jam Kirim</label>
                                <input type="datetime-local" wire:model="dikirim_at" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold bg-white focus:ring-2 focus:ring-amber-400">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Catatan Pengiriman</label>
                                <input type="text" wire:model="catatan" placeholder="Catatan khusus driver / workshop..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold bg-white focus:ring-2 focus:ring-amber-400">
                            </div>
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="button" wire:click="saveMetadata" class="px-4 py-1.5 bg-slate-900 hover:bg-slate-800 text-amber-300 rounded-xl text-xs font-black transition-all cursor-pointer shadow-xs">
                                💾 Simpan Catatan
                            </button>
                        </div>
                    </div>

                    {{-- 2. Add New SPK to this Surat Jalan --}}
                    <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-200/80 space-y-3">
                        <div class="text-xs font-black text-emerald-900 uppercase tracking-wider flex items-center gap-2">
                            <span>➕</span> Tambahkan SPK Baru ke Surat Jalan Ini
                        </div>
                        <div class="relative">
                            <input type="text" 
                                   wire:model.live.debounce.300ms="searchSpk" 
                                   placeholder="Ketik Nomor SPK / Nama Pelanggan / Brand Sepatu..." 
                                   class="w-full pl-4 pr-4 py-2.5 rounded-xl border border-emerald-300 text-xs font-bold bg-white focus:ring-2 focus:ring-emerald-500">
                        </div>

                        {{-- Search Results --}}
                        @if(!empty($searchSpk))
                            <div class="bg-white rounded-xl border border-emerald-200 shadow-md divide-y divide-slate-100 max-h-48 overflow-y-auto">
                                @forelse($availableWorkOrders as $avWo)
                                    <div class="p-3 flex items-center justify-between hover:bg-emerald-50/50 transition-colors">
                                        <div>
                                            <div class="text-xs font-black text-slate-900">{{ $avWo->spk_number }}</div>
                                            <div class="text-[11px] font-semibold text-slate-500">{{ $avWo->customer_name }} • {{ $avWo->shoe_brand }} ({{ $avWo->shoe_type }})</div>
                                            <div class="text-[9px] font-bold text-emerald-600">Status: {{ $avWo->status->value ?? $avWo->status }}</div>
                                        </div>
                                        <button type="button" 
                                                wire:click="addSpk({{ $avWo->id }})" 
                                                class="px-3 py-1.5 bg-[#22AF85] hover:bg-emerald-600 text-white rounded-xl text-xs font-black transition-all cursor-pointer active:scale-95">
                                            + Masukkan Muatan
                                        </button>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-slate-400 text-xs italic font-bold">
                                        Tidak ada SPK yang cocok dengan kata kunci "{{ $searchSpk }}".
                                    </div>
                                @endforelse
                            </div>
                        @endif
                    </div>

                    {{-- 3. Current Load List (Daftar Muatan Saat Ini) --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">
                                📦 Daftar Muatan SPK Saat Ini (Total: {{ $suratJalan->items->count() }} Unit)
                            </h4>
                        </div>

                        <div class="rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                            <table class="min-w-full divide-y divide-slate-100 text-left">
                                <thead class="bg-slate-100 text-slate-700 text-[10px] font-black uppercase tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3">No</th>
                                        <th class="px-4 py-3">Nomor SPK &amp; Pelanggan</th>
                                        <th class="px-4 py-3">Sepatu</th>
                                        <th class="px-4 py-3">Layanan / Material</th>
                                        <th class="px-4 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs bg-white">
                                    @forelse($suratJalan->items as $idx => $item)
                                        @php $wo = $item->workOrder; @endphp
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-slate-400">{{ $idx + 1 }}</td>
                                            <td class="px-4 py-3">
                                                <div class="font-black text-slate-900 font-mono text-[11px]">{{ $wo?->spk_number ?? '-' }}</div>
                                                <div class="text-[10px] text-slate-500 font-medium">{{ $wo?->customer_name ?? '-' }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="font-bold text-slate-800">{{ $wo?->shoe_brand }}</span> {{ $wo?->shoe_type }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-[10px] font-bold text-indigo-600">{{ $wo?->services?->count() ?? 0 }} Layanan Jasa</div>
                                                <div class="text-[10px] font-bold text-emerald-600">{{ $wo?->materials?->count() ?? 0 }} Bahan Baku</div>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <button type="button" 
                                                        wire:click="removeSpk({{ $item->id }})" 
                                                        wire:confirm="Keluarkan SPK '{{ $wo?->spk_number }}' dari Surat Jalan ini?" 
                                                        class="px-3 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg text-xs font-black transition-all border border-rose-200 cursor-pointer">
                                                    ✕ Keluarkan
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-6 text-center text-slate-400 italic text-xs font-bold">
                                                Muatan Surat Jalan kosong.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="p-5 bg-slate-50 border-t border-slate-100 flex justify-between items-center gap-3 shrink-0">
                    <button type="button" 
                            wire:click="deleteEmptySuratJalan" 
                            wire:confirm="PERINGATAN: Apakah Anda yakin ingin membatalkan & menghapus dokumen Surat Jalan ini?" 
                            class="px-4 py-2 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl text-xs font-black transition-all border border-rose-200 cursor-pointer flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        <span>🗑️ Batalkan / Hapus Surat Jalan Ini</span>
                    </button>

                    <button type="button" wire:click="closeModal" class="px-5 py-2.5 bg-slate-900 text-white hover:bg-slate-800 rounded-xl text-xs font-black transition-all cursor-pointer shadow-md">
                        Selesai &amp; Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>
    @endif
</div>

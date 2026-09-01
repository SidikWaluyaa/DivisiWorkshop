<x-workshop-pwa-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-slate-800 dark:text-white leading-tight flex items-center gap-2">
            <span>📄</span> Buat Surat Jalan Workshop
        </h2>
    </x-slot>

    <div class="py-8 bg-slate-50/50 min-h-screen" x-data="{ 
        selectAll: false, 
        search: '', 
        toggleAll() {
            const checkboxes = document.querySelectorAll('.spk-checkbox');
            checkboxes.forEach(cb => {
                if (cb.offsetParent !== null) cb.checked = this.selectAll;
            });
        } 
    }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Top Navigation & Return Button --}}
            <div class="mb-6 flex items-center justify-between">
                <a href="{{ route('surat-jalan.index', ['jenis' => $jenis]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:text-slate-950 rounded-2xl text-xs font-black shadow-sm border border-slate-200/80 dark:border-slate-700 hover:border-[#FFC232] transition-all active:scale-95">
                    <svg class="w-4 h-4 text-slate-800 dark:text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>← Kembali ke Surat Jalan</span>
                </a>

                <span class="px-3 py-1 bg-slate-900 text-[#FFC232] font-black text-[10px] rounded-xl uppercase tracking-wider shadow-sm">
                    Penerbitan Surat Jalan
                </span>
            </div>

            <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 dark:border-slate-700">
                <form action="{{ route('surat-jalan.store') }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black uppercase text-slate-400 tracking-wider mb-2">Nomor Surat Jalan (Otomatis)</label>
                                <div class="px-4 py-3 bg-slate-100 dark:bg-slate-700 rounded-2xl text-sm font-mono font-bold text-slate-700 dark:text-white border border-slate-200 dark:border-slate-600 flex items-center justify-between">
                                    <span>{{ $nomorSurat }}</span>
                                    <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 text-[10px] font-black rounded-lg">AUTO</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase text-indigo-600 tracking-wider mb-2">Rute Serah-Terima Tahap</label>
                                <select name="jenis_serah_terima" class="w-full rounded-2xl border-indigo-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm font-bold p-3.5 focus:ring-indigo-500" onchange="window.location.href='{{ route('surat-jalan.create') }}?jenis=' + this.value">
                                    <option value="sortir_to_produksi" {{ $jenis == 'sortir_to_produksi' ? 'selected' : '' }}>🔨 1. Sortir ➔ Produksi (Siap Pengerjaan Produksi)</option>
                                    <option value="produksi_to_post_qc" {{ $jenis == 'produksi_to_post_qc' ? 'selected' : '' }}>✅ 2. Produksi ➔ QC (Selesai Produksi Siap QC)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <label class="text-xs font-black uppercase text-slate-700 dark:text-slate-200 tracking-wider flex items-center gap-2">
                                    <span>📦</span> Pilih SPK yang Diserah-Terimakan 
                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] rounded-full font-bold">Total: {{ count($availableOrders) }} SPK</span>
                                </label>

                                @if(count($availableOrders) > 0)
                                    <label class="flex items-center gap-2 text-xs font-bold text-indigo-600 cursor-pointer hover:text-indigo-800">
                                        <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                        <span>Pilih Semua SPK</span>
                                    </label>
                                @endif
                            </div>

                            @if(count($availableOrders) > 0)
                                <div class="mb-3">
                                    <input type="text" x-model="search" placeholder="Cari SPK, Nama Customer, atau Merk Sepatu..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-400">
                                </div>
                            @endif

                            <div class="max-h-72 overflow-y-auto border border-slate-200 dark:border-slate-700 rounded-2xl p-3 space-y-2 bg-slate-50/50 custom-scroll">
                                @forelse ($availableOrders as $wo)
                                    <label x-show="!search || '{{ strtolower($wo->spk_number . ' ' . $wo->customer_name . ' ' . $wo->shoe_brand . ' ' . $wo->shoe_type) }}'.includes(search.toLowerCase())" 
                                           class="flex items-center justify-between p-3.5 bg-white dark:bg-slate-700 rounded-xl border border-slate-100 dark:border-slate-600 hover:border-indigo-300 transition-all cursor-pointer group shadow-sm">
                                        <div class="flex items-center gap-3.5">
                                            <input type="checkbox" name="work_order_ids[]" value="{{ $wo->id }}" class="spk-checkbox rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-black text-xs text-slate-800 dark:text-white group-hover:text-indigo-600 transition-colors">{{ $wo->spk_number }}</span>
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ strtolower($wo->priority) == 'urgent' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600' }}">
                                                        {{ $wo->priority ?? 'NORMAL' }}
                                                    </span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 font-bold mt-0.5">
                                                    {{ $wo->customer_name }} • <span class="text-slate-700 dark:text-slate-300">{{ $wo->shoe_brand }} {{ $wo->shoe_type }}</span>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="text-right text-[10px] text-slate-400 font-bold">
                                            {{ $wo->created_at ? $wo->created_at->format('d M Y') : '-' }}
                                        </div>
                                    </label>
                                @empty
                                    <div class="text-center py-8">
                                        <span class="text-3xl block mb-2">📭</span>
                                        <p class="text-xs font-bold text-slate-400">Tidak ada SPK yang siap diserah-terimakan pada rute ini saat ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-slate-400 tracking-wider mb-2">Catatan Serah-Terima (Opsional)</label>
                            <textarea name="catatan" rows="3" class="w-full rounded-2xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-xs font-bold p-4 focus:ring-indigo-500" placeholder="Contoh: Diserahkan fisik sepatu 5 pasang oleh Admin Sortir ke Penanggung Jawab Produksi..."></textarea>
                        </div>

                        <div class="flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700 pt-6">
                            <a href="{{ route('surat-jalan.index', ['jenis' => $jenis]) }}" class="px-6 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold hover:bg-slate-200 transition-all">Batal</a>
                            <button type="submit" class="px-8 py-3 bg-[#22AF85] hover:bg-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20 transition-all active:scale-95 flex items-center gap-2">
                                <span>📄 Terbitkan Surat Jalan</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-workshop-pwa-layout>

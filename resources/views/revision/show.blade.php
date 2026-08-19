<x-workshop-pwa-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('revision.index') }}" class="p-2 bg-white/10 hover:bg-white/20 rounded-full transition-colors text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h2 class="font-black text-2xl text-white leading-tight tracking-tight">
                    {{ __('Detail Revisi Teknik') }}
                </h2>
            </div>
            <div class="flex items-center gap-3">
                 <span class="bg-white/20 px-4 py-1.5 rounded-full text-sm font-bold font-mono border border-white/30 tracking-wider text-white">
                    {{ $revision->workOrder->spk_number }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- MAIN CONTENT: PROBLEM DESCRIPTION --}}
                <div class="lg:col-span-2 space-y-8">
                    {{-- Problem Card --}}
                    <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-10">
                            <h3 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em] mb-6">Deskripsi Masalah & Komplain</h3>
                            <div class="prose dark:prose-invert max-w-none">
                                <div class="bg-red-50/50 dark:bg-red-900/5 rounded-3xl p-8 border border-red-100/50 dark:border-red-900/10">
                                    <p class="text-xl text-gray-700 dark:text-gray-300 leading-relaxed italic font-medium">
                                        "{{ $revision->description }}"
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($revision->photo_urls && count($revision->photo_urls) > 0)
                        <div class="px-10 pb-10">
                            <h3 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em] mb-6">Foto Dokumentasi Masalah ({{ count($revision->photo_urls) }})</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($revision->photo_urls as $url)
                                <div class="rounded-[2rem] overflow-hidden border-4 border-gray-50 dark:border-gray-700 shadow-inner group relative aspect-video bg-gray-100 dark:bg-gray-900">
                                    <img src="{{ $url }}" 
                                         alt="Foto Revisi" 
                                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                                        <a href="{{ $url }}" target="_blank" class="bg-white text-gray-900 px-4 py-2 rounded-full font-black uppercase text-[10px] tracking-widest flex items-center gap-2 shadow-2xl transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Buka Ukuran Penuh
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- SIDEBAR: UNIT & REPORTER INFO --}}
                <div class="space-y-8">
                    {{-- Quick Action Card --}}
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-[2rem] p-8 border border-gray-100 dark:border-gray-700">
                        <h3 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-6">Status Revisi</h3>
                        
                        @if($revision->status === 'FINISHED')
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-2xl">
                                    ✅
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Saat Ini</p>
                                    <p class="text-lg font-black text-emerald-600 uppercase">SELESAI DIREVISI</p>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800 text-xs space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 dark:text-gray-400 font-semibold">Diselesaikan Oleh:</span>
                                    <span class="font-black text-emerald-800 dark:text-emerald-300">{{ $revision->resolver->name ?? 'Admin Workshop' }}</span>
                                </div>
                                @if($revision->finished_at)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 dark:text-gray-400 font-semibold">Waktu Selesai:</span>
                                    <span class="font-bold text-emerald-700 dark:text-emerald-400">{{ $revision->finished_at->translatedFormat('d M Y H:i') }}</span>
                                </div>
                                @endif
                            </div>
                        @else
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900/30 text-red-600 flex items-center justify-center text-2xl">
                                    ⏳
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Saat Ini</p>
                                    <p class="text-lg font-black text-red-600 uppercase">SEDANG DIREVISI</p>
                                </div>
                            </div>

                            <form action="{{ route('revision.complete', $revision->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-2xl py-5 font-black uppercase text-xs tracking-[0.2em] shadow-xl shadow-green-200 dark:shadow-none hover:scale-[1.02] active:scale-[0.98] transition-all">
                                    Selesai Revisi ✅
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Workshop Loss Card (Always visible & Editable) --}}
                    <div x-data="{ editingLoss: false }" class="bg-gradient-to-br from-rose-50 to-amber-50 dark:from-rose-950/30 dark:to-amber-950/20 shadow-xl rounded-[2rem] p-8 border border-rose-200/80 dark:border-rose-900/40 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">💰</span>
                                <h3 class="text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-[0.2em]">Estimasi Kerugian Workshop</h3>
                            </div>
                            <button @click="editingLoss = !editingLoss" class="px-3 py-1 bg-white/80 dark:bg-gray-800 text-rose-600 dark:text-rose-400 hover:bg-rose-100 rounded-xl text-[10px] font-black uppercase tracking-wider border border-rose-200 shadow-sm transition-all flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                <span x-text="editingLoss ? 'Batal' : 'Edit Kerugian'"></span>
                            </button>
                        </div>

                        <!-- Readonly View -->
                        <div x-show="!editingLoss" class="space-y-3">
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 block font-semibold">Nominal Kerugian:</span>
                                <span class="text-xl font-black text-rose-600 dark:text-rose-400">
                                    {{ ($revision->loss_amount && $revision->loss_amount > 0) ? 'Rp ' . number_format($revision->loss_amount, 0, ',', '.') : 'Belum diisi (Rp 0)' }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center text-xs border-t border-rose-200/60 dark:border-rose-900/40 pt-2">
                                <span class="text-gray-500 dark:text-gray-400 font-semibold">Kategori:</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200 uppercase">{{ $revision->loss_category ?: 'REWORK_LABOR' }}</span>
                            </div>

                            @if($revision->responsible_party)
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500 dark:text-gray-400 font-semibold">Penanggung Jawab:</span>
                                <span class="font-bold text-rose-700 dark:text-rose-300">{{ $revision->responsible_party }}</span>
                            </div>
                            @endif

                            @if($revision->loss_description)
                            <div class="border-t border-rose-200/60 dark:border-rose-900/40 pt-2 text-xs">
                                <span class="text-gray-500 dark:text-gray-400 block font-semibold mb-1">Rincian Material / Kerugian:</span>
                                <p class="text-gray-700 dark:text-gray-300 font-medium italic bg-white/70 dark:bg-gray-900/50 p-3 rounded-xl border border-rose-100 dark:border-rose-900/30">
                                    "{{ $revision->loss_description }}"
                                </p>
                            </div>
                            @endif
                        </div>

                        <!-- Edit Form View -->
                        <div x-show="editingLoss" style="display: none;" class="pt-2 border-t border-rose-200/60">
                            <form action="{{ route('revision.update-loss', $revision->id) }}" method="POST" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-300 mb-1">Nominal Kerugian (Rp):</label>
                                    <input type="number" name="loss_amount" value="{{ old('loss_amount', $revision->loss_amount ?: 0) }}" min="0" placeholder="0" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:ring-[#22AF85]">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-300 mb-1">Kategori Kerugian:</label>
                                    <select name="loss_category" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:ring-[#22AF85]">
                                        <option value="REWORK_LABOR" {{ ($revision->loss_category ?? '') === 'REWORK_LABOR' ? 'selected' : '' }}>Ongkos Pengerjaan Ulang</option>
                                        <option value="MATERIAL_WASTE" {{ ($revision->loss_category ?? '') === 'MATERIAL_WASTE' ? 'selected' : '' }}>Bahan Terbuang / Rusak</option>
                                        <option value="REPLACEMENT" {{ ($revision->loss_category ?? '') === 'REPLACEMENT' ? 'selected' : '' }}>Penggantian Komponen/Unit</option>
                                        <option value="OTHERS" {{ ($revision->loss_category ?? '') === 'OTHERS' ? 'selected' : '' }}>Lain-lain</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-300 mb-1">Penanggung Jawab / Penyebab:</label>
                                    <input type="text" name="responsible_party" value="{{ old('responsible_party', $revision->responsible_party) }}" placeholder="Misal: Teknisi Soling / Supplier Material / QC" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:ring-[#22AF85]">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-300 mb-1">Rincian Material / Catatan:</label>
                                    <input type="text" name="loss_description" value="{{ old('loss_description', $revision->loss_description) }}" placeholder="Misal: 1 Pcs Sol Rubber, 2 Botol Lem..." class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:ring-[#22AF85]">
                                </div>

                                <div class="pt-2 flex gap-2">
                                    <button type="button" @click="editingLoss = false" class="flex-1 py-2 rounded-xl bg-gray-100 text-gray-600 font-bold text-xs hover:bg-gray-200 transition-colors">Batal</button>
                                    <button type="submit" class="flex-1 py-2 rounded-xl bg-[#22AF85] text-white font-bold text-xs hover:bg-[#1a8a68] shadow-md transition-all">Simpan Kerugian</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Metadata Card --}}
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-[2rem] p-8 border border-gray-100 dark:border-gray-700 space-y-8">
                        {{-- Sumber / Origin Status --}}
                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Sumber Revisi</h4>
                            @php
                                $origin = strtoupper($revision->origin_status ?: 'SELESAI');
                                $badgeClass = match($origin) {
                                    'QC' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400 border-amber-100 dark:border-amber-900/30',
                                    'PRODUCTION' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 border-blue-100 dark:border-blue-900/30',
                                    default => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/30'
                                };
                                $label = match($origin) {
                                    'QC' => 'QC',
                                    'PRODUCTION' => 'PRODUCTION',
                                    default => 'SELESAI'
                                };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-black border uppercase tracking-widest {{ $badgeClass }}">
                                {{ $label }}
                            </span>
                        </div>

                        {{-- Customer --}}
                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Customer</h4>
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center font-bold text-gray-500">
                                    {{ substr($revision->workOrder->customer_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-800 dark:text-gray-200 leading-tight">{{ $revision->workOrder->customer_name }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium">{{ $revision->workOrder->customer_phone }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Shoe --}}
                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Unit Sepatu</h4>
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4 flex items-center gap-4 border border-gray-100 dark:border-gray-700">
                                <div class="text-2xl">👟</div>
                                <div>
                                    <p class="text-xs font-black text-gray-800 dark:text-gray-200 uppercase tracking-tight">{{ $revision->workOrder->shoe_brand }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $revision->workOrder->shoe_color }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Pelapor --}}
                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Dilaporkan Oleh</h4>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 flex items-center justify-center text-xs font-bold border border-indigo-100/50">
                                        {{ substr($revision->creator->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-400">{{ $revision->creator->name ?? 'System' }}</span>
                                </div>
                                <span class="text-[10px] font-black text-gray-400 uppercase">{{ $revision->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Link Back --}}
                    <div class="text-center">
                         <a href="{{ route('finish.show', $revision->work_order_id) }}" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-indigo-500 transition-colors">
                            Lihat Detail SPK Lengkap
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-workshop-pwa-layout>

<x-app-layout>
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

                        @if($revision->photo_path)
                        <div class="px-10 pb-10">
                            <h3 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em] mb-6">Foto Dokumentasi Masalah</h3>
                            <div class="rounded-[2rem] overflow-hidden border-4 border-gray-50 dark:border-gray-700 shadow-inner group relative">
                                <img src="{{ asset('storage/' . $revision->photo_path) }}" 
                                     alt="Foto Revisi" 
                                     class="w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-8">
                                    <a href="{{ asset('storage/' . $revision->photo_path) }}" target="_blank" class="bg-white text-gray-900 px-6 py-3 rounded-full font-black uppercase text-xs tracking-widest flex items-center gap-2 shadow-2xl transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Buka Ukuran Penuh
                                    </a>
                                </div>
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
                    </div>

                    {{-- Metadata Card --}}
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-[2rem] p-8 border border-gray-100 dark:border-gray-700 space-y-8">
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
</x-app-layout>

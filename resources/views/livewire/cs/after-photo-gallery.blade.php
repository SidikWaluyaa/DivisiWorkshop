<div x-data="{ 
        open: false, 
        allBefore: [],
        allAfter: [],
        activeSpk: '',
        activeCustomer: '',
        showLightbox(beforePhotos, afterPhotos, spk, customer) {
            this.allBefore = beforePhotos;
            this.allAfter = afterPhotos;
            this.activeSpk = spk;
            this.activeCustomer = customer;
            this.open = true;
        },
        downloadAll(photos, type) {
            photos.forEach((url, index) => {
                const link = document.createElement('a');
                link.href = url;
                link.download = `${this.activeSpk}_${type}_${index + 1}.jpg`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        }
    }" 
    class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Header & Filters --}}
        <div class="mb-8 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                        📸 Galeri <span class="text-teal-600">Before & After</span>
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1 font-medium">Perbandingan hasil pengerjaan untuk bukti kualitas ke pelanggan.</p>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    {{-- Search --}}
                    <div class="relative w-full sm:w-64">
                        <input type="text" wire:model.live.debounce.500ms="search" 
                               class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700 border-none rounded-xl text-sm focus:ring-2 focus:ring-teal-500 transition-all dark:text-white"
                               placeholder="Cari SPK / Nama / Jasa...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    {{-- Service Filter --}}
                    <div class="w-full sm:w-64">
                        <select wire:model.live="serviceId" 
                                class="w-full py-2.5 bg-gray-50 dark:bg-gray-700 border-none rounded-xl text-sm focus:ring-2 focus:ring-teal-500 transition-all dark:text-white">
                            <option value="">Semua Jasa</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Comparison Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" id="photo-grid">
            @forelse($workOrders as $wo)
                @php
                    $before = $wo->photos->where('step', 'WAREHOUSE_BEFORE')->first();
                    $after = $wo->photos->where('step', 'FINISH')->first();
                @endphp
                <div wire:key="wo-{{ $wo->id }}" 
                     class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden group hover:shadow-2xl transition-all duration-500">
                    
                    {{-- Comparison Header --}}
                    <div class="px-6 py-4 border-b border-gray-50 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-xl flex items-center justify-center text-teal-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase leading-none">{{ \Illuminate\Support\Str::beforeLast($wo->spk_number, '-') }}</h3>
                                <p class="text-[10px] font-bold text-teal-600 uppercase mt-1">{{ $wo->customer_name }}</p>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            @foreach($wo->workOrderServices->take(2) as $wos)
                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-[8px] font-black rounded-lg uppercase">
                                    {{ $wos->service->name ?? $wos->custom_service_name }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex h-72 sm:h-96 relative group/container cursor-pointer will-change-transform"
                         @click="showLightbox(
                            {{ $wo->photos->where('step', 'WAREHOUSE_BEFORE')->map(fn($p) => Storage::url($p->file_path))->values() }}, 
                            {{ $wo->photos->where('step', 'FINISH')->map(fn($p) => Storage::url($p->file_path))->values() }}, 
                            '{{ \Illuminate\Support\Str::beforeLast($wo->spk_number, '-') }}', 
                            '{{ $wo->customer_name }}'
                         )">
                        
                        {{-- Before Side --}}
                        <div class="w-1/2 relative overflow-hidden border-r border-white dark:border-gray-900 bg-gray-200 dark:bg-gray-800">
                            @if($before)
                                <img src="{{ Storage::url($before->file_path) }}" loading="lazy" class="w-full h-full object-cover transition-all duration-700 group-hover:scale-105 [image-rendering:auto]">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 p-4 text-center">
                                    <svg class="w-10 h-10 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-[10px] font-black uppercase">No Before Photo</span>
                                </div>
                            @endif
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1.5 bg-amber-500 text-white text-[10px] font-black rounded-full shadow-lg uppercase tracking-widest">Before</span>
                            </div>
                        </div>

                        {{-- After Side --}}
                        <div class="w-1/2 relative overflow-hidden bg-gray-200 dark:bg-gray-800">
                            @if($after)
                                <img src="{{ Storage::url($after->file_path) }}" loading="lazy" class="w-full h-full object-cover transition-all duration-700 group-hover:scale-105 [image-rendering:auto]">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 p-4 text-center">
                                    <svg class="w-10 h-10 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-[10px] font-black uppercase">No After Photo</span>
                                </div>
                            @endif
                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1.5 bg-teal-600 text-white text-[10px] font-black rounded-full shadow-lg uppercase tracking-widest">After</span>
                            </div>
                        </div>

                        {{-- Action Overlay --}}
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/container:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center gap-4">
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <span class="text-white text-2xl font-black">{{ $wo->photos->where('step', 'WAREHOUSE_BEFORE')->count() }}</span>
                                    <span class="text-white/70 text-[8px] font-bold uppercase tracking-widest">Before Photos</span>
                                </div>
                                <div class="w-px h-10 bg-white/20"></div>
                                <div class="flex flex-col items-center">
                                    <span class="text-white text-2xl font-black">{{ $wo->photos->where('step', 'FINISH')->count() }}</span>
                                    <span class="text-white/70 text-[8px] font-bold uppercase tracking-widest">After Photos</span>
                                </div>
                            </div>
                            <div class="px-6 py-2.5 bg-white text-gray-900 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-2xl transform translate-y-4 group-hover/container:translate-y-0 transition-transform duration-500">
                                View All Angles
                            </div>
                        </div>
                    </div>

                    {{-- Footer Actions --}}
                    <div class="px-6 py-4 bg-gray-50/30 dark:bg-gray-800/30 flex justify-between items-center">
                        <span class="text-[9px] font-bold text-gray-400 uppercase">{{ $wo->created_at->format('d M Y') }}</span>
                        <div class="flex gap-2">
                            @if($after)
                                <button onclick="copyImage('{{ Storage::url($after->file_path) }}')" 
                                        @click.stop
                                        class="p-2.5 bg-white dark:bg-gray-700 hover:bg-blue-600 hover:text-white rounded-xl text-gray-600 dark:text-gray-300 transition-all border border-gray-100 dark:border-gray-600 shadow-sm" title="Salin Foto After">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                </button>
                                <a href="{{ Storage::url($after->file_path) }}" download="{{ $wo->spk_number }}_after.jpg" 
                                   @click.stop
                                   class="p-2.5 bg-white dark:bg-gray-700 hover:bg-teal-600 hover:text-white rounded-xl text-gray-600 dark:text-gray-300 transition-all border border-gray-100 dark:border-gray-600 shadow-sm" title="Download After">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="text-5xl mb-4">🔍</div>
                    <h3 class="text-gray-400 font-black text-sm uppercase">Data Tidak Ditemukan</h3>
                    <p class="text-gray-300 text-[10px] mt-1">Coba sesuaikan filter atau pencarian Anda.</p>
                </div>
            @endforelse
        </div>

        {{-- Infinite Scroll Trigger --}}
        @if($workOrders->hasMorePages())
            <div x-data="{
                    observe() {
                        let observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    @this.call('loadMore')
                                }
                            })
                        }, {
                            rootMargin: '100px'
                        })
                        observer.observe(this.$el)
                    }
                }" 
                x-init="observe()"
                class="mt-12 flex justify-center py-8">
                <div wire:loading wire:target="loadMore" class="flex flex-col items-center gap-2">
                    <div class="w-10 h-10 border-4 border-teal-500 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-xs font-black text-teal-600 uppercase tracking-widest animate-pulse">Memuat lebih banyak...</span>
                </div>
                <div wire:loading.remove wire:target="loadMore">
                     <span class="text-gray-400 text-xs font-medium italic">Scroll untuk memuat lebih banyak</span>
                </div>
            </div>
        @else
            <div class="mt-12 text-center py-8 border-t border-gray-100 dark:border-gray-800">
                <p class="text-gray-400 text-xs font-black uppercase tracking-widest">Semua data telah ditampilkan</p>
            </div>
        @endif
    </div>

    {{-- Overhauled Modal: All Angles Showcase --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-[99] flex items-center justify-center p-4 bg-black/95 backdrop-blur-xl"
         @keydown.escape.window="open = false"
         style="display: none;">
        
        <div class="relative max-w-7xl w-full max-h-[90vh] flex flex-col bg-gray-100 dark:bg-gray-900 rounded-[3rem] shadow-2xl overflow-hidden border border-white/10" @click.away="open = false">
            
            {{-- Modal Header --}}
            <div class="px-10 py-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <h3 class="text-teal-600 font-black text-xs uppercase tracking-[0.2em]" x-text="activeSpk"></h3>
                    <p class="text-gray-900 dark:text-white font-black text-2xl uppercase" x-text="activeCustomer"></p>
                </div>
                <button @click="open = false" class="p-3 bg-gray-100 dark:bg-gray-700 hover:bg-red-500 hover:text-white rounded-2xl transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Modal Content (Scrollable) --}}
            <div class="flex-1 overflow-y-auto p-10 space-y-12 custom-scrollbar">
                
                {{-- Before Section --}}
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-l-4 border-amber-500 pl-4">
                        <div>
                            <h4 class="text-amber-500 font-black text-sm uppercase tracking-widest">Before Photos</h4>
                            <p class="text-gray-400 text-[10px] font-medium uppercase mt-1">Kondisi barang saat pertama kali diterima</p>
                        </div>
                        <template x-if="allBefore.length > 0">
                            <button @click="downloadAll(allBefore, 'before')" 
                                    class="flex items-center gap-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-black text-[10px] uppercase transition-all shadow-lg shadow-amber-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span>Unduh Semua Before</span>
                            </button>
                        </template>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <template x-for="(photo, index) in allBefore" :key="index">
                            <div class="group relative aspect-square rounded-2xl overflow-hidden bg-gray-200 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 shadow-sm">
                                <img :src="photo" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                    <a :href="photo" :download="activeSpk + '_before_' + (index+1) + '.jpg'" class="p-2 bg-white rounded-lg text-gray-900 hover:bg-amber-500 hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </template>
                        <template x-if="allBefore.length === 0">
                            <div class="col-span-full py-12 flex flex-col items-center justify-center bg-gray-200/50 dark:bg-gray-800/50 rounded-3xl border-2 border-dashed border-gray-300 dark:border-gray-700">
                                <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-[10px] font-black text-gray-400 uppercase">Tidak Ada Foto Before</span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="h-px bg-gray-200 dark:bg-gray-700"></div>

                {{-- After Section --}}
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-l-4 border-teal-500 pl-4">
                        <div>
                            <h4 class="text-teal-600 font-black text-sm uppercase tracking-widest">After Photos</h4>
                            <p class="text-gray-400 text-[10px] font-medium uppercase mt-1">Hasil akhir pengerjaan kualitas terbaik</p>
                        </div>
                        <template x-if="allAfter.length > 0">
                            <button @click="downloadAll(allAfter, 'after')" 
                                    class="flex items-center gap-2 px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-black text-[10px] uppercase transition-all shadow-lg shadow-teal-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span>Unduh Semua After</span>
                            </button>
                        </template>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <template x-for="(photo, index) in allAfter" :key="index">
                            <div class="group relative aspect-square rounded-2xl overflow-hidden bg-gray-200 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 shadow-sm">
                                <img :src="photo" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                    <a :href="photo" :download="activeSpk + '_after_' + (index+1) + '.jpg'" class="p-2 bg-white rounded-lg text-gray-900 hover:bg-teal-600 hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </template>
                        <template x-if="allAfter.length === 0">
                            <div class="col-span-full py-12 flex flex-col items-center justify-center bg-gray-200/50 dark:bg-gray-800/50 rounded-3xl border-2 border-dashed border-gray-300 dark:border-gray-700">
                                <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-[10px] font-black text-gray-400 uppercase">Tidak Ada Foto After</span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-10 py-6 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 text-center">
                <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest">Gunakan foto ini untuk bukti pengerjaan profesional kepada pelanggan.</p>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
        }
    </style>

    {{-- Script for Copying Image --}}
    <script>
        async function copyImage(imageUrl) {
            try {
                const response = await fetch(imageUrl);
                const blob = await response.blob();
                
                try {
                    await navigator.clipboard.write([
                        new ClipboardItem({
                            [blob.type]: blob
                        })
                    ]);
                    // Using SweetAlert if available, otherwise fallback
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Gambar telah disalin ke clipboard.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else {
                        alert('✅ Gambar berhasil disalin ke clipboard!');
                    }
                } catch (e) {
                    console.error('Clipboard error:', e);
                    await navigator.clipboard.writeText(window.location.origin + imageUrl);
                    alert('⚠️ Link gambar telah disalin ke clipboard!');
                }
            } catch (err) {
                console.error('Fetch error:', err);
                alert('❌ Gagal menyalin gambar.');
            }
        }
    </script>
</div>

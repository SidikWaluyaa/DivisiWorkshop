<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Header & Filters --}}
        <div class="mb-8 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                        🖼️ Galeri <span class="text-teal-600">After Photo</span>
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1 font-medium">Akses cepat foto hasil pengerjaan untuk dikirim ke pelanggan.</p>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    {{-- Search --}}
                    <div class="relative w-full sm:w-64">
                        <input type="text" wire:model.live.debounce.300ms="search" 
                               class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700 border-none rounded-xl text-sm focus:ring-2 focus:ring-teal-500 transition-all dark:text-white"
                               placeholder="Cari SPK / Nama...">
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

        {{-- Photo Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($photos as $photo)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden group hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1">
                    {{-- Photo Container --}}
                    <div class="relative aspect-square overflow-hidden bg-gray-100">
                        <img src="{{ Storage::url($photo->file_path) }}" 
                             alt="After Photo" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        
                        {{-- Overlay Actions --}}
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-3">
                            <a href="{{ Storage::url($photo->file_path) }}" download="{{ $photo->workOrder->spk_number }}_after.jpg" 
                               class="p-3 bg-white hover:bg-teal-600 hover:text-white rounded-full text-gray-900 transition-all shadow-lg" title="Download Gambar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </a>
                            <button onclick="copyImage('{{ Storage::url($photo->file_path) }}')" 
                                    class="p-3 bg-white hover:bg-blue-600 hover:text-white rounded-full text-gray-900 transition-all shadow-lg" title="Salin Gambar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            </button>
                        </div>

                        {{-- Service Badges --}}
                        <div class="absolute top-3 left-3 flex flex-wrap gap-1 max-w-[80%]">
                            @foreach($photo->workOrder->workOrderServices as $wos)
                                <span class="px-2 py-1 bg-teal-600/90 text-white text-[8px] font-black rounded-lg backdrop-blur-sm uppercase tracking-tighter">
                                    {{ $wos->service->name ?? $wos->custom_service_name }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div class="min-w-0">
                                <h3 class="font-bold text-teal-600 text-xs truncate">{{ $photo->workOrder->spk_number }}</h3>
                                <p class="font-black text-gray-900 dark:text-white text-sm truncate uppercase">{{ $photo->workOrder->customer_name }}</p>
                            </div>
                            <span class="text-[9px] font-bold text-gray-400 shrink-0">{{ $photo->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-50 dark:border-gray-700">
                             <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Divisi CS</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
                    <div class="text-4xl mb-4">📸</div>
                    <h3 class="text-gray-400 font-black text-sm uppercase">Belum Ada Foto After</h3>
                    <p class="text-gray-300 text-[10px] mt-1">Coba sesuaikan filter atau pencarian Anda.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $photos->links() }}
        </div>
    </div>

    {{-- Script for Copying Image --}}
    <script>
        async function copyImage(imageUrl) {
            try {
                // For images to be copyable to clipboard, they often need to be in a specific format (PNG)
                // and within the same origin or handled via proxy.
                // Simplest way for generic browser support is to fetch and write to clipboard.
                const response = await fetch(imageUrl);
                const blob = await response.blob();
                
                // Note: Most browsers only support PNG in ClipboardItem
                // If it's JPEG, we might need a canvas conversion, but let's try the direct blob first
                // if it fails, we notify.
                
                try {
                    await navigator.clipboard.write([
                        new ClipboardItem({
                            [blob.type]: blob
                        })
                    ]);
                    alert('✅ Gambar berhasil disalin ke clipboard!');
                } catch (e) {
                    console.error('Clipboard error:', e);
                    // Fallback: Copy URL
                    await navigator.clipboard.writeText(window.location.origin + imageUrl);
                    alert('⚠️ Browser Anda tidak mendukung salin gambar langsung. Link gambar telah disalin!');
                }
            } catch (err) {
                console.error('Fetch error:', err);
                alert('❌ Gagal menyalin gambar.');
            }
        }
    </script>
</div>

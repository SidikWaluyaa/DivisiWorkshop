<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('assessment.index') }}" class="p-2 bg-white/20 rounded-xl backdrop-blur-sm shadow-sm border border-white/30 hover:bg-white/30 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-white leading-tight flex items-center gap-2">
                        Kelola Foto SPK
                    </h2>
                    <p class="text-xs text-white/80 font-medium">
                        {{ $order->spk_number }} - {{ $order->customer_name }}
                    </p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('assessment.print-spk', $order->id) }}" target="_blank"
                   class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white text-sm font-bold rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-md shadow-orange-500/20 transform hover:-translate-y-0.5 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak SPK
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen"
         x-data="{
             photos: {{ json_encode($order->photos->map(function($p) {
                 return [
                     'id' => $p->id,
                     'photo_url' => $p->photo_url,
                     'step' => $p->step,
                     'caption' => $p->caption ?? '',
                     'is_printed' => $p->is_printed,
                     'print_settings' => $p->print_settings ?? ['zoom' => 1.0, 'x' => 0, 'y' => 0, 'rotate' => 0]
                 ];
             })) }},
             isSaving: false,
             
             // Editor Modal State
             editPhoto: null,
             isEditorOpen: false,
             scale: 1,
             panX: 0,
             panY: 0,
             rotate: 0,
             isDragging: false,
             startX: 0,
             startY: 0,

             togglePrint(photo) {
                 const printedCount = this.photos.filter(p => p.is_printed).length;
                 if (!photo.is_printed && printedCount >= 4) {
                     Swal.fire({
                         icon: 'warning',
                         title: 'Batas Maksimal',
                         text: 'Anda hanya dapat memilih maksimal 4 foto untuk dicetak di SPK.'
                     });
                     return;
                 }
                 photo.is_printed = !photo.is_printed;
             },

             openEditor(photo) {
                 this.editPhoto = photo;
                 this.scale = photo.print_settings.zoom || 1.0;
                 this.panX = photo.print_settings.x || 0;
                 this.panY = photo.print_settings.y || 0;
                 this.rotate = photo.print_settings.rotate || 0;
                 this.isEditorOpen = true;
             },

             saveEditorSettings() {
                 this.editPhoto.print_settings = {
                     zoom: parseFloat(this.scale),
                     x: parseInt(this.panX),
                     y: parseInt(this.panY),
                     rotate: parseInt(this.rotate)
                 };
                 this.editPhoto.is_printed = true; // Auto select if edited
                 this.isEditorOpen = false;
                 
                 Swal.fire({
                     icon: 'success',
                     title: 'Sudut & Zoom Diatur',
                     text: 'Pengaturan visual foto disimpan di browser. Jangan lupa Simpan Konfigurasi.',
                     toast: true,
                     position: 'top-end',
                     showConfirmButton: false,
                     timer: 3000
                 });
             },

             // Direct drag to pan
             dragStart(e) {
                 this.isDragging = true;
                 const clientX = e.clientX || (e.touches && e.touches[0].clientX);
                 const clientY = e.clientY || (e.touches && e.touches[0].clientY);
                 this.startX = clientX - (this.panX * this.scale * 2.5); // scaling factor
                 this.startY = clientY - (this.panY * this.scale * 2.5);
             },

             dragMove(e) {
                 if (!this.isDragging) return;
                 const clientX = e.clientX || (e.touches && e.touches[0].clientX);
                 const clientY = e.clientY || (e.touches && e.touches[0].clientY);
                 
                 const deltaX = clientX - this.startX;
                 const deltaY = clientY - this.startY;

                 // Convert to percentage based on scale
                 this.panX = Math.round(deltaX / (this.scale * 2.5));
                 this.panY = Math.round(deltaY / (this.scale * 2.5));

                 // Constrain pan within limits (-100 to 100)
                 this.panX = Math.max(-100, Math.min(100, this.panX));
                 this.panY = Math.max(-100, Math.min(100, this.panY));
             },

             dragEnd() {
                 this.isDragging = false;
             },

             async saveConfiguration() {
                 this.isSaving = true;
                 try {
                     const response = await fetch('{{ route('assessment.gallery-spk.save', $order->id) }}', {
                         method: 'POST',
                         headers: {
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                             'Content-Type': 'application/json',
                             'Accept': 'application/json'
                         },
                         body: JSON.stringify({
                             photos: this.photos.map(p => ({
                                 id: p.id,
                                 is_printed: p.is_printed,
                                 print_settings: p.print_settings
                             }))
                         })
                     });
                     
                     const result = await response.json();
                     if (result.success) {
                         Swal.fire({
                             icon: 'success',
                             title: 'Konfigurasi Disimpan!',
                             text: result.message,
                             confirmButtonColor: '#22B086'
                         });
                     } else {
                         Swal.fire({
                             icon: 'error',
                             title: 'Gagal Menyimpan',
                             text: result.message || 'Terjadi kesalahan sistem.'
                         });
                     }
                 } catch (e) {
                     console.error(e);
                     Swal.fire({
                         icon: 'error',
                         title: 'Error Koneksi',
                         text: 'Gagal menghubungi server.'
                     });
                 } finally {
                     this.isSaving = false;
                 }
             }
         }">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Instructions Panel --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-150 dark:border-gray-700 shadow-sm mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <span class="p-1.5 bg-teal-500/10 rounded-lg text-teal-600">📸</span>
                        Instruksi Pemilihan Foto SPK
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Pilih **maksimal 4 foto** yang ingin ditampilkan di lembar cetak SPK. Klik ikon roda gigi atau tombol **"Atur Zoom"** untuk menyesuaikan sudut crop, perbesaran, atau putaran arah foto.
                    </p>
                </div>
                
                <div class="flex items-center gap-3 flex-shrink-0 w-full md:w-auto justify-end">
                    <div class="px-4 py-2 bg-teal-50 dark:bg-teal-950/20 border border-teal-100 dark:border-teal-900 rounded-xl text-teal-700 dark:text-teal-400 text-xs font-bold font-mono">
                        Terpilih: <span x-text="photos.filter(p => p.is_printed).length + '/4'"></span>
                    </div>
                    <button type="button" @click="saveConfiguration()" :disabled="isSaving"
                            class="px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md shadow-teal-500/10 transition-all flex items-center gap-2 disabled:opacity-50">
                        <svg x-show="isSaving" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Konfigurasi'"></span>
                    </button>
                </div>
            </div>

            {{-- Photos Grid --}}
            <template x-if="photos.length === 0">
                <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-gray-500">Belum ada foto yang diunggah untuk order ini.</p>
                    <p class="text-xs text-gray-400 mt-1">Silakan ambil kamera atau unggah foto di stasiun pengerjaan terlebih dahulu.</p>
                </div>
            </template>

            <template x-if="photos.length > 0">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <template x-for="photo in photos" :key="photo.id">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-150 dark:border-gray-700 overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col group relative">
                            
                            {{-- Checkbox Overlay (Top Left) --}}
                            <div class="absolute top-3 left-3 z-10">
                                <button type="button" @click="togglePrint(photo)"
                                        :class="photo.is_printed ? 'bg-teal-500 border-teal-500 text-white' : 'bg-black/40 border-white/50 text-transparent hover:bg-black/60'"
                                        class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </button>
                            </div>

                            {{-- Gear Button Overlay (Top Right) --}}
                            <div class="absolute top-3 right-3 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button" @click="openEditor(photo)"
                                        class="w-8 h-8 rounded-lg bg-white/90 dark:bg-gray-800/90 text-gray-600 dark:text-gray-300 hover:text-teal-500 dark:hover:text-teal-400 shadow-md backdrop-blur-sm flex items-center justify-center transition-all transform hover:scale-105">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </button>
                            </div>

                            {{-- Image Container with Crop Preview --}}
                            <div class="aspect-square bg-gray-900 overflow-hidden relative border-b border-gray-100 dark:border-gray-800">
                                <img :src="photo.photo_url"
                                     :style="`transform: scale(${photo.print_settings.zoom || 1}) translate(${photo.print_settings.x || 0}%, ${photo.print_settings.y || 0}%) rotate(${photo.print_settings.rotate || 0}deg); transform-origin: center; object-fit: cover;`"
                                     class="w-full h-full cursor-pointer transition-transform duration-300"
                                     @click="openEditor(photo)">
                            </div>

                            {{-- Meta Details --}}
                            <div class="p-4 flex-1 flex flex-col justify-between gap-2">
                                <div>
                                    <span class="inline-block px-2 py-0.5 rounded-md text-[9px] font-bold bg-teal-50 dark:bg-teal-950/20 text-teal-600 dark:text-teal-400 border border-teal-100 dark:border-teal-900 uppercase tracking-wider"
                                          x-text="photo.step"></span>
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mt-2 line-clamp-2"
                                       x-text="photo.caption || '- Tanpa Keterangan -'"></p>
                                </div>
                                
                                <div class="pt-2 border-t border-dashed border-gray-150 dark:border-gray-700 flex justify-between items-center">
                                    <span class="text-[10px] font-bold" :class="photo.is_printed ? 'text-teal-600' : 'text-gray-400'">
                                        <span x-show="photo.is_printed">✓ Siap Dicetak</span>
                                        <span x-show="!photo.is_printed">Tidak Dicetak</span>
                                    </span>
                                    <button type="button" @click="openEditor(photo)"
                                            class="text-[11px] font-bold text-teal-600 hover:text-teal-700 dark:text-teal-400">
                                        Atur Zoom
                                    </button>
                                </div>
                            </div>
                            
                        </div>
                    </template>
                </div>
            </template>

            {{-- Save Warning Notice --}}
            <div class="mt-8 p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900 rounded-2xl flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                <div>
                    <h5 class="text-xs font-bold text-amber-800 dark:text-amber-300">Pengingat Penting</h5>
                    <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-0.5">
                        Setiap kali Anda mengubah pilihan foto atau memposisikan zoom gambar, pastikan untuk mengklik tombol **"Simpan Konfigurasi"** di atas sebelum membuka cetakan SPK, agar perubahan visual Anda tersimpan permanen ke server.
                    </p>
                </div>
            </div>

        </div>

        {{-- ==================== DYNAMIC CROP / ZOOM EDITOR MODAL ==================== --}}
        <div x-show="isEditorOpen"
             class="fixed inset-0 bg-gray-900/70 backdrop-blur-md z-50 flex items-center justify-center p-4 transition-opacity"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="bg-white dark:bg-gray-800 rounded-3xl overflow-hidden border border-gray-150 dark:border-gray-700 shadow-2xl max-w-lg w-full flex flex-col"
                 @click.away="isEditorOpen = false"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="scale-95 translate-y-4"
                 x-transition:enter-end="scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="scale-100 translate-y-0"
                 x-transition:leave-end="scale-95 translate-y-4">
                
                {{-- Editor Header --}}
                <div class="flex justify-between items-center p-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-teal-500 to-emerald-600 text-white">
                    <h3 class="text-base font-bold flex items-center gap-2">
                        <span>⚙</span> Atur Zoom & Crop Foto SPK
                    </h3>
                    <button type="button" @click="isEditorOpen = false" class="text-white/80 hover:text-white hover:bg-white/10 p-1 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                {{-- Editor Body --}}
                <div class="p-6 space-y-6 flex-1 overflow-y-auto">
                    
                    {{-- Live square preview frame --}}
                    <div class="flex flex-col items-center">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5">Preview Hasil Cetak (Persegi)</span>
                        
                        <div class="w-72 h-72 rounded-2xl overflow-hidden bg-gray-950 border-4 border-teal-500 shadow-xl relative cursor-move select-none"
                             @pointerdown="dragStart($event)"
                             @pointermove="dragMove($event)"
                             @pointerup="dragEnd()"
                             @pointerleave="dragEnd()">
                            
                            <img x-show="editPhoto" :src="editPhoto ? editPhoto.photo_url : ''"
                                 :style="`transform: scale(${scale}) translate(${panX}%, ${panY}%) rotate(${rotate}deg); transform-origin: center; object-fit: cover;`"
                                 class="w-full h-full pointer-events-none transition-transform duration-75">
                            
                            {{-- Target alignment circle --}}
                            <div class="absolute inset-0 border border-white/20 rounded-full pointer-events-none flex items-center justify-center">
                                <div class="w-1.5 h-1.5 bg-white/40 rounded-full"></div>
                            </div>
                        </div>
                        <span class="text-[10px] text-gray-400 mt-2">💡 Tips: Anda dapat men-drag/geser foto langsung di dalam box preview</span>
                    </div>
                    
                    {{-- Control Sliders --}}
                    <div class="space-y-4">
                        {{-- Zoom Slider --}}
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label for="editorZoom" class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Perbesaran (Zoom)</label>
                                <span class="text-xs font-mono font-bold text-teal-600" x-text="parseFloat(scale).toFixed(2) + 'x'"></span>
                            </div>
                            <input type="range" id="editorZoom" min="1.0" max="3.0" step="0.05" x-model="scale"
                                   class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-teal-500">
                        </div>

                        {{-- Rotation Control --}}
                        <div>
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider block mb-2">Rotasi Arah</span>
                            <div class="grid grid-cols-4 gap-2">
                                <template x-for="r in [0, 90, 180, 270]">
                                    <button type="button" @click="rotate = r"
                                            :class="rotate == r ? 'bg-teal-500 text-white border-teal-500' : 'bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-100'"
                                            class="py-1.5 border rounded-lg text-xs font-bold font-mono transition-colors shadow-sm"
                                            x-text="r + '°'"></button>
                                </template>
                            </div>
                        </div>

                        {{-- Manual Pan Inputs (Fallback) --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label for="editorPanX" class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Geser Horiz. (X)</label>
                                    <span class="text-[10px] font-mono font-bold text-gray-500" x-text="panX + '%'"></span>
                                </div>
                                <input type="range" id="editorPanX" min="-100" max="100" step="1" x-model="panX"
                                       class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-teal-500">
                            </div>
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label for="editorPanY" class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Geser Vert. (Y)</label>
                                    <span class="text-[10px] font-mono font-bold text-gray-500" x-text="panY + '%'"></span>
                                </div>
                                <input type="range" id="editorPanY" min="-100" max="100" step="1" x-model="panY"
                                       class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-teal-500">
                            </div>
                        </div>
                    </div>

                </div>
                
                {{-- Editor Footer --}}
                <div class="p-5 bg-gray-50 dark:bg-gray-800/40 border-t border-gray-150 dark:border-gray-700 flex justify-end gap-3">
                    <button type="button" @click="isEditorOpen = false"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 font-bold text-xs uppercase tracking-widest transition-all">
                        Batal
                    </button>
                    <button type="button" @click="saveEditorSettings()"
                            class="px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 text-white rounded-xl font-bold text-xs uppercase tracking-widest shadow-md shadow-teal-500/10 hover:from-teal-600 hover:to-emerald-700 transition-all">
                        Terapkan Pengaturan
                    </button>
                </div>
            </div>
            
        </div>

    </div>
</x-app-layout>

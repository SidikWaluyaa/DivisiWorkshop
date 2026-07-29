@props(['currentStage']) {{-- e.g., 'PREPARATION', 'PRODUCTION', 'QC' --}}

<div x-data="revisionModalData()" 
     x-on:open-revision-modal.window="openModal($event.detail.id, $event.detail.number)" 
     class="inline-block px-0">

    <script>
        function revisionModalData() {
            return {
                showRevisionModal: false, 
                orderId: null,
                orderNumber: '',
                targetStatus: '{{ in_array(strtoupper($currentStage), ["QC", "PRODUCTION"]) ? "REVISI" : "PREPARATION" }}',
                reason: '',
                targetStations: [],
                formAction: '',
                previews: [],
                
                stations: {
                    'PREPARATION': [
                        { id: 'prep_washing', label: 'Washing / Cuci' },
                        { id: 'prep_sol', label: 'Sol / Bongkar Sol' },
                        { id: 'prep_upper', label: 'Upper / Repair' }
                    ],
                    'SORTIR': [],
                    'PRODUCTION': [
                        { id: 'prod_sol', label: 'Production Sol' },
                        { id: 'prod_upper', label: 'Production Upper' },
                        { id: 'prod_cleaning', label: 'Production Treatment' }
                    ],
                    'QC': [
                        { id: 'qc_jahit', label: 'QC Jahit' },
                        { id: 'qc_cleanup', label: 'QC Cleanup' },
                        { id: 'qc_final', label: 'QC Final' }
                    ]
                },

                get activeStations() {
                    return this.stations[this.targetStatus] || [];
                },

                openModal(id, number) {
                    this.orderId = id;
                    this.orderNumber = number;
                    this.formAction = `/{{ strtolower($currentStage) }}/${id}/reject`;
                    this.showRevisionModal = true;
                },

                closeModal() {
                    this.showRevisionModal = false;
                    this.reason = '';
                    this.targetStations = [];
                    this.previews = [];
                    const fileInput = document.getElementById('evidence_photos_input');
                    if (fileInput) fileInput.value = '';
                },

                async handleFiles(event) {
                    const files = event.target.files;
                    this.previews = [];
                    
                    const dataTransfer = new DataTransfer();
                    
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        try {
                            if (file.type.startsWith('image/')) {
                                const compressedFile = await this.compressImage(file);
                                dataTransfer.items.add(compressedFile);
                                
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    this.previews.push({
                                        url: e.target.result,
                                        name: compressedFile.name
                                    });
                                };
                                reader.readAsDataURL(compressedFile);
                            } else {
                                dataTransfer.items.add(file);
                            }
                        } catch (error) {
                            console.error('Image compression error:', error);
                            dataTransfer.items.add(file);
                        }
                    }
                    
                    event.target.files = dataTransfer.files;
                },

                compressImage(file) {
                    return new Promise((resolve, reject) => {
                        const reader = new FileReader();
                        reader.readAsDataURL(file);
                        reader.onload = (event) => {
                            const img = new Image();
                            img.src = event.target.result;
                            img.onload = () => {
                                const canvas = document.createElement('canvas');
                                const ctx = canvas.getContext('2d');
                                
                                const maxW = 1000;
                                const maxH = 1000;
                                let w = img.width;
                                let h = img.height;
                                
                                if (w > h) {
                                    if (w > maxW) {
                                        h = Math.round((h * maxW) / w);
                                        w = maxW;
                                    }
                                } else {
                                    if (h > maxH) {
                                        w = Math.round((w * maxH) / h);
                                        h = maxH;
                                    }
                                }
                                
                                canvas.width = w;
                                canvas.height = h;
                                ctx.drawImage(img, 0, 0, w, h);
                                
                                canvas.toBlob((blob) => {
                                    if (blob) {
                                        const name = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                                        const compressedFile = new File([blob], name, {
                                            type: 'image/jpeg',
                                            lastModified: Date.now()
                                        });
                                        resolve(compressedFile);
                                    } else {
                                        reject(new Error('Canvas toBlob failed'));
                                    }
                                }, 'image/jpeg', 0.7);
                            };
                            img.onerror = (err) => reject(err);
                        };
                        reader.onerror = (err) => reject(err);
                    });
                }
            };
        }
    </script>

    <!-- Modal Backdrop -->
    <div x-show="showRevisionModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="closeModal()"></div>

            <!-- Modal Content -->
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full flex flex-col max-h-[90vh] overflow-hidden transform transition-all border-t-4 border-[#FFC232]">
                <div class="flex justify-between items-center p-6 pb-2 shrink-0">
                    <h3 class="text-xl font-bold text-gray-800">
                        Revisi Order <span class="text-[#22AF85]" x-text="'#' + orderNumber"></span>
                    </h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
                    <form :action="formAction" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        @if(in_array(strtoupper($currentStage), ['QC', 'PRODUCTION']))
                            <!-- Sembunyikan Pilihan Tahap & Reset Station untuk QC & Production -->
                            <input type="hidden" name="target_status" value="REVISI">
                        @else
                            <!-- Target Stage -->
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Pindah ke Kolam / Tahap:</label>
                                <select name="target_status" x-model="targetStatus" class="w-full rounded-xl border-gray-300 focus:border-[#22AF85] focus:ring-[#22AF85]">
                                    <option value="PREPARATION">PREPARATION (Pencucian/Bongkar)</option>
                                    <option value="SORTIR">SORTIR (Pengecekan Material)</option>
                                    <option value="PRODUCTION">PRODUCTION (Proses Produksi)</option>
                                </select>
                            </div>

                            <!-- Target Stations -->
                            <div class="mb-4" x-show="activeStations.length > 0">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Reset Progress Station (Bisa pilih banyak):</label>
                                <div class="grid grid-cols-1 gap-2 p-3 bg-gray-50 rounded-xl border border-gray-200">
                                    <template x-for="station in activeStations" :key="station.id">
                                        <label class="flex items-center space-x-3 cursor-pointer p-1 hover:bg-gray-100 rounded">
                                            <input type="checkbox" name="target_stations[]" :value="station.id" class="rounded text-[#22AF85] focus:ring-[#22AF85]">
                                            <span class="text-sm text-gray-700" x-text="station.label"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        @endif

                        <!-- Reason -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Revisi:</label>
                            <textarea name="reason" x-model="reason" required 
                                      placeholder="Jelaskan kondisi barang kenapa ditolak..."
                                      class="w-full rounded-xl border-gray-300 focus:border-[#22AF85] focus:ring-[#22AF85] h-32"></textarea>
                        </div>

                        <!-- Evidence Photos (Multiple with Previews) -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Bukti (Opsi, Bisa pilih banyak):</label>
                            
                            <div class="flex items-center justify-center w-full mb-3">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">PILIH BEBERAPA FOTO BUKTI</p>
                                    </div>
                                    <input type="file" id="evidence_photos_input" name="evidence_photos[]" multiple accept="image/*" class="hidden" @change="handleFiles($event)">
                                </label>
                            </div>

                            <!-- Preview Grid -->
                            <template x-if="previews.length > 0">
                                <div class="grid grid-cols-3 gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 max-h-48 overflow-y-auto custom-scrollbar">
                                    <template x-for="(preview, index) in previews" :key="index">
                                        <div class="relative group aspect-square rounded-lg overflow-hidden border border-gray-200 bg-white">
                                            <img :src="preview.url" class="object-cover w-full h-full">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                                <span class="text-[9px] text-white font-bold truncate max-w-full px-1" x-text="preview.name"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Actions sticky at bottom of body -->
                        <div class="flex space-x-3 pt-4 border-t border-gray-100 sticky bottom-0 bg-white pb-2">
                            <button type="button" @click="closeModal()" 
                                    class="flex-1 px-4 py-3 rounded-xl border border-gray-300 text-gray-700 font-bold hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="flex-1 px-4 py-3 rounded-xl bg-[#22AF85] text-white font-bold hover:bg-[#1a8a68] shadow-lg shadow-green-100 transition-all flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Kirim Revisi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

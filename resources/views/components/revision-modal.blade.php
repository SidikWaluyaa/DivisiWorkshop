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
                targetStatus: 'PREPARATION',
                reason: "Upper : \nSol : \nKondisi Bawaan : ",
                targetStations: [],
                formAction: '',
                
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
                    this.reason = "Upper : \nSol : \nKondisi Bawaan : ";
                    this.targetStations = [];
                },

                handleReasonInput() {
                    const prefixes = ["Upper :", "Sol :", "Kondisi Bawaan :"];
                    let lines = this.reason.split('\n');
                    let modified = false;

                    prefixes.forEach((prefix, i) => {
                        if (!lines[i] || !lines[i].startsWith(prefix)) {
                            const content = lines[i] ? lines[i].replace(/^(Upper|Sol|Kondisi Bawaan)\s*:\s*/i, '') : '';
                            lines[i] = prefix + (content ? ' ' + content : '');
                            modified = true;
                        }
                    });

                    if (lines.length < 3) {
                        for (let i = lines.length; i < 3; i++) {
                            lines.push(prefixes[i]);
                        }
                        modified = true;
                    }

                    if (modified) {
                        this.reason = lines.join('\n');
                    }
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

                        <!-- Reason -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Revisi:</label>
                            <textarea name="reason" x-model="reason" required 
                                      @input="handleReasonInput"
                                      placeholder="Jelaskan kondisi barang kenapa ditolak..."
                                      class="w-full rounded-xl border-gray-300 focus:border-[#22AF85] focus:ring-[#22AF85] h-32"></textarea>
                        </div>

                        <!-- Evidence Photo -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Bukti (Opsi):</label>
                            <input type="file" name="evidence_photo" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-[#22AF85] hover:file:bg-green-100">
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

@php
    $services = \App\Models\Service::orderBy('name')->get()->map(function($service) { return ['name' => data_get($service, 'name'), 'price' => data_get($service, 'price')]; });
@endphp

<div x-data="reportModalData()"
     x-show="isOpen"
     style="display: none;"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <script>
        function reportModalData() {
            return {
                isOpen: false, 
                workOrderId: null,
                category: 'TEKNIS',
                kendala: '',
                opsiSolusi: '',
                estimasiSelesai: '',
                
                init() {
                    window.addEventListener('open-report-modal', (e) => {
                        this.workOrderId = e.detail;
                        this.isOpen = true;
                    });
                },

                close() {
                    this.isOpen = false;
                    this.kendala = '';
                    this.opsiSolusi = '';
                    this.category = 'TEKNIS';
                    this.estimasiSelesai = '';
                }
            };
        }
    </script>

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh] overflow-hidden" 
         @click.away="close()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4">
        
        {{-- Header --}}
        <div class="bg-amber-500 p-4 flex justify-between items-center shrink-0">
            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Lapor Kendala / Follow Up
            </h3>
            <button @click="close()" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Scrollable Body --}}
        <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
            <form action="{{ route('cx-issues.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="work_order_id" :value="workOrderId">
                
                <div class="mb-4">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kategori Masalah</label>
                    <select name="category" x-model="category" class="w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                        <option value="TEKNIS">Teknis</option>
                        <option value="MATERIAL">Material</option>
                        <option value="OVERLOAD">Overload</option>
                    </select>
                </div>

                {{-- OVERLOAD: Estimasi Selesai Baru --}}
                <div class="mb-6 p-4 bg-red-50 rounded-2xl border border-red-200" x-show="category === 'OVERLOAD'" x-cloak>
                    <label class="block text-sm font-black text-red-700 mb-2 uppercase tracking-wider">Request Perubahan Estimasi Selesai</label>
                    <p class="text-xs text-red-600 mb-3">Tentukan estimasi selesai yang baru. Perubahan ini akan memohon persetujuan CX (tidak langsung mengubah data Order).</p>
                    <input type="date" x-model="estimasiSelesai" name="estimasi_selesai" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-sm p-2">
                </div>

                {{-- TEKNIS & MATERIAL: Kendala & Opsi Solusi --}}
                <div class="mb-4 space-y-4" x-show="category === 'TEKNIS' || category === 'MATERIAL'" x-cloak>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 tracking-wider">Detail Kendala</label>
                        <p class="text-[10px] text-gray-500 mb-2 leading-tight">Jelaskan detail kendala secara lengkap. Anda dapat menggunakan list angka (1, 2, 3...) sesuai kebutuhan.</p>
                        <textarea name="kendala" x-model="kendala" rows="4" 
                            class="w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm p-3"
                            placeholder="1. Detail kerusakan pada bagian...&#10;2. Penjelasan kondisi awal..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 tracking-wider">Opsi Solusi</label>
                         <p class="text-[10px] text-gray-500 mb-2 leading-tight">Berikan saran atau solusi perbaikan untuk customer (Opsional namun disarankan).</p>
                        <textarea name="opsi_solusi" x-model="opsiSolusi" rows="3" 
                            class="w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm p-3"
                            placeholder="Saran perbaikan:&#10;- Ganti material..."></textarea>
                    </div>
                </div>
                
                {{-- Handle Description payload dynamically --}}
                <template x-if="category === 'OVERLOAD'">
                    <input type="hidden" name="description" :value="(estimasiSelesai || 'TBD')">
                </template>
                <template x-if="category !== 'OVERLOAD'">
                    <input type="hidden" name="description" :value="'Kendala:\n' + (kendala || '-') + '\n\nOpsi Solusi:\n' + (opsiSolusi || '-')">
                </template>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Foto Bukti (Hanya JPG/PNG)</label>
                    <input type="file" name="photos[]" multiple accept=".jpg,.jpeg,.png" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[11px] file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    <p class="text-[10px] text-gray-500 mt-1">Maksimal 2MB per gambar. Format: JPG, PNG.</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 sticky bottom-0 bg-white pb-2">
                    <button type="button" @click="close()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold text-xs hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-bold text-xs shadow-md transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

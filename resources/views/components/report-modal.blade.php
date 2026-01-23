<div x-data="{ 
    isOpen: false, 
    workOrderId: null,
    category: 'PRODUK_CACAT',
    description: '',
    
    init() {
        console.log('Report Modal Initialized');
        // Listen to dispatch event
        window.addEventListener('open-report-modal', (e) => {
            console.log('Event received:', e.detail);
            this.workOrderId = e.detail;
            this.isOpen = true;
        });
    },

    close() {
        this.isOpen = false;
        this.description = '';
        this.category = 'PRODUK_CACAT';
    }
}"
x-show="isOpen"
style="display: none;"
class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0"
x-transition:enter-end="opacity-100"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden" 
         @click.away="close()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4">
        
        {{-- Header --}}
        <div class="bg-amber-500 p-4 flex justify-between items-center">
            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Lapor Kendala / Follow Up
            </h3>
            <button @click="close()" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6">
            <form action="{{ route('cx-issues.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="work_order_id" :value="workOrderId">
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kategori Masalah</label>
                    <select name="category" x-model="category" class="w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                        <option value="PRODUK_CACAT">Produk Cacat / Rusak</option>
                        <option value="TIDAK_SESUAI_SPK">Tidak Sesuai SPK</option>
                        <option value="MATERIAL_KOSONG">Material Kosong/Habis</option>
                        <option value="BUTUH_INFO_CX">Butuh Konfirmasi Customer (CX)</option>
                        <option value="LAINNYA">Lainnya</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi / Catatan</label>
                    <textarea name="description" x-model="description" rows="3" required
                              class="w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm"
                              placeholder="elaskan kendala secara detail..."></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Foto Bukti (Opsional)</label>
                    <input type="file" name="photos[]" multiple accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    <p class="text-xs text-gray-500 mt-1">Maksimal 2MB per gambar.</p>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="close()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold text-sm hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-bold text-sm shadow-md transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Storage Assignment Modal --}}
<div x-data="{ open: false, workOrderId: null, selectedRack: '', autoAssign: true }" 
     @storage-modal.window="open = true; workOrderId = $event.detail.workOrderId">
    
    <div x-show="open" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak
         style="display: none;">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
             @click="open = false"></div>
        
        {{-- Modal --}}
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-8" 
                 @click.away="open = false">
                
                {{-- Header --}}
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-black text-gray-900">
                        📦 Simpan ke Gudang
                    </h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                {{-- Form --}}
                <form action="{{ route('storage.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="work_order_id" x-model="workOrderId">
                    
                    {{-- Auto Assign Toggle --}}
                    <div class="flex items-center justify-between p-4 bg-teal-50 rounded-lg border-2 border-teal-200">
                        <div>
                            <label class="font-bold text-gray-900">Auto-Assign Rak</label>
                            <p class="text-sm text-gray-600">Sistem akan pilih rak yang paling kosong</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="autoAssign" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-teal-600"></div>
                        </label>
                    </div>
                    
                    {{-- Manual Rack Selection --}}
                    <div x-show="!autoAssign" x-transition class="space-y-3">
                        <label class="block font-bold text-gray-900">Pilih Rak Manual</label>
                        <select name="rack_code" 
                                x-model="selectedRack"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-2 focus:ring-teal-200">
                            <option value="">-- Pilih Rak --</option>
                            @foreach(\App\Models\StorageRack::active()->available()->where('category', 'shoes')->orderBy('rack_code')->get() as $rack)
                                <option value="{{ $rack->rack_code }}">
                                    {{ $rack->rack_code }} - {{ $rack->location }} 
                                    ({{ $rack->current_count }}/{{ $rack->capacity }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500">
                            💡 Tip: Pilih rak dengan utilisasi paling rendah
                        </p>
                    </div>
                    
                    {{-- Notes --}}
                    <div class="space-y-3">
                        <label class="block font-bold text-gray-900">Catatan (Optional)</label>
                        <textarea name="notes" 
                                  rows="3" 
                                  placeholder="Contoh: Handle with care, Sepatu basah, dll."
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-2 focus:ring-teal-200"></textarea>
                    </div>
                    
                    {{-- Info Box --}}
                    <div class="p-4 bg-orange-50 border-2 border-orange-200 rounded-lg">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">ℹ️</span>
                            <div class="text-sm text-gray-700">
                                <p class="font-bold mb-1">Setelah simpan:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Label akan otomatis ter-generate</li>
                                    <li>Print label dan tempel di sepatu</li>
                                    <li>Simpan sepatu di rak sesuai kode</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Actions --}}
                    <div class="flex gap-3">
                        <button type="button" 
                                @click="open = false"
                                class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-teal-600 to-orange-600 text-white font-bold rounded-lg hover:from-teal-700 hover:to-orange-700 transition-all shadow-lg hover:shadow-xl">
                            Simpan & Print Label
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

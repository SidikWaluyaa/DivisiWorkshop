<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight truncate">
                {{ __('Order Details: ') . $order->spk_number }}
            </h2>
            <a href="{{ route('finish.index') }}" class="shrink-0 px-4 py-2 bg-white/20 hover:bg-white/30 border border-white/50 text-white text-sm font-medium rounded-lg transition-colors shadow-sm flex items-center gap-2 backdrop-blur-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('otoApp', (initialServices) => ({
                open: false,
                selected: [],
                validDays: 7,
                validUntil: '',
                services: initialServices || [],
                
                init() {
                    this.updateDate();
                },
                
                toggle(id) {
                    const idx = this.selected.findIndex(s => s.id === id);
                    if (idx > -1) {
                        this.selected.splice(idx, 1);
                    } else {
                        const s = this.services.find(x => x.id === id);
                        if (s) {
                            // Service price is already the OTO (discounted) price
                            // We suggest a higher normal price (e.g., +25% or rounded)
                            const suggestedNormal = Math.ceil((s.price * 1.2) / 5000) * 5000;
                            this.selected.push({ 
                                id: s.id, 
                                oto_price: s.price, 
                                normal_price: suggestedNormal,
                                name: s.name 
                            });
                        }
                    }
                },
                
                isSelected(id) {
                    return this.selected.some(s => s.id === id);
                },
                
                getSelected(id) {
                    return this.selected.find(s => s.id === id) || { oto_price: 0, normal_price: 0 };
                },
                
                setDays(d) {
                    this.validDays = d;
                    this.updateDate();
                },
                
                updateDate() {
                    try {
                        const d = new Date();
                        const days = parseInt(this.validDays) || 0;
                        d.setDate(d.getDate() + days);
                        const m = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                        this.validUntil = d.getDate() + ' ' + m[d.getMonth()] + ' ' + d.getFullYear();
                    } catch (e) { 
                        this.validUntil = '-'; 
                    }
                },
                
                get total() {
                    return this.selected.reduce((a, b) => a + (Number(b.oto_price) || 0), 0);
                },
                
                get totalNormal() {
                    return this.selected.reduce((a, b) => a + (Number(b.normal_price) || 0), 0);
                },

                money(val) {
                    return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
                }
            }));
        });
    </script>
    @endpush

    <div class="py-12" x-data="otoApp(@js($services))">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- LEFT COLUMN -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden border border-teal-100 dark:border-gray-700">
                        <div class="bg-gradient-to-r from-teal-600 to-orange-500 p-6 text-white relative overflow-hidden text-center sm:text-left">
                            <h3 class="text-4xl font-extrabold mb-1 tracking-tight">{{ $order->customer_name }}</h3>
                            <p class="text-teal-50 font-medium">{{ $order->customer_phone }}</p>
                            <div class="mt-4 shrink-0">
                                <span class="bg-white/20 px-4 py-1.5 rounded-full text-sm font-bold font-mono border border-white/30 tracking-wider">
                                    {{ $order->spk_number }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                             <div class="flex items-center gap-5 mb-8 border-b border-gray-100 dark:border-gray-700 pb-8">
                                <div class="w-14 h-14 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-2xl border border-orange-200">👟</div>
                                <div>
                                    <h4 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $order->shoe_brand }}</h4>
                                    <p class="text-gray-500 dark:text-gray-400">{{ $order->shoe_color }}</p>
                                </div>
                             </div>
                             
                             <div class="bg-orange-50 dark:bg-gray-700/50 rounded-xl p-5 border border-orange-100 dark:border-gray-600">
                                 @if(is_null($order->taken_date))
                                    <div class="flex flex-col gap-3">
                                        <form action="{{ route('finish.pickup', $order->id) }}" method="POST">
                                            @csrf
                                            <button class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-4 rounded-xl shadow-lg font-bold uppercase tracking-widest flex items-center justify-center gap-2">
                                                <span>✅ Konfirmasi Barang Diambil</span>
                                            </button>
                                        </form>
                                        
                                        <button @click="open = true" class="w-full bg-gradient-to-r from-orange-500 to-pink-500 text-white font-bold py-3 px-4 rounded-lg shadow-lg flex items-center justify-center gap-2 mt-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
                                            Buat Penawaran OTO
                                        </button>

                                        <!-- OTO Modal -->
                                        <div x-show="open" 
                                             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" 
                                             style="display: none;" 
                                             x-cloak>
                                            
                                            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col" @click.away="open = false">
                                                <div class="p-8 pb-4 text-center">
                                                    <h3 class="text-3xl font-black text-gray-900 dark:text-gray-100">Penawaran OTO</h3>
                                                    <p class="text-gray-500 mt-1">Satu langkah lagi untuk sepatu sempurna ✨</p>
                                                </div>

                                                <div class="flex-1 overflow-y-auto px-8 py-4">
                                                    <form id="otoForm" action="{{ route('finish.create-oto', $order->id) }}" method="POST">
                                                        @csrf
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                            @foreach($services as $s)
                                                            <div @click="toggle({{ $s['id'] }})" 
                                                                 class="border-2 rounded-2xl p-4 cursor-pointer transition-all"
                                                                 :class="isSelected({{ $s['id'] }}) ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/10' : 'border-gray-100 dark:border-gray-700'">
                                                                <div class="flex justify-between font-bold text-gray-800 dark:text-gray-100">
                                                                    <span>{{ $s['name'] }}</span>
                                                                    <div class="w-5 h-5 rounded-full border-2" :class="isSelected({{ $s['id'] }}) ? 'bg-orange-500 border-orange-500' : 'border-gray-300'"></div>
                                                                </div>
                                                                 <div class="mt-2 flex items-center justify-between">
                                                                    <div class="text-xl font-black text-orange-600">
                                                                        Rp {{ number_format($s['price'], 0, ',', '.') }}
                                                                        <span class="text-[10px] font-bold text-gray-400 uppercase">(Harga OTO)</span>
                                                                    </div>
                                                                 </div>
                                                                 
                                                                 <div x-show="isSelected({{ $s['id'] }})" @click.stop class="mt-2 space-y-2">
                                                                    <div>
                                                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Harga Normal (Sebelum Diskon)</p>
                                                                        <input type="number" 
                                                                               name="services[{{ $s['id'] }}][normal_price]" 
                                                                               x-model.number="getSelected({{ $s['id'] }}).normal_price"
                                                                               :disabled="!isSelected({{ $s['id'] }})"
                                                                               class="w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-lg text-lg font-bold text-gray-400 focus:ring-orange-500 focus:border-orange-500">
                                                                    </div>
                                                                 </div>
                                                                 
                                                                 <input type="hidden" name="services[{{ $s['id'] }}][id]" value="{{ $s['id'] }}" :disabled="!isSelected({{ $s['id'] }})">
                                                                 <input type="hidden" name="services[{{ $s['id'] }}][oto_price]" value="{{ $s['price'] }}" :disabled="!isSelected({{ $s['id'] }})">
                                                                 <input type="hidden" name="services[{{ $s['id'] }}][discount]" :value="getSelected({{ $s['id'] }}).normal_price - {{ $s['price'] }}" :disabled="!isSelected({{ $s['id'] }})">
                                                            </div>
                                                            @endforeach
                                                        </div>

                                                        <div class="mt-8 pt-8 border-t border-gray-100 text-center">
                                                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Masa Berlaku</p>
                                                            <div class="flex justify-center gap-4">
                                                                @foreach([3, 7, 14] as $d)
                                                                <label class="cursor-pointer">
                                                                    <input type="radio" name="valid_days" value="{{ $d }}" 
                                                                           @click="setDays({{ $d }})"
                                                                           :checked="validDays == {{ $d }}"
                                                                           class="sr-only">
                                                                    <div class="w-16 h-16 rounded-2xl border-2 flex flex-col items-center justify-center transition-all"
                                                                         :class="validDays == {{ $d }} ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-100 bg-gray-50 text-gray-400'">
                                                                        <span class="text-xl font-black">{{ $d }}</span>
                                                                        <span class="text-[8px] uppercase font-bold">Hari</span>
                                                                    </div>
                                                                </label>
                                                                @endforeach
                                                            </div>
                                                            <p class="text-xs text-indigo-500 mt-4 font-bold">Sampai dengan: <span x-text="validUntil"></span></p>
                                                        </div>
                                                    </form>
                                                </div>

                                                <div class="p-8 pt-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100">
                                                     <div x-show="selected.length > 0" class="flex justify-between items-end mb-6">
                                                         <div>
                                                             <p class="text-[10px] uppercase font-black text-gray-400">Total Normal</p>
                                                             <p class="text-xl font-bold text-gray-400 line-through" x-text="money(totalNormal)"></p>
                                                         </div>
                                                         <div class="text-right">
                                                             <p class="text-[10px] uppercase font-black text-orange-400">Total OTO ✨</p>
                                                             <p class="text-4xl font-black text-orange-600" x-text="money(total)"></p>
                                                         </div>
                                                     </div>
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <button @click="open = false" class="py-4 font-black text-xs text-gray-400 uppercase">Batal</button>
                                                        <button type="submit" form="otoForm" :disabled="selected.length === 0" class="bg-gradient-to-r from-orange-500 to-pink-600 text-white rounded-2xl py-4 font-black uppercase text-xs shadow-xl disabled:opacity-50">Kirim</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                 @else
                                    <div class="text-center py-4 font-bold text-green-700 uppercase">Sudah Diambil</div>
                                 @endif
                             </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-photo-uploader :order="$order" step="FINISH_BEFORE" />
                            <x-photo-uploader :order="$order" step="FINISH_AFTER" />
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-100 dark:border-gray-700 h-full">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-6 uppercase text-xs">Tim</h3>
                        <div class="border-l-2 border-gray-100 ml-3 space-y-6">
                            <div class="relative pl-6"><span class="absolute -left-[7px] top-0 bg-indigo-500 w-3 h-3 rounded-full"></span><p class="text-sm">{{ $order->picSortirSol->name ?? '-' }} (Sortir)</p></div>
                            <div class="relative pl-6"><span class="absolute -left-[7px] top-0 bg-blue-500 w-3 h-3 rounded-full"></span><p class="text-sm">{{ $order->prodSolBy->name ?? '-' }} (Produksi)</p></div>
                            <div class="relative pl-6"><span class="absolute -left-[7px] top-0 bg-green-500 w-3 h-3 rounded-full"></span><p class="text-sm font-bold text-green-600">{{ $order->qcFinalBy->name ?? '-' }} (QC)</p></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

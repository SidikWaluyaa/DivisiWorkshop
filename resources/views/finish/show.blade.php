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

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- LEFT COLUMN: Order Info & Actions -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Main Card -->
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden border border-teal-100 dark:border-gray-700">
                        <div class="bg-gradient-to-r from-teal-600 to-orange-500 p-6 text-white text-center sm:text-left relative overflow-hidden">
                             <!-- Decorative Shapes -->
                             <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/10 blur-xl"></div>
                             <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-orange-400/20 blur-xl"></div>

                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center relative z-10 gap-4">
                                <div>
                                    <h3 class="text-4xl font-extrabold mb-1 tracking-tight">{{ $order->customer_name }}</h3>
                                    <p class="text-teal-50 font-medium text-lg flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        {{ $order->customer_phone }}
                                    </p>
                                    @if($order->customer_email)
                                    <p class="text-teal-50/80 font-medium text-sm flex items-center gap-2 mt-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        {{ $order->customer_email }}
                                    </p>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full text-sm sm:text-base font-bold font-mono border border-white/30 tracking-wider shadow-sm whitespace-nowrap inline-block">
                                        {{ $order->spk_number }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                             <div class="flex items-center gap-5 mb-8 border-b border-gray-100 dark:border-gray-700 pb-8">
                                <div class="w-14 h-14 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-2xl shadow-sm border border-orange-200">
                                    👟
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 uppercase tracking-widest font-bold mb-0.5">Detail Sepatu</p>
                                    <h4 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $order->shoe_brand }}</h4>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">{{ $order->shoe_color }}</p>
                                </div>
                             </div>
                             
                             <!-- Action Area -->
                             <div class="bg-orange-50 dark:bg-gray-700/50 rounded-xl p-5 border border-orange-100 dark:border-gray-600">
                                 @if(is_null($order->taken_date))
                                    <div class="flex flex-col gap-3">
                                        <!-- Manual WhatsApp Trigger -->
                                        <!-- Manual Email Trigger -->
                                        <!-- SMTP Email Trigger -->
                                        @if($order->customer_email)
                                        <button onclick="sendFinishEmail('{{ $order->id }}')" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl shadow-md font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-2 transform transition-all hover:-translate-y-0.5 mb-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            <span>Kirim Notifikasi Selesai (Email)</span>
                                        </button>
                                        @else
                                        <button disabled class="w-full bg-gray-400 text-white py-3 rounded-xl shadow-md font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-2 mb-2 cursor-not-allowed" title="Email tidak tersedia">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            <span>Email Tidak Tersedia</span>
                                        </button>
                                        @endif
                                        <form action="{{ route('finish.pickup', $order->id) }}" method="POST">
                                            @csrf
                                            <button class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white py-4 rounded-xl shadow-lg hover:shadow-orange-200 dark:hover:shadow-none font-bold text-base uppercase tracking-widest flex items-center justify-center gap-2 transform transition-all hover:-translate-y-0.5">
                                                <span>✅ Konfirmasi Barang Diambil</span>
                                            </button>
                                        </form>
                                        
                                        <div x-data="{ open: false }" class="text-center pt-2 space-y-3">
                                            @php
                                                $waMessage = "Halo Kak {$order->customer_name}, sepatu {$order->shoe_brand} - {$order->shoe_color} (SPK: {$order->spk_number}) sudah selesai dicuci/diperbaiki.\n\nApakah berminat untuk menambah layanan lain (Upsell) agar sepatu Kakak makin kinclong?";
                                                $waLink = "https://wa.me/" . preg_replace('/^0/', '62', $order->customer_phone) . "?text=" . urlencode($waMessage);
                                            @endphp
                                            
                                            <a href="mailto:{{ $order->customer_email }}?subject=Penawaran Layanan Tambahan (SPK: {{ $order->spk_number }})&body=Halo Kak {{ $order->customer_name }},%0D%0A%0D%0ASepatu {{ $order->shoe_brand }} - {{ $order->shoe_color }} (SPK: {{ $order->spk_number }}) sudah selesai kami proses.%0D%0A%0D%0AApakah berminat untuk menambah layanan lain agar sepatu Kakak makin kinclong?" target="_blank" class="block w-full border border-blue-500 text-blue-600 hover:bg-blue-50 font-bold py-2.5 rounded-lg transition-colors flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                Tawarkan Jasa via Email
                                            </a>

                                            <button @click="open = true" class="w-full bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-600 hover:to-pink-600 text-white font-bold py-3 px-4 rounded-lg transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
                                                🎁 Buat Penawaran OTO
                                            </button>

                                            <!-- OTO Modal -->
                                            <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;" x-transition.opacity>
                                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all scale-100" @click.away="open = false" x-data="{
                                                    selectedServices: [],
                                                    validDays: 7,
                                                    services: {{ $services->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'price' => $s->price])->toJson() }},
                                                    
                                                    toggleService(serviceId) {
                                                        const index = this.selectedServices.findIndex(s => s.id === serviceId);
                                                        if (index > -1) {
                                                            this.selectedServices.splice(index, 1);
                                                        } else {
                                                            const service = this.services.find(s => s.id === serviceId);
                                                            if (service) {
                                                                this.selectedServices.push({
                                                                    id: service.id,
                                                                    name: service.name,
                                                                    normalPrice: service.price,
                                                                    otoPrice: Math.round(service.price * 0.7), // 30% discount default
                                                                    discount: 30,
                                                                    customName: ''
                                                                });
                                                            }
                                                        }
                                                    },
                                                    
                                                    isSelected(serviceId) {
                                                        return this.selectedServices.some(s => s.id === serviceId);
                                                    },
                                                    
                                                    updateDiscount(serviceId, discount) {
                                                        const index = this.selectedServices.findIndex(s => s.id === serviceId);
                                                        if (index !== -1) {
                                                            let service = { ...this.selectedServices[index] };
                                                            service.discount = discount;
                                                            service.otoPrice = Math.round(service.normalPrice * (1 - discount/100));
                                                            this.selectedServices.splice(index, 1, service);
                                                        }
                                                    },

                                                    updateCustomPrice(serviceId, value) {
                                                        const index = this.selectedServices.findIndex(s => s.id === serviceId);
                                                        if (index !== -1) {
                                                            let service = { ...this.selectedServices[index] };
                                                            service.otoPrice = parseInt(value) || 0;
                                                            this.selectedServices.splice(index, 1, service);
                                                        }
                                                    },

                                                    updateCustomName(serviceId, value) {
                                                        const index = this.selectedServices.findIndex(s => s.id === serviceId);
                                                        if (index !== -1) {
                                                            let service = { ...this.selectedServices[index] };
                                                            service.customName = value;
                                                            this.selectedServices.splice(index, 1, service);
                                                        }
                                                    },
                                                    
                                                    get totalNormal() {
                                                        return this.selectedServices.reduce((sum, s) => sum + s.normalPrice, 0);
                                                    },
                                                    
                                                    get totalOTO() {
                                                        return this.selectedServices.reduce((sum, s) => sum + s.otoPrice, 0);
                                                    },
                                                    
                                                    get totalSavings() {
                                                        return this.totalNormal - this.totalOTO;
                                                    },
                                                    
                                                    get averageDiscount() {
                                                        if (this.selectedServices.length === 0) return 0;
                                                        return Math.round((this.totalSavings / this.totalNormal) * 100);
                                                    },
                                                    
                                                    get validUntilDate() {
                                                        const date = new Date();
                                                        date.setDate(date.getDate() + parseInt(this.validDays));
                                                        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                                                    }
                                                }">
                                                    <!-- Header -->
                                                    <div class="mb-6">
                                                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-r from-orange-100 to-pink-100">
                                                            <svg class="h-8 w-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" /></svg>
                                                        </div>
                                                        <h3 class="text-2xl font-bold text-center text-gray-900 dark:text-gray-100 mt-4">🎁 Buat Penawaran OTO</h3>
                                                        <p class="text-sm text-center text-gray-600 dark:text-gray-400 mt-2">
                                                            One Time Offer - Penawaran spesial untuk customer
                                                        </p>
                                                        <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                                            <p class="text-xs text-blue-800 dark:text-blue-200 text-center">
                                                                ✅ <strong>Barang tetap bisa diambil</strong> meskipun customer tolak penawaran
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <form action="{{ route('finish.create-oto', $order->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        
                                                        <!-- Service Selection -->
                                                        <div class="mb-6">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                                                Pilih Layanan yang Ditawarkan <span class="text-red-500">*</span>
                                                            </label>
                                                            <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                                                @foreach($services as $service)
                                                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                                                         :class="isSelected({{ $service->id }}) ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-300' : ''">
                                                                        <label class="flex items-start gap-3 cursor-pointer">
                                                                            <input type="checkbox" 
                                                                                   :checked="isSelected({{ $service->id }})"
                                                                                   @change="toggleService({{ $service->id }})"
                                                                                   class="mt-1 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                                                            <div class="flex-1">
                                                                                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $service->name }}</div>
                                                                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                                                                    Harga Normal: <span class="font-bold">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                                                                                </div>
                                                                                
                                                                                <!-- Discount Slider (shown when selected) -->
                                                                                <div x-show="isSelected({{ $service->id }})" class="mt-3 space-y-2" x-transition>
                                                                                    <template x-for="selectedService in selectedServices" :key="selectedService.id">
                                                                                        <div x-show="selectedService.id === {{ $service->id }}">
                                                                                            <!-- Logic: If Base Price > 0 (Normal Service) -> Show Discount Slider -->
                                                                                            <template x-if="{{ $service->price }} > 0">
                                                                                                <div class="space-y-2">
                                                                                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                                                                                        Diskon: <span x-text="selectedService.discount"></span>%
                                                                                                    </label>
                                                                                                    <input type="range" 
                                                                                                           min="10" max="70" step="5"
                                                                                                           :value="selectedService.discount"
                                                                                                           @input="updateDiscount({{ $service->id }}, $event.target.value)"
                                                                                                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-orange-600">
                                                                                                    <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                                                                        <span>Harga OTO:</span>
                                                                                                        <span class="font-bold text-orange-600" x-text="'Rp ' + selectedService.otoPrice.toLocaleString('id-ID')"></span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </template>

                                                                                            <!-- Logic: If Base Price == 0 (Custom Service) -> Show Manual Input -->
                                                                                            <template x-if="{{ $service->price }} == 0">
                                                                                                <div class="space-y-3">
                                                                                                    <div>
                                                                                                         <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                                                                                             Nama Layanan Custom
                                                                                                         </label>
                                                                                                         <input type="text" 
                                                                                                                :value="selectedService.customName"
                                                                                                                @input="updateCustomName(selectedService.id, $event.target.value)"
                                                                                                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm"
                                                                                                                placeholder="Contoh: Repaint Patina">
                                                                                                    </div>
                                                                                                    <div>
                                                                                                        <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                                                                                            Harga Custom (Fleksibel)
                                                                                                        </label>
                                                                                                        <div class="relative">
                                                                                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                                                                                            </div>
                                                                                                            <input type="number" 
                                                                                                                   min="0"
                                                                                                                   :value="selectedService.otoPrice"
                                                                                                                   @input="updateCustomPrice(selectedService.id, $event.target.value)"
                                                                                                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm font-bold text-gray-900"
                                                                                                                   placeholder="0">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <p class="text-[10px] text-gray-400 italic">Harga dan Nama ini yang akan ditawarkan ke customer.</p>
                                                                                                </div>
                                                                                            </template>
                                                                                        </div>
                                                                                    </template>
                                                                                </div>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <!-- Hidden inputs for selected services -->
                                                        <template x-for="service in selectedServices" :key="service.id">
                                                            <div> {{-- Wrap in div because x-for needs single root or similar --}}
                                                                <input type="hidden" :name="'services[' + service.id + '][id]'" :value="service.id">
                                                                <input type="hidden" :name="'services[' + service.id + '][oto_price]'" x-effect="$el.value = service.otoPrice">
                                                                <input type="hidden" :name="'services[' + service.id + '][discount]'" x-effect="$el.value = service.discount">
                                                                <input type="hidden" :name="'services[' + service.id + '][custom_name]'" :value="service.customName">
                                                            </div>
                                                        </template>

                                                        <!-- Validity Period -->
                                                        <div class="mb-6">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                                                Penawaran Valid Sampai
                                                            </label>
                                                            <div class="flex gap-3">
                                                                <label class="flex-1">
                                                                    <input type="radio" name="valid_days" value="3" x-model="validDays" class="sr-only peer">
                                                                    <div class="p-3 border-2 border-gray-300 rounded-lg cursor-pointer peer-checked:border-orange-500 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-900/20 text-center transition-colors">
                                                                        <div class="font-bold text-gray-900 dark:text-gray-100">3 Hari</div>
                                                                    </div>
                                                                </label>
                                                                <label class="flex-1">
                                                                    <input type="radio" name="valid_days" value="7" x-model="validDays" class="sr-only peer" checked>
                                                                    <div class="p-3 border-2 border-gray-300 rounded-lg cursor-pointer peer-checked:border-orange-500 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-900/20 text-center transition-colors">
                                                                        <div class="font-bold text-gray-900 dark:text-gray-100">7 Hari</div>
                                                                    </div>
                                                                </label>
                                                                <label class="flex-1">
                                                                    <input type="radio" name="valid_days" value="14" x-model="validDays" class="sr-only peer">
                                                                    <div class="p-3 border-2 border-gray-300 rounded-lg cursor-pointer peer-checked:border-orange-500 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-900/20 text-center transition-colors">
                                                                        <div class="font-bold text-gray-900 dark:text-gray-100">14 Hari</div>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                            <p class="text-xs text-gray-500 mt-2 text-center">
                                                                Valid sampai: <span class="font-bold text-orange-600" x-text="validUntilDate"></span>
                                                            </p>
                                                        </div>

                                                        <!-- Summary -->
                                                        <div x-show="selectedServices.length > 0" class="mb-6 p-4 bg-gradient-to-r from-orange-50 to-pink-50 dark:from-orange-900/20 dark:to-pink-900/20 rounded-lg border-2 border-orange-200 dark:border-orange-800" x-transition>
                                                            <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                                                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                                Ringkasan Penawaran
                                                            </h4>
                                                            <div class="space-y-2 text-sm">
                                                                <div class="flex justify-between">
                                                                    <span class="text-gray-600 dark:text-gray-400">Harga Normal:</span>
                                                                    <span class="font-semibold line-through text-gray-500" x-text="'Rp ' + totalNormal.toLocaleString('id-ID')"></span>
                                                                </div>
                                                                <div class="flex justify-between text-lg">
                                                                    <span class="font-bold text-gray-900 dark:text-gray-100">Harga OTO:</span>
                                                                    <span class="font-bold text-orange-600" x-text="'Rp ' + totalOTO.toLocaleString('id-ID')"></span>
                                                                </div>
                                                                <div class="flex justify-between pt-2 border-t border-orange-200 dark:border-orange-800">
                                                                    <span class="font-bold text-green-700 dark:text-green-400">Hemat:</span>
                                                                    <span class="font-bold text-green-700 dark:text-green-400" x-text="'Rp ' + totalSavings.toLocaleString('id-ID') + ' (' + averageDiscount + '%)'"></span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Action Buttons -->
                                                        <div class="grid grid-cols-2 gap-3">
                                                            <button type="button" @click="open = false" class="px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 font-semibold transition-colors">
                                                                Batal
                                                            </button>
                                                            <button type="submit" 
                                                                    :disabled="selectedServices.length === 0"
                                                                    :class="selectedServices.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-xl'"
                                                                    class="px-4 py-3 bg-gradient-to-r from-orange-600 to-pink-600 text-white rounded-lg font-bold shadow-lg transition-all">
                                                                🎁 Buat Penawaran
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <div class="inline-flex flex-col items-center">
                                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 mb-2">
                                                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <span class="text-lg font-bold text-green-700">SUDAH DIAMBIL</span>
                                            <p class="text-sm text-gray-500 mt-1">Pada: {{ $order->taken_date->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                @endif
                             </div>
                        </div>
                    </div>

                    <!-- Final Documentation -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span class="w-1 h-6 bg-orange-500 rounded-full"></span>
                            Dokumentasi Final
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 dark:border-gray-600 dark:bg-gray-700">
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase block mb-2">📸 Kondisi Diterima (Before)</span>
                                <x-photo-uploader :order="$order" step="FINISH_BEFORE" />
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 dark:border-gray-600 dark:bg-gray-700">
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase block mb-2">✨ Siap Diambil (After)</span>
                                <x-photo-uploader :order="$order" step="FINISH_AFTER" />
                            </div>
                        </div>
                    </div>

                    <!-- Services List -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span class="w-1 h-6 bg-teal-500 rounded-full"></span>
                            Layanan yang Dikerjakan
                        </h3>
                        <div class="space-y-3">
                            @foreach($order->workOrderServices as $detail)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="font-medium text-gray-700 dark:text-gray-200">{{ $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan') }}</span>
                                <span class="text-sm font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded">Rp {{ number_format($detail->cost, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                        
                        @if($order->notes)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">Catatan Order</h4>
                            <p class="text-gray-600 italic bg-yellow-50 p-3 rounded-lg border border-yellow-100">{{ $order->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- RIGHT COLUMN: Timeline & Team -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-100 dark:border-gray-700 h-full">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center gap-2">
                            <span class="w-1 h-6 bg-indigo-500 rounded-full"></span>
                            Tim Eksekusi
                        </h3>

                        <div class="relative border-l-2 border-gray-200 ml-3 space-y-8">
                            <!-- Sortir -->
                            <div class="relative pl-8">
                                <span class="absolute -left-[9px] top-0 bg-white dark:bg-gray-800 w-4 h-4 rounded-full border-2 border-indigo-500"></span>
                                <h4 class="font-bold text-gray-800 dark:text-gray-100">Sortir Material</h4>
                                <div class="mt-2 space-y-2">
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">PIC Sol</span> 
                                        <span class="font-medium">{{ $order->picSortirSol->name ?? '-' }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">PIC Upper</span>
                                        <span class="font-medium">{{ $order->picSortirUpper->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Production -->
                            <div class="relative pl-8">
                                <span class="absolute -left-[9px] top-0 bg-white dark:bg-gray-800 w-4 h-4 rounded-full border-2 border-blue-500"></span>
                                <h4 class="font-bold text-gray-800 dark:text-gray-100">Production</h4>
                                <div class="mt-2 space-y-2">
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">Sol</span>
                                        <span class="font-medium">{{ $order->prodSolBy->name ?? '-' }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">Upper</span>
                                        <span class="font-medium">{{ $order->prodUpperBy->name ?? '-' }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">Cleaning/Repaint</span>
                                        <span class="font-medium">{{ $order->prodCleaningBy->name ?? '-' }}</span>
                                    </div>
                                    {{-- Fallback for legacy data --}}
                                    @if(!$order->prodSolBy && !$order->prodUpperBy && !$order->prodCleaningBy && $order->technicianProduction)
                                        <div class="text-sm pt-2 border-t border-gray-100">
                                            <span class="text-gray-400 block text-xs">Teknisi (Legacy)</span>
                                            <span class="font-medium">{{ $order->technicianProduction->name ?? '-' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- QC -->
                            <div class="relative pl-8">
                                <span class="absolute -left-[9px] top-0 bg-white dark:bg-gray-800 w-4 h-4 rounded-full border-2 border-green-500"></span>
                                <h4 class="font-bold text-gray-800 dark:text-gray-100">Quality Control</h4>
                                <div class="mt-2 space-y-3 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Jahit</span>
                                        <span class="font-medium">{{ $order->qcJahitBy->name ?? $order->qcJahitTechnician->name ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Cleanup</span>
                                        <span class="font-medium">{{ $order->qcCleanupBy->name ?? $order->qcCleanupTechnician->name ?? '-' }}</span>
                                    </div>
                                    <div class="border-t border-gray-200 my-1"></div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 font-bold">FINAL CHECK</span>
                                        <span class="font-bold text-green-600">{{ $order->qcFinalBy->name ?? $order->qcFinalPic->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<script>
    function sendFinishEmail(id) {
        Swal.fire({
            title: 'Kirim Notifikasi Selesai?',
            text: "Sistem akan mengirimkan email notifikasi selesai ke customer.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Kirim!',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/finish/${id}/send-email`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.statusText)
                    }
                    return response.json()
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Request failed: ${error}`
                    )
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                if(result.value.success) {
                    Swal.fire({
                        title: 'Terkirim!',
                        text: result.value.message,
                        icon: 'success'
                    })
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: result.value.message,
                        icon: 'error'
                    })
                }
            }
        })
    }
</script>

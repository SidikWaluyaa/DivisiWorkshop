<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">
                    {{ __('Assessment Check') }}
                </h2>
                <div class="text-xs font-medium opacity-90 flex items-center gap-2">
                    <span class="bg-white/20 px-2 py-0.5 rounded text-white font-mono">
                        {{ $order->spk_number }}
                    </span>
                    <span>{{ $order->customer_name }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form id="assessment-form" action="{{ route('assessment.store', $order->id) }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Shoe Info & Notes -->
                    <div class="space-y-6 lg:col-span-1">
                        
                        <!-- Item Info Card -->
                        <div class="dashboard-card overflow-hidden">
                            <div class="dashboard-card-header">
                                <h3 class="dashboard-card-title text-base">
                                    👟 Info Sepatu
                                </h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <x-input-label for="shoe_brand" value="Brand Sepatu" class="text-xs uppercase text-gray-500 font-bold tracking-wider mb-1" />
                                    <input id="shoe_brand" name="shoe_brand" type="text" class="block w-full rounded-lg border-gray-300 bg-gray-50 focus:border-teal-500 focus:ring-teal-500 text-sm font-bold text-gray-800" value="{{ $order->shoe_brand }}" required />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="shoe_size" value="Size" class="text-xs uppercase text-gray-500 font-bold tracking-wider mb-1" />
                                        <input id="shoe_size" name="shoe_size" type="text" class="block w-full rounded-lg border-gray-300 bg-gray-50 focus:border-teal-500 focus:ring-teal-500 text-sm font-bold text-gray-800" value="{{ $order->shoe_size }}" required />
                                    </div>
                                    <div>
                                        <x-input-label for="shoe_color" value="Warna" class="text-xs uppercase text-gray-500 font-bold tracking-wider mb-1" />
                                        <input id="shoe_color" name="shoe_color" type="text" class="block w-full rounded-lg border-gray-300 bg-gray-50 focus:border-teal-500 focus:ring-teal-500 text-sm font-bold text-gray-800" value="{{ $order->shoe_color }}" required />
                                    </div>
                                </div>
                                
                                <div class="pt-4 border-t border-gray-100 mt-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 font-bold text-lg hidden sm:flex">
                                            {{ substr($order->customer_name, 0, 1) }}
                                        </div>
                                        <div class="w-full">
                                            <div class="flex justify-between items-center mb-1">
                                                <p class="text-xs text-gray-500 uppercase font-bold">Customer Info</p>
                                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded">{{ $order->customer_name }}</span>
                                            </div>
                                            
                                            <!-- Email Input -->
                                            <div>
                                                <x-input-label for="customer_email" value="Email (Untuk Notifikasi)" class="sr-only" />
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                    <input id="customer_email" name="customer_email" type="email" 
                                                        class="block w-full pl-9 rounded-lg border-gray-300 bg-white focus:border-teal-500 focus:ring-teal-500 text-sm font-medium text-gray-800 placeholder-gray-400" 
                                                        value="{{ old('customer_email', $order->customer_email) }}" 
                                                        placeholder="Masukkan Email Customer..." />
                                                </div>
                                                <x-input-error class="mt-1" :messages="$errors->get('customer_email')" />
                                            </div>

                                            <!-- Address Input -->
                                            <div class="mt-3">
                                                <x-input-label for="customer_address" value="Alamat (Untuk Pengiriman)" class="sr-only" />
                                                <div class="relative">
                                                    <div class="absolute top-2.5 left-0 pl-3 flex items-start pointer-events-none">
                                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                    </div>
                                                    <textarea id="customer_address" name="customer_address" rows="2"
                                                        class="block w-full pl-9 rounded-lg border-gray-300 bg-white focus:border-teal-500 focus:ring-teal-500 text-sm font-medium text-gray-800 placeholder-gray-400" 
                                                        placeholder="Masukkan Alamat Customer...">{{ old('customer_address', $order->customer_address) }}</textarea>
                                                </div>
                                                <x-input-error class="mt-1" :messages="$errors->get('customer_address')" />
                                            </div>

                                            <!-- Priority Input -->
                                            <div class="mt-3">
                                                <x-input-label for="priority" value="Prioritas Pengerjaan" class="text-xs uppercase text-gray-500 font-bold tracking-wider mb-1" />
                                                <select id="priority" name="priority" class="block w-full rounded-lg border-gray-300 bg-white focus:border-teal-500 focus:ring-teal-500 text-sm font-bold text-gray-800">
                                                    <option value="Reguler" {{ (old('priority', $order->priority) == 'Reguler' || old('priority', $order->priority) == 'Normal') ? 'selected' : '' }}>Reguler</option>
                                                    <option value="Prioritas" {{ (old('priority', $order->priority) == 'Prioritas' || old('priority', $order->priority) == 'Urgent' || old('priority', $order->priority) == 'Express') ? 'selected' : '' }}>Prioritas</option>
                                                </select>
                                                <x-input-error class="mt-1" :messages="$errors->get('priority')" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Card -->
                        <div class="dashboard-card overflow-hidden">
                             <div class="dashboard-card-header bg-orange-50 border-b-orange-100">
                                <h3 class="dashboard-card-title text-base text-orange-800">
                                    📝 Catatan / Kondisi
                                </h3>
                            </div>
                            <div class="p-6">
                                <textarea id="notes" name="notes" rows="6" class="block w-full rounded-lg border-gray-300 bg-yellow-50/50 focus:border-orange-500 focus:ring-orange-500 text-sm" placeholder="Contoh: Midsole menguning parah, lem lekang bagian tumit, ada baret di upper..."></textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                                <p class="text-xs text-gray-400 mt-2 italic">* Catat semua cacat awal untuk menghindari klaim customer.</p>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Services (Dropdown Style) -->
                    <div class="lg:col-span-2">
                        <div class="dashboard-card overflow-hidden h-full">
                            <div class="dashboard-card-header flex justify-between items-center">
                                <h3 class="dashboard-card-title">
                                    🛠️ Pilih Layanan (Services)
                                </h3>
                                <span class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded border">
                                    Dropdown Selection
                                </span>
                            </div>
                            
                            <div class="p-6" x-data="{ 
                                open: false, 
                                selected: [], 
                                services: {{ Js::from($services) }},
                                toggle(id, name, price) {
                                    const index = this.selected.findIndex(item => item.id === id);
                                    if (index > -1) {
                                        this.selected.splice(index, 1);
                                    } else {
                                        this.selected.push({ id: id, name: name, price: price });
                                    }
                                },
                                isSelected(id) {
                                    return this.selected.findIndex(item => item.id === id) > -1;
                                },
                                formatPrice(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                },
                                totalPrice() {
                                    return this.selected.reduce((sum, item) => sum + item.price, 0);
                                }
                            }">
                                <!-- Hidden Inputs for Form Submission -->
                                <template x-for="item in selected" :key="item.id">
                                    <input type="hidden" name="services[]" :value="item.id">
                                </template>

                                <div class="relative">
                                    <!-- Trigger Button -->
                                    <button @click="open = !open" @click.away="open = false" type="button" class="w-full bg-white border border-gray-300 rounded-lg py-3 px-4 flex justify-between items-center shadow-sm hover:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all">
                                        <span class="text-gray-700 font-medium" x-text="selected.length > 0 ? selected.length + ' Layanan Dipilih' : 'Pilih Layanan...'"></span>
                                        <svg class="w-5 h-5 text-gray-500 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 transform scale-95"
                                         x-transition:enter-end="opacity-100 transform scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 transform scale-100"
                                         x-transition:leave-end="opacity-0 transform scale-95"
                                         class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-2xl max-h-96 overflow-y-auto ring-1 ring-black ring-opacity-5">
                                        
                                        @foreach($services as $category => $serviceList)
                                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100 font-bold text-teal-800 text-xs uppercase tracking-wider sticky top-0">
                                            {{ $category }}
                                        </div>
                                        <div class="divide-y divide-gray-100">
                                            @foreach($serviceList as $service)
                                            <div @click="toggle({{ $service->id }}, '{{ $service->name }}', {{ $service->price }})" 
                                                 class="px-4 py-3 cursor-pointer hover:bg-teal-50 transition-colors flex items-center justify-between group">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-5 h-5 rounded border border-gray-300 flex items-center justify-center transition-colors"
                                                         :class="isSelected({{ $service->id }}) ? 'bg-teal-500 border-teal-500' : 'bg-white'">
                                                        <svg x-show="isSelected({{ $service->id }})" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-700 group-hover:text-teal-700">{{ $service->name }}</span>
                                                </div>
                                                <span class="text-xs font-mono text-gray-500">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Selected Items Tags -->
                                <div class="mt-4 flex flex-wrap gap-2" x-show="selected.length > 0" x-cloak>
                                    <template x-for="item in selected" :key="item.id">
                                        <div class="inline-flex items-center bg-teal-50 border border-teal-100 rounded-full px-3 py-1">
                                            <span class="text-xs font-bold text-teal-700" x-text="item.name"></span>
                                            <span class="ml-2 text-xs text-teal-500 font-mono" x-text="formatPrice(item.price)"></span>
                                            <button @click="toggle(item.id, item.name, item.price)" type="button" class="ml-2 text-teal-400 hover:text-red-500 focus:outline-none">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                <!-- Total Price -->
                                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-end" x-show="selected.length > 0" x-cloak>
                                    <div class="text-sm text-gray-500">Total Estimasi Biaya</div>
                                    <div class="text-2xl font-black text-orange-500" x-text="formatPrice(totalPrice())"></div>
                                </div>
                                <div class="mt-2 text-center" x-show="selected.length === 0">
                                    <span class="text-sm text-gray-400 italic">Belum ada layanan dipilih</span>
                                </div>

                                <x-input-error class="mt-4" :messages="$errors->get('services')" />
                            </div>

                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                                <a href="{{ route('assessment.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
                                    &larr; Batal & Kembali
                                </a>
                                <button type="button" onclick="confirmAssessment()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-teal-500 to-teal-700 hover:from-teal-600 hover:to-teal-800 text-white font-bold rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all text-sm uppercase tracking-wider">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Simpan Assessment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                function confirmAssessment() {
                    const form = document.getElementById('assessment-form');
                    // Manual validity check
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    Swal.fire({
                        title: 'Simpan Assessment?',
                        text: "Pastikan data sepatu, foto, dan layanan sudah benar. Lanjut?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10B981', // green
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Ya, Simpan!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
                </script>
            </form>
        </div>
    </div>
</x-app-layout>

<div x-data="{ showPreview: false, previewUrl: '' }">
    {{-- Flatpickr Styles & Scripts --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-xl text-white leading-tight flex items-center gap-2">
                <div class="p-2 bg-emerald-500/20 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                {{ __('Riwayat Pengambilan Sepatu') }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-medium text-emerald-200 border border-white/10 backdrop-blur-md">
                    Divisi Gudang
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-6 shadow-xl shadow-emerald-500/20 text-white relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-10 transform rotate-12 group-hover:scale-110 transition-transform duration-500">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.828a1 1 0 101.414-1.414L11 9.586V6z" clip-rule="evenodd"></path></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-emerald-100 text-sm font-medium mb-1">Hari Ini</p>
                    <h3 class="text-4xl font-black">{{ $stats['today'] }} <span class="text-lg font-normal text-emerald-200">Sepatu</span></h3>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl p-6 shadow-xl shadow-blue-500/20 text-white relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-10 transform rotate-12 group-hover:scale-110 transition-transform duration-500">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-blue-100 text-sm font-medium mb-1">Minggu Ini</p>
                    <h3 class="text-4xl font-black">{{ $stats['week'] }} <span class="text-lg font-normal text-blue-200">Sepatu</span></h3>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-fuchsia-600 rounded-3xl p-6 shadow-xl shadow-purple-500/20 text-white relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-10 transform rotate-12 group-hover:scale-110 transition-transform duration-500">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-purple-100 text-sm font-medium mb-1">Bulan Ini</p>
                    <h3 class="text-4xl font-black">{{ $stats['month'] }} <span class="text-lg font-normal text-purple-200">Sepatu</span></h3>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-xl border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Total Keseluruhan</p>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 mb-8">
            <div class="flex flex-col lg:flex-row gap-6 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Pencarian SPK / Customer</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" 
                               class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-emerald-500 text-sm transition-all" 
                               placeholder="Ketik nomor SPK, nama customer, atau merk sepatu...">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
                    <!-- Date Picker Range (Flatpickr) -->
                    <div wire:ignore class="relative w-full sm:w-auto" x-data="{
                        initFlatpickr() {
                            flatpickr($refs.datePicker, {
                                mode: 'range',
                                dateFormat: 'Y-m-d',
                                defaultDate: [@js($startDate), @js($endDate)],
                                locale: {
                                    rangeSeparator: ' s/d '
                                },
                                onChange: (selectedDates, dateStr, instance) => {
                                    if (selectedDates.length === 2) {
                                        let start = instance.formatDate(selectedDates[0], 'Y-m-d');
                                        let end = instance.formatDate(selectedDates[1], 'Y-m-d');
                                        $wire.set('startDate', start);
                                        $wire.set('endDate', end);
                                    } else if (selectedDates.length === 0) {
                                        $wire.set('startDate', '');
                                        $wire.set('endDate', '');
                                    }
                                }
                            });

                            $watch('$wire.startDate', (value) => {
                                if ($refs.datePicker._flatpickr) {
                                    if (!value) {
                                        $refs.datePicker._flatpickr.clear();
                                    } else {
                                        $refs.datePicker._flatpickr.setDate([value, $wire.endDate], false);
                                    }
                                }
                            });
                            $watch('$wire.endDate', (value) => {
                                if ($refs.datePicker._flatpickr && value) {
                                    $refs.datePicker._flatpickr.setDate([$wire.startDate, value], false);
                                }
                            });
                        }
                    }" x-init="initFlatpickr()">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Rentang Tanggal</label>
                        <input x-ref="datePicker" type="text" readonly 
                            class="w-full sm:w-56 px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-emerald-500 text-sm transition-all cursor-pointer text-center"
                            placeholder="Pilih Rentang Tanggal...">
                    </div>

                    <!-- Pickup Method Filter -->
                    <div class="w-full sm:w-auto">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Metode Pengambilan</label>
                        <select wire:model.live="pickup_method_filter" 
                            class="w-full sm:w-48 px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-emerald-500 text-sm transition-all cursor-pointer">
                            <option value="">Semua Metode</option>
                            <option value="offline">Offline / Direct Pickup</option>
                            <option value="delivery">Delivery / Ekspedisi</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button wire:click="resetFilters" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-2xl hover:bg-gray-200 transition-colors font-bold text-sm">
                        Reset
                    </button>
                    
                    <a href="{{ route('storage.pickup-history.print') }}?search={{ urlencode($search) }}&startDate={{ urlencode($startDate) }}&endDate={{ urlencode($endDate) }}&sort={{ urlencode($sort) }}&pickup_method_filter={{ urlencode($pickup_method_filter) }}" 
                        target="_blank"
                        class="px-6 py-3 bg-[#22B086] hover:bg-[#1fa17a] text-white rounded-2xl transition-colors font-bold text-sm flex items-center gap-2 shadow-md shadow-emerald-500/10">
                        🖨️ Cetak
                    </a>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Data Sepatu</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Waktu Ambil</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Metode</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Logistik & Margin</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Layanan</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($orders as $order)
                        <tr class="hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border-2 border-transparent group-hover:border-emerald-500 transition-all shadow-sm cursor-zoom-in"
                                         @if($order->spk_cover_photo_url) 
                                            @click="previewUrl = '{{ $order->spk_cover_photo_url }}'; showPreview = true" 
                                         @endif>
                                        @if($order->spk_cover_photo_url)
                                            <img src="{{ $order->spk_cover_photo_url }}" 
                                                 class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                                                 alt="SPK Cover Photo">
                                        @else
                                            <svg class="w-6 h-6 text-gray-400 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gray-900 dark:text-white font-mono">{{ $order->spk_number }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $order->shoe_brand }} {{ $order->shoe_type }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $order->customer_name }}</div>
                                <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ \Carbon\Carbon::parse($order->taken_date)->format('d M Y') }}
                                    </span>
                                    <span class="text-[10px] text-gray-400">Jam: {{ \Carbon\Carbon::parse($order->taken_date)->format('H:i') }} WIB</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2 group">
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                        {{ $order->pickup_method ?: '-' }}
                                    </span>
                                    <button onclick="editPickupMethod({{ $order->id }}, '{{ addslashes($order->pickup_method) }}')" 
                                            class="p-1 text-gray-300 hover:text-blue-600 transition-colors opacity-0 group-hover:opacity-100"
                                            title="Edit Metode">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @php
                                    $custOngkir = $order->invoice ? $order->invoice->shipping_cost : $order->shipping_cost;
                                    $realOngkir = $order->actual_shipping_cost ?? 0;
                                    $margin = $custOngkir - $realOngkir;
                                @endphp
                                <div class="flex flex-col gap-1">
                                    <div class="flex justify-between text-[10px]">
                                        <span class="text-gray-400">Cust:</span>
                                        <span class="font-bold text-gray-600 dark:text-gray-300">Rp {{ number_format($custOngkir, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-[10px] group/edit-real relative">
                                        <span class="text-gray-400">Real:</span>
                                        <div class="flex items-center gap-1">
                                            <span class="font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($realOngkir, 0, ',', '.') }}</span>
                                            <button onclick="editActualOngkir({{ $order->id }}, '{{ addslashes($order->pickup_method) }}', {{ $realOngkir }})" 
                                                    class="opacity-0 group-hover/edit-real:opacity-100 transition-opacity text-gray-300 hover:text-indigo-500">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="pt-1 mt-1 border-t border-gray-50 dark:border-gray-700 flex justify-between text-[10px]">
                                        <span class="text-gray-400">Margin:</span>
                                        <span class="font-black {{ $margin >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                            {{ $margin >= 0 ? '+' : '' }}Rp {{ number_format($margin, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($order->workOrderServices as $svc)
                                        <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded text-[10px] font-bold">
                                            {{ $svc->service->name ?? $svc->custom_service_name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right flex justify-end gap-2">
                                <button wire:click="undoPickup({{ $order->id }})" 
                                        wire:confirm="Apakah Anda yakin ingin membatalkan pengambilan ini? Sepatu akan kembali ke status 'Menunggu Disimpan' di Gudang Finish."
                                        class="p-2 text-gray-400 hover:text-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-xl transition-all"
                                        title="Kembalikan ke Menunggu Disimpan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                    </svg>
                                </button>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-xs font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition-all shadow-sm">
                                    Detail
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center opacity-40">
                                    <svg class="w-20 h-20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    <p class="text-xl font-bold">Tidak ada riwayat pengambilan</p>
                                    <p class="text-sm">Gunakan filter atau cari nomor SPK lain.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($orders->hasPages())
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Image Preview Modal --}}
    <div x-show="showPreview" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm"
         x-cloak
         @keydown.escape.window="showPreview = false">
        
        <button @click="showPreview = false" class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <img :src="previewUrl" 
             @click.away="showPreview = false"
             class="max-w-full max-h-[90vh] rounded-2xl shadow-2xl border-4 border-white/10 object-contain"
             alt="Enlarged Preview">
    </div>
    <script>
        function editPickupMethod(orderId, currentMethod) {
            Swal.fire({
                title: 'Edit Metode Pengambilan',
                input: 'text',
                inputLabel: 'Masukkan metode pengambilan baru:',
                inputValue: currentMethod || 'Offline',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Metode tidak boleh kosong!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('updatePickupMethod', orderId, result.value);
                }
            })
        }

        function editActualOngkir(orderId, currentMethod, currentCost) {
            Swal.fire({
                title: 'Edit Ongkir Real',
                html: `
                    <div class="text-left">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Metode:</label>
                        <input id="swal-method" class="swal2-input !mt-0 !mb-4 !w-full" value="${currentMethod}">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ongkir Real (Workshop):</label>
                        <input id="swal-cost" type="number" class="swal2-input !mt-0 !w-full" value="${currentCost}">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    return [
                        document.getElementById('swal-method').value,
                        document.getElementById('swal-cost').value
                    ]
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('updatePickupMethod', orderId, result.value[0], result.value[1]);
                }
            })
        }

        window.addEventListener('swal', event => {
            const data = event.detail[0] || event.detail;
            Swal.fire({
                icon: data.icon,
                title: data.title,
                text: data.text,
                timer: 3000,
                showConfirmButton: false
            });
        });
    </script>
</div>

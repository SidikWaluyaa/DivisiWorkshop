<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="p-2.5 bg-white/20 rounded-xl backdrop-blur-sm shadow-sm border border-white/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl leading-tight">
                        Assessment & Detailing
                    </h2>
                    <div class="text-sm opacity-90 font-medium">
                        {{ $order->spk_number }}
                    </div>
                </div>
            </div>
            <div class="text-sm text-white/80 bg-white/10 px-4 py-2 rounded-lg backdrop-blur-sm border border-white/20">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    {{ $order->created_at->format('d M Y H:i') }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('assessment.store', $order->id) }}" method="POST" id="assessmentForm" x-data="assessmentServiceForm()">
                @csrf
                
                <div class="grid grid-cols-12 gap-6">
                    {{-- LEFT COLUMN: PHOTO UPLOAD --}}
                    <div class="col-span-12 lg:col-span-7">
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl h-full flex flex-col border border-gray-100">
                            <div class="p-5 border-b bg-gradient-to-r from-teal-50 to-teal-100/50 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-teal-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md">
                                        1
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-800 text-lg">Dokumentasi Fisik</h3>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <svg class="w-3 h-3 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-xs text-teal-700 font-semibold">Watermark Otomatis</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('assessment.print-spk', $order->id) }}" target="_blank" 
                                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:from-orange-600 hover:to-orange-700 transition-all transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    <span>Print SPK</span>
                                </a>
                            </div>
                            
                            {{-- Drop Zone --}}
                            <div class="p-6 flex-1 flex flex-col gap-6">
                                <div id="drop-zone" 
                                     class="border-4 border-dashed border-gray-300 rounded-2xl bg-gradient-to-br from-gray-50 to-white hover:from-teal-50 hover:to-teal-100/30 hover:border-teal-400 transition-all duration-300 cursor-pointer group min-h-[220px] flex flex-col items-center justify-center relative"
                                     onclick="document.getElementById('file_input').click()">
                                    
                                    <input type="file" id="file_input" multiple accept="image/*" class="hidden">
                                    
                                    <div class="text-center p-8 pointer-events-none">
                                        <div class="mb-4 text-gray-400 group-hover:text-teal-500 transition-colors">
                                            <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <h4 class="text-xl font-bold text-gray-700 mb-2 group-hover:text-teal-700 transition-colors">Drag & Drop Foto Disini</h4>
                                        <p class="text-sm text-gray-500 mb-1">Atau klik untuk memilih foto</p>
                                        <p class="text-xs text-gray-400">(Bisa upload banyak sekaligus)</p>
                                    </div>

                                    {{-- Loading Overlay --}}
                                    <div id="upload-loading" class="absolute inset-0 bg-white/95 z-10 hidden flex-col items-center justify-center rounded-2xl backdrop-blur-sm">
                                        <svg class="animate-spin h-12 w-12 text-teal-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="font-bold text-teal-700 text-lg animate-pulse">Sedang Mengupload & Compress...</span>
                                    </div>
                                </div>

                                {{-- Gallery Grid --}}
                                <div x-data="photoModal()">
                                    <h4 class="font-bold text-gray-700 mb-3 text-sm uppercase tracking-wider flex items-center gap-2">
                                        <svg class="w-4 h-4 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                        </svg>
                                        Gallery Foto
                                    </h4>
                                    <div id="gallery-grid" class="grid grid-cols-4 gap-3">
                                        {{-- Existing Photos --}}
                                        @foreach($order->photos as $photo)
                                            <div @click="openModal('{{ Storage::url($photo->file_path) }}', {{ $photo->id }})"
                                                 class="relative group aspect-square rounded-xl overflow-hidden shadow-md border-2 border-gray-200 hover:border-teal-400 transition-all duration-300 cursor-pointer">
                                                <img src="{{ Storage::url($photo->file_path) }}" 
                                                     alt="Photo {{ $loop->iteration }}"
                                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center">
                                                    <svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                                    </svg>
                                                </div>
                                                <button type="button" @click.stop="deletePhoto({{ $photo->id }}, $el)" 
                                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600 z-10 shadow-lg" title="Hapus Foto">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div id="empty-gallery-msg" class="text-center text-sm text-gray-400 py-8 italic {{ $order->photos->count() > 0 ? 'hidden' : '' }}">
                                        Belum ada foto yang diupload.
                                    </div>

                                    {{-- Photo Modal --}}
                                    <div x-show="isOpen" 
                                         x-cloak
                                         @keydown.escape.window="closeModal()"
                                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0">
                                        
                                        <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl max-h-[90vh] w-full flex flex-col"
                                             @click.away="closeModal()">
                                            
                                            {{-- Header --}}
                                            <div class="flex items-center justify-between p-4 border-b bg-gradient-to-r from-teal-50 to-teal-100">
                                                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Preview Foto
                                                </h3>
                                                <div class="flex items-center gap-2">
                                                    <a :href="currentImage" 
                                                       download 
                                                       class="px-3 py-1.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg font-bold text-sm hover:from-orange-600 hover:to-orange-700 transition-all flex items-center gap-1.5">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                        </svg>
                                                        Download
                                                    </a>
                                                    <button type="button"
                                                            @click.prevent="closeModal()" 
                                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Image Container --}}
                                            <div class="flex-1 overflow-auto p-6 bg-gray-50 flex items-center justify-center">
                                                <img :src="currentImage" 
                                                     alt="Preview" 
                                                     class="max-w-full max-h-full object-contain rounded-lg shadow-lg">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN: INFO & SERVICES --}}
                    <div class="col-span-12 lg:col-span-5 flex flex-col gap-6">
                        
                        {{-- Identity Card --}}
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100">
                            <div class="p-5 border-b bg-gradient-to-r from-teal-50 to-teal-100/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-teal-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md">
                                        2
                                    </div>
                                    <h3 class="font-bold text-gray-800 text-lg">Identitas & Pelanggan</h3>
                                </div>
                            </div>
                            <div class="p-6 space-y-5">
                                {{-- Customer Details --}}
                                <div class="border-b pb-5">
                                    <h4 class="text-xs font-bold text-teal-700 uppercase mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                        Biodata Pelanggan
                                    </h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Nama Customer</label>
                                            <input type="text" value="{{ $order->customer_name }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-700 font-medium" readonly>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">No. WhatsApp</label>
                                            <input type="text" value="{{ $order->customer_phone }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-700 font-medium" readonly>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Email</label>
                                            <input type="email" value="{{ $order->customer_email }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-700 font-medium" readonly>
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase">Alamat Lengkap (Master Customer)</label>
                                            <div class="w-full px-3 py-2 border border-blue-200 rounded-lg text-sm bg-blue-50 text-blue-700 font-medium leading-relaxed">
                                                <div class="font-bold mb-1">{{ $order->customer->address ?? '-' }}</div>
                                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-[11px] opacity-80 uppercase tracking-tight">
                                                    <div><span class="font-normal lowercase italic text-gray-500 mr-2">kelurahan:</span> {{ $order->customer->village ?? '-' }}</div>
                                                    <div><span class="font-normal lowercase italic text-gray-500 mr-2">kecamatan:</span> {{ $order->customer->district ?? '-' }}</div>
                                                    <div><span class="font-normal lowercase italic text-gray-500 mr-2">kota/kab:</span> {{ $order->customer->city ?? '-' }}</div>
                                                    <div><span class="font-normal lowercase italic text-gray-500 mr-2">provinsi:</span> {{ $order->customer->province ?? '-' }}</div>
                                                    <div class="col-span-2"><span class="font-normal lowercase italic text-gray-500 mr-2">kode pos:</span> {{ $order->customer->postal_code ?? '-' }}</div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="customer_address" value="{{ $order->customer_address }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Reception Info --}}
                                <div class="border-b pb-5">
                                    <h4 class="text-xs font-bold text-teal-700 uppercase mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Info Dari Gudang
                                    </h4>
                                    
                                    {{-- Accessories --}}
                                    <div class="grid grid-cols-4 gap-2 mb-3">
                                        @php 
                                            $accessories = [
                                                ['key' => 'accessories_tali', 'label' => 'Tali'],
                                                ['key' => 'accessories_insole', 'label' => 'Insol'],
                                                ['key' => 'accessories_box', 'label' => 'Box'],
                                            ];
                                        @endphp
                                        @foreach($accessories as $acc)
                                            @php $val = $order->{$acc['key']} ?? 'Tidak Ada'; @endphp
                                            <div class="border-2 rounded-lg p-2 text-center transition-all {{ in_array($val, ['Tidak Ada', '']) ? 'bg-gray-50 border-gray-200 opacity-60' : 'bg-gradient-to-br from-teal-50 to-white border-teal-300' }}">
                                                <div class="text-[9px] uppercase text-gray-500 font-bold mb-0.5">{{ $acc['label'] }}</div>
                                                <div class="font-bold text-xs {{ $val == 'Simpan' ? 'text-blue-600' : ($val == 'Nempel' ? 'text-orange-600' : 'text-gray-400') }}">
                                                    {{ $val == 'Simpan' ? 'Disimpan' : ($val == 'Nempel' ? 'Nempel' : 'Tidak Ada') }}
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        <div class="border-2 rounded-lg p-2 text-center transition-all {{ empty($order->accessories_other) ? 'bg-gray-50 border-gray-200 opacity-60' : 'bg-gradient-to-br from-purple-50 to-white border-purple-300' }}">
                                            <div class="text-[9px] uppercase text-gray-500 font-bold mb-0.5">Lainnya</div>
                                            <div class="font-bold text-xs {{ !empty($order->accessories_other) ? 'text-purple-600' : 'text-gray-400' }}">
                                                {{ !empty($order->accessories_other) ? 'Ada' : 'Tidak Ada' }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if(!empty($order->accessories_other))
                                        <div class="text-xs bg-purple-50 text-purple-700 p-2.5 rounded-lg border border-purple-200 mb-3">
                                            <strong>Ket. Lainnya:</strong> {{ $order->accessories_other }}
                                        </div>
                                    @endif

                                    {{-- Requested Services --}}
                                    @if($order->services->count() > 0)
                                        <div class="mt-3">
                                            <span class="font-bold text-gray-700 block mb-2 text-xs">Request Awal (CS):</span>
                                            <ul class="space-y-1">
                                                @foreach($order->services as $s)
                                                    <li class="text-xs text-gray-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200 flex items-center gap-2">
                                                        <svg class="w-3 h-3 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        {{ $s->name }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                                {{-- Shoe Details --}}
                                <div>
                                    <h4 class="text-xs font-bold text-teal-700 uppercase mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z"/>
                                            <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z"/>
                                            <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z"/>
                                        </svg>
                                        Detail Sepatu
                                    </h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Brand</label>
                                            <input type="text" name="shoe_brand" value="{{ $order->shoe_brand }}" required class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Size</label>
                                            <input type="text" name="shoe_size" value="{{ $order->shoe_size }}" required class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all">
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Warna</label>
                                            <input type="text" name="shoe_color" value="{{ $order->shoe_color }}" required class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all">
                                        </div>
                                        {{-- NOTES SECTION: SEPARATED --}}
                                    
                                    {{-- 1. Customer/CS Notes (Readonly) --}}
                                    <div class="mb-4 col-span-2">
                                        <label class="block text-xs font-bold text-gray-600 mb-1.5 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            Catatan Awal Customer (CS)
                                        </label>
                                        <div class="w-full px-3 py-3 border border-blue-200 rounded-lg text-sm bg-blue-50 text-blue-800 italic">
                                            "{{ $order->notes ?: 'Tidak ada catatan khusus dari customer.' }}"
                                        </div>
                                        {{-- Hidden input to keep existing notes value if we want to preserve it / not delete it --}}
                                        <input type="hidden" name="notes" value="{{ $order->notes }}"> 
                                    </div>

                                    {{-- 2. Technician Instructions (New Input) --}}
                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-gray-600 mb-1.5 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Instrusksi Khusus Teknisi (Untuk Workshop)
                                        </label>
                                        <textarea name="technician_notes" x-model="technician_notes" rows="4" class="w-full px-3 py-2 border-2 border-amber-200 rounded-lg text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all placeholder-gray-400" placeholder="Contoh: Hati-hati bagian heel counter rapuh, Gunakan lem grafton tipis...">{{ $order->technician_notes }}</textarea>
                                        <p class="text-[10px] text-gray-500 mt-1">*Catatan ini akan muncul dengan highlight peringatan di HP Teknisi.</p>
                                    </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Prioritas</label>
                                            <select name="priority" class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all">
                                                @foreach(['Normal', 'Urgent', 'Express', 'Reguler', 'Prioritas'] as $p)
                                                    <option value="{{ $p }}" {{ $order->priority == $p ? 'selected' : '' }}>{{ $p }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Tanggal Masuk</label>
                                            <input type="date" name="entry_date" value="{{ $order->entry_date ? $order->entry_date->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Estimasi Selesai</label>
                                            <input type="date" name="estimation_date" value="{{ $order->estimation_date ? $order->estimation_date->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Services Selection (Refactored) --}}
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl flex-1 border border-gray-100">
                            <div class="p-5 border-b bg-gradient-to-r from-teal-50 to-teal-100/50 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-teal-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md">
                                        3
                                    </div>
                                    <h3 class="font-bold text-gray-800 text-lg">Pilih Layanan</h3>
                                </div>
                                <button type="button" @click="openServiceModal()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-bold text-sm shadow-md transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah Layanan
                                </button>
                            </div>

                            <div class="p-6">
                                {{-- Service Table --}}
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Layanan</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Detail</th>
                                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Harga</th>
                                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <template x-if="selectedServices.length === 0">
                                                <tr>
                                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 italic text-sm">
                                                        Belum ada layanan yang dipilih. Klik "Tambah Layanan" untuk memulai.
                                                    </td>
                                                </tr>
                                            </template>
                                            <template x-for="(svc, index) in selectedServices" :key="index">
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-4 py-3">
                                                        <div class="font-bold text-gray-800" x-text="svc.name || svc.custom_name"></div>
                                                        <div class="text-xs text-gray-500" x-show="svc.service_id === 'custom'">(Custom Service)</div>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-teal-100 text-teal-800" x-text="svc.category"></span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="flex flex-wrap gap-1">
                                                            <template x-for="detail in svc.details">
                                                                <span class="bg-gray-100 text-gray-600 text-[10px] px-2 py-0.5 rounded border border-gray-200" x-text="detail"></span>
                                                            </template>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 text-right font-mono text-sm font-bold text-gray-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(svc.price)"></td>
                                                    <td class="px-4 py-3 text-center">
                                                        <button type="button" @click="removeService(index)" class="text-red-500 hover:text-red-700 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                        
                                                        {{-- Hidden Inputs --}}
                                                        <input type="hidden" :name="`services[${index}][service_id]`" :value="svc.service_id">
                                                        <input type="hidden" :name="`services[${index}][custom_name]`" :value="svc.custom_name || svc.name">
                                                        <input type="hidden" :name="`services[${index}][category]`" :value="svc.category">
                                                        <input type="hidden" :name="`services[${index}][price]`" :value="svc.price">
                                                        <template x-for="detail in svc.details">
                                                            <input type="hidden" :name="`services[${index}][details][]`" :value="detail">
                                                        </template>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            {{-- Service Modal --}}
                            <div x-show="showServiceModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showServiceModal = false">
                                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                    </div>
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Tambah Layanan</h3>
                                            
                                            <div class="space-y-4">
                                                {{-- Category Select --}}
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                                    <select x-model="serviceForm.category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                                        <option value="">-- Pilih Kategori --</option>
                                                        <option value="Custom">Custom / Manual</option>
                                                        <template x-for="cat in uniqueCategories" :key="cat">
                                                            <option :value="cat" x-text="cat"></option>
                                                        </template>
                                                    </select>
                                                </div>

                                                {{-- Service Select --}}
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Layanan</label>
                                                    <select x-model="serviceForm.service_id" @change="selectService()" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50" :disabled="!serviceForm.category">
                                                        <option value="">-- Pilih Layanan --</option>
                                                        <template x-for="svc in filteredServices" :key="svc.id">
                                                            <option :value="svc.id" x-text="svc.name + ' (' + new Intl.NumberFormat('id-ID').format(svc.price) + ')'"></option>
                                                        </template>
                                                        <option value="custom">+ Input Manual (Custom)</option>
                                                    </select>
                                                </div>

                                                {{-- Custom Name --}}
                                                <div x-show="serviceForm.service_id === 'custom'">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Layanan Custom</label>
                                                    <input type="text" x-model="serviceForm.custom_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50" placeholder="Contoh: Repaint Khusus">
                                                </div>

                                                {{-- Price --}}
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                                                    <input type="number" x-model="serviceForm.price" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                                </div>

                                                {{-- Details --}}
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Detail Tambahan (Opsional)</label>
                                                    <div class="flex gap-2 mb-2">
                                                        <input type="text" x-model="serviceForm.newDetail" @keydown.enter.prevent="addDetail()" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50" placeholder="Contoh: Jahit Sol, Extra Wangi">
                                                        <button type="button" @click="addDetail()" class="px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300 text-gray-700 font-bold">+</button>
                                                    </div>
                                                    <div class="flex flex-wrap gap-2">
                                                        <template x-for="(detail, idx) in serviceForm.details" :key="idx">
                                                            <span class="bg-teal-50 text-teal-700 px-2 py-1 rounded-md text-xs border border-teal-100 flex items-center gap-1">
                                                                <span x-text="detail"></span>
                                                                <button type="button" @click="removeDetail(idx)" class="text-teal-400 hover:text-teal-600 font-bold">&times;</button>
                                                            </span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="button" @click="saveService()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                Simpan
                                            </button>
                                            <button type="button" @click="showServiceModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            
                            {{-- Total Summary --}}
                            <div class="p-5 bg-gradient-to-r from-teal-600 to-teal-700 border-t-4 border-teal-800 text-white">


                                {{-- Discount Section --}}
                                <div class="mb-3 pb-3 border-b border-teal-500/50">
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="text-xs font-bold text-teal-100">Potongan / Diskon (Rp)</label>
                                    </div>
                                    <input type="number" name="discount" id="discount-input" x-model="discount"
                                           class="w-full text-right bg-black/20 border border-teal-500 rounded px-2 py-1.5 text-sm rounded focus:ring-2 focus:ring-white/50 text-white placeholder-teal-200/50" 
                                           placeholder="0">
                                </div>

                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-sm font-bold text-white/90">Total Estimasi Netto</span>
                                    <span class="text-2xl font-black text-white" id="totalPriceDisplay" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(calculateTotal())">Rp 0</span>
                                </div>

                                <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Simpan & Lanjut ke Pembayaran
                                </button>
                            </div>
                        </div>

                        {{-- Hidden Container for Custom Services --}}
                        <div id="custom-services-inputs"></div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Javascript --}}
    <script>
        // Alpine.js Photo Modal Component
        function photoModal() {
            return {
                isOpen: false,
                currentImage: '',
                currentPhotoId: null,

                openModal(imageUrl, photoId) {
                    this.currentImage = imageUrl;
                    this.currentPhotoId = photoId;
                    this.isOpen = true;
                    document.body.style.overflow = 'hidden';
                },

                closeModal() {
                    this.isOpen = false;
                    document.body.style.overflow = '';
                },

                deletePhoto(id, element) {
                    if(!confirm('Hapus foto ini?')) return;
                    
                    fetch(`/photos/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            element.closest('div.group').remove();
                            if (document.getElementById('gallery-grid').children.length === 0) {
                                document.getElementById('empty-gallery-msg').classList.remove('hidden');
                            }
                            this.closeModal();
                        } else {
                            alert('Gagal menghapus foto.');
                        }
                    });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('file_input');
            const galleryGrid = document.getElementById('gallery-grid');
            const emptyMsg = document.getElementById('empty-gallery-msg');
            const loadingOverlay = document.getElementById('upload-loading');

            // Drag & Drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.add('border-teal-500', 'ring-4', 'ring-teal-200'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.remove('border-teal-500', 'ring-4', 'ring-teal-200'), false);
            });

            dropZone.addEventListener('drop', (e) => handleFiles(e.dataTransfer.files), false);
            fileInput.addEventListener('change', (e) => handleFiles(e.target.files), false);

            function handleFiles(files) {
                if (files.length === 0) return;
                
                loadingOverlay.classList.remove('hidden');
                loadingOverlay.classList.add('flex');

                const uploadPromises = Array.from(files).map((file, index) => uploadFile(file, index));

                Promise.all(uploadPromises).then(() => {
                    loadingOverlay.classList.remove('flex');
                    loadingOverlay.classList.add('hidden');
                    fileInput.value = '';
                });
            }

            function uploadFile(file, index) {
                return new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append('photo', file);
                    formData.append('step', 'bulk_' + Date.now() + '_' + index);
                    formData.append('is_public', 1);

                    fetch('{{ route("work-order-photos.store", $order->id) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            addPhotoToGallery(data.photo, data.url);
                            resolve(data);
                        } else {
                            alert('Gagal upload ' + file.name + ': ' + data.message);
                            resolve(null);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        resolve(null);
                    });
                });
            }

            function addPhotoToGallery(photo, url) {
                emptyMsg.classList.add('hidden');
                
                const div = document.createElement('div');
                div.className = 'relative group aspect-square rounded-xl overflow-hidden shadow-md border-2 border-gray-200 hover:border-teal-400 transition-all duration-300 cursor-pointer animate-fade-in';
                div.onclick = function() {
                    // Find the parent Alpine component and call openModal
                    const parentComponent = Alpine.$data(galleryGrid.closest('[x-data]'));
                    if (parentComponent && parentComponent.openModal) {
                        parentComponent.openModal(url, photo.id);
                    }
                };
                div.innerHTML = `
                    <img src="${url}" alt="Photo" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center">
                        <svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </div>
                    <button type="button" onclick="event.stopPropagation(); deletePhotoFromGallery(${photo.id}, this)" 
                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600 z-10 shadow-lg" title="Hapus Foto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                `;
                galleryGrid.appendChild(div);
            }

            // Global delete function for dynamically added photos
            window.deletePhotoFromGallery = function(id, btnElement) {
                if(!confirm('Hapus foto ini?')) return;
                
                fetch(`/photos/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        btnElement.closest('div.group').remove();
                        if (document.getElementById('gallery-grid').children.length === 0) {
                            document.getElementById('empty-gallery-msg').classList.remove('hidden');
                        }
                    } else {
                        alert('Gagal menghapus foto.');
                    }
                });
            };

            // Price Calculation & Custom Services Logic
            // Legacy Script Removed - Replaced by AlpineJS logic below
        });
    </script>
</x-app-layout>

<script>
    function assessmentServiceForm() {
        return {
            masterServices: @json($services),
            selectedServices: [
                @foreach($order->workOrderServices as $wos)
                {
                    service_id: '{{ $wos->service_id ?? "custom" }}',
                    name: '{{ $wos->service_id ? $wos->service->name : $wos->custom_service_name }}',
                    custom_name: '{{ $wos->custom_service_name }}',
                    category: '{{ $wos->category_name }}',
                    price: {{ $wos->cost }},
                    details: @json($wos->service_details ?? [])
                },
                @endforeach
            ],
            showServiceModal: false,
            serviceForm: {
                category: '',
                service_id: '',
                custom_name: '',
                price: 0,
                details: [],
                newDetail: ''
            },
            
            technician_notes: '{{ $order->technician_notes }}',
            discount: {{ $order->discount ?? 0 }},
            shipping_cost: 0,

            init() {
                this.$watch('selectedServices', (value) => {
                    this.updateTechnicianNotes();
                });
            },

            updateTechnicianNotes() {
                // Auto-generate notes from services
                let notes = "Layanan: \n";
                this.selectedServices.forEach(s => {
                    notes += `- ${s.name || s.custom_name}`;
                    if(s.details && s.details.length > 0) {
                        notes += ` (${s.details.join(', ')})`;
                    }
                    notes += "\n";
                });
                
                // If there's existing manual text, we might want to preserve it? 
                // For now, let's append or replace. 
                // User asked for "detail jasa ini muncul", so let's overwrite for clarity or append if needed.
                // Simple approach: overwritten by the generated list + user can add more.
                // Better: Just formatting the list.
                
                // Let's just set it for now as the user requested "detail jasa muncul".
                this.technician_notes = notes;
            },

            get uniqueCategories() {
                if (!Array.isArray(this.masterServices)) return [];
                return [...new Set(this.masterServices.map(s => s.category))].filter(Boolean);
            },
            
            get filteredServices() {
                if (!this.serviceForm.category) return [];
                return this.masterServices.filter(s => s.category === this.serviceForm.category);
            },
            
            openServiceModal() {
                this.serviceForm = {
                    category: '',
                    service_id: '',
                    custom_name: '',
                    price: 0,
                    details: [],
                    newDetail: ''
                };
                this.showServiceModal = true;
            },
            
            selectService() {
                if (this.serviceForm.service_id === 'custom') {
                    this.serviceForm.custom_name = '';
                    this.serviceForm.price = 0;
                } else if (this.serviceForm.service_id) {
                    const svc = this.masterServices.find(s => s.id == this.serviceForm.service_id);
                    if (svc) {
                        this.serviceForm.custom_name = svc.name;
                        this.serviceForm.price = svc.price;
                    }
                }
            },
            
            addDetail() {
                if (this.serviceForm.newDetail.trim()) {
                    this.serviceForm.details.push(this.serviceForm.newDetail.trim());
                    this.serviceForm.newDetail = '';
                }
            },
            
            removeDetail(index) {
                this.serviceForm.details.splice(index, 1);
            },
            
            saveService() {
                if (!this.serviceForm.service_id) return;
                if (this.serviceForm.service_id === 'custom' && !this.serviceForm.custom_name) return;
                
                let serviceName = this.serviceForm.custom_name;
                
                this.selectedServices.push({
                    service_id: this.serviceForm.service_id,
                    name: serviceName,
                    custom_name: this.serviceForm.custom_name,
                    category: this.serviceForm.category,
                    price: parseInt(this.serviceForm.price),
                    details: [...this.serviceForm.details]
                });
                
                this.showServiceModal = false;
            },
            
            removeService(index) {
                this.selectedServices.splice(index, 1);
            },

            calculateTotal() {
                let subtotal = this.selectedServices.reduce((sum, svc) => sum + svc.price, 0);
                // Total = Service Subtotal + Shipping - Discount
                // Ensure non-negative
                let total = (subtotal - parseInt(this.discount || 0));
                return Math.max(0, total);
            }
        }
    }
</script>

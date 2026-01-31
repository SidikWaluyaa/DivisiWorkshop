<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            {{-- Left Side: Title & SPK --}}
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md shadow-inner border border-white/30 transform transition-transform hover:scale-105">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl leading-none text-white uppercase tracking-tighter">
                        Assessment & Detailing
                    </h2>
                    <div class="flex items-center gap-3 mt-2">
                        <div class="bg-white/10 backdrop-blur-sm px-3 py-1 rounded-lg border border-white/20 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-[#FFC232]" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg>
                            <span class="text-xs font-black text-white uppercase tracking-widest">{{ $order->spk_number }}</span>
                        </div>
                        <span class="w-1 h-1 bg-white/40 rounded-full"></span>
                        <div class="bg-black/10 px-2.5 py-1 rounded text-[10px] font-black text-[#FFC232] uppercase tracking-widest border border-[#FFC232]/20">
                            Status: Assessment
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto">
                <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-2.5 flex items-center gap-3 shadow-sm">
                    <div class="p-1.5 bg-[#FFC232]/20 rounded-md">
                        <svg class="w-4 h-4 text-[#FFC232]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-white/50 uppercase tracking-widest leading-none mb-0.5">Entry Date</span>
                        <span class="text-xs font-black text-white uppercase tracking-tight">{{ $order->created_at->format('d M Y') }} <span class="text-[#FFC232] ml-1">{{ $order->created_at->format('H:i') }}</span></span>
                    </div>
                </div>
                <div class="relative flex-1 lg:flex-initial min-w-[200px]">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" placeholder="Scan Barcode / Search..." class="w-full bg-black/10 border border-white/20 rounded-xl py-2.5 pl-10 pr-4 text-xs font-black text-white placeholder-white/40 focus:ring-2 focus:ring-[#FFC232] focus:border-[#FFC232] transition-all backdrop-blur-sm">
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-gray-50 to-gray-100" x-data="assessmentServiceForm()">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('assessment.store', $order->id) }}" method="POST" id="assessmentForm">
                @csrf
                <div class="mb-4 flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-200 shrink-0">
                        <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                        <span class="text-[9px] font-extrabold text-blue-700 uppercase tracking-tighter">1. Input CS</span>
                    </div>
                    <div class="w-4 h-px bg-gray-300"></div>
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-orange-50 border border-orange-200 shrink-0">
                        <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                        <span class="text-[9px] font-extrabold text-orange-700 uppercase tracking-tighter">2. Gudang</span>
                    </div>
                    <div class="w-4 h-px bg-gray-300"></div>
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#22AF85] shadow-sm shadow-[#22AF85]/20 shrink-0">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-[9px] font-extrabold text-white uppercase tracking-tighter">3. Assessment</span>
                    </div>
                    <div class="w-4 h-px bg-gray-300"></div>
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-100 border border-gray-200 opacity-60 shrink-0">
                        <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                        <span class="text-[9px] font-extrabold text-gray-400 uppercase tracking-tighter">4. Workshop</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 lg:col-span-7">
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl h-full flex flex-col border border-gray-100">
                            <div class="p-5 border-b bg-white flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-[#22AF85] rounded-xl flex items-center justify-center text-white font-bold shadow-md">1</div>
                                    <div>
                                        <h3 class="font-bold text-gray-800 text-lg uppercase tracking-tight">Dokumentasi Fisik</h3>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <svg class="w-3 h-3 text-[#22AF85]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            <span class="text-[10px] text-[#22AF85] font-black uppercase">Watermark Otomatis</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('assessment.print-spk', $order->id) }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#FFC232] text-gray-900 font-black rounded-xl shadow-lg hover:shadow-xl hover:bg-[#FFC232]/90 transition-all transform hover:-translate-y-0.5 text-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    <span>PRINT SPK</span>
                                </a>
                            </div>
                            <div class="p-6 flex-1 flex flex-col gap-6">
                                <div id="drop-zone" class="border-4 border-dashed border-gray-300 rounded-2xl bg-gradient-to-br from-gray-50 to-white hover:from-teal-50 hover:to-teal-100/30 hover:border-teal-400 transition-all duration-300 cursor-pointer group min-h-[220px] flex flex-col items-center justify-center relative" onclick="document.getElementById('file_input').click()">
                                    <input type="file" id="file_input" multiple accept="image/*" class="hidden">
                                    <div class="text-center p-8 pointer-events-none">
                                        <div class="mb-4 text-gray-400 group-hover:text-[#22AF85] transition-colors">
                                            <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <h4 class="text-xl font-bold text-gray-700 mb-2 group-hover:text-[#22AF85] transition-colors uppercase tracking-tight">Drag & Drop Foto Disini</h4>
                                        <p class="text-sm text-gray-500 mb-1">Atau klik untuk memilih foto</p>
                                    </div>
                                    <div id="upload-loading" class="absolute inset-0 bg-white/95 z-10 hidden flex-col items-center justify-center rounded-2xl backdrop-blur-sm">
                                        <svg class="animate-spin h-12 w-12 text-[#22AF85] mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span class="font-black text-[#22AF85] text-lg animate-pulse uppercase tracking-[0.2em]">Mengupload...</span>
                                    </div>
                                </div>
                                <div x-data="photoModal()">
                                    <h4 class="font-bold text-gray-700 mb-3 text-sm uppercase tracking-wider flex items-center gap-2">Gallery Foto</h4>
                                    <div id="gallery-grid" class="grid grid-cols-4 gap-3">
                                        @foreach($order->photos as $photo)
                                            <div @click="openModal('{{ Storage::url($photo->file_path) }}', {{ $photo->id }})" class="relative group aspect-square rounded-xl overflow-hidden shadow-md border-2 border-gray-100 hover:border-[#22AF85] transition-all duration-300 cursor-pointer">
                                                <img src="{{ Storage::url($photo->file_path) }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                                <button type="button" @click.stop="deletePhoto({{ $photo->id }}, $el)" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600 z-10 shadow-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div id="empty-gallery-msg" class="text-center text-sm text-gray-400 py-8 italic {{ $order->photos->count() > 0 ? 'hidden' : '' }}">Belum ada foto.</div>
                                    <div x-show="isOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click.away="closeModal()">
                                        <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl max-h-[90vh] w-full flex flex-col">
                                            <div class="flex items-center justify-between p-4 border-b bg-gray-50">
                                                <h3 class="font-bold text-gray-800">Preview Foto</h3>
                                                <button type="button" @click="closeModal()" class="p-2 hover:bg-gray-200 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                            </div>
                                            <div class="flex-1 overflow-auto p-6 flex items-center justify-center"><img :src="currentImage" class="max-w-full max-h-full object-contain"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-12 lg:col-span-5 flex flex-col gap-6">
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100">
                            <div class="p-5 border-b bg-white"><div class="flex items-center gap-3"><div class="w-10 h-10 bg-[#22AF85] rounded-xl flex items-center justify-center text-white font-bold shadow-md">2</div><h3 class="font-bold text-gray-800 text-lg uppercase tracking-tight">Data Barang & Pelanggan</h3></div></div>
                            <div class="p-6 space-y-5">
                                <div class="border-b pb-5">
                                    <h4 class="text-xs font-black text-[#22AF85] uppercase mb-3 tracking-widest">Biodata Pelanggan</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Nama</label>
                                            <input type="text" value="{{ $order->customer_name }}" class="w-full px-3 py-2 bg-gray-50 border rounded-lg text-sm" readonly>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">WhatsApp</label>
                                            <input type="text" value="{{ $order->customer_phone }}" class="w-full px-3 py-2 bg-gray-50 border rounded-lg text-sm" readonly>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Email</label>
                                            <input type="text" value="{{ $order->customer_email }}" class="w-full px-3 py-2 bg-gray-50 border rounded-lg text-sm" readonly>
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs font-black text-gray-600 mb-1.5 uppercase tracking-tight">Alamat Lengkap (Master Customer)</label>
                                            <div class="w-full px-3 py-2 border border-[#22AF85]/20 rounded-lg text-sm bg-gray-50 text-gray-700 font-medium leading-relaxed">
                                                <div class="font-bold mb-1">{{ $order->customer->address ?? '-' }}</div>
                                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-[11px] opacity-80 uppercase tracking-tight">
                                                    <div><span class="font-normal lowercase italic text-gray-400 mr-2">kel:</span> {{ $order->customer->village ?? '-' }}</div>
                                                    <div><span class="font-normal lowercase italic text-gray-400 mr-2">kec:</span> {{ $order->customer->district ?? '-' }}</div>
                                                    <div><span class="font-normal lowercase italic text-gray-400 mr-2">kota:</span> {{ $order->customer->city ?? '-' }}</div>
                                                    <div><span class="font-normal lowercase italic text-gray-400 mr-2">prov:</span> {{ $order->customer->province ?? '-' }}</div>
                                                    <div><span class="font-normal lowercase italic text-gray-400 mr-2">pos:</span> {{ $order->customer->postal_code ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-b pb-5">
                                    <h4 class="text-xs font-black text-[#22AF85] uppercase mb-3 tracking-widest">Data Barang</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Brand</label>
                                            <input type="text" value="{{ $order->shoe_brand }}" class="w-full px-3 py-2 bg-gray-50 border rounded-lg text-sm" readonly>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Tipe</label>
                                            <input type="text" value="{{ $order->shoe_type }}" class="w-full px-3 py-2 bg-gray-50 border rounded-lg text-sm" readonly>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Size</label>
                                            <input type="text" value="{{ $order->shoe_size }}" class="w-full px-3 py-2 bg-gray-50 border rounded-lg text-sm" readonly>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Warna</label>
                                            <input type="text" value="{{ $order->shoe_color }}" class="w-full px-3 py-2 bg-gray-50 border rounded-lg text-sm" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-b pb-5">
                                    <h4 class="text-xs font-black text-[#22AF85] uppercase mb-3 tracking-widest">Informasi Rak Penyimpanan</h4>
                                    
                                    @php
                                        // Specific retrieval to avoid ambiguity
                                        $activeAssignments = $order->storageAssignments->where('status', 'stored');
                                        $inboundRack = $activeAssignments->whereIn('category', ['before', 'Inbound'])->first();
                                        $shoeRack = $activeAssignments->where('category', 'shoes')->first();
                                        $accRack = $activeAssignments->whereIn('category', ['accessories', 'accessory'])->first();
                                    @endphp

                                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                                        {{-- Inbound Rack --}}
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 tracking-widest">Rak Inbound</label>
                                            @if($inboundRack)
                                            <div class="inline-flex items-center gap-2 px-3 py-2 bg-orange-50 border border-orange-200 rounded-lg text-orange-600 font-black text-xs uppercase tracking-widest shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                                {{ $inboundRack->rack_code }}
                                            </div>
                                            @else
                                            <div class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-400 font-black text-xs shadow-sm">T</div>
                                            @endif
                                        </div>

                                        {{-- Shoe Rack --}}
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 tracking-widest">Rak Sepatu</label>
                                            @if($shoeRack)
                                            <div class="inline-flex items-center gap-2 px-3 py-2 bg-[#FFC232]/10 border border-[#FFC232]/30 rounded-lg text-[#D4A017] font-black text-xs uppercase tracking-widest shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg>
                                                {{ $shoeRack->rack_code }}
                                            </div>
                                            @else
                                            <div class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-400 font-black text-xs shadow-sm">T</div>
                                            @endif
                                        </div>

                                        {{-- Accessory Rack --}}
                                        <div class="col-span-2 lg:col-span-1">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 tracking-widest">Rak Aksesoris</label>
                                            @if($accRack)
                                            <div class="inline-flex items-center gap-2 px-3 py-2 bg-[#22AF85]/10 border border-[#22AF85]/30 rounded-lg text-[#22AF85] font-black text-xs uppercase tracking-widest shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                {{ $accRack->rack_code }}
                                            </div>
                                            @else
                                            <div class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-400 font-black text-xs shadow-sm">T</div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Aksesoris Penyerta:</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach(['Tali' => $order->accessories_tali, 'Insole' => $order->accessories_insole, 'Box' => $order->accessories_box] as $label => $val)
                                        <div class="flex items-center justify-between px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-lg">
                                            <span class="text-[10px] font-bold text-gray-600 uppercase">{{ $label }}</span>
                                            <div class="flex gap-1">
                                                @php
                                                    $isNempel = in_array(strtoupper($val), ['N', 'NEMPEL']);
                                                    $isSimpan = in_array(strtoupper($val), ['S', 'SIMPAN']);
                                                    $isEmpty = !$val || in_array(strtoupper($val), ['T', 'TIDAK ADA', 'NONE', '-']);
                                                @endphp
                                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black {{ $isEmpty ? 'bg-red-100 text-red-600' : 'bg-gray-200 text-gray-400' }}">T</span>
                                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black {{ $isNempel ? 'bg-[#22AF85] text-white' : 'bg-gray-200 text-gray-400' }}">N</span>
                                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black {{ $isSimpan ? 'bg-[#FFC232] text-white' : 'bg-gray-200 text-gray-400' }}">S</span>
                                            </div>
                                        </div>
                                        @endforeach

                                        @if(!$order->accessories_tali && !$order->accessories_insole && !$order->accessories_box && !$order->accessories_other)
                                        <div class="col-span-2 text-[10px] text-gray-400 italic">Tidak ada aksesoris.</div>
                                        @endif
                                    </div>

                                    @if(!empty($order->accessories_other))
                                    <div class="mt-3 p-2 bg-gray-50 border border-gray-200 rounded-lg">
                                        <label class="block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Lainnya:</label>
                                        <div class="text-[11px] text-gray-700 font-bold leading-tight">{{ $order->accessories_other }}</div>
                                    </div>
                                    @endif
                                </div>
                                <div class="mb-4">
                                    <label class="block text-xs font-black text-gray-600 mb-1.5 flex items-center gap-2 uppercase tracking-tight">
                                        <svg class="w-4 h-4 text-[#22AF85]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                        Catatan / Keluhan CS
                                    </label>
                                    <div class="w-full px-3 py-3 border border-[#22AF85]/20 rounded-lg text-sm bg-gray-50 text-gray-700 italic font-medium leading-relaxed">
                                        "{{ $order->notes ?: 'Tidak ada catatan khusus.' }}"
                                    </div>
                                    <input type="hidden" name="notes" value="{{ $order->notes }}">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-600 mb-1.5 uppercase tracking-tight">Instruksi Khusus Teknisi</label>
                                    <textarea name="technician_notes" x-model="technician_notes" rows="4" class="w-full px-4 py-3 border-2 border-gray-100 rounded-lg text-sm focus:border-[#22AF85] focus:ring-4 focus:ring-[#22AF85]/10 font-bold bg-white" placeholder="Contoh: Hati-hati bagian heel counter rapuh...">{{ $order->technician_notes }}</textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Prioritas</label>
                                        <input type="text" value="{{ $order->priority }}" class="w-full px-3 py-2 bg-gray-50 border rounded-lg text-sm" readonly>
                                        <input type="hidden" name="priority" value="{{ $order->priority }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl flex-1 border border-gray-100">
                            <div class="p-5 border-b bg-white flex justify-between items-center">
                                <div class="flex items-center gap-3"><div class="w-10 h-10 bg-[#22AF85] rounded-xl flex items-center justify-center text-white font-bold shadow-md">3</div><h3 class="font-bold text-gray-800 text-lg uppercase tracking-tight">Layanan</h3></div>
                                <button type="button" @click="openServiceModal()" class="px-5 py-2.5 bg-[#22AF85] text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-lg">Tambah</button>
                            </div>
                            <div class="p-6">
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50"><tr><th class="px-4 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Layanan</th><th class="px-4 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Harga</th><th class="px-4 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th></tr></thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <template x-if="selectedServices.length === 0"><tr><td colspan="3" class="px-4 py-8 text-center text-gray-400 italic text-sm">Belum ada layanan.</td></tr></template>
                                            <template x-for="(svc, index) in selectedServices" :key="index">
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-4 py-3">
                                                        <div class="font-bold text-gray-800" x-text="svc.name"></div>
                                                        <div class="flex flex-wrap gap-1 mt-1">
                                                            <template x-for="detail in svc.details">
                                                                <span class="bg-[#22AF85]/10 text-[#22AF85] text-[9px] px-2 py-0.5 rounded-full border border-[#22AF85]/20 font-bold" x-text="detail"></span>
                                                            </template>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 text-right font-mono text-sm font-bold text-gray-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(svc.price)"></td>
                                                    <td class="px-4 py-3 text-center">
                                                        <button type="button" @click="removeService(index)" class="text-red-500 hover:text-red-700">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                        <input type="hidden" :name="`services[${index}][service_id]`" :value="svc.service_id">
                                                        <input type="hidden" :name="`services[${index}][custom_name]`" :value="svc.custom_name">
                                                        <input type="hidden" :name="`services[${index}][category]`" :value="svc.category">
                                                        <input type="hidden" :name="`services[${index}][price]`" :value="svc.price">
                                                        <template x-for="(detail, dIdx) in svc.details" :key="dIdx">
                                                            <input type="hidden" :name="`services[${index}][details][]`" :value="detail">
                                                        </template>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="p-6 bg-[#22AF85] text-white">
                                <div class="mb-5 pb-5 border-b border-white/20"><label class="text-[10px] font-black uppercase text-white/90">Diskon (Rp)</label><input type="number" name="discount" x-model="discount" class="w-full text-right bg-white text-gray-900 rounded-xl px-4 py-3 text-lg font-black focus:ring-4 focus:ring-[#FFC232]/50" placeholder="0"></div>
                                <div class="flex justify-between items-center mb-6"><span class="text-xs font-black uppercase text-white/80">Total Netto</span><span class="text-3xl font-black text-white" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(calculateTotal())">Rp 0</span></div>
                                <button type="submit" class="w-full bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black py-4 px-6 rounded-2xl shadow-xl transition-all flex items-center justify-center gap-3 text-sm uppercase tracking-[0.15em]"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>Simpan & Cetak</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Move Modal Here (Inside x-data scope) --}}
            <div x-show="showServiceModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showServiceModal = false"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                        <div class="bg-white px-6 pt-6 pb-6 sm:p-8">
                            <h3 class="text-xl font-black text-gray-900 mb-6 uppercase tracking-tight flex items-center gap-3"><div class="w-2 h-6 bg-[#FFC232] rounded-full"></div>Tambah Layanan</h3>
                            <div class="space-y-4">
                                <div><label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 tracking-widest">Kategori</label><select x-model="serviceForm.category" class="w-full rounded-xl border-gray-100 shadow-sm focus:border-[#22AF85] font-bold text-sm h-12"><option value="">-- Pilih --</option><option value="Custom">Custom</option><template x-for="cat in uniqueCategories" :key="cat"><option :value="cat" x-text="cat"></option></template></select></div>
                                <div><label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 tracking-widest">Layanan</label><select x-model="serviceForm.service_id" @change="selectService()" class="w-full rounded-xl border-gray-100 shadow-sm focus:border-[#22AF85] font-bold text-sm h-12" :disabled="!serviceForm.category"><option value="">-- Pilih --</option><template x-for="svc in filteredServices" :key="svc.id"><option :value="svc.id" x-text="svc.name + ' (' + new Intl.NumberFormat('id-ID').format(svc.price) + ')'"></option></template><option value="custom">+ Manual</option></select></div>
                                <div x-show="serviceForm.service_id === 'custom'"><label class="block text-sm font-medium text-gray-700 mb-1">Nama Layanan</label><input type="text" x-model="serviceForm.custom_name" class="w-full rounded-md border-gray-300"></div>
                                <div><label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 tracking-widest">Harga (Rp)</label><input type="number" x-model="serviceForm.price" class="w-full rounded-xl border-gray-100 shadow-sm focus:border-[#22AF85] font-bold text-sm h-12"></div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 tracking-widest">Detail Tambahan</label>
                                    <div class="flex gap-2 mb-3">
                                        <input type="text" x-model="serviceForm.newDetail" @keydown.enter.prevent="addDetail()" class="flex-1 rounded-xl border-gray-100 shadow-sm focus:border-[#22AF85] font-bold text-sm h-12" placeholder="Contoh: Jahit Sol">
                                        <button type="button" @click="addDetail()" class="px-4 h-12 bg-gray-100 rounded-xl hover:bg-gray-200 text-gray-600 font-black text-xl flex items-center justify-center">+</button>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="(detail, idx) in serviceForm.details" :key="idx">
                                            <span class="bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg text-[10px] font-black flex items-center gap-2 border border-gray-200 uppercase tracking-wider">
                                                <span x-text="detail"></span>
                                                <button type="button" @click="removeDetail(idx)" class="text-red-400 hover:text-red-600 font-black ml-1">&times;</button>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3"><button type="button" @click="saveService()" class="w-full inline-flex justify-center rounded-xl px-6 py-3 bg-[#22AF85] text-xs font-black text-white uppercase tracking-widest shadow-lg">Simpan</button><button type="button" @click="showServiceModal = false" class="mt-3 sm:mt-0 w-full inline-flex justify-center rounded-xl px-6 py-3 bg-white text-xs font-black text-gray-400 border border-gray-200">Batal</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function photoModal() {
            return {
                isOpen: false, currentImage: '', currentPhotoId: null,
                openModal(imageUrl, photoId) { this.currentImage = imageUrl; this.currentPhotoId = photoId; this.isOpen = true; document.body.style.overflow = 'hidden'; },
                closeModal() { this.isOpen = false; document.body.style.overflow = ''; },
                deletePhoto(id, element) {
                    if(!confirm('Hapus foto?')) return;
                    fetch(`/photos/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
                    .then(response => response.json()).then(data => { if (data.success) { element.closest('div.group')?.remove() || element.closest('div.relative')?.remove(); closeModal(); } });
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('file_input');
            const galleryGrid = document.getElementById('gallery-grid');
            const loadingOverlay = document.getElementById('upload-loading');
            if(!dropZone) return;

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(e => { dropZone.addEventListener(e, (ev) => { ev.preventDefault(); ev.stopPropagation(); }); });
            dropZone.addEventListener('dragenter', () => dropZone.classList.add('border-teal-500'));
            dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-teal-500'));
            dropZone.addEventListener('drop', (e) => handleFiles(e.dataTransfer.files));
            fileInput.addEventListener('change', (e) => handleFiles(e.target.files));

            function handleFiles(files) {
                if (!files.length) return;
                loadingOverlay.classList.remove('hidden'); loadingOverlay.classList.add('flex');
                const uploadPromises = Array.from(files).map((file, index) => {
                    const formData = new FormData(); 
                    formData.append('photos[]', file); // Use photos[] as expected by controller
                    formData.append('step', 'bulk_' + Date.now()); 
                    formData.append('is_public', 1);
                    return fetch('{{ route("work-order-photos.store", $order->id) }}', { 
                        method: 'POST', 
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, 
                        body: formData 
                    }).then(r => r.json());
                });
                Promise.all(uploadPromises).then(() => { 
                    loadingOverlay.classList.replace('flex', 'hidden');
                    location.reload();
                }).catch(err => {
                    console.error('Upload error:', err);
                    loadingOverlay.classList.replace('flex', 'hidden');
                    alert('Gagal mengupload foto. Silakan coba lagi.');
                });
            }
        });
    </script>
    <script>
        function assessmentServiceForm() {
            return {
                masterServices: @json($services),
                selectedServices: [
                    @foreach($order->workOrderServices as $wos)
                    {
                        service_id: '{{ $wos->service_id ?? "custom" }}',
                        name: @json($wos->service_id ? $wos->service->name : $wos->custom_service_name),
                        custom_name: @json($wos->custom_service_name),
                        category: @json($wos->category_name),
                        price: {{ $wos->cost }},
                        details: @json($wos->service_details ?? [])
                    },
                    @endforeach
                ],
                showServiceModal: false,
                serviceForm: { category: '', service_id: '', custom_name: '', price: 0, details: [], newDetail: '' },
                technician_notes: '{{ $order->technician_notes }}',
                discount: {{ $order->discount ?? 0 }},
                init() { 
                    this.$watch('selectedServices', () => this.updateTechnicianNotes()); 
                },
                updateTechnicianNotes() {
                    let notes = "Layanan: \n";
                    this.selectedServices.forEach(s => { 
                        notes += `- ${s.name || s.custom_name}`;
                        if (s.details && s.details.length > 0) {
                            notes += ` (${s.details.join(', ')})`;
                        }
                        notes += `\n`;
                    });
                    this.technician_notes = notes;
                },
                get uniqueCategories() { return [...new Set(this.masterServices.map(s => s.category))].filter(Boolean); },
                get filteredServices() { return this.masterServices.filter(s => s.category === this.serviceForm.category); },
                openServiceModal() { this.serviceForm = { category: '', service_id: '', custom_name: '', price: 0, details: [], newDetail: '' }; this.showServiceModal = true; },
                selectService() {
                    const svc = this.masterServices.find(s => s.id == this.serviceForm.service_id);
                    if (svc) { this.serviceForm.custom_name = svc.name; this.serviceForm.price = svc.price; }
                },
                addDetail() {
                    if (!this.serviceForm.newDetail.trim()) return;
                    this.serviceForm.details.push(this.serviceForm.newDetail.trim());
                    this.serviceForm.newDetail = '';
                },
                removeDetail(index) {
                    this.serviceForm.details.splice(index, 1);
                },
                saveService() {
                    if (!this.serviceForm.service_id) return;
                    this.selectedServices.push({ 
                        service_id: this.serviceForm.service_id, 
                        name: this.serviceForm.custom_name, 
                        custom_name: this.serviceForm.custom_name, 
                        category: this.serviceForm.category, 
                        price: parseInt(this.serviceForm.price), 
                        details: [...this.serviceForm.details] 
                    });
                    this.showServiceModal = false;
                },
                removeService(index) { this.selectedServices.splice(index, 1); },
                calculateTotal() {
                    let subtotal = this.selectedServices.reduce((sum, svc) => sum + svc.price, 0);
                    return Math.max(0, subtotal - parseInt(this.discount || 0));
                }
            }
        }
    </script>
    @endpush
</x-app-layout>

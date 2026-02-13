<x-app-layout>
    <div class="min-h-screen bg-[#F3F4F6] pb-20 font-sans">
        
        {{-- Hero Header (Light Theme) --}}
        <div class="relative bg-white border-b border-gray-200 pb-24 overflow-hidden">
            {{-- Background Pattern --}}
            <div class="absolute inset-0">
                <div class="absolute inset-0 bg-gray-50/50"></div>
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-[#22B086] blur-3xl opacity-5 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-[#FFC232] blur-3xl opacity-5"></div>
            </div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-6">
                {{-- Breadcrumb / Back --}}
                <div class="flex items-center gap-4 mb-8 relative z-50">
                    @if($order->customer_id)
                        <a href="{{ route('admin.customers.show', $order->customer_id) }}" class="group flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-medium shadow-sm transition-all">
                            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali ke Customer
                        </a>
                    @else
                        <a href="{{ route('admin.customers.index') }}" class="group flex items-center gap-2 px-4 py-2 rounded-full bg-[#FFC232] hover:bg-[#FFB000] text-gray-900 text-sm font-bold shadow-lg shadow-orange-200 transition-all">
                            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali ke List Customer
                        </a>
                    @endif
                    <span class="text-gray-500">/</span>
                    <span class="text-gray-400 text-sm">Detail Work Order</span>
                </div>

                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-[#22B086]/10 text-[#22B086] border border-[#22B086]/20">
                                Work Order
                            </span>
                            <span class="text-gray-500 text-xs font-medium flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $order->created_at->format('d F Y, H:i') }}
                            </span>
                        </div>
                        <h1 class="text-5xl font-black text-gray-900 tracking-tight leading-tight">
                            {{ $order->spk_number }}
                        </h1>
                        <div class="mt-4 flex items-center gap-4">
                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-100 border border-gray-200">
                                <span class="w-2 h-2 rounded-full bg-[#FFC232] animate-pulse"></span>
                                <span class="text-gray-700 text-sm font-bold">{{ str_replace('_', ' ', $order->status->value) }}</span>
                            </div>
                            <div class="h-4 w-px bg-gray-300"></div>
                            <div class="text-gray-500 text-sm">
                                Estimasi: <span class="text-gray-900 font-bold">{{ $order->estimation_date ? \Carbon\Carbon::parse($order->estimation_date)->format('d M Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="flex gap-3">
                        <a href="{{ route('admin.orders.shipping-label', $order->id) }}" target="_blank" class="flex items-center gap-2 px-6 py-3 bg-[#FFC232] text-gray-900 rounded-xl font-bold text-sm shadow-xl shadow-orange-200 hover:bg-[#FFB000] transition-all hover:-translate-y-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6"></path></svg>
                            Print Label
                        </a>
                        <a href="{{ route('assessment.print-spk', $order->id) }}" target="_blank" class="flex items-center gap-2 px-6 py-3 bg-[#22B086] text-white rounded-xl font-bold text-sm shadow-xl shadow-emerald-200 hover:bg-[#1C8D6C] transition-all hover:-translate-y-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print SPK
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Grid --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-10">
            
            {{-- Status Steps (Visual) --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8 overflow-x-auto">
                <div class="flex items-center justify-between min-w-[600px]">
                    @foreach([\App\Enums\WorkOrderStatus::DITERIMA, \App\Enums\WorkOrderStatus::ASSESSMENT, \App\Enums\WorkOrderStatus::PREPARATION, \App\Enums\WorkOrderStatus::PRODUCTION, \App\Enums\WorkOrderStatus::QC, \App\Enums\WorkOrderStatus::SELESAI] as $index => $step)
                        @php
                            $isCompleted = false; // Need logic or just visual for now based on current status order
                            // Simplified for visual: current status matches or is 'after' in enum list
                            // Ideally use workflow logs but standard enum order is decent proxy if linear
                            $isActive = $order->status == $step;
                        @endphp
                        <div class="flex flex-col items-center relative flex-1 group">
                            {{-- Line --}}
                            @if(!$loop->last)
                                <div class="absolute top-4 left-1/2 w-full h-1 bg-gray-100 -z-10"></div>
                            @endif
                            
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 mb-2 z-10 
                                {{ $isActive ? 'bg-[#22B086] border-[#22B086] text-white shadow-lg shadow-emerald-500/30' : 'bg-white border-gray-200 text-gray-400' }}">
                                {{ $loop->iteration }}
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $isActive ? 'text-[#22B086]' : 'text-gray-400' }}">
                                {{ str_replace('_', ' ', $step->value) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- LEFT COLUMN: Customer & Address --}}
                <div class="space-y-8">
                    {{-- Customer Card --}}
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden group hover:shadow-xl transition-all duration-300">
                        <div class="bg-gray-50/50 p-6 border-b border-gray-100">
                            <h3 class="font-black text-gray-800 text-base uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#22B086]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Customer
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-[#22B086] to-[#1C8D6C] rounded-full flex items-center justify-center text-3xl font-black text-white shadow-lg mb-4">
                                    {{ substr($order->customer_name, 0, 1) }}
                                </div>
                                <h4 class="text-xl font-bold text-gray-900">{{ $order->customer_name }}</h4>
                                <p class="text-sm text-gray-500 font-medium">{{ $order->customer_phone }}</p>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                    <span class="text-xs font-bold text-gray-400 uppercase">Email</span>
                                    <span class="text-sm font-bold text-gray-700">{{ $order->customer_email ?? '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                    <span class="text-xs font-bold text-gray-400 uppercase">Member Sejak</span>
                                    <span class="text-sm font-bold text-gray-700">{{ $order->customer ? $order->customer->created_at->format('M Y') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Address Card --}}
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden group hover:shadow-xl transition-all duration-300">
                        <div class="bg-gray-50/50 p-6 border-b border-gray-100">
                            <h3 class="font-black text-gray-800 text-base uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Alamat Pengiriman
                            </h3>
                        </div>
                        <div class="p-6 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-[#FFC232]/5 rounded-full blur-3xl -z-0"></div>
                            
                            <p class="text-gray-800 font-bold leading-relaxed relative z-10 mb-4">
                                "{{ $order->customer_address ?? $order->customer?->address ?? 'Alamat tidak tersedia' }}"
                            </p>
                            
                            <div class="grid grid-cols-2 gap-3 relative z-10">
                                <div class="p-3 bg-orange-50/50 border border-orange-100 rounded-xl">
                                    <span class="block text-[10px] font-black text-[#FFC232] uppercase">Kota</span>
                                    <span class="font-bold text-gray-700 text-sm">{{ $order->customer?->city ?? '-' }}</span>
                                </div>
                                <div class="p-3 bg-orange-50/50 border border-orange-100 rounded-xl">
                                    <span class="block text-[10px] font-black text-[#FFC232] uppercase">Kecamatan</span>
                                    <span class="font-bold text-gray-700 text-sm">{{ $order->customer?->district ?? '-' }}</span>
                                </div>
                                <div class="p-3 bg-orange-50/50 border border-orange-100 rounded-xl">
                                    <span class="block text-[10px] font-black text-[#FFC232] uppercase">Provinsi</span>
                                    <span class="font-bold text-gray-700 text-sm">{{ $order->customer?->province ?? '-' }}</span>
                                </div>
                                <div class="p-3 bg-orange-50/50 border border-orange-100 rounded-xl">
                                    <span class="block text-[10px] font-black text-[#FFC232] uppercase">Kode Pos</span>
                                    <span class="font-bold text-gray-700 text-sm">{{ $order->customer?->postal_code ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Items & Services (Span 2) --}}
                <div class="lg:col-span-2 space-y-8">
                    
                    {{-- Item Details --}}
                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 p-8 relative overflow-hidden">
                        <div class="absolute right-0 top-0 w-64 h-64 bg-[#22B086]/5 rounded-full blur-3xl pointer-events-none"></div>
                        
                        <div class="flex items-center gap-4 mb-8 relative z-10">
                             <span class="w-12 h-12 rounded-2xl bg-[#22B086] text-white flex items-center justify-center shadow-lg shadow-emerald-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </span>
                            <div>
                                <h3 class="text-2xl font-black text-gray-900">Detail Sepatu</h3>
                                <p class="text-[#22B086] font-medium text-sm">Informasi lengkap spesifikasi barang</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 relative z-10 mb-8">
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Brand</p>
                                <p class="text-xl font-black text-gray-800">{{ $order->shoe_brand }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Size</p>
                                <p class="text-xl font-black text-gray-800">{{ $order->shoe_size }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Color</p>
                                <p class="text-xl font-black text-gray-800">{{ $order->shoe_color }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Category</p>
                                <p class="text-xl font-black text-gray-800">{{ $order->category ?? 'General' }}</p>
                            </div>
                        </div>

                        {{-- Accessories Checklist (Assessment Style) --}}
                        <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-100">
                            <div class="flex items-center gap-2 mb-6">
                                <span class="w-1.5 h-6 bg-[#22B086] rounded-full"></span>
                                <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest">Aksesoris Penyerta</h4>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach(['Tali' => $order->accessories_tali, 'Insole' => $order->accessories_insole, 'Box' => $order->accessories_box] as $label => $val)
                                    @php
                                        $isNempel = in_array(strtoupper($val), ['N', 'NEMPEL']);
                                        $isSimpan = in_array(strtoupper($val), ['S', 'SIMPAN']);
                                        $isEmpty = !$val || in_array(strtoupper($val), ['T', 'TIDAK ADA', 'NONE', '-']);
                                    @endphp
                                    <div class="flex items-center justify-between px-4 py-3 bg-white border border-gray-100 rounded-xl shadow-sm">
                                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-tight">{{ $label }}</span>
                                        <div class="flex gap-1.5">
                                            <span class="w-7 h-7 flex items-center justify-center rounded-lg text-[9px] font-black transition-all {{ $isEmpty ? 'bg-red-500 text-white shadow-lg shadow-red-100' : 'bg-gray-100 text-gray-300' }}">T</span>
                                            <span class="w-7 h-7 flex items-center justify-center rounded-lg text-[9px] font-black transition-all {{ $isNempel ? 'bg-[#22B086] text-white shadow-lg shadow-emerald-100' : 'bg-gray-100 text-gray-300' }}">N</span>
                                            <span class="w-7 h-7 flex items-center justify-center rounded-lg text-[9px] font-black transition-all {{ $isSimpan ? 'bg-[#FFC232] text-white shadow-lg shadow-yellow-100' : 'bg-gray-100 text-gray-300' }}">S</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($order->accessories_other && $order->accessories_other != 'Tidak Ada')
                                <div class="mt-4 p-4 bg-white border border-gray-100 rounded-xl">
                                    <p class="text-[9px] font-black text-[#22B086] uppercase tracking-widest mb-1">Aksesoris Lainnya:</p>
                                    <p class="text-sm font-bold text-gray-700 leading-relaxed">{{ $order->accessories_other }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Database Rack Information (Assessment Style) --}}
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden group hover:shadow-xl transition-all duration-300">
                        <div class="bg-gray-50/50 p-8 border-b border-gray-100">
                            <h3 class="text-2xl font-black text-gray-900 leading-none">Informasi Rak Penyimpanan</h3>
                            <p class="text-[#22B086] font-bold text-[10px] uppercase tracking-[0.2em] mt-2">Data Alokasi Slot Gudang Terpusat</p>
                        </div>

                        <div class="p-8">
                            @php
                                $activeAssignments = $order->storageAssignments->where('status', 'stored');
                                $inboundRack = $activeAssignments->filter(fn($a) => in_array(strtolower($a->category), ['before', 'inbound']))->first();
                                $shoeRack = $activeAssignments->filter(fn($a) => in_array(strtolower($a->category), ['shoes', 'finish', 'sepatu']))->first();
                                $accRack = $activeAssignments->filter(fn($a) => in_array(strtolower($a->category), ['accessories', 'accessory', 'aksesoris']))->first();
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {{-- Inbound Rack --}}
                                <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100 flex flex-col items-center justify-center text-center">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Rak Inbound</span>
                                    @if($inboundRack)
                                        <div class="w-20 h-20 bg-white rounded-2xl flex flex-col items-center justify-center shadow-lg border-b-4 border-orange-400 rotate-1 mb-2">
                                            <span class="text-3xl font-black text-gray-900 leading-none">{{ $inboundRack->rack_code }}</span>
                                        </div>
                                        <p class="text-[9px] font-black text-orange-500 uppercase">Sector: {{ $inboundRack->category }}</p>
                                    @else
                                        <div class="w-16 h-16 bg-white/50 rounded-2xl flex items-center justify-center border-2 border-dashed border-gray-200 text-gray-300 text-xl font-black mb-2">T</div>
                                        <p class="text-[9px] font-black text-gray-300 uppercase italic">Not Stored</p>
                                    @endif
                                </div>

                                {{-- Shoe Rack --}}
                                <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100 flex flex-col items-center justify-center text-center">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Rak Sepatu</span>
                                    @if($shoeRack)
                                        <div class="w-20 h-20 bg-white rounded-2xl flex flex-col items-center justify-center shadow-lg border-b-4 border-[#FFC232] -rotate-1 mb-2">
                                            <span class="text-3xl font-black text-gray-900 leading-none">{{ $shoeRack->rack_code }}</span>
                                        </div>
                                        <p class="text-[9px] font-black text-[#D4A017] uppercase">Sector: {{ $shoeRack->category }}</p>
                                    @else
                                        <div class="w-16 h-16 bg-white/50 rounded-2xl flex items-center justify-center border-2 border-dashed border-gray-200 text-gray-300 text-xl font-black mb-2">T</div>
                                        <p class="text-[9px] font-black text-gray-300 uppercase italic">Not Stored</p>
                                    @endif
                                </div>

                                {{-- Accessory Rack --}}
                                <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100 flex flex-col items-center justify-center text-center">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Rak Aksesoris</span>
                                    @if($accRack)
                                        <div class="w-20 h-20 bg-white rounded-2xl flex flex-col items-center justify-center shadow-lg border-b-4 border-[#22AF85] rotate-2 mb-2">
                                            <span class="text-3xl font-black text-gray-900 leading-none">{{ $accRack->rack_code }}</span>
                                        </div>
                                        <p class="text-[9px] font-black text-[#22AF85] uppercase">Sector: {{ $accRack->category }}</p>
                                    @else
                                        <div class="w-16 h-16 bg-white/50 rounded-2xl flex items-center justify-center border-2 border-dashed border-gray-200 text-gray-300 text-xl font-black mb-2">T</div>
                                        <p class="text-[9px] font-black text-gray-300 uppercase italic">Not Stored</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Services Table --}}
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
                            <div>
                                <h3 class="font-black text-gray-900 text-lg">Layanan & Harga</h3>
                                <p class="text-gray-500 text-xs mt-0.5">Rincian service yang dipilih</p>
                            </div>
                            <div class="px-4 py-2 bg-[#FFC232] text-gray-900 rounded-lg font-mono font-bold shadow-lg shadow-orange-200">
                                TOTAL: Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}
                            </div>
                        </div>
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-8 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Service Name</th>
                                    <th class="px-8 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-wider">Biaya</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($order->workOrderServices as $detail)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-1 h-8 bg-gray-200 rounded-full group-hover:bg-[#22B086] transition-colors"></div>
                                                <span class="font-bold text-gray-800 text-sm">
                                                    {{ $detail->custom_service_name ?? ($detail->service ? $detail->service->name : '-') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-right font-mono font-bold text-gray-700">
                                            Rp {{ number_format($detail->cost, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-8 py-8 text-center text-gray-500 italic bg-gray-50/50">
                                            Tidak ada layanan yang dipilih
                                        </td>
                                    </tr>
                                @endforelse
                                {{-- Extra Costs (Ongkir etc) --}}
                                @if($order->shipping_cost > 0)
                                    <tr class="bg-gray-50/30">
                                        <td class="px-8 py-4 text-xs font-bold text-gray-500 uppercase pl-12">Ongkos Kirim</td>
                                        <td class="px-8 py-4 text-right font-mono font-bold text-gray-700">
                                            Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 5. Gallery Foto --}}
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 lg:col-span-2" 
                     x-data="{ 
                        showLightbox: false, 
                        activeId: null,
                        activeImage: '', 
                        activeCaption: '', 
                        activeStep: '',
                        activeUploader: '',
                        activeSize: '',
                        isCover: false,
                        isRef: false,
                        openLightbox(id, url, caption, step, uploader, size, isCover, isRef) {
                            this.activeId = id;
                            this.activeImage = url;
                            this.activeCaption = caption;
                            this.activeStep = step;
                            this.activeUploader = uploader;
                            this.activeSize = size;
                            this.isCover = isCover;
                            this.isRef = isRef;
                            this.showLightbox = true;
                        },
                        async setAsCover() {
                            try {
                                const res = await fetch(`/photos/${this.activeId}/set-cover`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                });
                                const data = await res.json();
                                if(data.success) {
                                    this.isCover = true;
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: data.message,
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                }
                            } catch(e) { console.error(e); }
                        },
                        async setAsReference() {
                            try {
                                const res = await fetch(`/photos/${this.activeId}/set-reference`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                });
                                const data = await res.json();
                                if(data.success) {
                                    this.isRef = true;
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: data.message,
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                }
                            } catch(e) { console.error(e); }
                        },
                        downloadImage() {
                            const link = document.createElement('a');
                            link.href = this.activeImage;
                            link.download = 'photo_' + Date.now();
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }
                     }">
                    
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-black text-gray-900 text-lg flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-[#22B086]/10 text-[#22B086] flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </span>
                            Galeri Foto Lengkap
                        </h3>
                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-1 rounded-full uppercase tracking-wider">
                            {{ $order->photos->count() }} Foto
                        </span>
                    </div>
                    
                    <div class="p-6 bg-gray-50 min-h-[400px]">
                        @if($order->photos->count() > 0)
                            @php
                                $groupedPhotos = $order->photos->groupBy('step');
                                $stepLabels = [
                                    'RECEPTION' => '📩 Foto Referensi CS',
                                    'WAREHOUSE_BEFORE' => '📸 Foto Penerimaan (Gudang)',
                                    'ASSESSMENT' => '📋 Foto Assessment (Teknisi)',
                                    'WASHING' => 'washing', 
                                    'PREPARATION' => '🛠 Preparation',
                                    'PRODUCTION' => '🏭 Production / Cuci',
                                    'QC' => '✨ Quality Control',
                                    'FINISH' => '✅ Finishing & Packing',
                                    'CX_FOLLOWUP' => '📞 Foto Follow-up CX',
                                ];
                            @endphp

                            @foreach($groupedPhotos as $step => $photos)
                                <div class="mb-8 last:mb-0">
                                    <h4 class="text-[#22B086] font-bold uppercase tracking-widest text-xs mb-4 flex items-center gap-2 border-b border-gray-200 pb-2">
                                        <span class="w-2 h-2 rounded-full bg-[#22B086]"></span>
                                        {{ $stepLabels[$step] ?? $step }}
                                    </h4>
                                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                        @foreach($photos as $photo)
                                            @php
                                                $size = 0;
                                                try {
                                                    if(\Illuminate\Support\Facades\Storage::disk('public')->exists($photo->file_path)) {
                                                        $size = \Illuminate\Support\Facades\Storage::disk('public')->size($photo->file_path);
                                                    }
                                                } catch(\Exception $e) {}
                                                $formattedSize = $size > 1048576 ? round($size/1048576, 1).' MB' : round($size/1024, 0).' KB';
                                                $uploaderName = $photo->uploader ? $photo->uploader->name : 'Admin';
                                            @endphp
                                            <div class="group relative aspect-square bg-white rounded-xl overflow-hidden border {{ $photo->is_spk_cover ? 'border-[#FFC232] ring-2 ring-[#FFC232]/50' : ($photo->is_primary_reference ? 'border-purple-500 ring-2 ring-purple-500/30' : 'border-gray-200') }} shadow-lg cursor-pointer transition-transform duration-300 hover:scale-105 hover:border-[#22B086]/50 hover:shadow-emerald-500/20"
                                                 @click="openLightbox('{{ $photo->id }}', '{{ asset('storage/' . $photo->file_path) }}', '{{ $photo->caption ?? 'Tanpa Caption' }}', '{{ $stepLabels[$step] ?? $step }}', '{{ $uploaderName }}', '{{ $formattedSize }}', {{ $photo->is_spk_cover ? 'true' : 'false' }}, {{ $photo->is_primary_reference ? 'true' : 'false' }})">
                                                <img src="{{ asset('storage/' . $photo->file_path) }}" 
                                                     class="w-full h-full object-cover">
                                                
                                                {{-- Overlay Info --}}
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-3">
                                                    <div class="absolute top-2 right-2 flex flex-col gap-1 items-end">
                                                        @if($photo->is_spk_cover)
                                                           <div class="px-1.5 py-0.5 bg-amber-500 text-white text-[8px] font-black rounded uppercase shadow-lg">Cover</div>
                                                        @endif
                                                        @if($photo->is_primary_reference)
                                                           <div class="px-1.5 py-0.5 bg-purple-600 text-white text-[8px] font-black rounded uppercase shadow-lg">Referansi</div>
                                                        @endif
                                                    </div>
                                                    <p class="text-white text-xs font-bold line-clamp-2">{{ $photo->caption ?? 'Tanpa Caption' }}</p>
                                                    <div class="flex justify-between items-center mt-1 text-[10px]">
                                                        <span class="text-gray-400">{{ $uploaderName }}</span>
                                                        <span class="text-gray-500">{{ $formattedSize }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="flex flex-col items-center justify-center py-20 text-gray-600">
                                <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-lg font-medium">Belum ada foto yang diupload</p>
                            </div>
                        @endif
                    </div>

                    {{-- Lightbox Modal --}}
                    <div x-show="showLightbox" 
                         style="display: none;"
                         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/90 backdrop-blur-sm p-4"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-90"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-90">
                        
                        {{-- Close Button --}}
                        <button @click="showLightbox = false" class="absolute top-4 right-4 p-2 bg-white/10 hover:bg-white/20 rounded-full text-white transition-colors z-50">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        <div class="max-w-7xl w-full max-h-screen flex flex-col md:flex-row gap-6 bg-gray-50 rounded-2xl overflow-hidden shadow-2xl border border-gray-200" @click.outside="showLightbox = false">
                            {{-- Image Area --}}
                            <div class="flex-1 bg-gray-200/50 flex items-center justify-center relative min-h-[400px]">
                                <img :src="activeImage" class="max-w-full max-h-[85vh] object-contain">
                            </div>

                            {{-- Sidebar Info --}}
                            <div class="w-full md:w-80 bg-white p-6 flex flex-col border-l border-gray-100">
                                <h3 class="text-[#22B086] font-bold uppercase tracking-widest text-xs mb-2" x-text="activeStep"></h3>
                                <p class="text-gray-900 font-bold text-lg mb-4 leading-relaxed" x-text="activeCaption || 'Tanpa Caption'"></p>

                                <div class="space-y-4 mb-8">
                                    <div>
                                        <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Diupload Oleh</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-600 uppercase font-bold" x-text="activeUploader.charAt(0)"></div>
                                            <p class="text-gray-600 text-sm" x-text="activeUploader"></p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Ukuran File</p>
                                        <p class="text-gray-600 text-sm mt-1" x-text="activeSize"></p>
                                    </div>
                                </div>

                                <div class="mt-auto space-y-3">
                                    <button @click="setAsReference()" 
                                            x-show="activeId"
                                            :disabled="isRef"
                                            :class="isRef ? 'bg-purple-600 text-white cursor-default' : 'bg-gray-100 hover:bg-gray-200 text-purple-600 border border-purple-500/50'"
                                            class="w-full py-3 px-4 font-bold rounded-xl flex items-center justify-center gap-2 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h2.2c.462 0 .694 0 .898-.053.204-.053.385-.143.748-.325l.443-.221c.643-.322.964-.482 1.275-.482.311 0 .632.16 1.275.482l.443.221c.363.182.544.272.748.325.204.053.436.053.898.053H18c1.105 0 2-.895 2-2V7c0-1.105-.895-2-2-2H8c-1.105 0-2 .895-2 2v13z"></path></svg>
                                        <span x-text="isRef ? 'Referensi Utama Aktif' : 'Atur Sebagai Referensi'"></span>
                                    </button>

                                    <button @click="setAsCover()" 
                                            x-show="activeId"
                                            :disabled="isCover"
                                            :class="isCover ? 'bg-[#FFC232] text-white cursor-default' : 'bg-gray-100 hover:bg-gray-200 text-[#FFC232] border border-[#FFC232]/50'"
                                            class="w-full py-3 px-4 font-bold rounded-xl flex items-center justify-center gap-2 transition-all">
                                        <svg class="w-5 h-5" :class="isCover ? 'fill-current' : 'fill-none'" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                        <span x-text="isCover ? 'SPK Cover Aktif' : 'Atur Sebagai Cover'"></span>
                                    </button>

                                    <button @click="downloadImage()" class="w-full py-3 px-4 bg-[#22B086] hover:bg-[#1C8D6C] text-white font-bold rounded-xl flex items-center justify-center gap-2 transition-colors shadow-lg shadow-emerald-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Download Foto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

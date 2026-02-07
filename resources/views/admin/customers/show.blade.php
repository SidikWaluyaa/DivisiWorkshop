<x-app-layout>
    {{-- Premium Hero Section (Light Theme) --}}
    <div class="relative bg-white border-b border-gray-200 overflow-hidden">
        {{-- Abstract Background --}}
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gray-50/50"></div>
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-[#22B086] blur-3xl opacity-5 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-[#FFC232] blur-3xl opacity-5"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                {{-- Customer Profile --}}
                <div class="flex items-center gap-6">
                    <div class="relative">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-800 text-3xl font-black shadow-xl border-4 border-white ring-2 ring-[#22B086]/20">
                            {{ substr($customer->name, 0, 2) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-8 h-8 bg-[#22B086] rounded-full border-4 border-white flex items-center justify-center shadow-lg">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>
                    <div class="text-center md:text-left">
                        <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">{{ $customer->name }}</h1>
                        <div class="flex items-center justify-center md:justify-start gap-4 mt-2 text-gray-500 text-sm font-medium">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#22B086]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $customer->phone }}
                            </span>
                            @if($customer->email)
                            <span class="flex items-center gap-1.5 border-l border-gray-200 pl-4">
                                <svg class="w-4 h-4 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                {{ $customer->email }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Quick Controls --}}
                <div class="flex gap-3">
                    <a href="{{ route('admin.customers.edit', $customer) }}" class="px-5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-gray-600 font-semibold transition-all">
                        Edit Profile
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="px-5 py-2.5 bg-[#FFC232] text-gray-900 rounded-xl font-bold hover:bg-[#FFB000] transition-colors shadow-lg shadow-orange-200">
                        Kembali
                    </a>
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm group hover:border-[#22B086]/30 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Order</p>
                            <p class="text-3xl font-black text-gray-900 mt-1">{{ $customer->workOrders->count() }} <span class="text-base font-medium text-gray-400">transaksi</span></p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-[#22B086]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                    </div>
                </div>

                @php
                    $totalSpent = $customer->workOrders->sum('total_price');
                @endphp
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm group hover:border-[#FFC232]/30 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Spend</p>
                            <p class="text-3xl font-black text-gray-900 mt-1">Rp {{ number_format($totalSpent, 0, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm group hover:border-gray-300 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Member Since</p>
                            <p class="text-3xl font-black text-gray-900 mt-1">{{ $customer->created_at->diffForHumans(null, true) }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">
            
            {{-- Info & Photos Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Left Column: Address & Info --}}
                <div class="space-y-8">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-[#22B086] rounded-full"></span>
                            Alamat & Catatan
                        </h3>
                        
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="mt-1 w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Alamat</p>
                                    <p class="text-gray-900 font-medium mt-1 leading-relaxed">{{ $customer->address ?? 'Belum diisi' }}</p>
                                    <p class="text-sm text-gray-500 mt-1">{{ $customer->city }} {{ $customer->province ? ', ' . $customer->province : '' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="mt-1 w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Catatan Customer</p>
                                    <div class="mt-2 bg-gray-50 rounded-xl p-3 border border-gray-200 text-sm text-gray-600 italic">
                                        "{{ $customer->notes ?? 'Tidak ada catatan khusus' }}"
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Photos --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-black text-gray-800 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-[#FFC232] rounded-full"></span>
                                Dokumen & Foto CS ({{ $customer->photos->count() }})
                            </h3>
                            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                                    class="px-4 py-2 bg-[#22B086] text-white rounded-xl hover:bg-[#1C8D6C] font-bold text-sm transition-all shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Upload Baru
                            </button>
                        </div>

                        @if($customer->photos->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($customer->photos as $photo)
                            <div class="relative group aspect-square rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 bg-gray-100" id="photo-container-{{ $photo->id }}">
                                <img src="{{ asset('storage/' . $photo->file_path) }}" 
                                     class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500 cursor-pointer"
                                     onclick="window.open('{{ asset('storage/' . $photo->file_path) }}', '_blank')">
                                
                                {{-- Delete Button --}}
                                <button onclick="deleteCustomerPhoto({{ $photo->id }})" 
                                        class="absolute top-2 right-2 p-1.5 bg-red-600/80 hover:bg-red-700 text-white rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-all transform hover:scale-110 z-10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-3a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>

                                <div class="absolute inset-0 pointer-events-none bg-gradient-to-t from-gray-900/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-3 flex flex-col justify-end">
                                    <p class="text-white text-xs font-bold line-clamp-1">{{ $photo->caption ?? 'Foto Customer' }}</p>
                                    <p class="text-gray-400 text-[10px]">{{ $photo->created_at->format('d/M/y') }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="h-64 flex flex-col items-center justify-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-4 shadow-sm">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-gray-400 font-medium">Belum ada dokumen foto</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- History Section --}}
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-900 flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-[#22B086]/10 flex items-center justify-center text-[#22B086]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        Riwayat Pesanan
                    </h3>
                    <span class="px-4 py-1 bg-[#FFC232]/20 text-orange-700 rounded-full text-xs font-bold border border-[#FFC232]/30">
                        {{ $customer->workOrders->count() }} Total
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-left">
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">No. SPK</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal Masuk</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Detail Sepatu</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($customer->workOrders as $order)
                            <tr class="group hover:bg-gray-50 transition-colors">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-900">{{ $order->spk_number }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-sm font-medium text-gray-600">{{ $order->entry_date->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $order->entry_date->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-xl">
                                            👟
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800">{{ $order->shoe_brand }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->shoe_type }} • {{ $order->shoe_color }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    @php
                                        $statusConfig = [
                                            'DONE' => ['bg' => 'bg-emerald-50', 'text' => 'text-[#1C8D6C]', 'icon' => '✅'],
                                            'CANCELLED' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'icon' => '❌'],
                                            'PROGRESS' => ['bg' => 'bg-orange-50', 'text' => 'text-[#FFB000]', 'icon' => '⚙️'],
                                        ];
                                        $statusClass = $statusConfig[$order->status->value ?? $order->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'icon' => '⏳'];
                                    @endphp
                                    <span class="px-3 py-1.5 rounded-lg text-xs font-bold border {{ $statusClass['bg'] }} {{ $statusClass['text'] }} border-transparent">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                        {{-- View Photos --}}

                                        {{-- View Photos --}}
                                        @php
                                            $valPhotos = $order->photos->map(function($p) {
                                                // DB path is relative to public disk (e.g., photos/orders/...)
                                                $size = 0;
                                                try {
                                                    if(\Illuminate\Support\Facades\Storage::disk('public')->exists($p->file_path)) {
                                                        $size = \Illuminate\Support\Facades\Storage::disk('public')->size($p->file_path);
                                                    }
                                                } catch(\Exception $e) {}
                                                
                                                $p->size_bytes = $size;
                                                $p->formatted_size = $size > 1048576 
                                                    ? round($size / 1048576, 2) . ' MB' 
                                                    : round($size / 1024, 2) . ' KB';
                                                return $p;
                                            });
                                        @endphp
                                        <button data-spk="{{ $order->spk_number }}" 
                                                data-photos="{{ $valPhotos->toJson() }}"
                                                onclick="openPhotoModal(this)" 
                                                class="p-2 bg-white border border-gray-200 rounded-lg text-[#FFC232] hover:bg-orange-50 hover:border-[#FFE399] transition-colors" title="Lihat Galeri Foto">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </button>

                                        {{-- Upload Photo --}}
                                        <button onclick="openOrderUploadModal('{{ $order->id }}', '{{ $order->spk_number }}')" 
                                                class="p-2 bg-white border border-gray-200 rounded-lg text-purple-600 hover:bg-purple-50 hover:border-purple-200 transition-colors" title="Upload Foto Baru">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        </button>

                                        {{-- Detail Link --}}
                                        <a href="{{ route('admin.orders.show', $order->id) }}" 
                                           class="px-4 py-2 bg-[#22B086] text-white rounded-lg text-xs font-bold hover:bg-[#1C8D6C] transition-colors shadow-sm shadow-emerald-200">
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        <p class="font-medium text-lg">Belum ada riwayat pesanan</p>
                                        <p class="text-sm">Customer ini belum pernah melakukan transaksi</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

        </div>
    </div>

    {{-- Order Photo Gallery Modal (Reused Logic) --}}
    <div id="orderPhotoModal" class="hidden fixed inset-0 bg-gray-900/70 backdrop-blur-md flex items-center justify-center z-50 transition-opacity">
        <div class="bg-white rounded-2xl max-w-6xl w-full mx-4 overflow-hidden border border-gray-100 shadow-2xl flex flex-col max-h-[90vh]">
            <div class="p-6 border-b border-gray-100 flex flex-wrap gap-4 justify-between items-center bg-white">
                <div>
                    <h3 class="text-2xl font-black text-gray-900 flex items-center gap-3">
                        <span class="w-10 h-10 rounded-lg bg-[#FFC232]/20 flex items-center justify-center text-[#FFC232]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </span>
                        Galeri Foto Order
                    </h3>
                    <div class="flex items-center gap-3 mt-1">
                        <p class="text-gray-400 font-mono" id="modalSpkNumber">SPK-XXX</p>
                        <span class="text-gray-600">|</span>
                        <p class="text-[#22B086] text-sm font-bold" id="modalTotalSize">Total: 0 MB</p>
                    </div>
                </div>
                
                {{-- Bulk Select Actions --}}
                <div id="bulkToolbar" class="flex items-center gap-2">
                    <button type="button" id="btnToggleSelect" onclick="toggleSelectMode()" 
                            class="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-gray-600 font-bold text-xs uppercase tracking-widest transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span id="btnSelectLabel">Pilih Foto</span>
                    </button>
                    <button type="button" id="btnSelectAll" onclick="selectAllPhotos()" class="hidden px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-gray-600 font-bold text-xs uppercase tracking-widest transition-all">Pilih Semua</button>
                    <button type="button" id="btnDeleteBulk" onclick="deleteSelectedPhotos()" class="hidden px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold text-xs uppercase tracking-widest shadow-md transition-all disabled:opacity-50" disabled>Hapus</button>
                </div>
                
                <button onclick="document.getElementById('orderPhotoModal').classList.add('hidden'); cancelBulkSelect();" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 hover:text-red-500 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-8 overflow-y-auto flex-1 custom-scrollbar bg-gray-50">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 h-full">
                    {{-- Before Column --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-xl">
                            <span class="w-3 h-3 rounded-full bg-red-500 animate-pulse shadow-[0_0_10px_rgba(239,68,68,0.5)]"></span>
                            <h4 class="font-bold text-red-400 tracking-wide uppercase text-sm">Kondisi Awal (Before)</h4>
                        </div>
                        <div id="beforePhotosContainer" class="space-y-4 min-h-[300px]">
                            {{-- Photos injected here --}}
                        </div>
                    </div>

                    {{-- After Column --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 mb-4 p-3 bg-green-500/10 border border-green-500/20 rounded-xl">
                            <span class="w-3 h-3 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                            <h4 class="font-bold text-green-400 tracking-wide uppercase text-sm">Hasil Akhir (After)</h4>
                        </div>
                        <div id="afterPhotosContainer" class="space-y-4 min-h-[300px]">
                            {{-- Photos injected here --}}
                        </div>
                    </div>
                </div>

                {{-- Other Photos --}}
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <h4 class="text-gray-400 font-bold mb-6 text-sm uppercase tracking-wider">Foto Lainnya / Proses</h4>
                    <div id="otherPhotosContainer" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                         {{-- Other photos --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Upload Modal (Chunk Upload with Compression) --}}
    <div id="orderUploadModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-[60] p-4 transition-all duration-300">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all scale-100 opacity-100">
            <!-- Header -->
            <div class="p-8 text-center bg-white border-b border-gray-50">
                <div class="mx-auto w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mb-4 text-purple-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 leading-tight">Upload Foto Order</h3>
                <p class="text-sm text-gray-500 mt-2 font-medium">
                    Upload foto baru untuk <span id="uploadSpkNumber" class="text-purple-600 font-bold px-1">SPK-XXX</span>
                </p>
            </div>

            <div class="p-8 space-y-6">
                
                <!-- Dropzone Area -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Pilih File</label>
                    <div class="relative group">
                        <input type="file" id="orderChunkFileInput" multiple accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="border-2 border-dashed border-gray-200 group-hover:border-purple-300 bg-gray-50/50 group-hover:bg-purple-50/30 rounded-2xl p-8 transition-all duration-300 flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-400 group-hover:text-purple-500 transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <span id="orderChunkFileLabelText" class="text-sm font-bold text-gray-500 group-hover:text-purple-600 transition-colors text-center px-4">
                                Klik untuk pilih foto
                            </span>
                            <p id="orderChunkFileCountText" class="text-[10px] font-medium text-gray-400 mt-1 hidden"></p>
                        </div>
                    </div>
                </div>

                <!-- Progress Container (Hidden by Default) -->
                <div id="orderUploadProgress" class="hidden space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Progress Upload</span>
                        <span id="orderUploadProgressText" class="text-xs font-bold text-purple-600">0%</span>
                    </div>
                    <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                        <div id="orderUploadProgressBar" class="h-full bg-gradient-to-r from-purple-500 to-purple-600 transition-all duration-300 highlight-bar" style="width: 0%"></div>
                    </div>
                    <p id="orderUploadStatusText" class="text-xs text-gray-400 font-medium"></p>
                </div>

                <!-- Step Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="orderStep" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">TAHAPAN</label>
                        <div class="relative">
                            <select id="orderStep" required 
                                    class="appearance-none block w-full bg-white border-2 border-gray-100 rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 focus:outline-none focus:border-purple-500 focus:ring-0 transition-all cursor-pointer">
                                <option value="RECEPTION">📦 Foto Referensi</option>
                                <option value="WAREHOUSE_BEFORE">🏭 Gudang (Before)</option>
                                <option value="PRODUCTION">⚙️ Produksi / Proses</option>
                                <option value="QC">✨ Quality Control</option>
                                <option value="FINISH">🏁 Finish / Packing</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">CAPTION (OPSIONAL)</label>
                        <input type="text" id="orderCaption" placeholder="Detail foto..." 
                               class="block w-full bg-white border-2 border-gray-100 rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 focus:outline-none focus:border-purple-500 focus:ring-0 transition-all">
                    </div>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <button type="button" onclick="closeOrderUploadModal()"
                            class="w-full px-6 py-4 bg-gray-100 text-gray-500 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="button" id="orderUploadBtn" onclick="startOrderChunkUpload()"
                            class="w-full px-6 py-4 bg-purple-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-purple-600/20 hover:bg-purple-700 hover:shadow-purple-700/30 transform hover:-translate-y-0.5 active:translate-y-0 transition-all disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Upload
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Customer Profile Upload Modal (Chunk Upload with Compression) --}}
    <div id="uploadModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-[60] p-4 transition-all duration-300">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all scale-100 opacity-100">
            <!-- Header -->
            <div class="p-8 text-center bg-white border-b border-gray-50">
                <div class="mx-auto w-16 h-16 bg-teal-50 rounded-2xl flex items-center justify-center mb-4 text-[#22B086]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 leading-tight">Upload Dokumen Customer</h3>
                <p class="text-sm text-gray-500 mt-2 font-medium">Upload file identitas atau dokumen pendukung</p>
            </div>

            <div class="p-8 space-y-6">
                <!-- Dropzone Area -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Pilih File</label>
                    <div class="relative group">
                        <input type="file" id="custChunkFileInput" multiple accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="border-2 border-dashed border-gray-200 group-hover:border-[#22B086]/30 bg-gray-50/50 group-hover:bg-[#22B086]/10 rounded-2xl p-8 transition-all duration-300 flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-400 group-hover:text-[#22B086] transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <span id="custChunkFileLabelText" class="text-sm font-bold text-gray-500 group-hover:text-[#22B086] transition-colors text-center px-4">
                                Klik untuk pilih dokumen
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Progress Container (Hidden by Default) -->
                <div id="custUploadProgress" class="hidden space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Progress Upload</span>
                        <span id="custUploadProgressText" class="text-xs font-bold text-[#22B086]">0%</span>
                    </div>
                    <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                        <div id="custUploadProgressBar" class="h-full bg-gradient-to-r from-[#22B086] to-[#1C8D6C] transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p id="custUploadStatusText" class="text-xs text-gray-400 font-medium"></p>
                </div>

                <!-- Meta Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="doc_type" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">JENIS DOKUMEN</label>
                        <div class="relative">
                            <select id="custDocType" 
                                    class="appearance-none block w-full bg-white border-2 border-gray-100 rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 focus:outline-none focus:border-[#22B086] focus:ring-0 transition-all cursor-pointer">
                                <option value="general">📄 Dokumen Umum</option>
                                <option value="before">📸 Foto Awal (Before)</option>
                                <option value="after">✨ Foto Akhir (After)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">KETERANGAN</label>
                        <input type="text" id="custDocCaption" placeholder="Contoh: KTP Susi..." 
                               class="block w-full bg-white border-2 border-gray-100 rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 focus:outline-none focus:border-[#22B086] focus:ring-0 transition-all">
                    </div>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <button type="button" onclick="closeCustUploadModal()"
                            class="w-full px-6 py-4 bg-gray-100 text-gray-500 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="button" id="custUploadBtn" onclick="startCustChunkUpload()"
                            class="w-full px-6 py-4 bg-[#22B086] text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-emerald-600/20 hover:bg-[#1C8D6C] hover:shadow-emerald-600/30 transform hover:-translate-y-0.5 active:translate-y-0 transition-all disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Upload
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function deleteCustomerPhoto(photoId) {
            if (!confirm('Yakin ingin menghapus dokumen ini?')) return;
            
            try {
                const res = await fetch(`/admin/customers/photos/${photoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await res.json();
                if (data.success) {
                    const el = document.getElementById(`photo-container-${photoId}`);
                    if (el) {
                        el.style.opacity = '0';
                        el.style.transform = 'scale(0.9)';
                        setTimeout(() => el.remove(), 300);
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: data.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    alert('Gagal: ' + data.message);
                }
            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan network');
            }
        }
    </script>

    {{-- Script for Gallery --}}
    <script>
        // Bulk Selection State
        let isSelectMode = false;
        let selectedPhotoIds = [];
        let currentPhotosData = [];
        let currentSpkNumber = '';

        function toggleSelectMode() {
            isSelectMode = !isSelectMode;
            const label = document.getElementById('btnSelectLabel');
            const selectAllBtn = document.getElementById('btnSelectAll');
            const deleteBtn = document.getElementById('btnDeleteBulk');

            if (isSelectMode) {
                label.textContent = 'Batal';
                selectAllBtn.classList.remove('hidden');
                deleteBtn.classList.remove('hidden');
                document.querySelectorAll('.photo-checkbox').forEach(cb => cb.classList.remove('hidden'));
                document.querySelectorAll('.photo-item').forEach(el => {
                    el.classList.add('ring-2', 'ring-transparent');
                });
            } else {
                cancelBulkSelect();
            }
        }

        function cancelBulkSelect() {
            isSelectMode = false;
            selectedPhotoIds = [];
            const label = document.getElementById('btnSelectLabel');
            const selectAllBtn = document.getElementById('btnSelectAll');
            const deleteBtn = document.getElementById('btnDeleteBulk');

            if(label) label.textContent = 'Pilih Foto';
            if(selectAllBtn) selectAllBtn.classList.add('hidden');
            if(deleteBtn) { deleteBtn.classList.add('hidden'); deleteBtn.disabled = true; deleteBtn.textContent = 'Hapus'; }
            document.querySelectorAll('.photo-checkbox').forEach(cb => { cb.classList.add('hidden'); cb.checked = false; });
            document.querySelectorAll('.photo-item').forEach(el => {
                el.classList.remove('ring-[#22B086]', 'ring-2');
                el.classList.add('ring-transparent');
            });
        }

        function selectAllPhotos() {
            selectedPhotoIds = currentPhotosData.map(p => p.id);
            document.querySelectorAll('.photo-checkbox').forEach(cb => { cb.checked = true; });
            document.querySelectorAll('.photo-item').forEach(el => {
                el.classList.add('ring-[#22B086]');
                el.classList.remove('ring-transparent');
            });
            updateDeleteButton();
        }

        function togglePhotoSelection(photoId, wrapper, checkbox) {
            if (checkbox.checked) {
                if (!selectedPhotoIds.includes(photoId)) selectedPhotoIds.push(photoId);
                wrapper.classList.add('ring-[#22B086]');
                wrapper.classList.remove('ring-transparent');
            } else {
                selectedPhotoIds = selectedPhotoIds.filter(id => id !== photoId);
                wrapper.classList.remove('ring-[#22B086]');
                wrapper.classList.add('ring-transparent');
            }
            updateDeleteButton();
        }

        function updateDeleteButton() {
            const deleteBtn = document.getElementById('btnDeleteBulk');
            if (deleteBtn) {
                deleteBtn.disabled = selectedPhotoIds.length === 0;
                deleteBtn.textContent = `Hapus (${selectedPhotoIds.length})`;
            }
        }

        async function deleteSelectedPhotos() {
            if (selectedPhotoIds.length === 0) return;
            if (!confirm(`Yakin ingin menghapus ${selectedPhotoIds.length} foto secara PERMANEN? File akan dihapus dari server.`)) return;

            try {
                const response = await fetch('{{ route("photos.bulk-destroy") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ids: selectedPhotoIds })
                });

                const result = await response.json();
                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: result.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                    // Refresh the modal by filtering out deleted photos
                    currentPhotosData = currentPhotosData.filter(p => !selectedPhotoIds.includes(p.id));
                    cancelBulkSelect();
                    openPhotoModal(currentSpkNumber, currentPhotosData);
                } else {
                    alert('Gagal menghapus foto: ' + (result.message || 'Error unknown'));
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan saat menghapus foto.');
            }
        }

        function openPhotoModal(arg, photosData = null) {
            let spk, photos;
            if (arg instanceof HTMLElement) {
                spk = arg.dataset.spk;
                photos = JSON.parse(arg.dataset.photos);
            } else {
                spk = arg;
                photos = photosData;
            }

            currentSpkNumber = spk;
            currentPhotosData = photos;
            
            cancelBulkSelect(); // Reset selection state when opening
            
            document.getElementById('modalSpkNumber').textContent = spk;
            const beforeContainer = document.getElementById('beforePhotosContainer');
            const afterContainer = document.getElementById('afterPhotosContainer');
            const otherContainer = document.getElementById('otherPhotosContainer');
            
            // Calculate Total Size
            let totalBytes = photos.reduce((acc, curr) => acc + (curr.size_bytes || 0), 0);
            let sizeText = '0 KB';
            if (totalBytes > 1048576) {
                sizeText = (totalBytes / 1048576).toFixed(2) + ' MB';
            } else {
                sizeText = (totalBytes / 1024).toFixed(2) + ' KB';
            }
            const modalTotalSize = document.getElementById('modalTotalSize');
            if(modalTotalSize) modalTotalSize.textContent = 'Total: ' + sizeText;
            
            // Clean
            beforeContainer.innerHTML = '';
            afterContainer.innerHTML = '';
            otherContainer.innerHTML = '';
            
            beforeContainer.className = "grid grid-cols-2 gap-4 auto-rows-max px-2";
            afterContainer.className = "grid grid-cols-2 gap-4 auto-rows-max px-2";

            const beforeSteps = ['RECEPTION', 'WAREHOUSE_BEFORE', 'ASSESSMENT', 'before'];
            const afterSteps = ['QC', 'QC_FINAL', 'FINISH', 'PACKING', 'after'];

            let hasBefore = false;
            let hasAfter = false;

            photos.forEach(photo => {
                const img = document.createElement('img');
                img.src = `/storage/${photo.file_path}`;
                img.className = 'w-full h-40 object-cover rounded-xl shadow-sm border border-gray-200 hover:scale-[1.02] transition-transform cursor-pointer ring-1 ring-black/5';
                img.onclick = () => window.open(img.src, '_blank');
                
                const wrapper = document.createElement('div');
                wrapper.className = 'relative group photo-item transition-all rounded-xl';
                wrapper.dataset.photoId = photo.id;
                wrapper.appendChild(img);

                // Checkbox for bulk selection
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'photo-checkbox hidden absolute top-2 left-2 w-5 h-5 rounded border-gray-300 text-[#22B086] focus:ring-[#22B086] z-30 cursor-pointer';
                checkbox.onclick = (e) => { e.stopPropagation(); togglePhotoSelection(photo.id, wrapper, checkbox); };
                wrapper.appendChild(checkbox);
                
                // Caption & Size
                const cap = document.createElement('div');
                cap.className = 'absolute bottom-0 left-0 right-0 bg-white/95 backdrop-blur-sm text-gray-800 p-2 opacity-0 group-hover:opacity-100 transition-opacity rounded-b-xl border-t border-gray-100';
                
                const sizeBadge = photo.formatted_size ? `<span class="bg-gray-100 text-[9px] px-1 rounded ml-1 text-gray-400 border border-gray-200">${photo.formatted_size}</span>` : '';
                
                cap.innerHTML = `
                    <div class="text-[10px] font-bold line-clamp-1">${photo.caption || ''}</div>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-[9px] text-gray-400 font-medium">${photo.created_at ? new Date(photo.created_at).toLocaleDateString() : ''}</span>
                        ${sizeBadge}
                    </div>
                `;
                wrapper.appendChild(cap);


                // Delete Button
                const delBtn = document.createElement('button');
                delBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-3a1 1 0 00-1 1v3M4 7h16"></path></svg>';
                delBtn.className = 'absolute top-2 right-2 p-1.5 bg-red-600/80 hover:bg-red-700 text-white rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-all transform hover:scale-110 z-10';
                delBtn.title = 'Hapus Foto';
                delBtn.onclick = (e) => {
                    e.stopPropagation(); 
                    if(confirm('Yakin ingin menghapus foto ini?')) {
                        deletePhoto(photo.id, wrapper);
                    }
                };
                wrapper.appendChild(delBtn);

                // Set as Cover Button
                const coverBtn = document.createElement('button');
                coverBtn.innerHTML = photo.is_spk_cover 
                    ? '<svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>'
                    : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>';
                
                coverBtn.className = photo.is_spk_cover
                    ? 'absolute top-2 left-2 p-1.5 bg-amber-500 text-white rounded-lg shadow-lg z-10'
                    : 'absolute top-2 left-2 p-1.5 bg-gray-900/60 hover:bg-amber-500 text-white rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-all transform hover:scale-110 z-10';
                
                coverBtn.title = photo.is_spk_cover ? 'SPK Cover Aktif' : 'Atur sebagai Cover SPK';
                coverBtn.onclick = (e) => {
                    e.stopPropagation();
                    setSpkCover(photo.id, spk, photos);
                };
                wrapper.appendChild(coverBtn);

                // Cover Badge (If active)
                if(photo.is_spk_cover) {
                    const badge = document.createElement('div');
                    badge.className = 'absolute bottom-2 right-2 px-2 py-0.5 bg-amber-500 text-white text-[8px] font-black rounded uppercase tracking-widest shadow-sm';
                    badge.textContent = 'COVER SPK';
                    wrapper.appendChild(badge);
                    wrapper.querySelector('img').classList.add('ring-2', 'ring-amber-500', 'ring-offset-2', 'ring-offset-gray-900');
                }
                
                // Reference Badge (If RECEPTION)
                if(photo.step === 'RECEPTION') {
                    const refBadge = document.createElement('div');
                    refBadge.className = 'absolute top-2 right-2 px-2 py-0.5 bg-purple-600 text-white text-[8px] font-black rounded-lg uppercase tracking-wider shadow-lg border border-purple-500/50 z-20 flex items-center gap-1';
                    refBadge.innerHTML = '<span>📦</span> <span>REFERENSI</span>';
                    wrapper.appendChild(refBadge);
                    // Adjust delete button to not overlap too much
                    const existingDelBtn = wrapper.querySelector('button[title="Hapus Foto"]');
                    if(existingDelBtn) existingDelBtn.classList.replace('top-2', 'top-10');
                }

                if (beforeSteps.includes(photo.step) || (photo.step && photo.step.includes('BEFORE'))) {
                    beforeContainer.appendChild(wrapper);
                    hasBefore = true;
                } else if (afterSteps.includes(photo.step) || (photo.step && photo.step.includes('AFTER'))) {
                    afterContainer.appendChild(wrapper);
                    hasAfter = true;
                } else {
                    otherContainer.appendChild(wrapper);
                }
            });

            // Empty States with Premium Icons
            const emptyState = (text) => `
                <div class="col-span-2 flex flex-col items-center justify-center p-8 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-gray-400 font-bold text-xs italic">${text}</p>
                </div>
            `;

            if (!hasBefore) beforeContainer.innerHTML = emptyState('Belum ada foto before');
            if (!hasAfter) afterContainer.innerHTML = emptyState('Belum ada foto after');

            document.getElementById('orderPhotoModal').classList.remove('hidden');
        }

        async function deletePhoto(photoId, element) {
            try {
                const response = await fetch(`/photos/${photoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    element.style.transition = 'all 0.3s ease';
                    element.style.opacity = '0';
                    element.style.transform = 'scale(0.9)';
                    setTimeout(() => element.remove(), 300);
                } else {
                    alert('Gagal menghapus foto: ' + (result.message || 'Error unknown'));
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan saat menghapus foto.');
            }
        }

        async function setSpkCover(photoId, spk, allPhotos) {
            try {
                const response = await fetch(`/photos/${photoId}/set-cover`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    // Update the local data and re-render
                    allPhotos.forEach(p => {
                        p.is_spk_cover = (p.id == photoId);
                    });
                    openPhotoModal(spk, allPhotos); // Refresh modal content
                    
                    // Show success toast
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan saat mengatur cover SPK.');
            }
        }
    </script>

    {{-- Resumable.js for Chunk Upload --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/resumable.js/1.1.0/resumable.min.js"></script>
    <script>
        // Store current customer ID
        const customerId = {{ $customer->id }};
        
        // --- Customer Profile Photo Chunk Upload ---
        let custResumable = null;

        function initCustResumable() {
            if (custResumable) return;

            custResumable = new Resumable({
                target: `{{ route('admin.customers.photos.chunk', $customer->id) }}`,
                query: () => ({
                    _token: '{{ csrf_token() }}',
                    caption: document.getElementById('custDocCaption').value,
                    type: document.getElementById('custDocType').value
                }),
                fileType: ['jpg', 'jpeg', 'png'],
                chunkSize: 1 * 1024 * 1024, // 1MB chunks
                headers: {
                    'Accept': 'application/json'
                },
                testChunks: false,
                throttleProgressCallbacks: 1
            });

            custResumable.assignBrowse(document.getElementById('custChunkFileInput'));

            custResumable.on('fileAdded', function(file) {
                document.getElementById('custChunkFileLabelText').textContent = file.fileName + ' (' + formatSize(file.size) + ')';
                document.getElementById('custUploadBtn').disabled = false;
                document.getElementById('custUploadProgress').classList.add('hidden');
            });

            custResumable.on('fileProgress', function(file) {
                const progress = Math.floor(file.progress() * 100);
                document.getElementById('custUploadProgressBar').style.width = `${progress}%`;
                document.getElementById('custUploadProgressText').textContent = `${progress}%`;
                document.getElementById('custUploadStatusText').textContent = 'Mengupload: ' + progress + '%';
            });

            custResumable.on('fileSuccess', function(file, response) {
                const data = JSON.parse(response);
                if (data.success) {
                    document.getElementById('custUploadStatusText').textContent = 'Upload Selesai! Mengompres...';
                    document.getElementById('custUploadProgressBar').classList.add('bg-green-500');
                    setTimeout(() => {
                        location.reload(); 
                    }, 1000);
                } else {
                    alert('Upload gagal: ' + data.message);
                    resetCustUpload();
                }
            });

            custResumable.on('fileError', function(file, response) {
                alert('Terjadi kesalahan saat upload.');
                resetCustUpload();
            });
        }

        function startCustChunkUpload() {
            if (!custResumable || custResumable.files.length === 0) return;
            document.getElementById('custUploadBtn').disabled = true;
            document.getElementById('custUploadProgress').classList.remove('hidden');
            custResumable.upload();
        }

        function closeCustUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
            if(custResumable) custResumable.cancel();
            resetCustUpload();
        }

        function resetCustUpload() {
            document.getElementById('custChunkFileLabelText').textContent = 'Klik untuk pilih dokumen';
            document.getElementById('custUploadBtn').disabled = true;
            document.getElementById('custUploadProgress').classList.add('hidden');
            document.getElementById('custUploadProgressBar').style.width = '0%';
            document.getElementById('custDocCaption').value = '';
        }

        function updateCustFileLabel(input) {
            // Placeholder for original handler compatibility, now handled by Resumable
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', () => {
             // Delay init until modal open or just init here
             // It's safe to init early as browse button exists
             initCustResumable();
             initOrderResumable();
        });


        // --- Order Photo Chunk Upload ---
        let orderResumable = null;
        let currentOrderSpk = null;
        let currentOrderId = null;
        let uploadedPhotoIds = [];

        function initOrderResumable() {
            if (orderResumable) return;

            orderResumable = new Resumable({
                target: () => window.location.origin + `/orders/${currentOrderId}/photos/chunk`,
                query: () => ({
                    _token: '{{ csrf_token() }}',
                    caption: document.getElementById('orderCaption').value,
                    step: document.getElementById('orderStep').value
                }),
                fileType: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG', 'webp', 'WEBP'],
                chunkSize: 1 * 1024 * 1024,
                headers: { 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                testChunks: false,
                throttleProgressCallbacks: 1,
                maxFiles: 10,
                fileTypeErrorCallback: function(file, errorCount) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tipe File Tidak Didukung',
                        text: 'Silakan pilih file gambar (JPG, PNG, atau WEBP).'
                    });
                }
            });

            orderResumable.assignBrowse(document.getElementById('orderChunkFileInput'));

            orderResumable.on('fileAdded', function(file) {
                 console.log('File added:', file.fileName);
                 updateOrderFileLabel();
                 document.getElementById('orderUploadBtn').disabled = false;
            });

            orderResumable.on('filesAdded', function(files) {
                 console.log('Multiple files added:', files.length);
                 updateOrderFileLabel();
                 document.getElementById('orderUploadBtn').disabled = false;
                 document.getElementById('orderUploadProgress').classList.add('hidden');
            });

            function updateOrderFileLabel() {
                 const count = orderResumable.files.length;
                 const label = document.getElementById('orderChunkFileLabelText');
                 if (count === 0) {
                     label.textContent = 'Klik untuk pilih foto';
                 } else if (count === 1) {
                     label.textContent = orderResumable.files[0].fileName;
                 } else {
                     label.textContent = `${count} File Terpilih`;
                 }
            }

            orderResumable.on('fileProgress', function(file) {
                const progress = Math.floor(orderResumable.progress() * 100);
                document.getElementById('orderUploadProgressBar').style.width = `${progress}%`;
                document.getElementById('orderUploadProgressText').textContent = `${progress}%`;
                document.getElementById('orderUploadStatusText').textContent = 'Mengupload...';
            });

            orderResumable.on('fileSuccess', function(file, response) {
                try {
                    const res = JSON.parse(response);
                    if (res.success && res.photo_id) {
                        uploadedPhotoIds.push(res.photo_id);
                        console.log(`Uploaded & Collected ID: ${res.photo_id} for file: ${file.fileName}`);
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
            });

            orderResumable.on('complete', function() {
                document.getElementById('orderUploadStatusText').textContent = 'Semua file terupload! Menunggu antrian...';
                // Wait 1 second to ensure all fileSuccess events have pushed IDs to the array
                setTimeout(() => {
                    console.log('Final collected IDs before processing:', uploadedPhotoIds);
                    processSequential(uploadedPhotoIds);
                }, 1000);
            });
            
            orderResumable.on('fileError', function(file, message) {
                 console.error('Upload Error:', message);
                 // message often contains a JSON string if it's a Laravel error
                 let errorMsg = message;
                 try {
                     const errData = JSON.parse(message);
                     errorMsg = errData.message || message;
                 } catch(e) {}
                 
                 alert('Gagal mengupload file ' + file.fileName + ': ' + errorMsg);
            });
        }
        
        function openOrderUploadModal(orderId, spkNumber) {
            currentOrderId = orderId;
            currentOrderSpk = spkNumber;
            document.getElementById('uploadSpkNumber').textContent = spkNumber;
            
            // Reset state
            uploadedPhotoIds = [];
            if(orderResumable) {
                orderResumable.cancel(); // Clear any existing files in queue
            }
            document.getElementById('orderChunkFileLabelText').textContent = 'Klik untuk pilih foto';
            document.getElementById('orderUploadBtn').disabled = true;
            document.getElementById('orderUploadProgress').classList.add('hidden');
            document.getElementById('orderUploadProgressBar').style.width = '0%';
            document.getElementById('orderCaption').value = '';

            document.getElementById('orderUploadModal').classList.remove('hidden');
        }

        function startOrderChunkUpload() {
            if (!orderResumable || orderResumable.files.length === 0) return;
            document.getElementById('orderUploadBtn').disabled = true;
            document.getElementById('orderUploadProgress').classList.remove('hidden');
            orderResumable.upload();
        }
        
        function closeOrderUploadModal() {
            document.getElementById('orderUploadModal').classList.add('hidden');
            if(orderResumable) orderResumable.cancel();
            document.getElementById('orderChunkFileLabelText').textContent = 'Klik untuk pilih foto';
            document.getElementById('orderUploadBtn').disabled = true;
            document.getElementById('orderUploadProgress').classList.add('hidden');
        }

        // --- Sequential Processing Logic (True per-photo processing) ---
        async function processSequential(ids) {
            console.log('Starting sequential processing for IDs:', ids);
            
            if (!ids || ids.length === 0) {
                console.log('No IDs to process, reloading...');
                location.reload();
                return;
            }

            const total = ids.length;
            const statusText = document.getElementById('orderUploadStatusText');
            const progressBar = document.getElementById('orderUploadProgressBar');
            let failureCount = 0;
            let lastErrorMessage = '';
            
            for (let i = 0; i < ids.length; i++) {
                const photoId = ids[i];
                const currentNum = i + 1;
                console.log(`Processing photo ${currentNum}/${total} (ID: ${photoId})`);
                
                statusText.textContent = `Mengompres foto (${currentNum}/${total})...`;
                
                // Update progress bar to reflect processing progress
                const procProgress = (currentNum / total) * 100;
                progressBar.style.width = `${procProgress}%`;

                try {
                    // Small delay to allow server cleanup between heavy tasks
                    if (i > 0) await new Promise(resolve => setTimeout(resolve, 500));

                    const response = await fetch(window.location.origin + `/photos/${photoId}/process`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    
                    const responseText = await response.text();
                    let result;
                    try {
                        result = JSON.parse(responseText);
                    } catch (pE) {
                        console.error(`Raw response for ID ${photoId} was not JSON:`, responseText);
                        failureCount++;
                        lastErrorMessage = 'Invalid server response. Check console.';
                        continue;
                    }

                    if(!result.success) {
                        failureCount++;
                        lastErrorMessage = result.message || 'Unknown error';
                        console.error(`Failed to process photo ${photoId}:`, lastErrorMessage);
                    } else {
                        console.log(`Successfully processed photo ID: ${photoId}`);
                    }
                } catch(e) {
                    failureCount++;
                    lastErrorMessage = e.message;
                    console.error(`Network error processing photo ${photoId}:`, e);
                }
            }

            console.log(`Processing complete. Failures: ${failureCount}`);

            if (failureCount > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Proses Selesai dengan Catatan',
                    text: `${failureCount} dari ${total} foto gagal dikompres. Silakan cek koneksi/log.`,
                    confirmButtonText: 'Tutup'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Semua foto berhasil diupload dan dikompres.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        }

        function formatSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }


        function updateFileLabel(input) {
            const label = document.getElementById('fileLabelText');
            const count = document.getElementById('fileCountText');
            
            if (input.files && input.files.length > 0) {
                if (input.files.length === 1) {
                    label.textContent = input.files[0].name;
                } else {
                    label.textContent = input.files.length + ' file terpilih';
                }
                label.classList.add('text-purple-600');
                
                // Show filenames (up to 3)
                let names = Array.from(input.files).map(f => f.name).slice(0, 3).join(', ');
                if(input.files.length > 3) names += ', ...';
                
                count.textContent = names;
                count.classList.remove('hidden');
            } else {
                label.textContent = 'Klik untuk pilih foto';
                label.classList.remove('text-purple-600');
                count.classList.add('hidden');
            }
        }

        function updateCustFileLabel(input) {
            const label = document.getElementById('custFileLabelText');
            if (input.files && input.files.length > 0) {
                if (input.files.length === 1) {
                    label.textContent = input.files[0].name;
                } else {
                    label.textContent = input.files.length + ' file terpilih';
                }
                label.classList.add('text-[#22B086]');
            } else {
                label.textContent = 'Klik untuk pilih dokumen';
                label.classList.remove('text-[#22B086]');
            }
        }

        // Modal close on backdrop click for all modals
        window.onclick = function(event) {
            const orderModal = document.getElementById('orderUploadModal');
            const custModal = document.getElementById('uploadModal');
            if (event.target === orderModal) orderModal.classList.add('hidden');
            if (event.target === custModal) custModal.classList.add('hidden');
        }
    </script>

    <style>
        @keyframes modalEnter {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-modal-enter {
            animation: modalEnter 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        #orderUploadModal .bg-white, #uploadModal .bg-white {
            animation: modalEnter 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
    </style>
</x-app-layout>

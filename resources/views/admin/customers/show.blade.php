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
                    $totalSpent = $customer->workOrders->sum(fn($o) => $o->total_price);
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
                                Dokumen & Foto ({{ $customer->photos->count() }})
                            </h3>
                            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                                    class="px-4 py-2 bg-[#22B086] text-white rounded-xl hover:bg-[#1C8D6C] font-bold text-sm transition-all shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Upload Baru
                            </button>
                        </div>

                        @if($customer->photos->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($customer->photos->take(8) as $photo)
                            <div class="relative group aspect-square rounded-xl overflow-hidden cursor-pointer shadow-sm hover:shadow-xl transition-all duration-300 bg-gray-100" 
                                 onclick="window.open('{{ asset('storage/' . $photo->file_path) }}', '_blank')">
                                <img src="{{ asset('storage/' . $photo->file_path) }}" 
                                     class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-3 flex flex-col justify-end">
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
                                        <button onclick="openPhotoModal('{{ $order->spk_number }}', {{ $valPhotos->toJson() }})" 
                                                class="p-2 bg-white border border-gray-200 rounded-lg text-[#FFC232] hover:bg-orange-50 hover:border-[#FFE399] transition-colors" title="Lihat Galeri Foto">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </button>

                                        {{-- Upload Photo --}}
                                        <button onclick="openOrderUploadModal('{{ $order->id }}', '{{ $order->spk_number }}', '{{ route('work-order-photos.store', $order->id) }}')" 
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
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white">
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
                <button onclick="document.getElementById('orderPhotoModal').classList.add('hidden')" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 hover:text-red-500 transition-all">
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

    {{-- Order Upload Modal (Modern) --}}
    <div id="orderUploadModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl transform transition-all scale-100">
            <h3 class="text-2xl font-black text-gray-900 mb-6 flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                </span>
                Upload Foto
            </h3>
            <p class="text-sm text-gray-500 -mt-4 mb-6">Upload foto baru untuk <span id="uploadSpkNumber" class="font-bold text-purple-600 font-mono">SPK-XXX</span></p>
            
            <form id="orderUploadForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Pilih File</label>
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer hover:bg-gray-50 hover:border-purple-500 transition-colors group">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-3 text-gray-400 group-hover:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p id="fileLabelText" class="text-sm text-gray-500 group-hover:text-purple-600">Klik untuk pilih foto</p>
                                <p id="fileCountText" class="text-xs text-gray-400 mt-1 hidden"></p>
                            </div>
                            <input type="file" name="photos[]" id="fileInput" multiple accept="image/*" required class="hidden" onchange="updateFileLabel(this)" />
                        </label>
                        <ul class="text-xs text-gray-500 mt-3 space-y-1 list-disc list-inside">
                            <li>Maksimal <strong>10MB</strong> per foto</li>
                            <li>Foto akan otomatis <strong>dikompres & watermark</strong></li>
                            <li>Bisa upload banyak foto sekaligus</li>
                        </ul>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Tahapan</label>
                        <div class="relative">
                            <select name="step" class="w-full bg-gray-50 border-gray-200 text-gray-800 rounded-xl focus:ring-purple-500 focus:border-purple-500 appearance-none py-3 px-4 font-medium">
                                 <option value="RECEPTION">📦 Penerimaan (Reception)</option>
                                 <option value="WAREHOUSE_BEFORE">🏭 Gudang (Before)</option>
                                 <option value="PRODUCTION">⚙️ Produksi / Proses</option>
                                 <option value="QC_FINAL">✨ QC Final (After)</option>
                                 <option value="FINISH">🛍️ Finish / Packing</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Caption</label>
                        <input type="text" name="caption" placeholder="Contoh: Detail noda di bagian heel..." 
                               class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 py-3 px-4">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-8">
                    <button type="button" onclick="document.getElementById('orderUploadModal').classList.add('hidden')" 
                            class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-3 bg-purple-600 text-white rounded-xl font-bold hover:bg-purple-700 hover:shadow-lg hover:shadow-purple-500/30 transition-all">
                        Upload Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Customer Profile Upload Modal --}}
    <div id="uploadModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
            <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
                Upload Dokumen Customer
            </h3>
            <form action="{{ route('admin.customers.upload-photo', $customer) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Pilih Foto</label>
                        <input type="file" name="photos[]" multiple accept="image/*" required 
                               class="w-full border-gray-300 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Caption</label>
                        <input type="text" name="caption" class="w-full border-gray-300 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Tipe</label>
                        <select name="type" class="w-full border-gray-300 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                            <option value="general">General</option>
                            <option value="before">Before</option>
                            <option value="after">After</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-8">
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="px-4 py-3 bg-gray-100 rounded-xl font-bold">Batal</button>
                    <button type="submit" class="px-4 py-3 bg-[#22B086] text-white rounded-xl font-bold shadow-lg hover:bg-[#1C8D6C]">Upload</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script for Gallery --}}
    <script>
        function openPhotoModal(spk, photos) {
            document.getElementById('modalSpkNumber').textContent = spk;
            const beforeContainer = document.getElementById('beforePhotosContainer');
            
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
            const afterContainer = document.getElementById('afterPhotosContainer');
            const otherContainer = document.getElementById('otherPhotosContainer');
            
            // Clean
            beforeContainer.innerHTML = '';
            afterContainer.innerHTML = '';
            otherContainer.innerHTML = '';
            beforeContainer.className = "grid grid-cols-2 gap-4 auto-rows-max px-2";
            afterContainer.className = "grid grid-cols-2 gap-4 auto-rows-max px-2";

            const beforeSteps = ['RECEPTION', 'WAREHOUSE_BEFORE', 'ASSESSMENT', 'before'];
            const afterSteps = ['QC_FINAL', 'FINISH', 'PACKING', 'after'];

            let hasBefore = false;
            let hasAfter = false;

            photos.forEach(photo => {
                const img = document.createElement('img');
                img.src = `/storage/${photo.file_path}`;
                img.className = 'w-full h-40 object-cover rounded-lg shadow-md border border-gray-700 hover:scale-105 transition-transform cursor-pointer ring-1 ring-white/10';
                img.onclick = () => window.open(img.src, '_blank');
                
                const wrapper = document.createElement('div');
                wrapper.className = 'relative group';
                wrapper.appendChild(img);
                
                // Caption & Size
                const cap = document.createElement('div');
                cap.className = 'absolute bottom-0 left-0 right-0 bg-black/80 backdrop-blur-sm text-white p-2 opacity-0 group-hover:opacity-100 transition-opacity rounded-b-lg';
                
                const sizeBadge = photo.formatted_size ? `<span class="bg-gray-700 text-[9px] px-1 rounded ml-1 text-gray-300 border border-gray-600">${photo.formatted_size}</span>` : '';
                
                cap.innerHTML = `
                    <div class="text-[10px] line-clamp-1">${photo.caption || ''}</div>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-[9px] text-gray-400">${photo.created_at ? new Date(photo.created_at).toLocaleDateString() : ''}</span>
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
                <div class="col-span-2 flex flex-col items-center justify-center p-8 border-2 border-dashed border-gray-700 rounded-xl bg-white/5">
                    <svg class="w-12 h-12 text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-gray-500 font-medium text-sm italic">${text}</p>
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

        function openOrderUploadModal(orderId, spkNumber, url) {
            document.getElementById('uploadSpkNumber').textContent = spkNumber;
            document.getElementById('orderUploadForm').action = url; 
            document.getElementById('orderUploadModal').classList.remove('hidden');
            
            // Reset input
            document.getElementById('fileInput').value = '';
            updateFileLabel(document.getElementById('fileInput'));
        }

        function updateFileLabel(input) {
            const label = document.getElementById('fileLabelText');
            const count = document.getElementById('fileCountText');
            
            if (input.files && input.files.length > 0) {
                label.textContent = input.files.length + " foto dipilih";
                label.className = "text-sm font-bold text-purple-600";
                
                // Show filenames (up to 3)
                let names = Array.from(input.files).map(f => f.name).slice(0, 3).join(', ');
                if(input.files.length > 3) names += ', ...';
                
                count.textContent = names;
                count.className = "text-xs text-gray-500 mt-1 block";
            } else {
                label.textContent = "Klik untuk pilih foto";
                label.className = "text-sm text-gray-500 group-hover:text-purple-600";
                count.className = "hidden";
            }
        }
    </script>
</x-app-layout>

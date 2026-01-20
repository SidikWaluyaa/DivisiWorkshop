<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <h2 class="font-bold text-xl leading-tight tracking-wide">{{ $customer->name }}</h2>
                    <div class="text-xs font-medium opacity-90">Detail Customer</div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.customers.edit', $customer) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition-colors">
                    Edit
                </a>
                <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 font-semibold transition-colors">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Customer Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                    Informasi Customer
                </h3>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Nama Lengkap</label>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $customer->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">No. WhatsApp</label>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $customer->phone }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Email</label>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $customer->email ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Kota</label>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $customer->city ?? '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm font-semibold text-gray-600">Alamat Lengkap</label>
                        <p class="text-gray-900 mt-1">{{ $customer->address ?? '-' }}</p>
                    </div>
                    @if($customer->notes)
                    <div class="col-span-2">
                        <label class="text-sm font-semibold text-gray-600">Catatan</label>
                        <p class="text-gray-700 mt-1 bg-yellow-50 p-3 rounded-lg border border-yellow-200">{{ $customer->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Photos Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                        Foto Customer ({{ $customer->photos->count() }})
                    </h3>
                    <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                            class="px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg hover:from-teal-700 hover:to-teal-800 font-semibold transition-all shadow-md flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Upload Foto
                    </button>
                </div>

                @if($customer->photos->count() > 0)
                <div class="grid grid-cols-4 gap-4">
                    @foreach($customer->photos as $photo)
                    <div class="relative group overflow-hidden rounded-lg border-2 border-gray-200 hover:border-teal-500 transition-all">
                        <img src="{{ asset('storage/' . $photo->file_path) }}" 
                             alt="{{ $photo->caption }}" 
                             class="w-full h-48 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-3">
                            <p class="text-white text-sm font-semibold">{{ $photo->caption ?? 'No caption' }}</p>
                            <p class="text-white/80 text-xs">{{ $photo->created_at->format('d M Y H:i') }}</p>
                            @if($photo->uploader)
                            <p class="text-white/60 text-xs">By: {{ $photo->uploader->name }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada foto</h3>
                    <p class="mt-1 text-sm text-gray-500">Upload foto customer untuk dokumentasi</p>
                </div>
                @endif
            </div>

            {{-- Work Orders --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    Riwayat SPK ({{ $customer->workOrders->count() }})
                </h3>
                @if($customer->workOrders->count() > 0)
                <div class="space-y-3">
                    @foreach($customer->workOrders as $order)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-teal-500 transition-colors">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <p class="font-bold text-gray-900">{{ $order->spk_number }}</p>
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold">
                                    {{ $order->status }}
                                </span>
                            </div>
                            <div class="mt-1 flex items-center gap-4 text-sm text-gray-600">
                                <span>📅 {{ $order->created_at->format('d M Y') }}</span>
                                @if($order->shoe_brand)
                                <span>👟 {{ $order->shoe_brand }}</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('finance.show', $order) }}" class="px-3 py-1.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 text-xs font-semibold transition-colors">
                            Lihat Detail
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada SPK</h3>
                    <p class="mt-1 text-sm text-gray-500">Customer belum pernah melakukan order</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Upload Photo Modal --}}
    <div id="uploadModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 shadow-2xl" onclick="event.stopPropagation()">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Upload Foto Customer
            </h3>
            <form action="{{ route('admin.customers.upload-photo', $customer) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Foto</label>
                        <input type="file" name="photos[]" multiple accept="image/*" required 
                               class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                        <p class="text-xs text-gray-500 mt-1">📷 Max 10 foto • Akan di-compress & watermark otomatis</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Caption (Opsional)</label>
                        <input type="text" name="caption" placeholder="Contoh: Foto sepatu sebelum repair" 
                               class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Foto</label>
                        <select name="type" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                            <option value="general">General</option>
                            <option value="before">Before</option>
                            <option value="after">After</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg hover:from-teal-700 hover:to-teal-800 font-semibold transition-all shadow-md">
                        Upload
                    </button>
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" 
                            class="flex-1 px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

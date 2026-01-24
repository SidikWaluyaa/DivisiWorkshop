<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">
                    {{ __('Gudang Penerimaan') }}
                </h2>
                <div class="text-xs font-medium opacity-90">
                    Penerimaan & Validasi Data
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50" x-data="{ 
        selectedItems: [],
        updateSelection() {
            const isMobile = window.innerWidth < 1024;
            const selector = isMobile ? '.check-item-mobile:checked' : '.check-item-desktop:checked';
            const checkboxes = document.querySelectorAll(selector);
            this.selectedItems = Array.from(checkboxes).map(cb => cb.value);
        },
        toggleAll(event) {
            const isMobile = window.innerWidth < 1024;
            const selector = isMobile ? '.check-item-mobile' : '.check-item-desktop';
            const checkboxes = document.querySelectorAll(selector);
            checkboxes.forEach(cb => cb.checked = event.target.checked);
            this.updateSelection();
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Import Section -->
            <div class="dashboard-card overflow-hidden">
                <div class="dashboard-card-header bg-teal-50 border-b border-teal-100 flex justify-between items-center">
                    <h3 class="dashboard-card-title text-teal-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        📥 Import Data Customer & SPK
                    </h3>
                    <div class="flex gap-2">
                        {{-- Reset button removed --}}
                    </div>
                </div>
                
                <div class="dashboard-card-body">
                    <form method="POST" action="{{ route('reception.import') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-8">
                        @csrf
                        
                        <!-- Left Side: Instructions -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <h4 class="font-bold text-gray-800">Petunjuk Import</h4>
                                <a href="{{ route('reception.template') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition-colors text-sm font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Download Template
                                </a>
                                <button type="button" onclick="openCreateOrderModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-sm font-medium rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Order Manual
                                </button>
                                <a href="{{ route('reception.export') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export Excel
                                </a>
                            </div>
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            Pastikan file Excel (.xlsx) Anda mengikuti format template yang telah ditentukan untuk menghindari error saat import.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-semibold text-gray-700">Langkah-langkah:</label>
                                <ul class="text-sm text-gray-600 space-y-2 list-disc list-inside ml-2">
                                    <li>Siapkan file Excel data order.</li>
                                    <li>Upload pada kolom di samping kanan.</li>
                                    <li>Klik tombol <strong>Import Database</strong>.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Right Side: File Input -->
                        <div class="flex flex-col justify-center" x-data="{ fileName: null }">
                            <label for="file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-teal-300 rounded-lg cursor-pointer bg-teal-50 hover:bg-teal-100 transition-all duration-300 group" :class="{'bg-teal-100 border-teal-500': fileName}">
                                
                                <!-- Default State -->
                                <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="!fileName">
                                    <svg class="w-10 h-10 mb-3 text-teal-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold text-teal-600">Klik untuk upload</span> atau drag and drop</p>
                                    <p class="text-xs text-gray-400">XLSX, XLS (MAX. 10MB)</p>
                                </div>

                                <!-- File Selected State -->
                                <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="fileName" style="display: none;">
                                    <svg class="w-10 h-10 mb-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="mb-1 text-sm font-bold text-teal-700">File Terpilih:</p>
                                    <p class="text-sm text-gray-700 bg-white px-2 py-1 rounded shadow-sm border border-gray-200" x-text="fileName"></p>
                                    <p class="mt-2 text-xs text-blue-500 hover:underline">Klik untuk ganti file</p>
                                </div>

                                <input id="file" name="file" type="file" class="hidden" required @change="fileName = $event.target.files[0] ? $event.target.files[0].name : null" />
                            </label>
                            <x-input-error class="mt-2 text-center" :messages="$errors->get('file')" />
                            
                            <div class="mt-4 flex justify-end">
                                <x-primary-button class="bg-gradient-to-r from-teal-500 to-teal-700 hover:from-teal-600 hover:to-teal-800 shadow-lg transform hover:-translate-y-0.5 transition-all" ::disabled="!fileName" ::class="{'opacity-50 cursor-not-allowed': !fileName}">
                                    {{ __('Import Database') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>

                    @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" x-transition class="mt-4 p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                            <span class="font-medium">Berhasil!</span> {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mt-4 p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                            <span class="font-medium">Error!</span> {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- List of Received Orders -->
            <div class="dashboard-card" x-data="{ activeTab: '{{ session('activeTab', 'pending') }}' }" x-on:switch-tab.window="activeTab = $event.detail">
                <div class="dashboard-card-header bg-teal-50 border-b border-teal-100 flex flex-col md:flex-row justify-between md:items-center gap-3">
                    <div class="flex items-center gap-4">
                        <h3 class="dashboard-card-title text-teal-800 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                            📦 Data Penerimaan
                        </h3>
                        
                        {{-- Tabs --}}
                        <div class="flex bg-white/50 p-1 rounded-lg border border-teal-100">
                            <button @click="$dispatch('switch-tab', 'pending')" 
                                    :class="{ 'bg-white shadow-sm text-teal-700': activeTab === 'pending', 'text-gray-500 hover:text-teal-600': activeTab !== 'pending' }"
                                    class="px-3 py-1.5 rounded-md text-xs font-bold transition-all flex items-center gap-2">
                                SPK Masuk (Pending)
                                <span class="bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full text-[10px]">{{ $pendingOrders->count() }}</span>
                            </button>
                            <button @click="$dispatch('switch-tab', 'received')"
                                    :class="{ 'bg-white shadow-sm text-teal-700': activeTab === 'received', 'text-gray-500 hover:text-teal-600': activeTab !== 'received' }"
                                    class="px-3 py-1.5 rounded-md text-xs font-bold transition-all flex items-center gap-2">
                                Diterima (Warehouse)
                                <span class="bg-green-100 text-green-600 px-1.5 py-0.5 rounded-full text-[10px]">{{ $orders->total() }}</span>
                            </button>
                            <button @click="$dispatch('switch-tab', 'processed')"
                                    :class="{ 'bg-white shadow-sm text-teal-700': activeTab === 'processed', 'text-gray-500 hover:text-teal-600': activeTab !== 'processed' }"
                                    class="px-3 py-1.5 rounded-md text-xs font-bold transition-all flex items-center gap-2">
                                Sudah Diproses
                                <span class="bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full text-[10px]">{{ $processedOrders->count() }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 items-center">
                        <span class="px-3 py-1 bg-white text-teal-700 rounded-full text-xs font-bold border border-teal-200 shadow-sm">
                            Total: {{ $orders->total() }} Pcs
                        </span>
                        
                        <!-- Bulk Delete Button (Hidden by default) -->
                        <button id="btn-bulk-delete" type="button" 
                            class="hidden px-3 py-1.5 bg-red-100 text-red-600 hover:bg-red-200 rounded-lg text-xs font-bold transition-colors flex items-center gap-1"
                            onclick="submitBulkDelete()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus Terpilih (<span id="count-selected">0</span>)
                        </button>
                    </div>
                </div>

                {{-- Tab Content Wrapper --}}
                <div>
                    
                    {{-- TAB 1: PENDING SPK (From CS) --}}
                    <div x-show="activeTab === 'pending'" x-transition class="p-6">
                        @if($pendingOrders->isEmpty())
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada SPK Masuk</h3>
                                <p class="mt-1 text-sm text-gray-500">Belum ada data SPK Pending dari CS.</p>
                            </div>
                        @else
                            <div class="block lg:hidden space-y-4">
                                @foreach($pendingOrders as $order)
                                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <div class="font-bold text-indigo-600 text-sm">{{ $order->spk_number }}</div>
                                            <div class="text-xs text-gray-400">{{ $order->created_at->format('d M Y H:i') }}</div>
                                        </div>
                                        @php
                                            $parts = explode('-', $order->spk_number);
                                            $csCode = end($parts);
                                            if(strlen($csCode) > 3) $csCode = $order->creator->cs_code ?? 'XX';
                                        @endphp
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">{{ $csCode }}</span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="font-bold text-gray-900">{{ $order->customer_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                                    </div>

                                    <div class="bg-gray-50 p-3 rounded-lg mb-4 border border-gray-100">
                                        <div class="text-sm font-medium text-gray-800">{{ $order->shoe_brand }} {{ $order->shoe_size }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->shoe_color }} - {{ $order->category }}</div>
                                    </div>

                                    <button type="button" onclick="confirmReceive('{{ $order->id }}', '{{ $order->spk_number }}')" 
                                        class="w-full py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 text-sm font-bold transition-all shadow-sm flex justify-center items-center gap-2">
                                        <span>Terima Barang</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>

                            <div class="hidden lg:block overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                                        <tr>
                                            <th class="px-4 py-3">SPK / Tanggal</th>
                                            <th class="px-4 py-3">Customer</th>
                                            <th class="px-4 py-3">Sepatu / Item</th>
                                            <th class="px-4 py-3">CS</th>
                                            <th class="px-4 py-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($pendingOrders as $order)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3">
                                                <div class="font-bold text-indigo-600">{{ $order->spk_number }}</div>
                                                <div class="text-xs text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900">{{ $order->customer_name }}</div>
                                                <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-gray-800">{{ $order->shoe_brand }} {{ $order->shoe_size }}</div>
                                                <div class="text-xs text-gray-500">{{ $order->shoe_color }} - {{ $order->category }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                @php
                                                    $parts = explode('-', $order->spk_number);
                                                    $csCode = end($parts);
                                                    // Fallback check if it looks like a code (2-3 chars)
                                                    if(strlen($csCode) > 3) $csCode = $order->creator->cs_code ?? 'XX';
                                                @endphp
                                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">{{ $csCode }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" onclick="confirmReceive('{{ $order->id }}', '{{ $order->spk_number }}')" 
                                                    class="inline-block px-3 py-1 bg-teal-600 text-white rounded hover:bg-teal-700 text-xs font-bold transition-all shadow-sm">
                                                    Terima Barang →
                                                </button>
                                                <form id="receive-{{ $order->id }}" action="{{ route('reception.receive', $order->id) }}" method="POST" class="hidden">
                                                    @csrf
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- TAB 2: RECEIVED ORDERS (Existing) --}}
                    <div x-show="activeTab === 'received'" x-transition>
                        
                {{-- Filter Section --}}
                <div class="dashboard-card-body border-b border-gray-200">
                    <form method="GET" action="{{ route('reception.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                        {{-- Search --}}
                        <div class="sm:col-span-2 md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Cari (SPK / Nama / No. WA)</label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Ketik untuk mencari..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm">
                        </div>

                        {{-- Date From --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Masuk Dari</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm">
                        </div>

                        {{-- Date To --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Sampai</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm">
                        </div>

                        {{-- Priority Filter --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Prioritas</label>
                            <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm">
                                <option value="">Semua Prioritas</option>
                                <option value="Reguler" {{ request('priority') == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                                <option value="Prioritas" {{ request('priority') == 'Prioritas' ? 'selected' : '' }}>Prioritas</option>
                            </select>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="sm:col-span-2 md:col-span-4 flex flex-col sm:flex-row gap-2 justify-end">
                            <a href="{{ route('reception.index') }}" 
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Reset
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all text-sm font-bold flex items-center justify-center gap-2 shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                Filter
                            </button>
                        </div>
                    </form>
                </div>

                <form id="bulk-delete-form" action="{{ route('reception.bulk-delete') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    {{-- Mobile Card View --}}
                    <div class="block lg:hidden space-y-4 px-4 sm:px-0 mb-4">
                        @forelse($orders as $order)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 relative overflow-hidden">
                            {{-- Selection & Priority header --}}
                            <div class="p-3 border-b border-gray-100 flex justify-between items-start bg-gray-50/50">
                                <div class="flex items-start gap-3">
                                    <div class="pt-1">
                                        <input type="checkbox" name="ids[]" value="{{ $order->id }}" class="check-item check-item-mobile w-5 h-5 rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50" @change="updateSelection()">
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 text-sm leading-tight mb-1">{{ $order->created_at->format('d M Y') }}</div>
                                        <div class="flex flex-wrap gap-1">
                                            @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 border border-red-200 uppercase">PRIORITAS</span>
                                            @else
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200 uppercase">REGULER</span>
                                            @endif
                                            
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-700 border border-teal-200">DITERIMA</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-black text-teal-600 font-mono text-sm tracking-tight">{{ $order->spk_number }}</div>
                                    <div class="text-[10px] text-gray-500">Est: {{ $order->estimation_date ? $order->estimation_date->format('d M') : '-' }}</div>
                                </div>
                            </div>

                            {{-- Main Content --}}
                            <div class="p-4 space-y-3">
                                {{-- Customer --}}
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-gray-900">{{ $order->customer_name }}</h4>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="text-xs text-green-600 hover:underline flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.6 1.967.3 3.945 1.511 6.085l-1.615 5.9 5.908-1.616zM18.312 14.5c-.266-.133-1.574-.776-1.817-.866-.234-.088-.352-.108-.501.121-.148.229-.588.751-.722.906-.134.156-.269.176-.534.043-.267-.133-1.127-.415-2.147-1.324-.795-.71-1.332-1.585-1.488-1.852-.155-.267-.016-.411.117-.544.119-.119.267-.311.4-.466.134-.155.177-.267.267-.445.089-.177.045-.333-.022-.467-.067-.133-.602-1.448-.824-1.983-.215-.515-.434-.445-.595-.453-.155-.008-.333-.008-.511-.008-.178 0-.467.067-.711.333-.244.267-.933.911-.933 2.222s.955 2.578 1.088 2.756c.133.178 1.881 2.871 4.557 4.026 2.676 1.155 2.676.769 3.167.724.488-.044 1.574-.643 1.797-1.264.221-.621.221-1.153.155-1.264-.067-.111-.244-.178-.511-.311zm-4.433 1.458z"/></svg>
                                            {{ $order->customer_phone }}
                                        </a>
                                    </div>
                                    {{-- Email Action --}}
                                    @if($order->customer_email)
                                        <button type="button" onclick="sendEmailNotification('{{ $order->id }}')" class="text-blue-500 hover:text-blue-700 bg-blue-50 p-1.5 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                        </button>
                                    @endif
                                </div>

                                {{-- Shoes --}}
                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-3">
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div>
                                            <span class="block text-[10px] text-gray-400">Brand</span>
                                            <span class="font-medium text-gray-800 break-words">{{ $order->shoe_brand ?? '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] text-gray-400">Warna / Size</span>
                                            <span class="font-medium text-gray-800 break-words">{{ $order->shoe_color ?? '-' }} / {{ $order->shoe_size ?? '-' }}</span>
                                        </div>
                                        <div class="col-span-2">
                                            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Jenis / Kategori</span>
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-100 text-xs">{{ $order->category ?? 'Belum Diatur' }}</span>
                                                <button type="button" onclick="openEditShoeModal('{{ $order->id }}', '{{ $order->shoe_brand }}', '{{ $order->shoe_size }}', '{{ $order->shoe_color }}', '{{ $order->category }}')" class="text-[10px] text-teal-600 font-bold flex items-center gap-1 border border-teal-200 px-2 py-1 rounded bg-white">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                    Edit Info
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-gray-200">
                                         @php
                                            $qcStatus = $order->warehouse_qc_status;
                                            if (!$qcStatus && !is_null($order->reception_qc_passed)) {
                                                $qcStatus = $order->reception_qc_passed ? 'lolos' : 'reject';
                                            }
                                            $tali = $order->accessories_tali ?? ($order->accessories_data['tali'] ?? null);
                                            $insole = $order->accessories_insole ?? ($order->accessories_data['insole'] ?? null);
                                            $box = $order->accessories_box ?? ($order->accessories_data['box'] ?? null);
                                        @endphp
                                        
                                        <div class="flex flex-wrap gap-2 items-center">
                                            @if($qcStatus == 'lolos')
                                                 <span class="text-[10px] font-bold text-teal-600 bg-teal-50 px-1.5 py-0.5 rounded border border-teal-100">QC OK</span>
                                            @elseif($qcStatus == 'reject')
                                                 <span class="text-[10px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-100">QC REJECT</span>
                                            @endif
                                            
                                            <div class="flex gap-1">
                                                <span class="text-[10px] px-1 rounded border {{ in_array($tali, ['Simpan','S','Nempel','N']) ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-gray-50 text-gray-400 border-gray-100' }}">Tali</span>
                                                <span class="text-[10px] px-1 rounded border {{ in_array($insole, ['Simpan','S','Nempel','N']) ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-gray-50 text-gray-400 border-gray-100' }}">Insol</span>
                                                <span class="text-[10px] px-1 rounded border {{ in_array($box, ['Simpan','S','Nempel','N']) ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-gray-50 text-gray-400 border-gray-100' }}">Box</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Actions --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ route('reception.show', $order->id) }}" class="col-span-2 flex items-center justify-center py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg font-bold text-sm shadow">
                                        Proses Order
                                    </a>
                                     <a href="{{ route('reception.print-spk', $order->id) }}" target="_blank" class="flex items-center justify-center py-2 bg-white text-gray-700 border border-gray-200 rounded-lg font-medium text-xs">
                                        Print SPK
                                    </a>
                                    <button type="button" onclick="openEditOrderModal({{ json_encode($order) }})" class="flex items-center justify-center py-2 bg-white text-teal-700 border border-teal-200 rounded-lg font-medium text-xs">
                                        Edit
                                    </button>
                                    <button type="button" x-data @click="$dispatch('open-photo-modal-{{ $order->id }}')" class="flex items-center justify-center py-2 bg-white text-gray-700 border border-gray-200 rounded-lg font-medium text-xs">
                                        Foto
                                    </button>
                                     <form action="{{ route('reception.skip-assessment', $order->id) }}" method="POST" class="col-span-2" onsubmit="return confirm('Langsung kirim ke Preparation (Skip Assessment)? Pastikan QC fisik sudah oke.')">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center justify-center py-2 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg font-bold text-xs hover:bg-indigo-100 transition-colors">
                                            Langsung ke Prep ⏩
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="text-center py-8 text-gray-500 bg-white rounded-xl border border-gray-100">
                                Belum ada data.
                            </div>
                        @endforelse
                    </div>

                    <div class="hidden lg:block overflow-x-auto -mx-4 sm:mx-0">
                        <table class="min-w-full w-full text-sm text-left text-gray-500">

                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th scope="col" class="px-6 py-4 w-[1%]">
                                        <input type="checkbox" @change="toggleAll($event)" :checked="selectedItems.length > 0 && selectedItems.length === document.querySelectorAll('.check-item').length" class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800">Info & Waktu</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800">Order & Customer</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800">Item Sepatu</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800">Data & QC</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800 text-center">Status</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($orders as $order)
                                    <tr class="bg-white hover:bg-teal-50/30 transition-colors duration-150">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="ids[]" value="{{ $order->id }}" class="check-item check-item-desktop rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50" @change="updateSelection()">
                                        </td>
                                        {{-- Info & Waktu --}}
                                        <td class="px-6 py-4 align-top">
                                            <div class="flex flex-col gap-1.5">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-gray-400 text-xs font-mono">#{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</span>
                                                    <div class="flex items-center gap-1.5 font-bold text-gray-700">
                                                        <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                        {{ $order->entry_date->format('d M Y') }}
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Est: {{ $order->estimation_date ? $order->estimation_date->format('d M Y') : '-' }}
                                                </div>

                                                @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                                    <span class="inline-flex items-center w-fit px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 border border-red-200 uppercase tracking-wide mt-1">
                                                        PRIORITAS
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center w-fit px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200 uppercase tracking-wide mt-1">
                                                        REGULER
                                                    </span>
                                                @endif
                                            </div>
                                        </td>


                                        {{-- Order & Customer --}}
                                        <td class="px-6 py-4 align-top">
                                            <div class="flex flex-col gap-1">
                                                <div class="font-mono font-black text-teal-600 text-sm tracking-tight mb-0.5">
                                                    {{ $order->spk_number }}
                                                </div>
                                                <div class="font-bold text-gray-900 leading-tight">
                                                    {{ $order->customer_name }}
                                                </div>
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="text-xs text-green-600 hover:text-green-800 flex items-center gap-1 mt-0.5 w-fit hover:underline">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.6 1.967.3 3.945 1.511 6.085l-1.615 5.9 5.908-1.616zM18.312 14.5c-.266-.133-1.574-.776-1.817-.866-.234-.088-.352-.108-.501.121-.148.229-.588.751-.722.906-.134.156-.269.176-.534.043-.267-.133-1.127-.415-2.147-1.324-.795-.71-1.332-1.585-1.488-1.852-.155-.267-.016-.411.117-.544.119-.119.267-.311.4-.466.134-.155.177-.267.267-.445.089-.177.045-.333-.022-.467-.067-.133-.602-1.448-.824-1.983-.215-.515-.434-.445-.595-.453-.155-.008-.333-.008-.511-.008-.178 0-.467.067-.711.333-.244.267-.933.911-.933 2.222s.955 2.578 1.088 2.756c.133.178 1.881 2.871 4.557 4.026 2.676 1.155 2.676.769 3.167.724.488-.044 1.574-.643 1.797-1.264.221-.621.221-1.153.155-1.264-.067-.111-.244-.178-.511-.311zm-4.433 1.458z"/></svg>
                                                    {{ $order->customer_phone }}
                                                </a>
                                                
                                                {{-- Email Status & Edit --}}
                                                <div class="email-container-{{ $order->id }}">
                                                    @if($order->customer_email)
                                                        <div class="flex items-center gap-1 text-xs text-blue-600">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                            <span class="truncate max-w-[150px]" title="{{ $order->customer_email }}">{{ $order->customer_email }}</span>
                                                            <button type="button" onclick="openEditEmailModal('{{ $order->id }}', '{{ $order->customer_email }}')"
 class="text-gray-400 hover:text-teal-600 transition-colors" title="Edit Email">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                            </button>
                                                        </div>
                                                    @else
                                                        <button type="button" onclick="openEditEmailModal('{{ $order->id }}', '')" class="flex items-center gap-1 text-xs text-gray-400 hover:text-teal-600 transition-colors w-fit">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                            Tambah Email
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        {{-- Item Sepatu --}}
                                        <td class="px-6 py-4 align-top">
                                            <div class="flex items-start gap-3">
                                                <div class="p-2.5 bg-orange-50 rounded-lg text-orange-500 shadow-sm border border-orange-100">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                </div>
                                                <div class="flex-1">
                                                    @if($order->shoe_brand && $order->shoe_size && $order->shoe_color)
                                                        <div class="font-bold text-gray-800 uppercase tracking-tight">{{ $order->shoe_brand }}</div>
                                                        <div class="text-[10px] text-gray-500 mt-0.5 font-bold">{{ $order->category ?? '-' }} | {{ $order->shoe_color }} | {{ $order->shoe_size }}</div>
                                                        
                                                        <div class="flex items-center gap-2 mt-2">
                                                            <button type="button" onclick="openEditShoeModal('{{ $order->id }}', '{{ $order->shoe_brand }}', '{{ $order->shoe_size }}', '{{ $order->shoe_color }}', '{{ $order->category }}')" class="text-[10px] text-teal-600 hover:text-teal-800 flex items-center gap-1 bg-teal-50 px-2 py-0.5 rounded border border-teal-100">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                                Edit
                                                            </button>
                                                            <button type="button" x-data @click="$dispatch('open-photo-modal-{{ $order->id }}')" class="text-[10px] text-gray-600 hover:text-gray-800 flex items-center gap-1 bg-gray-50 px-2 py-0.5 rounded border border-gray-200">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                                                Foto
                                                            </button>
                                                        </div>
                                                    @else
                                                        <button type="button" onclick="openEditShoeModal('{{ $order->id }}', '', '', '', '')" class="flex items-center gap-1 text-xs text-orange-600 hover:text-orange-800 transition-colors border border-dashed border-orange-300 px-2 py-1 rounded-lg bg-orange-50 hover:bg-orange-100">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                            Lengkapi Data Sepatu
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Kelengkapan & QC (New Column) --}}
                                        <td class="px-6 py-4 align-top">
                                            <div class="space-y-3">
                                                {{-- QC Status --}}
                                                <div>
                                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status Fisik</div>
                                                    @php
                                                        // Fallback Logic
                                                        $qcStatus = $order->warehouse_qc_status;
                                                        if (!$qcStatus && !is_null($order->reception_qc_passed)) {
                                                            $qcStatus = $order->reception_qc_passed ? 'lolos' : 'reject';
                                                        }
                                                        
                                                        // Notes Fallback
                                                        $qcNotes = $order->warehouse_qc_notes ?? $order->reception_rejection_reason;
                                                    @endphp

                                                    @if($qcStatus)
                                                        @if($qcStatus == 'lolos')
                                                            <span class="inline-flex w-full justify-center items-center gap-1.5 px-2 py-1 rounded bg-gradient-to-r from-emerald-50 to-teal-50 text-teal-700 border border-teal-100 shadow-sm">
                                                                <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                <span class="text-[10px] font-bold">QC PASSED</span>
                                                            </span>
                                                        @elseif($qcStatus == 'reject')
                                                            {{-- If Rejected but Status is NOT HOLD (meaning Approved by CX) --}}
                                                            @if($order->status != \App\Enums\WorkOrderStatus::HOLD_FOR_CX->value)
                                                                <span class="inline-flex w-full justify-center items-center gap-1.5 px-2 py-1 rounded bg-blue-50 text-blue-700 border border-blue-100 shadow-sm" title="Fisik Reject tapi disetujui Customer">
                                                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                    <span class="text-[10px] font-bold">ACC CUSTOMER</span>
                                                                </span>
                                                            @else
                                                                <span class="inline-flex w-full justify-center items-center gap-1.5 px-2 py-1 rounded bg-red-50 text-red-700 border border-red-100 shadow-sm cursor-help" title="Alasan: {{ $qcNotes }}">
                                                                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                    <span class="text-[10px] font-bold">REJECTED</span>
                                                                </span>
                                                            @endif
                                                        @endif
                                                    @else
                                                        <span class="text-xs text-center block text-gray-400 italic">Belum QC</span>
                                                    @endif
                                                </div>

                                                {{-- Accessories Checklist --}}
                                                <div>
                                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Kelengkapan</div>
                                                    @php
                                                        // Fallback Logic for Accessories
                                                        $tali = $order->accessories_tali ?? ($order->accessories_data['tali'] ?? null);
                                                        $insole = $order->accessories_insole ?? ($order->accessories_data['insole'] ?? null);
                                                        $box = $order->accessories_box ?? ($order->accessories_data['box'] ?? null);
                                                        
                                                        // Helper to normalize values
                                                        $isSimpan = fn($v) => in_array($v, ['Simpan', 'S']);
                                                        $isNempel = fn($v) => in_array($v, ['Nempel', 'N']);
                                                        $isTidakAda = fn($v) => in_array($v, ['Tidak Ada', 'T', null]);
                                                    @endphp

                                                    @if($tali || $insole || $box)
                                                        <div class="grid grid-cols-1 gap-1">
                                                            {{-- Tali --}}
                                                            <div class="flex items-center justify-between px-2 py-1 rounded border text-[10px] 
                                                                {{ $isTidakAda($tali) ? 'bg-gray-50 border-gray-100 text-gray-400' : 'bg-white border-gray-200 text-gray-600' }}">
                                                                <span class="{{ $isTidakAda($tali) ? 'opacity-70' : 'font-semibold' }}">Tali</span>
                                                                
                                                                @if($isSimpan($tali))
                                                                    <span class="w-5 h-5 flex items-center justify-center rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold" title="Disimpan">S</span>
                                                                @elseif($isNempel($tali))
                                                                    <span class="w-5 h-5 flex items-center justify-center rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold" title="Nempel">N</span>
                                                                @else
                                                                    <span class="w-5 h-5 flex items-center justify-center rounded-full bg-gray-200 text-gray-500 text-[10px] font-bold" title="Tidak Ada">T</span>
                                                                @endif
                                                            </div>

                                                            {{-- Insole --}}
                                                            <div class="flex items-center justify-between px-2 py-1 rounded border text-[10px] 
                                                                {{ $isTidakAda($insole) ? 'bg-gray-50 border-gray-100 text-gray-400' : 'bg-white border-gray-200 text-gray-600' }}">
                                                                <span class="{{ $isTidakAda($insole) ? 'opacity-70' : 'font-semibold' }}">Insol</span>
                                                                
                                                                @if($isSimpan($insole))
                                                                    <span class="w-5 h-5 flex items-center justify-center rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold" title="Disimpan">S</span>
                                                                @elseif($isNempel($insole))
                                                                    <span class="w-5 h-5 flex items-center justify-center rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold" title="Nempel">N</span>
                                                                @else
                                                                    <span class="w-5 h-5 flex items-center justify-center rounded-full bg-gray-200 text-gray-500 text-[10px] font-bold" title="Tidak Ada">T</span>
                                                                @endif
                                                            </div>

                                                            {{-- Box --}}
                                                            <div class="flex items-center justify-between px-2 py-1 rounded border text-[10px] 
                                                                {{ $isTidakAda($box) ? 'bg-gray-50 border-gray-100 text-gray-400' : 'bg-white border-gray-200 text-gray-600' }}">
                                                                <span class="{{ $isTidakAda($box) ? 'opacity-70' : 'font-semibold' }}">Box</span>
                                                                
                                                                @if($isSimpan($box))
                                                                    <span class="w-5 h-5 flex items-center justify-center rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold" title="Disimpan">S</span>
                                                                @elseif($isNempel($box))
                                                                    <span class="w-5 h-5 flex items-center justify-center rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold" title="Nempel">N</span>
                                                                @else
                                                                    <span class="w-5 h-5 flex items-center justify-center rounded-full bg-gray-200 text-gray-500 text-[10px] font-bold" title="Tidak Ada">T</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        
                                                        @if(!empty($order->accessories_data['lainnya']) || $order->accessories_other)
                                                            <div class="mt-1.5 px-2 py-1 bg-yellow-50 text-yellow-800 text-[10px] rounded border border-yellow-100 italic truncate" title="{{ $order->accessories_other ?? $order->accessories_data['lainnya'] }}">
                                                                + {{ $order->accessories_other ?? $order->accessories_data['lainnya'] }}
                                                            </div>
                                                        @endif
                                                    @else
                                                        <span class="text-xs text-center block text-gray-400 italic">Belum diinput</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                             <span class="status-badge teal">
                                                DITERIMA
                                            </span>
                                        </td>
                                        {{-- Aksi --}}
                                        <td class="px-6 py-4 text-right align-top">
                                            <div class="flex flex-col items-end gap-2">
                                                <div class="flex flex-col gap-2 w-full lg:w-auto">
                                                    <a href="{{ route('reception.show', $order->id) }}" 
                                                        class="flex items-center justify-center w-full px-3 py-1.5 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 shadow-md hover:shadow-lg transition-all text-xs font-bold uppercase tracking-wider group">
                                                        <span>Proses (Form QC)</span>
                                                        <svg class="w-3 h-3 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                                    </a>
                                                    
                                                     <form action="{{ route('reception.skip-assessment', $order->id) }}" method="POST" class="w-full" onsubmit="return confirm('Langsung kirim ke Preparation (Skip Assessment)? Pastikan QC fisik sudah oke.')">
                                                        @csrf
                                                        <button type="submit" class="flex items-center justify-center w-full px-3 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-all text-xs font-bold uppercase tracking-wider group" title="Langsung ke Preparation">
                                                            <span>To Prep ⏩</span>
                                                        </button>
                                                    </form>
                                                    
                                                    <a href="{{ route('reception.print-spk', $order->id) }}" target="_blank"
                                                        class="flex items-center justify-center w-full px-3 py-1.5 bg-white text-teal-700 border border-teal-200 rounded-lg hover:bg-teal-50 shadow-sm transition-all text-xs font-bold uppercase tracking-wider group">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                        <span>Print SPK</span>
                                                    </a>
                                                </div>

                                                <div class="flex items-center gap-1">
                                                    <a href="{{ route('reception.print-tag', $order->id) }}" target="_blank"
                                                       class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" title="Print Tag">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                        </svg>
                                                    </a>
                                                    
                                                    @if($order->customer_email)
                                                        <button type="button" onclick="sendEmailNotification('{{ $order->id }}')" class="p-2 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors inline-block" title="Kirim Nota Digital via Email">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                            </svg>
                                                        </button>
                                                    @else
                                                        <button type="button" onclick="Swal.fire('Email Tidak Ada', 'Silakan tambahkan email customer terlebih dahulu.', 'warning')" class="p-2 text-gray-300 cursor-not-allowed rounded-lg" title="Email customer belum tersedia" disabled>
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                    
                                                    <button type="button" onclick="openEditOrderModal({{ json_encode($order) }})" class="p-2 text-teal-600 hover:text-teal-800 hover:bg-teal-50 rounded-lg transition-colors" title="Edit Data Order">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>

                                                    <button type="button" x-data @click="$dispatch('open-photo-modal-{{ $order->id }}')" class="p-2 text-gray-500 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-colors" title="Dokumentasi Foto">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    </button>

                                                    <form action="{{ route('reception.destroy', $order->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data order ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-2 text-red-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Order">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-2.132-1.859L4.764 7M16 17v-4m-4 4v-4m-4 4v-4m-6-6h14m2 0a2 2 0 002-2V7a2 2 0 00-2 2H3a2 2 0 00-2 2v.17c0 1.1.9 2 2 2h1M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"></path></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-12 text-center text-gray-500 bg-gray-50/30">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                                <p class="font-medium">Belum ada sepatu masuk hari ini.</p>
                                                <p class="text-sm">Silahkan import data SPK terlebih dahulu.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                {{-- Floating Bulk Action Bar --}}
                <div x-show="selectedItems.length > 0" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="translate-y-full opacity-0 scale-95"
                     x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="translate-y-0 opacity-100 scale-100"
                     x-transition:leave-end="translate-y-full opacity-0 scale-95"
                     class="fixed bottom-6 inset-x-0 z-[9999] flex justify-center px-4">
                    
                    <div class="bg-white/90 backdrop-blur-md border border-gray-200 shadow-2xl rounded-2xl p-4 w-full max-w-2xl flex items-center justify-between gap-4 ring-1 ring-black/5">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 bg-teal-100 px-3 py-1.5 rounded-lg text-teal-700">
                                <span class="text-xs font-bold uppercase tracking-wider">Terpilih</span>
                                <span class="bg-teal-600 text-white px-2 py-0.5 rounded-md font-bold text-sm" x-text="selectedItems.length"></span>
                            </div>
                            <button @click="selectedItems = []" type="button" class="text-xs font-bold text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
                                Batal
                            </button>
                        </div>

                        <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>

                        <button type="button" 
                                onclick="bulkDirectToPrep()" 
                                class="bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:shadow-indigo-200 transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            Langsung ke Prep (Massal)
                        </button>
                    </div>
                </div>

                <!-- Hidden process forms to avoid nesting -->
                @foreach($orders as $order)
                    <form id="process-{{ $order->id }}" action="{{ route('reception.process', $order->id) }}" method="POST" class="hidden">
                        @csrf
                    </form>

                    <form id="wa-{{ $order->id }}" action="{{ route('orders.whatsapp_send', $order->id) }}" method="POST" class="hidden" target="_blank">
                        @csrf
                        <input type="hidden" name="type" value="received">
                    </form>

                    <!-- Photo Modal -->
                    <div x-data="{ open: false }" @open-photo-modal-{{ $order->id }}.window="open = true">
                        <template x-teleport="body">
                            <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-show="open" style="display: none;">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

                                <div class="fixed inset-0 z-10 overflow-y-auto">
                                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg" @click.away="open = false">
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                        <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Dokumentasi Foto - {{ $order->spk_number }}</h3>
                                                        <div class="mt-2">
                                                            <p class="text-sm text-gray-500">Upload foto kondisi awal sepatu sebelum diproses.</p>
                                                            
                                                            <x-photo-uploader :order="$order" step="RECEIVING" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" @click="open = false">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                @endforeach

                <script>
                    // Function to Confirm Process
                    function confirmProcess(id) {
                        Swal.fire({
                            title: 'Kirim ke Assessment?',
                            text: "Pastikan data dan foto sepatu sudah sesuai.",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#0F766E', // Teal-700
                            cancelButtonColor: '#9CA3AF',
                            confirmButtonText: 'Ya, Proses!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('process-' + id).submit();
                            }
                        })
                    }

                    function confirmReceive(id, spk) {
                        Swal.fire({
                            title: 'Terima Barang?',
                            text: "SPK: " + spk + "\nPastikan fisik sepatu sudah ada di gudang.",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#0F766E',
                            cancelButtonColor: '#9CA3AF',
                            confirmButtonText: 'Ya, Terima!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('receive-' + id).submit();
                            }
                        })
                    }

                    function submitBulkDelete() {
                        const checkedBoxes = document.querySelectorAll('.check-item:checked');
                        
                        if (checkedBoxes.length === 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Tidak Ada Data Terpilih',
                                text: 'Silakan pilih data yang ingin dihapus terlebih dahulu.'
                            });
                            return;
                        }
                        
                        Swal.fire({
                            title: 'Hapus Data Terpilih?',
                            text: "Data yang dihapus tidak dapat dikembalikan!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#EF4444',
                            cancelButtonColor: '#9CA3AF',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('bulk-delete-form').submit();
                            }
                        })
                    }

                    document.addEventListener('DOMContentLoaded', function() {
                        const checkAll = document.getElementById('check-all');
                        const checkItems = document.querySelectorAll('.check-item');
                        const btnBulkDelete = document.getElementById('btn-bulk-delete');
                        const countSelected = document.getElementById('count-selected');

                        function updateBulkButton() {
                            const checkedCount = document.querySelectorAll('.check-item:checked').length;
                            countSelected.textContent = checkedCount;
                            if (checkedCount > 0) {
                                btnBulkDelete.classList.remove('hidden');
                            } else {
                                btnBulkDelete.classList.add('hidden');
                            }
                        }

                        if(checkAll) {
                            checkAll.addEventListener('change', function() {
                                checkItems.forEach(item => item.checked = this.checked);
                                updateBulkButton();
                            });
                        }

                        checkItems.forEach(item => {
                            item.addEventListener('change', updateBulkButton);
                        });
                    });
                </script>
                
                {{-- Pagination Info and Links --}}
                @if($orders->total() > 0)
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-xs text-gray-500">
                        Menampilkan <span class="font-semibold text-gray-700">{{ $orders->firstItem() }}</span> 
                        sampai <span class="font-semibold text-gray-700">{{ $orders->lastItem() }}</span> 
                        dari <span class="font-semibold text-gray-700">{{ $orders->total() }}</span> data
                    </div>
                    
                    <div class="flex justify-center">
                        {{ $orders->links() }}
                    </div>
                </div>
                @endif
                    </div> {{-- End Tab 2: Received --}}

                    {{-- TAB 3: PROCESSED ORDERS --}}
                    <div x-show="activeTab === 'processed'" x-transition class="p-6" x-data="{
                        search: '',
                        priorityFilter: '',
                        qcFilter: '',
                        isVisible(orderSpk, orderName, orderPhone, orderBrand, orderPriority, orderQc) {
                            const searchLower = this.search.toLowerCase();
                            const matchesSearch = orderSpk.toLowerCase().includes(searchLower) ||
                                                orderName.toLowerCase().includes(searchLower) ||
                                                orderPhone.toLowerCase().includes(searchLower) ||
                                                orderBrand.toLowerCase().includes(searchLower);
                            
                            const matchesPriority = this.priorityFilter === '' || orderPriority === this.priorityFilter;
                            
                            // Map QC status for easier filtering if needed, or exact match
                            // orderQc value is likely 'lolos' or something else
                            const matchesQc = this.qcFilter === '' || orderQc === this.qcFilter;

                            return matchesSearch && matchesPriority && matchesQc;
                        }
                    }">
                        {{-- Toolbar Filter & Search --}}
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6">
                            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                                {{-- Search Bar --}}
                                <div class="w-full md:w-96 relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-teal-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" x-model="search" placeholder="Cari SPK, Nama, atau Brand..." 
                                        class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm transition-all focus:bg-white"
                                    >
                                </div>

                                {{-- Filters --}}
                                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                        </div>
                                        <select x-model="priorityFilter" class="pl-9 pr-8 py-2.5 w-full sm:w-40 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 cursor-pointer hover:bg-white transition-colors">
                                            <option value="">Semua Prioritas</option>
                                            <option value="Reguler">Reguler</option>
                                            <option value="Prioritas">Prioritas</option>
                                            <option value="Urgent">Urgent</option>
                                            <option value="Express">Express</option>
                                        </select>
                                    </div>

                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <select x-model="qcFilter" class="pl-9 pr-8 py-2.5 w-full sm:w-40 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 cursor-pointer hover:bg-white transition-colors">
                                            <option value="">Semua Status QC</option>
                                            <option value="lolos">Lolos QC</option>
                                            <option value="reject">Tidak Lolos</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($processedOrders->isEmpty())
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada Order yang Diproses</h3>
                                <p class="mt-1 text-sm text-gray-500">Order yang sudah dilakukan QC Gudang akan muncul di sini.</p>
                            </div>
                        @else
                    {{-- Mobile Card View Processed --}}
                    <div class="block lg:hidden space-y-4 mb-4">
                            @foreach($processedOrders as $order)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4" 
                                x-show="isVisible('{{ $order->spk_number }}', '{{ addslashes($order->customer_name) }}', '{{ $order->customer_phone }}', '{{ addslashes($order->shoe_brand) }}', '{{ $order->priority }}', '{{ $order->warehouse_qc_status }}')">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <div class="font-bold text-teal-600 text-sm">{{ $order->spk_number }}</div>
                                        <div class="text-[10px] text-gray-500">{{ $order->warehouse_qc_at ? $order->warehouse_qc_at->format('d M Y H:i') : '-' }}</div>
                                    </div>
                                    <div>
                                        @if($order->status === \App\Enums\WorkOrderStatus::WAITING_PAYMENT->value)
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[9px] font-bold uppercase">Finance</span>
                                        @elseif($order->status === \App\Enums\WorkOrderStatus::CX_FOLLOWUP->value)
                                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-[9px] font-bold uppercase">Follow Up</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-[9px] font-bold uppercase">{{ $order->status }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <h4 class="font-bold text-gray-900">{{ $order->customer_name }}</h4>
                                    <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                                </div>

                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 mb-3 grid grid-cols-2 gap-2">
                                    <div>
                                        <span class="text-[10px] text-gray-400 block">Brand / Size</span>
                                        <span class="text-sm font-medium">{{ $order->shoe_brand }} ({{ $order->shoe_size }})</span>
                                        @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                            <span class="block text-[9px] text-red-600 font-bold uppercase mt-1">{{ $order->priority }}</span>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] text-gray-400 block">QC Status</span>
                                            @if($order->warehouse_qc_status === 'lolos')
                                            <span class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase border border-green-200">Lolos QC</span>
                                        @else
                                            <span class="inline-block px-2 py-1 bg-red-100 text-red-700 rounded text-[10px] font-bold uppercase border border-red-200">Tidak Lolos</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" 
                                            onclick='openDetailModal(@json($order), @json($order->services), @json($order->accessories_data))'
                                            class="flex items-center justify-center py-2 bg-gray-100 text-gray-700 rounded-lg font-bold text-xs border border-gray-200 hover:bg-gray-200">
                                        Lihat Detail
                                    </button>
                                    <a href="{{ route('reception.print-spk', $order->id) }}" target="_blank"
                                        class="flex items-center justify-center py-2 bg-teal-50 text-teal-700 rounded-lg font-bold text-xs border border-teal-100 hover:bg-teal-100">
                                        Print SPK
                                    </a>
                                </div>
                            </div>
                            @endforeach
                    </div>

                    <div class="hidden lg:block overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                                        <tr>
                                            <th class="px-4 py-3">SPK / Tgl Proses</th>
                                            <th class="px-4 py-3">Customer</th>
                                            <th class="px-4 py-3">Info Item</th>
                                            <th class="px-4 py-3">Status QC</th>
                                            <th class="px-4 py-3">Posisi Order</th>
                                            <th class="px-4 py-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($processedOrders as $order)
                                        <tr class="hover:bg-gray-50 transition-colors"
   x-show="isVisible('{{ $order->spk_number }}', '{{ addslashes($order->customer_name) }}', '{{ $order->customer_phone }}', '{{ addslashes($order->shoe_brand) }}', '{{ $order->priority }}', '{{ $order->warehouse_qc_status }}')">
                                            <td class="px-4 py-3">
                                                <div class="font-bold text-teal-600">{{ $order->spk_number }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $order->warehouse_qc_at ? $order->warehouse_qc_at->format('d M Y H:i') : '-' }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900">{{ $order->customer_name }}</div>
                                                <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="font-bold text-gray-800 text-sm">{{ $order->shoe_brand }} - {{ $order->shoe_color }}</span>
                                                    @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-600 border border-red-200 uppercase tracking-wide">
                                                            {{ $order->priority }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-600 mb-1.5 font-medium">Size: {{ $order->shoe_size }}</div>
                                                
                                                <div class="inline-flex items-center gap-1 text-teal-600 text-xs font-medium cursor-pointer" onclick='openDetailModal(@json($order), @json($order->services), @json($order->accessories_data))'>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    {{ $order->services->count() }} Layanan
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($order->warehouse_qc_status === 'lolos')
                                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase border border-green-200">Lolos QC</span>
                                                @else
                                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-[10px] font-bold uppercase border border-red-200">Tidak Lolos</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($order->status === \App\Enums\WorkOrderStatus::WAITING_PAYMENT->value)
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase border border-blue-200">Finance (Pembayaran)</span>
                                                @elseif($order->status === \App\Enums\WorkOrderStatus::CX_FOLLOWUP->value)
                                                    <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-[10px] font-bold uppercase border border-orange-200">CX (Follow Up)</span>
                                                @else
                                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-[10px] font-bold uppercase border border-gray-200">{{ $order->status }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button type="button" 
                                                            onclick='openDetailModal(@json($order), @json($order->services), @json($order->accessories_data))'
                                                            class="inline-block px-3 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-xs font-bold transition-all border border-gray-200">
                                                        Lihat Detail
                                                    </button>
                                                    <a href="{{ route('reception.print-spk', $order->id) }}" target="_blank"
                                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-teal-50 text-teal-700 rounded border border-teal-100 hover:bg-teal-100 transition-all text-xs font-bold uppercase tracking-wider">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                        Print SPK
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div> {{-- End Tab Content Wrapper --}}
            </div>

        </div>
        </div>
    </div>
    
    <script>
    function sendEmailNotification(id) {
        Swal.fire({
            title: 'Kirim Nota Email?',
            text: "Sistem akan mengirimkan nota digital ke email customer.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Kirim!',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/reception/${id}/send-email`, {
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

    

{{-- Edit Shoe Info Modal --}}
<div id="editShoeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Info Sepatu</h3>
            <form id="editShoeForm" onsubmit="updateShoeInfo(event)">
                <input type="hidden" id="editShoeOrderId" value="">
                
                <div class="mb-4">
                    <label for="editShoeBrand" class="block text-sm font-medium text-gray-700 mb-2">Brand Sepatu *</label>
                    <input type="text" id="editShoeBrand" name="shoe_brand" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                
                <div class="mb-4">
                    <label for="editShoeSize" class="block text-sm font-medium text-gray-700 mb-2">Ukuran *</label>
                    <input type="text" id="editShoeSize" name="shoe_size" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                
                <div class="mb-4">
                    <label for="editShoeColor" class="block text-sm font-medium text-gray-700 mb-2">Warna *</label>
                    <input type="text" id="editShoeColor" name="shoe_color" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div class="mb-4">
                    <label for="editShoeCategory" class="block text-sm font-medium text-gray-700 mb-2">Jenis / Kategori</label>
                    <input type="text" id="editShoeCategory" name="category"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                           placeholder="Contoh: Sneakers, Boots, dll">
                </div>
                
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="closeEditShoeModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all shadow-md">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditShoeModal(orderId, brand, size, color, category) {
    document.getElementById('editShoeOrderId').value = orderId;
    document.getElementById('editShoeBrand').value = brand;
    document.getElementById('editShoeSize').value = size;
    document.getElementById('editShoeColor').value = color;
    document.getElementById('editShoeCategory').value = category || '';
    document.getElementById('editShoeModal').classList.remove('hidden');
}

function closeEditShoeModal() {
    document.getElementById('editShoeModal').classList.add('hidden');
    document.getElementById('editShoeForm').reset();
}

function updateShoeInfo(event) {
    event.preventDefault();
    
    const orderId = document.getElementById('editShoeOrderId').value;
    const brand = document.getElementById('editShoeBrand').value;
    const size = document.getElementById('editShoeSize').value;
    const color = document.getElementById('editShoeColor').value;
    const category = document.getElementById('editShoeCategory').value;
    
    fetch(`/reception/${orderId}/update-shoe-info`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            shoe_brand: brand,
            shoe_size: size,
            shoe_color: color,
            category: category
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            closeEditShoeModal();
            setTimeout(() => location.reload(), 2000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan: ' + error.message
        });
    });
}

document.getElementById('editShoeModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditShoeModal();
    }
});
</script>


{{-- Create Order Modal --}}
<div id="createOrderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-md bg-white my-10">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Tambah Order Manual</h3>
                <button onclick="closeCreateOrderModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="createOrderForm" onsubmit="submitCreateOrder(event)" x-data="serviceSelector()">
                <div class="space-y-6">
                    {{-- Section 1: Data Customer --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-bold text-teal-800 mb-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-xs">1</span>
                            Data Customer
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Customer *</label>
                                <input type="text" name="customer_name" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">No. WhatsApp *</label>
                                <input type="text" name="customer_phone" required placeholder="08..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Email (Opsional)</label>
                                <input type="email" name="customer_email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                            </div>
                            <div class="col-span-1 md:col-span-2 space-y-3">
                                <label class="block text-xs font-bold text-teal-800 uppercase tracking-wider">Alamat Lengkap & Pengiriman</label>
                                
                                {{-- Jalan / Detail --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1">ALAMAT JALAN / DETAIL</label>
                                    <textarea name="customer_address" rows="2" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm" placeholder="Nama Jalan, No. Rumah, RT/RW, Patokan..."></textarea>
                                </div>
                                
                                {{-- Grid for City/Prov --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Provinsi</label>
                                        <select id="manual_select_province" onchange="handleManualProvinceChange(this)" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                                            <option value="">-- Pilih Provinsi --</option>
                                        </select>
                                        <input type="hidden" name="customer_province" id="manual_input_province">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Kota / Kabupaten</label>
                                        <select id="manual_select_city" onchange="handleManualCityChange(this)" disabled class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                                            <option value="">-- Pilih Kota --</option>
                                        </select>
                                        <input type="hidden" name="customer_city" id="manual_input_city">
                                    </div>
                                </div>
                                
                                {{-- Grid for District/Village/Zip --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Kecamatan</label>
                                        <select id="manual_select_district" onchange="handleManualDistrictChange(this)" disabled class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                                            <option value="">-- Pilih Kecamatan --</option>
                                        </select>
                                        <input type="hidden" name="customer_district" id="manual_input_district">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Kelurahan</label>
                                        <select id="manual_select_village" onchange="handleManualVillageChange(this)" disabled class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                                            <option value="">-- Pilih Kelurahan --</option>
                                        </select>
                                        <input type="hidden" name="customer_village" id="manual_input_village">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Kode Pos</label>
                                    <input type="text" name="customer_postal_code" placeholder="Kode Pos" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                                </div>
                                <p class="text-[10px] text-gray-500 italic mt-1">*Data alamat ini akan disimpan ke Master Customer untuk keperluan Ongkir di Finance nanti.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Data Order & SPK --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-bold text-teal-800 mb-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-xs">2</span>
                            Data Order
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 mb-1">No. SPK (Wajib Sesuai Nota Fisik) *</label>
                                <input type="text" name="spk_number" required placeholder="Contoh: SPK-2024-001"
                                    class="w-full px-3 py-2 border-2 border-teal-100 bg-white rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm font-mono font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Prioritas *</label>
                                <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <option value="Reguler">Reguler</option>
                                    <option value="Prioritas">Prioritas</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Masuk *</label>
                                <input type="date" name="entry_date" required value="{{ date('Y-m-d') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Estimasi Selesai *</label>
                                <input type="date" name="estimation_date" required value="{{ date('Y-m-d', strtotime('+3 days')) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-bold text-teal-800 mb-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-xs">3</span>
                            Identitas Sepatu
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Brand</label>
                                <input type="text" name="shoe_brand" required placeholder="Nike/Adidas..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Kategori</label>
                                <input type="text" name="category" placeholder="Sneakers/Boots..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Size</label>
                                <input type="text" name="shoe_size" required placeholder="42"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Color</label>
                                <input type="text" name="shoe_color" required placeholder="Red/Black..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Section 3B: Pilih Layanan (Dynamic Service Selection) --}}
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-bold text-indigo-800 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs">3.B</span>
                                Pilih Layanan
                            </h4>
                            <button type="button" @click="showServiceModal = true" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-bold text-xs shadow-sm transition-colors flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah
                            </button>
                        </div>
                        
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-if="selectedServices.length === 0">
                                        <tr>
                                            <td colspan="3" class="px-4 py-8 text-center text-gray-400 italic text-xs">
                                                Belum ada layanan. Klik "Tambah" untuk memilih.
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="(svc, index) in selectedServices" :key="index">
                                        <tr class="hover:bg-gray-50 group">
                                            <td class="px-3 py-2">
                                                <div class="font-bold text-xs text-gray-800" x-text="svc.name || svc.custom_name"></div>
                                                <div class="text-[10px] text-gray-500" x-show="svc.service_id === 'custom'">(Custom)</div>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    <template x-for="detail in svc.details">
                                                        <span class="bg-indigo-50 text-indigo-700 text-[9px] px-1.5 py-0.5 rounded border border-indigo-100" x-text="detail"></span>
                                                    </template>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-right">
                                                <div class="text-xs font-bold text-gray-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(svc.price)"></div>
                                            </td>
                                            <td class="px-2 py-2 text-center w-8">
                                                <button type="button" @click="removeService(index)" class="text-gray-400 hover:text-red-500 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                                
                                                {{-- Hidden Inputs for Submission --}}
                                                <input type="hidden" :name="`services[${index}][service_id]`" :value="svc.service_id">
                                                <input type="hidden" :name="`services[${index}][custom_name]`" :value="svc.custom_name || svc.name">
                                                <input type="hidden" :name="`services[${index}][category]`" :value="svc.category">
                                                <input type="hidden" :name="`services[${index}][price]`" :value="svc.price">
                                                {{-- Serialize Details as JSON Sting for easier hidden input handling in JS submission --}}
                                                <input type="hidden" :name="`services[${index}][details]`" :value="JSON.stringify(svc.details)">
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            
                            {{-- Total --}}
                            <div class="bg-gray-50 px-3 py-2 border-t border-gray-200 flex justify-between items-center" x-show="selectedServices.length > 0">
                                <span class="text-xs font-bold text-gray-600">Total</span>
                                <span class="text-sm font-black text-indigo-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(calculateTotal())"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Service Modal (Inside x-data scope) --}}
                    <div x-show="showServiceModal" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;" x-cloak>
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showServiceModal = false">
                                <div class="absolute inset-0 bg-gray-900 opacity-75 backdrop-blur-sm"></div>
                            </div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4 flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        </div>
                                        Tambah Layanan
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        {{-- Category Select --}}
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Kategori</label>
                                            <select x-model="serviceForm.category" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="">-- Pilih Kategori --</option>
                                                <option value="Custom">Custom / Manual</option>
                                                <template x-for="cat in uniqueCategories" :key="cat">
                                                    <option :value="cat" x-text="cat"></option>
                                                </template>
                                            </select>
                                        </div>

                                        {{-- Service Select --}}
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Layanan</label>
                                            <select x-model="serviceForm.service_id" @change="selectService()" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" :disabled="!serviceForm.category">
                                                <option value="">-- Pilih Layanan --</option>
                                                <template x-for="svc in filteredServices" :key="svc.id">
                                                    <option :value="svc.id" x-text="svc.name + ' (' + new Intl.NumberFormat('id-ID').format(svc.price) + ')'"></option>
                                                </template>
                                                <option value="custom">+ Input Manual (Custom)</option>
                                            </select>
                                        </div>

                                        {{-- Custom Name --}}
                                        <div x-show="serviceForm.service_id === 'custom'">
                                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Nama Layanan Custom</label>
                                            <input type="text" x-model="serviceForm.custom_name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: Repaint Khusus">
                                        </div>

                                        {{-- Price --}}
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Harga (Rp)</label>
                                            <input type="number" x-model="serviceForm.price" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono font-bold">
                                        </div>

                                        {{-- Details --}}
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Detail Tambahan (Opsional)</label>
                                            <div class="flex gap-2 mb-2">
                                                <input type="text" x-model="serviceForm.newDetail" @keydown.enter.prevent="addDetail()" class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: Jahit Sol, Extra Wangi">
                                                <button type="button" @click="addDetail()" class="px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 text-gray-700 font-bold border border-gray-300">+</button>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="(detail, idx) in serviceForm.details" :key="idx">
                                                    <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded-md text-xs border border-indigo-100 flex items-center gap-1 font-semibold">
                                                        <span x-text="detail"></span>
                                                        <button type="button" @click="removeDetail(idx)" class="text-indigo-400 hover:text-indigo-600 font-bold ml-1">&times;</button>
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                                    <button type="button" @click="saveService()" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Simpan
                                    </button>
                                    <button type="button" @click="showServiceModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 4: Catatan Order (Pindahan dari Section 5) --}}
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <h4 class="font-bold text-yellow-800 mb-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center text-xs">4</span>
                            Catatan Order
                        </h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Keluhan / Request Customer (CS) *</label>
                                <textarea name="notes" rows="3" required placeholder="Jelaskan detail keluhan atau permintaan khusus pelanggan di sini..."
                                    class="w-full px-3 py-2 border-2 border-yellow-100 rounded-lg focus:ring-yellow-500 focus:border-yellow-500 text-sm italic"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Instruksi Khusus Teknisi (Opsional)</label>
                                <textarea name="technician_notes" rows="2" placeholder="Pesan teknis untuk tim workshop (Misal: Hati-hati bahan suede...)"
                                    class="w-full px-3 py-2 border border-yellow-200 rounded-lg focus:ring-yellow-500 text-sm"></textarea>
                            </div>
                        </div>
                    </div>




                </div>
                
                <div class="flex gap-2 justify-end mt-6 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeCreateOrderModal()" 
                            class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all shadow-md font-bold text-sm">
                        Simpan & Lanjut Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCreateOrderModal() {
    document.getElementById('createOrderModal').classList.remove('hidden');
}

function closeCreateOrderModal() {
    document.getElementById('createOrderModal').classList.add('hidden');
    document.getElementById('createOrderForm').reset();
}

function submitCreateOrder(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    // Manual check for reception_qc_passed if not picked (though radio should handle it)
    // Send FormData directly so Laravel handles the array notation 'accessories_data[key]' automatically
    
    fetch('/reception/store', {
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
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            closeCreateOrderModal();
            setTimeout(() => location.reload(), 2000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Terjadi kesalahan'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan: ' + error.message
        });
    });
}

// Close modal when clicking outside
document.getElementById('createOrderModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeCreateOrderModal();
    }
});

function openEditOrderModal(order) {
    document.getElementById('edit_order_id').value = order.id;
    document.getElementById('edit_spk_number').value = order.spk_number;
    document.getElementById('edit_customer_name').value = order.customer_name;
    document.getElementById('edit_customer_phone').value = order.customer_phone;
    document.getElementById('edit_notes').value = order.notes || '';
    document.getElementById('edit_technician_notes').value = order.technician_notes || '';
    document.getElementById('edit_priority').value = order.priority;
    
    document.getElementById('editOrderModal').classList.remove('hidden');
}

function closeEditOrderModal() {
    document.getElementById('editOrderModal').classList.add('hidden');
    document.getElementById('editOrderForm').reset();
}

function submitEditOrder(event) {
    event.preventDefault();
    const orderId = document.getElementById('edit_order_id').value;
    const formData = new FormData(event.target);
    
    fetch(`/reception/${orderId}/update-order`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-HTTP-Method-Override': 'PATCH',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false });
            closeEditOrderModal();
            location.reload();
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
        }
    })
    .catch(error => {
        Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan sistem.' });
    });
}
</script>


{{-- Edit Email Modal --}}
    <div id="editEmailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Email Customer</h3>
                <form id="editEmailForm" onsubmit="updateEmail(event)">
                    <input type="hidden" id="editOrderId" value="">
                    <div class="mb-4">
                        <label for="editEmailInput" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="editEmailInput" name="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500" 
                               placeholder="customer@example.com">
                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika ingin menghapus email</p>
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button type="button" onclick="closeEditEmailModal()" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all shadow-md">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Order Modal --}}
    <div id="editOrderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[70]">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-xl bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2 border-b pb-3">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Data Order
                </h3>
                <form id="editOrderForm" onsubmit="submitEditOrder(event)" class="space-y-4">
                    <input type="hidden" id="edit_order_id">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">No. SPK</label>
                        <input type="text" id="edit_spk_number" name="spk_number" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Nama Customer</label>
                            <input type="text" id="edit_customer_name" name="customer_name" required class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">No. WhatsApp</label>
                            <input type="text" id="edit_customer_phone" name="customer_phone" required class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Prioritas</label>
                        <select id="edit_priority" name="priority" required class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="Reguler">Reguler</option>
                            <option value="Prioritas">Prioritas</option>
                            <option value="Urgent">Urgent</option>
                            <option value="Express">Express</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Keluhan Customer (CS)</label>
                        <textarea id="edit_notes" name="notes" rows="3" class="w-full rounded-lg border-gray-300 text-sm italic bg-blue-50/30"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Instruksi Teknisi</label>
                        <textarea id="edit_technician_notes" name="technician_notes" rows="2" class="w-full rounded-lg border-gray-300 text-sm bg-amber-50/30"></textarea>
                    </div>

                    <div class="flex gap-2 justify-end pt-4 border-t">
                        <button type="button" onclick="closeEditOrderModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-bold transition-colors">Batal</button>
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 font-bold shadow-md transition-all">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openEditEmailModal(orderId, currentEmail) {
        document.getElementById('editOrderId').value = orderId;
        document.getElementById('editEmailInput').value = currentEmail || '';
        document.getElementById('editEmailModal').classList.remove('hidden');
    }

    function closeEditEmailModal() {
        document.getElementById('editEmailModal').classList.add('hidden');
        document.getElementById('editEmailForm').reset();
    }

    function updateEmail(event) {
        event.preventDefault();
        
        const orderId = document.getElementById('editOrderId').value;
        const email = document.getElementById('editEmailInput').value;
        
        fetch(`/reception/${orderId}/update-email`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                closeEditEmailModal();
                setTimeout(() => location.reload(), 2000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan: ' + error.message
            });
        });
    }

    document.getElementById('editEmailModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditEmailModal();
        }
    });

    // Confirm and accept pending SPK
    function confirmOrder(orderId, spkNumber) {
        Swal.fire({
            title: 'Verifikasi SPK',
            html: `Terima SPK <strong>${spkNumber}</strong> ke Gudang?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d9488',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Terima',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form to confirm route
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/reception/${orderId}/confirm`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    // Check for Print SPK Session
    @if(session('print_spk_id'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Data Tersimpan!',
                text: "Apakah Anda ingin mencetak Tag SPK sekarang?",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '🖨️ Ya, Cetak SPK',
                cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open("{{ url('reception/print') }}/{{ session('print_spk_id') }}", '_blank');
                }
            });
        });
    @endif
    </script>

    <!-- Order Detail Modal -->
    <div id="orderDetailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[60]">
        <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-2xl rounded-xl bg-white my-10 animate-fade-in">
            <div class="flex justify-between items-center mb-6 pb-4 border-b">
                <div>
                    <h3 class="text-xl font-bold text-gray-900" id="detail_spk_number">Detail Order</h3>
                    <div class="flex flex-col sm:flex-row sm:gap-4 mt-1">
                        <p class="text-xs text-gray-500" id="detail_entry_date"></p>
                        <p class="text-xs text-orange-600 font-bold" id="detail_estimation_date"></p>
                    </div>
                </div>
                <button onclick="closeDetailModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="space-y-6">
                {{-- Customer Info --}}
                <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                    <h4 class="text-xs font-bold text-indigo-700 uppercase mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Informasi Pelanggan
                    </h4>
                    <div class="grid grid-cols-2 gap-y-3 text-sm">
                        <div>
                            <span class="block text-[10px] text-indigo-400 font-bold uppercase">Nama</span>
                            <span class="font-bold text-gray-800" id="detail_customer_name">-</span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-indigo-400 font-bold uppercase">No. WhatsApp</span>
                            <span class="font-bold text-gray-800" id="detail_customer_phone">-</span>
                        </div>
                        <div class="col-span-2">
                             <span class="block text-[10px] text-indigo-400 font-bold uppercase">Email</span>
                             <span class="font-bold text-gray-800" id="detail_customer_email">-</span>
                        </div>
                        <div class="col-span-2">
                            <span class="block text-[10px] text-indigo-400 font-bold uppercase">Alamat</span>
                            <span class="text-gray-700 leading-relaxed" id="detail_customer_address">-</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Item Data --}}
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <h4 class="text-xs font-bold text-gray-600 uppercase mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            Data Barang
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center py-1 border-b border-gray-100">
                                <span class="text-gray-500">Brand</span>
                                <span class="font-bold text-gray-800" id="detail_shoe_brand">-</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-gray-100">
                                <span class="text-gray-500">Kategori</span>
                                <span class="font-bold text-gray-800" id="detail_category">-</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-gray-100">
                                <span class="text-gray-500">Warna</span>
                                <span class="font-bold text-gray-800" id="detail_shoe_color">-</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-gray-100">
                                <span class="text-gray-500">Ukuran</span>
                                <span class="font-bold text-gray-800" id="detail_shoe_size">-</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-gray-500">Prioritas</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase text-white" id="detail_priority">-</span>
                            </div>
                        </div>
                    </div>

                    {{-- Accessories Checklist --}}
                    <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                        <h4 class="text-xs font-bold text-orange-700 uppercase mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Kelengkapan
                        </h4>
                        <div id="detail_accessories_list" class="grid grid-cols-2 gap-2 text-[10px]">
                            {{-- Populated by JS --}}
                        </div>
                    </div>
                </div>
                
                {{-- Notes --}}
                <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100">
                     <h4 class="text-xs font-bold text-yellow-700 uppercase mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Catatan Order
                    </h4>
                    <div class="space-y-3">
                        <div>
                            <span class="block text-[10px] text-yellow-600 font-bold uppercase">Keluhan / Request (CS)</span>
                            <p class="text-sm text-gray-800 bg-white p-2 rounded border border-yellow-200" id="detail_cs_notes">-</p>
                        </div>
                        <div>
                             <span class="block text-[10px] text-yellow-600 font-bold uppercase">Instruksi Teknisi</span>
                             <p class="text-sm text-gray-800 bg-white p-2 rounded border border-yellow-200" id="detail_technician_notes">-</p>
                        </div>
                    </div>
                </div>

                {{-- Service Data --}}
                <div class="bg-white p-4 rounded-xl border border-gray-200">
                    <h4 class="text-xs font-bold text-teal-700 uppercase mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Layanan Dipesan
                    </h4>
                    <div id="detail_services_list" class="space-y-2">
                        {{-- Populated by JS --}}
                    </div>
                    <div class="mt-4 pt-3 border-t flex justify-between items-center font-bold text-gray-900">
                        <span>Total Estimasi</span>
                        <span id="detail_total_price" class="text-teal-600">Rp 0</span>
                    </div>
                </div>

                @if(isset($order->warehouse_qc_status))
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-sm">
                    <span class="font-bold text-gray-700">Catatan QC:</span>
                    <p class="text-gray-600 italic mt-1" id="detail_qc_notes">-</p>
                </div>
                @endif
            </div>

            <div class="mt-8 pt-4 border-t text-right">
                <button onclick="closeDetailModal()" class="px-6 py-2.5 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-all font-bold shadow-md">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>

    <script>
    function openDetailModal(order, services, accessories) {
        // Basic Info
        document.getElementById('detail_spk_number').innerText = `SPK: ${order.spk_number}`;
        document.getElementById('detail_entry_date').innerText = `Masuk: ${new Date(order.entry_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}`;
        document.getElementById('detail_estimation_date').innerText = `Estimasi: ${new Date(order.estimation_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}`;
        
        // Customer Info
        document.getElementById('detail_customer_name').innerText = order.customer_name || '-';
        document.getElementById('detail_customer_phone').innerText = order.customer_phone || '-';
        document.getElementById('detail_customer_email').innerText = order.customer_email || '-';
        document.getElementById('detail_customer_address').innerText = order.customer_address || '-';
        
        // Item Data
        document.getElementById('detail_shoe_brand').innerText = order.shoe_brand || '-';
        document.getElementById('detail_category').innerText = order.category || '-';
        document.getElementById('detail_shoe_color').innerText = order.shoe_color || '-';
        document.getElementById('detail_shoe_size').innerText = order.shoe_size || '-';
        
        const priorityEl = document.getElementById('detail_priority');
        priorityEl.innerText = order.priority || 'NORMAL';
        priorityEl.className = 'px-2 py-0.5 rounded text-[10px] font-black uppercase text-white ' + 
            (order.priority === 'Prioritas' || order.priority === 'Urgent' ? 'bg-red-500' : 'bg-teal-500');
        
        // Accessories
        const accList = document.getElementById('detail_accessories_list');
        accList.innerHTML = '';
        const labels = {tali: 'Tali', insole: 'Insole', box: 'Box', lainnya: 'Lainnya'};
        for (const [key, label] of Object.entries(labels)) {
            const val = accessories ? (accessories[key] || 'T') : 'T';
            let statusText = 'Tidak Ada';
            let statusClass = 'bg-gray-200 text-gray-500';
            if (val === 'S') { statusText = 'Disimpan'; statusClass = 'bg-blue-100 text-blue-700'; }
            if (val === 'N') { statusText = 'Nempel'; statusClass = 'bg-orange-100 text-orange-700'; }
            
            accList.innerHTML += `
                <div class="flex items-center justify-between p-2 rounded bg-white border border-gray-100">
                    <span class="text-gray-500">${label}</span>
                    <span class="px-1.5 py-0.5 rounded font-black ${statusClass}">${val}</span>
                </div>
            `;
        }

        // Services
        const svcList = document.getElementById('detail_services_list');
        svcList.innerHTML = '';
        let total = 0;
        if (services && services.length > 0) {
            services.forEach(svc => {
                // Use pivot cost if available (for Custom Services), otherwise use standard service price
                let price = parseFloat(svc.price || 0);
                if(svc.pivot && svc.pivot.cost) {
                    price = parseFloat(svc.pivot.cost);
                }
                
                total += price;
                
                svcList.innerHTML += `
                    <div class="flex justify-between items-start text-sm p-2 bg-gray-50 rounded border border-gray-100">
                        <div>
                            <div class="font-bold text-gray-800">${svc.name === 'Custom Service' && svc.pivot && svc.pivot.custom_name ? svc.pivot.custom_name : svc.name}</div>
                            <div class="text-[10px] text-gray-400 uppercase">${svc.category || ''}</div>
                        </div>
                        <div class="font-bold text-gray-900">Rp ${new Intl.NumberFormat('id-ID').format(price)}</div>
                    </div>
                `;
            });
        } else {
            svcList.innerHTML = '<p class="text-xs text-center text-gray-400 italic">Tidak ada data layanan</p>';
        }
        document.getElementById('detail_total_price').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;

        // QC Notes
        const noteEl = document.getElementById('detail_qc_notes');
        if (noteEl) noteEl.innerText = order.warehouse_qc_notes || 'Tidak ada catatan';
        
        // Notes
        document.getElementById('detail_cs_notes').innerText = order.notes || '-';
        document.getElementById('detail_technician_notes').innerText = order.technician_notes || '-';
        
        // Show Modal
        document.getElementById('orderDetailModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('orderDetailModal').classList.add('hidden');
    }

    // Close on backdrop click
    document.getElementById('orderDetailModal').addEventListener('click', function(e) {
        if (e.target === this) closeDetailModal();
    });

    // Bulk Direct to Prep Function
    function bulkDirectToPrep() {
        let ids = [];
        
        // Try to get Alpine data
        try {
            const alpineEl = document.querySelector('[x-data]');
            if (alpineEl && alpineEl._x_dataStack) {
                ids = alpineEl._x_dataStack[0].selectedItems || [];
            }
        } catch (e) {
            console.error('Alpine access error:', e);
        }

        // Fallback: get from checkboxes directly
        if (!ids || ids.length === 0) {
            // Detect if mobile or desktop view is visible
            const isMobile = window.innerWidth < 1024; // lg breakpoint
            const selector = isMobile ? '.check-item-mobile:checked' : '.check-item-desktop:checked';
            const checkboxes = document.querySelectorAll(selector);
            ids = Array.from(checkboxes).map(cb => cb.value);
        }

        if (ids.length === 0) {
            Swal.fire('Peringatan', 'Pilih item terlebih dahulu.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Bulk Direct to Prep',
            text: `Langsung kirim ${ids.length} order ke Preparation (Skip Assessment)? Pastikan QC fisik sudah oke.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Kirim ke Prep!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route('reception.bulk-skip-assessment') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                });
            }
        });
    }

    // --- Manual Accessory Rack Logic ---
    document.addEventListener('DOMContentLoaded', function() {
        const accContainer = document.getElementById('manual_accessory_rack_container');
        const accSelect = document.getElementById('manual_accessory_rack_code');
        const accInputs = document.querySelectorAll('input[name^="accessories_data"]');

        function checkAccessoryStorage() {
            let hasStored = false;
            accInputs.forEach(input => {
                if (input.checked && input.value === 'S') {
                    hasStored = true;
                }
            });

            if (hasStored) {
                accContainer.classList.remove('hidden');
                accSelect.required = true;
            } else {
                accContainer.classList.add('hidden');
                accSelect.required = false;
                accSelect.value = '';
            }
        }

        accInputs.forEach(input => {
            input.addEventListener('change', checkAccessoryStorage);
        });
    });

    // --- Manual Order Regional Dropdown Logic (EMSifa API) ---
    const REGIONAL_API_BASE = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('manual_select_province')) {
            fetchManualProvinces();
        }
    });

    function fetchManualProvinces() {
        const select = document.getElementById('manual_select_province');
        if (!select) return;
        fetch(`${REGIONAL_API_BASE}/provinces.json`)
            .then(response => response.json())
            .then(data => {
                data.forEach(prov => {
                    const opt = document.createElement('option');
                    opt.value = prov.id;
                    opt.text = prov.name;
                    opt.dataset.name = prov.name;
                    select.appendChild(opt);
                });
            })
            .catch(err => console.error('Error fetching provinces:', err));
    }

    function handleManualProvinceChange(el) {
        const selectedOption = el.options[el.selectedIndex];
        const provId = el.value;
        const provName = selectedOption.dataset.name || '';
        document.getElementById('manual_input_province').value = provName;

        const citySelect = document.getElementById('manual_select_city');
        const distSelect = document.getElementById('manual_select_district');
        const villSelect = document.getElementById('manual_select_village');

        citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
        citySelect.disabled = true;
        distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        distSelect.disabled = true;
        villSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        villSelect.disabled = true;

        document.getElementById('manual_input_city').value = '';
        document.getElementById('manual_input_district').value = '';
        document.getElementById('manual_input_village').value = '';

        if (provId) {
            fetch(`${REGIONAL_API_BASE}/regencies/${provId}.json`)
                .then(response => response.json())
                .then(data => {
                    citySelect.disabled = false;
                    data.forEach(city => {
                        const opt = document.createElement('option');
                        opt.value = city.id;
                        opt.text = city.name;
                        opt.dataset.name = city.name;
                        citySelect.appendChild(opt);
                    });
                })
                .catch(err => console.error('Error fetching cities:', err));
        }
    }

    function handleManualCityChange(el) {
        const selectedOption = el.options[el.selectedIndex];
        const cityId = el.value;
        const cityName = selectedOption.dataset.name || '';
        document.getElementById('manual_input_city').value = cityName;

        const distSelect = document.getElementById('manual_select_district');
        const villSelect = document.getElementById('manual_select_village');

        distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        distSelect.disabled = true;
        villSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        villSelect.disabled = true;

        document.getElementById('manual_input_district').value = '';
        document.getElementById('manual_input_village').value = '';

        if (cityId) {
            fetch(`${REGIONAL_API_BASE}/districts/${cityId}.json`)
                .then(response => response.json())
                .then(data => {
                    distSelect.disabled = false;
                    data.forEach(dist => {
                        const opt = document.createElement('option');
                        opt.value = dist.id;
                        opt.text = dist.name;
                        opt.dataset.name = dist.name;
                        distSelect.appendChild(opt);
                    });
                })
                .catch(err => console.error('Error fetching districts:', err));
        }
    }

    function handleManualDistrictChange(el) {
        const selectedOption = el.options[el.selectedIndex];
        const distId = el.value;
        const distName = selectedOption.dataset.name || '';
        document.getElementById('manual_input_district').value = distName;

        const villSelect = document.getElementById('manual_select_village');
        villSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        villSelect.disabled = true;
        document.getElementById('manual_input_village').value = '';

        if (distId) {
            fetch(`${REGIONAL_API_BASE}/villages/${distId}.json`)
                .then(response => response.json())
                .then(data => {
                    villSelect.disabled = false;
                    data.forEach(vill => {
                        const opt = document.createElement('option');
                        opt.value = vill.id;
                        opt.text = vill.name;
                        opt.dataset.name = vill.name;
                        villSelect.appendChild(opt);
                    });
                })
                .catch(err => console.error('Error fetching villages:', err));
        }
    }

    function handleManualVillageChange(el) {
        const selectedOption = el.options[el.selectedIndex];
        const villName = selectedOption.dataset.name || '';
        document.getElementById('manual_input_village').value = villName;
    }

    // Alpine Component for Service Selection (Manual Order)
    function serviceSelector() {
        return {
            masterServices: @json($services),
            selectedServices: [],
            showServiceModal: false,
            serviceForm: {
                category: '',
                service_id: '',
                custom_name: '',
                price: 0,
                details: [],
                newDetail: ''
            },

            init() {
                // Initialize if needed
            },

            get uniqueCategories() {
                if (!Array.isArray(this.masterServices)) return [];
                return [...new Set(this.masterServices.map(s => s.category))].filter(Boolean);
            },
            
            get filteredServices() {
                if (!this.serviceForm.category) return [];
                return this.masterServices.filter(s => s.category === this.serviceForm.category);
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
                // Validation
                if (!this.serviceForm.category || !this.serviceForm.service_id) {
                    Swal.fire('Error', 'Harap pilih kategori dan layanan.', 'error');
                    return;
                }
                if (this.serviceForm.service_id === 'custom' && !this.serviceForm.custom_name) {
                    Swal.fire('Error', 'Harap isi nama layanan custom.', 'error');
                    return;
                }

                // Add to list
                this.selectedServices.push({
                    service_id: this.serviceForm.service_id,
                    name: this.serviceForm.service_id === 'custom' ? this.serviceForm.custom_name : (this.masterServices.find(s => s.id == this.serviceForm.service_id)?.name || this.serviceForm.custom_name),
                    custom_name: this.serviceForm.custom_name,
                    category: this.serviceForm.category,
                    price: parseInt(this.serviceForm.price) || 0,
                    details: [...this.serviceForm.details] // Clone array
                });

                // Reset Form
                this.serviceForm = {
                    category: '',
                    service_id: '',
                    custom_name: '',
                    price: 0,
                    details: [],
                    newDetail: ''
                };
                this.showServiceModal = false;
            },

            removeService(index) {
                this.selectedServices.splice(index, 1);
            },

            calculateTotal() {
                return this.selectedServices.reduce((sum, svc) => sum + (parseInt(svc.price) || 0), 0);
            }
        }
    }
    </script>
</x-app-layout>

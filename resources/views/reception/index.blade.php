<x-app-layout>
    <style>
        :root {
            --primary-green: #22AF85;
            --accent-yellow: #FFC232;
            --dark-gray: #1F2937;
            --light-gray: #F9FAFB;
        }
        .bg-primary-green { background-color: var(--primary-green); }
        .text-primary-green { color: var(--primary-green); }
        .border-emerald-glow { border-color: rgba(34, 175, 133, 0.2); }
        
        .premium-card {
            background: white;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            border-radius: 2rem;
        }
        .premium-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -10px rgba(34, 175, 133, 0.08);
        }
        .glass-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, #1a8a69 100%);
            position: relative;
        }
    </style>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-black text-2xl leading-tight tracking-tight text-white">
                    {{ __('Gudang Penerimaan') }}
                </h2>
                <div class="text-[10px] font-bold text-white/70 uppercase tracking-[0.2em] mt-0.5">
                    RECEPTION & QUALITY CONTROL CENTER
                </div>
            </div>
        </div>
    </x-slot>

    <div id="reception-main-container" class="py-12 bg-gray-50/50" x-data="{ 
        selectedItems: [],
        selectAllMode: false,
        totalRecords: {{ $orders->total() }},
        pageRecords: {{ $orders->count() }},
        
        updateSelection() {
            const isMobile = window.innerWidth < 1024;
            const selector = isMobile ? '.check-item-mobile:checked' : '.check-item-desktop:checked';
            const checkboxes = document.querySelectorAll(selector);
            this.selectedItems = Array.from(checkboxes).map(cb => cb.value);
            
            // If user manually unchecks, disable selectAllMode
            if (this.selectedItems.length < this.pageRecords) {
                this.selectAllMode = false;
            }
        },
        toggleAll(event) {
            const isMobile = window.innerWidth < 1024;
            const selector = isMobile ? '.check-item-mobile' : '.check-item-desktop';
            const checkboxes = document.querySelectorAll(selector);
            checkboxes.forEach(cb => cb.checked = event.target.checked);
            
            this.updateSelection();
            
            // Reset Select All Mode on toggle
            this.selectAllMode = false;
        },
        selectAllAcrossPages() {
            this.selectAllMode = true;
            // Visual check all checkboxes on current page too
            const isMobile = window.innerWidth < 1024;
            const selector = isMobile ? '.check-item-mobile' : '.check-item-desktop';
            document.querySelectorAll(selector).forEach(cb => cb.checked = true);
            this.updateSelection();
            // Force true again
            this.selectAllMode = true; 
        },
        clearSelection() {
            this.selectedItems = [];
            this.selectAllMode = false;
            const checkboxes = document.querySelectorAll('.check-item');
            checkboxes.forEach(cb => cb.checked = false);
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Import Section -->
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-6 border-b border-emerald-glow bg-emerald-50/30 flex justify-between items-center">
                    <h3 class="text-xl font-black text-gray-900 tracking-tighter flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-primary-green shadow-[0_0_10px_rgba(34,175,133,0.5)]"></span>
                        📥 IMPORT DATA CUSTOMER & SPK
                    </h3>
                    <div class="flex gap-2">
                        <a href="{{ route('reception.trash') }}" class="flex items-center gap-2 bg-gray-100 hover:bg-red-50 text-gray-600 hover:text-red-600 px-4 py-2 rounded-xl text-xs font-bold transition-all border border-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            Tempat Sampah
                        </a>
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
                                    <svg class="w-10 h-10 mb-3 text-primary-green/40 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-black text-primary-green uppercase tracking-tight">Klik untuk upload</span> atau drag and drop</p>
                                    <p class="text-[10px] font-bold text-gray-400">XLSX, XLS (MAX. 10MB)</p>
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
                                <button type="submit" class="px-8 py-4 bg-primary-green text-white rounded-2xl font-black text-xs hover:bg-emerald-600 transition-all shadow-xl shadow-emerald-100 uppercase tracking-widest disabled:opacity-50 disabled:cursor-not-allowed" ::disabled="!fileName">
                                    {{ __('Import Database') }}
                                </button>
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
            <div class="premium-card" x-data="{ activeTab: '{{ session('activeTab', 'pending') }}' }" x-on:switch-tab.window="activeTab = $event.detail">
                <div class="px-8 py-6 border-b border-emerald-glow bg-emerald-50/30 flex flex-col md:flex-row justify-between md:items-center gap-3">
                    <div class="flex items-center gap-4">
                        <h3 class="text-xl font-black text-gray-900 tracking-tighter flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-primary-green shadow-[0_0_10px_rgba(34,175,133,0.5)]"></span>
                            📦 DATA PENERIMAAN
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
                        @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
                            <form action="{{ route('reception.index') }}" method="GET" class="flex items-center gap-2">
                                <select name="handler_id" onchange="this.form.submit()" class="text-xs border-gray-300 rounded-lg focus:ring-teal-500 py-1.5 pr-8">
                                    <option value="">Semua Handler</option>
                                    @php
                                        $handlers = \App\Models\User::where('role', 'gudang')->get();
                                    @endphp
                                    @foreach($handlers as $h)
                                        <option value="{{ $h->id }}" {{ request('handler_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @endif

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
                                    <form id="receive-mobile-{{ $order->id }}" action="{{ route('reception.receive', $order->id) }}" method="POST" class="hidden">
                                        @csrf
                                        <input type="hidden" name="rack_code" class="selected-rack-input">
                                    </form>
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
                                                    <input type="hidden" name="rack_code" class="selected-rack-input">
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

                                {{-- Items --}}
                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-3">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 bg-orange-100 rounded-lg text-orange-600 shadow-sm border border-orange-200">
                                            @php
                                                $cat = strtolower($order->category ?? '');
                                            @endphp
                                            @if(str_contains($cat, 'tas') || str_contains($cat, 'bag') || str_contains($cat, 'dompet'))
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                            @elseif(str_contains($cat, 'topi') || str_contains($cat, 'head') || str_contains($cat, 'helm'))
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582"></path></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            @if($order->shoe_brand || $order->shoe_size)
                                                <div class="flex flex-col gap-0.5">
                                                    <div class="flex items-center gap-1.5">
                                                        <div class="font-black text-gray-900 uppercase tracking-tight text-sm">{{ $order->shoe_brand ?? 'NO BRAND' }}</div>
                                                        <span class="px-1.5 py-0.5 bg-green-50 text-green-600 text-[9px] font-extrabold rounded border border-green-200 uppercase">CS OK</span>
                                                    </div>
                                                    <div class="text-[10px] text-gray-500 font-bold">
                                                        {{ strtoupper($order->category ?? 'Item') }} | {{ $order->shoe_color ?? '-' }} | SIZE: {{ $order->shoe_size ?? '-' }}
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-center gap-2 mt-2">
                                                    <button type="button" onclick="openEditShoeModal('{{ $order->id }}', '{{ $order->shoe_brand }}', '{{ $order->shoe_size }}', '{{ $order->shoe_color }}', '{{ $order->category }}')" class="text-[10px] text-teal-600 font-bold hover:text-teal-800 flex items-center gap-1 bg-white border border-teal-200 px-2 py-1 rounded shadow-sm">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                        Ubah Detail
                                                    </button>
                                                </div>
                                            @else
                                                <button type="button" onclick="openEditShoeModal('{{ $order->id }}', '', '', '', '')" class="flex items-center gap-1 text-xs text-orange-600 hover:text-orange-800 transition-colors border border-dashed border-orange-300 px-2 py-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 font-bold">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                    Lengkapi Barang
                                                </button>
                                            @endif
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
                                        
                                        <div class="flex flex-col gap-1 items-end">
                                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Handler</div>
                                        <div class="flex items-center gap-1">
                                            <div class="w-4 h-4 rounded-full bg-teal-100 flex items-center justify-center text-[8px] text-teal-600 font-bold">
                                                {{ $order->warehouseQcBy ? substr($order->warehouseQcBy->name, 0, 1) : '?' }}
                                            </div>
                                            <span class="text-xs font-semibold text-gray-700">{{ $order->warehouseQcBy->name ?? 'Belum Ada' }}</span>
                                        </div>
                                    </div>

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
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800">Item Barang</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800">Data & QC</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800">Handler</th>
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
                                                    @php
                                                        $cat = strtolower($order->category ?? '');
                                                    @endphp
                                                    @if(str_contains($cat, 'tas') || str_contains($cat, 'bag') || str_contains($cat, 'dompet'))
                                                        {{-- Bag Icon --}}
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                    @elseif(str_contains($cat, 'topi') || str_contains($cat, 'head') || str_contains($cat, 'helm'))
                                                        {{-- Hat Icon --}}
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582"></path></svg>
                                                    @else
                                                        {{-- Shoe/Default Icon --}}
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                    @endif
                                                </div>
                                                <div class="flex-1">
                                                    @if($order->shoe_brand || $order->shoe_size)
                                                        <div class="flex flex-col gap-0.5">
                                                            <div class="flex items-center gap-1.5">
                                                                <div class="font-black text-gray-900 uppercase tracking-tight text-sm">{{ $order->shoe_brand ?? 'NO BRAND' }}</div>
                                                                <span class="px-1.5 py-0.5 bg-green-50 text-green-600 text-[9px] font-extrabold rounded border border-green-200 uppercase">CS OK</span>
                                                            </div>
                                                            <div class="text-[10px] text-gray-500 font-bold">
                                                                {{ strtoupper($order->category ?? 'Item') }} | {{ $order->shoe_color ?? '-' }} | SIZE: {{ $order->shoe_size ?? '-' }}
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="flex items-center gap-2 mt-2">
                                                            <button type="button" onclick="openEditShoeModal('{{ $order->id }}', '{{ $order->shoe_brand }}', '{{ $order->shoe_size }}', '{{ $order->shoe_color }}', '{{ $order->category }}')" class="text-[10px] text-teal-600 font-bold hover:text-teal-800 flex items-center gap-1 bg-white border border-teal-200 px-2 py-1 rounded shadow-sm">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                                Ubah Detail
                                                            </button>
                                                        </div>
                                                    @else
                                                        <button type="button" onclick="openEditShoeModal('{{ $order->id }}', '', '', '', '')" class="flex items-center gap-1 text-xs text-orange-600 hover:text-orange-800 transition-colors border border-dashed border-orange-300 px-2 py-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 font-bold">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                            Lengkapi Barang
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
                                                <p class="font-medium">Belum ada barang masuk hari ini.</p>
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
                     class="fixed bottom-6 inset-x-0 z-[9999] flex flex-col items-center gap-2 px-4">
                    
                    {{-- Select All Across Pages Banner --}}
                    <div x-show="!selectAllMode && selectedItems.length === pageRecords && totalRecords > pageRecords" 
                         class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg text-sm font-medium flex items-center gap-2 animate-bounce">
                        <span>Anda memilih {{ $orders->count() }} data di halaman ini.</span>
                        <button @click="selectAllAcrossPages()" class="underline font-bold hover:text-blue-100">
                            Pilih seluruh <span x-text="totalRecords"></span> data?
                        </button>
                    </div>

                    {{-- Main Action Bar --}}
                    <div class="bg-white/90 backdrop-blur-md border border-gray-200 shadow-2xl rounded-2xl p-4 w-full max-w-2xl flex flex-col sm:flex-row items-center justify-between gap-4 ring-1 ring-black/5">
                        <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-start">
                            <div class="flex items-center gap-2 bg-teal-100 px-3 py-1.5 rounded-lg text-teal-700">
                                <span class="text-xs font-bold uppercase tracking-wider">Terpilih</span>
                                <span class="bg-teal-600 text-white px-2 py-0.5 rounded-md font-bold text-sm" x-text="selectAllMode ? totalRecords : selectedItems.length"></span>
                            </div>
                            <button @click="clearSelection()" type="button" class="text-xs font-bold text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
                                Batal
                            </button>
                        </div>

                        <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            {{-- Bulk Delete --}}
                            <button type="button" 
                                    onclick="submitBulkDelete()" 
                                    class="flex-1 sm:flex-none bg-red-100 hover:bg-red-200 text-red-600 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest shadow-sm hover:shadow transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus
                            </button>

                            {{-- Bulk Prep --}}
                            <button type="button" 
                                    onclick="bulkDirectToPrep()" 
                                    class="flex-1 sm:flex-none bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:shadow-indigo-200 transition-all flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                To Prep
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Order Actions (Photos & Forms) --}}
                @foreach($orders as $order)
                    @include('reception.partials.order-actions', ['order' => $order])
                @endforeach
                
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

                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 mb-3 flex gap-3 items-center">
                                    <div class="p-2 bg-white rounded border border-gray-200 shadow-sm text-center min-w-[40px]">
                                        @php
                                            $cat = strtolower($order->category ?? '');
                                        @endphp
                                        @if(str_contains($cat, 'tas') || str_contains($cat, 'bag') || str_contains($cat, 'dompet'))
                                            <span class="text-lg">👜</span>
                                        @elseif(str_contains($cat, 'topi') || str_contains($cat, 'head') || str_contains($cat, 'helm'))
                                            <span class="text-lg">🧢</span>
                                        @else
                                            <span class="text-lg">👟</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 grid grid-cols-2 gap-2">
                                        <div>
                                            <span class="text-[10px] text-gray-400 block uppercase font-bold">Item & Size</span>
                                            <span class="text-xs font-black text-gray-800">{{ $order->shoe_brand ?? '-' }} ({{ $order->shoe_size ?? '-' }})</span>
                                            @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                                <span class="block text-[8px] text-red-600 font-bold uppercase mt-0.5">{{ $order->priority }}</span>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] text-gray-400 block uppercase font-bold">QC Status</span>
                                            @if($order->warehouse_qc_status === 'lolos')
                                                <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-[9px] font-bold uppercase border border-green-200">Lolos</span>
                                            @else
                                                <span class="inline-block px-2 py-0.5 bg-red-100 text-red-700 rounded text-[9px] font-bold uppercase border border-red-200">Reject</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" 
                                            onclick='openDetailModal(@json($order), @json($order->services), @json($order->accessories_data))'
                                            class="flex items-center justify-center py-2 bg-gray-100 text-gray-700 rounded-lg font-bold text-xs border border-gray-200 hover:bg-gray-200">
                                        Lihat Detail
                                    </button>
                                    <a href="{{ route('reception.print-spk', $order->id) }}" target="_blank"
                                        class="flex items-center justify-center py-2 bg-[#22AF85]/10 text-[#22AF85] rounded-lg font-bold text-xs border border-[#22AF85]/20 hover:bg-[#22AF85]/20">
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
                                                <div class="font-bold text-[#22AF85]">{{ $order->spk_number }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $order->warehouse_qc_at ? $order->warehouse_qc_at->format('d M Y H:i') : '-' }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900">{{ $order->customer_name }}</div>
                                                <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="p-1.5 bg-gray-50 rounded border border-gray-100 text-gray-400">
                                                        @php
                                                            $cat = strtolower($order->category ?? '');
                                                        @endphp
                                                        @if(str_contains($cat, 'tas') || str_contains($cat, 'bag') || str_contains($cat, 'dompet'))
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                        @elseif(str_contains($cat, 'topi') || str_contains($cat, 'head') || str_contains($cat, 'helm'))
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582"></path></svg>
                                                        @else
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <span class="font-bold text-gray-800 text-sm">{{ $order->shoe_brand ?? '-' }} - {{ $order->shoe_color ?? '-' }}</span>
                                                            @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-600 border border-red-200 uppercase tracking-wide">
                                                                    {{ $order->priority }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-xs text-gray-600 mb-1.5 font-medium">{{ strtoupper($order->category ?? 'Item') }} | Size: {{ $order->shoe_size ?? '-' }}</div>
                                                        
                                                        <div class="inline-flex items-center gap-1 text-[#22AF85] text-[10px] font-bold cursor-pointer hover:underline" onclick='openDetailModal(@json($order), @json($order->services), @json($order->accessories_data))'>
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            {{ $order->services->count() }} Layanan
                                                        </div>
                                                    </div>
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
                                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#22AF85]/10 text-[#22AF85] rounded border border-[#22AF85]/20 hover:bg-[#22AF85]/20 transition-all text-xs font-bold uppercase tracking-wider">
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
    @push('modals')
        @include('reception.partials.modals')
    @endpush

    @push('scripts')
        @include('reception.partials.scripts')
    @endpush
    
</x-app-layout>

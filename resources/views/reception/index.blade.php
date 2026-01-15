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

    <div class="py-12 bg-gray-50/50">
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
            <div class="dashboard-card">
                <div class="dashboard-card-header bg-teal-50 border-b border-teal-100 flex flex-col md:flex-row justify-between md:items-center gap-3">
                    <h3 class="dashboard-card-title text-teal-800 flex items-center gap-2">
                         <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                         📦 Sepatu Masuk (Diterima)
                    </h3>
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
                    <div class="overflow-x-auto -mx-4 sm:mx-0">
                        <table class="min-w-full w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th scope="col" class="px-6 py-4">
                                        <input type="checkbox" id="check-all" class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800">No</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800">Tanggal Masuk</th>
                                    {{-- Priority Header --}}
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800 text-center min-w-[120px]">Prioritas</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800 min-w-[150px]">SPK</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800 min-w-[200px]">Customer</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800 min-w-[250px]">Item</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800 text-center min-w-[150px]">Estimasi</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800 text-center min-w-[120px]">Status</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800 text-end min-w-[140px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($orders as $order)
                                    <tr class="bg-white hover:bg-teal-50/30 transition-colors duration-150">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="ids[]" value="{{ $order->id }}" class="check-item rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                        </td>
                                        <td class="px-6 py-4 font-bold text-gray-500">
                                            {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <span class="font-medium text-gray-700">{{ $order->entry_date->format('d M Y') }}</span>
                                            </div>
                                        </td>
                                        {{-- Priority Badge --}}
                                        <td class="px-6 py-4 text-center">
                                            @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200 shadow-sm">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.45-.412-1.725a1 1 0 00-1.426-.692l-.08.03c-.233.09-.38.31-.486.602-.15.412-.21 1.056.037 1.814.242.74.721 1.63 1.542 2.37.77.695 1.785 1.123 2.81 1.123 2.112 0 3.966-1.523 4.454-3.55.337-1.4.156-2.825-.36-4.013a7.618 7.618 0 00-1.332-1.897zM7.222 16.712a1 1 0 01-.176 1.397L6 19l2.768.923a1 1 0 01.633 1.265l-.3 1.002 2.924-.73-1.03-3.606-2.551-2.55a1 1 0 01-.844.757l-1.378.65z" clip-rule="evenodd" /></svg>
                                                    PRIORITAS
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                    REGULER
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 font-mono font-bold text-teal-600">
                                            {{ $order->spk_number }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1">
                                                <span class="font-bold text-gray-800">{{ $order->customer_name }}</span>
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
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="p-2 bg-orange-100 rounded-lg mr-3 text-orange-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                </div>
                                                <div>
                                                    @if($order->shoe_brand && $order->shoe_size && $order->shoe_color)
                                                    <div class="flex items-center gap-2">
                                                        <div>
                                                            <div class="text-sm font-bold text-gray-800">{{ $order->shoe_brand }}</div>
                                                            <div class="text-xs text-gray-500">{{ $order->shoe_color }} • {{ $order->shoe_size }}</div>
                                                        </div>
                                                        <button type="button" onclick="openEditShoeModal('{{ $order->id }}', '{{ $order->shoe_brand }}', '{{ $order->shoe_size }}', '{{ $order->shoe_color }}')" class="text-gray-400 hover:text-teal-600 transition-colors" title="Edit Info Sepatu">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                        </button>
                                                    </div>
                                                @else
                                                    <button type="button" onclick="openEditShoeModal('{{ $order->id }}', '', '', '')" class="flex items-center gap-1 text-xs text-gray-400 hover:text-teal-600 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                        Tambah Info Sepatu
                                                    </button>
                                                @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-md {{ \Carbon\Carbon::parse($order->estimation_date)->isPast() && $order->status !== 'SELESAI' ? 'bg-red-100 text-red-600 border border-red-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                               ⏱️ {{ $order->estimation_date->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                             <span class="status-badge teal">
                                                DITERIMA
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('reception.print-tag', $order->id) }}" target="_blank"
                                                   class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" title="Print Tag">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                </a>
                                                
                                                <!-- Manual WhatsApp Trigger -->
                                                {{-- SMTP Email Trigger - Only show if email exists --}}
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
                                                
                                                {{-- Test Cekat Template Trigger (DISABLED)
                                                <button type="button" onclick="document.getElementById('wa-template-{{ $order->id }}').submit()" class="p-2 text-purple-500 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition-colors" title="Test Konfirmasi via Cekat Template (API)">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                </button>
                                                --}}
                                                
                                                <!-- Photo Trigger -->
                                                <button type="button" x-data @click="$dispatch('open-photo-modal-{{ $order->id }}')" class="p-2 text-gray-500 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-colors" title="Dokumentasi Foto">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                </button>

                                                <!-- Process Form Trigger -->
                                                <button type="button" onclick="confirmProcess('{{ $order->id }}')" 
                                                    class="flex items-center px-3 py-1.5 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 shadow-md hover:shadow-lg transition-all text-xs font-bold uppercase tracking-wider group">
                                                    <span>Proses</span>
                                                    <svg class="w-3 h-3 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                                </button>
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
function openEditShoeModal(orderId, brand, size, color) {
    document.getElementById('editShoeOrderId').value = orderId;
    document.getElementById('editShoeBrand').value = brand;
    document.getElementById('editShoeSize').value = size;
    document.getElementById('editShoeColor').value = color;
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
            shoe_color: color
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
            
            <form id="createOrderForm" onsubmit="submitCreateOrder(event)">
                <div class="grid grid-cols-2 gap-4">
                    {{-- Customer Info --}}
                    <div class="col-span-2 border-b pb-2 mb-2">
                        <h4 class="font-semibold text-gray-700">Informasi Customer</h4>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Customer *</label>
                        <input type="text" name="customer_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp *</label>
                        <input type="text" name="customer_phone" required placeholder="08123456789"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email (Opsional)</label>
                        <input type="email" name="customer_email" placeholder="customer@example.com"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat (Opsional)</label>
                        <input type="text" name="customer_address"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    
                    {{-- Shoe Info --}}
                    <div class="col-span-2 border-b pb-2 mb-2 mt-2">
                        <h4 class="font-semibold text-gray-700">Informasi Sepatu</h4>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Brand Sepatu *</label>
                        <input type="text" name="shoe_brand" required placeholder="Nike, Adidas, dll"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran *</label>
                        <input type="text" name="shoe_size" required placeholder="42, 43, dll"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Warna *</label>
                        <input type="text" name="shoe_color" required placeholder="Hitam, Putih, dll"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    
                    {{-- Order Info --}}
                    <div class="col-span-2 border-b pb-2 mb-2 mt-2">
                        <h4 class="font-semibold text-gray-700">Informasi Order</h4>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. SPK (Opsional)</label>
                        <input type="text" name="spk_number" placeholder="Auto-generate jika kosong"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan untuk auto-generate</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas *</label>
                        <select name="priority" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="Reguler">Reguler</option>
                            <option value="Prioritas">Prioritas</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk *</label>
                        <input type="date" name="entry_date" required value="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Selesai *</label>
                        <input type="date" name="estimation_date" required value="{{ date('Y-m-d', strtotime('+3 days')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                </div>
                
                <div class="flex gap-2 justify-end mt-6">
                    <button type="button" onclick="closeCreateOrderModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all shadow-md">
                        Simpan Order
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
    const data = Object.fromEntries(formData.entries());
    
    fetch('/reception/store', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
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
    </script>

</x-app-layout>
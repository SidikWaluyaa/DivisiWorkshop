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
                    {{ \Carbon\Carbon::now()->format('l, d F Y') }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Import Section -->
            <div class="dashboard-card overflow-hidden">
                <div class="dashboard-card-header flex justify-between items-center">
                    <h3 class="dashboard-card-title">
                        📥 Import Data Customer & SPK
                    </h3>
                    <div class="flex gap-2">
                        {{-- Reset button removed --}}
                    </div>
                </div>
                
                <div class="dashboard-card-body">
                    <form method="POST" action="{{ route('reception.import') }}" enctype="multipart/form-data" class="grid lg:grid-cols-2 gap-8">
                        @csrf
                        
                        <!-- Left Side: Instructions -->
                        <div class="space-y-4">
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
                <div class="dashboard-card-header flex justify-between items-center">
                    <h3 class="dashboard-card-title">
                         📦 Sepatu Masuk (Diterima)
                    </h3>
                    <div class="flex gap-2 items-center">
                        <span class="px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-xs font-bold">
                            Total: {{ $orders->count() }} Pcs
                        </span>
                        
                        <!-- Bulk Delete Button (Hidden by default) -->
                        <button id="btn-bulk-delete" type="button" onclick="submitBulkDelete()" class="hidden px-3 py-1.5 bg-red-100 text-red-600 hover:bg-red-200 rounded-lg text-xs font-bold transition-colors flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus Terpilih (<span id="count-selected">0</span>)
                        </button>
                    </div>
                </div>

                <form id="bulk-delete-form" action="{{ route('reception.bulk-delete') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data yang dipilih?');">
                    @csrf
                    @method('DELETE')
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th scope="col" class="px-6 py-4">
                                        <input type="checkbox" id="check-all" class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800">Tanggal Masuk</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800">SPK</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800">Customer</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800">Item</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800 text-center">Estimasi</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800 text-center">Status</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-teal-800 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($orders as $order)
                                    <tr class="bg-white hover:bg-teal-50/30 transition-colors duration-150">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="ids[]" value="{{ $order->id }}" class="check-item rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <span class="font-medium text-gray-700">{{ $order->entry_date->format('d M Y') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-mono font-bold text-teal-600">
                                            {{ $order->spk_number }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-800">{{ $order->customer_name }}</span>
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="text-xs text-green-600 hover:text-green-800 flex items-center gap-1 mt-0.5 w-fit hover:underline">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.6 1.967.3 3.945 1.511 6.085l-1.615 5.9 5.908-1.616zM18.312 14.5c-.266-.133-1.574-.776-1.817-.866-.234-.088-.352-.108-.501.121-.148.229-.588.751-.722.906-.134.156-.269.176-.534.043-.267-.133-1.127-.415-2.147-1.324-.795-.71-1.332-1.585-1.488-1.852-.155-.267-.016-.411.117-.544.119-.119.267-.311.4-.466.134-.155.177-.267.267-.445.089-.177.045-.333-.022-.467-.067-.133-.602-1.448-.824-1.983-.215-.515-.434-.445-.595-.453-.155-.008-.333-.008-.511-.008-.178 0-.467.067-.711.333-.244.267-.933.911-.933 2.222s.955 2.578 1.088 2.756c.133.178 1.881 2.871 4.557 4.026 2.676 1.155 2.676.769 3.167.724.488-.044 1.574-.643 1.797-1.264.221-.621.221-1.153.155-1.264-.067-.111-.244-.178-.511-.311zm-4.433 1.458z"/></svg>
                                                    {{ $order->customer_phone }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="p-2 bg-orange-100 rounded-lg mr-3 text-orange-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-800">{{ $order->shoe_brand }}</div>
                                                    <div class="text-xs text-gray-500">{{ $order->shoe_color }} • {{ $order->shoe_size }}</div>
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
                                                
                                                <!-- Process Form Trigger (Since we cannot nest forms, we check outside) -->
                                                <button type="button" onclick="document.getElementById('process-{{ $order->id }}').submit()" class="flex items-center px-3 py-1.5 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 shadow-md hover:shadow-lg transition-all text-xs font-bold uppercase tracking-wider group">
                                                    <span>Proses</span>
                                                    <svg class="w-3 h-3 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center text-gray-500 bg-gray-50/30">
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
                    <form id="process-{{ $order->id }}" action="{{ route('reception.process', $order->id) }}" method="POST" onsubmit="return confirm('Kirim sepatu ini ke bagian Assessment?');" class="hidden">
                        @csrf
                    </form>
                @endforeach

                <script>
                    function submitBulkDelete() {
                        document.getElementById('bulk-delete-form').submit();
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
                
                @if($orders->count() > 0)
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 text-xs text-gray-500">
                    Menampilkan {{ $orders->count() }} data order terbaru.
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
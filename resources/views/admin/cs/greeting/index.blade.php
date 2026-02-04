<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Customer Service Greeting') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data chat masuk dan performa greeting awal CS.</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('cs.greeting.template') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="C4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Template
                </a>
                
                <button x-data @click="$dispatch('open-modal', 'import-modal')"
                        class="inline-flex items-center px-4 py-2 bg-crimson-red border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-red-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="C7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Import Chat
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="greetingManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('import_errors'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-xl shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 font-bold">Terjadi beberapa kesalahan saat import:</p>
                            <ul class="mt-1 text-sm text-red-600 list-disc list-inside">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Filter Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 mb-6">
                <form action="{{ route('cs.greeting.index') }}" method="GET" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Cari Nomor</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="812..." 
                                   class="w-full rounded-xl border-gray-200 focus:border-red-400 focus:ring-red-400 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">PIC Handle</label>
                            <select name="cs_id" class="w-full rounded-xl border-gray-200 focus:border-red-400 focus:ring-red-400 text-sm">
                                <option value="">Semua PIC</option>
                                @foreach($csUsers as $user)
                                    <option value="{{ $user->id }}" {{ request('cs_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" 
                                   class="w-full rounded-xl border-gray-200 focus:border-red-400 focus:ring-red-400 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" 
                                   class="w-full rounded-xl border-gray-200 focus:border-red-400 focus:ring-red-400 text-sm">
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between space-y-3 md:space-y-0">
                        <div class="flex items-center space-x-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Filter
                            </button>
                            <a href="{{ route('cs.greeting.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Reset
                            </a>
                        </div>

                        @if(request()->anyFilled(['search', 'cs_id', 'start_date', 'end_date']))
                            <button type="button" @click="confirmDeleteFiltered" 
                                    class="inline-flex items-center px-4 py-2 bg-red-50 border border-red-200 rounded-xl font-semibold text-xs text-red-600 uppercase tracking-widest hover:bg-red-100 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus Semua Hasil Filter ({{ $greetings->count() }})
                            </button>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-600">
                                    <th class="py-4 px-4 w-10">
                                        <input type="checkbox" 
                                               x-model="selectAll" 
                                               @change="toggleSelectAll"
                                               class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    </th>
                                    <th class="py-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal Chat</th>
                                    <th class="py-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Nomor Customer</th>
                                    <th class="py-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">PIC Handle</th>
                                    <th class="py-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="py-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @forelse($greetings as $greeting)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                                        <td class="py-4 px-4">
                                            <input type="checkbox" 
                                                   value="{{ $greeting->id }}" 
                                                   x-model="selectedIds"
                                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                    {{ $greeting->first_contact_at->format('d M Y') }}
                                                </span>
                                                <span class="text-xs text-gray-400">
                                                    {{ $greeting->first_contact_at->format('H:i') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-gray-600 dark:text-gray-400">
                                                {{ $greeting->customer_phone }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center">
                                                @if($greeting->cs)
                                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                                        <span class="text-red-600 font-bold text-xs">{{ substr($greeting->cs->name, 0, 1) }}</span>
                                                    </div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $greeting->cs->name }}
                                                    </div>
                                                @else
                                                    <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-bold uppercase tracking-wider border border-gray-200 shadow-sm animate-pulse">
                                                        Belum Diambil
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $greeting->status_badge_class }}">
                                                {{ $greeting->status }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center justify-end space-x-3">
                                                @if(!$greeting->cs_id)
                                                    <button @click="claimLead({{ $greeting->id }})" 
                                                            class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-green-100 transition duration-150">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                        </svg>
                                                        Ambil Data
                                                    </button>
                                                @else
                                                    <a href="{{ $greeting->wa_greeting_link }}" target="_blank" class="p-2 text-green-600 hover:bg-green-50 rounded-xl transition duration-150" title="Kirim WA">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                                </svg>
                                                <p class="font-bold text-gray-400">Belum ada data greeting.</p>
                                                <p class="text-xs">Silakan import data dari file Excel.</p>
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

        <!-- Floating Bulk Delete FAB -->
        <div x-show="selectedIds.length > 0" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-10"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-10"
             class="fixed bottom-10 right-10 z-50">
            <button @click="confirmBulkDelete"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center space-x-3 transition-all transform hover:scale-105 active:scale-95">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span class="font-bold">Hapus <span x-text="selectedIds.length"></span> Data</span>
            </button>
        </div>

        <!-- Import Modal -->
        <x-modal name="import-modal" focusable>
            <form action="{{ route('cs.greeting.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    Import Data Greeting
                </h2>
                
                <div class="space-y-4">
                    <div class="border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl p-8 flex flex-col items-center justify-center bg-gray-50/50 dark:bg-gray-900/50 group hover:border-red-400 transition-colors">
                        <svg class="w-12 h-12 text-gray-400 group-hover:text-red-400 mb-4 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Pilih file Excel anda (.xlsx / .xls)</p>
                        <input type="file" name="file" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Batal
                    </x-secondary-button>

                    <x-danger-button class="ml-3 !bg-red-600">
                        Proses Import
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    </div>

    <script>
        function greetingManager() {
            return {
                selectedIds: [],
                selectAll: false,
                
                toggleSelectAll() {
                    if (this.selectAll) {
                        this.selectedIds = Array.from(document.querySelectorAll('input[type="checkbox"][value]')).map(el => el.value);
                    } else {
                        this.selectedIds = [];
                    }
                },

                async confirmBulkDelete() {
                    if (!confirm(`Apakah Anda yakin ingin menghapus ${this.selectedIds.length} data ini? Tindakan ini tidak dapat dibatalkan.`)) {
                        return;
                    }

                    try {
                        const response = await fetch('{{ route('cs.greeting.bulk-delete') }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ ids: this.selectedIds })
                        });

                        const result = await response.json();

                        if (result.success) {
                            window.location.reload();
                        } else {
                            alert(result.message || 'Gagal menghapus data.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan sistem.');
                    }
                },

                async confirmDeleteFiltered() {
                    if (!confirm('Apakah Anda yakin ingin menghapus SEMUA data yang muncul sesuai filter saat ini? Tindakan ini tidak dapat dibatalkan.')) {
                        return;
                    }

                    const params = new URLSearchParams(window.location.search);
                    
                    try {
                        const response = await fetch('{{ route('cs.greeting.bulk-delete-filtered') }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(Object.fromEntries(params))
                        });

                        const result = await response.json();

                        if (result.success) {
                            window.location.href = '{{ route('cs.greeting.index') }}';
                        } else {
                            alert(result.message || 'Gagal menghapus data.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan sistem.');
                    }
                },

                async claimLead(id) {
                    if (!confirm('Ambil lead ini untuk mulai mengelola konsultasi?')) {
                        return;
                    }

                    try {
                        let url = '{{ route('cs.greeting.claim', ['lead' => ':id']) }}';
                        url = url.replace(':id', id);

                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const contentType = response.headers.get("content-type");
                        if (contentType && contentType.indexOf("application/json") !== -1) {
                            const result = await response.json();
                            if (result.success) {
                                alert(result.message);
                                if (result.redirect) {
                                    window.location.href = result.redirect;
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                alert(result.message || 'Gagal mengambil lead.');
                            }
                        } else {
                            const errorText = await response.text();
                            console.error('Server Error:', errorText);
                            alert(`Error ${response.status}: Terjadi kesalahan pada server.`);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan koneksi atau sistem.');
                    }
                }
            }
        }
    </script>
</x-app-layout>

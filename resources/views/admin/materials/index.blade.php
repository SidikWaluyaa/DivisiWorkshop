<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-xl text-white leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                {{ __('Master Data Material') }}
            </h2>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-white/10 rounded-lg backdrop-blur-sm border border-white/20 text-white text-sm">
                    <span class="opacity-75">Total Material:</span> 
                    <span class="font-bold ml-1">{{ $materials->count() }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        selected: [], 
        activeTab: 'upper', 
        statusFilter: 'all', 
        subCategoryFilter: 'all',
        upperMaterials: {{ $upperMaterials->map(fn($m) => ['id' => $m->id, 'status' => $m->status])->values() }},
        solMaterials: {{ $solMaterials->map(fn($m) => ['id' => $m->id, 'status' => $m->status, 'sub_category' => $m->sub_category])->values() }},
        
        get filteredItems() {
            if (this.activeTab === 'upper') {
                return this.upperMaterials.filter(m => this.statusFilter === 'all' || m.status === this.statusFilter);
            } else {
                return this.solMaterials.filter(m => (this.statusFilter === 'all' || m.status === this.statusFilter) && (this.subCategoryFilter === 'all' || m.sub_category === this.subCategoryFilter));
            }
        },

        selectAll() {
            const ids = this.filteredItems.map(m => m.id);
            // Check if all filtered items are already selected
            const allSelected = ids.every(id => this.selected.includes(id));
            
            if (allSelected) {
                // Deselect only the filtered items
                this.selected = this.selected.filter(id => !ids.includes(id));
            } else {
                // Select all filtered items (merge unique)
                this.selected = [...new Set([...this.selected, ...ids])];
            }
        },

        isAllSelected() {
            const ids = this.filteredItems.map(m => m.id);
            return ids.length > 0 && ids.every(id => this.selected.includes(id));
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Toolbar & Search --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 overflow-hidden mb-6 p-5">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    {{-- Left: Search & Filter --}}
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <form action="{{ route('admin.materials.index') }}" method="GET" class="w-full md:w-64 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 text-sm transition-all bg-gray-50 focus:bg-white" 
                                   placeholder="Cari material...">
                        </form>
                        
                        <!-- Status Filter -->
                        <select x-model="statusFilter" class="w-40 py-2.5 rounded-xl border-gray-200 focus:border-teal-500 text-sm bg-gray-50 focus:bg-white transition-all">
                            <option value="all">Semua Status</option>
                            <option value="Ready">Ready</option>
                            <option value="Belanja">Belanja</option>
                            <option value="Followup">Followup</option>
                            <option value="Reject">Reject</option>
                            <option value="Retur">Retur</option>
                        </select>
                    </div>

                    {{-- Right: Actions --}}
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        {{-- Bulk Delete --}}
                        <form action="{{ route('admin.materials.bulk-destroy') }}" method="POST" onsubmit="return confirm('Yakin hapus ' + this.selected.length + ' material terpilih?')">
                            @csrf
                            @method('DELETE')
                            <template x-for="id in selected">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" 
                                    x-show="selected.length > 0"
                                    x-transition
                                    class="px-4 py-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-colors flex items-center gap-2 font-medium text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus (<span x-text="selected.length"></span>)
                            </button>
                        </form>

                        {{-- New Actions --}}
                        <div class="flex items-center gap-2 mr-2">
                             <a href="{{ route('admin.materials.export-pdf') }}" target="_blank" class="px-3 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors flex items-center gap-2 font-medium text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                PDF
                            </a>
                            <a href="{{ route('admin.materials.export-excel') }}" target="_blank" class="px-3 py-2.5 bg-green-50 text-green-600 rounded-xl hover:bg-green-100 transition-colors flex items-center gap-2 font-medium text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Excel
                            </a>
                            <button x-on:click="$dispatch('open-modal', 'import-material-modal')" class="px-3 py-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition-colors flex items-center gap-2 font-medium text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import
                            </button>
                        </div>
                        
                        <button x-on:click.prevent="$dispatch('open-modal', 'create-material-modal')" 
                                class="w-full md:w-auto px-6 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white rounded-xl shadow-lg shadow-teal-500/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2 font-medium text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Material
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table Card --}}
            <!-- Tabs & Filter Section -->
            <div class="space-y-6">
                
                <!-- Tab Navigation -->
                <div class="flex gap-4 border-b border-gray-200 dark:border-gray-700">
                    <button @click="activeTab = 'upper'" 
                        class="pb-3 px-4 text-sm font-bold border-b-2 transition-colors duration-300"
                        :class="activeTab === 'upper' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                        Material Upper
                    </button>
                    <button @click="activeTab = 'sol'" 
                        class="pb-3 px-4 text-sm font-bold border-b-2 transition-colors duration-300"
                        :class="activeTab === 'sol' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                        Material Sol
                    </button>
                </div>

                <!-- Material Upper Tab -->
                <div x-show="activeTab === 'upper'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 overflow-hidden">
                        <div class="overflow-x-auto -mx-4 sm:mx-0">
                            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                <thead>
                                    <tr class="bg-gradient-to-r from-teal-50 to-emerald-50 dark:from-gray-700 dark:to-gray-700">
                                        <th scope="col" class="px-6 py-4 text-left">
                                            <input type="checkbox" 
                                                   class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500"
                                                   @click="selectAll()"
                                                   :checked="isAllSelected()">
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Material</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Stock</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Harga Beli</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Status & PIC</th>
                                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                    @forelse ($upperMaterials as $material)
                                    <tr x-show="statusFilter === 'all' || statusFilter === '{{ $material->status }}'" class="hover:bg-teal-50/30 dark:hover:bg-gray-700 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" value="{{ $material->id }}" x-model="selected" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $material->name }}</div>
                                            <div class="text-xs text-gray-500">Min: {{ $material->min_stock }} {{ $material->unit }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold {{ $material->stock <= $material->min_stock ? 'text-red-600' : 'text-gray-700' }}">
                                                {{ $material->stock }} <span class="text-xs font-normal text-gray-500">{{ $material->unit }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 font-medium">
                                            Rp {{ number_format($material->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col gap-1">
                                                @php
                                                    $statusColors = [
                                                        'Ready' => 'bg-green-100 text-green-800',
                                                        'Belanja' => 'bg-blue-100 text-blue-800',
                                                        'Followup' => 'bg-purple-100 text-purple-800',
                                                        'Reject' => 'bg-red-100 text-red-800',
                                                        'Retur' => 'bg-gray-100 text-gray-800',
                                                    ];
                                                    $colorClass = $statusColors[$material->status] ?? 'bg-gray-100 text-gray-800';
                                                    
                                                    // Override color if critical stock
                                                    if ($material->stock <= 0) $colorClass = 'bg-red-100 text-red-800';
                                                    elseif ($material->stock <= $material->min_stock && $material->status == 'Ready') $colorClass = 'bg-yellow-100 text-yellow-800';
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full w-fit {{ $colorClass }}">
                                                    {{ $material->status }}
                                                </span>
                                                
                                                @if($material->pic)
                                                    <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                        {{ Str::limit($material->pic->name, 15) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($material->pic && $material->pic->phone)
                                                @php
                                                    $message = "Halo {$material->pic->name}, Material *{$material->name}* statusnya *{$material->status}* (Stock: {$material->stock} {$material->unit}). Mohon info update.";
                                                    $waLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $material->pic->phone) . "?text=" . urlencode($message);
                                                @endphp
                                                <a href="{{ $waLink }}" target="_blank" class="text-green-600 hover:text-green-800 mr-2 p-1 bg-green-50 rounded-lg inline-flex" title="Chat WA">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                </a>
                                            @endif
                                            <button x-on:click.prevent="$dispatch('open-modal', 'edit-material-{{ $material->id }}')" 
                                                    class="text-teal-600 hover:text-teal-900 dark:hover:text-teal-400 mr-2 transition-colors p-1 hover:bg-teal-50 rounded-lg inline-flex">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <form action="{{ route('admin.materials.destroy', $material) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus material ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-600 transition-colors p-1 hover:bg-red-50 rounded-lg inline-flex">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            Material tidak ditemukan.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Material Sol Tab -->
                <div x-show="activeTab === 'sol'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    
                    <!-- Filter Sol -->
                    <div class="flex justify-end mb-4">
                        <select x-model="subCategoryFilter" class="rounded-lg border-gray-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                            <option value="all">Semua Kategori</option>
                            <option value="Sol Potong">Sol Potong</option>
                            <option value="Sol Jadi">Sol Jadi</option>
                            <option value="Foxing">Foxing</option>
                            <option value="Vibram">Vibram</option>
                        </select>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 overflow-hidden">
                        <div class="overflow-x-auto -mx-4 sm:mx-0">
                            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                <thead>
                                    <tr class="bg-gradient-to-r from-teal-50 to-emerald-50 dark:from-gray-700 dark:to-gray-700">
                                        <th scope="col" class="px-6 py-4 text-left">
                                            <input type="checkbox" 
                                                   class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500"
                                                   @click="selectAll()"
                                                   :checked="isAllSelected()">
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Material</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Kategori</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Size</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Stock</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Harga Beli</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Status & PIC</th>
                                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                    @forelse ($solMaterials as $material)
                                        <tr x-show="(subCategoryFilter === 'all' || subCategoryFilter === '{{ $material->sub_category }}') && (statusFilter === 'all' || statusFilter === '{{ $material->status }}')" class="hover:bg-teal-50/30 dark:hover:bg-gray-700 transition-colors group">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" value="{{ $material->id }}" x-model="selected" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $material->name }}</div>
                                                <div class="text-xs text-gray-500">Min: {{ $material->min_stock }} {{ $material->unit }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-teal-50 text-teal-700 border border-teal-100">
                                                    {{ $material->sub_category }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($material->size)
                                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-lg bg-gray-100 text-gray-700">
                                                    {{ $material->size }}
                                                </span>
                                                @else
                                                <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold {{ $material->stock <= $material->min_stock ? 'text-red-600' : 'text-gray-700' }}">
                                                    {{ $material->stock }} <span class="text-xs font-normal text-gray-500">{{ $material->unit }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 font-medium">
                                                Rp {{ number_format($material->price, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex flex-col gap-1">
                                                    @php
                                                        $statusColors = [
                                                            'Ready' => 'bg-green-100 text-green-800',
                                                            'Belanja' => 'bg-blue-100 text-blue-800',
                                                            'Followup' => 'bg-purple-100 text-purple-800',
                                                            'Reject' => 'bg-red-100 text-red-800',
                                                            'Retur' => 'bg-gray-100 text-gray-800',
                                                        ];
                                                        $colorClass = $statusColors[$material->status] ?? 'bg-gray-100 text-gray-800';
                                                        
                                                        // Override color if critical stock
                                                        if ($material->stock <= 0) $colorClass = 'bg-red-100 text-red-800';
                                                        elseif ($material->stock <= $material->min_stock && $material->status == 'Ready') $colorClass = 'bg-yellow-100 text-yellow-800';
                                                    @endphp
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full w-fit {{ $colorClass }}">
                                                        {{ $material->status }}
                                                    </span>
                                                    
                                                    @if($material->pic)
                                                        <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                            {{ Str::limit($material->pic->name, 15) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @if($material->pic && $material->pic->phone)
                                                    @php
                                                        $message = "Halo {$material->pic->name}, Material *{$material->name}* statusnya *{$material->status}* (Stock: {$material->stock} {$material->unit}). Mohon info update.";
                                                        $waLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $material->pic->phone) . "?text=" . urlencode($message);
                                                    @endphp
                                                    <a href="{{ $waLink }}" target="_blank" class="text-green-600 hover:text-green-800 mr-2 p-1 bg-green-50 rounded-lg inline-flex" title="Chat WA">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                    </a>
                                                @endif
                                                <button x-on:click.prevent="$dispatch('open-modal', 'edit-material-{{ $material->id }}')" 
                                                        class="text-teal-600 hover:text-teal-900 dark:hover:text-teal-400 mr-2 transition-colors p-1 hover:bg-teal-50 rounded-lg inline-flex">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <form action="{{ route('admin.materials.destroy', $material) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus material ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-600 transition-colors p-1 hover:bg-red-50 rounded-lg inline-flex">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                                Material tidak ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination Removed as we load all Data -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-100 dark:border-gray-600 text-xs text-gray-500 text-center">
                    Menampilkan semua data material ({{ $upperMaterials->count() + $solMaterials->count() }} item).
                </div>
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-material-modal" :show="$errors->any() && old('form_type') === 'create_material'" focusable>
        <form method="POST" action="{{ route('admin.materials.store') }}">
            @csrf
            <input type="hidden" name="form_type" value="create_material">
            
            <div class="flex justify-between items-center p-4 rounded-t-xl bg-gradient-to-r from-teal-500 to-emerald-600 text-white">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    Tambah Material Baru
                </h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-white/80 hover:text-white transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-6" x-data="{ type: 'Material Upper' }">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-1 md:col-span-2">
                    <x-input-label for="name" :value="__('Nama Material')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required />
                </div>
                <div>
                     <x-input-label for="type" :value="__('Tipe')" />
                    <select id="type" name="type" x-model="type" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-teal-500">
                        <option value="Material Upper">Material Upper</option>
                        <option value="Material Sol">Material Sol</option>
                    </select>
                </div>
                <div x-show="type === 'Material Sol'">
                    <x-input-label for="sub_category" :value="__('Kategori Sol')" />
                    <select id="sub_category" name="sub_category" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-teal-500">
                        <option value="" selected disabled>Pilih Kategori</option>
                        <option value="Sol Potong">Sol Potong</option>
                        <option value="Sol Jadi">Sol Jadi</option>
                        <option value="Foxing">Foxing</option>
                        <option value="Vibram">Vibram</option>
                    </select>
                </div>
                <!-- Size Column (New) -->
                <div x-show="type === 'Material Sol'">
                    <x-input-label for="size" :value="__('Size (Opsional)')" />
                    <x-text-input id="size" class="block mt-1 w-full" type="text" name="size" placeholder="Contoh: 40, 41, S, M" />
                </div>
                <div>
                    <x-input-label for="stock" :value="__('Stock')" />
                    <x-text-input id="stock" class="block mt-1 w-full" type="number" name="stock" required />
                </div>
                <div>
                    <x-input-label for="unit" :value="__('Unit')" />
                    <x-text-input id="unit" class="block mt-1 w-full" type="text" name="unit" required />
                </div>
                <div>
                    <x-input-label for="price" :value="__('Harga per Unit')" />
                    <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" required />
                </div>
                <div>
                    <x-input-label for="min_stock" :value="__('Minimal Stock')" />
                    <x-text-input id="min_stock" class="block mt-1 w-full" type="number" name="min_stock" required />
                </div>
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">
                        <option value="Ready">Ready</option>
                        <option value="Belanja">Belanja</option>
                        <option value="Followup">Followup</option>
                        <option value="Reject">Reject</option>
                        <option value="Retur">Retur</option>
                    </select>
                </div>
                <div class="col-span-1 md:col-span-2">
                    <x-input-label for="pic_user_id" :value="__('PIC Material (Opsional)')" />
                    <select id="pic_user_id" name="pic_user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">
                        <option value="">-- Pilih PIC --</option>
                        @foreach($pics as $pic)
                            <option value="{{ $pic->id }}">
                                {{ $pic->name }} @if($pic->phone) ({{ $pic->phone }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                <x-primary-button class="bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 shadow-md transform hover:-translate-y-0.5 transition-all">{{ __('Simpan Material') }}</x-primary-button>
            </div>
            </div>
        </form>
    </x-modal>
    <!-- Edit Material Modals -->
    @foreach($upperMaterials->merge($solMaterials) as $material)
    <x-modal name="edit-material-{{ $material->id }}" :show="$errors->any() && old('form_type') === 'edit_material_' . $material->id" focusable>
        <form method="POST" action="{{ route('admin.materials.update', $material) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="form_type" value="edit_material_{{ $material->id }}">

            <div class="flex justify-between items-center p-4 rounded-t-xl bg-gradient-to-r from-teal-500 to-emerald-600 text-white">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    Edit Material: {{ $material->name }}
                </h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-white/80 hover:text-white transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="p-6" x-data="{ type: '{{ $material->type }}' }">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-1 md:col-span-2">
                        <x-input-label for="name_{{ $material->id }}" :value="__('Nama Material')" />
                        <x-text-input id="name_{{ $material->id }}" class="block mt-1 w-full" type="text" name="name" :value="$material->name" required />
                    </div>
                    <div>
                        <x-input-label for="type_{{ $material->id }}" :value="__('Tipe')" />
                        <select id="type_{{ $material->id }}" name="type" x-model="type" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-teal-500">
                            <option value="Material Upper">Material Upper</option>
                            <option value="Material Sol">Material Sol</option>
                        </select>
                    </div>
                    <div x-show="type === 'Material Sol'">
                        <x-input-label for="sub_category_{{ $material->id }}" :value="__('Kategori Sol')" />
                        <select id="sub_category_{{ $material->id }}" name="sub_category" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-teal-500">
                            <option value="" disabled>Pilih Kategori</option>
                            <option value="Sol Potong" {{ $material->sub_category == 'Sol Potong' ? 'selected' : '' }}>Sol Potong</option>
                            <option value="Sol Jadi" {{ $material->sub_category == 'Sol Jadi' ? 'selected' : '' }}>Sol Jadi</option>
                            <option value="Foxing" {{ $material->sub_category == 'Foxing' ? 'selected' : '' }}>Foxing</option>
                            <option value="Vibram" {{ $material->sub_category == 'Vibram' ? 'selected' : '' }}>Vibram</option>
                        </select>
                    </div>
                    
                    <!-- Size Column -->
                    <div x-show="type === 'Material Sol'">
                        <x-input-label for="size_{{ $material->id }}" :value="__('Size (Opsional)')" />
                        <x-text-input id="size_{{ $material->id }}" class="block mt-1 w-full" type="text" name="size" :value="$material->size" placeholder="Contoh: 40, 41, S, M" />
                    </div>

                    <div>
                        <x-input-label for="stock_{{ $material->id }}" :value="__('Stock')" />
                        <x-text-input id="stock_{{ $material->id }}" class="block mt-1 w-full" type="number" name="stock" :value="$material->stock" required />
                    </div>
                    <div>
                        <x-input-label for="unit_{{ $material->id }}" :value="__('Unit')" />
                        <x-text-input id="unit_{{ $material->id }}" class="block mt-1 w-full" type="text" name="unit" :value="$material->unit" required />
                    </div>
                    <div>
                        <x-input-label for="price_{{ $material->id }}" :value="__('Harga per Unit')" />
                        <x-text-input id="price_{{ $material->id }}" class="block mt-1 w-full" type="number" step="0.01" name="price" :value="$material->price" required />
                    </div>
                    <div>
                        <x-input-label for="min_stock_{{ $material->id }}" :value="__('Minimal Stock')" />
                        <x-text-input id="min_stock_{{ $material->id }}" class="block mt-1 w-full" type="number" name="min_stock" :value="$material->min_stock" required />
                    </div>
                    <div>
                        <x-input-label for="status_{{ $material->id }}" :value="__('Status')" />
                        <select id="status_{{ $material->id }}" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 rounded-md shadow-sm">
                            <option value="Ready" {{ $material->status == 'Ready' ? 'selected' : '' }}>Ready</option>
                            <option value="Belanja" {{ $material->status == 'Belanja' ? 'selected' : '' }}>Belanja</option>
                            <option value="Followup" {{ $material->status == 'Followup' ? 'selected' : '' }}>Followup</option>
                            <option value="Reject" {{ $material->status == 'Reject' ? 'selected' : '' }}>Reject</option>
                            <option value="Retur" {{ $material->status == 'Retur' ? 'selected' : '' }}>Retur</option>
                        </select>
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <x-input-label for="pic_user_id_{{ $material->id }}" :value="__('PIC Material')" />
                        <select id="pic_user_id_{{ $material->id }}" name="pic_user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 rounded-md shadow-sm">
                            <option value="">-- Pilih PIC --</option>
                            @foreach($pics as $pic)
                                <option value="{{ $pic->id }}" {{ $material->pic_user_id == $pic->id ? 'selected' : '' }}>
                                    {{ $pic->name }} @if($pic->phone) ({{ $pic->phone }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                    <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                    <x-primary-button class="bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 shadow-md transform hover:-translate-y-0.5 transition-all">{{ __('Simpan Perubahan') }}</x-primary-button>
                </div>
            </div>
        </form>
    </x-modal>
    @endforeach

    <!-- Import Modal -->
    <x-modal name="import-material-modal" :show="false" focusable>
        <form method="POST" action="{{ route('admin.materials.import') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="flex justify-between items-center p-4 rounded-t-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white mb-6">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    </div>
                    Import Material
                </h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-white/80 hover:text-white transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="space-y-4">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Gunakan template Excel untuk menghindari kesalahan format columns.
                                <a href="{{ route('admin.materials.template') }}" class="font-bold underline hover:text-blue-900">Download Template</a>
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <x-input-label for="file" :value="__('File Excel (.xlsx, .xls)')" />
                    <input type="file" name="file" id="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mt-2" required accept=".xlsx,.xls,.csv">
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                <x-primary-button class="bg-blue-600 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 shadow-md transform hover:-translate-y-0.5 transition-all">{{ __('Import Data') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>


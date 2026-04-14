<x-app-layout>
    <div class="min-h-screen bg-[#F9FAFB] dark:bg-gray-900">
        {{-- Header Section --}}
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="max-w-[1600px] mx-auto">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3 text-xs font-medium text-gray-400">
                        <li>Master Data</li>
                        <li>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        </li>
                        <li class="text-teal-600 font-bold uppercase tracking-wider">Material</li>
                    </ol>
                </nav>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                            Master Data Material
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-teal-50 text-teal-600 border border-teal-100 uppercase tracking-widest">
                                TOTAL MATERIAL: {{ $totalCount }}
                            </span>
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[1600px] mx-auto p-6" x-data="{ 
            selected: [], 
            activeTab: '{{ $activeTab }}',
            selectAll(type) {
                let checkBoxes = document.querySelectorAll('.' + type + '-checkbox');
                let allChecked = true;
                checkBoxes.forEach(cb => { if(!cb.checked) allChecked = false; });
                
                checkBoxes.forEach(cb => {
                    cb.checked = !allChecked;
                    let val = parseInt(cb.value);
                    if (!allChecked) {
                        if(!this.selected.includes(val)) this.selected.push(val);
                    } else {
                        this.selected = this.selected.filter(id => id !== val);
                    }
                });
            }
        }">
            
            {{-- Toolbar Section --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <div class="flex flex-col xl:flex-row justify-between items-center gap-4">
                    <form action="{{ route('admin.materials.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 w-full xl:w-auto">
                        <input type="hidden" name="tab" :value="activeTab">
                        <div class="relative w-full md:w-80 group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400 group-focus-within:text-teal-500 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="block w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                placeholder="Cari material...">
                        </div>

                        <div class="flex items-center gap-2 w-full md:w-auto">
                            <select name="status" onchange="this.form.submit()" 
                                class="w-full md:w-44 py-2.5 text-sm bg-gray-50 border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="all">Semua Status</option>
                                <option value="Ready" {{ request('status') == 'Ready' ? 'selected' : '' }}>Ready</option>
                                <option value="Belanja" {{ request('status') == 'Belanja' ? 'selected' : '' }}>Belanja</option>
                                <option value="Followup" {{ request('status') == 'Followup' ? 'selected' : '' }}>Followup</option>
                                <option value="Reject" {{ request('status') == 'Reject' ? 'selected' : '' }}>Reject</option>
                                <option value="Retur" {{ request('status') == 'Retur' ? 'selected' : '' }}>Retur</option>
                            </select>

                            @if(request('tab') == 'sol' || (!request('tab') && $activeTab == 'sol'))
                            <select name="sub_category" onchange="this.form.submit()"
                                class="w-full md:w-44 py-2.5 text-sm bg-gray-50 border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="all">Semua Kategori</option>
                                <option value="Sol Potong" {{ request('sub_category') == 'Sol Potong' ? 'selected' : '' }}>Sol Potong</option>
                                <option value="Sol Jadi" {{ request('sub_category') == 'Sol Jadi' ? 'selected' : '' }}>Sol Jadi</option>
                                <option value="Foxing" {{ request('sub_category') == 'Foxing' ? 'selected' : '' }}>Foxing</option>
                                <option value="Vibram" {{ request('sub_category') == 'Vibram' ? 'selected' : '' }}>Vibram</option>
                            </select>
                            @endif
                        </div>
                    </form>

                    <div class="flex items-center gap-3 w-full xl:w-auto justify-end">
                        <div class="flex items-center bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl p-1 shadow-sm">
                            <a href="{{ route('admin.materials.export-pdf') }}" class="p-2.5 hover:bg-white dark:hover:bg-gray-600 rounded-lg text-gray-500 hover:text-teal-600 transition-all" title="Export PDF">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </a>
                            <a href="{{ route('admin.materials.export-excel') }}" class="p-2.5 hover:bg-white dark:hover:bg-gray-600 rounded-lg text-gray-500 hover:text-green-600 transition-all" title="Export Excel">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </a>
                            <button x-on:click="$dispatch('open-modal', 'import-material-modal')" class="p-2.5 hover:bg-white dark:hover:bg-gray-600 rounded-lg text-gray-500 hover:text-blue-600 transition-all" title="Import Data">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            </button>
                            <a href="{{ route('admin.materials.template') }}" class="p-2.5 hover:bg-white dark:hover:bg-gray-600 rounded-lg text-gray-500 hover:text-yellow-600 transition-all" title="Download Template">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </a>
                        </div>

                        <template x-if="selected.length > 0">
                            <form action="{{ route('admin.materials.bulk-destroy') }}" method="POST" class="inline" onsubmit="return confirm('Hapus material yang dipilih?')">
                                @csrf
                                <template x-for="id in selected">
                                    <input type="hidden" name="ids[]" :value="id">
                                </template>
                                <button type="submit" 
                                        class="px-5 py-3 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl transition-all border border-red-100 flex items-center gap-2 font-black text-sm whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Hapus (<span x-text="selected.length"></span>)
                                </button>
                            </form>
                        </template>

                        <button x-on:click.prevent="$dispatch('open-modal', 'create-material-modal')" 
                                class="px-6 py-3 bg-[#0F766E] hover:bg-[#0D635C] text-white rounded-xl shadow-lg shadow-teal-900/10 transition-all transform hover:-translate-y-0.5 flex items-center gap-2 font-black text-sm whitespace-nowrap">
                            <span class="text-xl leading-none">+</span>
                            Tambah Material
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tabs Section --}}
            <div class="mb-6">
                <div class="flex gap-8 border-b border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.materials.index', ['tab' => 'upper'] + request()->except('tab')) }}" 
                       @click="activeTab = 'upper'"
                       class="pb-4 px-2 text-sm font-bold border-b-2 transition-all"
                       :class="activeTab === 'upper' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                       Material Upper
                    </a>
                    <a href="{{ route('admin.materials.index', ['tab' => 'sol'] + request()->except('tab')) }}" 
                       @click="activeTab = 'sol'"
                       class="pb-4 px-2 text-sm font-bold border-b-2 transition-all"
                       :class="activeTab === 'sol' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                       Material Sol
                    </a>
                </div>
            </div>

            {{-- Table View --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Material Upper Tab content --}}
                <div x-show="activeTab === 'upper'">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">
                                        <input type="checkbox" @click="selectAll('upper')" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                    </th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Material</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Stock</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Harga Beli</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Status & PIC</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($upperMaterials as $material)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-5 text-center">
                                        <input type="checkbox" value="{{ $material->id }}" x-model="selected" class="upper-checkbox rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="font-bold text-gray-900 dark:text-white mb-0.5">{{ $material->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium">Min: {{ $material->min_stock }} {{ $material->unit }}</div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm font-black {{ $material->stock <= $material->min_stock ? 'text-red-500' : 'text-gray-900 dark:text-gray-100' }}">
                                            {{ $material->stock }} <span class="text-[10px] font-bold text-gray-400 uppercase ml-0.5">{{ $material->unit }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right whitespace-nowrap">
                                        <div class="text-sm font-black text-gray-900 dark:text-gray-100 italic">Rp {{ number_format($material->price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $statusClass = match($material->status) {
                                                    'Ready' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                                    'Belanja', 'Followup' => 'bg-[#FFFBEB] text-[#D97706] border-[#FEF3C7]',
                                                    'Reject', 'Retur' => 'bg-red-50 text-red-600 border-red-200',
                                                    default => 'bg-gray-50 text-gray-600 border-gray-200',
                                                };
                                                // Override for low stock
                                                if ($material->stock <= $material->min_stock) {
                                                    $statusClass = 'bg-[#FFFBEB] text-[#D97706] border-[#FEF3C7] shadow-sm shadow-[#D97706]/5';
                                                    $displayText = 'LOW STOCK';
                                                } else {
                                                    $displayText = Str::upper($material->status);
                                                }
                                            @endphp
                                            <span class="px-2.5 py-1 text-[10px] font-black rounded border {{ $statusClass }} tracking-widest">
                                                {{ $displayText }}
                                            </span>
                                            
                                            @if($material->pic)
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-teal-50 dark:bg-gray-700 flex items-center justify-center text-[#0F766E] font-black text-[10px] border border-teal-100 dark:border-gray-600 shadow-inner" title="{{ $material->pic->name }}">
                                                {{ collect(explode(' ', $material->pic->name))->map(fn($n) => Str::substr($n, 0, 1))->take(2)->implode('') }}
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right space-x-1">
                                        <button x-on:click.prevent="$dispatch('open-modal', 'edit-material-{{ $material->id }}')" 
                                                class="p-2 text-gray-400 hover:text-teal-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('admin.materials.destroy', $material) }}" method="POST" class="inline" onsubmit="return confirm('Hapus material ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">Tidak ada material upper ditemukan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-5 bg-gray-50/50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                        {{ $upperMaterials->links() }}
                    </div>
                </div>

                {{-- Material Sol Tab content --}}
                <div x-show="activeTab === 'sol'">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">
                                        <input type="checkbox" @click="selectAll('sol')" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                    </th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Material</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Stock</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Harga Beli</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Status & PIC</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($solMaterials as $material)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-5 text-center">
                                        <input type="checkbox" value="{{ $material->id }}" x-model="selected" class="sol-checkbox rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="font-bold text-gray-900 dark:text-white mb-0.5">{{ $material->name }}</div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-100">{{ $material->sub_category }}</span>
                                            @if($material->size)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700">{{ $material->size }}</span>
                                            @endif
                                            <span class="text-[10px] text-gray-400">Min: {{ $material->min_stock }} {{ $material->unit }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm font-black {{ $material->stock <= $material->min_stock ? 'text-red-500' : 'text-gray-900 dark:text-gray-100' }}">
                                            {{ $material->stock }} <span class="text-[10px] font-bold text-gray-400 uppercase ml-0.5">{{ $material->unit }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right whitespace-nowrap">
                                        <div class="text-sm font-black text-gray-900 dark:text-gray-100 italic">Rp {{ number_format($material->price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $statusClass = match($material->status) {
                                                    'Ready' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                                    'Belanja', 'Followup' => 'bg-[#FFFBEB] text-[#D97706] border-[#FEF3C7]',
                                                    'Reject', 'Retur' => 'bg-red-50 text-red-600 border-red-200',
                                                    default => 'bg-gray-50 text-gray-600 border-gray-200',
                                                };
                                                if ($material->stock <= $material->min_stock) {
                                                    $statusClass = 'bg-[#FFFBEB] text-[#D97706] border-[#FEF3C7] shadow-sm shadow-[#D97706]/5';
                                                    $displayText = 'LOW STOCK';
                                                } else {
                                                    $displayText = Str::upper($material->status);
                                                }
                                            @endphp
                                            <span class="px-2.5 py-1 text-[10px] font-black rounded border {{ $statusClass }} tracking-widest">
                                                {{ $displayText }}
                                            </span>
                                            
                                            @if($material->pic)
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-teal-50 dark:bg-gray-700 flex items-center justify-center text-[#0F766E] font-black text-[10px] border border-teal-100 dark:border-gray-600 shadow-inner" title="{{ $material->pic->name }}">
                                                {{ collect(explode(' ', $material->pic->name))->map(fn($n) => Str::substr($n, 0, 1))->take(2)->implode('') }}
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right space-x-1">
                                        <button x-on:click.prevent="$dispatch('open-modal', 'edit-material-{{ $material->id }}')" 
                                                class="p-2 text-gray-400 hover:text-teal-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('admin.materials.destroy', $material) }}" method="POST" class="inline" onsubmit="return confirm('Hapus material ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">Tidak ada material sol ditemukan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-5 bg-gray-50/50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                        {{ $solMaterials->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create Material Modal --}}
    <x-modal name="create-material-modal" :show="$errors->any() && old('form_type') === 'create_material'" focusable>
        <form method="POST" action="{{ route('admin.materials.store') }}" class="p-0">
            @csrf
            <input type="hidden" name="form_type" value="create_material">
            
            {{-- Modal Header --}}
            <div class="px-8 py-6 border-b border-gray-100 relative">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Tambah Material Baru</h2>
                <p class="text-xs text-gray-400 mt-1 font-medium">Lengkapi informasi material untuk inventori pusat</p>
                <button type="button" x-on:click="$dispatch('close')" class="absolute top-6 right-8 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-8 space-y-8" x-data="{ type: '' }">
                {{-- Section 1: INFORMASI UTAMA --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-5 bg-[#0F766E] rounded-full"></div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-[#0F766E]">Informasi Utama</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nama Material</label>
                            <input type="text" name="name" required placeholder="Contoh: Plat Besi Galvanis 2mm"
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipe</label>
                            <select name="type" x-model="type" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="" disabled selected>Pilih Tipe</option>
                                <option value="Material Upper">Material Upper</option>
                                <option value="Material Sol">Material Sol</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kategori Material</label>
                            <select name="category" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="" disabled selected>Pilih Kategori</option>
                                <option value="PRODUCTION">Produksi (Cek Stok)</option>
                                <option value="SHOPPING">Belanja (Budget)</option>
                            </select>
                            <p class="mt-1.5 text-[10px] text-gray-400 italic leading-relaxed">Kategori menentukan lokasi penyimpanan di zona gudang A atau B.</p>
                        </div>

                        <div x-show="type === 'Material Sol'" x-cloak>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kategori Sol</label>
                            <select name="sub_category" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                                <option value="" selected disabled>Pilih Kategori Sol</option>
                                <option value="Sol Potong">Sol Potong</option>
                                <option value="Sol Jadi">Sol Jadi</option>
                                <option value="Foxing">Foxing</option>
                                <option value="Vibram">Vibram</option>
                            </select>
                        </div>

                        <div x-show="type === 'Material Sol'" x-cloak>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Size <span class="text-[8px] opacity-50 lowercase tracking-normal font-medium">(Opsional)</span></label>
                            <input type="text" name="size" placeholder="Contoh: 40, 41, M"
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Stock</label>
                            <input type="number" name="stock" required placeholder="0"
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Unit</label>
                            <input type="text" name="unit" required placeholder="Kg, Pcs, Liter..."
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Harga per Unit</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none border-r border-gray-100 pr-3">
                                    <span class="text-[10px] font-black text-gray-400 group-focus-within:text-teal-500 transition-colors uppercase tracking-widest">IDR</span>
                                </div>
                                <input type="number" name="price" required step="0.01" placeholder="0.00"
                                    class="w-full pl-16 pr-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Minimal Stock</label>
                            <input type="number" name="min_stock" required placeholder="Batas aman stok"
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-red-100 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-red-400 transition-all font-semibold">
                        </div>
                    </div>
                </div>

                {{-- Section 2: ADMINISTRASI --}}
                <div class="space-y-6 pt-4">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-5 bg-[#C2410C] rounded-full"></div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-[#C2410C]">Administrasi & PIC</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Status</label>
                            <select name="status" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="Ready">Aktif / Ready</option>
                                <option value="Belanja">Belanja</option>
                                <option value="Followup">Followup</option>
                                <option value="Reject">Reject</option>
                                <option value="Retur">Retur</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">PIC Material <span class="text-[8px] opacity-50 lowercase tracking-normal font-medium">(Opsional)</span></label>
                            <select name="pic_user_id" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="">Pilih Penanggung Jawab</option>
                                @foreach($pics as $pic)
                                    <option value="{{ $pic->id }}">{{ $pic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 py-6 bg-gray-50/80 dark:bg-gray-700/50 flex justify-end items-center gap-6 border-t border-gray-100">
                <button type="button" x-on:click="$dispatch('close')" class="text-sm font-black text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">Batal</button>
                <button type="submit" class="px-8 py-3 bg-[#0F766E] hover:bg-[#0D635C] text-white rounded-xl shadow-lg shadow-teal-900/10 transition-all transform hover:-translate-y-0.5 font-black text-sm uppercase tracking-widest">
                    Simpan Material
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Edit Material Modals --}}
    @foreach($materials as $material)
    <x-modal name="edit-material-{{ $material->id }}" :show="$errors->any() && old('form_type') === 'edit_material_' . $material->id" focusable>
        <form method="POST" action="{{ route('admin.materials.update', $material) }}" class="p-0">
            @csrf @method('PUT')
            <input type="hidden" name="form_type" value="edit_material_{{ $material->id }}">

            {{-- Modal Header --}}
            <div class="px-8 py-6 border-b border-gray-100 relative">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Edit: {{ $material->name }}</h2>
                <p class="text-xs text-gray-400 mt-1 font-medium">Perbarui informasi material inventori</p>
                <button type="button" x-on:click="$dispatch('close')" class="absolute top-6 right-8 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="p-8 space-y-8" x-data="{ type: '{{ $material->type }}' }">
                {{-- Section 1: INFORMASI UTAMA --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-5 bg-[#0F766E] rounded-full"></div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-[#0F766E]">Informasi Utama</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nama Material</label>
                            <input type="text" name="name" value="{{ $material->name }}" required
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipe</label>
                            <select name="type" x-model="type" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="Material Upper" {{ $material->type == 'Material Upper' ? 'selected' : '' }}>Material Upper</option>
                                <option value="Material Sol" {{ $material->type == 'Material Sol' ? 'selected' : '' }}>Material Sol</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kategori Material</label>
                            <select name="category" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="PRODUCTION" {{ $material->category == 'PRODUCTION' ? 'selected' : '' }}>Produksi (Cek Stok)</option>
                                <option value="SHOPPING" {{ $material->category == 'SHOPPING' ? 'selected' : '' }}>Belanja (Budget)</option>
                            </select>
                        </div>

                        <div x-show="type === 'Material Sol'" x-cloak>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kategori Sol</label>
                            <select name="sub_category" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                                <option value="Sol Potong" {{ $material->sub_category == 'Sol Potong' ? 'selected' : '' }}>Sol Potong</option>
                                <option value="Sol Jadi" {{ $material->sub_category == 'Sol Jadi' ? 'selected' : '' }}>Sol Jadi</option>
                                <option value="Foxing" {{ $material->sub_category == 'Foxing' ? 'selected' : '' }}>Foxing</option>
                                <option value="Vibram" {{ $material->sub_category == 'Vibram' ? 'selected' : '' }}>Vibram</option>
                            </select>
                        </div>

                        <div x-show="type === 'Material Sol'" x-cloak>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Size <span class="text-[8px] opacity-50 lowercase tracking-normal font-medium">(Opsional)</span></label>
                            <input type="text" name="size" value="{{ $material->size }}"
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Stock</label>
                            <input type="number" name="stock" value="{{ $material->stock }}" required
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Unit</label>
                            <input type="text" name="unit" value="{{ $material->unit }}" required
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Harga per Unit</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none border-r border-gray-100 pr-3">
                                    <span class="text-[10px] font-black text-gray-400 group-focus-within:text-teal-500 transition-colors uppercase tracking-widest">IDR</span>
                                </div>
                                <input type="number" name="price" value="{{ $material->price }}" required step="0.01"
                                    class="w-full pl-16 pr-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Minimal Stock</label>
                            <input type="number" name="min_stock" value="{{ $material->min_stock }}" required
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-red-100 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-red-400 transition-all font-semibold">
                        </div>
                    </div>
                </div>

                {{-- Section 2: ADMINISTRASI --}}
                <div class="space-y-6 pt-4">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-5 bg-[#C2410C] rounded-full"></div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-[#C2410C]">Administrasi & PIC</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Status</label>
                            <select name="status" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="Ready" {{ $material->status == 'Ready' ? 'selected' : '' }}>Aktif / Ready</option>
                                <option value="Belanja" {{ $material->status == 'Belanja' ? 'selected' : '' }}>Belanja</option>
                                <option value="Followup" {{ $material->status == 'Followup' ? 'selected' : '' }}>Followup</option>
                                <option value="Reject" {{ $material->status == 'Reject' ? 'selected' : '' }}>Reject</option>
                                <option value="Retur" {{ $material->status == 'Retur' ? 'selected' : '' }}>Retur</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">PIC Material <span class="text-[8px] opacity-50 lowercase tracking-normal font-medium">(Opsional)</span></label>
                            <select name="pic_user_id" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="">Pilih Penanggung Jawab</option>
                                @foreach($pics as $p)
                                    <option value="{{ $p->id }}" {{ $material->pic_user_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 py-6 bg-gray-50/80 dark:bg-gray-700/50 flex justify-end items-center gap-6 border-t border-gray-100">
                <button type="button" x-on:click="$dispatch('close')" class="text-sm font-black text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">Batal</button>
                <button type="submit" class="px-8 py-3 bg-[#0F766E] hover:bg-[#0D635C] text-white rounded-xl shadow-lg shadow-teal-900/10 transition-all transform hover:-translate-y-0.5 font-black text-sm uppercase tracking-widest">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </x-modal>
    @endforeach

    {{-- Import Modal (Standardized) --}}
    <x-modal name="import-material-modal" focusable>
        <form method="POST" action="{{ route('admin.materials.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="px-8 py-6 border-b border-gray-100 relative">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Import Material</h2>
                <p class="text-xs text-gray-400 mt-1 font-medium">Unggah berkas excel untuk pembaruan massal</p>
                <button type="button" x-on:click="$dispatch('close')" class="absolute top-6 right-8 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-8">
                <input type="file" name="file" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-black file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 transition-all">
            </div>
            <div class="px-8 py-6 bg-gray-50 dark:bg-gray-800 flex justify-end items-center gap-6 border-t border-gray-100">
                <button type="button" x-on:click="$dispatch('close')" class="text-sm font-black text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">Batal</button>
                <button type="submit" class="px-8 py-3 bg-[#0F766E] hover:bg-[#0D635C] text-white rounded-xl shadow-lg shadow-teal-900/10 transition-all transform hover:-translate-y-0.5 font-black text-sm uppercase tracking-widest">Unggah Berkas</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>

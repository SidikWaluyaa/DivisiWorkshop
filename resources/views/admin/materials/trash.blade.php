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
                        <li>
                            <a href="{{ route('admin.materials.index') }}" class="hover:text-teal-600 transition-colors">Material</a>
                        </li>
                        <li>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        </li>
                        <li class="text-red-600 font-bold uppercase tracking-wider">Tempat Sampah</li>
                    </ol>
                </nav>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                            🗑️ Tempat Sampah Material
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-red-50 text-red-600 border border-red-100 uppercase tracking-widest">
                                TOTAL TERHAPUS: {{ $materials->total() }}
                            </span>
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 text-xs mt-1 font-medium">Daftar material yang telah dihapus (soft-deleted). Anda dapat memulihkannya atau menghapusnya secara permanen.</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.materials.index') }}" 
                           class="flex items-center gap-2 px-5 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[1600px] mx-auto p-6"
             x-data="{ 
                selected: [], 
                selectAllMatching: false,
                lastSelectedIndex: null,
                totalResults: {{ $materials->total() }},

                toggleRow(id, index, event) {
                    if (event.shiftKey && this.lastSelectedIndex !== null) {
                        const checkboxes = Array.from(document.querySelectorAll('.material-checkbox'));
                        const start = Math.min(this.lastSelectedIndex, index);
                        const end = Math.max(this.lastSelectedIndex, index);
                        
                        checkboxes.slice(start, end + 1).forEach(cb => {
                            const val = parseInt(cb.value);
                            if (!this.selected.includes(val)) this.selected.push(val);
                        });
                    } else {
                        if (this.selected.includes(id)) {
                            this.selected = this.selected.filter(i => i !== id);
                        } else {
                            this.selected.push(id);
                        }
                    }
                    this.lastSelectedIndex = index;
                    this.selectAllMatching = false;
                },

                selectAllOnPage() {
                    const checkboxes = document.querySelectorAll('.material-checkbox');
                    const allOnPage = Array.from(checkboxes).map(cb => parseInt(cb.value));
                    
                    if (allOnPage.every(id => this.selected.includes(id))) {
                        this.selected = this.selected.filter(id => !allOnPage.includes(id));
                    } else {
                        allOnPage.forEach(id => {
                            if (!this.selected.includes(id)) this.selected.push(id);
                        });
                    }
                    this.selectAllMatching = false;
                },

                isAllOnPageSelected() {
                    const checkboxes = document.querySelectorAll('.material-checkbox');
                    if (checkboxes.length === 0) return false;
                    return Array.from(checkboxes).every(cb => this.selected.includes(parseInt(cb.value)));
                }
             }">

            {{-- Alert messages --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-xs font-bold">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-xs font-bold">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Smart Selection Banner --}}
            <template x-if="isAllOnPageSelected() && totalResults > selected.length">
                <div x-transition class="bg-red-600 text-white px-6 py-3 rounded-xl mb-6 flex justify-between items-center shadow-lg shadow-red-900/10">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-bold">
                            Semua <span x-text="selected.length"></span> material sampah di halaman ini terpilih.
                        </p>
                    </div>
                    <button type="button" @click="selectAllMatching = true; selected = Array(totalResults).fill(0)" 
                            class="px-4 py-1.5 bg-white/20 hover:bg-white text-white hover:text-red-700 rounded-lg text-xs font-black uppercase tracking-widest transition-all">
                        Pilih semua <span x-text="totalResults"></span> material sampah terhapus
                    </button>
                </div>
            </template>

            <template x-if="selectAllMatching">
                <div x-transition class="bg-gray-900 text-white px-6 py-3 rounded-xl mb-6 flex justify-between items-center shadow-lg">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-bold">
                            Semua <span x-text="totalResults"></span> material sampah di tempat sampah telah terpilih.
                        </p>
                    </div>
                    <button type="button" @click="selectAllMatching = false; selected = []" 
                            class="px-4 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-xs font-black uppercase tracking-widest transition-all">
                        Batalkan Seleksi
                    </button>
                </div>
            </template>

            {{-- Table View --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 w-12 text-center">
                                    <input type="checkbox" @click="selectAllOnPage" :checked="isAllOnPageSelected" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                </th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Material</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Ukuran</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Stock Terakhir</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Harga Beli</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Tanggal Dihapus</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($materials as $index => $material)
                            <tr @click="toggleRow({{ $material->id }}, {{ $index }}, $event)"
                                class="cursor-pointer transition-all duration-200"
                                :class="selected.includes({{ $material->id }}) ? 'bg-red-50/30 dark:bg-red-950/10' : 'hover:bg-gray-50/50 dark:hover:bg-gray-700/30'">
                                <td class="px-6 py-5 text-center" @click.stop>
                                    <input type="checkbox" value="{{ $material->id }}" x-model="selected" class="material-checkbox rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-gray-900 dark:text-white mb-0.5">{{ $material->name }}</div>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black {{ $material->type === 'Material Upper' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-purple-50 text-purple-700 border border-purple-100' }}">
                                            {{ $material->type }}
                                        </span>
                                        @if($material->category)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-orange-50 text-orange-700 border border-orange-100">
                                            {{ $material->category }}
                                        </span>
                                        @endif
                                        @if($material->sub_category)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-100">{{ $material->sub_category }}</span>
                                        @endif
                                        <span class="text-[10px] text-gray-400 font-medium">Min: {{ $material->min_stock }} {{ $material->unit }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($material->size)
                                        <span class="px-2.5 py-1 text-xs font-black bg-gray-100 text-gray-800 rounded-lg dark:bg-gray-700 dark:text-gray-200">
                                            {{ $material->size }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic font-medium">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-sm font-black text-gray-900 dark:text-gray-100">
                                        {{ $material->stock }} <span class="text-[10px] font-bold text-gray-400 uppercase ml-0.5">{{ $material->unit }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right whitespace-nowrap">
                                    <div class="text-sm font-black text-gray-900 dark:text-gray-100 italic">Rp {{ number_format($material->price, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $material->deleted_at->format('d M Y H:i') }}</div>
                                    <div class="text-[10px] text-gray-400 font-medium">{{ $material->deleted_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-5 text-right space-x-2 whitespace-nowrap" @click.stop>
                                    {{-- Restore Button --}}
                                    <form action="{{ route('admin.materials.restore', $material->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 border border-emerald-100 rounded-lg text-xs font-bold transition-all shadow-sm"
                                                title="Kembalikan Material">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3 3L22 4"></path></svg>
                                            Pulihkan
                                        </button>
                                    </form>

                                    {{-- Force Delete Button --}}
                                    <form action="{{ route('admin.materials.force-delete', $material->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda YAKIN ingin menghapus material ini secara PERMANEN? Tindakan ini akan menghapus semua riwayat transaksi terkait material ini dan TIDAK dapat dibatalkan!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 rounded-lg text-xs font-bold transition-all shadow-sm"
                                                title="Hapus Permanen">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus Permanen
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">Tempat sampah kosong.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-5 bg-gray-50/50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                    {{ $materials->links() }}
                </div>
            </div>

            <!-- Floating Command Center for Bulk Action -->
            <div x-show="selected.length > 0" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-y-full opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-y-0 opacity-100"
                 x-transition:leave-end="translate-y-full opacity-0"
                 class="fixed bottom-10 left-1/2 -translate-x-1/2 z-50 flex items-center gap-6 px-8 py-4 bg-gray-900/95 backdrop-blur-md text-white rounded-full shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white/10">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center font-black text-xs text-white" x-text="selectAllMatching ? totalResults : selected.length"></span>
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-300">Material Sampah Terpilih</span>
                </div>
                
                <div class="w-px h-8 bg-white/10"></div>
                
                <div class="flex items-center gap-3">
                    {{-- Bulk Restore --}}
                    <form action="{{ route('admin.materials.bulk-restore') }}" method="POST" class="inline" @submit.prevent="if(confirm(selectAllMatching ? 'Pulihkan SEMUA ' + totalResults + ' material terhapus?' : 'Pulihkan ' + selected.length + ' material terpilih?')) $el.submit()">
                        @csrf
                        <template x-if="selectAllMatching">
                            <input type="hidden" name="select_all_matching" value="1">
                        </template>
                        <template x-if="!selectAllMatching">
                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                        </template>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg shadow-emerald-600/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3 3L22 4"></path></svg>
                            Pulihkan Massal
                        </button>
                    </form>

                    {{-- Bulk Force Delete --}}
                    <form action="{{ route('admin.materials.bulk-force-delete') }}" method="POST" class="inline" @submit.prevent="if(confirm(selectAllMatching ? 'PERINGATAN: Hapus secara PERMANEN SEMUA ' + totalResults + ' material terhapus? Tindakan ini tidak dapat dibatalkan!' : 'PERINGATAN: Hapus secara PERMANEN ' + selected.length + ' material terpilih? Tindakan ini tidak dapat dibatalkan!')) $el.submit()">
                        @csrf
                        @method('DELETE')
                        <template x-if="selectAllMatching">
                            <input type="hidden" name="select_all_matching" value="1">
                        </template>
                        <template x-if="!selectAllMatching">
                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                        </template>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg shadow-red-600/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus Permanen Massal
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

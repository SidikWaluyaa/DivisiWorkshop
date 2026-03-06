<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-xl text-white leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ __('Master Data Kendala (Issues)') }}
            </h2>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-white/10 rounded-lg backdrop-blur-sm border border-white/20 text-white text-sm">
                    <span class="opacity-75">Total Kendala:</span> 
                    <span class="font-bold ml-1">{{ $issues->count() }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        categoryFilter: 'all',
        search: '',
        issues: {{ collect($issues)->map(fn($i) => ['id' => $i->id, 'category' => $i->category, 'name' => strtolower($i->name)])->toJson() }},
        get isMatch() {
            return (id) => {
                const issue = this.issues.find(i => i.id === id);
                if (!issue) return false;
                const matchCategory = this.categoryFilter === 'all' || issue.category === this.categoryFilter;
                const matchSearch = this.search === '' || issue.name.includes(this.search.toLowerCase());
                return matchCategory && matchSearch;
            }
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Toolbar & Search --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 overflow-hidden mb-6 p-5">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    {{-- Left: Search & Filter --}}
                    <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                        <div class="relative w-full md:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" x-model="search"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 text-sm transition-all bg-gray-50 focus:bg-white" 
                                   placeholder="Cari kendala...">
                        </div>
                        
                        <select x-model="categoryFilter" class="w-full md:w-48 py-2.5 rounded-xl border-gray-200 focus:border-teal-500 text-sm bg-gray-50 focus:bg-white transition-all">
                            <option value="all">Semua Kategori</option>
                            <option value="TEKNIS">Teknis</option>
                            <option value="MATERIAL">Material</option>
                        </select>
                    </div>

                    {{-- Right: Actions --}}
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <button x-on:click.prevent="$dispatch('open-modal', 'create-issue-modal')" 
                                class="w-full md:w-auto px-6 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white rounded-xl shadow-lg shadow-teal-500/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2 font-medium text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Kendala
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gradient-to-r from-teal-50 to-emerald-50 dark:from-gray-700 dark:to-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Detail Kendala</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Status (Aktif/Nonaktif)</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($issues as $issue)
                            <tr x-show="isMatch({{ $issue->id }})" class="hover:bg-teal-50/30 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $issue->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-[10px] font-black tracking-wider rounded-lg {{ $issue->category === 'TEKNIS' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                                        {{ $issue->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <label class="relative inline-flex items-center justify-center cursor-pointer group m-0">
                                        <input type="checkbox" class="sr-only peer" {{ $issue->is_active ? 'checked' : '' }} onchange="toggleActive(this, {{ $issue->id }})">
                                        <div class="w-11 h-6 bg-red-100 rounded-full peer peer-checked:bg-teal-100 transition-colors border border-red-200 peer-checked:border-teal-200"></div>
                                        <div class="absolute left-[6px] top-[6px] w-3 h-3 bg-red-500 rounded-full transition-transform peer-checked:translate-x-5 peer-checked:bg-teal-500 shadow-sm"></div>
                                    </label>
                                    <div class="text-[10px] uppercase font-bold mt-1 text-gray-500 status-text">{{ $issue->is_active ? 'Aktif' : 'Nonaktif' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button x-on:click.prevent="$dispatch('open-modal', 'edit-issue-{{ $issue->id }}')" 
                                            class="text-teal-600 hover:text-teal-900 dark:hover:text-teal-400 mr-2 transition-colors p-1 hover:bg-teal-50 rounded-lg inline-flex">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('admin.master-issues.destroy', $issue) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kendala ini permanen? Data riwayat tetap aman.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition-colors p-1 hover:bg-red-50 rounded-lg inline-flex">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">Belum ada data Master Kendala.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <x-modal name="create-issue-modal" :show="$errors->any() && !old('id')" focusable>
            <form method="POST" action="{{ route('admin.master-issues.store') }}">
                @csrf
                <div class="flex justify-between items-center p-4 rounded-t-xl bg-gradient-to-r from-teal-500 to-emerald-600 text-white">
                    <h2 class="text-lg font-bold">Tambah Master Kendala</h2>
                    <button type="button" x-on:click="$dispatch('close')" class="text-white/80 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="category" :value="__('Kategori Masalah')" />
                            <select id="category" name="category" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-teal-500 shadow-sm" required>
                                <option value="TEKNIS" {{ old('category') == 'TEKNIS' ? 'selected' : '' }}>Teknis</option>
                                <option value="MATERIAL" {{ old('category') == 'MATERIAL' ? 'selected' : '' }}>Material</option>
                            </select>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="name" :value="__('Nama/Detail Kendala')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required placeholder="Contoh: Sol Menguning, Outsole Lepas" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <x-input-label for="is_active" :value="__('Langsung Aktifkan?')" />
                            <label class="relative inline-flex items-center cursor-pointer group m-0">
                                <input type="checkbox" name="is_active" id="is_active" value="1" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-red-100 rounded-full peer peer-checked:bg-teal-100 transition-colors border border-red-200 peer-checked:border-teal-200"></div>
                                <div class="absolute left-[6px] top-[6px] w-3 h-3 bg-red-500 rounded-full transition-transform peer-checked:translate-x-5 peer-checked:bg-teal-500 shadow-sm"></div>
                            </label>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                        <x-primary-button class="bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 shadow-md">Simpan Data</x-primary-button>
                    </div>
                </div>
            </form>
        </x-modal>

        <!-- Edit Modals -->
        @foreach($issues as $issue)
        <x-modal name="edit-issue-{{ $issue->id }}" :show="$errors->any() && old('id') == $issue->id" focusable>
            <form method="POST" action="{{ route('admin.master-issues.update', $issue) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $issue->id }}">
                <div class="flex justify-between items-center p-4 rounded-t-xl bg-gradient-to-r from-teal-500 to-emerald-600 text-white">
                    <h2 class="text-lg font-bold">Edit Master Kendala</h2>
                    <button type="button" x-on:click="$dispatch('close')" class="text-white/80 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="category_{{ $issue->id }}" :value="__('Kategori Masalah')" />
                            <select id="category_{{ $issue->id }}" name="category" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-teal-500 shadow-sm" required>
                                <option value="TEKNIS" {{ (old('category') ?? $issue->category) == 'TEKNIS' ? 'selected' : '' }}>Teknis</option>
                                <option value="MATERIAL" {{ (old('category') ?? $issue->category) == 'MATERIAL' ? 'selected' : '' }}>Material</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="name_{{ $issue->id }}" :value="__('Nama/Detail Kendala')" />
                            <x-text-input id="name_{{ $issue->id }}" class="block mt-1 w-full" type="text" name="name" :value="old('name') ?? $issue->name" required />
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <x-input-label for="is_active_{{ $issue->id }}" :value="__('Status Aktif')" />
                            <label class="relative inline-flex items-center cursor-pointer group m-0">
                                <input type="checkbox" name="is_active" id="is_active_{{ $issue->id }}" value="1" class="sr-only peer" {{ (old('is_active') !== null ? old('is_active') : $issue->is_active) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-red-100 rounded-full peer peer-checked:bg-teal-100 transition-colors border border-red-200 peer-checked:border-teal-200"></div>
                                <div class="absolute left-[6px] top-[6px] w-3 h-3 bg-red-500 rounded-full transition-transform peer-checked:translate-x-5 peer-checked:bg-teal-500 shadow-sm"></div>
                            </label>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                        <x-primary-button class="bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 shadow-md">Simpan Perubahan</x-primary-button>
                    </div>
                </div>
            </form>
        </x-modal>
        @endforeach
    </div>

    <script>
    function toggleActive(checkbox, id) {
        const isChecked = checkbox.checked;
        const textContainer = checkbox.closest('td').querySelector('.status-text');
        
        // Optimistic UI
        textContainer.textContent = isChecked ? 'Aktif' : 'Nonaktif';

        fetch(`/admin/master-issues/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                checkbox.checked = !isChecked; // Revert
                textContainer.textContent = checkbox.checked ? 'Aktif' : 'Nonaktif';
                alert('Gagal merubah status.');
            }
        })
        .catch(error => {
            console.error(error);
            checkbox.checked = !isChecked; // Revert
            textContainer.textContent = checkbox.checked ? 'Aktif' : 'Nonaktif';
        });
    }
    </script>
</x-app-layout>

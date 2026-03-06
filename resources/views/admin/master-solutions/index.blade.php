<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-xl text-white leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('Master Data Solusi (Solutions)') }}
            </h2>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-white/10 rounded-lg backdrop-blur-sm border border-white/20 text-white text-sm">
                    <span class="opacity-75">Total Solusi:</span> 
                    <span class="font-bold ml-1">{{ $solutions->count() }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        search: '',
        category: '',
        solutions: {{ collect($solutions)->map(fn($s) => ['id' => $s->id, 'name' => strtolower($s->name), 'category' => $s->category])->toJson() }},
        get isMatch() {
            return (id) => {
                const solution = this.solutions.find(s => s.id === id);
                if (!solution) return false;
                const matchSearch = this.search === '' || solution.name.includes(this.search.toLowerCase());
                const matchCategory = this.category === '' || solution.category === this.category;
                return matchSearch && matchCategory;
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
                                   placeholder="Cari solusi...">
                        </div>
                        <select x-model="category" class="w-full md:w-48 py-2.5 rounded-xl border-gray-200 focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 text-sm bg-gray-50">
                            <option value="">Semua Kategori</option>
                            <option value="TEKNIS">TEKNIS</option>
                            <option value="MATERIAL">MATERIAL</option>
                            <option value="OVERLOAD">OVERLOAD</option>
                            <option value="QC">QC</option>
                        </select>
                    </div>

                    {{-- Right: Actions --}}
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <button x-on:click.prevent="$dispatch('open-modal', 'create-solution-modal')" 
                                class="w-full md:w-auto px-6 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white rounded-xl shadow-lg shadow-teal-500/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2 font-medium text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Solusi
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
                                <th class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Opsi Solusi</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Status (Aktif/Nonaktif)</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($solutions as $solution)
                            <tr x-show="isMatch({{ $solution->id }})" class="hover:bg-teal-50/30 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $solution->category === 'TEKNIS' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $solution->category === 'MATERIAL' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $solution->category === 'OVERLOAD' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $solution->category === 'QC' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $solution->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $solution->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <label class="relative inline-flex items-center justify-center cursor-pointer group m-0">
                                        <input type="checkbox" class="sr-only peer" {{ $solution->is_active ? 'checked' : '' }} onchange="toggleActive(this, {{ $solution->id }})">
                                        <div class="w-11 h-6 bg-red-100 rounded-full peer peer-checked:bg-teal-100 transition-colors border border-red-200 peer-checked:border-teal-200"></div>
                                        <div class="absolute left-[6px] top-[6px] w-3 h-3 bg-red-500 rounded-full transition-transform peer-checked:translate-x-5 peer-checked:bg-teal-500 shadow-sm"></div>
                                    </label>
                                    <div class="text-[10px] uppercase font-bold mt-1 text-gray-500 status-text">{{ $solution->is_active ? 'Aktif' : 'Nonaktif' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button x-on:click.prevent="$dispatch('open-modal', 'edit-solution-{{ $solution->id }}')" 
                                            class="text-teal-600 hover:text-teal-900 dark:hover:text-teal-400 mr-2 transition-colors p-1 hover:bg-teal-50 rounded-lg inline-flex">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('admin.master-solutions.destroy', $solution) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus solusi ini permanen? Data riwayat tetap aman.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition-colors p-1 hover:bg-red-50 rounded-lg inline-flex">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">Belum ada data Master Solusi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <x-modal name="create-solution-modal" :show="$errors->any() && !old('id')" focusable>
            <form method="POST" action="{{ route('admin.master-solutions.store') }}">
                @csrf
                <div class="flex justify-between items-center p-4 rounded-t-xl bg-gradient-to-r from-teal-500 to-emerald-600 text-white">
                    <h2 class="text-lg font-bold">Tambah Master Solusi</h2>
                    <button type="button" x-on:click="$dispatch('close')" class="text-white/80 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="category" :value="__('Kategori')" />
                            <select id="category" name="category" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="TEKNIS" {{ old('category') == 'TEKNIS' ? 'selected' : '' }}>TEKNIS</option>
                                <option value="MATERIAL" {{ old('category') == 'MATERIAL' ? 'selected' : '' }}>MATERIAL</option>
                                <option value="OVERLOAD" {{ old('category') == 'OVERLOAD' ? 'selected' : '' }}>OVERLOAD</option>
                                <option value="QC" {{ old('category') == 'QC' ? 'selected' : '' }}>QC</option>
                            </select>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="name" :value="__('Opsi Solusi')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required placeholder="Contoh: Reglue Full, Ganti Outsole" />
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
        @foreach($solutions as $solution)
        <x-modal name="edit-solution-{{ $solution->id }}" :show="$errors->any() && old('id') == $solution->id" focusable>
            <form method="POST" action="{{ route('admin.master-solutions.update', $solution) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $solution->id }}">
                <div class="flex justify-between items-center p-4 rounded-t-xl bg-gradient-to-r from-teal-500 to-emerald-600 text-white">
                    <h2 class="text-lg font-bold">Edit Master Solusi</h2>
                    <button type="button" x-on:click="$dispatch('close')" class="text-white/80 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="category_{{ $solution->id }}" :value="__('Kategori')" />
                            <select id="category_{{ $solution->id }}" name="category" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="TEKNIS" {{ (old('category') ?? $solution->category) == 'TEKNIS' ? 'selected' : '' }}>TEKNIS</option>
                                <option value="MATERIAL" {{ (old('category') ?? $solution->category) == 'MATERIAL' ? 'selected' : '' }}>MATERIAL</option>
                                <option value="OVERLOAD" {{ (old('category') ?? $solution->category) == 'OVERLOAD' ? 'selected' : '' }}>OVERLOAD</option>
                                <option value="QC" {{ (old('category') ?? $solution->category) == 'QC' ? 'selected' : '' }}>QC</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="name_{{ $solution->id }}" :value="__('Opsi Solusi')" />
                            <x-text-input id="name_{{ $solution->id }}" class="block mt-1 w-full" type="text" name="name" :value="old('name') ?? $solution->name" required />
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <x-input-label for="is_active_{{ $solution->id }}" :value="__('Status Aktif')" />
                            <label class="relative inline-flex items-center cursor-pointer group m-0">
                                <input type="checkbox" name="is_active" id="is_active_{{ $solution->id }}" value="1" class="sr-only peer" {{ (old('is_active') !== null ? old('is_active') : $solution->is_active) ? 'checked' : '' }}>
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

        fetch(`/admin/master-solutions/${id}/toggle`, {
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

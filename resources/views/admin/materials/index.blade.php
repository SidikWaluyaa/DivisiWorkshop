<x-app-layout>
    <x-slot name="header">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Master Data Material') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ selected: [] }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-white">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    {{-- Toolbar: Bulk Actions & Add Button --}}
                    <div class="flex justify-between items-center mb-4">
                        <form action="{{ route('admin.materials.bulk-destroy') }}" method="POST" onsubmit="return confirm('Yakin hapus ' + this.selected.length + ' material terpilih?')">
                            @csrf
                            @method('DELETE')
                            <template x-for="id in selected">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" 
                                    :disabled="selected.length === 0"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow-md flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus (<span x-text="selected.length">0</span>) Terpilih
                            </button>
                        </form>

                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-material-modal')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            + Tambah Material
                        </button>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left">
                                    <input type="checkbox" 
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                           @click="selected = $el.checked ? {{ json_encode($materials->pluck('id')) }} : []"
                                           :checked="selected.length === {{ $materials->count() }} && {{ $materials->count() }} > 0">
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kategori</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Harga</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Min Stock</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">PIC</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($materials as $material)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" value="{{ $material->id }}" x-model="selected" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $material->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $material->category }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $material->stock }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $material->unit }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-semibold">Rp {{ number_format($material->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $material->min_stock }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        // Auto-detect status if not manually overridden or just for display logic
                                        $displayStatus = $material->status;
                                        $colorClass = 'bg-gray-100 text-gray-800';
                                        
                                        if ($material->stock <= 0) {
                                             $displayStatus = 'Habis (0)';
                                             $colorClass = 'bg-red-100 text-red-800';
                                        } elseif ($material->stock <= $material->min_stock) {
                                             $displayStatus = 'Menipis';
                                             $colorClass = 'bg-yellow-100 text-yellow-800';
                                        } else {
                                             // Use the manual status for other states
                                            if($material->status === 'Ready') $colorClass = 'bg-green-100 text-green-800';
                                            elseif($material->status === 'Belanja') $colorClass = 'bg-blue-100 text-blue-800';
                                            elseif($material->status === 'Followup') $colorClass = 'bg-purple-100 text-purple-800';
                                            elseif($material->status === 'Reject') $colorClass = 'bg-red-100 text-red-800';
                                            elseif($material->status === 'Retur') $colorClass = 'bg-gray-200 text-gray-800';
                                        }
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                        {{ $displayStatus }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($material->pic)
                                        <div class="text-gray-900 dark:text-gray-100 font-medium">{{ $material->pic->name }}</div>
                                        @if($material->pic->phone)
                                            @php
                                                $needsFollowup = $material->stock <= 0 || $material->stock <= $material->min_stock || in_array($material->status, ['Belanja', 'Followup']);
                                                $message = "Halo {$material->pic->name}, Material *{$material->name}* statusnya *{$displayStatus}* (Stock: {$material->stock} {$material->unit}). Mohon info update.";
                                                $waLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $material->pic->phone) . "?text=" . urlencode($message);
                                            @endphp
                                            <a href="{{ $waLink }}" target="_blank" 
                                               class="text-xs {{ $needsFollowup ? 'text-green-600 hover:text-green-800 font-bold' : 'text-gray-400 hover:text-gray-600' }}">
                                                💬 {{ $needsFollowup ? 'Follow Up WA' : 'Chat WA' }}
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-xs italic">Belum ada PIC</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-material-modal-{{ $material->id }}')" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                                    <form action="{{ route('admin.materials.destroy', $material) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus material ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <x-modal name="edit-material-modal-{{ $material->id }}" :show="false" focusable>
                                <form method="POST" action="{{ route('admin.materials.update', $material) }}" class="p-6">
                                    @csrf
                                    @method('PUT')
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Material</h2>
                                    <div class="mt-4">
                                        <x-input-label for="name" :value="__('Nama Material')" />
                                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$material->name" required />
                                    </div>
                                    <div class="mt-4">
                                        <x-input-label for="category" :value="__('Kategori')" />
                                        <select id="category" name="category" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                            <option value="Material Sol" {{ $material->category == 'Material Sol' ? 'selected' : '' }}>Material Sol</option>
                                            <option value="Material Upper" {{ $material->category == 'Material Upper' ? 'selected' : '' }}>Material Upper</option>
                                            <option value="Umum" {{ $material->category == 'Umum' ? 'selected' : '' }}>Umum</option>
                                        </select>
                                    </div>
                                    <div class="mt-4">
                                        <x-input-label for="stock" :value="__('Stock')" />
                                        <x-text-input id="stock" class="block mt-1 w-full" type="number" name="stock" :value="$material->stock" required />
                                    </div>
                                    <div class="mt-4">
                                        <x-input-label for="unit" :value="__('Unit')" />
                                        <x-text-input id="unit" class="block mt-1 w-full" type="text" name="unit" :value="$material->unit" required />
                                    </div>
                                    <div class="mt-4">
                                        <x-input-label for="price" :value="__('Harga per Unit')" />
                                        <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" :value="$material->price" required />
                                    </div>
                                    <div class="mt-4">
                                        <x-input-label for="min_stock" :value="__('Minimal Stock')" />
                                        <x-text-input id="min_stock" class="block mt-1 w-full" type="number" name="min_stock" :value="$material->min_stock" required />
                                    </div>
                                    <div class="mt-4">
                                        <x-input-label for="status" :value="__('Status')" />
                                        <select id="status" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                            <option value="Ready" {{ $material->status == 'Ready' ? 'selected' : '' }}>Ready</option>
                                            <option value="Belanja" {{ $material->status == 'Belanja' ? 'selected' : '' }}>Belanja</option>
                                            <option value="Followup" {{ $material->status == 'Followup' ? 'selected' : '' }}>Followup</option>
                                            <option value="Reject" {{ $material->status == 'Reject' ? 'selected' : '' }}>Reject</option>
                                            <option value="Retur" {{ $material->status == 'Retur' ? 'selected' : '' }}>Retur</option>
                                        </select>
                                    </div>
                                    <div class="mt-4">
                                        <x-input-label for="pic_user_id" :value="__('PIC Material (Optional)')" />
                                        <select id="pic_user_id" name="pic_user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                            <option value="">-- Pilih PIC --</option>
                                            @foreach($pics as $pic)
                                                <option value="{{ $pic->id }}" {{ $material->pic_user_id == $pic->id ? 'selected' : '' }}>
                                                    {{ $pic->name }} @if($pic->phone) ({{ $pic->phone }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Pilih user dengan role "PIC Material"</p>
                                    </div>
                                    <div class="mt-6 flex justify-end">
                                        <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                                        <x-primary-button class="ms-3">{{ __('Simpan Perubahan') }}</x-primary-button>
                                    </div>
                                </form>
                            </x-modal>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $materials->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-material-modal" :show="false" focusable>
        <form method="POST" action="{{ route('admin.materials.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tambah Material Baru</h2>
             <div class="mt-4">
                <x-input-label for="name" :value="__('Nama Material')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required />
            </div>
            <div class="mt-4">
                <x-input-label for="category" :value="__('Kategori')" />
                <select id="category" name="category" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="Material Sol">Material Sol</option>
                    <option value="Material Upper">Material Upper</option>
                    <option value="Umum">Umum</option>
                </select>
            </div>
            <div class="mt-4">
                <x-input-label for="stock" :value="__('Stock')" />
                <x-text-input id="stock" class="block mt-1 w-full" type="number" name="stock" required />
            </div>
            <div class="mt-4">
                <x-input-label for="unit" :value="__('Unit')" />
                <x-text-input id="unit" class="block mt-1 w-full" type="text" name="unit" required />
            </div>
            <div class="mt-4">
                <x-input-label for="price" :value="__('Harga per Unit')" />
                <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" required />
            </div>
            <div class="mt-4">
                <x-input-label for="min_stock" :value="__('Minimal Stock')" />
                <x-text-input id="min_stock" class="block mt-1 w-full" type="number" name="min_stock" required />
            </div>
            <div class="mt-4">
                <x-input-label for="status" :value="__('Status')" />
                <select id="status" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="Ready">Ready</option>
                    <option value="Belanja">Belanja</option>
                    <option value="Followup">Followup</option>
                    <option value="Reject">Reject</option>
                    <option value="Retur">Retur</option>
                </select>
            </div>
            <div class="mt-4">
                <x-input-label for="pic_user_id" :value="__('PIC Material (Optional)')" />
                <select id="pic_user_id" name="pic_user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">-- Pilih PIC --</option>
                    @foreach($pics as $pic)
                        <option value="{{ $pic->id }}">
                            {{ $pic->name }} @if($pic->phone) ({{ $pic->phone }}) @endif
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih user dengan role "PIC Material"</p>
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                <x-primary-button class="ms-3">{{ __('Simpan') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Master Data Layanan') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ selected: [] }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-white">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    {{-- Toolbar: Bulk Actions & Add Button --}}
                    <div class="flex justify-between items-center mb-4">
                        {{-- Left: Bulk Delete --}}
                        <form action="{{ route('admin.services.bulk-destroy') }}" method="POST" onsubmit="return confirm('Yakin hapus ' + this.selected.length + ' layanan terpilih?')">
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

                        {{-- Right: Add Button --}}
                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-service-modal')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            + Tambah Layanan
                        </button>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left">
                                    <input type="checkbox" 
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                           @click="selected = $el.checked ? {{ json_encode($services->pluck('id')) }} : []"
                                           :checked="selected.length === {{ $services->count() }} && {{ $services->count() }} > 0">
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kategori</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Harga</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Durasi (Menit)</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($services as $service)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" value="{{ $service->id }}" x-model="selected" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $service->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $service->category }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $service->duration_minutes }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-service-modal-{{ $service->id }}')" class="text-indigo-600 hover:text-indigo-900 mr-3 font-semibold">Edit</button>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold cursor-pointer ml-2">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <x-modal name="edit-service-modal-{{ $service->id }}" :show="false" focusable>
                                <form method="POST" action="{{ route('admin.services.update', $service) }}" class="p-6">
                                    @csrf
                                    @method('PUT')
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Layanan</h2>
                                    <div class="mt-4">
                                        <x-input-label for="name" :value="__('Nama Layanan')" />
                                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$service->name" required />
                                    </div>
                                    <div class="mt-4">
                                        <x-input-label for="category" :value="__('Kategori')" />
                                        <x-text-input id="category" class="block mt-1 w-full" type="text" name="category" :value="$service->category" required />
                                    </div>
                                    <div class="mt-4">
                                        <x-input-label for="price" :value="__('Harga')" />
                                        <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="$service->price" required />
                                    </div>
                                    <div class="mt-4">
                                        <x-input-label for="duration_minutes" :value="__('Durasi (Menit)')" />
                                        <x-text-input id="duration_minutes" class="block mt-1 w-full" type="number" name="duration_minutes" :value="$service->duration_minutes" required />
                                    </div>
                                    <div class="mt-4">
                                        <x-input-label for="description" :value="__('Deskripsi')" />
                                        <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ $service->description }}</textarea>
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
                        {{ $services->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-service-modal" :show="false" focusable>
        <form method="POST" action="{{ route('admin.services.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tambah Layanan Baru</h2>
            <div class="mt-4">
                <x-input-label for="name" :value="__('Nama Layanan')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required />
            </div>
             <div class="mt-4">
                <x-input-label for="category" :value="__('Kategori')" />
                <x-text-input id="category" class="block mt-1 w-full" type="text" name="category" required />
            </div>
             <div class="mt-4">
                <x-input-label for="price" :value="__('Harga')" />
                <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" required />
            </div>
             <div class="mt-4">
                <x-input-label for="duration_minutes" :value="__('Durasi (Menit)')" />
                <x-text-input id="duration_minutes" class="block mt-1 w-full" type="number" name="duration_minutes" required />
            </div>
            <div class="mt-4">
                <x-input-label for="description" :value="__('Deskripsi')" />
                <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                <x-primary-button class="ms-3">{{ __('Simpan') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>

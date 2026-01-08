<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Master Data Layanan') }}
            </h2>
             <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-service-modal')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                + Tambah Layanan
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-white">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kategori</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Harga</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Durasi (Menit)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($services as $service)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $service->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $service->category }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $service->duration_minutes }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-service-modal-{{ $service->id }}')" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
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

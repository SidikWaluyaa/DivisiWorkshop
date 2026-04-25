<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-xl text-white leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                {{ __('Master Data Layanan') }}
            </h2>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-white/10 rounded-lg backdrop-blur-sm border border-white/20 text-white text-sm">
                    <span class="opacity-75">Master Data</span> 
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ selected: [] }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Livewire Component --}}
            <livewire:admin.service-index />
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-service-modal" :show="$errors->any() && old('form_type') === 'create_service'" focusable>
        <form method="POST" action="{{ route('admin.services.store') }}">
            @csrf
            <input type="hidden" name="form_type" value="create_service">

            <div class="flex justify-between items-center p-4 rounded-t-xl bg-gradient-to-r from-teal-500 to-emerald-600 text-white">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    Tambah Layanan Baru
                </h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-white/80 hover:text-white transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="p-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-1 md:col-span-2">
                    <x-input-label for="name" :value="__('Nama Layanan')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required />
                </div>
                <div>
                   <x-input-label for="category" :value="__('Kategori')" />
                   <x-text-input id="category" class="block mt-1 w-full" type="text" name="category" placeholder="Contoh: Washing, Repair" required />
               </div>
                <div>
                   <x-input-label for="price" :value="__('Harga (Rp)')" />
                   <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" required />
               </div>
                <div class="col-span-1 md:col-span-2">
                   <x-input-label for="duration_minutes" :value="__('Estimasi Durasi (Menit)')" />
                   <x-text-input id="duration_minutes" class="block mt-1 w-full" type="number" name="duration_minutes" required />
                   <p class="text-xs text-gray-500 mt-1">Estimasi waktu pengerjaan untuk satu item.</p>
               </div>
               <div class="col-span-1 md:col-span-2">
                   <x-input-label for="description" :value="__('Deskripsi')" />
                   <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm"></textarea>
               </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                <x-primary-button class="bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 shadow-md transform hover:-translate-y-0.5 transition-all">{{ __('Simpan Layanan') }}</x-primary-button>
            </div>
            </div>
        </form>
    </x-modal>
    <!-- Import Modal -->
    <x-modal name="import-service-modal" :show="false" focusable>
        <form method="POST" action="{{ route('admin.services.import') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="flex justify-between items-center p-4 rounded-t-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white mb-6">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    </div>
                    Import Layanan
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
                                <a href="{{ route('admin.services.template') }}" class="font-bold underline hover:text-blue-900">Download Template</a>
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

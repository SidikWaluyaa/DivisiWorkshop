<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gudang Penerima (Reception)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Import Section -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Import Data Customer & SPK') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Upload file Excel (.xlsx) sesuai template untuk registrasi masal.
                    </p>
                </header>

                <form method="POST" action="{{ route('reception.import') }}" enctype="multipart/form-data" class="mt-6">
                    @csrf
                    <div class="mb-4">
                        <x-input-label for="file" :value="__('File Excel')" />
                        <input id="file" name="file" type="file"
                            class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            required />
                        <x-input-error class="mt-2" :messages="$errors->get('file')" />
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <div class="flex gap-4 items-center">
                            <x-primary-button>{{ __('Import') }}</x-primary-button>
                            @if (session('success'))
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                                    class="text-sm text-green-600 dark:text-green-400">{{ session('success') }}</p>
                            @endif
                            @if (session('error'))
                                <p class="text-sm text-red-600 dark:text-red-400">{{ session('error') }}</p>
                            @endif
                        </div>
                    </div>
                </form>
                
                <div class="mt-6 pt-6 border-t dark:border-gray-700">
                    <form action="{{ route('reception.reset') }}" method="POST" onsubmit="return confirm('Yakin ingin MENGHAPUS SEMUA DATA ORDER? Tindakan ini tidak bisa dibatalkan.');">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 hover:text-red-900 text-sm font-bold underline">
                            ⚠️ Reset / Hapus Semua Data Order
                        </button>
                    </form>
                </div>
            </div>

            <!-- List of Received Orders -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header class="mb-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Sepatu Masuk (Diterima)') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Sepatu di list ini siap untuk di-Assessment.
                    </p>
                </header>

                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Tanggal Masuk</th>
                                <th scope="col" class="px-6 py-3">SPK</th>
                                <th scope="col" class="px-6 py-3">Customer</th>
                                <th scope="col" class="px-6 py-3">No. WA</th>
                                <th scope="col" class="px-6 py-3">Sepatu</th>
                                <th scope="col" class="px-6 py-3">Estimasi</th>
                                <th scope="col" class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4">{{ $order->entry_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 font-bold">{{ $order->spk_number }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ $order->customer_name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="text-green-600 hover:text-green-800 flex items-center gap-1">
                                            <span>{{ $order->customer_phone }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $order->shoe_brand }} - {{ $order->shoe_color }} [{{ $order->shoe_size }}]
                                    </td>
                                    <td class="px-6 py-4">{{ $order->estimation_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 flex items-center gap-2">
                                        <a href="{{ route('reception.print-tag', $order->id) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400">Print Tag</a>
                                        
                                        <form action="{{ route('reception.process', $order->id) }}" method="POST" onsubmit="return confirm('Kirim sepatu ini ke bagian Assessment?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs uppercase font-bold">
                                                Kirim Assessment
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center">Belum ada sepatu masuk hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
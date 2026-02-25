<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengiriman') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="shippingTable()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div
                class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <header
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 whitespace-nowrap">Daftar Antrean
                        Pengiriman</h2>

                    <!-- Search & Filter Form -->
                    <form method="GET" action="{{ route('shipping.index') }}"
                        class="w-full md:w-auto flex flex-col sm:flex-row gap-2">
                        <!-- Search Box -->
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-[#22AF85]" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="search" name="search" value="{{ request('search') }}"
                                class="block w-full p-2 pl-10 text-sm border border-gray-300 rounded-lg bg-white focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Cari Nama, No HP, SPK, Resi...">
                        </div>

                        <!-- Filter Status -->
                        <select name="status"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#22AF85] focus:border-[#22AF85] block p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="">Semua Status</option>
                            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified ✅
                            </option>
                            <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>Belum
                                Verified ❌</option>
                        </select>

                        <!-- Filter Tanggal Mulai -->
                        <input type="date" name="date_start" value="{{ request('date_start') }}"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#22AF85] focus:border-[#22AF85] block p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            title="Tanggal Masuk (Dari)">

                        <!-- Filter Tanggal Akhir -->
                        <input type="date" name="date_end" value="{{ request('date_end') }}"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#22AF85] focus:border-[#22AF85] block p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            title="Tanggal Masuk (Sampai)">

                        <button type="submit"
                            class="p-2 px-4 text-sm font-bold text-gray-900 bg-[#FFC232] rounded-lg hover:brightness-95 focus:ring-4 focus:outline-none focus:ring-[#FFC232]/50 transition-all flex justify-center items-center gap-2 shadow-sm">
                            <span class="hidden sm:inline">Filter</span>
                        </button>

                        @if(request()->hasAny(['search', 'status', 'date_start', 'date_end']))
                            <a href="{{ route('shipping.index') }}"
                                class="p-2 text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 flex justify-center items-center"
                                title="Reset Filters">
                                ↺
                            </a>
                        @endif
                    </form>
                </header>

                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full w-full text-sm text-left">
                        <thead
                            class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">#ID</th>
                                <th class="px-6 py-3">Tanggal Masuk</th>
                                <th class="px-6 py-3">Customer</th>
                                <th class="px-6 py-3">No SPK</th>
                                <th class="px-6 py-3">Kategori</th>
                                <th class="px-6 py-3 text-center">Verifikasi</th>
                                <th class="px-6 py-3">Tanggal Kirim</th>
                                <th class="px-6 py-3">PIC</th>
                                <th class="px-6 py-3">Resi Pengiriman</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($shippings as $shipping)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 font-mono font-bold text-gray-700 dark:text-gray-300">
                                        {{ $shipping->id }}
                                    </td>
                                    <form id="form-{{ $shipping->id }}"
                                        action="{{ route('shipping.update', $shipping->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <td class="px-6 py-4">{{ $shipping->tanggal_masuk->format('d M Y') }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800 dark:text-gray-200 text-base leading-tight">
                                                {{ $shipping->customer_name }}</div>
                                            <div
                                                class="flex items-center gap-1.5 mt-1.5 text-xs text-gray-600 dark:text-gray-400">
                                                <svg class="w-3.5 h-3.5 text-[#22AF85]" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                <span
                                                    class="font-medium tracking-wide">{{ $shipping->customer_phone }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-mono font-bold text-gray-700 dark:text-gray-300">
                                            {{ $shipping->spk_number }}</td>
                                        <td class="px-6 py-4">
                                            <select name="kategori_pengiriman" @change="saveForm({{ $shipping->id }})"
                                                class="w-full text-xs box-border border border-gray-300 dark:border-gray-600 rounded-md py-1.5 focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-800 dark:text-gray-200">
                                                <option value="">- Pilih Kategori -</option>
                                                <option value="Ojek Online" {{ $shipping->kategori_pengiriman == 'Ojek Online' ? 'selected' : '' }}>Ojek Online</option>
                                                <option value="Ambil Sendiri" {{ $shipping->kategori_pengiriman == 'Ambil Sendiri' ? 'selected' : '' }}>Ambil Sendiri</option>
                                                <option value="Ekspedisi" {{ $shipping->kategori_pengiriman == 'Ekspedisi' ? 'selected' : '' }}>Ekspedisi</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="is_verified" value="1" {{ $shipping->is_verified ? 'checked' : '' }} class="sr-only peer"
                                                    @change="saveForm({{ $shipping->id }})">
                                                <div
                                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#22AF85]/40 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-[#22AF85]">
                                                </div>
                                            </label>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="date" name="tanggal_pengiriman"
                                                value="{{ $shipping->tanggal_pengiriman?->format('Y-m-d') }}"
                                                @change="saveForm({{ $shipping->id }})"
                                                class="w-full text-xs box-border border border-gray-300 dark:border-gray-600 rounded-md py-1.5 focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-800 dark:text-gray-200">
                                        </td>
                                        <td class="px-6 py-4">
                                            <select name="pic" @change="saveForm({{ $shipping->id }})"
                                                class="w-full text-xs box-border border border-gray-300 dark:border-gray-600 rounded-md py-1.5 focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-800 dark:text-gray-200">
                                                <option value="">- Pilih PIC -</option>
                                                @foreach($technicians as $tech)
                                                    <option value="{{ $tech->name }}" {{ $shipping->pic == $tech->name ? 'selected' : '' }}>{{ $tech->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="text" name="resi_pengiriman"
                                                value="{{ $shipping->resi_pengiriman }}"
                                                @change="saveForm({{ $shipping->id }})" placeholder="Input Resi"
                                                class="w-full text-xs box-border border border-gray-300 dark:border-gray-600 rounded-md py-1.5 focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-800 dark:text-gray-200">
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span id="save-indicator-{{ $shipping->id }}"
                                                class="text-xs text-[#22AF85] font-bold hidden opacity-0 transition-opacity duration-300">Tersimpan</span>
                                        </td>
                                    </form>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-8 text-center text-gray-500 italic">Belum ada antrean
                                        pengiriman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($shippings->hasPages())
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700">
                        {{ $shippings->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('shippingTable', () => ({
                saveForm(id) {
                    const form = document.getElementById('form-' + id);
                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST', // Actually we use PUT override in Laravel via _method
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show save indicator
                                const indicator = document.getElementById('save-indicator-' + id);
                                indicator.classList.remove('hidden');
                                setTimeout(() => indicator.classList.remove('opacity-0'), 10);

                                setTimeout(() => {
                                    indicator.classList.add('opacity-0');
                                    setTimeout(() => indicator.classList.add('hidden'), 300);
                                }, 2000);
                            }
                        })
                        .catch(error => {
                            console.error('Error saving:', error);
                            alert('Gagal menyimpan data.');
                        });
                }
            }));
        });
    </script>
</x-app-layout>
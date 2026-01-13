<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan & Analitik') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                    <div class="w-full sm:w-auto">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div class="w-full sm:w-auto">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white font-bold rounded-lg hover:bg-teal-700 transition-colors shadow">
                        Filter Data
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Financial Card -->
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                     <div class="p-6 bg-gradient-to-br from-green-500 to-emerald-600 text-white">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold">Laporan Keuangan</h3>
                            <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between text-sm opacity-90">
                                <span>Pemasukan (Omzet)</span>
                                <span class="font-mono">Rp {{ number_format($revenue, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm opacity-90">
                                <span>Pengeluaran (Belanja)</span>
                                <span class="font-mono text-red-100">- Rp {{ number_format($expenses, 0, ',', '.') }}</span>
                            </div>
                            <div class="mt-4 pt-3 border-t border-white/20 flex justify-between font-bold text-2xl">
                                <span>Profit Bersih</span>
                                <span>Rp {{ number_format($profit, 0, ',', '.') }}</span>
                            </div>
                        </div>
                     </div>
                     <div class="p-6 bg-gray-50 dark:bg-gray-700/50">
                        <p class="text-sm text-gray-500 mb-4">
                            Laporan ini mencakup semua order yang <b>sudah diambil (Lunas)</b> dan pembelian material dalam rentang tanggal yang dipilih.
                        </p>
                        <a href="{{ route('admin.reports.financial.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="block w-full text-center py-3 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-200 font-bold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 hover:text-green-600 transition-colors shadow-sm">
                            📄 Download PDF Keuangan
                        </a>
                     </div>
                </div>

                <!-- Productivity Card -->
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                     <div class="p-6 bg-gradient-to-br from-blue-500 to-indigo-600 text-white">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold">Laporan Produktivitas</h3>
                            <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div class="space-y-3">
                            <p class="text-sm opacity-90 mb-2">Top 5 Teknisi Terrajin:</p>
                            @foreach($topTechnicians as $tech)
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-medium flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[10px] font-bold">{{ $loop->iteration }}</span>
                                    {{ $tech->name }}
                                </span>
                                <span class="font-mono bg-white/20 px-2 py-0.5 rounded text-xs">{{ $tech->logs_count }} Tasks</span>
                            </div>
                            @endforeach
                        </div>
                     </div>
                     <div class="p-6 bg-gray-50 dark:bg-gray-700/50">
                        <p class="text-sm text-gray-500 mb-4">
                            Laporan ini menghitung jumlah tugas (Washing, Sol, Jahit, QC) yang diselesaikan oleh setiap karyawan.
                        </p>
                        <a href="{{ route('admin.reports.productivity.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="block w-full text-center py-3 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-200 font-bold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 hover:text-blue-600 transition-colors shadow-sm">
                            📊 Download PDF Produktivitas
                        </a>
                     </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

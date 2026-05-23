<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">List Garansi</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Daftar semua order yang mendapat garansi workshop</p>
                </div>
            </div>
            <a href="{{ route('finish.index') }}" class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-semibold transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Finish
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- STATS CARDS --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Total --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex items-center gap-4">
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl">
                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wide">Total Garansi</div>
                    </div>
                </div>

                {{-- Aktif --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-emerald-100 dark:border-emerald-900/30 p-5 flex items-center gap-4">
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['active'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wide">Masih Aktif</div>
                    </div>
                </div>

                {{-- Expired --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-red-100 dark:border-red-900/30 p-5 flex items-center gap-4">
                    <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-xl">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-red-600 dark:text-red-400">{{ $stats['expired'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wide">Sudah Expired</div>
                    </div>
                </div>

                {{-- Akan Berakhir ≤7 hari --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-amber-100 dark:border-amber-900/30 p-5 flex items-center gap-4">
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $stats['soon'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wide">Berakhir ≤7 Hari</div>
                    </div>
                </div>
            </div>

            {{-- FILTER & SEARCH --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <form method="GET" action="{{ route('finish.list-garansi') }}" class="flex flex-col sm:flex-row gap-3 items-end">

                    {{-- Filter Tabs --}}
                    <div class="flex rounded-xl overflow-hidden border border-gray-200 dark:border-gray-600 shrink-0">
                        @foreach(['active' => '🟢 Aktif', 'expired' => '🔴 Expired', 'all' => '📋 Semua'] as $val => $label)
                            <a href="{{ route('finish.list-garansi', ['filter' => $val, 'search' => $search]) }}"
                               class="px-4 py-2 text-xs font-bold transition-all {{ $filter === $val ? 'bg-emerald-500 text-white' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    {{-- Search --}}
                    <div class="relative flex-1 min-w-[200px]">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari SPK, Nama, atau No HP..."
                               class="w-full pl-9 pr-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    </div>

                    <button type="submit" class="px-5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl transition-all shadow-sm">
                        Cari
                    </button>
                    @if($search)
                        <a href="{{ route('finish.list-garansi', ['filter' => $filter]) }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-all">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- TABLE --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100">
                        Daftar Garansi
                        <span class="ml-2 px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded-full font-bold">
                            {{ $orders->total() }} order
                        </span>
                    </h3>
                </div>

                @if($orders->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                        <svg class="w-14 h-14 mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <p class="text-sm font-semibold">Tidak ada data garansi ditemukan.</p>
                        <p class="text-xs mt-1">Garansi akan muncul setelah customer melakukan pengambilan dengan pilihan garansi.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50 dark:text-gray-400">
                                <tr>
                                    <th class="px-5 py-3">SPK & Customer</th>
                                    <th class="px-5 py-3">Info Sepatu</th>
                                    <th class="px-5 py-3">Layanan</th>
                                    <th class="px-5 py-3 text-center">Tanggal Ambil</th>
                                    <th class="px-5 py-3 text-center">Durasi Garansi</th>
                                    <th class="px-5 py-3 text-center">Berlaku Hingga</th>
                                    <th class="px-5 py-3 text-center">Status Garansi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($orders as $order)
                                    @php
                                        $now = now();
                                        $isExpired = $order->warranty_expires_at < $now;
                                        $isSoon = !$isExpired && $order->warranty_expires_at->diffInDays($now) <= 7;
                                        $sisaHari = $isExpired
                                            ? null
                                            : (int) $now->diffInDays($order->warranty_expires_at, false);
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors {{ $isExpired ? 'opacity-60' : '' }}">
                                        {{-- SPK & Customer --}}
                                        <td class="px-5 py-4">
                                            <a href="{{ route('finish.show', $order->id) }}" class="font-bold text-sm text-gray-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors block">
                                                {{ $order->spk_number }}
                                            </a>
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $order->customer_name }}</div>
                                            <div class="text-xs text-gray-400 font-mono">{{ $order->customer_phone }}</div>
                                        </td>

                                        {{-- Info Sepatu --}}
                                        <td class="px-5 py-4">
                                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $order->shoe_brand }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->shoe_type }} — {{ $order->shoe_color }}</div>
                                            @if($order->shoe_size)
                                                <div class="text-xs text-gray-400">Size: {{ $order->shoe_size }}</div>
                                            @endif
                                        </td>

                                        {{-- Layanan --}}
                                        <td class="px-5 py-4">
                                            <div class="space-y-0.5">
                                                @foreach($order->workOrderServices->take(3) as $svc)
                                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                                        • {{ $svc->custom_service_name ?? ($svc->service->name ?? '-') }}
                                                    </div>
                                                @endforeach
                                                @if($order->workOrderServices->count() > 3)
                                                    <div class="text-xs text-gray-400">+{{ $order->workOrderServices->count() - 3 }} lainnya</div>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Tanggal Ambil --}}
                                        <td class="px-5 py-4 text-center">
                                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                                {{ $order->taken_date->format('d M Y') }}
                                            </div>
                                            <div class="text-xs text-gray-400">{{ $order->taken_date->format('H:i') }}</div>
                                        </td>

                                        {{-- Durasi --}}
                                        <td class="px-5 py-4 text-center">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 rounded-full text-xs font-bold border border-indigo-100 dark:border-indigo-800">
                                                🛡️ {{ $order->warranty_duration_months }} Bulan
                                            </span>
                                        </td>

                                        {{-- Berlaku Hingga --}}
                                        <td class="px-5 py-4 text-center">
                                            <div class="text-sm font-bold {{ $isExpired ? 'text-red-500' : ($isSoon ? 'text-amber-600' : 'text-emerald-600') }}">
                                                {{ $order->warranty_expires_at->format('d M Y') }}
                                            </div>
                                            @if(!$isExpired)
                                                <div class="text-xs text-gray-400">sisa {{ $sisaHari }} hari</div>
                                            @else
                                                <div class="text-xs text-red-400">
                                                    {{ abs($now->diffInDays($order->warranty_expires_at)) }} hari lalu
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Status Badge --}}
                                        <td class="px-5 py-4 text-center">
                                            @if($isExpired)
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-xs font-black border border-red-200 dark:border-red-800">
                                                    ✕ EXPIRED
                                                </span>
                                            @elseif($isSoon)
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-full text-xs font-black border border-amber-200 dark:border-amber-800 animate-pulse">
                                                    ⚠️ SEGERA HABIS
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-full text-xs font-black border border-emerald-200 dark:border-emerald-800">
                                                    ✓ AKTIF
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700">
                        {{ $orders->withQueryString()->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

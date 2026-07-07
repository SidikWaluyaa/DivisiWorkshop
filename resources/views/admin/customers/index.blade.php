<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">Master Data Customer</h2>
                <div class="text-xs font-medium opacity-90">Database Konsumen & Foto</div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Search & Actions (Sticky on Mobile) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4 md:p-6 lg:rounded-2xl pwa-sticky-search">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <form method="GET" class="w-full md:flex-1">
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Cari nama, phone, atau email..." 
                                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-sm text-base lg:text-sm transition-all">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 sm:flex-none px-6 py-2.5 bg-teal-600 text-white rounded-xl hover:bg-teal-700 font-bold text-sm transition-all shadow-md shadow-teal-600/10 active:scale-[0.98]">
                                    Cari
                                </button>
                                @if(request('search'))
                                <a href="{{ route('admin.customers.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-bold text-sm transition-colors flex items-center justify-center">
                                    Reset
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>
                    {{-- Desktop-only add button (replaced by FAB on mobile) --}}
                    <a href="{{ route('admin.customers.create') }}" class="hidden lg:flex w-full md:w-auto px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 font-bold text-sm transition-all shadow-md shadow-orange-500/10 items-center justify-center gap-2 active:scale-[0.98]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Customer
                    </a>
                </div>
            </div>

            {{-- Customer Table (Flat on Mobile, Card on Desktop) --}}
            <div class="bg-transparent border-none shadow-none lg:bg-white lg:rounded-xl lg:shadow-sm lg:border lg:border-gray-200 overflow-hidden">
                <div class="bg-transparent border-none lg:bg-gradient-to-r lg:from-teal-50 lg:to-teal-100 lg:border-b lg:border-teal-200 px-0 lg:px-6 py-2 lg:py-4">
                    <h3 class="font-extrabold text-gray-800 lg:text-teal-800 flex items-center gap-2 text-lg lg:text-base">
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                        Daftar Customer ({{ $customers->total() }})
                    </h3>
                </div>

                {{-- Mobile Card View (Reclaim margin space, add scrolling clearance) --}}
                <div class="block lg:hidden px-0 py-2 pb-28 space-y-4">
                    @forelse($customers as $customer)
                    <div class="pwa-mobile-card space-y-3">
                        <div class="flex items-start gap-4">
                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-teal-500/10 to-emerald-500/10 flex-shrink-0 flex items-center justify-center text-teal-700 font-black text-base border border-teal-200/50 shadow-inner">
                                 {{ substr($customer->name, 0, 2) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <div class="min-w-0">
                                        <h4 class="font-extrabold text-gray-900 truncate text-base">{{ $customer->name }}</h4>
                                        @if($customer->city)
                                            <p class="text-[10px] text-gray-400 font-semibold flex items-center gap-1 mt-0.5">
                                                <svg class="w-3 h-3 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                {{ $customer->city }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mt-3 space-y-1.5 text-xs text-gray-600 font-medium">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        <span class="font-bold text-gray-800">{{ $customer->phone }}</span>
                                    </div>
                                    @if($customer->email)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        <span class="truncate">{{ $customer->email }}</span>
                                    </div>
                                    @endif
                                </div>
                
                                <div class="flex items-center gap-4 mt-4 pt-3 border-t border-gray-100">
                                     <div class="flex flex-col">
                                        <span class="text-[9px] text-gray-400 uppercase font-black tracking-wider">Foto</span>
                                        <span class="text-xs font-black text-blue-600 mt-0.5">{{ $customer->photos_count }}</span>
                                    </div>
                                    <div class="w-px h-6 bg-gray-200"></div>
                                     <div class="flex flex-col">
                                        <span class="text-[9px] text-gray-400 uppercase font-black tracking-wider">Order</span>
                                        <span class="text-xs font-black text-emerald-600 mt-0.5">{{ $customer->work_orders_count }}</span>
                                    </div>
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="ml-auto inline-flex items-center justify-center px-5 py-2 bg-teal-50 hover:bg-teal-100 text-teal-700 rounded-xl text-xs font-black tracking-wider transition-colors min-h-[44px]">
                                        DETAIL
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-2xl border border-gray-150 p-8 text-center text-gray-400 italic text-sm">
                        Belum ada customer.
                    </div>
                    @endforelse
                </div>
            
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Foto</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">SPK</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $customer->name }}</div>
                                    @if($customer->city)
                                    <div class="text-xs text-gray-500">{{ $customer->city }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-700">{{ $customer->phone }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $customer->email ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                        {{ $customer->photos_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        {{ $customer->work_orders_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.customers.show', $customer) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors text-xs font-semibold">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada customer</h3>
                                    <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan customer baru atau customer akan otomatis dibuat dari reception.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($customers->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $customers->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Mobile FAB: Tambah Customer --}}
    <a href="{{ route('admin.customers.create') }}" class="pwa-fab lg:hidden" aria-label="Tambah Customer">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
        </svg>
    </a>
</x-app-layout>

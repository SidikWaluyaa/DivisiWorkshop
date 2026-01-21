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
            
            {{-- Search & Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between gap-4">
                    <form method="GET" class="flex-1">
                        <div class="flex gap-3">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="🔍 Cari nama, phone, atau email..." 
                                   class="flex-1 rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500">
                            <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-semibold transition-colors">
                                Cari
                            </button>
                            @if(request('search'))
                            <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition-colors">
                                Reset
                            </a>
                            @endif
                        </div>
                    </form>
                    <a href="{{ route('admin.customers.create') }}" class="px-6 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 font-semibold transition-all shadow-md flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Customer
                    </a>
                </div>
            </div>

            {{-- Customer Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-teal-50 to-teal-100 border-b border-teal-200 px-6 py-4">
                    <h3 class="font-bold text-teal-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        Daftar Customer ({{ $customers->total() }})
                    </h3>
                </div>

                {{-- Mobile Card View --}}
                <div class="block lg:hidden grid grid-cols-1 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($customers as $customer)
                    <div class="p-4 bg-white dark:bg-gray-800 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-3">
                             <div class="h-10 w-10 rounded-full bg-teal-100 flex-shrink-0 flex items-center justify-center text-teal-600 font-bold text-sm border border-teal-200">
                                 {{ substr($customer->name, 0, 2) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                         <h3 class="font-bold text-gray-900 dark:text-white truncate">{{ $customer->name }}</h3>
                                         @if($customer->city)
                                            <p class="text-[10px] text-gray-500 flex items-center gap-1">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                {{ $customer->city }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mt-2 space-y-1 text-xs text-gray-600">
                                     <div class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        {{ $customer->phone }}
                                    </div>
                                    @if($customer->email)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        {{ $customer->email }}
                                    </div>
                                    @endif
                                </div>
                
                                <div class="flex items-center gap-4 mt-3 pt-2 border-t border-gray-100">
                                     <div class="flex flex-col items-center">
                                        <span class="text-[10px] text-gray-400 uppercase font-bold">Foto</span>
                                        <span class="text-xs font-bold text-blue-600">{{ $customer->photos_count }}</span>
                                    </div>
                                    <div class="w-px h-6 bg-gray-200"></div>
                                     <div class="flex flex-col items-center">
                                        <span class="text-[10px] text-gray-400 uppercase font-bold">Order</span>
                                        <span class="text-xs font-bold text-green-600">{{ $customer->work_orders_count }}</span>
                                    </div>
                                     <a href="{{ route('admin.customers.show', $customer) }}" class="ml-auto inline-flex items-center px-4 py-1.5 bg-teal-50 text-teal-700 rounded-lg text-xs font-bold hover:bg-teal-100 transition-colors">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                        <div class="text-center p-6 text-gray-500 italic text-sm">Belum ada customer.</div>
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
</x-app-layout>

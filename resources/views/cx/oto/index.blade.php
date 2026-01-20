<x-app-layout>
    <!-- Content -->
    <div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-600 to-pink-600 pb-24 pt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white">Kolam OTO (Upsell) 🎁</h1>
                    <p class="mt-2 text-orange-100">Manage penawaran One Time Offer untuk customer</p>
                </div>
                <!-- Stats -->
                <div class="flex space-x-4">
                    <div class="bg-white/10 backdrop-blur-lg rounded-lg p-4 text-white">
                        <div class="text-sm opacity-80">Pending Call</div>
                        <div class="text-2xl font-bold">{{ $stats['pending'] }}</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-lg rounded-lg p-4 text-white">
                        <div class="text-sm opacity-80">Contacted</div>
                        <div class="text-2xl font-bold">{{ $stats['contacted'] }}</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-lg rounded-lg p-4 text-white">
                        <div class="text-sm opacity-80">Revenue Potensial</div>
                        <div class="text-2xl font-bold">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-lg p-4 mb-6 flex items-center justify-between">
            <div class="flex space-x-2">
                <a href="{{ route('cx.oto.index', ['filter' => 'all']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ $filter === 'all' ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:bg-gray-100' }}">
                   Semua
                </a>
                <a href="{{ route('cx.oto.index', ['filter' => 'urgent']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ $filter === 'urgent' ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:bg-gray-100' }}">
                   🔥 Urgent (< 3 hari)
                </a>
                <a href="{{ route('cx.oto.index', ['filter' => 'my']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ $filter === 'my' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                   👋 My OTO
                </a>
            </div>
            
            <form action="{{ route('cx.oto.index') }}" method="GET" class="relative">
                <input type="text" name="search" placeholder="Cari SPK / Customer..." 
                       class="pl-10 pr-4 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" class="feather feather-search" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
            </form>
        </div>

        <!-- OTO List -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($otos as $oto)
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-200 border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-0.5 rounded">{{ $oto->workOrder->spk_number }}</span>
                                @if($oto->status === 'PENDING_CX')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-0.5 rounded">Perlu Dihubungi</span>
                                @else
                                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded">Sudah Dihubungi</span>
                                @endif
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mt-2">{{ $oto->workOrder->customer_name }}</h3>
                            <p class="text-sm text-gray-500">{{ $oto->workOrder->customer_phone }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Valid Until</div>
                            <div class="text-red-500 font-bold {{ Carbon\Carbon::parse($oto->valid_until)->diffInDays(now()) < 3 ? 'animate-pulse' : '' }}">
                                {{ Carbon\Carbon::parse($oto->valid_until)->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-400">{{ Carbon\Carbon::parse($oto->valid_until)->diffForHumans() }}</div>
                        </div>
                    </div>

                    <!-- Offer Details -->
                    <div class="bg-orange-50 rounded-lg p-4 mb-4">
                        <div class="text-xs font-bold text-orange-800 mb-2 uppercase tracking-wide">Penawaran</div>
                        <div class="space-y-2">
                            @foreach($oto->proposed_services as $service)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">{{ $service['service_name'] }}</span>
                                <div class="text-right">
                                    <span class="text-gray-400 line-through text-xs">Rp {{ number_format($service['normal_price'], 0, ',', '.') }}</span>
                                    <span class="text-orange-700 font-bold ml-1">Rp {{ number_format($service['oto_price'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @endforeach
                            <div class="border-t border-orange-200 pt-2 mt-2 flex justify-between items-center">
                                <span class="font-bold text-orange-900">Total</span>
                                <div class="text-right">
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full mr-2">Hemat {{ number_format($oto->total_discount, 0, ',', '.') }}</span>
                                    <span class="font-bold text-orange-700 text-lg">Rp {{ number_format($oto->total_oto_price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2" x-data="{ openContact: false }">
                        <button @click="openContact = true" 
                            class="flex-1 bg-gradient-to-r from-orange-500 to-pink-500 text-white py-2 rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition shadow-sm flex justify-center items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            Hubungi
                        </button>
                        
                        <!-- Contact Modal Component -->
                        <div x-show="openContact" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openContact = false">
                                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                </div>
                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                    <form action="{{ route('cx.oto.contact', $oto->id) }}" method="POST">
                                        @csrf
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Log Kontak Customer</h3>
                                            
                                            <!-- Script Template -->
                                            <div class="bg-gray-50 p-3 rounded-lg mb-4 text-sm text-gray-600 relative group">
                                                <p>"Halo Kak {{ $oto->workOrder->customer_name }}, sepatu {{ $oto->workOrder->custom_name ?? 'Anda' }} sudah selesai nih! Kami ada penawaran spesial OTO {{ $oto->proposed_services[0]['service_name'] }} diskon {{ number_format($oto->discount_percent) }}% lho kak. Cuma nambah {{ number_format($oto->total_oto_price) }} aja. Minat kak?"</p>
                                                <button type="button" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600" onclick="navigator.clipboard.writeText(this.parentElement.querySelector('p').innerText)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                </button>
                                            </div>

                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Kontak</label>
                                                <select name="contact_method" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                                    <option value="WHATSAPP">WhatsApp</option>
                                                    <option value="PHONE">Phone Call</option>
                                                    <option value="EMAIL">Email</option>
                                                </select>
                                            </div>

                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Respon Customer</label>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-green-50 has-[:checked]:bg-green-50 has-[:checked]:border-green-500">
                                                        <input type="radio" name="customer_response" value="INTERESTED" class="text-green-600 focus:ring-green-500">
                                                        <span class="ml-2 text-sm text-gray-700">Tertarik (Pending)</span>
                                                    </label>
                                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-yellow-50 has-[:checked]:bg-yellow-50 has-[:checked]:border-yellow-500">
                                                        <input type="radio" name="customer_response" value="NEED_TIME" class="text-yellow-600 focus:ring-yellow-500">
                                                        <span class="ml-2 text-sm text-gray-700">Mikir-mikir</span>
                                                    </label>
                                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-red-50 has-[:checked]:bg-red-50 has-[:checked]:border-red-500">
                                                        <input type="radio" name="customer_response" value="NOT_INTERESTED" class="text-red-600 focus:ring-red-500">
                                                        <span class="ml-2 text-sm text-gray-700">Tidak Minat</span>
                                                    </label>
                                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:bg-gray-50 has-[:checked]:border-gray-500">
                                                        <input type="radio" name="customer_response" value="NO_ANSWER" class="text-gray-600 focus:ring-gray-500">
                                                        <span class="ml-2 text-sm text-gray-700">Tidak Diangkat</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="mb-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                                                <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" placeholder="Hasil pembicaraan..."></textarea>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                Simpan Log
                                            </button>
                                            <button type="button" @click="openContact = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Direct Actions -->
                        <div x-data="{ openAccept: false }">
                            <button @click="openAccept = true" class="bg-green-100 text-green-700 p-2 rounded-lg hover:bg-green-200 transition" title="Customer Accept">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                            
                             <!-- Accept Modal -->
                             <div x-show="openAccept" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openAccept = false">
                                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                    </div>
                                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <form action="{{ route('cx.oto.accept', $oto->id) }}" method="POST">
                                            @csrf
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                        <h3 class="text-lg leading-6 font-medium text-gray-900">Konfirmasi Terima OTO</h3>
                                                        <div class="mt-2">
                                                            <p class="text-sm text-gray-500">
                                                                Apakah Anda yakin customer menyetujui penawaran ini? Order akan otomatis ditambahkan layanan dan masuk ke antrian <strong>PRIORITAS (Express)</strong>.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Ya, Terima Penawaran
                                                </button>
                                                <button type="button" @click="openAccept = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('cx.oto.cancel', $oto->id) }}" method="POST" onsubmit="return confirm('Yakin batalkan penawaran ini?')">
                            @csrf
                            <button type="submit" class="bg-red-100 text-red-700 p-2 rounded-lg hover:bg-red-200 transition" title="Cancel OTO">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- History -->
                @if($oto->contactLogs->count() > 0)
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                    <div class="text-xs font-bold text-gray-500 mb-2 uppercase">Riwayat Kontak</div>
                    <div class="space-y-3">
                        @foreach($oto->contactLogs->take(2) as $log)
                        <div class="flex text-xs">
                            <div class="w-20 text-gray-400">{{ $log->created_at->format('d/m H:i') }}</div>
                            <div class="flex-1">
                                <span class="font-medium text-gray-700">{{ $log->contactedBy->name }}</span>
                                <span class="text-gray-500">: {{ Str::limit($log->notes, 40) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @empty
            <div class="col-span-2 text-center py-20 bg-white rounded-xl shadow-sm border border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Kolam OTO Kosong</h3>
                <p class="mt-1 text-sm text-gray-500">Tidak ada penawaran OTO yang perlu ditangani saat ini.</p>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $otos->links() }}
        </div>
    </div>
</div>
</x-app-layout>

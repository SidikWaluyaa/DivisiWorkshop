<x-app-layout>
    <div class="hidden lg:block bg-gray-50" x-cloak>
    {{-- Premium Hero Section (Modern Glassmorphism) --}}
    <div class="relative bg-white border-b border-gray-100 overflow-hidden">
        {{-- Abstract Background Elements --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-[10%] -right-[5%] w-[400px] h-[400px] rounded-full bg-gradient-to-br from-[#22B086]/20 to-transparent blur-[100px] opacity-40 animate-pulse"></div>
            <div class="absolute -bottom-[10%] -left-[5%] w-[350px] h-[350px] rounded-full bg-gradient-to-tr from-[#FFC232]/20 to-transparent blur-[80px] opacity-30"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pwa-hero-mobile">
            <div class="flex flex-col md:flex-row items-center justify-between gap-10 lg:gap-10 gap-y-6">
                {{-- Customer Profile --}}
                <div class="flex flex-col md:flex-row items-center gap-8 lg:gap-8 gap-y-4">
                    <div class="relative group">
                        <div class="w-32 h-32 rounded-[2rem] bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-800 text-4xl font-black shadow-[0_20px_50px_rgba(0,0,0,0.05)] border-4 border-white ring-1 ring-gray-100 overflow-hidden transform group-hover:scale-105 transition-all duration-500">
                            <span class="relative z-10" x-text="$store.customerDetail.name.substring(0, 2).toUpperCase()">{{ substr($customer->name, 0, 2) }}</span>
                            <div class="absolute inset-0 bg-gradient-to-tr from-[#22B086]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-[#22B086] rounded-2xl border-4 border-white flex items-center justify-center shadow-xl transform group-hover:rotate-12 transition-transform">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>
                    <div class="text-center md:text-left space-y-3">
                        <h1 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight leading-tight" x-text="$store.customerDetail.name">{{ $customer->name }}</h1>
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 text-gray-500 text-sm font-semibold">
                            <span class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-100">
                                <svg class="w-4 h-4 text-[#22B086]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <span x-text="$store.customerDetail.phone">{{ $customer->phone }}</span>
                            </span>
                            <template x-if="$store.customerDetail.email">
                                <span class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-100">
                                    <svg class="w-4 h-4 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <span x-text="$store.customerDetail.email">{{ $customer->email }}</span>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Quick Controls: Desktop full buttons --}}
                <div class="hidden lg:flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <button @click="$store.customerDetail.openEditor()" class="w-full sm:w-auto group px-6 py-3.5 bg-white hover:bg-gray-50 border-2 border-gray-100 rounded-2xl text-gray-700 font-bold transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2 min-h-[48px]">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-[#22B086] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Profile
                    </button>
                    <a href="{{ route('admin.customers.index') }}" class="w-full sm:w-auto px-6 py-3.5 bg-gray-900 text-white rounded-2xl font-bold hover:bg-gray-800 transition-all shadow-lg shadow-gray-200 flex items-center justify-center gap-2 min-h-[48px]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali
                    </a>
                </div>

                {{-- Quick Controls: Mobile icon buttons --}}
                <div class="flex lg:hidden pwa-quick-actions">
                    <button @click="$store.customerDetail.openEditor()" class="pwa-quick-action">
                        <span class="pwa-quick-action__icon bg-[#22B086]/10 text-[#22B086]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </span>
                        <span class="pwa-quick-action__label">Edit</span>
                    </button>
                    @if($customer->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" target="_blank" class="pwa-quick-action">
                        <span class="pwa-quick-action__icon bg-green-50 text-green-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492l4.597-1.466A11.94 11.94 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.306 0-4.443-.678-6.244-1.843l-.436-.272-2.727.87.884-2.668-.295-.46A9.96 9.96 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/></svg>
                        </span>
                        <span class="pwa-quick-action__label">WhatsApp</span>
                    </a>
                    @endif
                    <a href="{{ route('admin.customers.index') }}" class="pwa-quick-action">
                        <span class="pwa-quick-action__icon bg-gray-100 text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </span>
                        <span class="pwa-quick-action__label">Kembali</span>
                    </a>
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
                <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.02)] group hover:border-[#22B086]/30 hover:shadow-[0_20px_50px_rgba(34,176,134,0.08)] transition-all duration-500">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Total Order</p>
                            <p class="text-4xl font-black text-gray-900 tracking-tight">{{ $customer->workOrders->count() }} <span class="text-sm font-bold text-gray-400 ml-1">Orders</span></p>
                            @php
                                $onlineCount = $customer->workOrders->where('channel', 'ONLINE')->count();
                                $offlineCount = $customer->workOrders->where('channel', 'OFFLINE')->count();
                            @endphp
                            <div class="flex gap-2 mt-2 pt-1">
                                <span class="px-2.5 py-0.5 text-[9px] font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wider">{{ $onlineCount }} Online</span>
                                <span class="px-2.5 py-0.5 text-[9px] font-bold rounded-lg bg-gray-100 text-gray-700 border border-gray-200 uppercase tracking-wider">{{ $offlineCount }} Offline</span>
                            </div>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-[#22B086]/5 flex items-center justify-center text-[#22B086] group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                    </div>
                </div>

                @php
                    $totalSpent = $customer->workOrders->sum('total_price');
                @endphp
                <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.02)] group hover:border-[#FFC232]/30 hover:shadow-[0_20px_50px_rgba(255,194,50,0.08)] transition-all duration-500">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Total Spend</p>
                            <p class="text-4xl font-black text-gray-900 tracking-tight">Rp {{ number_format($totalSpent, 0, ',', '.') }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-[#FFC232]/5 flex items-center justify-center text-[#FFC232] group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.02)] group hover:border-gray-200 hover:shadow-[0_20px_50px_rgba(0,0,0,0.05)] transition-all duration-500">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Member Since</p>
                            <p class="text-4xl font-black text-gray-900 tracking-tight">{{ $customer->created_at->diffForHumans(null, true) }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-300 group-hover:scale-110 group-hover:-rotate-6 transition-all duration-500">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">
            
            {{-- Mobile Tab Navigation Bar --}}
            <div class="block lg:hidden">
                <div class="pwa-tab-bar">
                    <button type="button" @click="$store.customerDetail.activeTab = 'profil'"
                            :class="{ 'pwa-tab-bar__item--active': $store.customerDetail.activeTab === 'profil' }"
                            class="pwa-tab-bar__item">
                        Profil
                    </button>
                    <button type="button" @click="$store.customerDetail.activeTab = 'foto'"
                            :class="{ 'pwa-tab-bar__item--active': $store.customerDetail.activeTab === 'foto' }"
                            class="pwa-tab-bar__item">
                        Foto & Dokumen ({{ $customer->photos->count() }})
                    </button>
                    <button type="button" @click="$store.customerDetail.activeTab = 'orders'"
                            :class="{ 'pwa-tab-bar__item--active': $store.customerDetail.activeTab === 'orders' }"
                            class="pwa-tab-bar__item">
                        Daftar SPK ({{ $customer->workOrders->count() }})
                    </button>
                </div>
            </div>

            {{-- Info & Photos Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Left Column: Address & Info --}}
                <div class="space-y-8" x-show="!$store.customerDetail.isMobile || $store.customerDetail.activeTab === 'profil'">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-[#22B086] rounded-full"></span>
                            Alamat & Catatan
                        </h3>
                        
                        <div class="space-y-6">
                            <div class="flex items-start gap-5">
                                <div class="mt-1 w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0 text-[#22B086] shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Alamat Pengiriman</p>
                                    <p class="text-gray-900 font-bold leading-relaxed" x-text="$store.customerDetail.address || 'Belum diisi'">{{ $customer->address ?? 'Belum diisi' }}</p>
                                    <p class="text-sm text-gray-500 mt-1 font-medium italic" x-show="$store.customerDetail.city">
                                        <span x-text="$store.customerDetail.city">{{ $customer->city }}</span>, <span x-text="$store.customerDetail.province">{{ $customer->province }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-5 pt-6 border-t border-gray-100">
                                <div class="mt-1 w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0 text-slate-650 shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Status Verifikasi Alamat</p>
                                    @if($customer->is_address_verified)
                                        <div class="flex flex-wrap items-center gap-2 mt-1">
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-green-50 text-green-700 border border-green-200 flex items-center gap-1.5 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                Terverifikasi
                                            </span>
                                            <span class="text-[10px] text-gray-400 font-medium">({{ $customer->address_verified_at->format('d M, H:i') }})</span>
                                        </div>
                                        <form action="{{ route('admin.customers.reset-verification', $customer->id) }}" method="POST" class="mt-3" onsubmit="return confirm('Reset status verifikasi alamat customer ini?')">
                                            @csrf
                                            <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 hover:underline flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Reset Verifikasi
                                            </button>
                                        </form>
                                    @else
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-red-50 text-red-700 border border-red-200 flex items-center gap-1.5 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                                Belum Terverifikasi
                                            </span>
                                        </div>
                                        @if($customer->address_verification_url)
                                            <div class="mt-3 bg-gray-50 rounded-xl p-3 border border-gray-150 relative" x-data="{ copied: false }">
                                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-wider mb-1">Link Verifikasi Alamat:</p>
                                                <div class="flex items-center gap-2">
                                                    <input type="text" readonly value="{{ $customer->address_verification_url }}" class="bg-white border border-gray-200 rounded-lg text-xs px-2 py-1 flex-1 font-mono text-gray-600 focus:outline-none">
                                                    <button type="button" @click="
                                                         if (navigator.clipboard && window.isSecureContext) {
                                                             navigator.clipboard.writeText('{{ $customer->address_verification_url }}');
                                                         } else {
                                                             let el = document.createElement('textarea');
                                                             el.value = '{{ $customer->address_verification_url }}';
                                                             el.style.position = 'absolute';
                                                             el.style.left = '-9999px';
                                                             document.body.appendChild(el);
                                                             el.select();
                                                             document.execCommand('copy');
                                                             document.body.removeChild(el);
                                                         }
                                                         copied = true;
                                                         setTimeout(() => copied = false, 2000);
                                                     " class="p-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-600 transition-colors" title="Salin Link">
                                                        <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                        <svg x-show="copied" style="display: none;" class="w-4 h-4 text-green-600 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-start gap-5 pt-6 border-t border-gray-100">
                                <div class="mt-1 w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0 text-[#FFC232] shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Catatan Customer</p>
                                    <div class="mt-2 bg-gray-50/50 rounded-2xl p-4 border border-gray-100 text-sm text-gray-600 font-medium leading-relaxed" x-text="$store.customerDetail.notes || 'Tidak ada catatan khusus'">
                                        "{{ $customer->notes ?? 'Tidak ada catatan khusus' }}"
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Photos --}}
                <div class="lg:col-span-2" x-show="!$store.customerDetail.isMobile || $store.customerDetail.activeTab === 'foto'">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-black text-gray-800 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-[#FFC232] rounded-full"></span>
                                Dokumen & Foto CS ({{ $customer->photos->count() }})
                            </h3>
                            <button onclick="openCustUploadModal()" 
                                    class="px-4 py-2 bg-[#22B086] text-white rounded-xl hover:bg-[#1C8D6C] font-bold text-sm transition-all shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Upload Baru
                            </button>
                        </div>

                        @if($customer->photos->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($customer->photos as $photo)
                            <div class="relative group aspect-square rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 bg-gray-100" id="photo-container-{{ $photo->id }}">
                                <img src="{{ $photo->photo_url }}" 
                                     class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500 cursor-pointer"
                                     onclick="window.open('{{ $photo->photo_url }}', '_blank')">
                                
                                {{-- Delete Button --}}
                                <button onclick="deleteCustomerPhoto({{ $photo->id }})" 
                                        class="absolute top-2 right-2 p-1.5 bg-red-600/80 hover:bg-red-700 text-white rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-all transform hover:scale-110 z-10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-3a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>

                                <div class="absolute inset-0 pointer-events-none bg-gradient-to-t from-gray-900/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-3 flex flex-col justify-end">
                                    <p class="text-white text-xs font-bold line-clamp-1">{{ $photo->caption ?? 'Foto Customer' }}</p>
                                    <p class="text-gray-400 text-[10px]">{{ $photo->created_at->format('d/M/y') }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="h-64 flex flex-col items-center justify-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-4 shadow-sm">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-gray-400 font-medium">Belum ada dokumen foto</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- History Section --}}
            <div class="bg-white rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden"
                 x-show="!$store.customerDetail.isMobile || $store.customerDetail.activeTab === 'orders'">
                <div class="px-8 py-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-6 bg-gradient-to-r from-gray-50/50 to-white">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-[#22B086]/10 flex items-center justify-center text-[#22B086] shadow-inner">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 tracking-tight">Riwayat Pesanan</h3>
                            <p class="text-sm text-gray-400 font-medium mt-0.5">
                                <span x-show="!$store.customerDetail.orderSearch">Total {{ $customer->workOrders->count() }} transaksi ditemukan</span>
                                <span x-show="$store.customerDetail.orderSearch" style="display: none;">
                                    Ditemukan <span class="text-[#22B086] font-bold" x-text="document.querySelectorAll('tbody tr:not([style*=\'display: none\'])').length"></span> hasil untuk pencarian ini
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    {{-- Search Bar for Orders --}}
                    <div class="relative w-full md:w-80">
                        <label for="order_search_input" class="sr-only">Cari No. SPK atau Sepatu</label>
                        <input type="text" id="order_search_input" name="order_search"
                               x-model="$store.customerDetail.orderSearch" 
                               placeholder="Cari No. SPK atau Sepatu..." autocomplete="off"
                               class="w-full pl-12 pr-4 py-3.5 bg-white border-2 border-gray-100 rounded-2xl text-sm font-bold text-gray-800 placeholder-gray-300 focus:outline-none focus:border-[#22B086] focus:ring-0 transition-all shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-300" x-show="!$store.customerDetail.orderSearch" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <svg class="w-5 h-5 text-[#22B086] animate-bounce" x-show="$store.customerDetail.orderSearch" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <button x-show="$store.customerDetail.orderSearch" @click="$store.customerDetail.orderSearch = ''" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-red-500 transition-colors" style="display: none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                {{-- Mobile Card List (Responsive Display) --}}
                <div class="block lg:hidden p-4 space-y-4">
                    @forelse($customer->workOrders as $order)
                    <div class="bg-white rounded-2xl border border-gray-150 shadow-sm p-5 space-y-4 hover:shadow-md transition-all duration-300"
                         x-show="!$store.customerDetail.orderSearch || '{{ strtolower($order->spk_number) }} {{ strtolower($order->shoe_brand) }} {{ strtolower($order->shoe_type) }}'.includes($store.customerDetail.orderSearch.toLowerCase())">
                        
                        {{-- SPK & Entry Date Row --}}
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">No. SPK</span>
                                <h4 class="font-extrabold text-gray-900 text-base leading-tight mt-0.5">{{ $order->spk_number }}</h4>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-gray-800 block">{{ $order->entry_date->format('d M Y') }}</span>
                                <span class="text-[10px] font-semibold text-gray-400 block mt-0.5">{{ $order->entry_date->format('H:i') }} WIB</span>
                            </div>
                        </div>

                        {{-- Shoe Info --}}
                        <div class="flex items-center gap-3 bg-gray-50/70 p-3 rounded-xl border border-gray-100">
                            <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center text-xl shadow-sm border border-gray-100 flex-shrink-0">
                                👟
                            </div>
                            <div class="min-w-0">
                                <div class="font-extrabold text-gray-800 text-sm truncate">{{ $order->shoe_brand }}</div>
                                <div class="text-xs text-gray-500 font-medium truncate mt-0.5">{{ $order->shoe_type }} • {{ $order->shoe_color }}</div>
                            </div>
                        </div>

                        {{-- Status & Technicians --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                            @php
                                $statusConfig = [
                                    'DONE' => ['bg' => 'bg-emerald-50', 'text' => 'text-[#1C8D6C]', 'icon' => '✅'],
                                    'CANCELLED' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'icon' => '❌'],
                                    'PROGRESS' => ['bg' => 'bg-orange-50', 'text' => 'text-[#FFB000]', 'icon' => '⚙️'],
                                ];
                                $statusClass = $statusConfig[$order->status->value ?? $order->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'icon' => '⏳'];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-black border border-transparent {{ $statusClass['bg'] }} {{ $statusClass['text'] }}">
                                {{ $order->status }}
                            </span>

                            <div class="flex flex-wrap gap-1">
                                @php
                                    $techs = [];
                                    if($order->prepWashingBy) $techs['Prep'] = $order->prepWashingBy->name;
                                    $prodName = $order->prodSolBy->name ?? $order->prodUpperBy->name ?? $order->prodCleaningBy->name ?? $order->technicianProduction->name ?? null;
                                    if($prodName) $techs['Prod'] = $prodName;
                                    $qcName = $order->qcFinalBy->name ?? $order->qcFinalPic->name ?? null;
                                    if($qcName) $techs['QC'] = $qcName;
                                @endphp
                                @foreach($techs as $label => $name)
                                    <span class="text-[9px] font-bold text-gray-500 bg-gray-100/80 px-2 py-0.5 rounded border border-gray-200" title="{{ $label }}: {{ $name }}">
                                        {{ $label }}: {{ explode(' ', $name)[0] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Action Tray --}}
                        <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                            @php
                                $valPhotos = $order->photos->map(function($p) {
                                    $size = 0;
                                    try {
                                        if(\Illuminate\Support\Facades\Storage::disk('public')->exists($p->file_path)) {
                                            $size = \Illuminate\Support\Facades\Storage::disk('public')->size($p->file_path);
                                        }
                                    } catch(\Exception $e) {}
                                    $p->size_bytes = $size;
                                    $p->formatted_size = $size > 1048576 
                                        ? round($size / 1048576, 2) . ' MB' 
                                        : round($size / 1024, 2) . ' KB';
                                    return $p;
                                });
                            @endphp
                            
                            {{-- Gallery Button --}}
                            <button data-spk="{{ $order->spk_number }}" 
                                    data-order-id="{{ $order->id }}"
                                    data-photos="{{ $valPhotos->toJson() }}"
                                    onclick="openPhotoModal(this)" 
                                    class="flex-1 min-h-[44px] flex items-center justify-center bg-gray-50 hover:bg-orange-50 border border-gray-200 text-[#FFC232] rounded-xl transition-all active:scale-95" 
                                    title="Lihat Galeri Foto">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </button>

                            {{-- Camera Button --}}
                            <button onclick="openOrderCameraModal('{{ $order->id }}', '{{ $order->spk_number }}')" 
                                    class="flex-1 min-h-[44px] flex items-center justify-center bg-gray-50 hover:bg-indigo-50 border border-gray-200 text-indigo-600 rounded-xl transition-all active:scale-95" 
                                    title="Buka Kamera">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </button>

                            {{-- Upload Button --}}
                            <button onclick="openOrderUploadModal('{{ $order->id }}', '{{ $order->spk_number }}')" 
                                    class="flex-1 min-h-[44px] flex items-center justify-center bg-gray-50 hover:bg-purple-50 border border-gray-200 text-purple-600 rounded-xl transition-all active:scale-95" 
                                    title="Upload Foto Baru">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            </button>

                            {{-- Detail Button --}}
                            <a href="{{ route('admin.orders.show', $order->id) }}" 
                               class="flex-[2] min-h-[44px] flex items-center justify-center bg-[#22B086] hover:bg-[#1C8D6C] text-white text-xs font-bold rounded-xl transition-all shadow-sm active:scale-95">
                                Detail
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center p-8 text-gray-500 italic text-sm">Belum ada riwayat pesanan.</div>
                    @endforelse
                </div>

                {{-- Desktop Table View --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-left">
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">No. SPK</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal Masuk</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Detail Sepatu</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($customer->workOrders as $order)
                            <tr class="group hover:bg-gray-50/50 transition-all duration-300" 
                                x-show="!$store.customerDetail.orderSearch || '{{ strtolower($order->spk_number) }} {{ strtolower($order->shoe_brand) }} {{ strtolower($order->shoe_type) }}'.includes($store.customerDetail.orderSearch.toLowerCase())">
                                <td class="px-8 py-6">
                                    <div class="font-bold text-gray-900">{{ $order->spk_number }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-sm font-medium text-gray-600">{{ $order->entry_date->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $order->entry_date->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-xl">
                                            👟
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800">{{ $order->shoe_brand }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->shoe_type }} • {{ $order->shoe_color }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    @php
                                        $statusConfig = [
                                            'DONE' => ['bg' => 'bg-emerald-50', 'text' => 'text-[#1C8D6C]', 'icon' => '✅'],
                                            'CANCELLED' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'icon' => '❌'],
                                            'PROGRESS' => ['bg' => 'bg-orange-50', 'text' => 'text-[#FFB000]', 'icon' => '⚙️'],
                                        ];
                                        $statusClass = $statusConfig[$order->status->value ?? $order->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'icon' => '⏳'];
                                    @endphp
                                    <span class="px-3 py-1.5 rounded-lg text-xs font-bold border {{ $statusClass['bg'] }} {{ $statusClass['text'] }} border-transparent">
                                        {{ $order->status }}
                                    </span>
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @php
                                            $techs = [];
                                            if($order->prepWashingBy) $techs['Prep'] = $order->prepWashingBy->name;
                                            $prodName = $order->prodSolBy->name ?? $order->prodUpperBy->name ?? $order->prodCleaningBy->name ?? $order->technicianProduction->name ?? null;
                                            if($prodName) $techs['Prod'] = $prodName;
                                            $qcName = $order->qcFinalBy->name ?? $order->qcFinalPic->name ?? null;
                                            if($qcName) $techs['QC'] = $qcName;
                                        @endphp
                                        @foreach($techs as $label => $name)
                                            <span class="text-[9px] font-bold text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-200" title="{{ $label }}: {{ $name }}">
                                                {{ $label }}: {{ explode(' ', $name)[0] }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                        @php
                                            $valPhotos = $order->photos->map(function($p) {
                                                $size = 0;
                                                try {
                                                    if(\Illuminate\Support\Facades\Storage::disk('public')->exists($p->file_path)) {
                                                        $size = \Illuminate\Support\Facades\Storage::disk('public')->size($p->file_path);
                                                    }
                                                } catch(\Exception $e) {}
                                                $p->size_bytes = $size;
                                                $p->formatted_size = $size > 1048576 
                                                    ? round($size / 1048576, 2) . ' MB' 
                                                    : round($size / 1024, 2) . ' KB';
                                                return $p;
                                            });
                                        @endphp
                                        <button data-spk="{{ $order->spk_number }}" 
                                                data-order-id="{{ $order->id }}"
                                                data-photos="{{ $valPhotos->toJson() }}"
                                                onclick="openPhotoModal(this)" 
                                                class="p-2 bg-white border border-gray-200 rounded-lg text-[#FFC232] hover:bg-orange-50 hover:border-[#FFE399] transition-colors" title="Lihat Galeri Foto">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </button>
                                        <button onclick="openOrderCameraModal('{{ $order->id }}', '{{ $order->spk_number }}')" 
                                                class="p-2 bg-white border border-gray-200 rounded-lg text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 transition-colors" title="Buka Kamera">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </button>
                                        <button onclick="openOrderUploadModal('{{ $order->id }}', '{{ $order->spk_number }}')" 
                                                class="p-2 bg-white border border-gray-200 rounded-lg text-purple-600 hover:bg-purple-50 hover:border-purple-200 transition-colors" title="Upload Foto Baru">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        </button>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" 
                                           class="px-4 py-2 bg-[#22B086] text-white rounded-lg text-xs font-bold hover:bg-[#1C8D6C] transition-colors shadow-sm shadow-emerald-200">
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        <p class="font-medium text-lg">Belum ada riwayat pesanan</p>
                                        <p class="text-sm">Customer ini belum pernah melakukan transaksi</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

        </div>
    </div>

    {{-- MOBILE VIEW --}}
    <div class="block lg:hidden bg-[#F8FAFC] pb-24" x-cloak>
        @php
            $totalSpentMobile = $customer->workOrders->sum('total_price');
        @endphp
        <main class="p-4 space-y-6">
            <!-- BEGIN: Profile Section -->
            <section class="bg-white rounded-3xl p-6 shadow-sm border border-gray-50 relative overflow-hidden" data-purpose="user-profile">
                <!-- Background subtle circle decoration -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-gray-50 rounded-full opacity-50"></div>
                <div class="flex flex-col items-center text-center">
                    <div class="relative mb-4">
                        <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center text-3xl font-bold text-gray-500">
                            {{ substr($customer->name, 0, 2) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 bg-teal-500 border-4 border-white rounded-full p-1">
                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $customer->name }}</h2>
                    <div class="flex items-center gap-2 text-gray-500 text-sm mb-6">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>{{ $customer->phone }}</span>
                    </div>
                    <div class="flex w-full gap-3">
                        <button @click="$store.customerDetail.openEditor()" class="flex-1 py-3 px-4 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Profile
                        </button>
                        <a href="{{ route('admin.customers.index') }}" class="flex-1 py-3 px-4 bg-gray-900 rounded-xl text-sm font-semibold text-white flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </section>
            <!-- END: Profile Section -->

            <!-- BEGIN: Stats Cards -->
            <section class="grid grid-cols-1 gap-4" data-purpose="summary-statistics">
                <!-- Total Order -->
                <div class="bg-white p-5 rounded-3xl border border-gray-50 flex items-center justify-between shadow-sm">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Order</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-bold text-gray-900">{{ $customer->workOrders->count() }}</span>
                            <span class="text-gray-400 text-sm">Orders</span>
                        </div>
                        @php
                            $onlineCount = $customer->workOrders->where('channel', 'ONLINE')->count();
                            $offlineCount = $customer->workOrders->where('channel', 'OFFLINE')->count();
                        @endphp
                        <div class="flex gap-2 mt-2 pt-0.5">
                            <span class="px-2.5 py-0.5 text-[9px] font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wider">{{ $onlineCount }} Online</span>
                            <span class="px-2.5 py-0.5 text-[9px] font-bold rounded-lg bg-gray-100 text-gray-700 border border-gray-200 uppercase tracking-wider">{{ $offlineCount }} Offline</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-shoe-light-green rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-shoe-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                <!-- Total Spend -->
                <div class="bg-white p-5 rounded-3xl border border-gray-50 flex items-center justify-between shadow-sm">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Spend</p>
                        <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalSpentMobile, 0, ',', '.') }}</div>
                    </div>
                    <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <!-- Member Since -->
                <div class="bg-white p-5 rounded-3xl border border-gray-50 flex items-center justify-between shadow-sm">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Member Since</p>
                        <div class="text-2xl font-bold text-gray-900">{{ $customer->created_at->diffForHumans(null, true) }}</div>
                    </div>
                    <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </section>
            <!-- END: Stats Cards -->

            <!-- BEGIN: Address & Notes -->
            <section class="bg-white rounded-3xl p-6 shadow-sm border border-gray-50" data-purpose="address-notes">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-1.5 h-6 bg-shoe-green rounded-full"></div>
                    <h3 class="font-bold text-gray-800">Alamat &amp; Catatan</h3>
                </div>
                <div class="space-y-6">
                    <!-- Address -->
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-shoe-light-green rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-shoe-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Alamat Pengiriman</p>
                            <p class="text-sm font-bold text-gray-800">{{ $customer->address ?? 'Belum diisi' }}</p>
                            @if($customer->city)
                            <p class="text-xs italic text-gray-500">{{ $customer->city }}, {{ $customer->province }}</p>
                            @endif
                        </div>
                    </div>
                    <!-- Notes -->
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Catatan Customer</p>
                            <div class="bg-gray-50 rounded-xl p-4 text-xs text-gray-500 font-medium">
                                {{ $customer->notes ?? 'Tidak ada catatan khusus' }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- END: Address & Notes -->

            <!-- BEGIN: Documents & Photo -->
            <section class="bg-white rounded-3xl p-6 shadow-sm border border-gray-50" data-purpose="documents-section">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-6 bg-yellow-400 rounded-full"></div>
                        <h3 class="font-bold text-gray-800">Dokumen &amp; Foto CS ({{ $customer->photos->count() }})</h3>
                    </div>
                    <button onclick="openCustUploadModal()" class="bg-teal-500 hover:bg-teal-600 text-white text-xs font-bold py-2 px-4 rounded-lg flex items-center gap-1 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Upload Baru
                    </button>
                </div>
                
                @if($customer->photos->count() > 0)
                <div class="grid grid-cols-2 gap-4">
                    @foreach($customer->photos as $photo)
                    <div class="relative aspect-square rounded-xl overflow-hidden shadow-sm bg-gray-100" id="mobile-photo-container-{{ $photo->id }}">
                        <img src="{{ $photo->photo_url }}" 
                             class="w-full h-full object-cover cursor-pointer"
                             onclick="window.open('{{ $photo->photo_url }}', '_blank')">
                        <button onclick="deleteCustomerPhoto({{ $photo->id }})" 
                                class="absolute top-2 right-2 p-1.5 bg-red-600/80 hover:bg-red-700 text-white rounded-lg shadow-lg z-10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-3a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="border-2 border-dashed border-gray-100 rounded-2xl py-12 flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-400 font-medium">Belum ada dokumen foto</p>
                </div>
                @endif
            </section>
            <!-- END: Documents & Photo -->

            <!-- BEGIN: Order History -->
            <section class="bg-white rounded-3xl p-6 shadow-sm border border-gray-50" data-purpose="order-history">
                <div class="flex flex-col gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-shoe-light-green rounded-xl flex items-center justify-center text-shoe-green">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Riwayat Pesanan</h3>
                            <p class="text-xs text-gray-400 font-medium">
                                <span x-show="!$store.customerDetail.orderSearch">Total {{ $customer->workOrders->count() }} transaksi ditemukan</span>
                                <span x-show="$store.customerDetail.orderSearch" style="display: none;">
                                    Ditemukan <span class="text-shoe-green font-bold" x-text="document.querySelectorAll('[data-purpose=\'order-card\']:not([style*=\'display: none\'])').length"></span> hasil
                                </span>
                            </p>
                        </div>
                    </div>
                    <!-- Mobile Search Bar -->
                    <div class="relative">
                        <input class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border-none rounded-xl focus:ring-1 focus:ring-shoe-green font-bold text-gray-800 placeholder-gray-300" 
                               x-model="$store.customerDetail.orderSearch"
                               placeholder="Cari No. SPK atau Sepatu..." type="text"/>
                        <svg class="h-4 w-4 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Mobile List Layout for Table Data -->
                <div class="space-y-4">
                    @forelse($customer->workOrders as $order)
                    @php
                        $valPhotos = $order->photos->map(function($p) {
                            $size = 0;
                            try {
                                if(\Illuminate\Support\Facades\Storage::disk('public')->exists($p->file_path)) {
                                    $size = \Illuminate\Support\Facades\Storage::disk('public')->size($p->file_path);
                                }
                            } catch(\Exception $e) {}
                            $p->size_bytes = $size;
                            $p->formatted_size = $size > 1048576 
                                ? round($size / 1048576, 2) . ' MB' 
                                : round($size / 1024, 2) . ' KB';
                            return $p;
                        });
                    @endphp
                    <!-- Order Item Card -->
                    <div class="border border-gray-100 rounded-2xl p-4 bg-white" data-purpose="order-card"
                         x-show="!$store.customerDetail.orderSearch || '{{ strtolower($order->spk_number) }} {{ strtolower($order->shoe_brand) }} {{ strtolower($order->shoe_type) }}'.includes($store.customerDetail.orderSearch.toLowerCase())">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="text-xs font-bold text-gray-900">{{ $order->spk_number }}</p>
                                <p class="text-[10px] text-gray-400">{{ $order->entry_date->format('d M Y, H:i') }} WIB</p>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-1 bg-gray-100 text-gray-500 rounded">{{ $order->status }}</span>
                        </div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="h-6 w-6 text-indigo-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"></path>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-800 truncate">{{ $order->shoe_brand }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $order->shoe_type }} • {{ $order->shoe_color }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                            <div class="flex gap-2">
                                <button data-spk="{{ $order->spk_number }}" 
                                        data-order-id="{{ $order->id }}"
                                        data-photos="{{ $valPhotos->toJson() }}"
                                        onclick="openPhotoModal(this)"
                                        class="w-8 h-8 flex items-center justify-center bg-yellow-50 text-yellow-500 rounded-lg">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                        <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                </button>
                                <button onclick="openOrderCameraModal('{{ $order->id }}', '{{ $order->spk_number }}')"
                                        class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-500 rounded-lg">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                        <path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                </button>
                                <button onclick="openOrderUploadModal('{{ $order->id }}', '{{ $order->spk_number }}')"
                                        class="w-8 h-8 flex items-center justify-center bg-purple-50 text-purple-500 rounded-lg">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                </button>
                            </div>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="bg-teal-500 hover:bg-teal-600 text-white text-xs font-bold py-2 px-6 rounded-lg">Detail</a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400">
                        Belum ada riwayat pesanan
                    </div>
                    @endforelse
                </div>
            </section>
            <!-- END: Order History -->    {{-- Order Photo Gallery Modal (Reused Logic) --}}
    <template x-teleport="body">
    <div id="orderPhotoModal" class="hidden fixed inset-0 bg-gray-900/70 backdrop-blur-md z-[60] transition-opacity">
        <div class="flex items-center justify-center min-h-screen p-2 sm:p-4">
            <div class="bg-white rounded-3xl max-w-6xl w-full mx-2 sm:mx-4 overflow-hidden border border-gray-100 shadow-2xl flex flex-col max-h-[92vh]">
            <div class="p-5 sm:p-6 border-b border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-start md:items-center bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-yellow-50 text-yellow-500 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 leading-tight">Galeri Foto Order</h3>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-gray-400 mt-1 font-medium">
                            <span id="modalSpkNumber" class="font-mono text-gray-500 font-bold">SPK-XXX</span>
                            <span class="text-gray-300">|</span>
                            <span id="modalTotalSize" class="text-shoe-green font-bold">Total: 0 MB</span>
                            <span class="text-gray-300">|</span>
                            <span id="modalSpkPrintCount" class="px-2 py-0.5 bg-teal-50 text-teal-700 border border-teal-100 text-[10px] font-bold rounded-lg font-mono">Terpilih SPK: 0/2</span>
                        </div>
                    </div>
                </div>
                
                {{-- Bulk Select & SPK Print Actions --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                    <div id="bulkToolbar" class="flex flex-col sm:flex-row gap-2 flex-1 md:flex-initial">
                        <button type="button" id="btnToggleSelect" onclick="toggleSelectMode()" 
                                class="px-4 py-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-gray-600 font-bold text-xs uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <span id="btnSelectLabel">Pilih Foto</span>
                        </button>
                        <button type="button" id="btnSelectAll" onclick="selectAllPhotos()" class="hidden px-3 py-3 bg-gray-100 hover:bg-gray-200 rounded-xl text-gray-600 font-bold text-xs uppercase tracking-widest transition-all text-center">Pilih Semua</button>
                        <button type="button" id="btnDeleteBulk" onclick="deleteSelectedPhotos()" class="hidden px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold text-xs uppercase tracking-widest shadow-md transition-all disabled:opacity-50 text-center" disabled>Hapus</button>
                        
                        <button type="button" id="btnPrintSpkModal" onclick="printSpkFromModal()" 
                                class="px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white rounded-xl font-bold text-xs uppercase tracking-widest shadow-md transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            Cetak SPK
                        </button>
                    </div>
                    
                    <button onclick="document.getElementById('orderPhotoModal').classList.add('hidden'); document.body.classList.remove('overflow-hidden'); cancelBulkSelect();" 
                            class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 hover:text-red-500 transition-all mx-auto sm:mx-0 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
            
            <div class="p-4 sm:p-8 overflow-y-auto flex-1 custom-scrollbar bg-gray-50">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 h-full">
                    {{-- Before Column --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-xl">
                            <span class="w-3 h-3 rounded-full bg-red-500 animate-pulse shadow-[0_0_10px_rgba(239,68,68,0.5)]"></span>
                            <h4 class="font-bold text-red-400 tracking-wide uppercase text-sm">Kondisi Awal (Before)</h4>
                        </div>
                        <div id="beforePhotosContainer" class="space-y-4 min-h-[300px]">
                            {{-- Photos injected here --}}
                        </div>
                    </div>

                    {{-- After Column --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 mb-4 p-3 bg-green-500/10 border border-green-500/20 rounded-xl">
                            <span class="w-3 h-3 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                            <h4 class="font-bold text-green-400 tracking-wide uppercase text-sm">Hasil Akhir (After)</h4>
                        </div>
                        <div id="afterPhotosContainer" class="space-y-4 min-h-[300px]">
                            {{-- Photos injected here --}}
                        </div>
                    </div>
                </div>

                {{-- Other Photos --}}
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <h4 class="text-gray-400 font-bold mb-6 text-sm uppercase tracking-wider">Foto Lainnya / Proses</h4>
                    <div id="otherPhotosContainer" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                         {{-- Other photos --}}
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    </template>

    {{-- Zoom/Pan Editor Sub-modal --}}
    <template x-teleport="body">
    <div id="orderPhotoCropModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md z-[70] transition-opacity overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 py-8">
            <div class="bg-white rounded-3xl overflow-hidden border border-gray-150 shadow-2xl max-w-lg w-full flex flex-col my-auto">
                {{-- Header --}}
                <div class="flex justify-between items-center p-5 bg-gradient-to-r from-teal-500 to-emerald-600 text-white">
                    <h3 class="text-base font-bold flex items-center gap-2">
                        <span>📸</span> Atur Zoom & Crop Foto SPK
                    </h3>
                    <button type="button" onclick="closeImageZoomEditor()" class="text-white/80 hover:text-white hover:bg-white/10 p-1 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                {{-- Body --}}
                <div class="p-6 space-y-6 flex-1">
                    <div class="flex flex-col items-center">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5">Preview Hasil Cetak (Persegi)</span>
                        
                        <div id="cropPreviewContainer" 
                             onpointerdown="handleCropPointerDown(event)" 
                             onpointermove="handleCropPointerMove(event)" 
                             onpointerup="handleCropPointerUp(event)" 
                             onpointerleave="handleCropPointerUp(event)"
                             class="w-72 h-72 rounded-2xl overflow-hidden bg-gray-950 border-4 border-teal-500 shadow-xl relative cursor-move select-none">
                            <img id="cropPreviewImg" src="" class="w-full h-full pointer-events-none transition-transform duration-75 object-cover">
                            
                            {{-- Center target --}}
                            <div class="absolute inset-0 border border-white/20 rounded-full pointer-events-none flex items-center justify-center">
                                <div class="w-1.5 h-1.5 bg-white/40 rounded-full"></div>
                            </div>
                        </div>
                        <span class="text-[10px] text-gray-400 mt-2">💡 Tips: Anda dapat men-drag/geser foto langsung di area pratinjau</span>
                    </div>
                    
                    {{-- Sliders --}}
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label for="cropZoom" class="text-xs font-bold text-gray-600 uppercase tracking-wider">Perbesaran (Zoom)</label>
                                <span id="cropZoomVal" class="text-xs font-mono font-bold text-teal-600">1.00x</span>
                            </div>
                            <input type="range" id="cropZoom" min="1.0" max="3.0" step="0.05" value="1.0" oninput="updateCropTransform()"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-teal-500">
                        </div>

                        <div>
                            <span class="text-xs font-bold text-gray-600 uppercase tracking-wider block mb-2">Rotasi Arah</span>
                            <div class="grid grid-cols-4 gap-2" id="cropRotateContainer">
                                <button type="button" onclick="setCropRotation(0)" class="rotate-btn py-1.5 border rounded-lg text-xs font-bold font-mono transition-colors shadow-sm bg-teal-500 text-white border-teal-500">0°</button>
                                <button type="button" onclick="setCropRotation(90)" class="rotate-btn py-1.5 border rounded-lg text-xs font-bold font-mono transition-colors shadow-sm bg-gray-50 border-gray-200 text-gray-600">90°</button>
                                <button type="button" onclick="setCropRotation(180)" class="rotate-btn py-1.5 border rounded-lg text-xs font-bold font-mono transition-colors shadow-sm bg-gray-50 border-gray-200 text-gray-600">180°</button>
                                <button type="button" onclick="setCropRotation(270)" class="rotate-btn py-1.5 border rounded-lg text-xs font-bold font-mono transition-colors shadow-sm bg-gray-50 border-gray-200 text-gray-600">270°</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label for="cropPanX" class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Geser Horiz. (X)</label>
                                    <span id="cropPanXVal" class="text-[10px] font-mono font-bold text-gray-500">0%</span>
                                 </div>
                                <input type="range" id="cropPanX" min="-100" max="100" step="1" value="0" oninput="updateCropTransform()"
                                       class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-teal-500">
                            </div>
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label for="cropPanY" class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Geser Vert. (Y)</label>
                                    <span id="cropPanYVal" class="text-[10px] font-mono font-bold text-gray-500">0%</span>
                                 </div>
                                <input type="range" id="cropPanY" min="-100" max="100" step="1" value="0" oninput="updateCropTransform()"
                                       class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-teal-500">
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Footer --}}
                <div class="p-5 bg-gray-50 border-t border-gray-150 flex justify-end gap-3">
                    <button type="button" onclick="closeImageZoomEditor()"
                            class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 font-bold text-xs uppercase tracking-widest transition-all">
                        Batal
                    </button>
                    <button type="button" onclick="saveImageZoomSettings()"
                            class="px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 text-white rounded-xl font-bold text-xs uppercase tracking-widest shadow-md shadow-teal-500/10 hover:from-teal-600 hover:to-emerald-700 transition-all">
                        Terapkan
                    </button>
                </div>
            </div>
        </div>
    </div>
    </template>

    {{-- Order Upload Modal (Chunk Upload with Compression) --}}
    <template x-teleport="body">
    <div id="orderUploadModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[60] transition-all duration-300">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-[2rem] shadow-2xl max-w-xl w-full overflow-hidden transform transition-all scale-100 opacity-100 border border-gray-100 flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="p-8 text-center bg-white border-b border-gray-50">
                <div class="mx-auto w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mb-4 text-purple-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 leading-tight">Upload Foto Order</h3>
                <p class="text-sm text-gray-500 mt-2 font-medium">
                    Upload foto baru untuk <span id="uploadSpkNumber" class="text-purple-600 font-bold px-1">SPK-XXX</span>
                </p>
            </div>

            <div class="p-8 space-y-6 flex-1 overflow-y-auto">
                
                <!-- Dropzone Area -->
                <div class="space-y-2">
                    <label for="orderChunkFileInput" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Pilih File</label>
                    <div class="relative group">
                        <input type="file" id="orderChunkFileInput" name="order_files" multiple accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="border-2 border-dashed border-gray-200 group-hover:border-purple-300 bg-gray-50/50 group-hover:bg-purple-50/30 rounded-2xl p-8 transition-all duration-300 flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-400 group-hover:text-purple-500 transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <span id="orderChunkFileLabelText" class="text-sm font-bold text-gray-500 group-hover:text-purple-600 transition-colors text-center px-4">
                                Klik untuk pilih foto
                            </span>
                            <p id="orderChunkFileCountText" class="text-[10px] font-medium text-gray-400 mt-1 hidden"></p>
                        </div>
                    </div>
                </div>

                <!-- Progress Container (Hidden by Default) -->
                <div id="orderUploadProgress" class="hidden space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Progress Upload</span>
                        <span id="orderUploadProgressText" class="text-xs font-bold text-purple-600">0%</span>
                    </div>
                    <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                        <div id="orderUploadProgressBar" class="h-full bg-gradient-to-r from-purple-500 to-purple-600 transition-all duration-300 highlight-bar" style="width: 0%"></div>
                    </div>
                    <p id="orderUploadStatusText" class="text-xs text-gray-400 font-medium"></p>
                </div>

                <!-- Step Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="orderStep" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">TAHAPAN</label>
                        <div class="relative">
                            <select id="orderStep" required 
                                    class="appearance-none block w-full bg-white border-2 border-gray-100 rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 focus:outline-none focus:border-purple-500 focus:ring-0 transition-all cursor-pointer">
                                <option value="RECEPTION">📦 Foto Referensi</option>
                                <option value="WAREHOUSE_BEFORE">🏭 Gudang (Before)</option>
                                <option value="PRODUCTION">⚙️ Produksi / Proses</option>
                                <option value="QC">✨ Quality Control</option>
                                <option value="FINISH">🏁 Finish / Packing</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="orderCaption" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">CAPTION (OPSIONAL)</label>
                        <input type="text" id="orderCaption" name="order_caption" placeholder="Detail foto..." autocomplete="off"
                               class="block w-full bg-white border-2 border-gray-100 rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 focus:outline-none focus:border-purple-500 focus:ring-0 transition-all">
                    </div>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <button type="button" onclick="closeOrderUploadModal()"
                            class="w-full px-6 py-4 bg-gray-100 text-gray-500 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="button" id="orderUploadBtn" onclick="startOrderChunkUpload()"
                            class="w-full px-6 py-4 bg-purple-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-purple-600/20 hover:bg-purple-700 hover:shadow-purple-700/30 transform hover:-translate-y-0.5 active:translate-y-0 transition-all disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Upload
                    </button>
                </div>
            </div>
            </div>
        </div>
    </div>
    </template>

    {{-- Order Camera Modal (Live Camera with WebRTC - Portrait view) --}}
    <template x-teleport="body">
    <div x-data="orderCameraCapture()" 
         id="orderCameraModal" 
         class="hidden fixed inset-0 bg-gray-900/75 backdrop-blur-md z-[70] transition-all duration-300"
         @open-order-camera.window="openModal($event.detail)"
         @close-order-camera.window="closeModal()">
        
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all scale-100 opacity-100 flex flex-col border border-gray-100 max-h-[90vh]">
            <!-- Header -->
            <div class="p-6 text-center bg-white border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 animate-pulse">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h3 class="text-lg font-black text-gray-900 leading-tight">Ambil Foto SPK</h3>
                        <p class="text-xs text-gray-500 font-medium">
                            Kamera langsung untuk SPK <span x-text="spkNumber" class="text-indigo-600 font-black"></span>
                        </p>
                    </div>
                </div>
                <button type="button" @click="closeModal()" 
                        class="w-10 h-10 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-6 flex-1 overflow-y-auto">
                <!-- Device Selector Dropdown -->
                <div>
                    <label for="cameraDeviceSelect" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">PILIH SUMBER KAMERA (CONTOH: DROIDCAM)</label>
                    <div class="relative">
                        <select id="cameraDeviceSelect" x-model="selectedDeviceId" @change="changeDevice()"
                                class="appearance-none block w-full bg-white border-2 border-gray-100 rounded-2xl py-3.5 px-4 pr-10 text-xs font-bold text-gray-800 focus:outline-none focus:border-indigo-500 focus:ring-0 transition-all cursor-pointer">
                            <template x-for="device in devices" :key="device.id">
                                <option :value="device.id" x-text="device.label"></option>
                            </template>
                            <template x-if="devices.length === 0">
                                <option value="">Mengakses kamera...</option>
                            </template>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                </div>

                <!-- Camera view area - Portrait (aspect-3/4) optimized -->
                <div class="relative w-full aspect-[3/4] overflow-hidden bg-gray-950 rounded-3xl border border-gray-800 shadow-inner cursor-crosshair"
                     @click="triggerFocus($event)">
                    <!-- Video stream tag -->
                    <video x-ref="videoElement" autoplay playsinline class="absolute inset-0 w-full h-full object-cover" x-show="streamActive && !isCaptured"></video>

                    <!-- Static Image snapshot preview tag -->
                    <img :src="capturedPhotoUrl" class="absolute inset-0 w-full h-full object-cover" x-show="isCaptured" x-cloak>

                    <!-- Programmatic snapshot canvas (hidden, used only for capture) -->
                    <canvas x-ref="canvasElement" class="hidden"></canvas>

                    <!-- AF/AE Lock Banner -->
                    <div x-show="streamActive && !isCaptured && (focusMode === 'manual' || focusMode === 'single-shot')"
                         class="absolute top-4 left-1/2 -translate-x-1/2 bg-amber-400 text-gray-950 font-black text-[9px] tracking-[0.2em] uppercase px-3 py-1 rounded-full shadow-lg z-20 animate-pulse"
                         x-cloak>
                        AE/AF LOCK
                    </div>

                    <!-- Tap-to-Focus Ring Overlay -->
                    <div x-show="focusRing.show" 
                         :style="`left: ${focusRing.x}px; top: ${focusRing.y}px;`" 
                         class="absolute w-14 h-14 border-2 border-amber-400 rounded-lg pointer-events-none z-30 -translate-x-1/2 -translate-y-1/2 animate-camera-focus"
                         x-cloak>
                        <!-- Center indicator dot -->
                        <div class="absolute inset-0 m-auto w-1 h-1 bg-amber-400 rounded-full"></div>
                        <!-- Corner brackets -->
                        <div class="absolute -top-1 -left-1 w-2 h-2 border-t-2 border-l-2 border-amber-400"></div>
                        <div class="absolute -top-1 -right-1 w-2 h-2 border-t-2 border-r-2 border-amber-400"></div>
                        <div class="absolute -bottom-1 -left-1 w-2 h-2 border-b-2 border-l-2 border-amber-400"></div>
                        <div class="absolute -bottom-1 -right-1 w-2 h-2 border-b-2 border-r-2 border-amber-400"></div>
                    </div>

                    <!-- Manual Focus Slider (Pro Control) -->
                    <div x-show="streamActive && !isCaptured && supportManualFocus" 
                         @click.stop
                         class="absolute right-4 top-1/2 -translate-y-1/2 flex flex-col items-center gap-2 bg-black/60 backdrop-blur-md px-2.5 py-4 rounded-2xl border border-white/10 z-20 transition-opacity duration-300"
                         x-cloak>
                        <span class="text-[7px] font-black text-amber-400 uppercase tracking-widest rotate-90 mb-6">FOCUS</span>
                        <input type="range" :min="focusMin" :max="focusMax" :step="focusStep" x-model="focusDistance" @input="adjustFocus()"
                               class="h-28 cursor-pointer accent-amber-400" style="appearance: slider-vertical; -webkit-appearance: slider-vertical; width: 6px;">
                    </div>

                    <!-- Zoom Slider (Pro Control) -->
                    <div x-show="streamActive && !isCaptured && supportZoom" 
                         @click.stop
                         class="absolute left-4 top-1/2 -translate-y-1/2 flex flex-col items-center gap-2 bg-black/60 backdrop-blur-md px-2.5 py-4 rounded-2xl border border-white/10 z-20 transition-opacity duration-300"
                         x-cloak>
                        <span class="text-[7px] font-black text-indigo-400 uppercase tracking-widest rotate-90 mb-6">ZOOM</span>
                        <input type="range" :min="zoomMin" :max="zoomMax" :step="zoomStep" x-model="zoomValue" @input="adjustZoom()"
                               class="h-28 cursor-pointer accent-indigo-400" style="appearance: slider-vertical; -webkit-appearance: slider-vertical; width: 6px;">
                    </div>

                    <!-- Focus Mode Toggle Buttons -->
                    <div x-show="streamActive && !isCaptured" 
                         class="absolute bottom-20 left-1/2 -translate-x-1/2 flex items-center bg-black/60 backdrop-blur-md px-1 py-1 rounded-full border border-white/10 z-20 gap-1 transition-all duration-300"
                         @click.stop
                         x-cloak>
                        <button type="button" @click="setFocusMode('continuous')"
                                :class="focusMode === 'continuous' ? 'bg-amber-400 text-gray-950 font-black' : 'text-white/70 hover:text-white font-bold'"
                                class="text-[8px] px-2.5 py-1 rounded-full uppercase tracking-wider transition-all duration-300">
                            Auto
                        </button>
                        <button type="button" @click="setFocusMode('single-shot')"
                                :class="focusMode === 'single-shot' ? 'bg-amber-400 text-gray-950 font-black' : 'text-white/70 hover:text-white font-bold'"
                                class="text-[8px] px-2.5 py-1 rounded-full uppercase tracking-wider transition-all duration-300">
                            Lock
                        </button>
                        <button type="button" @click="setFocusMode('manual')"
                                x-show="supportManualFocus"
                                :class="focusMode === 'manual' ? 'bg-amber-400 text-gray-950 font-black' : 'text-white/70 hover:text-white font-bold'"
                                class="text-[8px] px-2.5 py-1 rounded-full uppercase tracking-wider transition-all duration-300"
                                x-cloak>
                            Manual
                        </button>
                    </div>

                    <!-- Toggle Camera switch & Capture overlays -->
                    <div class="absolute bottom-0 inset-x-0 p-4 bg-gradient-to-t from-black/80 to-transparent flex justify-center items-center gap-4">
                        <!-- Switch camera facing -->
                        <button type="button" @click="switchCamera()" x-show="!isCaptured"
                                class="p-3 bg-gray-800/90 hover:bg-gray-700 text-white rounded-full backdrop-blur-sm transition-all hover:scale-105 active:scale-95" title="Ganti Kamera">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </button>

                        <!-- Shutter button -->
                        <button type="button" @click="captureImage()" x-show="!isCaptured"
                                class="w-14 h-14 bg-white border-4 border-gray-400 rounded-full hover:bg-gray-200 hover:scale-105 shadow-[0_0_15px_rgba(255,255,255,0.4)] transition-all active:scale-90" title="Ambil Foto">
                        </button>

                        <!-- When photo is captured, display retake / redo options -->
                        <div x-show="isCaptured" class="flex gap-2 w-full justify-between items-center px-2" x-cloak>
                            <button type="button" @click="retakePhoto()"
                                    class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-colors border border-gray-700 flex items-center gap-1.5 shadow-lg">
                                🔄 Foto Ulang
                            </button>
                            <span class="text-[10px] text-gray-300 font-bold bg-black/40 px-2.5 py-1.5 rounded-lg border border-white/10 backdrop-blur-sm">Foto Terkunci & Siap Disimpan</span>
                        </div>
                    </div>

                    <!-- Loader overlay -->
                    <div x-show="isLoading" class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center backdrop-blur-sm z-30">
                        <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-indigo-400 mb-3"></div>
                        <span class="text-xs text-indigo-400 font-black uppercase tracking-widest animate-pulse">Menyimpan & Watermarking...</span>
                    </div>
                </div>

                <!-- Form Controls -->
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="cameraOrderStep" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">TAHAPAN DOKUMENTASI</label>
                            <div class="relative">
                                <select id="cameraOrderStep" x-model="step" required 
                                        class="appearance-none block w-full bg-white border-2 border-gray-100 rounded-2xl py-3.5 px-4 text-xs font-bold text-gray-800 focus:outline-none focus:border-indigo-500 focus:ring-0 transition-all cursor-pointer">
                                    <option value="RECEPTION">📦 Foto Referensi / Awal</option>
                                    <option value="WAREHOUSE_BEFORE">🏭 Gudang (Before)</option>
                                    <option value="PRODUCTION">⚙️ Produksi / Proses</option>
                                    <option value="QC">✨ Quality Control (QC)</option>
                                    <option value="FINISH">🏁 Finish / Packing</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="cameraOrderCaption" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">CAPTION / KETERANGAN</label>
                            <input type="text" id="cameraOrderCaption" x-model="caption" placeholder="Detail atau catatan kaki foto..." autocomplete="off"
                                   class="block w-full bg-white border-2 border-gray-100 rounded-2xl py-3.5 px-4 text-xs font-bold text-gray-800 focus:outline-none focus:border-indigo-500 focus:ring-0 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Session Shelf (Newly Uploaded Photos Gallery) -->
                <div x-show="sessionPhotos.length > 0" class="mt-4 pt-4 border-t border-gray-100" style="display: none;">
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Foto Terunggah Sesi Ini (<span x-text="sessionPhotos.length"></span>)
                    </h4>
                    <div class="flex gap-3 overflow-x-auto pb-2 custom-scrollbar snap-x">
                        <template x-for="(p, index) in sessionPhotos" :key="p.id">
                            <div class="relative w-20 h-20 rounded-xl overflow-hidden border-2 border-gray-100 shadow-sm snap-start flex-shrink-0 group">
                                <img :src="p.url" class="w-full h-full object-cover">
                                <!-- Step Badge -->
                                <div class="absolute bottom-0 inset-x-0 bg-black/75 text-[8px] font-black text-white text-center py-0.5 truncate uppercase" x-text="p.step"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Footer buttons -->
                <div class="grid grid-cols-2 gap-4 pt-2">
                    <button type="button" @click="closeModal()"
                            :class="shouldReloadOnClose ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-xl shadow-emerald-600/20' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                            class="w-full px-6 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all">
                        <span x-text="shouldReloadOnClose ? 'Selesai & Reload' : 'Batal'"></span>
                    </button>
                    <button type="button" @click="saveAndSubmitPhoto()"
                            class="w-full px-6 py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 hover:shadow-indigo-700/30 transform hover:-translate-y-0.5 active:translate-y-0 transition-all disabled:opacity-50"
                            :disabled="isLoading">
                        <span x-show="!isLoading" x-text="isCaptured ? 'Simpan Foto' : 'Ambil & Simpan'"></span>
                        <span x-show="isLoading" style="display: none;">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
        </div>
    </div>
    </template>

    {{-- Customer Profile Upload Modal (Chunk Upload with Compression) --}}
    <template x-teleport="body">
    <div id="uploadModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[60] transition-all duration-300">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-[2rem] shadow-2xl max-w-xl w-full overflow-hidden transform transition-all scale-100 opacity-100 border border-gray-100 relative">
            <!-- Header -->
            <div class="p-8 text-center bg-white border-b border-gray-50">
                <div class="mx-auto w-16 h-16 bg-teal-50 rounded-2xl flex items-center justify-center mb-4 text-[#22B086]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 leading-tight">Upload Dokumen Customer</h3>
                <p class="text-sm text-gray-500 mt-2 font-medium">Upload file identitas atau dokumen pendukung</p>
            </div>

            <div class="p-8 space-y-6">
                <!-- Dropzone Area -->
                <div class="space-y-2">
                    <label for="custChunkFileInput" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Pilih File</label>
                    <div class="relative group">
                        <input type="file" id="custChunkFileInput" name="cust_files" multiple accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="border-2 border-dashed border-gray-200 group-hover:border-[#22B086]/30 bg-gray-50/50 group-hover:bg-[#22B086]/10 rounded-2xl p-8 transition-all duration-300 flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-400 group-hover:text-[#22B086] transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <span id="custChunkFileLabelText" class="text-sm font-bold text-gray-500 group-hover:text-[#22B086] transition-colors text-center px-4">
                                Klik untuk pilih dokumen
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Progress Container (Hidden by Default) -->
                <div id="custUploadProgress" class="hidden space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Progress Upload</span>
                        <span id="custUploadProgressText" class="text-xs font-bold text-[#22B086]">0%</span>
                    </div>
                    <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                        <div id="custUploadProgressBar" class="h-full bg-gradient-to-r from-[#22B086] to-[#1C8D6C] transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p id="custUploadStatusText" class="text-xs text-gray-400 font-medium"></p>
                </div>

                <!-- Meta Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="custDocType" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">JENIS DOKUMEN</label>
                        <div class="relative">
                            <select id="custDocType" name="cust_doc_type"
                                    class="appearance-none block w-full bg-white border-2 border-gray-100 rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 focus:outline-none focus:border-[#22B086] focus:ring-0 transition-all cursor-pointer">
                                <option value="general">📄 Dokumen Umum</option>
                                <option value="before">📸 Foto Awal (Before)</option>
                                <option value="after">✨ Foto Akhir (After)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="custDocCaption" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">KETERANGAN</label>
                        <input type="text" id="custDocCaption" name="cust_doc_caption" placeholder="Contoh: KTP Susi..." autocomplete="off"
                               class="block w-full bg-white border-2 border-gray-100 rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 focus:outline-none focus:border-[#22B086] focus:ring-0 transition-all">
                    </div>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <button type="button" onclick="closeCustUploadModal()"
                            class="w-full px-6 py-4 bg-gray-100 text-gray-500 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="button" id="custUploadBtn" onclick="startCustChunkUpload()"
                            class="w-full px-6 py-4 bg-[#22B086] text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-emerald-600/20 hover:bg-[#1C8D6C] hover:shadow-emerald-600/30 transform hover:-translate-y-0.5 active:translate-y-0 transition-all disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Upload
                    </button>
                </div>
            </div>
        </div>
        </div>
    </div>
    </template>

    {{-- Premium Inline Editor Modal --}}
    <template x-teleport="body">
        <div x-show="$store.customerDetail.showEditor" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/40 backdrop-blur-md flex items-center justify-center z-[100] p-4"
             style="display: none;">
            
            <div @click.away="$store.customerDetail.closeEditor()" 
                 x-show="$store.customerDetail.showEditor"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="bg-white rounded-[2.5rem] shadow-[0_30px_100px_rgba(0,0,0,0.15)] max-w-2xl w-full overflow-hidden border border-gray-100">
                
                {{-- Modal Header --}}
                <div class="px-10 py-10 bg-gradient-to-br from-gray-50 to-white border-b border-gray-50 flex justify-between items-center relative">
                    <div class="absolute top-0 right-0 p-10 pointer-events-none">
                        <div class="w-32 h-32 bg-[#22B086]/5 rounded-full blur-3xl"></div>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-3xl font-black text-gray-900 tracking-tight">Edit Identitas</h3>
                        <p class="text-gray-400 font-medium mt-1 uppercase text-[10px] tracking-[0.2em]">Pembaruan Data Customer Master</p>
                    </div>
                    <button @click="$store.customerDetail.closeEditor()" class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-gray-100 flex items-center justify-center text-gray-400 hover:text-red-500 hover:rotate-90 transition-all duration-300 relative z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" @submit="$store.customerDetail.isUpdating = true" class="px-10 py-10 space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Name --}}
                        <div class="space-y-3">
                            <label for="edit_name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Nama Lengkap</label>
                            <input type="text" id="edit_name" name="name" x-model="$store.customerDetail.tempData.name" required autocomplete="name"
                                   class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl text-sm font-bold text-gray-800 focus:outline-none focus:border-[#22B086] focus:bg-white transition-all">
                        </div>

                        {{-- Phone --}}
                        <div class="space-y-3">
                            <label for="edit_phone" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Nomor Telepon</label>
                            <input type="text" id="edit_phone" name="phone" x-model="$store.customerDetail.tempData.phone" required autocomplete="tel"
                                   class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl text-sm font-bold text-gray-800 focus:outline-none focus:border-[#22B086] focus:bg-white transition-all">
                        </div>

                        {{-- Email --}}
                        <div class="space-y-3">
                            <label for="edit_email" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Email</label>
                            <input type="email" id="edit_email" name="email" x-model="$store.customerDetail.tempData.email" autocomplete="email"
                                   class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl text-sm font-bold text-gray-800 focus:outline-none focus:border-[#22B086] focus:bg-white transition-all">
                        </div>

                        {{-- City --}}
                        <div class="space-y-3">
                            <label for="edit_city" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Kota</label>
                            <input type="text" id="edit_city" name="city" x-model="$store.customerDetail.tempData.city" autocomplete="address-level2"
                                   class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl text-sm font-bold text-gray-800 focus:outline-none focus:border-[#22B086] focus:bg-white transition-all">
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="space-y-3">
                        <label for="edit_address" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Alamat Lengkap</label>
                        <textarea id="edit_address" name="address" x-model="$store.customerDetail.tempData.address" rows="3" autocomplete="street-address"
                                  class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl text-sm font-bold text-gray-800 focus:outline-none focus:border-[#22B086] focus:bg-white transition-all resize-none"></textarea>
                    </div>

                    {{-- Notes --}}
                    <div class="space-y-3">
                        <label for="edit_notes" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Catatan Khusus</label>
                        <input type="text" id="edit_notes" name="notes" x-model="$store.customerDetail.tempData.notes" autocomplete="off"
                               class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl text-sm font-bold text-gray-800 focus:outline-none focus:border-[#22B086] focus:bg-white transition-all">
                    </div>

                    <div class="pt-6 flex gap-4">
                        <button type="button" @click="$store.customerDetail.closeEditor()" class="flex-1 px-8 py-5 bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-[1.5rem] font-black text-xs uppercase tracking-widest transition-all">
                            Batal
                        </button>
                        <button type="submit" class="flex-[2] px-8 py-5 bg-[#22B086] text-white rounded-[1.5rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-emerald-500/20 hover:bg-[#1C8D6C] hover:-translate-y-1 transition-all disabled:opacity-50"
                                :disabled="$store.customerDetail.isUpdating">
                            <span x-show="!$store.customerDetail.isUpdating">Simpan Perubahan</span>
                            <span x-show="$store.customerDetail.isUpdating" style="display: none;">Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <script>
        function showToast(message, type = 'success') {
            if (window.showToast) {
                window.showToast(message, type);
            } else {
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: type, message: message }
                }));
            }
        }

        function initCustomerStore() {
            if (window.Alpine && !window.Alpine.store('customerDetail')) {
                Alpine.store('customerDetail', {
                    id: {{ $customer->id }},
                    name: @js($customer->name),
                    phone: @js($customer->phone),
                    email: @js($customer->email),
                    address: @js($customer->address),
                    city: @js($customer->city),
                    province: @js($customer->province),
                    notes: @js($customer->notes),
                    
                    orderSearch: '',
                    showEditor: false,
                    isUpdating: false,
                    tempData: {},

                    activeTab: 'profil',
                    isMobile: window.innerWidth < 1024,

                    init() {
                        window.addEventListener('resize', () => {
                            this.isMobile = window.innerWidth < 1024;
                        });
                    },

                    openEditor() {
                        this.tempData = {
                            name: this.name,
                            phone: this.phone,
                            email: this.email,
                            address: this.address,
                            city: this.city,
                            province: this.province,
                            notes: this.notes
                        };
                        this.showEditor = true;
                    },

                    closeEditor() {
                        this.showEditor = false;
                    }
                });
            }
        }

        async function deleteCustomerPhoto(photoId) {
            if (!confirm('Yakin ingin menghapus dokumen ini?')) return;
            
            try {
                const res = await fetch(`/admin/customers/photos/${photoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await res.json();
                if (data.success) {
                    const el = document.getElementById(`photo-container-${photoId}`);
                    if (el) {
                        el.style.opacity = '0';
                        el.style.transform = 'scale(0.9)';
                        setTimeout(() => el.remove(), 300);
                    }
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'success', message: data.message || 'Dokumen berhasil dihapus.' }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: 'Gagal: ' + data.message }
                    }));
                }
            } catch (e) {
                console.error(e);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'error', message: 'Terjadi kesalahan network' }
                }));
            }
        }

        document.addEventListener('alpine:init', initCustomerStore);
        if (window.Alpine) initCustomerStore();
    </script>

    {{-- Script for Gallery --}}
    <script>
        // Bulk Selection State
        let isSelectMode = false;
        let selectedPhotoIds = [];
        let currentPhotosData = [];
        let currentSpkNumber = '';

        function toggleSelectMode() {
            isSelectMode = !isSelectMode;
            const label = document.getElementById('btnSelectLabel');
            const selectAllBtn = document.getElementById('btnSelectAll');
            const deleteBtn = document.getElementById('btnDeleteBulk');

            if (isSelectMode) {
                label.textContent = 'Batal';
                selectAllBtn.classList.remove('hidden');
                deleteBtn.classList.remove('hidden');
                document.querySelectorAll('.photo-checkbox').forEach(cb => cb.classList.remove('hidden'));
                document.querySelectorAll('.photo-item').forEach(el => {
                    el.classList.add('ring-2', 'ring-transparent');
                });
            } else {
                cancelBulkSelect();
            }
        }

        function cancelBulkSelect() {
            isSelectMode = false;
            selectedPhotoIds = [];
            const label = document.getElementById('btnSelectLabel');
            const selectAllBtn = document.getElementById('btnSelectAll');
            const deleteBtn = document.getElementById('btnDeleteBulk');

            if(label) label.textContent = 'Pilih Foto';
            if(selectAllBtn) selectAllBtn.classList.add('hidden');
            if(deleteBtn) { deleteBtn.classList.add('hidden'); deleteBtn.disabled = true; deleteBtn.textContent = 'Hapus'; }
            document.querySelectorAll('.photo-checkbox').forEach(cb => { cb.classList.add('hidden'); cb.checked = false; });
            document.querySelectorAll('.photo-item').forEach(el => {
                el.classList.remove('ring-[#22B086]', 'ring-2');
                el.classList.add('ring-transparent');
            });
        }

        function selectAllPhotos() {
            selectedPhotoIds = currentPhotosData.map(p => p.id);
            document.querySelectorAll('.photo-checkbox').forEach(cb => { cb.checked = true; });
            document.querySelectorAll('.photo-item').forEach(el => {
                el.classList.add('ring-[#22B086]');
                el.classList.remove('ring-transparent');
            });
            updateDeleteButton();
        }

        function togglePhotoSelection(photoId, wrapper, checkbox) {
            if (checkbox.checked) {
                if (!selectedPhotoIds.includes(photoId)) selectedPhotoIds.push(photoId);
                wrapper.classList.add('ring-[#22B086]');
                wrapper.classList.remove('ring-transparent');
            } else {
                selectedPhotoIds = selectedPhotoIds.filter(id => id !== photoId);
                wrapper.classList.remove('ring-[#22B086]');
                wrapper.classList.add('ring-transparent');
            }
            updateDeleteButton();
        }

        function updateDeleteButton() {
            const deleteBtn = document.getElementById('btnDeleteBulk');
            if (deleteBtn) {
                deleteBtn.disabled = selectedPhotoIds.length === 0;
                deleteBtn.textContent = `Hapus (${selectedPhotoIds.length})`;
            }
        }

        async function deleteSelectedPhotos() {
            if (selectedPhotoIds.length === 0) return;
            if (!confirm(`Yakin ingin menghapus ${selectedPhotoIds.length} foto secara PERMANEN? File akan dihapus dari server.`)) return;

            try {
                const response = await fetch('{{ route("photos.bulk-destroy") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ids: selectedPhotoIds })
                });

                const result = await response.json();
                if (result.success) {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'success', message: result.message || 'Foto berhasil dihapus.' }
                    }));
                    // Refresh the modal by filtering out deleted photos
                    currentPhotosData = currentPhotosData.filter(p => !selectedPhotoIds.includes(p.id));
                    cancelBulkSelect();
                    openPhotoModal(currentSpkNumber, currentPhotosData);
                } else {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: 'Gagal menghapus foto: ' + (result.message || 'Error unknown') }
                    }));
                }
            } catch (error) {
                console.error(error);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'error', message: 'Terjadi kesalahan saat menghapus foto.' }
                }));
            }
        }

        // Global SPK Photo configurations state
        let currentOrderId = null;
        let currentEditingPhoto = null;
        let cropScale = 1.0;
        let cropPanX = 0;
        let cropPanY = 0;
        let cropRotate = 0;
        let isDraggingCrop = false;
        let dragStartX = 0;
        let dragStartY = 0;

        // Setup helper functions for drag-to-pan in editor
        function handleCropPointerDown(e) {
            isDraggingCrop = true;
            const clientX = e.clientX;
            const clientY = e.clientY;
            dragStartX = clientX - (cropPanX * cropScale * 2.5);
            dragStartY = clientY - (cropPanY * cropScale * 2.5);
            e.currentTarget.setPointerCapture(e.pointerId);
        }

        function handleCropPointerMove(e) {
            if (!isDraggingCrop) return;
            const clientX = e.clientX;
            const clientY = e.clientY;
            
            const deltaX = clientX - dragStartX;
            const deltaY = clientY - dragStartY;

            cropPanX = Math.round(deltaX / (cropScale * 2.5));
            cropPanY = Math.round(deltaY / (cropScale * 2.5));

            cropPanX = Math.max(-100, Math.min(100, cropPanX));
            cropPanY = Math.max(-100, Math.min(100, cropPanY));

            document.getElementById('cropPanX').value = cropPanX;
            document.getElementById('cropPanY').value = cropPanY;
            updateCropTransform();
        }

        function handleCropPointerUp(e) {
            isDraggingCrop = false;
        }

        function openImageZoomEditor(photo, photoUrl) {
            currentEditingPhoto = photo;
            
            // Safe JSON decode or parse
            let settings = {zoom: 1.0, x: 0, y: 0, rotate: 0};
            if (photo.print_settings) {
                try {
                    settings = typeof photo.print_settings === 'string' 
                        ? JSON.parse(photo.print_settings) 
                        : photo.print_settings;
                } catch(e) {
                    console.error("Failed to parse settings:", e);
                }
            }

            cropScale = settings.zoom || 1.0;
            cropPanX = settings.x || 0;
            cropPanY = settings.y || 0;
            cropRotate = settings.rotate || 0;

            document.getElementById('cropPreviewImg').src = photoUrl;
            document.getElementById('cropZoom').value = cropScale;
            document.getElementById('cropPanX').value = cropPanX;
            document.getElementById('cropPanY').value = cropPanY;
            
            setCropRotation(cropRotate);
            updateCropTransform();

            document.getElementById('orderPhotoCropModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeImageZoomEditor() {
            document.getElementById('orderPhotoCropModal').classList.add('hidden');
        }

        function updateCropTransform() {
            cropScale = parseFloat(document.getElementById('cropZoom').value);
            cropPanX = parseInt(document.getElementById('cropPanX').value);
            cropPanY = parseInt(document.getElementById('cropPanY').value);
            
            document.getElementById('cropZoomVal').textContent = cropScale.toFixed(2) + 'x';
            document.getElementById('cropPanXVal').textContent = cropPanX + '%';
            document.getElementById('cropPanYVal').textContent = cropPanY + '%';

            const img = document.getElementById('cropPreviewImg');
            img.style.transform = `scale(${cropScale}) translate(${cropPanX}%, ${cropPanY}%) rotate(${cropRotate}deg)`;
        }

        function setCropRotation(degree) {
            cropRotate = degree;
            const buttons = document.querySelectorAll('.rotate-btn');
            buttons.forEach((btn, index) => {
                const degs = [0, 90, 180, 270];
                if (degs[index] === degree) {
                    btn.className = 'rotate-btn py-1.5 border rounded-lg text-xs font-bold font-mono transition-colors shadow-sm bg-teal-500 text-white border-teal-500';
                } else {
                    btn.className = 'rotate-btn py-1.5 border rounded-lg text-xs font-bold font-mono transition-colors shadow-sm bg-gray-50 border-gray-200 text-gray-600';
                }
            });
            updateCropTransform();
        }

        async function saveImageZoomSettings() {
            const applyBtns = document.querySelectorAll('#orderPhotoCropModal button[onclick="saveImageZoomSettings()"]');
            const applyBtn = applyBtns[0];
            const originalText = applyBtn ? applyBtn.innerHTML : 'Terapkan';
            
            if (applyBtn) {
                applyBtn.innerHTML = '<span class="inline-block animate-spin mr-1">🌀</span> Menyimpan...';
                applyBtn.disabled = true;
            }

            try {
                // Update local model first
                currentEditingPhoto.print_settings = {
                    zoom: cropScale,
                    x: cropPanX,
                    y: cropPanY,
                    rotate: cropRotate
                };
                // Deselect all other photos (enforce single print photo selection)
                currentPhotosData.forEach(p => {
                    if (p.id != currentEditingPhoto.id) p.is_printed = false;
                });
                currentEditingPhoto.is_printed = true; // Auto check for print

                // Perform AJAX Auto Save to Database
                const response = await fetch(`/assessment/${currentOrderId}/gallery-spk`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        photos: currentPhotosData.map(p => ({
                            id: p.id,
                            is_printed: p.is_printed ? true : false,
                            print_settings: typeof p.print_settings === 'string' ? JSON.parse(p.print_settings) : p.print_settings
                        }))
                    })
                });

                const result = await response.json();
                if (result.success) {
                    closeImageZoomEditor();
                    
                    // Refresh modal contents to apply transforms immediately
                    openPhotoModal(currentSpkNumber, currentPhotosData);

                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'success', message: 'Konfigurasi foto SPK berhasil disimpan.' }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: result.message || 'Terjadi kesalahan sistem.' }
                    }));
                }
            } catch (e) {
                console.error("Auto save failed:", e);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'error', message: 'Gagal menghubungi server untuk menyimpan konfigurasi.' }
                }));
            } finally {
                if (applyBtn) {
                    applyBtn.innerHTML = originalText;
                    applyBtn.disabled = false;
                }
            }
        }

        async function togglePrintPhoto(photoId) {
            const photo = currentPhotosData.find(p => p.id == photoId);
            if (!photo) return;

            const wasPrinted = photo.is_printed;

            // Deselect all others first (enforce single print photo selection)
            currentPhotosData.forEach(p => p.is_printed = false);

            // Toggle state locally
            photo.is_printed = !wasPrinted;
            
            // Refresh modal contents to show loading/updated state
            openPhotoModal(currentSpkNumber, currentPhotosData);

            try {
                // Auto-save update to database
                const response = await fetch(`/assessment/${currentOrderId}/gallery-spk`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        photos: currentPhotosData.map(p => ({
                            id: p.id,
                            is_printed: p.is_printed ? true : false,
                            print_settings: typeof p.print_settings === 'string' ? JSON.parse(p.print_settings) : p.print_settings
                        }))
                    })
                });

                const result = await response.json();
                if (result.success) {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'success', message: 'Pilihan foto cetak SPK diperbarui.' }
                    }));
                } else {
                    // Revert state if failed
                    photo.is_printed = !photo.is_printed;
                    openPhotoModal(currentSpkNumber, currentPhotosData);
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: result.message || 'Terjadi kesalahan sistem.' }
                    }));
                }
            } catch (e) {
                console.error("Auto save failed on print toggle:", e);
                // Revert state if failed
                photo.is_printed = !photo.is_printed;
                openPhotoModal(currentSpkNumber, currentPhotosData);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'error', message: 'Gagal menghubungi server untuk menyimpan pilihan cetak.' }
                }));
            }
        }

        async function saveSpkPhotoConfig() {
            if (!currentOrderId) {
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'error', message: 'ID order tidak ditemukan.' }
                }));
                return;
            }
            
            const saveBtn = document.getElementById('btnSaveSpkConfig');
            const originalText = saveBtn.textContent;
            saveBtn.textContent = 'Menyimpan...';
            saveBtn.disabled = true;

            try {
                const response = await fetch(`/assessment/${currentOrderId}/gallery-spk`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        photos: currentPhotosData.map(p => ({
                            id: p.id,
                            is_printed: p.is_printed ? true : false,
                            print_settings: typeof p.print_settings === 'string' ? JSON.parse(p.print_settings) : p.print_settings
                        }))
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'success', message: result.message || 'Konfigurasi berhasil disimpan!' }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: result.message || 'Terjadi kesalahan sistem.' }
                    }));
                }
            } catch (e) {
                console.error(e);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'error', message: 'Gagal menghubungi server.' }
                }));
            } finally {
                saveBtn.textContent = originalText;
                saveBtn.disabled = false;
            }
        }

        function printSpkFromModal() {
            if (!currentOrderId) {
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'error', message: 'ID order tidak ditemukan.' }
                }));
                return;
            }
            window.open(`/assessment/${currentOrderId}/print-spk`, '_blank');
            window.dispatchEvent(new CustomEvent('show-toast', {
                detail: { type: 'success', message: 'Membuka halaman cetak SPK...' }
            }));
        }

        function openPhotoModal(arg, photosData = null) {
            let spk, photos;
            if (arg instanceof HTMLElement) {
                spk = arg.dataset.spk;
                photos = JSON.parse(arg.dataset.photos);
                currentOrderId = arg.dataset.orderId;
            } else {
                spk = arg;
                photos = photosData;
            }

            currentSpkNumber = spk;
            currentPhotosData = photos;
            
            cancelBulkSelect(); // Reset selection state when opening
            
            document.getElementById('modalSpkNumber').textContent = spk;
            const beforeContainer = document.getElementById('beforePhotosContainer');
            const afterContainer = document.getElementById('afterPhotosContainer');
            const otherContainer = document.getElementById('otherPhotosContainer');
            
            // Calculate Total Size
            let totalBytes = photos.reduce((acc, curr) => acc + (curr.size_bytes || 0), 0);
            let sizeText = '0 KB';
            if (totalBytes > 1048576) {
                sizeText = (totalBytes / 1048576).toFixed(2) + ' MB';
            } else {
                sizeText = (totalBytes / 1024).toFixed(2) + ' KB';
            }
            const modalTotalSize = document.getElementById('modalTotalSize');
            if(modalTotalSize) modalTotalSize.textContent = 'Total: ' + sizeText;

            // Update Selected Count Badge
            const printedCount = photos.filter(p => p.is_printed).length;
            const countBadge = document.getElementById('modalSpkPrintCount');
            if (countBadge) countBadge.textContent = `Terpilih SPK: ${printedCount}/1`;
            
            // Clean
            beforeContainer.innerHTML = '';
            afterContainer.innerHTML = '';
            otherContainer.innerHTML = '';
            
            beforeContainer.className = "grid grid-cols-2 gap-4 auto-rows-max px-2";
            afterContainer.className = "grid grid-cols-2 gap-4 auto-rows-max px-2";

            const beforeSteps = ['RECEPTION', 'WAREHOUSE_BEFORE', 'ASSESSMENT', 'before'];
            const afterSteps = ['QC', 'QC_FINAL', 'FINISH', 'PACKING', 'after'];

            let hasBefore = false;
            let hasAfter = false;

            photos.forEach(photo => {
                const img = document.createElement('img');
                const photoUrl = (photo.photo_url) ? photo.photo_url : (photo.file_path.startsWith('http') ? photo.file_path : `/storage/${photo.file_path}`);
                img.src = photoUrl;
                img.className = 'w-full aspect-square object-cover rounded-xl shadow-sm border border-gray-200 hover:scale-[1.02] transition-transform cursor-pointer ring-1 ring-black/5';
                
                // Clicking image opens the Crop/Zoom Editor modal
                img.onclick = () => openImageZoomEditor(photo, photoUrl);
                
                // Pre-visualize zoom & crop transform on thumbnail
                let settings = {zoom: 1.0, x: 0, y: 0, rotate: 0};
                if (photo.print_settings) {
                    try {
                        settings = typeof photo.print_settings === 'string' ? JSON.parse(photo.print_settings) : photo.print_settings;
                    } catch(e) {}
                }
                
                if (photo.is_printed) {
                    img.style.transform = `scale(${settings.zoom || 1}) translate(${settings.x || 0}%, ${settings.y || 0}%) rotate(${settings.rotate || 0}deg)`;
                    img.style.transformOrigin = 'center';
                    img.style.objectFit = 'cover';
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'relative group photo-item transition-all rounded-xl overflow-hidden bg-black aspect-square';
                wrapper.dataset.photoId = photo.id;
                wrapper.appendChild(img);

                // Checkbox for bulk selection
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'photo-checkbox hidden absolute top-2 left-2 w-5 h-5 rounded border-gray-300 text-[#22B086] focus:ring-[#22B086] z-30 cursor-pointer';
                checkbox.onclick = (e) => { e.stopPropagation(); togglePhotoSelection(photo.id, wrapper, checkbox); };
                wrapper.appendChild(checkbox);
                
                // Caption & Size
                const cap = document.createElement('div');
                cap.className = 'absolute bottom-0 left-0 right-0 bg-white/95 backdrop-blur-sm text-gray-800 p-2 opacity-0 group-hover:opacity-100 transition-opacity rounded-b-xl border-t border-gray-100 z-10';
                
                const sizeBadge = photo.formatted_size ? `<span class="bg-gray-100 text-[9px] px-1 rounded ml-1 text-gray-400 border border-gray-200">${photo.formatted_size}</span>` : '';
                
                cap.innerHTML = `
                    <div class="text-[10px] font-bold line-clamp-1">${photo.caption || ''}</div>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-[9px] text-gray-400 font-medium">${photo.created_at ? new Date(photo.created_at).toLocaleDateString() : ''}</span>
                        ${sizeBadge}
                    </div>
                `;
                wrapper.appendChild(cap);


                // Delete Button
                const delBtn = document.createElement('button');
                delBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-3a1 1 0 00-1 1v3M4 7h16"></path></svg>';
                delBtn.className = 'absolute top-2 right-2 p-1.5 bg-red-600/80 hover:bg-red-700 text-white rounded-lg shadow-lg transition-all transform hover:scale-110 z-20';
                delBtn.title = 'Hapus Foto';
                delBtn.onclick = (e) => {
                    e.stopPropagation(); 
                    if(confirm('Yakin ingin menghapus foto ini?')) {
                        deletePhoto(photo.id, wrapper);
                    }
                };
                wrapper.appendChild(delBtn);

                // Set as Cover Button (Adjusted position to top left)
                const coverBtn = document.createElement('button');
                coverBtn.innerHTML = photo.is_spk_cover 
                    ? '<svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>'
                    : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>';
                
                coverBtn.className = photo.is_spk_cover
                    ? 'absolute top-2 left-2 p-1.5 bg-amber-500 text-white rounded-lg shadow-lg z-20'
                    : 'absolute top-2 left-2 p-1.5 bg-gray-900/60 hover:bg-amber-500 text-white rounded-lg shadow-lg transition-all transform hover:scale-110 z-20';
                
                coverBtn.title = photo.is_spk_cover ? 'SPK Cover Aktif' : 'Atur sebagai Cover SPK';
                coverBtn.onclick = (e) => {
                    e.stopPropagation();
                    setSpkCover(photo.id, spk, photos);
                };
                wrapper.appendChild(coverBtn);

                // Print Toggle Button (Printer Icon)
                const printBtn = document.createElement('button');
                printBtn.innerHTML = photo.is_printed
                    ? '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>'
                    : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>';
                
                printBtn.className = photo.is_printed
                    ? 'absolute bottom-2 left-2 p-1.5 bg-teal-600 text-white rounded-lg shadow-lg z-20 transition-all transform hover:scale-110'
                    : 'absolute bottom-2 left-2 p-1.5 bg-gray-900/60 text-white hover:bg-teal-600 rounded-lg shadow-lg transition-all transform hover:scale-110 z-20';
                
                printBtn.title = photo.is_printed ? 'Batal Cetak SPK' : 'Pilih Cetak SPK';
                printBtn.onclick = (e) => {
                    e.stopPropagation();
                    togglePrintPhoto(photo.id);
                };
                wrapper.appendChild(printBtn);

                // Cover Badge (If active)
                if(photo.is_spk_cover) {
                    const badge = document.createElement('div');
                    badge.className = 'absolute bottom-2 right-2 px-2 py-0.5 bg-amber-500 text-white text-[8px] font-black rounded uppercase tracking-widest shadow-sm z-20';
                    badge.textContent = 'COVER SPK';
                    wrapper.appendChild(badge);
                    wrapper.querySelector('img').classList.add('ring-2', 'ring-amber-500', 'ring-offset-2', 'ring-offset-gray-900');
                }

                // Printed Badge
                if(photo.is_printed) {
                    const printBadge = document.createElement('div');
                    printBadge.className = 'absolute top-2 right-12 px-2 py-0.5 bg-teal-500 text-white text-[8px] font-black rounded uppercase tracking-widest shadow-sm z-20';
                    printBadge.textContent = 'CETAK SPK';
                    wrapper.appendChild(printBadge);
                    wrapper.querySelector('img').classList.add('ring-2', 'ring-teal-500', 'ring-offset-2', 'ring-offset-gray-900');
                }
                
                // Reference Badge (If RECEPTION)
                if(photo.step === 'RECEPTION') {
                    const refBadge = document.createElement('div');
                    refBadge.className = 'absolute top-10 left-2 px-2 py-0.5 bg-purple-600 text-white text-[8px] font-black rounded-lg uppercase tracking-wider shadow-lg border border-purple-500/50 z-20 flex items-center gap-1';
                    refBadge.innerHTML = '<span>📦</span> <span>REFERENSI</span>';
                    wrapper.appendChild(refBadge);
                }

                if (beforeSteps.includes(photo.step) || (photo.step && photo.step.includes('BEFORE'))) {
                    beforeContainer.appendChild(wrapper);
                    hasBefore = true;
                } else if (afterSteps.includes(photo.step) || (photo.step && photo.step.includes('AFTER'))) {
                    afterContainer.appendChild(wrapper);
                    hasAfter = true;
                } else {
                    otherContainer.appendChild(wrapper);
                }
            });

            // Empty States with Premium Icons
            const emptyState = (text) => `
                <div class="col-span-2 flex flex-col items-center justify-center p-8 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-gray-400 font-bold text-xs italic">${text}</p>
                </div>
            `;

            if (!hasBefore) beforeContainer.innerHTML = emptyState('Belum ada foto before');
            if (!hasAfter) afterContainer.innerHTML = emptyState('Belum ada foto after');

            document.getElementById('orderPhotoModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        async function deletePhoto(photoId, element) {
            try {
                const response = await fetch(`/photos/${photoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    element.style.transition = 'all 0.3s ease';
                    element.style.opacity = '0';
                    element.style.transform = 'scale(0.9)';
                    setTimeout(() => element.remove(), 300);
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'success', message: 'Foto berhasil dihapus.' }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: 'Gagal menghapus foto: ' + (result.message || 'Error unknown') }
                    }));
                }
            } catch (error) {
                console.error(error);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'error', message: 'Terjadi kesalahan saat menghapus foto.' }
                }));
            }
        }

        async function setSpkCover(photoId, spk, allPhotos) {
            try {
                const response = await fetch(`/photos/${photoId}/set-cover`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    // Update the local data and re-render
                    allPhotos.forEach(p => {
                        p.is_spk_cover = (p.id == photoId);
                    });
                    openPhotoModal(spk, allPhotos); // Refresh modal content
                    
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'success', message: result.message || 'Cover SPK berhasil diatur!' }
                    }));
                }
            } catch (error) {
                console.error(error);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'error', message: 'Terjadi kesalahan saat mengatur cover SPK.' }
                }));
            }
        }
    </script>

    {{-- Resumable.js for Chunk Upload --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/resumable.js/1.1.0/resumable.min.js"></script>
    <script>
        // Store current customer ID
        const customerId = {{ $customer->id }};
        
        // --- Customer Profile Photo Chunk Upload ---
        let custResumable = null;

        function initCustResumable() {
            if (custResumable) return true;

            const input = document.getElementById('custChunkFileInput');
            if (!input) return false;

            custResumable = new Resumable({
                target: `{{ route('admin.customers.photos.chunk', $customer->id) }}`,
                query: () => ({
                    _token: '{{ csrf_token() }}',
                    caption: document.getElementById('custDocCaption').value,
                    type: document.getElementById('custDocType').value
                }),
                fileType: ['jpg', 'jpeg', 'png'],
                chunkSize: 1 * 1024 * 1024, // 1MB chunks
                headers: {
                    'Accept': 'application/json'
                },
                testChunks: false,
                throttleProgressCallbacks: 1
            });

            custResumable.assignBrowse(input);

            custResumable.on('fileAdded', function(file) {
                document.getElementById('custChunkFileLabelText').textContent = file.fileName + ' (' + formatSize(file.size) + ')';
                document.getElementById('custUploadBtn').disabled = false;
                document.getElementById('custUploadProgress').classList.add('hidden');
            });

            custResumable.on('fileProgress', function(file) {
                const progress = Math.floor(file.progress() * 100);
                document.getElementById('custUploadProgressBar').style.width = `${progress}%`;
                document.getElementById('custUploadProgressText').textContent = `${progress}%`;
                document.getElementById('custUploadStatusText').textContent = 'Mengupload: ' + progress + '%';
            });

            custResumable.on('fileSuccess', function(file, response) {
                const data = JSON.parse(response);
                if (data.success) {
                    document.getElementById('custUploadStatusText').textContent = 'Upload Selesai! Mengompres...';
                    document.getElementById('custUploadProgressBar').classList.add('bg-green-500');
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'success', message: 'Dokumen berhasil diupload!' }
                    }));
                    setTimeout(() => {
                        location.reload(); 
                    }, 1000);
                } else {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: 'Upload gagal: ' + data.message }
                    }));
                    resetCustUpload();
                }
            });

            custResumable.on('fileError', function(file, message) {
                let errorMsg = 'Terjadi kesalahan saat upload.';
                try {
                    const data = JSON.parse(message);
                    if(data.message) {
                        errorMsg = 'Upload gagal: ' + data.message;
                    }
                } catch(e) {
                    errorMsg = 'Upload gagal: ' + message;
                }
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'error', message: errorMsg }
                }));
                resetCustUpload();
            });

            return true;
        }

        function startCustChunkUpload() {
            if (!custResumable || custResumable.files.length === 0) return;
            document.getElementById('custUploadBtn').disabled = true;
            document.getElementById('custUploadProgress').classList.remove('hidden');
            custResumable.upload();
        }

        function openCustUploadModal() {
            document.getElementById('uploadModal').classList.remove('hidden');
            initCustResumable();
        }

        function closeCustUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
            if(custResumable) custResumable.cancel();
            resetCustUpload();
        }

        function resetCustUpload() {
            const label = document.getElementById('custChunkFileLabelText');
            const btn = document.getElementById('custUploadBtn');
            const progress = document.getElementById('custUploadProgress');
            const bar = document.getElementById('custUploadProgressBar');
            const caption = document.getElementById('custDocCaption');

            if(label) label.textContent = 'Klik untuk pilih dokumen';
            if(btn) btn.disabled = true;
            if(progress) progress.classList.add('hidden');
            if(bar) bar.style.width = '0%';
            if(caption) caption.value = '';
        }

        // No immediate init per load to avoid timing issues with teleported elements. 
        // Initialized JIT in open modals.


        // --- Order Photo Chunk Upload ---
        let orderResumable = null;
        let currentOrderSpk = null;
        let uploadOrderId = null;
        let uploadedPhotoIds = [];

        function initOrderResumable() {
            if (orderResumable) return true;

            const input = document.getElementById('orderChunkFileInput');
            if (!input) return false;

            orderResumable = new Resumable({
                target: () => window.location.origin + `/orders/${uploadOrderId}/photos/chunk`,
                query: () => ({
                    _token: '{{ csrf_token() }}',
                    caption: document.getElementById('orderCaption').value,
                    step: document.getElementById('orderStep').value
                }),
                fileType: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG', 'webp', 'WEBP'],
                chunkSize: 1 * 1024 * 1024,
                headers: { 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                testChunks: false,
                throttleProgressCallbacks: 1,
                maxFiles: 10,
                fileTypeErrorCallback: function(file, errorCount) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tipe File Tidak Didukung',
                        text: 'Silakan pilih file gambar (JPG, PNG, atau WEBP).'
                    });
                }
            });

            orderResumable.assignBrowse(input);

            orderResumable.on('fileAdded', function(file) {
                 console.log('File added:', file.fileName);
                 updateOrderFileLabel();
                 document.getElementById('orderUploadBtn').disabled = false;
            });

            orderResumable.on('filesAdded', function(files) {
                 console.log('Multiple files added:', files.length);
                 updateOrderFileLabel();
                 document.getElementById('orderUploadBtn').disabled = false;
                 document.getElementById('orderUploadProgress').classList.add('hidden');
            });

            function updateOrderFileLabel() {
                 const count = orderResumable.files.length;
                 const label = document.getElementById('orderChunkFileLabelText');
                 if (count === 0) {
                     label.textContent = 'Klik untuk pilih foto';
                 } else if (count === 1) {
                     label.textContent = orderResumable.files[0].fileName;
                 } else {
                     label.textContent = `${count} File Terpilih`;
                 }
            }

            orderResumable.on('fileProgress', function(file) {
                const progress = Math.floor(orderResumable.progress() * 100);
                document.getElementById('orderUploadProgressBar').style.width = `${progress}%`;
                document.getElementById('orderUploadProgressText').textContent = `${progress}%`;
                document.getElementById('orderUploadStatusText').textContent = 'Mengupload...';
            });

            orderResumable.on('fileSuccess', function(file, response) {
                try {
                    const res = JSON.parse(response);
                    if (res.success && res.photo_id) {
                        uploadedPhotoIds.push(res.photo_id);
                        console.log(`Uploaded & Collected ID: ${res.photo_id} for file: ${file.fileName}`);
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
            });

            orderResumable.on('complete', function() {
                document.getElementById('orderUploadStatusText').textContent = 'Upload selesai! Menyiapkan antrian...';
                
                // Wait 2 seconds to ensure all fileSuccess events have pushed IDs to the array
                setTimeout(() => {
                    const expectedCount = orderResumable.files.length;
                    const actualCount = uploadedPhotoIds.length;
                    
                    console.log(`Consistency check: Expected ${expectedCount}, Collected ${actualCount}`);
                    
                    if (actualCount < expectedCount) {
                        console.warn('IDs not fully collected yet, waiting an extra second...');
                        setTimeout(() => processSequential(uploadedPhotoIds), 1000);
                    } else {
                        processSequential(uploadedPhotoIds);
                    }
                }, 2000);
            });
            
            orderResumable.on('fileError', function(file, message) {
                 console.error('Upload Error:', message);
                 // message often contains a JSON string if it's a Laravel error
                 let errorMsg = message;
                 try {
                     const errData = JSON.parse(message);
                     errorMsg = errData.message || message;
                 } catch(e) {}
                 
                 window.dispatchEvent(new CustomEvent('show-toast', {
                     detail: { type: 'error', message: 'Gagal mengupload file ' + file.fileName + ': ' + errorMsg }
                 }));
             });

            return true;
        }
        
        function openOrderUploadModal(orderId, spkNumber) {
            uploadOrderId = orderId;
            currentOrderSpk = spkNumber;
            const spkEl = document.getElementById('uploadSpkNumber');
            if(spkEl) spkEl.textContent = spkNumber;
            
            // Re-init just in case teleporting was late
            initOrderResumable();
            
            // Reset state
            uploadedPhotoIds = [];
            if(orderResumable) {
                orderResumable.cancel(); // Clear any existing files in queue
            }
            const label = document.getElementById('orderChunkFileLabelText');
            const btn = document.getElementById('orderUploadBtn');
            const progress = document.getElementById('orderUploadProgress');
            const bar = document.getElementById('orderUploadProgressBar');
            const caption = document.getElementById('orderCaption');

            if(label) label.textContent = 'Klik untuk pilih foto';
            if(btn) btn.disabled = true;
            if(progress) progress.classList.add('hidden');
            if(bar) bar.style.width = '0%';
            if(caption) caption.value = '';

            document.getElementById('orderUploadModal').classList.remove('hidden');
        }

        function startOrderChunkUpload() {
            if (!orderResumable || orderResumable.files.length === 0) return;
            
            // Clear previous collected IDs for a fresh batch
            uploadedPhotoIds = [];
            
            document.getElementById('orderUploadBtn').disabled = true;
            document.getElementById('orderUploadProgress').classList.remove('hidden');
            orderResumable.upload();
        }
        
        function closeOrderUploadModal() {
            document.getElementById('orderUploadModal').classList.add('hidden');
            if(orderResumable) orderResumable.cancel();
            const label = document.getElementById('orderChunkFileLabelText');
            const btn = document.getElementById('orderUploadBtn');
            const progress = document.getElementById('orderUploadProgress');

            if(label) label.textContent = 'Klik untuk pilih foto';
            if(btn) btn.disabled = true;
            if(progress) progress.classList.add('hidden');
        }

        // --- Sequential Processing Logic (True per-photo processing) ---
        async function processSequential(ids) {
            console.log('Starting sequential processing for IDs:', ids);
            
            if (!ids || ids.length === 0) {
                console.log('No IDs to process, reloading...');
                location.reload();
                return;
            }

            const total = ids.length;
            const statusText = document.getElementById('orderUploadStatusText');
            const progressBar = document.getElementById('orderUploadProgressBar');
            let failureCount = 0;
            let lastErrorMessage = '';
            
            for (let i = 0; i < ids.length; i++) {
                const photoId = ids[i];
                const currentNum = i + 1;
                console.log(`Processing photo ${currentNum}/${total} (ID: ${photoId})`);
                
                statusText.textContent = `Mengompres foto (${currentNum}/${total})...`;
                
                // Update progress bar to reflect processing progress
                const procProgress = (currentNum / total) * 100;
                progressBar.style.width = `${procProgress}%`;

                try {
                    // Mandatory delay before EVERY request to ensure server settling
                    await new Promise(resolve => setTimeout(resolve, 500));

                    const response = await fetch(window.location.origin + `/photos/${photoId}/process`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    
                    const responseText = await response.text();
                    let result;
                    try {
                        result = JSON.parse(responseText);
                    } catch (pE) {
                        console.error(`Raw response for ID ${photoId} was not JSON:`, responseText);
                        failureCount++;
                        lastErrorMessage = 'Invalid server response. Check console.';
                        continue;
                    }

                    if(!result.success) {
                        failureCount++;
                        lastErrorMessage = result.message || 'Unknown error';
                        console.error(`Failed to process photo ${photoId}:`, lastErrorMessage);
                    } else {
                        console.log(`Successfully processed photo ID: ${photoId}`);
                    }
                } catch(e) {
                    failureCount++;
                    lastErrorMessage = e.message;
                    console.error(`Network error processing photo ${photoId}:`, e);
                }
            }

            console.log(`Processing complete. Failures: ${failureCount}`);

            if (failureCount > 0) {
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'error', message: `${failureCount} dari ${total} foto gagal dikompres. Silakan cek koneksi/log.` }
                }));
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'success', message: 'Semua foto berhasil diupload dan dikompres!' }
                }));
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        }

        function formatSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }


        function updateFileLabel(input) {
            const label = document.getElementById('fileLabelText');
            const count = document.getElementById('fileCountText');
            
            if (input.files && input.files.length > 0) {
                if (input.files.length === 1) {
                    label.textContent = input.files[0].name;
                } else {
                    label.textContent = input.files.length + ' file terpilih';
                }
                label.classList.add('text-purple-600');
                
                // Show filenames (up to 3)
                let names = Array.from(input.files).map(f => f.name).slice(0, 3).join(', ');
                if(input.files.length > 3) names += ', ...';
                
                count.textContent = names;
                count.classList.remove('hidden');
            } else {
                label.textContent = 'Klik untuk pilih foto';
                label.classList.remove('text-purple-600');
                count.classList.add('hidden');
            }
        }

        function updateCustFileLabel(input) {
            const label = document.getElementById('custFileLabelText');
            if (input.files && input.files.length > 0) {
                if (input.files.length === 1) {
                    label.textContent = input.files[0].name;
                } else {
                    label.textContent = input.files.length + ' file terpilih';
                }
                label.classList.add('text-[#22B086]');
            } else {
                label.textContent = 'Klik untuk pilih dokumen';
                label.classList.remove('text-[#22B086]');
            }
        }

        // Camera Modal Trigger Functions
        function openOrderCameraModal(orderId, spkNumber) {
            window.dispatchEvent(new CustomEvent('open-order-camera', { detail: { orderId, spkNumber } }));
        }

        function closeOrderCameraModal() {
            window.dispatchEvent(new CustomEvent('close-order-camera'));
        }

        // Alpine Camera Controller Definition
        function orderCameraCapture() {
            return {
                orderId: null,
                spkNumber: '',
                step: 'RECEPTION',
                caption: '',
                isCameraOpen: false,
                stream: null,
                streamActive: false,
                isCaptured: false,
                capturedPhotoUrl: '',
                ctx: null,
                isLoading: false,
                facingMode: 'environment', // Default to rear camera
                devices: [],
                selectedDeviceId: '',
                sessionPhotos: [],
                shouldReloadOnClose: false,
                focusRing: {
                    show: false,
                    x: 0,
                    y: 0
                },
                supportManualFocus: false,
                focusMin: 0,
                focusMax: 10,
                focusStep: 0.1,
                focusDistance: 5,
                focusMode: 'continuous',
                supportedFocusModes: [],
                supportZoom: false,
                zoomMin: 1,
                zoomMax: 5,
                zoomStep: 0.1,
                zoomValue: 1,

                async openModal(detail) {
                    this.orderId = detail.orderId;
                    this.spkNumber = detail.spkNumber;
                    this.step = 'RECEPTION';
                    this.caption = '';
                    this.devices = [];
                    this.selectedDeviceId = '';
                    this.sessionPhotos = [];
                    this.shouldReloadOnClose = false;
                    this.isCaptured = false;
                    this.capturedPhotoUrl = '';
                    
                    // Reset focus and zoom states
                    this.focusMode = 'continuous';
                    this.supportedFocusModes = [];
                    this.supportManualFocus = false;
                    this.supportZoom = false;
                    this.zoomValue = 1;
                    
                    // Show modal element
                    const modal = document.getElementById('orderCameraModal');
                    if (modal) modal.classList.remove('hidden');
                    
                    // Open camera stream
                    this.isCameraOpen = true;
                    await this.$nextTick(); 
                    this.ctx = this.$refs.canvasElement.getContext('2d');
                    await this.startCamera();
                },

                closeModal() {
                    // Stop stream
                    this.isCameraOpen = false;
                    this.isCaptured = false;
                    this.capturedPhotoUrl = '';
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                    }
                    this.stream = null;
                    this.streamActive = false;
                    
                    // Hide modal element
                    const modal = document.getElementById('orderCameraModal');
                    if (modal) modal.classList.add('hidden');

                    // Reload page if anything was uploaded in this session
                    if (this.shouldReloadOnClose) {
                        this.isLoading = true;
                        location.reload();
                    }
                },

                async startCamera() {
                    try {
                        if (this.stream) {
                            this.stream.getTracks().forEach(track => track.stop());
                        }
                        this.streamActive = false;
                        this.supportManualFocus = false;
                        this.supportZoom = false;
                        
                        const constraints = {
                            video: {
                                deviceId: this.selectedDeviceId ? { exact: this.selectedDeviceId } : undefined,
                                facingMode: this.selectedDeviceId ? undefined : this.facingMode,
                                // Portrait vertical ideal constraints
                                width: { ideal: 960 },
                                height: { ideal: 1280 }
                            }
                        };

                        this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                        this.$refs.videoElement.srcObject = this.stream;
                        this.streamActive = true;

                        // Check track capabilities for pro controls (focusMode, focusDistance, zoom)
                        const track = this.stream.getVideoTracks()[0];
                        if (track && typeof track.getCapabilities === 'function') {
                            const capabilities = track.getCapabilities();
                            
                            // 1. Detect focus modes
                            if (capabilities.focusMode) {
                                this.supportedFocusModes = capabilities.focusMode;
                                if (capabilities.focusMode.includes('continuous')) {
                                    this.focusMode = 'continuous';
                                } else {
                                    this.focusMode = capabilities.focusMode[0];
                                }
                            }
                            
                            // 2. Detect manual focus support
                            if (capabilities.focusMode && capabilities.focusMode.includes('manual') && capabilities.focusDistance) {
                                this.supportManualFocus = true;
                                this.focusMin = capabilities.focusDistance.min || 0;
                                this.focusMax = capabilities.focusDistance.max || 10;
                                this.focusStep = capabilities.focusDistance.step || 0.1;
                                const settings = typeof track.getSettings === 'function' ? track.getSettings() : {};
                                this.focusDistance = settings.focusDistance || (this.focusMin + this.focusMax) / 2;
                            }

                            // 3. Detect zoom support
                            if (capabilities.zoom) {
                                this.supportZoom = true;
                                this.zoomMin = capabilities.zoom.min || 1;
                                this.zoomMax = capabilities.zoom.max || 5;
                                this.zoomStep = capabilities.zoom.step || 0.1;
                                const settings = typeof track.getSettings === 'function' ? track.getSettings() : {};
                                this.zoomValue = settings.zoom || 1;
                            }
                        }

                        // Load and populate camera devices
                        await this.loadDevices();
                    } catch (err) {
                        console.error("Error accessing camera: ", err);
                        // Fallback: try basic video constraints
                        try {
                            this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: this.facingMode } });
                            this.$refs.videoElement.srcObject = this.stream;
                            this.streamActive = true;

                            // Check capabilities in fallback too
                            const track = this.stream.getVideoTracks()[0];
                            if (track && typeof track.getCapabilities === 'function') {
                                const capabilities = track.getCapabilities();
                                
                                if (capabilities.focusMode) {
                                    this.supportedFocusModes = capabilities.focusMode;
                                    if (capabilities.focusMode.includes('continuous')) {
                                        this.focusMode = 'continuous';
                                    } else {
                                        this.focusMode = capabilities.focusMode[0];
                                    }
                                }

                                if (capabilities.focusMode && capabilities.focusMode.includes('manual') && capabilities.focusDistance) {
                                    this.supportManualFocus = true;
                                    this.focusMin = capabilities.focusDistance.min || 0;
                                    this.focusMax = capabilities.focusDistance.max || 10;
                                    this.focusStep = capabilities.focusDistance.step || 0.1;
                                    const settings = typeof track.getSettings === 'function' ? track.getSettings() : {};
                                    this.focusDistance = settings.focusDistance || (this.focusMin + this.focusMax) / 2;
                                }

                                if (capabilities.zoom) {
                                    this.supportZoom = true;
                                    this.zoomMin = capabilities.zoom.min || 1;
                                    this.zoomMax = capabilities.zoom.max || 5;
                                    this.zoomStep = capabilities.zoom.step || 0.1;
                                    const settings = typeof track.getSettings === 'function' ? track.getSettings() : {};
                                    this.zoomValue = settings.zoom || 1;
                                }
                            }

                            await this.loadDevices();
                        } catch (fallbackErr) {
                            console.error("Fallback camera access failed: ", fallbackErr);
                            window.dispatchEvent(new CustomEvent('show-toast', {
                                detail: { type: 'error', message: 'Kamera Gagal Opened. Pastikan Anda memberikan izin akses kamera.' }
                            }));
                            this.closeModal();
                        }
                    }
                },

                async loadDevices() {
                    try {
                        const devices = await navigator.mediaDevices.enumerateDevices();
                        const videoDevices = devices.filter(device => device.kind === 'videoinput');
                        this.devices = videoDevices.map(device => ({
                            id: device.deviceId,
                            label: device.label || `Kamera ${this.devices.length + 1}`
                        }));
                        // Auto-select current stream's device ID if we don't have one selected yet
                        if (!this.selectedDeviceId && this.devices.length > 0) {
                            if (this.stream) {
                                const activeTrack = this.stream.getVideoTracks()[0];
                                const settings = activeTrack ? activeTrack.getSettings() : null;
                                if (settings && settings.deviceId) {
                                    this.selectedDeviceId = settings.deviceId;
                                    return;
                                }
                            }
                            this.selectedDeviceId = this.devices[0].id;
                        }
                    } catch (e) {
                        console.error("Error enumerating devices: ", e);
                    }
                },

                async changeDevice() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                    }
                    this.stream = null;
                    this.streamActive = false;
                    this.isCaptured = false;
                    this.capturedPhotoUrl = '';
                    await this.startCamera();
                },

                async switchCamera() {
                    this.facingMode = this.facingMode === 'environment' ? 'user' : 'environment';
                    this.selectedDeviceId = ''; // reset so startCamera uses facingMode constraints instead
                    await this.startCamera();
                },

                captureImage() {
                    if (!this.streamActive) return;
                    
                    const video = this.$refs.videoElement;
                    const canvas = this.$refs.canvasElement;
                    
                    // Match camera orientation
                    let width = video.videoWidth || 960;
                    let height = video.videoHeight || 1280;
                    
                    // Max width for client-side processing
                    const maxWidth = 1200;
                    if (width > maxWidth) {
                        const ratio = maxWidth / width;
                        width = maxWidth;
                        height = Math.round(height * ratio);
                    }

                    canvas.width = width;
                    canvas.height = height;

                    // Draw video frame to canvas
                    this.ctx.drawImage(video, 0, 0, width, height);
                    
                    // Render image capture preview URL
                    this.capturedPhotoUrl = canvas.toDataURL('image/jpeg', 0.85);
                    this.isCaptured = true;
                    
                    // Temporarily pause camera to save resource utilization
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.enabled = false);
                    }
                },

                retakePhoto() {
                    this.isCaptured = false;
                    this.capturedPhotoUrl = '';
                    // Resume camera
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.enabled = true);
                    }
                },

                async saveAndSubmitPhoto() {
                    if (!this.isCaptured) {
                        // If not captured yet, capture first
                        this.captureImage();
                    }
                    
                    this.isLoading = true;
                    
                    try {
                        // Compress to JPG 0.6 (60% quality)
                        const dataUrl = this.$refs.canvasElement.toDataURL('image/jpeg', 0.6);
                        
                        // Convert DataURL to Blob
                        const resBlob = await fetch(dataUrl);
                        const blob = await resBlob.blob();
                        
                        // Prepare form data
                        const formData = new FormData();
                        formData.append('photo', blob, `spk_${this.spkNumber}_cam_${Date.now()}.jpg`);
                        formData.append('step', this.step);
                        if (this.caption) {
                            formData.append('caption', this.caption);
                        }

                        // POST to storeCamera endpoint
                        const response = await fetch(`/orders/${this.orderId}/photos/camera`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const data = await response.json();
                        
                        if (data.success) {
                            window.dispatchEvent(new CustomEvent('show-toast', {
                                detail: { type: 'success', message: 'Foto berhasil disimpan.' }
                            }));
                            
                            // Add photo to session photos list to display in the modal's session shelf/gallery
                            this.sessionPhotos.push({
                                id: data.photo_id || Date.now(),
                                url: data.photo_url || data.path || dataUrl,
                                caption: this.caption || 'Foto Kamera',
                                step: this.step
                            });
                            
                            // Mark that we need to reload the parent page when the modal closes
                            this.shouldReloadOnClose = true;
                            
                            // Clear caption for next photo
                            this.caption = '';
                            
                            // Clear canvas and resume camera stream for next capture
                            this.isCaptured = false;
                            this.capturedPhotoUrl = '';
                            if (this.stream) {
                                this.stream.getTracks().forEach(track => track.enabled = true);
                            } else {
                                await this.startCamera();
                            }
                        } else {
                            window.dispatchEvent(new CustomEvent('show-toast', {
                                detail: { type: 'error', message: data.message || 'Terjadi kesalahan saat menyimpan foto.' }
                            }));
                        }
                    } catch (e) {
                        console.error("AJAX camera upload error:", e);
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: { type: 'error', message: 'Gagal menghubungi server. Silakan coba lagi.' }
                        }));
                    } finally {
                        this.isLoading = false;
                    }
                },

                triggerFocus(e) {
                    if (!this.streamActive || this.isCaptured || this.isLoading) return;
                    
                    const rect = e.currentTarget.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    this.focusRing.show = false;
                    this.$nextTick(() => {
                        this.focusRing.x = x;
                        this.focusRing.y = y;
                        this.focusRing.show = true;
                        
                        // Hide focus ring after animation
                        setTimeout(() => {
                            this.focusRing.show = false;
                        }, 1200);
                    });

                    if (this.stream) {
                        const track = this.stream.getVideoTracks()[0];
                        if (track) {
                            try {
                                const capabilities = typeof track.getCapabilities === 'function' ? track.getCapabilities() : {};
                                if (capabilities.focusMode) {
                                    if (capabilities.focusMode.includes('single-shot')) {
                                        this.focusMode = 'single-shot';
                                        track.applyConstraints({
                                            advanced: [{ focusMode: 'single-shot' }]
                                        }).catch(err => {
                                            console.warn("Autofocus single-shot apply failed:", err);
                                        });
                                    } else if (capabilities.focusMode.includes('continuous')) {
                                        this.focusMode = 'continuous';
                                        track.applyConstraints({
                                            advanced: [{ focusMode: 'continuous' }]
                                        }).catch(err => {
                                            console.warn("Autofocus continuous apply failed:", err);
                                        });
                                    }
                                }
                            } catch (err) {
                                console.warn("Autofocus trigger error:", err);
                            }
                        }
                    }
                },

                async adjustFocus() {
                    if (!this.stream) return;
                    const track = this.stream.getVideoTracks()[0];
                    if (track) {
                        try {
                            this.focusMode = 'manual';
                            await track.applyConstraints({
                                advanced: [{ 
                                    focusMode: 'manual', 
                                    focusDistance: parseFloat(this.focusDistance) 
                                }]
                            });
                        } catch (err) {
                            console.warn("Manual focus apply failed:", err);
                        }
                    }
                },

                async setFocusMode(mode) {
                    if (!this.stream) return;
                    const track = this.stream.getVideoTracks()[0];
                    if (!track) return;

                    this.focusMode = mode;
                    
                    try {
                        const capabilities = typeof track.getCapabilities === 'function' ? track.getCapabilities() : {};
                        if (capabilities.focusMode && capabilities.focusMode.includes(mode)) {
                            const constraints = {
                                advanced: [{ focusMode: mode }]
                            };
                            
                            if (mode === 'manual' && this.supportManualFocus) {
                                constraints.advanced[0].focusDistance = parseFloat(this.focusDistance);
                            }
                            
                            await track.applyConstraints(constraints);
                        } else {
                            console.warn(`Focus mode ${mode} not supported by hardware/track capabilities.`);
                        }
                    } catch (err) {
                        console.error("Error setting focus mode:", err);
                    }
                },

                async adjustZoom() {
                    if (!this.stream) return;
                    const track = this.stream.getVideoTracks()[0];
                    if (track && this.supportZoom) {
                        try {
                            await track.applyConstraints({
                                advanced: [{ 
                                    zoom: parseFloat(this.zoomValue) 
                                }]
                            });
                        } catch (err) {
                            console.warn("Zoom apply failed:", err);
                        }
                    }
                }
            };
        }

        // Modal close on backdrop click for all modals
        window.onclick = function(event) {
            const orderModal = document.getElementById('orderUploadModal');
            const custModal = document.getElementById('uploadModal');
            const cameraModal = document.getElementById('orderCameraModal');
            if (event.target === orderModal) orderModal.classList.add('hidden');
            if (event.target === custModal) custModal.classList.add('hidden');
            if (event.target === cameraModal) {
                window.dispatchEvent(new CustomEvent('close-order-camera'));
            }
        }
    </script>

    <style>
        @keyframes cameraFocusPulse {
            0% { transform: translate(-50%, -50%) scale(1.6); opacity: 0.3; }
            20% { transform: translate(-50%, -50%) scale(1.0); opacity: 1; }
            80% { transform: translate(-50%, -50%) scale(1.0); opacity: 1; }
            100% { transform: translate(-50%, -50%) scale(0.9); opacity: 0; }
        }
        .animate-camera-focus {
            animation: cameraFocusPulse 1.2s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }
        @keyframes modalEnter {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-modal-enter {
            animation: modalEnter 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        #orderUploadModal .bg-white, #uploadModal .bg-white {
            animation: modalEnter 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F3F4F6;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E5E7EB;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #D1D5DB;
        }
    </style>
    </div> {{-- Close Alpine Component Wrapper --}}
</x-app-layout>

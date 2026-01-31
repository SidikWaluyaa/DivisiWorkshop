<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-indigo-500/20 rounded-lg backdrop-blur-sm shadow-sm border border-indigo-500/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48V11.5" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-xl leading-tight tracking-wide">{{ __('Pusat Kesehatan Data') }}</h2>
                <p class="text-xs font-medium opacity-90">Pemantauan & Pembersihan Data Seluruh Sistem</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Alerts --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Berhasil!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Error!</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- Overview Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Trash Card --}}
                <div class="bg-white p-6 rounded-3xl shadow-xl border border-gray-100 group hover:border-red-200 transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-red-50 text-red-600 rounded-2xl group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </div>
                        <a href="{{ route('admin.data-integrity.trash') }}" class="text-[10px] font-black text-red-600 uppercase tracking-widest hover:underline">Kelola Sampah →</a>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900">{{ array_sum($stats['trash']) }}</h3>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-tight">Total Data Terhapus</p>
                    <div class="mt-4 pt-4 border-t border-gray-50 grid grid-cols-2 gap-2">
                        <div class="text-[10px] font-bold"><span class="text-gray-400">Workshop:</span> <span class="text-gray-900">{{ $stats['trash']['workshop'] }}</span></div>
                        <div class="text-[10px] font-bold"><span class="text-gray-400">CS:</span> <span class="text-gray-900">{{ $stats['trash']['cs'] }}</span></div>
                        <div class="text-[10px] font-bold"><span class="text-gray-400">Gudang:</span> <span class="text-gray-900">{{ $stats['trash']['warehouse'] }}</span></div>
                        <div class="text-[10px] font-bold"><span class="text-gray-400">Lainnya:</span> <span class="text-gray-900">{{ $stats['trash']['cx'] + $stats['trash']['master'] }}</span></div>
                    </div>
                </div>

                {{-- Limbo Card --}}
                <div class="bg-white p-6 rounded-3xl shadow-xl border border-gray-100 group hover:border-yellow-200 transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-yellow-50 text-yellow-600 rounded-2xl group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <a href="{{ route('admin.data-integrity.limbo') }}" class="text-[10px] font-black text-yellow-600 uppercase tracking-widest hover:underline">Lihat Detail →</a>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900">{{ array_sum($stats['limbo']) }}</h3>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-tight">Data Dalam Antrean "Tersembunyi"</p>
                    <div class="mt-4 pt-4 border-t border-gray-50 flex gap-4">
                        <div class="text-[10px] font-bold"><span class="text-gray-400">Batal:</span> <span class="text-gray-900">{{ $stats['limbo']['batal'] }}</span></div>
                        <div class="text-[10px] font-bold"><span class="text-gray-400">Donasi:</span> <span class="text-gray-900">{{ $stats['limbo']['donasi'] }}</span></div>
                        <div class="text-[10px] font-bold"><span class="text-gray-400">Verifikasi(F):</span> <span class="text-gray-900">{{ $stats['limbo']['wait_verification'] }}</span></div>
                    </div>
                </div>

                {{-- Issues Card --}}
                <div class="bg-white p-6 rounded-3xl shadow-xl border border-gray-100 group hover:border-orange-200 transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-orange-50 text-orange-600 rounded-2xl group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <span class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Peringatan Sistem</span>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900">{{ array_sum($stats['issues']) }}</h3>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-tight">Potensi Masalah Integritas</p>
                    <div class="mt-4 pt-4 border-t border-gray-50 flex gap-4">
                        <div class="text-[10px] font-bold"><span class="text-gray-400">Stale:</span> <span class="text-gray-900">{{ $stats['issues']['stale_reception'] + $stats['issues']['stale_assessment'] }}</span></div>
                        <div class="text-[10px] font-bold"><span class="text-gray-400">Orphaned:</span> <span class="text-gray-900">{{ $stats['issues']['orphaned_storage'] }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Recommendations / Quick Actions --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- CS & CX Health --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                        <div class="p-6 bg-indigo-50 border-b border-indigo-100 flex items-center justify-between">
                            <h4 class="font-black text-indigo-800 uppercase tracking-widest text-xs">CS & Customer Experience</h4>
                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-600 rounded-full text-[10px] font-bold">Sales & Feedback</span>
                        </div>
                        <div class="divide-y divide-gray-50">
                            {{-- Stale Leads --}}
                            @if($stats['issues']['stale_leads'] > 0)
                                <div class="p-6 flex items-start gap-4 hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center flex-shrink-0 font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-black text-gray-900">Lead Macet (Stale Leads)</p>
                                        <p class="text-xs text-gray-500 mt-1">Ada <b>{{ $stats['issues']['stale_leads'] }}</b> lead yang sudah > 7 hari tidak ada aktivitas.</p>
                                        <a href="{{ route('cs.dashboard') }}" class="inline-block mt-3 text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline">Follow Up di Pipeline →</a>
                                    </div>
                                </div>
                            @endif

                            {{-- Expired Quotations --}}
                            @if($stats['issues']['expired_quotations'] > 0)
                                <div class="p-6 flex items-start gap-4 hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 font-bold">
                                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-black text-gray-900">Penawaran Kadaluarsa</p>
                                        <p class="text-xs text-gray-500 mt-1">Ditemukan <b>{{ $stats['issues']['expired_quotations'] }}</b> penawaran (quotation) yang melewati batas waktu.</p>
                                        <a href="{{ route('cs.dashboard') }}" class="inline-block mt-3 text-[10px] font-black text-red-600 uppercase tracking-widest hover:underline">Update Penawaran →</a>
                                    </div>
                                </div>
                            @endif

                            {{-- Pending Complaints --}}
                            @if($stats['issues']['pending_complaints'] > 0)
                                <div class="p-6 flex items-start gap-4 hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center flex-shrink-0 font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-black text-gray-900">Keluhan Belum Direspon</p>
                                        <p class="text-xs text-gray-500 mt-1">Ada <b>{{ $stats['issues']['pending_complaints'] }}</b> komplain nasabah yang belum dibalas admin.</p>
                                        <a href="{{ route('admin.complaints.index') }}" class="inline-block mt-3 text-[10px] font-black text-orange-600 uppercase tracking-widest hover:underline">Balas Keluhan →</a>
                                    </div>
                                </div>
                            @endif

                            @if($stats['issues']['stale_leads'] == 0 && $stats['issues']['expired_quotations'] == 0 && $stats['issues']['pending_complaints'] == 0)
                                <div class="p-12 text-center">
                                    <p class="text-sm font-bold text-gray-400 italic">Tidak ada masalah di modul CS & CX.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Warehouse & Workshop Health --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                        <div class="p-6 bg-teal-50 border-b border-teal-100 flex items-center justify-between">
                            <h4 class="font-black text-teal-800 uppercase tracking-widest text-xs">Warehouse & Production</h4>
                            <span class="px-2 py-0.5 bg-teal-100 text-teal-600 rounded-full text-[10px] font-bold">Operations</span>
                        </div>
                        <div class="divide-y divide-gray-50">
                            {{-- Stale Reception --}}
                            @if($stats['issues']['stale_reception'] > 0)
                                <div class="p-6 flex items-start gap-4 hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center flex-shrink-0 font-bold">!</div>
                                    <div class="flex-1">
                                        <p class="text-sm font-black text-gray-900">Antrean Penerimaan Mengendap</p>
                                        <p class="text-xs text-gray-500 mt-1">Ditemukan <b>{{ $stats['issues']['stale_reception'] }}</b> SPK yang sudah lebih dari 7 hari belum diterima oleh gudang.</p>
                                        <a href="{{ route('reception.index') }}" class="inline-block mt-3 text-[10px] font-black text-teal-600 uppercase tracking-widest hover:underline">Cek Penerimaan →</a>
                                    </div>
                                </div>
                            @endif

                            {{-- Stale Transit (Before) --}}
                            @if($stats['issues']['stale_transit'] > 0)
                                <div class="p-6 flex items-start gap-4 hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-black text-gray-900">Stagnan di Rak Transit (Before)</p>
                                        <p class="text-xs text-gray-500 mt-1">Ada <b>{{ $stats['issues']['stale_transit'] }}</b> barang di rak before yang lebih dari 3 hari belum masuk Assessment.</p>
                                        <a href="{{ route('index') }}" class="inline-block mt-3 text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Proses ke Workshop →</a>
                                    </div>
                                </div>
                            @endif

                            {{-- Overdue Production --}}
                            @if($stats['issues']['overdue_production'] > 0)
                                <div class="p-6 flex items-start gap-4 hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-black text-gray-900">Produksi Melewati Estimasi</p>
                                        <p class="text-xs text-gray-500 mt-1">Terdapat <b>{{ $stats['issues']['overdue_production'] }}</b> order yang sudah melewati tanggal estimasi selesai.</p>
                                        <a href="{{ route('workshop.dashboard') }}" class="inline-block mt-3 text-[10px] font-black text-red-600 uppercase tracking-widest hover:underline">Monitor Workshop →</a>
                                    </div>
                                </div>
                            @endif

                            {{-- Orphaned Storage --}}
                            @if($stats['issues']['orphaned_storage'] > 0)
                                <div class="p-6 flex items-start gap-4 hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center flex-shrink-0 font-bold">?</div>
                                    <div class="flex-1">
                                        <p class="text-sm font-black text-gray-900">Storage Tanpa SPK (Orphaned)</p>
                                        <p class="text-xs text-gray-500 mt-1">Ditemukan <b>{{ $stats['issues']['orphaned_storage'] }}</b> penempatan rak yang SPK-nya sudah terhapus permanen.</p>
                                        <form action="{{ route('admin.system.cleanup-orphaned-storage') }}" method="POST" onsubmit="return confirm('Tindakan ini akan membersihkan sisa storage tak bertuan. Lanjutkan?')">
                                            @csrf
                                            <button type="submit" class="inline-block mt-3 text-[10px] font-black text-red-600 uppercase tracking-widest hover:underline">Bersihkan Otomatis →</button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            @if($stats['issues']['stale_reception'] == 0 && $stats['issues']['stale_transit'] == 0 && $stats['issues']['overdue_production'] == 0 && $stats['issues']['orphaned_storage'] == 0)
                                <div class="p-12 text-center">
                                    <p class="text-sm font-bold text-gray-400 italic">Operasi gudang & produksi berjalan lancar.</p>
                                </div>
                            @endif
                </div>
            </div>

            {{-- Departmental Cleanup --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 bg-red-600 border-b border-red-700 flex items-center justify-between">
                    <div>
                        <h4 class="font-black text-white uppercase tracking-widest text-xs italic">Maintenance Departemen</h4>
                        <p class="text-[10px] text-red-100 font-bold tracking-tight">Tindakan Pembersihan Massal Permanen</p>
                    </div>
                    <svg class="w-6 h-6 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @php
                        $cleanupDepts = [
                            ['name' => 'Workshop', 'count' => $stats['trash']['workshop'], 'category' => 'workshop', 'type' => 'work_order', 'color' => 'blue'],
                            ['name' => 'Customer Service', 'count' => $stats['trash']['cs'], 'category' => 'cs', 'type' => 'cs_lead', 'color' => 'indigo'],
                            ['name' => 'Warehouse', 'count' => $stats['trash']['warehouse'], 'category' => 'warehouse', 'type' => 'material_request', 'color' => 'teal'],
                            ['name' => 'Experience & Master', 'count' => $stats['trash']['cx'] + $stats['trash']['master'], 'category' => 'cx', 'type' => 'complaint', 'color' => 'red']
                        ];
                    @endphp

                    @foreach($cleanupDepts as $dept)
                        <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100 flex flex-col justify-between">
                            <div>
                                <span class="px-2 py-0.5 bg-{{ $dept['color'] }}-100 text-{{ $dept['color'] }}-600 rounded-full text-[8px] font-black uppercase tracking-widest">{{ $dept['name'] }}</span>
                                <h5 class="mt-4 text-2xl font-black text-gray-900 tracking-tight">{{ $dept['count'] }} <span class="text-xs font-bold text-gray-400">item</span></h5>
                                <p class="text-[10px] text-gray-500 font-bold uppercase mt-1">Data di Sampah {{ $dept['name'] }}</p>
                            </div>
                            <div class="mt-6 space-y-2">
                                <a href="{{ route('admin.data-integrity.trash', ['category' => $dept['category']]) }}" 
                                   class="block text-center px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-gray-50 transition-colors">
                                    Tinjau Sampah
                                </a>
                                <form action="{{ route('admin.data-integrity.cleanup') }}" method="POST" onsubmit="return confirm('HAPUS SEMUA SAMPAH {{ $dept['name'] }} SECARA PERMANEN?')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="category" value="{{ $dept['category'] }}">
                                    <button type="submit" @if($dept['count'] == 0) disabled @endif class="block w-full text-center px-4 py-2 {{ $dept['count'] == 0 ? 'bg-gray-100 text-gray-400 opacity-50 cursor-not-allowed' : 'bg-red-50 text-red-600 hover:bg-red-100' }} rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">
                                        Kosongkan
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

@php
    $groups = $matrixData['groups'] ?? [];
    $totalSpk = $matrixData['total_spk'] ?? 0;
@endphp

<div class="space-y-6">
    {{-- Main Header --}}
    <div class="flex items-center">
        <div class="bg-slate-800 text-white px-8 py-3 rounded-tr-[2rem] rounded-bl-[1rem] shadow-lg border-b-4 border-teal-500">
            <h2 class="text-xl font-black uppercase tracking-[0.2em] flex items-center gap-3">
                <span class="w-2 h-6 bg-orange-500 rounded-full"></span>
                KONTROL SPK WORKSHOP
            </h2>
        </div>
        <div class="flex-1 border-b-2 border-slate-200 ml-4 hidden md:block opacity-30"></div>
    </div>

    {{-- The Matrix Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        {{-- 1. PROSES PERSIAPAN --}}
        <div class="group bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden transform hover:-translate-y-2 transition-all duration-500">
            {{-- Category Header --}}
            <div class="bg-gradient-to-br from-red-500 to-pink-600 p-6 flex justify-between items-center text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <span class="font-black text-lg tracking-tight uppercase">Proses Persiapan</span>
                </div>
                <div class="bg-white/20 px-3 py-1 rounded-full text-xs font-black backdrop-blur-md">
                    {{ $groups['Persiapan']['total'] ?? 0 }} SPK
                </div>
            </div>

            {{-- List Items --}}
            <div class="p-8 space-y-4">
                @php 
                    $persiapanItems = [
                        'Cuci' => $groups['Persiapan']['Cuci'] ?? 0,
                        'Bongkar Sol' => $groups['Persiapan']['Bongkar Sol'] ?? 0,
                        'Bongkar Upper' => $groups['Persiapan']['Bongkar Upper'] ?? 0,
                        'Persiapan Bahan' => $groups['Persiapan']['Persiapan Bahan'] ?? 0,
                        'Revisi' => $groups['Persiapan']['Revisi'] ?? 0,
                        'Followup' => $groups['Persiapan']['Followup'] ?? 0,
                    ];
                @endphp
                @foreach($persiapanItems as $label => $val)
                    <div class="flex justify-between items-center group/item hover:bg-slate-50 p-2 rounded-xl transition-colors">
                        <span class="text-slate-600 font-bold tracking-tight">{{ $label }}</span>
                        <span class="w-12 text-center py-1 bg-slate-100 text-slate-800 rounded-lg font-black text-sm group-hover/item:bg-red-100 group-hover/item:text-red-700 transition-colors">
                            {{ $val }}
                        </span>
                    </div>
                @endforeach
            </div>
            
            {{-- Decorative accent --}}
            <div class="h-2 bg-gradient-to-r from-red-500 to-pink-600 opacity-20"></div>
        </div>

        {{-- 2. PROSES REPARASI --}}
        <div class="group bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden transform hover:-translate-y-2 transition-all duration-500">
            {{-- Category Header --}}
            <div class="bg-gradient-to-br from-orange-400 to-yellow-500 p-6 flex justify-between items-center text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <span class="font-black text-lg tracking-tight uppercase">Proses Reparasi</span>
                </div>
                <div class="bg-white/20 px-3 py-1 rounded-full text-xs font-black backdrop-blur-md">
                    {{ $groups['Reparasi']['total'] ?? 0 }} SPK
                </div>
            </div>

            {{-- List Items --}}
            <div class="p-8 space-y-4">
                @php 
                    $reparasiItems = [
                        'Upper' => $groups['Reparasi']['Upper'] ?? 0,
                        'Sol' => $groups['Reparasi']['Sol'] ?? 0,
                        'Repaint' => $groups['Reparasi']['Repaint'] ?? 0,
                        'Treatment' => $groups['Reparasi']['Treatment'] ?? 0,
                        'Revisi' => $groups['Reparasi']['Revisi'] ?? 0,
                        'Followup' => $groups['Reparasi']['Followup'] ?? 0,
                    ];
                @endphp
                @foreach($reparasiItems as $label => $val)
                    <div class="flex justify-between items-center group/item hover:bg-slate-50 p-2 rounded-xl transition-colors">
                        <span class="text-slate-600 font-bold tracking-tight">{{ $label }}</span>
                        <span class="w-12 text-center py-1 bg-slate-100 text-slate-800 rounded-lg font-black text-sm group-hover/item:bg-orange-100 group-hover/item:text-orange-700 transition-colors">
                            {{ $val }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Decorative accent --}}
            <div class="h-2 bg-gradient-to-r from-orange-400 to-yellow-500 opacity-20"></div>
        </div>

        {{-- 3. PROSES POST --}}
        <div class="group bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden transform hover:-translate-y-2 transition-all duration-500">
            {{-- Category Header --}}
            <div class="bg-gradient-to-br from-teal-500 to-emerald-600 p-6 flex justify-between items-center text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="font-black text-lg tracking-tight uppercase">Proses Post</span>
                </div>
                <div class="bg-white/20 px-3 py-1 rounded-full text-xs font-black backdrop-blur-md">
                    {{ $groups['Post']['total'] ?? 0 }} SPK
                </div>
            </div>

            {{-- List Items --}}
            <div class="p-8 space-y-4">
                @php 
                    $postItems = [
                        'Jahit Sol' => $groups['Post']['Jahit Sol'] ?? 0,
                        'Cleanup' => $groups['Post']['Cleanup'] ?? 0,
                        'Qc' => $groups['Post']['Qc'] ?? 0,
                        'Foto After' => $groups['Post']['Foto After'] ?? 0,
                        'Revisi' => $groups['Post']['Revisi'] ?? 0,
                        'Followup' => $groups['Post']['Followup'] ?? 0,
                    ];
                @endphp
                @foreach($postItems as $label => $val)
                    <div class="flex justify-between items-center group/item hover:bg-slate-50 p-2 rounded-xl transition-colors">
                        <span class="text-slate-600 font-bold tracking-tight">{{ $label }}</span>
                        <span class="w-12 text-center py-1 bg-slate-100 text-slate-800 rounded-lg font-black text-sm group-hover/item:bg-teal-100 group-hover/item:text-teal-700 transition-colors">
                            {{ $val }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Decorative accent --}}
            <div class="h-2 bg-gradient-to-r from-teal-500 to-emerald-600 opacity-20"></div>
        </div>

    </div>

    {{-- Grand Total Bar --}}
    <div class="mt-8">
        <div class="bg-gradient-to-r from-teal-600 via-teal-700 to-slate-900 rounded-3xl p-6 md:p-8 flex flex-col md:flex-row justify-between items-center shadow-2xl relative overflow-hidden group/total">
            {{-- Abstract Background SVG --}}
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover/total:scale-150 transition-transform duration-1000"></div>
            
            <div class="flex items-center gap-6 relative z-10">
                <div class="w-16 h-16 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl flex items-center justify-center text-3xl">
                    🏢
                </div>
                <div>
                    <h3 class="text-2xl font-black text-white tracking-widest uppercase">Total SPK Di Workshop</h3>
                    <p class="text-teal-200 text-sm font-bold uppercase tracking-widest opacity-80">Aggregate Load Monitor</p>
                </div>
            </div>

            <div class="mt-6 md:mt-0 relative z-10">
                <div class="bg-white px-8 py-4 rounded-[2rem] shadow-2xl text-center border-b-4 border-orange-500 flex flex-col items-center justify-center transform group-hover/total:scale-110 transition-transform duration-500">
                    <span class="text-4xl font-black text-slate-900 leading-none">{{ $totalSpk }}</span>
                    <span class="text-[10px] font-black text-teal-600 uppercase tracking-widest mt-1">Order Aktif</span>
                </div>
            </div>
        </div>
    </div>
</div>

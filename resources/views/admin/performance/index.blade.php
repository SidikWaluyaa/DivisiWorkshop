<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-3">
            <h2 class="font-bold text-xl text-white leading-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                {{ __('Performance & Produktivitas Teknisi') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1 bg-white/10 rounded-lg backdrop-blur-sm border border-white/20 text-white text-xs font-semibold">
                    Total: {{ $users->count() }} Teknisi/PIC
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50 dark:bg-gray-900 min-h-screen" x-data="{ 
        openDetail: false, 
        selectedTechName: '', 
        selectedTechJobs: [] 
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- FILTER DATE RANGE & QUICK PRESETS -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-150 dark:border-gray-700 p-6">
                <form method="GET" action="{{ route('admin.performance.index') }}" class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
                        <div class="flex flex-col gap-1 flex-1 sm:flex-none">
                            <label class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-wider">Mulai Tanggal</label>
                            <input type="date" name="start_date" value="{{ $start->format('Y-m-d') }}" 
                                   class="w-full sm:w-44 py-2 px-3 text-xs font-bold bg-gray-50 border border-gray-250 dark:border-gray-650 rounded-xl focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="flex flex-col gap-1 flex-1 sm:flex-none">
                            <label class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-wider">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ $end->format('Y-m-d') }}" 
                                   class="w-full sm:w-44 py-2 px-3 text-xs font-bold bg-gray-50 border border-gray-250 dark:border-gray-650 rounded-xl focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="flex items-end h-full pt-5">
                            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-teal-100 dark:shadow-none transition-all active:scale-95 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Filter
                            </button>
                        </div>
                    </div>
                    
                    <!-- Quick Presets -->
                    <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto lg:justify-end">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block w-full lg:w-auto mb-1 lg:mb-0 mr-2">Pilihan Cepat:</span>
                        <a href="?start_date={{ \Illuminate\Support\Carbon::today()->format('Y-m-d') }}&end_date={{ \Illuminate\Support\Carbon::today()->format('Y-m-d') }}" 
                           class="px-3 py-2 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 rounded-xl text-[10px] font-black uppercase tracking-wider text-gray-700 dark:text-gray-200 transition-all">
                            Hari Ini
                        </a>
                        <a href="?start_date={{ \Illuminate\Support\Carbon::now()->startOfWeek()->format('Y-m-d') }}&end_date={{ \Illuminate\Support\Carbon::today()->format('Y-m-d') }}" 
                           class="px-3 py-2 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 rounded-xl text-[10px] font-black uppercase tracking-wider text-gray-700 dark:text-gray-200 transition-all">
                            Minggu Ini
                        </a>
                        <a href="?start_date={{ \Illuminate\Support\Carbon::now()->startOfMonth()->format('Y-m-d') }}&end_date={{ \Illuminate\Support\Carbon::today()->format('Y-m-d') }}" 
                           class="px-3 py-2 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 rounded-xl text-[10px] font-black uppercase tracking-wider text-gray-700 dark:text-gray-200 transition-all">
                            Bulan Ini
                        </a>
                    </div>
                </form>
            </div>

            <!-- STATS CARDS -->
            @php
                $totPrep = $users->sum('prep_washing_count') + $users->sum('prep_sol_count') + $users->sum('prep_upper_count');
                $totSortir = $users->sum('sortir_sol_count') + $users->sum('sortir_upper_count');
                $totProd = $users->sum('prod_sol_count') + $users->sum('prod_upper_count') + $users->sum('prod_cleaning_count');
                $totQc = $users->sum('qc_jahit_count') + $users->sum('qc_cleanup_count') + $users->sum('qc_final_count');
                $totAll = $totPrep + $totSortir + $totProd + $totQc;
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Total Jobs -->
                <div class="bg-gradient-to-br from-teal-500 to-emerald-600 text-white rounded-2xl shadow-sm p-5 flex flex-col justify-between">
                    <span class="text-[10px] font-black uppercase tracking-widest text-teal-100">Total Pekerjaan</span>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-3xl font-black">{{ $totAll }}</span>
                        <span class="text-xs font-semibold text-teal-100">tugas selesai</span>
                    </div>
                </div>
                <!-- Prep -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-150 dark:border-gray-700 p-5 flex flex-col justify-between">
                    <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Preparation</span>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-2xl font-black text-yellow-600">{{ $totPrep }}</span>
                        <span class="text-xs font-bold text-gray-400 uppercase">Subtasks</span>
                    </div>
                </div>
                <!-- Sortir -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-150 dark:border-gray-700 p-5 flex flex-col justify-between">
                    <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Sortir</span>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-2xl font-black text-blue-600">{{ $totSortir }}</span>
                        <span class="text-xs font-bold text-gray-400 uppercase">SPK</span>
                    </div>
                </div>
                <!-- Production -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-150 dark:border-gray-700 p-5 flex flex-col justify-between">
                    <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Production</span>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-2xl font-black text-indigo-600">{{ $totProd }}</span>
                        <span class="text-xs font-bold text-gray-400 uppercase">Proses</span>
                    </div>
                </div>
                <!-- QC -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-150 dark:border-gray-700 p-5 flex flex-col justify-between">
                    <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Quality Control</span>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-2xl font-black text-green-600">{{ $totQc }}</span>
                        <span class="text-xs font-bold text-gray-400 uppercase">Pengecekan</span>
                    </div>
                </div>
            </div>

            <!-- LEADERBOARD TOP 3 -->
            @if($sortedUsersForLeaderboard->where('total_jobs', '>', 0)->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-150 dark:border-gray-700 p-6">
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500 animate-bounce" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.18l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    Teknisi Paling Produktif Periode Ini
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($sortedUsersForLeaderboard->where('total_jobs', '>', 0)->take(3) as $index => $leader)
                        @php
                            $colors = [
                                0 => ['bg' => 'from-amber-400 to-yellow-500', 'border' => 'border-amber-200', 'shadow' => 'shadow-amber-100', 'badge' => '🥇'],
                                1 => ['bg' => 'from-slate-300 to-slate-400', 'border' => 'border-slate-200', 'shadow' => 'shadow-slate-100', 'badge' => '🥈'],
                                2 => ['bg' => 'from-amber-600 to-amber-700', 'border' => 'border-amber-500', 'shadow' => 'shadow-amber-900/10', 'badge' => '🥉'],
                            ][$index] ?? ['bg' => 'from-gray-100 to-gray-200', 'border' => 'border-gray-200', 'shadow' => 'shadow-none', 'badge' => ''];
                        @endphp
                        <div class="bg-gradient-to-r {{ $colors['bg'] }} p-[2px] rounded-2xl shadow-xl {{ $colors['shadow'] }}">
                            <div class="bg-white dark:bg-gray-800 rounded-[14px] p-5 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center font-black text-lg relative text-gray-700 dark:text-gray-200">
                                        {{ strtoupper(substr($leader->name, 0, 2)) }}
                                        <span class="absolute -top-2 -left-2 text-xl">{{ $colors['badge'] }}</span>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-sm text-gray-900 dark:text-white uppercase tracking-tight">{{ $leader->name }}</h4>
                                        <span class="px-2 py-0.5 rounded bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 text-[9px] font-black uppercase">{{ $leader->specialization ?? 'Umum' }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl font-black text-gray-950 dark:text-white">{{ $leader->total_jobs }}</span>
                                    <div class="text-[9px] font-black uppercase text-gray-400 dark:text-gray-500">Tugas</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- MAIN PERFORMANCE TABLE -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-150 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-150 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                    <h3 class="font-black text-sm text-gray-900 dark:text-white uppercase tracking-tight">Detail Produktivitas per Spesialisasi</h3>
                    <span class="text-xs text-gray-500 font-medium">Periode: {{ $start->format('d M Y') }} s/d {{ $end->format('d M Y') }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-750 dark:text-gray-300">
                            <tr>
                                <th class="px-6 py-4">Nama Teknisi</th>
                                <th class="px-6 py-4 text-center bg-yellow-50/50 dark:bg-gray-800">Prep</th>
                                <th class="px-6 py-4 text-center bg-blue-50/50 dark:bg-gray-800">Sortir</th>
                                <th class="px-6 py-4 text-center bg-indigo-50/50 dark:bg-gray-800">Production</th>
                                <th class="px-6 py-4 text-center bg-green-50/50 dark:bg-gray-800">QC</th>
                                <th class="px-6 py-4 text-center bg-teal-50 dark:bg-gray-700 font-black text-teal-800 dark:text-teal-200">TOTAL</th>
                                <th class="px-6 py-4 text-center bg-red-50 dark:bg-red-950/20 text-red-650 dark:text-red-400 font-bold">Revisi</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 dark:divide-gray-750">
                            @forelse($usersBySpecialization as $spec => $specUsers)
                                <tr class="bg-gray-100/50 dark:bg-gray-850">
                                    <td colspan="8" class="px-6 py-2.5 font-black text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                        📁 {{ $spec ?: 'TANPA SPESIALISASI' }}
                                    </td>
                                </tr>
                                @foreach($specUsers as $user)
                                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50/50 dark:hover:bg-gray-750/30 transition-all">
                                        <!-- User details -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-900/10 text-teal-700 dark:text-teal-300 font-black text-xs flex items-center justify-center">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                                    <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $user->role }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Prep -->
                                        <td class="px-6 py-4 text-center font-bold text-yellow-600">
                                            {{ $user->prep_washing_count + $user->prep_sol_count + $user->prep_upper_count }}
                                            <div class="text-[8px] text-gray-400 font-normal uppercase tracking-tighter">
                                                W:{{ $user->prep_washing_count }} | S:{{ $user->prep_sol_count }} | U:{{ $user->prep_upper_count }}
                                            </div>
                                        </td>

                                        <!-- Sortir -->
                                        <td class="px-6 py-4 text-center font-bold text-blue-600">
                                            {{ $user->sortir_sol_count + $user->sortir_upper_count }}
                                            <div class="text-[8px] text-gray-400 font-normal uppercase tracking-tighter">
                                                S:{{ $user->sortir_sol_count }} | U:{{ $user->sortir_upper_count }}
                                            </div>
                                        </td>

                                        <!-- Production -->
                                        <td class="px-6 py-4 text-center font-bold text-indigo-600">
                                            {{ $user->prod_sol_count + $user->prod_upper_count + $user->prod_cleaning_count }}
                                            <div class="text-[8px] text-gray-400 font-normal uppercase tracking-tighter">
                                                S:{{ $user->prod_sol_count }} | U:{{ $user->prod_upper_count }} | C:{{ $user->prod_cleaning_count }}
                                            </div>
                                        </td>

                                        <!-- QC -->
                                        <td class="px-6 py-4 text-center font-bold text-green-600">
                                            {{ $user->qc_jahit_count + $user->qc_cleanup_count + $user->qc_final_count }}
                                            <div class="text-[8px] text-gray-400 font-normal uppercase tracking-tighter">
                                                J:{{ $user->qc_jahit_count }} | C:{{ $user->qc_cleanup_count }} | F:{{ $user->qc_final_count }}
                                            </div>
                                        </td>

                                        <!-- Total -->
                                        <td class="px-6 py-4 text-center font-black text-lg text-teal-600 bg-teal-50/20 dark:bg-teal-900/10">
                                            {{ $user->total_jobs }}
                                        </td>

                                        <!-- Complaints -->
                                        <td class="px-6 py-4 text-center font-bold text-red-650 dark:text-red-400">
                                            {{ $user->complaints_count ?: '-' }}
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4 text-right">
                                            <button type="button" 
                                                    @click="
                                                        selectedTechName = '{{ $user->name }}';
                                                        selectedTechJobs = {{ json_encode($user->completed_orders_details) }};
                                                        openDetail = true;
                                                    "
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-teal-50 dark:bg-gray-700 dark:hover:bg-teal-900/30 text-gray-700 hover:text-teal-700 dark:text-gray-200 dark:hover:text-teal-300 border border-gray-200 dark:border-gray-600 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                Audit
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-full mb-3">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </div>
                                            <p class="font-black text-sm text-gray-900 dark:text-white uppercase tracking-wider">Tidak Ada Data Teknisi</p>
                                            <p class="text-xs text-gray-400 mt-1">Silakan sesuaikan tanggal atau tambahkan teknisi ke sistem.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ALPINE.JS AUDIT MODAL -->
        <div x-show="openDetail" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-950/40 backdrop-blur-sm transition-opacity" @click="openDetail = false"></div>

            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-4xl transform rounded-2xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-100 dark:border-gray-700 overflow-hidden">
                    
                    <!-- Modal Header -->
                    <div class="bg-gray-50 dark:bg-gray-850 px-6 py-4 border-b border-gray-150 dark:border-gray-700 flex justify-between items-center">
                        <div>
                            <h3 class="font-black text-sm text-gray-900 dark:text-white uppercase tracking-tight flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-teal-500"></span>
                                Rincian Pengerjaan: <span x-text="selectedTechName" class="text-teal-600"></span>
                            </h3>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase mt-0.5">Daftar semua tugas yang selesai dalam periode terpilih</p>
                        </div>
                        <button @click="openDetail = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 max-h-[500px] overflow-y-auto">
                        <template x-if="selectedTechJobs.length === 0">
                            <div class="text-center py-12 text-gray-400 font-medium italic">✨ Tidak ada pekerjaan selesai pada periode ini.</div>
                        </template>

                        <template x-if="selectedTechJobs.length > 0">
                            <div class="overflow-x-auto rounded-xl border border-gray-150 dark:border-gray-700">
                                <table class="min-w-full w-full text-xs text-left text-gray-500 dark:text-gray-400">
                                    <thead class="bg-gray-50 dark:bg-gray-750 text-gray-700 dark:text-gray-300 uppercase font-black">
                                        <tr>
                                            <th class="px-4 py-3">SPK</th>
                                            <th class="px-4 py-3">Pelanggan</th>
                                            <th class="px-4 py-3">Sepatu</th>
                                            <th class="px-4 py-3">Treatment</th>
                                            <th class="px-4 py-3">Stasiun Kerja</th>
                                            <th class="px-4 py-3 text-right">Selesai Pada</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-150 dark:divide-gray-750">
                                        <template x-for="job in selectedTechJobs" :key="job.spk_number + job.stations">
                                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750/30">
                                                <td class="px-4 py-3 font-mono font-black text-gray-900 dark:text-white" x-text="job.spk_number"></td>
                                                <td class="px-4 py-3 font-semibold text-gray-750 dark:text-gray-300" x-text="job.customer_name"></td>
                                                <td class="px-4 py-3 text-gray-600 dark:text-gray-450" x-text="job.shoe"></td>
                                                <td class="px-4 py-3 text-gray-650 dark:text-gray-350" x-text="job.treatment"></td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-0.5 rounded bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 font-bold" x-text="job.stations"></span>
                                                </td>
                                                <td class="px-4 py-3 text-right text-gray-400 dark:text-gray-550" x-text="job.date"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 dark:bg-gray-850 px-6 py-4 border-t border-gray-150 dark:border-gray-700 flex justify-end">
                        <button @click="openDetail = false" class="px-5 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>

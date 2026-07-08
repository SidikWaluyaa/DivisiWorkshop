<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-xl text-white leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-teal-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Log Aktivitas Sistem (Audit Logs)') }}
            </h2>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-white/10 rounded-lg backdrop-blur-sm border border-white/20 text-white text-sm">
                    <span class="opacity-75">Total Log:</span> 
                    <span class="font-bold ml-1">{{ $logs->total() }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filters & Search Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 p-5 mb-6">
                <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Search field --}}
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Cari Aktivitas / Perangkat</label>
                            <div class="absolute inset-y-0 left-0 pl-3 pt-6 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   class="w-full pl-9 pr-4 py-2 rounded-xl border-gray-200 focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 text-sm bg-gray-50 focus:bg-white" 
                                   placeholder="Kata kunci log...">
                        </div>

                        {{-- User filter --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Filter Pengguna</label>
                            <select name="user_id" 
                                    class="w-full py-2 rounded-xl border-gray-200 focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 text-sm bg-gray-50 focus:bg-white">
                                <option value="">-- Semua Pengguna --</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ ucfirst($u->role) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Start Date --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Tanggal Mulai</label>
                            <input type="date" 
                                   name="start_date" 
                                   value="{{ request('start_date') }}"
                                   class="w-full py-2 rounded-xl border-gray-200 focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 text-sm bg-gray-50 focus:bg-white">
                        </div>

                        {{-- End Date --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Tanggal Akhir</label>
                            <input type="date" 
                                   name="end_date" 
                                   value="{{ request('end_date') }}"
                                   class="w-full py-2 rounded-xl border-gray-200 focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 text-sm bg-gray-50 focus:bg-white">
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('admin.activity-logs.index') }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-xl text-sm font-semibold transition-colors">
                            Reset Filter
                        </a>
                        <button type="submit" 
                                class="px-5 py-2 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white rounded-xl text-sm font-semibold shadow-md shadow-teal-500/20 transition-all">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>

            {{-- Logs List Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 overflow-hidden">
                
                {{-- Table Desktop --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gradient-to-r from-teal-50 to-emerald-50 dark:from-gray-700 dark:to-gray-700">
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Pengguna</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Aktivitas</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Detail Deskripsi</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Sumber & Perangkat</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($logs as $log)
                            <tr class="hover:bg-teal-50/20 dark:hover:bg-gray-700/50 transition-colors">
                                {{-- User Column --}}
                                <td class="px-6 py-4">
                                    @if ($log->user)
                                        <div class="flex items-center">
                                            <div class="h-9 w-9 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 font-bold text-sm">
                                                {{ substr($log->user->name, 0, 2) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $log->user->name }}</div>
                                                <div class="text-[10px] text-gray-400 font-mono tracking-tight">{{ $log->user->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center">
                                            <div class="h-9 w-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 font-bold text-sm">
                                                S
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-semibold text-gray-500 italic">Sistem / Dihapus</div>
                                                <div class="text-[10px] text-gray-400 font-mono tracking-tight">-</div>
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                {{-- Activity Title --}}
                                <td class="px-6 py-4">
                                    @php
                                        // Dynamic color based on common keywords
                                        $activityClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                        $act = strtolower($log->activity);
                                        if (str_contains($act, 'hapus') || str_contains($act, 'delete')) {
                                            $activityClass = 'bg-red-50 text-red-700 border border-red-200/50 dark:bg-red-950/20 dark:text-red-400 dark:border-red-900/30';
                                        } elseif (str_contains($act, 'buat') || str_contains($act, 'tambah') || str_contains($act, 'create')) {
                                            $activityClass = 'bg-green-50 text-green-700 border border-green-200/50 dark:bg-green-950/20 dark:text-green-400 dark:border-green-900/30';
                                        } elseif (str_contains($act, 'edit') || str_contains($act, 'ubah') || str_contains($act, 'update')) {
                                            $activityClass = 'bg-amber-50 text-amber-700 border border-amber-200/50 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30';
                                        } elseif (str_contains($act, 'login')) {
                                            $activityClass = 'bg-blue-50 text-blue-700 border border-blue-200/50 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/30';
                                        }
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg inline-block {{ $activityClass }}">
                                        {{ $log->activity }}
                                    </span>
                                </td>

                                {{-- Description Detail --}}
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-700 dark:text-gray-300 max-w-sm whitespace-normal break-words leading-relaxed font-medium">
                                        {{ $log->description ?? '-' }}
                                    </div>
                                </td>

                                {{-- Metadata (IP & Device) --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $ua = $log->user_agent ?? '';
                                        $os = 'Unknown OS';
                                        if (preg_match('/windows|win32/i', $ua)) $os = 'Windows';
                                        elseif (preg_match('/macintosh|mac os x/i', $ua)) $os = 'Mac OS';
                                        elseif (preg_match('/android/i', $ua)) $os = 'Android';
                                        elseif (preg_match('/iphone|ipad/i', $ua)) $os = 'iOS';
                                        elseif (preg_match('/linux/i', $ua)) $os = 'Linux';

                                        $browser = 'Unknown';
                                        if (preg_match('/chrome/i', $ua) && !preg_match('/edge|edg/i', $ua)) $browser = 'Chrome';
                                        elseif (preg_match('/firefox/i', $ua)) $browser = 'Firefox';
                                        elseif (preg_match('/safari/i', $ua) && !preg_match('/chrome/i', $ua)) $browser = 'Safari';
                                        elseif (preg_match('/edge|edg/i', $ua)) $browser = 'Edge';
                                        elseif (preg_match('/opera|opr/i', $ua)) $browser = 'Opera';
                                    @endphp
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 rounded-md">
                                                {{ $os }}
                                            </span>
                                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-teal-50 text-teal-600 dark:bg-teal-950/20 dark:text-teal-400 rounded-md">
                                                {{ $browser }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] font-mono text-gray-400 tracking-tight" title="{{ $ua }}">
                                            IP: {{ $log->ip_address ?? '127.0.0.1' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Date/Time Column --}}
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="text-xs font-semibold text-gray-800 dark:text-gray-200" title="{{ $log->created_at->translatedFormat('d M Y H:i:s') }}">
                                        {{ $log->created_at->diffForHumans() }}
                                    </div>
                                    <div class="text-[10px] text-gray-400 tracking-tighter mt-0.5">
                                        {{ $log->created_at->translatedFormat('d M H:i') }} WIB
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic text-sm">
                                    Tidak ada log aktivitas sistem yang ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                @if ($logs->hasPages())
                    <div class="bg-gray-50 dark:bg-gray-850 px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

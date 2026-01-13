<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-3">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Performance & Produktivitas Teknisi') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1 bg-white/10 rounded-lg backdrop-blur-sm border border-white/20 text-white text-xs">
                    Total: {{ $users->count() }} Teknisi/PIC
                </span>
                <span class="px-3 py-1 bg-white/10 rounded-lg backdrop-blur-sm border border-white/20 text-white text-xs">
                    Spesialisasi: {{ $usersBySpecialization->count() }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                        <h3 class="text-lg font-bold">Ringkasan Pekerjaan (All Time)</h3>
                        <div class="text-xs text-gray-500 bg-blue-50 dark:bg-gray-700 px-3 py-2 rounded-lg">
                            <strong>Filter:</strong> Hanya Teknisi & PIC | Diurutkan berdasarkan Spesialisasi
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto -mx-4 sm:mx-0">
                        <table class="min-w-full w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th rowspan="2" class="px-6 py-3 border-r">Nama Teknisi / PIC</th>
                                    <th rowspan="2" class="px-6 py-3 border-r bg-purple-50 dark:bg-gray-800">Spesialisasi</th>
                                    <th rowspan="2" class="px-6 py-3 text-center border-r bg-yellow-50 dark:bg-gray-800 font-bold">Preparation<br>(Subtasks)</th>
                                    <th colspan="2" class="px-6 py-3 text-center border-r bg-blue-50 dark:bg-gray-800">Sortir</th>
                                    <th rowspan="2" class="px-6 py-3 text-center border-r bg-indigo-50 dark:bg-gray-800 font-bold">Production</th>
                                    <th colspan="3" class="px-6 py-3 text-center bg-green-50 dark:bg-gray-800">Quality Control (QC)</th>
                                    <th rowspan="2" class="px-6 py-3 text-center border-l bg-gray-100 dark:bg-gray-600 font-black">TOTAL</th>
                                </tr>
                                <tr>
                                    <!-- Sortir headers -->
                                    <th class="px-4 py-2 text-center bg-blue-100 dark:bg-gray-700">Sol</th>
                                    <th class="px-4 py-2 text-center border-r bg-blue-100 dark:bg-gray-700">Upper</th>
                                    
                                    <!-- QC headers -->
                                    <th class="px-4 py-2 text-center bg-green-100 dark:bg-gray-700">Jahit</th>
                                    <th class="px-4 py-2 text-center bg-green-100 dark:bg-gray-700">Clean</th>
                                    <th class="px-4 py-2 text-center bg-green-100 dark:bg-gray-700">Final</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentSpecialization = null; @endphp
                                @foreach($users as $user)
                                    @if($currentSpecialization !== $user->specialization)
                                        @php $currentSpecialization = $user->specialization; @endphp
                                        <tr class="bg-teal-50 dark:bg-gray-700 border-t-2 border-teal-200">
                                            <td colspan="10" class="px-6 py-2 font-bold text-teal-800 dark:text-teal-300 text-xs uppercase tracking-wider">
                                                📌 {{ $user->specialization ?? 'Tidak Ada Spesialisasi' }}
                                            </td>
                                        </tr>
                                    @endif
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white border-r">
                                        {{ $user->name }}
                                        <div class="text-xs text-gray-400">{{ ucfirst($user->role) }}</div>
                                    </td>
                                    
                                    <!-- Specialization -->
                                    <td class="px-6 py-4 border-r">
                                        @if($user->specialization)
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300 rounded-full text-xs font-semibold">
                                                {{ $user->specialization }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs italic">-</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Preparation -->
                                    <td class="px-6 py-4 text-center border-r font-bold text-yellow-600">
                                        {{ $user->prep_tasks_count }}
                                    </td>

                                    <!-- Sortir -->
                                    <td class="px-4 py-4 text-center">
                                        {{ $user->jobs_sortir_sol_count }}
                                    </td>
                                    <td class="px-4 py-4 text-center border-r">
                                        {{ $user->jobs_sortir_upper_count }}
                                    </td>

                                    <!-- Production -->
                                    <td class="px-6 py-4 text-center border-r font-bold text-indigo-600">
                                        {{ $user->jobs_production_count }}
                                    </td>
                                    
                                    <!-- QC -->
                                    <td class="px-4 py-4 text-center">
                                        {{ $user->jobs_qc_jahit_count }}
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        {{ $user->jobs_qc_cleanup_count }}
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-green-600">
                                        {{ $user->jobs_qc_final_count }}
                                    </td>

                                    <!-- TOTAL -->
                                    @php
                                        $total = $user->prep_tasks_count + 
                                                 $user->jobs_sortir_sol_count + 
                                                 $user->jobs_sortir_upper_count + 
                                                 $user->jobs_production_count + 
                                                 $user->jobs_qc_jahit_count + 
                                                 $user->jobs_qc_cleanup_count + 
                                                 $user->jobs_qc_final_count;
                                    @endphp
                                    <td class="px-6 py-4 text-center border-l font-black text-lg {{ $total > 0 ? 'text-teal-600' : 'text-gray-400' }}">
                                        {{ $total }}
                                    </td>
                                </tr>
                                @endforeach
                                
                                @if($users->isEmpty())
                                <tr>
                                    <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="p-4 bg-gray-100 rounded-full mb-3">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </div>
                                            <p class="font-medium text-gray-900">Tidak Ada Data Teknisi/PIC</p>
                                            <p class="text-sm">Belum ada teknisi atau PIC yang terdaftar di sistem.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Performance & Produktivitas Teknisi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <h3 class="text-lg font-bold mb-4">Ringkasan Pekerjaan (All Time)</h3>
                    
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th rowspan="2" class="px-6 py-3 border-r">Nama Teknisi / PIC</th>
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
                                @foreach($users as $user)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white border-r">
                                        {{ $user->name }}
                                        <div class="text-xs text-gray-400">{{ ucfirst($user->role) }}</div>
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
                                    <td class="px-6 py-4 text-center border-l font-black text-lg">
                                        {{ $total }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

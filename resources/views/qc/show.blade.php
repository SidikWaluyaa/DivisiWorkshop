<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('QC Inspection: ') . $order->spk_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 dark:text-gray-100">Checklist Quality Control</h3>
                
                <div class="space-y-4 mb-8">
                    <!-- Jahit Sol -->
                    <div class="flex items-center justify-between border-b pb-2 dark:border-gray-700">
                        <span class="text-gray-700 dark:text-gray-300">1. Pengecekan Jahitan Sol (Jika ada)</span>
                        @if($subtasks['jahit']['done'])
                             <div class="text-right">
                                <span class="text-green-600 font-bold">PASSED</span>
                                <div class="text-xs text-gray-500 mt-1">
                                    Mulai: {{ $subtasks['jahit']['start']->format('H:i') }} |
                                    Selesai: {{ $subtasks['jahit']['end']->format('H:i') }} |
                                    Durasi: {{ $subtasks['jahit']['duration'] }} m
                                </div>
                            </div>
                        @else
                            <form action="{{ route('qc.update', $order->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="jahit">
                                <div class="mb-2">
                                    <select name="worker_id" class="text-sm border-gray-300 rounded dark:bg-gray-900 w-full" required>
                                        <option value="">-- Pilih Teknisi (Jahit) --</option>
                                        @foreach($techJahit as $tech)
                                            <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="text-sm bg-gray-200 hover:bg-green-200 text-gray-800 px-3 py-1 rounded">Mark PASS</button>
                                <div class="text-xs text-gray-400 mt-1 text-right">Mulai: {{ $subtasks['jahit']['start']->format('H:i') }}</div>
                            </form>
                        @endif
                    </div>

                    <!-- Clean Up -->
                    <div class="flex items-center justify-between border-b pb-2 dark:border-gray-700">
                        <span class="text-gray-700 dark:text-gray-300">2. Kebersihan / Clean Up Detail</span>
                        @if($subtasks['clean_up']['done'])
                             <div class="text-right">
                                <span class="text-green-600 font-bold">PASSED</span>
                                <div class="text-xs text-gray-500 mt-1">
                                    Mulai: {{ $subtasks['clean_up']['start']->format('H:i') }} |
                                    Selesai: {{ $subtasks['clean_up']['end']->format('H:i') }} |
                                    Durasi: {{ $subtasks['clean_up']['duration'] }} m
                                </div>
                            </div>
                        @else
                            <form action="{{ route('qc.update', $order->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="clean_up">
                                <div class="mb-2">
                                    <select name="worker_id" class="text-sm border-gray-300 rounded dark:bg-gray-900 w-full" required>
                                        <option value="">-- Pilih Teknisi (Clean Up) --</option>
                                        @foreach($techCleanup as $tech)
                                            <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="text-sm bg-gray-200 hover:bg-green-200 text-gray-800 px-3 py-1 rounded">Mark PASS</button>
                                <div class="text-xs text-gray-400 mt-1 text-right">Mulai: {{ $subtasks['clean_up']['start']->format('H:i') }}</div>
                            </form>
                        @endif
                    </div>

                    <!-- Final Check -->
                    <div class="flex items-center justify-between border-b pb-2 dark:border-gray-700">
                        <span class="text-gray-700 dark:text-gray-300">3. QC Akhir (Keseluruhan)</span>
                        @if($subtasks['final']['done'])
                             <div class="text-right">
                                <span class="text-green-600 font-bold">PASSED</span>
                                <div class="text-xs text-gray-500 mt-1">
                                    Mulai: {{ $subtasks['final']['start']->format('H:i') }} |
                                    Selesai: {{ $subtasks['final']['end']->format('H:i') }} |
                                    Durasi: {{ $subtasks['final']['duration'] }} m
                                </div>
                            </div>
                        @else
                            <form action="{{ route('qc.update', $order->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="final">
                                <div class="mb-2">
                                    <label class="block text-xs text-gray-500 mb-1">PIC QC Akhir</label>
                                    <select name="worker_id" class="text-sm border-gray-300 rounded dark:bg-gray-900 w-full" required>
                                        <option value="">-- Pilih PIC (Final) --</option>
                                        @foreach($techFinal as $tech)
                                            <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="text-sm bg-gray-200 hover:bg-green-200 text-gray-800 px-3 py-1 rounded">Mark PASS</button>
                                <div class="text-xs text-gray-400 mt-1 text-right">Mulai: {{ $subtasks['final']['start']->format('H:i') }}</div>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Final Decision Buttons -->
                <div class="flex justify-between items-end border-t pt-6 dark:border-gray-700">
                    
                    <!-- Fail / Reject -->
                    <form action="{{ route('qc.fail', $order->id) }}" method="POST" class="w-1/3">
                        @csrf
                        <label class="block text-sm text-red-600 mb-1">Alasan Reject:</label>
                        <input type="text" name="note" class="w-full text-sm border-red-300 rounded mb-2 dark:bg-gray-900" placeholder="Contoh: Lem kurang rapi" required>
                        <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm w-full font-bold">
                            ❌ REJECT (Ke Produksi)
                        </button>
                    </form>

                    <!-- Pass -->
                    <div class="text-right">
                         @php
                            $allDone = $subtasks['jahit']['done'] && $subtasks['clean_up']['done'] && $subtasks['final']['done'];
                         @endphp
                         @if($allDone)
                            <form action="{{ route('qc.pass', $order->id) }}" method="POST">
                                @csrf
                                <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg text-lg">
                                    ✅ QC LOLOS (SELESAI)
                                </button>
                            </form>
                         @else
                            <button disabled class="bg-gray-300 text-gray-500 px-6 py-3 rounded-lg font-bold cursor-not-allowed">
                                Lengkapi Checklist Dulu
                            </button>
                         @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>

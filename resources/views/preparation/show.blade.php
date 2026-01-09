<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Preparation Details: ') . $order->spk_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-gray-100">Sub-Task Checklist</h3>

                <div class="space-y-6">
                    <!-- Cuci -->
                    <div class="flex items-center justify-between border-b pb-4 dark:border-gray-700">
                        <div>
                            <h4 class="font-bold text-lg dark:text-gray-200">1. P. Cuci (Wajib)</h4>
                            <p class="text-sm text-gray-500">Pembersihan awal sebelum tindakan</p>
                        </div>
                        <div>
                            @if($status['cleaning']['done'] === true)
                                <div class="text-right">
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">SELESAI</span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Datang: {{ $status['cleaning']['start']->format('H:i') }}<br>
                                        Selesai: {{ $status['cleaning']['end']->format('H:i') }}<br>
                                        Durasi: {{ $status['cleaning']['duration'] }} menit
                                    </div>
                                </div>
                            @else
                                <form action="{{ route('preparation.update', $order->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="cleaning">
                                    <div class="mb-2">
                                        <select name="worker_id" class="text-sm border-gray-300 rounded dark:bg-gray-900 w-full" required>
                                            <option value="">-- Pilih Teknisi (Washing) --</option>
                                            @foreach($techWashing as $tech)
                                                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">Mark Done</button>
                                    <div class="text-xs text-gray-400 mt-1 text-right">Datang: {{ $status['cleaning']['start'] ? $status['cleaning']['start']->format('H:i') : '-' }}</div>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Sol -->
                    <div class="flex items-center justify-between border-b pb-4 dark:border-gray-700">
                        <div>
                            <h4 class="font-bold text-lg dark:text-gray-200">2. P. Reparasi Sol</h4>
                            <p class="text-sm text-gray-500">Bongkar sol lama, bersihkan sisa lem</p>
                        </div>
                        <div>
                            @if($status['sol'] === 'SKIP')
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">SKIP (Not Required)</span>
                            @elseif($status['sol']['done'] === true)
                                <div class="text-right">
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">SELESAI</span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Datang: {{ $status['sol']['start']->format('H:i') }}<br>
                                        Selesai: {{ $status['sol']['end']->format('H:i') }}<br>
                                        Durasi: {{ $status['sol']['duration'] }} menit
                                    </div>
                                </div>
                            @else
                                <form action="{{ route('preparation.update', $order->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="sol">
                                    <div class="mb-2">
                                        <select name="worker_id" class="text-sm border-gray-300 rounded dark:bg-gray-900 w-full" required>
                                            <option value="">-- Pilih Teknisi (Sol) --</option>
                                            @foreach($techSol as $tech)
                                                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">Mark Done</button>
                                    <div class="text-xs text-gray-400 mt-1 text-right">Datang: {{ $status['sol']['start']->format('H:i') }}</div>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Upper -->
                    <div class="flex items-center justify-between border-b pb-4 dark:border-gray-700">
                        <div>
                            <h4 class="font-bold text-lg dark:text-gray-200">3. P. Reparasi Upper</h4>
                            <p class="text-sm text-gray-500">Acetone, amplas, masking</p>
                        </div>
                        <div>
                            @if($status['upper'] === 'SKIP')
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">SKIP (Not Required)</span>
                            @elseif($status['upper']['done'] === true)
                                <div class="text-right">
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">SELESAI</span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Datang: {{ $status['upper']['start']->format('H:i') }}<br>
                                        Selesai: {{ $status['upper']['end']->format('H:i') }}<br>
                                        Durasi: {{ $status['upper']['duration'] }} menit
                                    </div>
                                </div>
                            @else
                                <form action="{{ route('preparation.update', $order->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="upper">
                                    <div class="mb-2">
                                        <select name="worker_id" class="text-sm border-gray-300 rounded dark:bg-gray-900 w-full" required>
                                            <option value="">-- Pilih Teknisi (Upper) --</option>
                                            @foreach($techUpper as $tech)
                                                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">Mark Done</button>
                                    <div class="text-xs text-gray-400 mt-1 text-right">Datang: {{ $status['upper']['start']->format('H:i') }}</div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    @if($canFinish)
                        <form action="{{ route('preparation.finish', $order->id) }}" method="POST">
                            @csrf
                            <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg">
                                PREPARATION SELESAI →
                            </button>
                        </form>
                    @else
                        <button disabled class="bg-gray-300 text-gray-500 px-6 py-3 rounded-lg font-bold cursor-not-allowed">
                            Lengkapi Semua Task
                        </button>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Station: Production Implementation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Queue -->
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Antrian Produksi</h2>
                    <p class="text-sm text-gray-500">Material sudah ready. Silakan ambil pekerjaan.</p>
                </header>

                <div class="space-y-4">
                    @forelse($queue as $order)
                    <div class="border p-4 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-lg">{{ $order->spk_number }}</span>
                            @if(isset($order->is_revisi) && $order->is_revisi)
                                <span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded font-bold border border-red-500 animate-pulse">⚠️ REVISI (QC REJECT)</span>
                            @else
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Material OK</span>
                            @endif
                        </div>
                        <div class="text-sm mb-3">
                             {{ $order->shoe_brand }} - {{ $order->shoe_color }}
                             <br>
                             <span class="text-gray-500">
                                 Notes: 
                                 @foreach($order->services as $s) {{ $s->name }}, @endforeach
                             </span>
                        </div>
                        
                        <form action="{{ route('production.start', $order->id) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Pilih Teknisi:</label>
                                <select name="technician_id" class="w-full text-sm border-gray-300 rounded dark:bg-gray-900" required>
                                    <option value="">-- Pilih Teknisi --</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="w-full bg-indigo-600 text-white py-2 rounded shadow hover:bg-indigo-700 font-bold">
                                AMBIL KERJAAN (START)
                            </button>
                        </form>
                    </div>
                    @empty
                    <p class="text-gray-500 italic">Tidak ada antrian produksi.</p>
                    @endforelse
                </div>
            </div>

            <!-- In Progress -->
            <div class="p-6 bg-blue-50 dark:bg-gray-900 border border-blue-100 dark:border-gray-700 shadow sm:rounded-lg">
                <header class="mb-4">
                    <h2 class="text-lg font-bold text-blue-900 dark:text-blue-100 mb-1">Sedang Dikerjakan</h2>
                    <p class="text-sm text-blue-600 dark:text-blue-300">Fokus pengerjaan sesuai SOP.</p>
                </header>

                <div class="space-y-4">
                    @forelse($inProgress as $order)
                    <div class="border p-4 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-lg">
                                {{ $order->spk_number }}
                                @if(isset($order->is_revisi) && $order->is_revisi)
                                    <span class="ml-2 text-[10px] bg-red-100 text-red-800 px-1 rounded border border-red-500">REVISI</span>
                                @endif
                            </span>
                            <span class="text-xs text-gray-400">Dimulai: {{ $order->taken_date->format('H:i') }}</span>
                        </div>
                        <div class="text-sm mb-4">
                             {{ $order->shoe_brand }}
                             <ul class="list-disc list-inside mt-2 text-gray-600 dark:text-gray-400">
                                 @foreach($order->services as $s) 
                                    <li>{{ $s->name }}</li>
                                 @endforeach
                             </ul>
                        </div>
                        
                        <form action="{{ route('production.finish', $order->id) }}" method="POST">
                            @csrf
                            <button class="w-full bg-green-600 text-white py-2 rounded shadow hover:bg-green-700 font-bold">
                                SELESAI & KIRIM KE QC
                            </button>
                        </form>
                    </div>
                    @empty
                    <p class="text-gray-500 italic">Belum ada yang dikerjakan.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

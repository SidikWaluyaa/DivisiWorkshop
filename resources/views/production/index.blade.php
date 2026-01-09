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
                            <div class="space-y-3 mb-4">
                                @if(isset($order->groupedServices))
                                    @foreach($order->groupedServices as $category => $services)
                                        <div class="p-2 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700">
                                            <div class="text-xs font-bold text-gray-500 uppercase mb-1">{{ $category }}</div>
                                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                                @foreach($services as $s)
                                                    • {{ $s->name }}<br>
                                                @endforeach
                                            </div>
                                            <label class="block text-xs text-gray-400 mb-1">Assigned Technician:</label>
                                            <select name="assignments[{{ $category }}]" class="w-full text-sm border-gray-300 rounded dark:bg-gray-900 leading-tight py-1" required>
                                                <option value="">-- Pilih Teknisi {{ $category }} --</option>
                                                {{-- Filter Technicians --}}
                                                @php
                                                    $filteredTechs = $techsByCategory[$category] ?? ($techsByCategory['General'] ?? $allTechnicians);
                                                @endphp
                                                @foreach($filteredTechs as $tech)
                                                    <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-red-500 text-xs">Error loading services.</p>
                                @endif
                            </div>
                            <button class="w-full bg-indigo-600 text-white py-2 rounded shadow hover:bg-indigo-700 font-bold text-sm">
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
                    <p class="text-sm text-blue-600 dark:text-blue-300">Update status per layanan.</p>
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
                             <div class="font-semibold mb-2">{{ $order->shoe_brand }}</div>
                             
                             <div class="space-y-2 mt-2">
                                 @foreach($order->services as $s)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded border {{ $s->pivot->status === 'DONE' ? 'border-green-200 bg-green-50' : 'border-gray-200' }}">
                                        <div>
                                            <div class="px-2 py-0.5 rounded text-[10px] font-bold inline-block mb-1 {{ $s->pivot->status === 'DONE' ? 'bg-green-200 text-green-800' : ($s->pivot->status === 'REVISI' ? 'bg-red-200 text-red-800 animate-pulse' : 'bg-gray-200 text-gray-600') }}">
                                                {{ $s->pivot->status ?? 'IN PROGRESS' }}
                                            </div>
                                            <div class="text-gray-800 dark:text-gray-200">{{ $s->name }}</div>
                                            @if($s->tech_name !== '-')
                                                <div class="text-xs text-gray-500">Tech: {{ $s->tech_name }}</div>
                                            @endif
                                        </div>
                                        
                                        @if($s->pivot->status !== 'DONE')
                                            <form action="{{ route('production.update-service', ['id' => $order->id, 'serviceId' => $s->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded shadow-sm">
                                                    Mark Done
                                                </button>
                                            </form>
                                        @else
                                            <div class="flex flex-col items-end">
                                                <span class="text-green-600 font-bold text-lg">✓</span>
                                                <span class="text-[10px] text-gray-500">Selesai: {{ $s->pivot->updated_at->format('H:i') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                 @endforeach
                             </div>
                        </div>
                        
                        <form action="{{ route('production.finish', $order->id) }}" method="POST" class="mt-4">
                            @csrf
                            <button class="w-full py-2 rounded shadow font-bold {{ $order->all_services_done ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}"
                                {{ !$order->all_services_done ? 'disabled' : '' }}
                                title="{{ !$order->all_services_done ? 'Selesaikan semua service terlebih dahulu' : 'Kirim ke QC' }}">
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

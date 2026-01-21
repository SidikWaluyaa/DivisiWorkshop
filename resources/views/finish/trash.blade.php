<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Sampah History & Order') }}
            </h2>
            <a href="{{ route('finish.index') }}" class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm font-bold shadow-md transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700 relative">
                <header class="px-6 py-4 bg-red-50 dark:bg-red-900/20 border-b border-red-100 dark:border-red-900/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-red-800 dark:text-red-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Tong Sampah (Soft Deleted)
                        </h3>
                        <p class="text-sm text-red-600 dark:text-red-300 mt-1">
                            Data di sini bisa <b>dikembalikan</b> atau <b>dihapus permanen</b>.
                        </p>
                    </div>
                    
                    <!-- Bulk Actions Toolbar (Hidden by default) -->
                    <div id="bulk-actions" class="hidden flex items-center gap-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400 mr-2"><span id="selected-count">0</span> terpilih</span>
                        
                        <form id="bulk-restore-form" action="{{ route('finish.bulk-restore') }}" method="POST">
                            @csrf
                            <input type="hidden" name="ids[]" class="bulk-ids-input">
                            <button type="button" onclick="submitBulkRestore()" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-bold transition-colors flex items-center gap-1 shadow-sm">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Restore Terpilih
                            </button>
                        </form>

                        <form id="bulk-force-delete-form" action="{{ route('finish.bulk-force-delete') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="ids[]" class="bulk-ids-input">
                            <button type="button" onclick="submitBulkForceDelete()" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-bold transition-colors flex items-center gap-1 shadow-sm">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus Permanen
                            </button>
                        </form>
                    </div>
                </header>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3 w-4">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </th>
                                <th class="px-6 py-3">Waktu Hapus</th>
                                <th class="px-6 py-3">SPK & Pelanggan</th>
                                <th class="px-6 py-3">Item Sepatu</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($deletedOrders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="ids[]" value="{{ $order->id }}" class="item-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-mono text-gray-500">
                                        {{ $order->deleted_at->format('d M Y, H:i') }}
                                        <br>
                                        ({{ $order->deleted_at->diffForHumans() }})
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $order->spk_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->customer_name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-700 dark:text-gray-300">{{ $order->shoe_brand }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->shoe_color }}</div>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <!-- Restore -->
                                    <form action="{{ route('finish.restore', $order->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-green-100 text-green-700 hover:bg-green-200 rounded text-xs font-bold transition-colors flex items-center gap-1 inline-flex">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            Restore
                                        </button>
                                    </form>

                                    <!-- Force Delete -->
                                    <form action="{{ route('finish.force-delete', $order->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-confirm px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 rounded text-xs font-bold transition-colors flex items-center gap-1 inline-flex"
                                                data-title="Hapus Permanen?" 
                                                data-text="Data ini akan hilang SELAMANYA dan tidak bisa dikembalikan!"
                                                data-confirm="Hapus Selamanya!">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus Permanen
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Tong sampah kosong.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');
            
            // Toggle all
            if(selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    itemCheckboxes.forEach(cb => cb.checked = this.checked);
                    updateBulkActions();
                });
            }

            // Individual toggle
            itemCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkActions);
            });

            function updateBulkActions() {
                const checked = document.querySelectorAll('.item-checkbox:checked');
                const count = checked.length;
                
                selectedCount.textContent = count;
                
                if (count > 0) {
                    bulkActions.classList.remove('hidden');
                } else {
                    bulkActions.classList.add('hidden');
                }

                // Update input values on forms
                // We'll gather IDs when submitting instead to be safe, 
                // but let's just make sure we know something is selected.
            }
        });

        function submitBulkRestore() {
            if (!confirm('Apakah Anda yakin ingin mengembalikan data yang dipilih?')) return;
            
            submitBulkForm('bulk-restore-form');
        }

        function submitBulkForceDelete() {
            if (!confirm('PERINGATAN: Data yang dipilih akan dihapus PERMANEN dan TIDAK BISA KEMBALI. Yakin hapus?')) return;
            
            submitBulkForm('bulk-force-delete-form');
        }

        function submitBulkForm(formId) {
            const form = document.getElementById(formId);
            const checked = document.querySelectorAll('.item-checkbox:checked');
            const ids = Array.from(checked).map(cb => cb.value);

            // Clear existing inputs
            const existingInputs = form.querySelectorAll('input[name="ids[]"]');
            existingInputs.forEach(el => el.remove());

            // Add new inputs
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });

            form.submit();
        }
    </script>
</x-app-layout>

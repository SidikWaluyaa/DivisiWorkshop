<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Tools') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold text-red-600 mb-4">⚠️ Danger Zone</h3>
                    
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    Tindakan ini akan <strong>MENGHAPUS SEMUA DATA TRANSAKSI</strong> (Order, Logging, Keluhan).
                                    <br>
                                    Master Data seperti User, Layanan, dan Material <strong>TIDAK AKAN</strong> dihapus.
                                    <br>
                                    Data yang sudah dihapus tidak dapat dikembalikan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form id="reset-form" action="{{ route('admin.system.reset') }}" method="POST">
                        @csrf
                        <button type="button" onclick="confirmReset()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transform transition hover:scale-105 flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            RESET DATA TRANSAKSI
                        </button>
                    </form>

                    <script>
                        function confirmReset() {
                            Swal.fire({
                                title: 'ANDA YAKIN?',
                                text: "Seluruh data transaksi akan DIHAPUS PERMANEN! Tindakan ini tidak dapat dibatalkan.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Ya, Hapus Semuanya!',
                                cancelButtonText: 'Batal',
                                background: '#fff',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Submit form
                                    document.getElementById('reset-form').submit();
                                }
                            })
                        }
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

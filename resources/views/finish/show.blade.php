<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight truncate">
                {{ __('Order Details: ') . $order->spk_number }}
            </h2>
            <a href="{{ route('finish.index') }}" class="shrink-0 px-4 py-2 bg-white/20 hover:bg-white/30 border border-white/50 text-white text-sm font-medium rounded-lg transition-colors shadow-sm flex items-center gap-2 backdrop-blur-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- LEFT COLUMN: Order Info & Actions -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Main Card -->
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden border border-teal-100 dark:border-gray-700">
                        <div class="bg-gradient-to-r from-teal-600 to-orange-500 p-6 text-white text-center sm:text-left relative overflow-hidden">
                             <!-- Decorative Shapes -->
                             <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/10 blur-xl"></div>
                             <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-orange-400/20 blur-xl"></div>

                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center relative z-10 gap-4">
                                <div>
                                    <h3 class="text-4xl font-extrabold mb-1 tracking-tight">{{ $order->customer_name }}</h3>
                                    <p class="text-teal-50 font-medium text-lg flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        {{ $order->customer_phone }}
                                    </p>
                                    @if($order->customer_email)
                                    <p class="text-teal-50/80 font-medium text-sm flex items-center gap-2 mt-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        {{ $order->customer_email }}
                                    </p>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full text-sm sm:text-base font-bold font-mono border border-white/30 tracking-wider shadow-sm whitespace-nowrap inline-block">
                                        {{ $order->spk_number }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                             <div class="flex items-center gap-5 mb-8 border-b border-gray-100 dark:border-gray-700 pb-8">
                                <div class="w-14 h-14 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-2xl shadow-sm border border-orange-200">
                                    👟
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 uppercase tracking-widest font-bold mb-0.5">Detail Sepatu</p>
                                    <h4 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $order->shoe_brand }}</h4>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">{{ $order->shoe_color }}</p>
                                </div>
                             </div>
                             
                             <!-- Action Area -->
                             <div class="bg-orange-50 dark:bg-gray-700/50 rounded-xl p-5 border border-orange-100 dark:border-gray-600">
                                 @if(is_null($order->taken_date))
                                    <div class="flex flex-col gap-3">
                                        <!-- Manual WhatsApp Trigger -->
                                        <!-- Manual Email Trigger -->
                                        <!-- SMTP Email Trigger -->
                                        @if($order->customer_email)
                                        <button onclick="sendFinishEmail('{{ $order->id }}')" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl shadow-md font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-2 transform transition-all hover:-translate-y-0.5 mb-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            <span>Kirim Notifikasi Selesai (Email)</span>
                                        </button>
                                        @else
                                        <button disabled class="w-full bg-gray-400 text-white py-3 rounded-xl shadow-md font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-2 mb-2 cursor-not-allowed" title="Email tidak tersedia">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            <span>Email Tidak Tersedia</span>
                                        </button>
                                        @endif
                                        <form action="{{ route('finish.pickup', $order->id) }}" method="POST">
                                            @csrf
                                            <button class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white py-4 rounded-xl shadow-lg hover:shadow-orange-200 dark:hover:shadow-none font-bold text-base uppercase tracking-widest flex items-center justify-center gap-2 transform transition-all hover:-translate-y-0.5">
                                                <span>✅ Konfirmasi Barang Diambil</span>
                                            </button>
                                        </form>
                                        
                                        <div x-data="{ open: false }" class="text-center pt-2 space-y-3">
                                            @php
                                                $waMessage = "Halo Kak {$order->customer_name}, sepatu {$order->shoe_brand} - {$order->shoe_color} (SPK: {$order->spk_number}) sudah selesai dicuci/diperbaiki.\n\nApakah berminat untuk menambah layanan lain (Upsell) agar sepatu Kakak makin kinclong?";
                                                $waLink = "https://wa.me/" . preg_replace('/^0/', '62', $order->customer_phone) . "?text=" . urlencode($waMessage);
                                            @endphp
                                            
                                            <a href="mailto:{{ $order->customer_email }}?subject=Penawaran Layanan Tambahan (SPK: {{ $order->spk_number }})&body=Halo Kak {{ $order->customer_name }},%0D%0A%0D%0ASepatu {{ $order->shoe_brand }} - {{ $order->shoe_color }} (SPK: {{ $order->spk_number }}) sudah selesai kami proses.%0D%0A%0D%0AApakah berminat untuk menambah layanan lain agar sepatu Kakak makin kinclong?" target="_blank" class="block w-full border border-blue-500 text-blue-600 hover:bg-blue-50 font-bold py-2.5 rounded-lg transition-colors flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                Tawarkan Jasa via Email
                                            </a>

                                            <button @click="open = true" class="text-sm font-medium text-teal-600 hover:text-teal-800 flex items-center justify-center gap-1 mx-auto transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Input Tambah Jasa (System)
                                            </button>

                                            <!-- Modal -->
                                            <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;" x-transition.opacity>
                                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-md transform transition-all scale-100" @click.away="open = false">
                                                    <div class="mb-4">
                                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                                                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                                        </div>
                                                        <h3 class="text-lg font-bold text-center text-gray-900 dark:text-gray-100 mt-4">Tambah Layanan (Upsell)</h3>
                                                        <p class="text-sm text-center text-gray-500 mt-1">
                                                            Order akan dikembalikan ke status <strong>PREPARATION</strong> untuk pengerjaan ulang.
                                                        </p>
                                                    </div>

                                                    <form action="{{ route('finish.add-service', $order->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="mb-6">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pilih Layanan</label>
                                                            <div class="relative">
                                                                <select name="service_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 sm:text-sm py-3" required>
                                                                    <option value="">-- Cari Layanan --</option>
                                                                    @foreach($services as $service)
                                                                        <option value="{{ $service->id }}">{{ $service->name }} (Rp {{ number_format($service->price, 0, ',', '.') }})</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="mb-6">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Foto Kondisi (Opsional)</label>
                                                            <label class="block mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50 transition-colors cursor-pointer bg-white">
                                                                <div class="space-y-1 text-center w-full">
                                                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                                    </svg>
                                                                    <div class="text-sm text-gray-600">
                                                                        <span class="font-medium text-blue-600 hover:text-blue-500">Upload Foto</span>
                                                                        <span class="pl-1">atau drag and drop (Klik disini)</span>
                                                                        <input id="upsell-photo" name="upsell_photo" type="file" class="sr-only" accept="image/*" onchange="document.getElementById('file-chosen').textContent = this.files[0].name">
                                                                    </div>
                                                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                                                    <p id="file-chosen" class="text-xs font-bold text-teal-600 pt-2"></p>
                                                                </div>
                                                            </label>
                                                        </div>

                                                        <div class="grid grid-cols-2 gap-3">
                                                            <button type="button" @click="open = false" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition-colors">Batal</button>
                                                            <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-md transition-colors">Simpan & Proses</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <div class="inline-flex flex-col items-center">
                                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 mb-2">
                                                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <span class="text-lg font-bold text-green-700">SUDAH DIAMBIL</span>
                                            <p class="text-sm text-gray-500 mt-1">Pada: {{ $order->taken_date->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                @endif
                             </div>
                        </div>
                    </div>

                    <!-- Final Documentation -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span class="w-1 h-6 bg-orange-500 rounded-full"></span>
                            Dokumentasi Final
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 dark:border-gray-600 dark:bg-gray-700">
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase block mb-2">📸 Kondisi Diterima (Before)</span>
                                <x-photo-uploader :order="$order" step="FINISH_BEFORE" />
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 dark:border-gray-600 dark:bg-gray-700">
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase block mb-2">✨ Siap Diambil (After)</span>
                                <x-photo-uploader :order="$order" step="FINISH_AFTER" />
                            </div>
                        </div>
                    </div>

                    <!-- Services List -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span class="w-1 h-6 bg-teal-500 rounded-full"></span>
                            Layanan yang Dikerjakan
                        </h3>
                        <div class="space-y-3">
                            @foreach($order->services as $service)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="font-medium text-gray-700 dark:text-gray-200">{{ $service->name }}</span>
                                <span class="text-sm font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                        
                        @if($order->notes)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">Catatan Order</h4>
                            <p class="text-gray-600 italic bg-yellow-50 p-3 rounded-lg border border-yellow-100">{{ $order->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- RIGHT COLUMN: Timeline & Team -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-100 dark:border-gray-700 h-full">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center gap-2">
                            <span class="w-1 h-6 bg-indigo-500 rounded-full"></span>
                            Tim Eksekusi
                        </h3>

                        <div class="relative border-l-2 border-gray-200 ml-3 space-y-8">
                            <!-- Sortir -->
                            <div class="relative pl-8">
                                <span class="absolute -left-[9px] top-0 bg-white dark:bg-gray-800 w-4 h-4 rounded-full border-2 border-indigo-500"></span>
                                <h4 class="font-bold text-gray-800 dark:text-gray-100">Sortir Material</h4>
                                <div class="mt-2 space-y-2">
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">PIC Sol</span> 
                                        <span class="font-medium">{{ $order->picSortirSol->name ?? '-' }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">PIC Upper</span>
                                        <span class="font-medium">{{ $order->picSortirUpper->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Production -->
                            <div class="relative pl-8">
                                <span class="absolute -left-[9px] top-0 bg-white dark:bg-gray-800 w-4 h-4 rounded-full border-2 border-blue-500"></span>
                                <h4 class="font-bold text-gray-800 dark:text-gray-100">Production</h4>
                                <div class="mt-2 space-y-2">
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">Sol</span>
                                        <span class="font-medium">{{ $order->prodSolBy->name ?? '-' }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">Upper</span>
                                        <span class="font-medium">{{ $order->prodUpperBy->name ?? '-' }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">Cleaning/Repaint</span>
                                        <span class="font-medium">{{ $order->prodCleaningBy->name ?? '-' }}</span>
                                    </div>
                                    {{-- Fallback for legacy data --}}
                                    @if(!$order->prodSolBy && !$order->prodUpperBy && !$order->prodCleaningBy && $order->technicianProduction)
                                        <div class="text-sm pt-2 border-t border-gray-100">
                                            <span class="text-gray-400 block text-xs">Teknisi (Legacy)</span>
                                            <span class="font-medium">{{ $order->technicianProduction->name ?? '-' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- QC -->
                            <div class="relative pl-8">
                                <span class="absolute -left-[9px] top-0 bg-white dark:bg-gray-800 w-4 h-4 rounded-full border-2 border-green-500"></span>
                                <h4 class="font-bold text-gray-800 dark:text-gray-100">Quality Control</h4>
                                <div class="mt-2 space-y-3 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Jahit</span>
                                        <span class="font-medium">{{ $order->qcJahitBy->name ?? $order->qcJahitTechnician->name ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Cleanup</span>
                                        <span class="font-medium">{{ $order->qcCleanupBy->name ?? $order->qcCleanupTechnician->name ?? '-' }}</span>
                                    </div>
                                    <div class="border-t border-gray-200 my-1"></div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 font-bold">FINAL CHECK</span>
                                        <span class="font-bold text-green-600">{{ $order->qcFinalBy->name ?? $order->qcFinalPic->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<script>
    function sendFinishEmail(id) {
        Swal.fire({
            title: 'Kirim Notifikasi Selesai?',
            text: "Sistem akan mengirimkan email notifikasi selesai ke customer.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Kirim!',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/finish/${id}/send-email`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.statusText)
                    }
                    return response.json()
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Request failed: ${error}`
                    )
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                if(result.value.success) {
                    Swal.fire({
                        title: 'Terkirim!',
                        text: result.value.message,
                        icon: 'success'
                    })
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: result.value.message,
                        icon: 'error'
                    })
                }
            }
        })
    }
</script>

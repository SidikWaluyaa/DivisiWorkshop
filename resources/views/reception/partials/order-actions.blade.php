@props(['order'])

<!-- Hidden Forms -->
<form id="process-{{ $order->id }}" action="{{ route('reception.process', $order->id) }}" method="POST" class="hidden">
    @csrf
</form>

<form id="wa-{{ $order->id }}" action="{{ route('orders.whatsapp_send', $order->id) }}" method="POST" class="hidden" target="_blank">
    @csrf
    <input type="hidden" name="type" value="received">
</form>

<!-- Photo Modal -->
<div x-data="{ open: false }" @open-photo-modal-{{ $order->id }}.window="open = true">
    <template x-teleport="body">
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-show="open" style="display: none;">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg" @click.away="open = false">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Dokumentasi Foto - {{ $order->spk_number }}</h3>
                                    <div class="mt-2 text-left">
                                        <p class="text-sm text-gray-500 mb-4">Upload foto kondisi awal sepatu sebelum diproses.</p>
                                        
                                        <x-photo-uploader :order="$order" step="RECEIVING" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" @click="open = false">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

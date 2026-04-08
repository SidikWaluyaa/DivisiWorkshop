<x-app-layout>
    <div class="px-4 py-8 max-w-7xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Konfirmasi After Service</h1>
                <p class="text-sm text-gray-500 font-medium tracking-tight">Monitoring kepuasan pelanggan secara real-time.</p>
            </div>
        </div>

        @livewire('cx.after-confirmation-list')
    </div>
</x-app-layout>

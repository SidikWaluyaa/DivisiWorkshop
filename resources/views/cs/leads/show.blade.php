<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xs font-black text-white/70 uppercase tracking-[0.3em]">Lead Management Console</h2>
    </x-slot>

    @livewire('cs.lead-detail-manager', ['lead' => $lead])

    @push('scripts')
    <script>
        window.addEventListener('notify', event => {
            const data = event.detail[0];
            // Use existing toastr or Swal if available, or simple alert
            if (window.toastr) {
                toastr[data.type](data.message);
            } else if (window.Swal) {
                Swal.fire({
                    icon: data.type,
                    title: data.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                alert(data.message);
            }
        });
    </script>
    @endpush
</x-app-layout>

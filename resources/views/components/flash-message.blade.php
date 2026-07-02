<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dynamic Dark Mode Detection
        const isDarkMode = document.documentElement.classList.contains('dark') || 
                           (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        const swalBg = isDarkMode ? '#1f2937' : '#ffffff'; // gray-800 vs white
        const swalColor = isDarkMode ? '#f3f4f6' : '#111827'; // gray-100 vs gray-900

        // Success Message
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                toast: false,
                position: 'center',
                background: swalBg,
                color: swalColor,
                iconColor: '#1B8A68', // Premium workshop green
            });
        @endif

        // Error Message
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                showConfirmButton: true, // Let them click ok
                confirmButtonColor: '#EF4444', // Tailwind Red-500
                background: swalBg,
                color: swalColor,
            });
        @endif

        // Warning Message
        @if (session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: "{{ session('warning') }}",
                confirmButtonColor: '#F59E0B', // Tailwind Yellow-500
                background: swalBg,
                color: swalColor,
            });
        @endif
        
        // Validation Errors
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Data Tidak Valid',
                html: `
                    <div class="text-left mt-2 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-red-500 font-medium">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                `,
                confirmButtonColor: '#EF4444',
                confirmButtonText: 'Tutup',
                background: swalBg,
                color: swalColor,
            });
        @endif
    });

    // Global Listener for Delete/Action Confirmation
    document.addEventListener('click', function(e) {
        // Check if the clicked element or its parent has the class 'delete-confirm'
        let target = e.target.closest('.delete-confirm');
        
        if (target) {
            e.preventDefault(); // Stop form submission/link navigation
            
            // Get custom title/text from data attributes, or use defaults
            let title = target.dataset.title || 'Apakah Anda yakin?';
            let text = target.dataset.text || 'Data yang dihapus tidak dapat dikembalikan.';
            let confirmText = target.dataset.confirm || 'Ya, Hapus!';
            let cancelText = target.dataset.cancel || 'Batal';
            
            const isDarkMode = document.documentElement.classList.contains('dark') || 
                               (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
            const swalBg = isDarkMode ? '#1f2937' : '#ffffff';
            const swalColor = isDarkMode ? '#f3f4f6' : '#111827';
            
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444', // Red
                cancelButtonColor: '#6B7280', // Gray
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                background: swalBg,
                color: swalColor,
            }).then((result) => {
                if (result.isConfirmed) {
                    // If it's inside a form, submit the form
                    if (target.form) {
                        target.form.submit();
                    } else if (target.type === 'submit' && target.closest('form')) {
                        target.closest('form').submit();
                    }
                    // If it's a link (<a>), follow the href
                    else if (target.tagName === 'A') {
                        window.location.href = target.href;
                    }
                }
            });
        }
    });

    // Livewire Notify Listener
    document.addEventListener('livewire:init', () => {
        Livewire.on('notify', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            const isDarkMode = document.documentElement.classList.contains('dark') || 
                               (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
            Swal.fire({
                icon: data.type || 'success',
                title: data.type === 'success' ? 'Berhasil!' : 'Informasi',
                text: data.message,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                toast: false,
                position: 'center',
                background: isDarkMode ? '#1f2937' : '#ffffff',
                color: isDarkMode ? '#f3f4f6' : '#111827',
                iconColor: data.type === 'success' ? '#1B8A68' : undefined
            });
        });

        Livewire.on('swal:toast', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            const isDarkMode = document.documentElement.classList.contains('dark') || 
                               (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
            Swal.fire({
                icon: data.icon || 'success',
                title: data.title || 'Berhasil!',
                text: data.text || '',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                toast: false,
                position: 'center',
                background: isDarkMode ? '#1f2937' : '#ffffff',
                color: isDarkMode ? '#f3f4f6' : '#111827',
                iconColor: (data.icon || 'success') === 'success' ? '#1B8A68' : undefined
            });
        });
    });</script>

<style>
    /* Optional: Custom Toast Style adjustments if needed */
    .colored-toast.swal2-icon-success {
        background-color: #ffffff !important;
    }
</style>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Success Message
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end',
                background: '#fff',
                iconColor: '#10B981', // Tailwind Green-500
                customClass: {
                    popup: 'colored-toast'
                }
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
            });
        @endif

        // Warning Message
        @if (session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: "{{ session('warning') }}",
                confirmButtonColor: '#F59E0B', // Tailwind Yellow-500
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
                                <li class="text-red-600 font-medium">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                `,
                confirmButtonColor: '#EF4444',
                confirmButtonText: 'Tutup'
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
            
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444', // Red
                cancelButtonColor: '#6B7280', // Gray
                confirmButtonText: confirmText,
                cancelButtonText: cancelText
            }).then((result) => {
                if (result.isConfirmed) {
                    // If it's a submit button inside a form, submit the form
                    if (target.type === 'submit' && target.form) {
                        target.form.submit();
                    } 
                    // If it's a link (<a>), follow the href
                    else if (target.tagName === 'A') {
                        window.location.href = target.href;
                    }
                }
            });
        }
    });
</script>

<style>
    /* Optional: Custom Toast Style adjustments if needed */
    .colored-toast.swal2-icon-success {
        background-color: #ffffff !important;
    }
</style>

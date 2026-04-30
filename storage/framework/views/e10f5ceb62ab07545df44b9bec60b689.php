<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Success Message
        <?php if(session('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "<?php echo e(session('success')); ?>",
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
        <?php endif; ?>

        // Error Message
        <?php if(session('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "<?php echo e(session('error')); ?>",
                showConfirmButton: true, // Let them click ok
                confirmButtonColor: '#EF4444', // Tailwind Red-500
            });
        <?php endif; ?>

        // Warning Message
        <?php if(session('warning')): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: "<?php echo e(session('warning')); ?>",
                confirmButtonColor: '#F59E0B', // Tailwind Yellow-500
            });
        <?php endif; ?>
        
        // Validation Errors
        <?php if($errors->any()): ?>
            Swal.fire({
                icon: 'error',
                title: 'Data Tidak Valid',
                html: `
                    <div class="text-left mt-2 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="text-red-600 font-medium"><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                `,
                confirmButtonColor: '#EF4444',
                confirmButtonText: 'Tutup'
            });
        <?php endif; ?>
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

    // Livewire Notify Listener
    document.addEventListener('livewire:init', () => {
        Livewire.on('notify', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            Swal.fire({
                icon: data.type || 'success',
                title: data.type === 'success' ? 'Berhasil' : 'Informasi',
                text: data.message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end'
            });
        });

        Livewire.on('swal:toast', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            Swal.fire({
                icon: data.icon || 'success',
                title: data.title || 'Berhasil',
                text: data.text || '',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end'
            });
        });
    });
</script>

<style>
    /* Optional: Custom Toast Style adjustments if needed */
    .colored-toast.swal2-icon-success {
        background-color: #ffffff !important;
    }
</style>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\components\flash-message.blade.php ENDPATH**/ ?>
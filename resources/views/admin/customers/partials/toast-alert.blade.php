{{-- Premium SweetAlert2 Toast Notification Helper Component --}}
<style>
    /* Force SweetAlert2 container to be on top of all active modals (z-index > 30000) */
    .swal2-container {
        z-index: 999999 !important;
    }
</style>

<script>
    (function() {
        let SwalToast;

        function getToast() {
            if (SwalToast) return SwalToast;
            if (window.Swal) {
                SwalToast = window.Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-[1.25rem] shadow-2xl border border-gray-100 font-sans p-4 bg-white/95 backdrop-blur-md',
                        title: 'text-sm font-bold text-gray-800',
                        timerProgressBar: 'bg-[#22B086]'
                    },
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', window.Swal.stopTimer);
                        toast.addEventListener('mouseleave', window.Swal.resumeTimer);
                    }
                });
                return SwalToast;
            }
            return null;
        }

        window.showToast = function(message, type = 'success') {
            const toast = getToast();
            if (toast) {
                toast.fire({
                    icon: type,
                    title: message
                });
            } else {
                console.warn('Swal not loaded yet. Queueing message:', message);
                alert(message);
            }
        };

        // Listen for custom event globally
        window.addEventListener('show-toast', function(e) {
            if (e.detail && e.detail.message) {
                window.showToast(e.detail.message, e.detail.type || 'success');
            }
        });
    })();
</script>

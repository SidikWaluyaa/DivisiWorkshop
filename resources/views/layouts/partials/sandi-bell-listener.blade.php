<audio id="bell-sound" src="{{ asset('audio/bell.wav') }}" preload="auto"></audio>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let activeAlertId = null;

    async function checkPickupCalls() {
        if (activeAlertId) return; // Tunggu alert aktif diselesaikan

        try {
            const res = await fetch('{{ route('admin.pickup-calls.check') }}');
            const data = await res.json();

            if (data.status === 'success') {
                activeAlertId = data.id;

                // Mainkan suara bel kustom
                const audio = document.getElementById('bell-sound');
                if (audio) {
                    audio.play().catch(e => console.warn('Autoplay blocked by browser. Click anywhere on page first:', e));
                }

                // Tampilkan popup SweetAlert2
                const photoHtml = data.photo_url 
                    ? `<img src="${data.photo_url}" class="w-full max-h-64 object-cover rounded-2xl mt-3 border border-gray-200 shadow-inner" alt="Foto Sepatu">`
                    : `<div class="w-full h-40 bg-gray-50 flex items-center justify-center rounded-2xl mt-3 border border-dashed border-gray-300 text-gray-400 font-bold">Tidak ada foto sepatu</div>`;

                Swal.fire({
                    title: '📢 PANGGILAN PENGAMBILAN!',
                    html: `
                        <div class="text-left space-y-3 mt-2">
                            <p class="text-sm text-gray-600 font-semibold leading-relaxed">Petugas, mohon ambilkan sepatu dari rak barang jadi untuk pelanggan berikut:</p>
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nomor SPK</p>
                                <p class="text-lg font-black text-indigo-600 tracking-tight">${data.spk_number}</p>
                                
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-3">Nama Pelanggan</p>
                                <p class="text-sm font-extrabold text-gray-800 uppercase tracking-tight">${data.customer_name}</p>
                            </div>
                            ${photoHtml}
                        </div>
                    `,
                    icon: 'warning',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#64748b',
                    allowOutsideClick: true,
                    allowEscapeKey: true,
                    showCloseButton: true
                }).then(async (result) => {
                    // Tandai dibaca di latar belakang saat popup ditutup (baik via tombol Tutup, X, Esc, atau klik di luar)
                    try {
                        const readRes = await fetch(`/admin/pickup-calls/${data.id}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                        const readData = await readRes.json();
                        if (readData.status === 'success') {
                            activeAlertId = null;
                        } else {
                            activeAlertId = null;
                        }
                    } catch (err) {
                        console.error(err);
                        activeAlertId = null;
                    }
                });
            }
        } catch (err) {
            console.warn('Gagal memproses check pickup calls:', err);
        }
    }

    // Polling setiap 10 detik
    setInterval(checkPickupCalls, 10000);
    // Jalankan check pertama setelah 2 detik halaman dimuat
    setTimeout(checkPickupCalls, 2000);
});
</script>

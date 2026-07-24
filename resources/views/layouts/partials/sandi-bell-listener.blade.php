<audio id="bell-sound" src="{{ asset('audio/ambil.aac') }}" preload="auto" loop></audio>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let activeAlertId = null;

    // Autoplay browser audio unlocking
    const unlockAudio = () => {
        const audio = document.getElementById('bell-sound');
        if (audio) {
            audio.play().then(() => {
                audio.pause();
                audio.currentTime = 0;
            }).catch(e => console.log('Audio unlock ignored/failed:', e));
        }
        document.removeEventListener('click', unlockAudio);
        document.removeEventListener('keydown', unlockAudio);
    };
    document.addEventListener('click', unlockAudio, { once: true });
    document.addEventListener('keydown', unlockAudio, { once: true });

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

                // Tentukan class badge untuk status pembayaran
                let badgeClass = 'bg-gray-100 text-gray-700 border border-gray-200';
                if (data.invoice_status === 'Lunas') {
                    badgeClass = 'bg-emerald-100 text-emerald-800 border border-emerald-200';
                } else if (data.invoice_status === 'Belum Bayar') {
                    badgeClass = 'bg-rose-100 text-rose-800 border border-rose-200';
                } else if (data.invoice_status === 'DP') {
                    badgeClass = 'bg-amber-100 text-amber-800 border border-amber-200';
                }

                // Tampilkan foto atau placeholder
                const photoHtml = data.photo_url 
                    ? `<img src="${data.photo_url}" class="w-full h-full min-h-[320px] max-h-[380px] object-cover rounded-2xl border border-gray-200 shadow-md" alt="Foto Sepatu">`
                    : `<div class="w-full h-full min-h-[320px] bg-gray-50 flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-300 text-gray-400 font-bold p-4">
                        <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Tidak ada foto sepatu</span>
                       </div>`;

                // Tampilkan popup SweetAlert2 dengan tata letak split 2 kolom lebar
                Swal.fire({
                    title: '📢 PANGGILAN PENGAMBILAN!',
                    html: `
                        <div class="text-left space-y-3 mt-2">
                            <p class="text-xs text-gray-500 font-semibold leading-relaxed mb-1">Mohon ambilkan sepatu dari rak barang jadi untuk pelanggan berikut:</p>
                            
                            <div class="flex flex-col md:flex-row gap-5 items-stretch">
                                <!-- Kolom Kiri: Foto Sepatu (40%) -->
                                <div class="w-full md:w-[40%] flex flex-col">
                                    ${photoHtml}
                                </div>
                                
                                <!-- Kolom Kanan: Detail Informasi Lengkap (60%) -->
                                <div class="w-full md:w-[60%] flex flex-col justify-start space-y-2.5">
                                    
                                    <!-- SPK & Invoice Info -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-150 shadow-sm">
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Nomor SPK</span>
                                            <span class="text-xs font-black text-indigo-600 tracking-tight block mt-0.5">${data.spk_number}</span>
                                        </div>
                                        <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-150 shadow-sm flex flex-col justify-between">
                                            <div>
                                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Nomor Invoice</span>
                                                <span class="text-xs font-black text-gray-800 tracking-tight block mt-0.5">${data.invoice_number}</span>
                                            </div>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] font-bold ${badgeClass}">${data.invoice_status}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Customer Details -->
                                    <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-150 shadow-sm">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Nama Pelanggan</span>
                                        <span class="text-xs font-extrabold text-gray-800 uppercase block mt-0.5">${data.customer_name}</span>
                                        <span class="text-[10px] font-bold text-gray-500 font-mono block mt-0.5">📞 ${data.customer_phone}</span>
                                    </div>
                                    
                                    <!-- Shoe Specs -->
                                    <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-150 shadow-sm">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Spesifikasi Sepatu</span>
                                        <span class="text-xs font-extrabold text-gray-700 block mt-0.5">${data.shoe_brand} - ${data.shoe_type}</span>
                                        <span class="text-[9px] text-gray-500 block mt-0.5">Warna: <span class="font-bold text-gray-700">${data.shoe_color}</span> | Size: <span class="font-bold text-gray-700">${data.shoe_size}</span></span>
                                    </div>
                                    
                                    <!-- Shelf Location -->
                                    <div class="p-2.5 bg-indigo-50 border border-indigo-100 rounded-xl shadow-sm">
                                        <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest block">📍 Lokasi Rak</span>
                                        <span class="text-xs font-black text-indigo-700 uppercase tracking-tight block mt-0.5">${data.current_location}</span>
                                    </div>
                                    
                                    <!-- Notes -->
                                    <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-150 shadow-sm">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Catatan SPK</span>
                                        <span class="text-[10px] font-semibold text-gray-600 block italic leading-relaxed mt-0.5">"${data.notes}"</span>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    `,
                    icon: 'warning',
                    width: '800px',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#64748b',
                    allowOutsideClick: true,
                    allowEscapeKey: true,
                    showCloseButton: true
                }).then(async (result) => {
                    // Hentikan pemutaran audio
                    const audio = document.getElementById('bell-sound');
                    if (audio) {
                        audio.pause();
                        audio.currentTime = 0;
                    }
                    
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

    // Polling setiap 3 detik agar panggillan terdengar seketika
    setInterval(checkPickupCalls, 3000);
    // Jalankan check pertama setelah 1 detik halaman dimuat
    setTimeout(checkPickupCalls, 1000);
});
</script>

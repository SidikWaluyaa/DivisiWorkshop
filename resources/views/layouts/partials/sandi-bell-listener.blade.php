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
                    audio.play().catch(e => console.warn('Autoplay blocked by browser:', e));
                }

                // Tentukan class badge untuk status pembayaran
                let badgeClass = 'bg-gray-100 text-gray-700 border border-gray-200';
                if (data.invoice_status === 'Lunas') {
                    badgeClass = 'bg-emerald-100/80 text-emerald-800 border border-emerald-200';
                } else if (data.invoice_status === 'Belum Bayar') {
                    badgeClass = 'bg-rose-100/80 text-rose-800 border border-rose-200';
                } else if (data.invoice_status === 'DP' || data.invoice_status === 'DP/Cicil') {
                    badgeClass = 'bg-amber-100/80 text-amber-800 border border-amber-200';
                }

                // Tentukan lokasi rak penyimpanan jika ada (bedakan Inbound, Aksesoris, Finish)
                let rackHtml = '';
                if (data.rack_inbound || data.rack_finish || data.rack_accessories) {
                    let rackItemsHtml = '';
                    if (data.rack_inbound) {
                        rackItemsHtml += `
                            <div class="p-2.5 bg-white rounded-xl border border-orange-200 text-center flex-1 shadow-sm">
                                <span class="text-[8px] font-bold text-orange-400 uppercase tracking-widest block">Inbound</span>
                                <span class="text-xs font-black text-orange-600 block mt-1 font-mono tracking-tight">${data.rack_inbound}</span>
                            </div>
                        `;
                    }
                    if (data.rack_accessories) {
                        rackItemsHtml += `
                            <div class="p-2.5 bg-white rounded-xl border border-emerald-200 text-center flex-1 shadow-sm">
                                <span class="text-[8px] font-bold text-emerald-400 uppercase tracking-widest block">Aksesoris</span>
                                <span class="text-xs font-black text-emerald-600 block mt-1 font-mono tracking-tight">${data.rack_accessories}</span>
                            </div>
                        `;
                    }
                    if (data.rack_finish) {
                        rackItemsHtml += `
                            <div class="p-2.5 bg-white rounded-xl border border-indigo-200 text-center flex-1 shadow-sm">
                                <span class="text-[8px] font-bold text-indigo-400 uppercase tracking-widest block">Finish</span>
                                <span class="text-xs font-black text-indigo-600 block mt-1 font-mono tracking-tight">${data.rack_finish}</span>
                            </div>
                        `;
                    }

                    rackHtml = `
                        <div class="p-3.5 bg-indigo-50/40 border border-indigo-100/50 rounded-2xl shadow-inner space-y-2 w-full">
                            <span class="text-[8px] font-bold text-indigo-400 uppercase tracking-widest block">📍 Lokasi Slot Gudang</span>
                            <div class="flex gap-3">
                                ${rackItemsHtml}
                            </div>
                        </div>
                    `;
                }

                // Tampilkan foto atau placeholder
                const photoHtml = data.photo_url 
                    ? `<div class="relative w-full h-full min-h-[320px] max-h-[380px] rounded-2xl overflow-hidden shadow-md border border-slate-200/80 bg-slate-50">
                        <img src="${data.photo_url}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" alt="Foto Sepatu">
                       </div>`
                    : `<div class="w-full h-full min-h-[320px] max-h-[380px] bg-slate-50 flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 text-slate-400 font-bold p-6">
                        <svg class="w-12 h-12 text-slate-300 mb-3 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-xs uppercase tracking-wider font-extrabold text-slate-400">Tidak ada foto sepatu</span>
                       </div>`;

                // Tampilkan popup SweetAlert2 dengan tata letak split 2 kolom lebar premium
                Swal.fire({
                    html: `
                        <div class="text-left">
                            <!-- Custom Header -->
                            <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-4">
                                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center animate-bounce shadow-inner">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight leading-none">Panggilan Pengambilan</h3>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                        </span>
                                        <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest leading-none">LIVE NOTIFICATION</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Main Layout -->
                            <div class="flex flex-col md:flex-row gap-5 items-stretch mt-4">
                                <!-- Kolom Kiri: Foto Sepatu (40%) -->
                                <div class="w-full md:w-[40%] flex flex-col justify-stretch">
                                    ${photoHtml}
                                </div>
                                
                                <!-- Kolom Kanan: Detail Informasi Lengkap (60%) -->
                                <div class="w-full md:w-[60%] flex flex-col justify-start space-y-2.5">
                                    
                                    <!-- SPK & Invoice Info -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="p-3 bg-slate-50/60 rounded-2xl border border-slate-100 hover:border-indigo-150 transition-colors shadow-sm">
                                            <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest block">Nomor SPK</span>
                                            <span class="text-xs font-black text-indigo-600 tracking-tight block mt-1 uppercase font-mono">${data.spk_number}</span>
                                        </div>
                                        <div class="p-3 bg-slate-50/60 rounded-2xl border border-slate-100 hover:border-slate-200 transition-colors shadow-sm flex flex-col justify-between">
                                            <div>
                                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest block">Nomor Invoice</span>
                                                <span class="text-xs font-black text-slate-800 tracking-tight block mt-1 uppercase font-mono">${data.invoice_number}</span>
                                            </div>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[8px] font-extrabold uppercase tracking-wider ${badgeClass}">${data.invoice_status}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Customer Details -->
                                    <div class="p-3 bg-slate-50/60 rounded-2xl border border-slate-100 hover:border-slate-200 transition-colors shadow-sm">
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest block">Data Pelanggan</span>
                                        <span class="text-xs font-black text-slate-800 uppercase block mt-1 tracking-tight">${data.customer_name}</span>
                                        <span class="text-[10px] font-bold text-slate-500 font-mono block mt-1 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                            ${data.customer_phone}
                                        </span>
                                    </div>
                                    
                                    <!-- Shoe Specs -->
                                    <div class="p-3 bg-slate-50/60 rounded-2xl border border-slate-100 hover:border-slate-200 transition-colors shadow-sm">
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest block">Detail Sepatu</span>
                                        <span class="text-xs font-black text-slate-800 block mt-1 tracking-tight uppercase">${data.shoe_brand} - ${data.shoe_type}</span>
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <span class="inline-flex items-center px-2 py-0.5 bg-white text-slate-600 border border-slate-200 rounded-md text-[9px] font-bold">Warna: ${data.shoe_color}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 bg-white text-slate-600 border border-slate-200 rounded-md text-[9px] font-bold">Size: ${data.shoe_size}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Racks (Conditional) -->
                                    ${rackHtml}
                                    
                                    <!-- Notes -->
                                    <div class="p-3 bg-slate-50/60 rounded-2xl border border-slate-100 hover:border-slate-200 transition-colors shadow-sm">
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest block">Catatan SPK</span>
                                        <span class="text-[10px] font-semibold text-slate-600 block italic leading-relaxed mt-1">"${data.notes}"</span>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    `,
                    width: '820px',
                    confirmButtonText: 'Tutup Peringatan',
                    allowOutsideClick: true,
                    allowEscapeKey: true,
                    showCloseButton: true,
                    customClass: {
                        popup: 'rounded-[32px] border-0 shadow-2xl p-6 bg-white overflow-hidden',
                        confirmButton: 'px-8 py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-slate-200 transition-all hover:scale-105 active:scale-95 cursor-pointer mt-4'
                    },
                    buttonsStyling: false
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

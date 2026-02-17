<x-app-layout>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content: Complaint Info & Photos -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-teal-500 via-teal-600 to-orange-500 text-white flex justify-between items-center">
                    <h3 class="text-xl font-black flex items-center gap-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Detail Keluhan #{{ $complaint->id }}
                    </h3>
                    <a href="{{ route('admin.complaints.index') }}" class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali
                    </a>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        @php
                            $categoryConfig = [
                                'QUALITY' => ['bg' => 'from-purple-400 to-purple-500', 'icon' => '🔍', 'label' => 'Kualitas'],
                                'DAMAGE' => ['bg' => 'from-red-400 to-red-500', 'icon' => '⚠️', 'label' => 'Kerusakan'],
                                'LATE' => ['bg' => 'from-yellow-400 to-yellow-500', 'icon' => '⏰', 'label' => 'Terlambat'],
                                'SERVICE' => ['bg' => 'from-blue-400 to-blue-500', 'icon' => '💬', 'label' => 'Layanan'],
                                'OTHER' => ['bg' => 'from-gray-400 to-gray-500', 'icon' => '📌', 'label' => 'Lainnya'],
                            ];
                            $catConfig = $categoryConfig[$complaint->category] ?? $categoryConfig['OTHER'];
                        @endphp
                        <div class="bg-gradient-to-br {{ $catConfig['bg'] }} p-4 rounded-xl text-white shadow-lg">
                            <p class="text-xs font-bold uppercase tracking-wider opacity-90 mb-1">Kategori</p>
                            <p class="font-black text-lg flex items-center gap-2">
                                <span class="text-2xl">{{ $catConfig['icon'] }}</span>
                                {{ $catConfig['label'] }}
                            </p>
                        </div>
                        <div class="bg-gradient-to-br from-teal-400 to-teal-500 p-4 rounded-xl text-white shadow-lg">
                            <p class="text-xs font-bold uppercase tracking-wider opacity-90 mb-1">Tanggal Masuk</p>
                            <p class="font-black text-lg">{{ $complaint->created_at->format('d M Y') }}</p>
                            <p class="text-xs opacity-90">{{ $complaint->created_at->format('H:i:s') }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-2">Deskripsi Masalah</p>
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-xl text-gray-800 text-sm whitespace-pre-wrap leading-relaxed border-l-4 border-rose-500 shadow-sm">
                            {{ $complaint->description }}
                        </div>
                    </div>

                    @if($complaint->photos)
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-3">Bukti Foto</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @foreach($complaint->photo_urls as $photoUrl)
                                    <a href="{{ $photoUrl }}" target="_blank" class="block group relative rounded-xl overflow-hidden border-2 border-gray-200 aspect-square hover:border-teal-500 transition-all hover:shadow-lg">
                                        <img src="{{ $photoUrl }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" alt="Bukti Foto">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-3">
                                            <span class="text-white text-xs font-bold">Lihat Foto</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-teal-500 to-orange-500 text-white">
                    <h3 class="text-xl font-black flex items-center gap-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Info Pesanan: {{ $complaint->workOrder->spk_number }}
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-gray-500 text-xs font-bold uppercase mb-1">Pelanggan</p>
                            <p class="font-bold text-gray-900">{{ $complaint->workOrder->customer_name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $complaint->customer_phone }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-gray-500 text-xs font-bold uppercase mb-1">Sepatu</p>
                            <p class="font-bold text-gray-900">{{ $complaint->workOrder->shoe_brand }}</p>
                            <p class="text-xs text-gray-500">{{ $complaint->workOrder->shoe_color }}</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Layanan Dikerjakan</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($complaint->workOrder->services as $service)
                                <span class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-teal-50 to-teal-100 border border-teal-200 rounded-lg text-xs font-bold text-teal-700">
                                    <svg class="h-3 w-3 text-teal-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $service->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Riwayat Teknisi</p>
                        
                        <div class="space-y-2">
                             {{-- Main Technician --}}
                             @if($complaint->workOrder->technicianProduction)
                                <div class="flex items-center bg-gradient-to-r from-orange-50 to-orange-100 p-2 rounded-lg border border-orange-200">
                                    <div class="h-8 w-8 rounded-full bg-orange-500 flex items-center justify-center text-white font-black text-xs mr-3 shadow-sm">
                                        {{ substr($complaint->workOrder->technicianProduction->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="text-xs text-orange-700 font-bold uppercase">Teknisi Utama</p>
                                        <p class="text-sm font-bold text-orange-900">{{ $complaint->workOrder->technicianProduction->name }}</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Sol Technician --}}
                            @if($complaint->workOrder->prodSolBy)
                                <div class="flex items-center bg-gray-50 p-2 rounded-lg border border-gray-200">
                                    <div class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center text-white font-bold text-xs mr-3">
                                        {{ substr($complaint->workOrder->prodSolBy->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-bold uppercase">Sol</p>
                                        <p class="text-sm font-medium text-gray-700">{{ $complaint->workOrder->prodSolBy->name }}</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Upper Technician --}}
                            @if($complaint->workOrder->prodUpperBy)
                                <div class="flex items-center bg-gray-50 p-2 rounded-lg border border-gray-200">
                                    <div class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center text-white font-bold text-xs mr-3">
                                        {{ substr($complaint->workOrder->prodUpperBy->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-bold uppercase">Upper</p>
                                        <p class="text-sm font-medium text-gray-700">{{ $complaint->workOrder->prodUpperBy->name }}</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Cleaning/Treatment Technician --}}
                            @if($complaint->workOrder->prodCleaningBy)
                                <div class="flex items-center bg-gray-50 p-2 rounded-lg border border-gray-200">
                                    <div class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center text-white font-bold text-xs mr-3">
                                        {{ substr($complaint->workOrder->prodCleaningBy->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-bold uppercase">Cleaning/Treatment</p>
                                        <p class="text-sm font-medium text-gray-700">{{ $complaint->workOrder->prodCleaningBy->name }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            @if(!$complaint->workOrder->technicianProduction && !$complaint->workOrder->prodSolBy && !$complaint->workOrder->prodUpperBy && !$complaint->workOrder->prodCleaningBy)
                                <span class="text-sm text-gray-400 italic">Belum ada data teknisi.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar: Actions -->
        <div class="space-y-6">
            <!-- Update Status -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <div class="bg-gradient-to-br from-teal-500 to-orange-500 p-2 rounded-lg">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    Update Status Keluhan
                </h3>
                
                <form action="{{ route('admin.complaints.update', $complaint) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-2 gap-2">
                        @php
                            $statusStyles = [
                                'PENDING' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-300', 'ring' => 'ring-orange-500', 'icon' => '🟠'],
                                'PROCESS' => ['bg' => 'bg-teal-50', 'text' => 'text-teal-700', 'border' => 'border-teal-300', 'ring' => 'ring-teal-500', 'icon' => '🔵'],
                                'RESOLVED' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-300', 'ring' => 'ring-emerald-500', 'icon' => '🟢'],
                                'REJECTED' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-300', 'ring' => 'ring-red-500', 'icon' => '🔴'],
                            ];
                        @endphp
                        @foreach($statusStyles as $status => $style)
                            <label class="cursor-pointer relative">
                                <input type="radio" name="status" value="{{ $status }}" class="peer sr-only" {{ $complaint->status == $status ? 'checked' : '' }}>
                                <div class="px-3 py-2.5 rounded-xl border-2 {{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }} text-xs font-bold text-center peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:{{ $style['ring'] }} transition-all opacity-60 peer-checked:opacity-100 hover:opacity-80 peer-checked:shadow-md flex items-center justify-center gap-1.5">
                                    <span>{{ $style['icon'] }}</span>
                                    {{ $status }}
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5 block">Catatan Admin</label>
                        <textarea name="admin_notes" rows="3" class="w-full text-sm border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-gray-50" placeholder="Tambahkan catatan internal...">{{ $complaint->admin_notes }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-teal-500 to-orange-500 hover:from-teal-600 hover:to-orange-600 text-white rounded-xl text-sm font-bold shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <form action="{{ route('admin.complaints.destroy', $complaint) }}" method="POST" onsubmit="return confirmDelete(event)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2.5 px-4 bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 rounded-xl text-sm font-bold border border-red-200 hover:border-red-300 transition-all flex items-center justify-center gap-2 group">
                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <span>Hapus Keluhan</span>
                        </button>
                    </form>
                </div>
            </div>

            <script>
                function confirmDelete(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Hapus Keluhan?',
                        text: "Data akan dipindahkan ke Sampah. Anda masih bisa memulihkannya nanti.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            e.target.submit();
                        }
                    });
                }
            </script>
            <!-- WhatsApp Reply Section -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-emerald-500 to-teal-500 text-white">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 backdrop-blur-sm p-2.5 rounded-xl">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.438 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884 0 2.225.569 4.315 1.751 6.132l-1.01 3.687 3.748-.984zm11.387-4.605c-.328-.164-1.94-.957-2.24-1.066-.3-.11-.518-.164-.738.164-.219.328-.848 1.066-1.039 1.284-.191.218-.382.245-.71.082-.328-.164-1.386-.511-2.641-1.63-1.054-.939-1.765-2.098-1.972-2.425-.207-.327-.022-.505.142-.667.147-.146.328-.382.492-.573.164-.191.218-.328.328-.546.109-.218.055-.409-.027-.573-.082-.164-.738-1.776-1.011-2.431-.267-.641-.539-.554-.738-.564-.191-.01-.409-.011-.628-.011s-.573.082-.874.409c-.3.327-1.147 1.12-1.147 2.729 0 1.609 1.174 3.166 1.338 3.384.164.218 2.312 3.529 5.599 4.946.782.338 1.392.54 1.868.691.784.248 1.498.213 2.061.129.627-.094 1.94-.792 2.213-1.556.273-.764.273-1.42.191-1.556-.081-.136-.3-.218-.627-.382z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-black tracking-tight">BALAS KELUHAN</h4>
                            <p class="text-[10px] opacity-90 font-medium">Hubungi customer via WhatsApp</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <form action="{{ route('admin.complaints.api_reply', $complaint) }}" method="POST" id="reply_form" class="space-y-6">
                        @csrf
                        
                        <!-- Chat Preview Area -->
                        <div class="bg-white/80 rounded-2xl p-4 border border-gray-100 shadow-inner min-h-[100px] relative overflow-hidden flex flex-col justify-end">
                            <div id="chat_preview_bubble" class="self-start bg-gray-100 rounded-2xl rounded-bl-none p-3 max-w-[85%] transform transition-all duration-300 translate-y-2 opacity-0">
                                <span class="text-[10px] font-bold text-pink-500 block mb-1">CUSTOMER</span>
                                <p class="text-xs text-gray-600 leading-relaxed font-medium">{{ $complaint->description }}</p>
                            </div>
                            
                            <div id="reply_preview_bubble" class="self-end bg-indigo-50 rounded-2xl rounded-br-none p-3 max-w-[85%] mt-3 border border-indigo-100 transform transition-all duration-300 -translate-y-2 opacity-0">
                                <span class="text-[10px] font-bold text-indigo-600 block mb-1">ADMIN</span>
                                <p id="preview_text" class="text-xs text-indigo-800 leading-relaxed font-medium italic">Ketik balasan untuk melihat preview...</p>
                            </div>
                        </div>

                        <!-- Template Buttons -->
                        <div class="grid grid-cols-3 gap-2 mb-4">
                            <button type="button" onclick="setReply('confirm')" class="px-3 py-2 bg-gradient-to-br from-teal-100 to-teal-200 hover:from-teal-200 hover:to-teal-300 text-teal-700 rounded-xl text-xs font-bold transition-all border-2 border-teal-300 hover:shadow-md">
                                ✅ Konfirmasi
                            </button>
                            <button type="button" onclick="setReply('finish')" class="px-3 py-2 bg-gradient-to-br from-emerald-100 to-emerald-200 hover:from-emerald-200 hover:to-emerald-300 text-emerald-700 rounded-xl text-xs font-bold transition-all border-2 border-emerald-300 hover:shadow-md">
                                ✨ Selesai
                            </button>
                            <button type="button" onclick="setReply('photo')" class="px-3 py-2 bg-gradient-to-br from-orange-100 to-orange-200 hover:from-orange-200 hover:to-orange-300 text-orange-700 rounded-xl text-xs font-bold transition-all border-2 border-orange-300 hover:shadow-md">
                                📸 Minta Foto
                            </button>
                        </div>

                        <!-- Message Input -->
                        <div class="relative group">
                            <textarea name="message" id="reply_input" onkeyup="updatePreview()" class="w-full bg-white border-2 border-gray-200 rounded-2xl p-4 text-sm text-gray-700 placeholder:text-gray-300 focus:ring-2 focus:ring-teal-500/50 focus:border-teal-500 transition-all shadow-sm resize-none" placeholder="Ketik pesan di sini..." rows="3" required></textarea>
                            
                            <!-- API Send Button Removed -->
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            {{-- WhatsApp Button (Primary) --}}
                            @php
                                $phone = $complaint->customer_phone;
                                if (str_starts_with($phone, '0')) {
                                    $phone = '62' . substr($phone, 1);
                                }
                                $phone = preg_replace('/[^0-9]/', '', $phone);
                            @endphp
                            
                            <a id="wa_primary_btn" href="https://wa.me/{{ $phone }}" target="_blank" class="col-span-2 flex items-center justify-center py-3 px-4 rounded-2xl text-sm font-black text-white bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 shadow-lg hover:shadow-xl transition-all group border-b-4 border-teal-700 active:border-b-0 active:translate-y-1">
                                <svg class="w-6 h-6 mr-2 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.438 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884 0 2.225.569 4.315 1.751 6.132l-1.01 3.687 3.748-.984zm11.387-4.605c-.328-.164-1.94-.957-2.24-1.066-.3-.11-.518-.164-.738.164-.219.328-.848 1.066-1.039 1.284-.191.218-.382.245-.71.082-.328-.164-1.386-.511-2.641-1.63-1.054-.939-1.765-2.098-1.972-2.425-.207-.327-.022-.505.142-.667.147-.146.328-.382.492-.573.164-.191.218-.328.328-.546.109-.218.055-.409-.027-.573-.082-.164-.738-1.776-1.011-2.431-.267-.641-.539-.554-.738-.564-.191-.01-.409-.011-.628-.011s-.573.082-.874.409c-.3.327-1.147 1.12-1.147 2.729 0 1.609 1.174 3.166 1.338 3.384.164.218 2.312 3.529 5.599 4.946.782.338 1.392.54 1.868.691.784.248 1.498.213 2.061.129.627-.094 1.94-.792 2.213-1.556.273-.764.273-1.42.191-1.556-.081-.136-.3-.218-.627-.382z"/>
                                </svg>
                                KIRIM KE WHATSAPP
                            </a>
                        </div>
                    </form>
                </div>

                <script>
                function updatePreview() {
                    let input = document.getElementById('reply_input').value;
                    let preview = document.getElementById('preview_text');
                    let bubble = document.getElementById('reply_preview_bubble');
                    // WhatsApp Link Update
                    let waBtn = document.getElementById('wa_primary_btn');
                    let baseWaUrl = "https://wa.me/{{ $phone }}?text=";

                    if (input.trim() !== "") {
                        preview.innerText = input;
                        preview.classList.remove('italic');
                        bubble.style.opacity = "1";
                        bubble.style.transform = "translateY(0)";
                        
                        let encoded = encodeURIComponent(input);
                        waBtn.href = baseWaUrl + encoded;
                    } else {
                        preview.innerText = "Ketik balasan untuk melihat preview...";
                        preview.classList.add('italic');
                        bubble.style.opacity = "0.7";
                        
                        waBtn.href = "https://wa.me/{{ $phone }}";
                    }
                }

                function setReply(type) {
                    let input = document.getElementById('reply_input');
                    let text = "";
                    let name = "{{ $complaint->customer_name }}";
                    
                    switch(type) {
                        case 'confirm':
                            text = "Halo Kak " + name + ", terima kasih laporan keluhannya. Kami sedang mengecek detail komplain Kakak dan akan segera memberikan solusi terbaik. Mohon ditunggu ya Kak.";
                            break;
                        case 'finish':
                            text = "Kabar baik Kak " + name + "! Komplain Kakak sudah kami tindak lanjuti dan selesai diperbaiki. Kami akan pastikan kejadian serupa tidak terulang. Terima kasih atas kesabarannya.";
                            break;
                        case 'photo':
                            text = "Mohon maaf Kak " + name + ", untuk membantu kami melakukan pengecekan lebih detail, bolehkah kami dikirimkan foto detail bagian yang dikeluhkan? Terima kasih sebelumnya.";
                            break;
                    }
                    
                    input.value = text;
                    updatePreview();
                    
                    // Trigger animation
                    let bubble = document.getElementById('reply_preview_bubble');
                    bubble.style.transform = "scale(1.05)";
                    setTimeout(() => { bubble.style.transform = "scale(1)"; }, 200);
                }

                // Initial show of customer bubble
                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(() => {
                        let customerBubble = document.getElementById('chat_preview_bubble');
                        customerBubble.style.opacity = "1";
                        customerBubble.style.transform = "translateY(0)";
                    }, 300);
                });
                </script>
        </div>
    </div>
</x-app-layout>

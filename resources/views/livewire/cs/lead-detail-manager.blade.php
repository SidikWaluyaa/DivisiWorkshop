<div>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap');
        
        .lead-console {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            color: #1e293b;
            padding-bottom: 50px;
        }
        .font-display { font-family: 'Outfit', sans-serif; }
        
        /* Premium Background */
        .bg-mesh {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 0;
            background-color: #f0f2f5;
            background-image: 
                radial-gradient(at 0% 0%, rgba(34, 176, 134, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(255, 194, 50, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(34, 176, 134, 0.05) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(255, 194, 50, 0.05) 0px, transparent 50%);
        }

        .content-container { position: relative; z-index: 1; }

        /* Glassmorphism Cards */
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 30px;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-panel:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px -15px rgba(0,0,0,0.1);
        }

        /* Hero Header */
        .hero-header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 35px;
            padding: 40px;
            color: white;
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px -15px rgba(30, 41, 59, 0.3);
        }
        .hero-header::after {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 400px; h-400px;
            background: radial-gradient(circle, rgba(34, 176, 134, 0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        /* Status Badges */
        .badge-premium {
            padding: 6px 16px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .badge-hot { background: #fee2e2; color: #dc2626; box-shadow: 0 4px 10px rgba(220, 38, 38, 0.1); }
        .badge-warm { background: #fef3c7; color: #d97706; box-shadow: 0 4px 10px rgba(217, 119, 6, 0.1); }
        .badge-emerald { background: #d1fae5; color: #059669; box-shadow: 0 4px 10px rgba(5, 150, 105, 0.1); }

        /* Buttons */
        .btn-action {
            background: #22B086;
            color: white;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            padding: 14px 28px;
            border-radius: 18px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 20px -5px rgba(34, 176, 134, 0.4);
        }
        .btn-action:hover {
            background: #1a8a68;
            transform: scale(1.05);
            box-shadow: 0 15px 30px -5px rgba(34, 176, 134, 0.5);
        }

        .btn-outline {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 24px;
            border-radius: 18px;
            font-weight: 700;
            transition: all 0.3s;
        }
        .btn-outline:hover { background: rgba(255, 255, 255, 0.2); }

        /* Service Cards */
        .service-tile {
            background: white;
            border: 2px solid #f1f5f9;
            border-radius: 20px;
            padding: 15px;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .service-tile.selected {
            border-color: #22B086;
            background: #f0fdf4;
            box-shadow: 0 10px 25px -10px rgba(34, 176, 134, 0.3);
        }
        .service-tile.selected::after {
            content: '✓';
            position: absolute;
            top: 5px; right: 8px;
            color: #22B086;
            font-weight: 900;
            font-size: 14px;
        }

        /* Activity Timeline */
        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 25px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0; top: 0;
            width: 2px; height: 100%;
            background: #e2e8f0;
        }
        .timeline-dot {
            position: absolute;
            left: -5px; top: 5px;
            width: 12px; height: 12px;
            border-radius: 50%;
            background: white;
            border: 3px solid #22B086;
            box-shadow: 0 0 0 5px rgba(34, 176, 134, 0.1);
        }

        /* Tab Premium */
        .tab-btn {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.1em;
            padding: 15px 25px;
            border-radius: 15px;
            transition: all 0.3s;
            color: #64748b;
        }
        .tab-btn.active {
            background: white;
            color: #1e293b;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        /* Custom Scrollbar */
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>

    <div class="lead-console">
        <div class="bg-mesh"></div>
        
        <div class="content-container container mx-auto px-6 py-10">
            
            {{-- Elite Hero Header --}}
            <div class="hero-header">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative z-10">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-3xl bg-emerald-500/20 backdrop-blur-xl border border-white/20 flex items-center justify-center shadow-2xl">
                             <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="badge-premium {{ $lead->status === 'LOST' ? 'bg-red-500 text-white' : 'badge-emerald' }}">
                                    {{ $lead->status }}
                                </span>
                                <span class="text-xs font-bold text-white/40 tracking-widest">#LD-{{ str_pad($lead->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <h1 class="text-4xl md:text-5xl font-black font-display tracking-tight">{{ $lead->customer_name ?: 'Pelanggan Anonim' }}</h1>
                            <div class="flex items-center gap-4 mt-2 text-white/60 text-sm font-medium">
                                <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" /></svg> {{ $lead->customer_phone }}</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-white/20"></span>
                                <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" /></svg> Sumber: {{ $lead->source }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        @if(!$lead->spk)
                             <button wire:click="$set('showDraftModal', true)" class="btn-outline">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                                Buat Penawaran
                             </button>
                             @if($lead->quotations()->accepted()->exists())
                                <button wire:click="openSpkModal" class="btn-action">
                                    Terbitkan SPK
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
                                </button>
                             @endif
                        @else
                             <div class="bg-emerald-500 p-4 px-8 rounded-2xl flex items-center gap-4 shadow-2xl shadow-emerald-500/20">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase text-white/60 tracking-widest leading-none">SPK AKTIF</p>
                                    <p class="text-xl font-black text-white">#{{ $lead->spk->spk_number }}</p>
                                </div>
                             </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Main Dashboard Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- Left Sidebar: Info & Timeline --}}
                <div class="lg:col-span-4 space-y-8">
                    {{-- Pipeline Stage Control --}}
                    <div class="glass-panel p-8 bg-gradient-to-br from-white to-slate-50/50">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Kontrol Pipeline</h3>
                        
                        <div class="space-y-4">
                            @if($lead->status === 'GREETING')
                                <button wire:click="moveToStatus('KONSULTASI')" class="w-full btn-action justify-center py-5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                    Mulai Konsultasi
                                </button>
                            @endif

                            @if($lead->status === 'KONSULTASI')
                                <button wire:click="moveToStatus('FOLLOW_UP')" class="w-full py-5 rounded-2xl bg-orange-100 text-orange-600 font-black text-xs uppercase tracking-widest hover:bg-orange-200 transition-all flex items-center justify-center gap-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Pindah ke Follow Up
                                </button>
                            @endif

                            @if(in_array($lead->status, ['KONSULTASI', 'FOLLOW_UP']))
                                @if($lead->getAcceptedQuotation())
                                    <button wire:click="moveToStatus('CLOSING')" class="w-full py-5 rounded-2xl bg-blue-600 text-white font-black text-xs uppercase tracking-widest hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all flex items-center justify-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Siap Closing
                                    </button>
                                @else
                                    <div class="p-4 rounded-2xl bg-slate-100 border border-slate-200 text-center">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">Kirim & Terima Penawaran untuk lanjut ke Closing</p>
                                    </div>
                                @endif
                            @endif

                            @if($lead->status === 'CLOSING' && !$lead->spk)
                                <button wire:click="openSpkModal" class="w-full btn-action justify-center py-5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    Terbitkan SPK
                                </button>
                            @endif

                            @if($lead->status === 'LOST')
                                <div class="p-6 rounded-[30px] bg-red-50 border border-red-100 text-center">
                                    <p class="text-xs font-black text-red-500 uppercase tracking-widest mb-1">Status: LOST</p>
                                    <p class="text-[10px] font-bold text-red-400 uppercase tracking-tight">{{ $lead->lost_reason }}</p>
                                    <button wire:click="moveToStatus('KONSULTASI')" class="mt-4 text-[10px] font-black text-slate-400 hover:text-slate-600 underline uppercase tracking-widest">Aktifkan Kembali</button>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Lead Info Panel --}}
                    <div class="glass-panel p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Detail Profil</h3>
                            <button class="text-emerald-500 hover:text-emerald-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                        </div>
                        
                        <div class="space-y-5">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">CS Handler</span>
                                <div class="flex flex-col items-end gap-1">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-[10px] font-black text-emerald-600">{{ substr($lead->cs->name ?? '?', 0, 1) }}</div>
                                        <span class="text-sm font-black text-slate-800">{{ $lead->cs->name ?? 'Belum Ditunjuk' }}</span>
                                    </div>
                                    @if($lead->cs_id !== auth()->id())
                                        <button wire:click="takeOverLead" class="text-[9px] font-black text-emerald-600 hover:text-emerald-700 uppercase tracking-widest underline decoration-emerald-200 underline-offset-4 transition-all">
                                            Ambil Alih Lead
                                        </button>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Channel</span>
                                <span class="text-sm font-black text-slate-700 bg-slate-100 px-3 py-1 rounded-lg">{{ $lead->channel ?: 'WhatsApp' }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Prioritas</span>
                                <span class="badge-premium {{ $lead->priority === 'HOT' ? 'badge-hot' : ($lead->priority === 'WARM' ? 'badge-warm' : 'bg-slate-100 text-slate-500') }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $lead->priority === 'HOT' ? 'bg-red-500' : ($lead->priority === 'WARM' ? 'bg-amber-500' : 'bg-slate-400') }}"></span>
                                    {{ $lead->priority }}
                                </span>
                            </div>

                            <div class="pt-4 border-t border-slate-100 space-y-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Informasi Kontak</span>
                                    <p class="text-sm font-bold text-slate-800">{{ $lead->customer_phone }}</p>
                                    <p class="text-sm font-medium text-slate-500">{{ $lead->customer_email ?: 'Email tidak tersedia' }}</p>
                                </div>

                                {{-- Catatan Internal --}}
                                <div class="pt-6 border-t border-slate-100">
                                    <p class="text-[10px] font-black text-slate-400 uppercase mb-3">Catatan Internal</p>
                                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-sm italic text-slate-600 leading-relaxed shadow-inner">
                                        "{{ $lead->notes ?: 'Tidak ada catatan khusus untuk lead ini.' }}"
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Pengiriman Panel --}}
                            <div class="glass-panel p-8 bg-slate-50/50 border border-slate-100">
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-black text-slate-900 uppercase tracking-tighter text-lg">Informasi Pengiriman</h3>
                                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Alamat untuk SPK & Kurir</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="text-[9px] font-black text-slate-400 uppercase mb-2 block tracking-widest">Alamat Lengkap</label>
                                        <textarea wire:model="lead.customer_address" rows="3" class="w-full bg-white border border-slate-100 rounded-2xl p-4 text-[10px] font-bold focus:ring-4 focus:ring-emerald-500/10 resize-none transition-all" placeholder="Masukkan alamat lengkap..."></textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-[9px] font-black text-slate-400 uppercase mb-2 block tracking-widest">Kota</label>
                                            <input type="text" wire:model="lead.customer_city" class="w-full bg-white border border-slate-100 rounded-xl p-3 text-[10px] font-bold focus:ring-4 focus:ring-emerald-500/10 transition-all" placeholder="Kota/Kab">
                                        </div>
                                        <div>
                                            <label class="text-[9px] font-black text-slate-400 uppercase mb-2 block tracking-widest">Provinsi</label>
                                            <input type="text" wire:model="lead.customer_province" class="w-full bg-white border border-slate-100 rounded-xl p-3 text-[10px] font-bold focus:ring-4 focus:ring-emerald-500/10 transition-all" placeholder="Provinsi">
                                        </div>
                                    </div>
                                    <button wire:click="updateLeadAddress" class="w-full py-4 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-xl hover:bg-slate-800 transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                        SIMPAN ALAMAT
                                    </button>
                                </div>
                            </div>


                    {{-- Activity Timeline Panel --}}
                    <div class="glass-panel p-8 overflow-hidden">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-8">Aktivitas Terakhir</h3>
                        
                        {{-- Log Input --}}
                        <div class="mb-10 relative">
                            <textarea wire:model="activityContent" placeholder="Catat aktivitas baru..." class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-sm font-medium focus:ring-2 focus:ring-emerald-500 transition-all shadow-inner" rows="3"></textarea>
                            <div class="flex justify-between mt-3 items-center">
                                <div class="flex gap-2">
                                    <button wire:click="$set('activityType', 'CHAT')" class="w-8 h-8 rounded-lg flex items-center justify-center transition-all {{ $activityType === 'CHAT' ? 'bg-emerald-500 text-white shadow-lg' : 'bg-slate-100 text-slate-400' }}">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" /></svg>
                                    </button>
                                    <button wire:click="$set('activityType', 'CALL')" class="w-8 h-8 rounded-lg flex items-center justify-center transition-all {{ $activityType === 'CALL' ? 'bg-blue-500 text-white shadow-lg' : 'bg-slate-100 text-slate-400' }}">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" /></svg>
                                    </button>
                                </div>
                                <button wire:click="logActivity" class="text-xs font-black text-emerald-600 uppercase tracking-widest hover:text-emerald-700">Simpan Aktivitas</button>
                            </div>
                        </div>

                        <div class="space-y-2 max-h-[500px] overflow-y-auto pr-2 custom-scroll">
                            @foreach($lead->activities->take(15) as $activity)
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ $activity->created_at->diffForHumans() }}</span>
                                        <span class="text-[8px] font-black text-slate-300 uppercase">{{ $activity->type }}</span>
                                    </div>
                                    <p class="text-sm font-medium text-slate-700 leading-snug">{{ $activity->content }}</p>
                                    <p class="text-[10px] font-bold text-emerald-500 mt-1">@ {{ $activity->user->name }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Right Main: Workflow & Tabs --}}
                <div class="lg:col-span-8 space-y-8">
                    
                    {{-- Premium Tabs --}}
                    <div class="flex gap-2 p-2 bg-slate-200/50 rounded-[22px] backdrop-blur-md w-fit">
                        <button wire:click="$set('activeTab', 'all')" class="tab-btn {{ $activeTab === 'all' ? 'active' : '' }}">
                            Daftar Barang
                        </button>
                        <button wire:click="$set('activeTab', 'draft')" class="tab-btn {{ $activeTab === 'draft' ? 'active' : '' }}">
                            Riwayat Penawaran
                        </button>
                        <button wire:click="$set('activeTab', 'spk')" class="tab-btn {{ $activeTab === 'spk' ? 'active' : '' }}">
                            Detail SPK
                        </button>
                    </div>

                    {{-- Pipeline Content --}}
                    <div class="min-h-[400px]">
                        @if($activeTab === 'all')
                            <div class="grid grid-cols-1 gap-6">
                                @php $allQuotationItems = $lead->quotations->flatMap->quotationItems; @endphp
                                @forelse($allQuotationItems as $item)
                                    <div class="glass-panel p-8 flex flex-col md:flex-row gap-8 relative overflow-hidden group">
                                        @if($item->is_warranty)
                                            <div class="absolute -top-10 -right-10 w-24 h-24 bg-emerald-500 rotate-45 flex items-end justify-center pb-2">
                                                <svg class="w-6 h-6 text-white -rotate-45" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                            </div>
                                        @endif
                                        
                                        <div class="w-full md:w-48 h-48 rounded-[25px] bg-slate-100 flex items-center justify-center border-2 border-dashed border-slate-200 group-hover:border-emerald-300 transition-colors">
                                             <svg class="w-16 h-16 text-slate-300 group-hover:text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>

                                        <div class="flex-grow">
                                            <div class="flex justify-between items-start mb-6">
                                                <div>
                                                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em] mb-1 block">{{ $item->category }}</span>
                                                    <h4 class="text-2xl font-black font-display text-slate-900 leading-tight">{{ $item->shoe_brand ?: 'Brand' }} {{ $item->shoe_type ?: 'Model' }}</h4>
                                                    <p class="text-sm font-bold text-slate-400 mt-1">Warna: {{ $item->shoe_color }} | Ukuran: {{ $item->shoe_size }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Estimasi Nilai</p>
                                                    <p class="text-3xl font-black text-emerald-600 font-display">Rp {{ number_format($item->item_total_price, 0, ',', '.') }}</p>
                                                </div>
                                            </div>

                                            <div class="flex flex-wrap gap-2 mb-8">
                                                @php $itemServices = $item->services ?? []; @endphp
                                                @foreach($itemServices as $svc)
                                                    <span class="px-4 py-2 bg-slate-900 text-white text-[10px] font-black rounded-xl tracking-tight shadow-lg shadow-slate-900/10">{{ strtoupper($svc['name'] ?? 'Jasa') }}</span>
                                                @endforeach
                                                @if(empty($itemServices))
                                                    <span class="px-4 py-2 bg-slate-100 text-slate-400 text-[10px] font-black rounded-xl italic">Belum ada jasa terpilih.</span>
                                                @endif
                                            </div>

                                            <div class="flex justify-between items-center pt-6 border-t border-slate-50">
                                                <div class="flex gap-8">
                                                    <div class="flex flex-col">
                                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Estimasi Durasi</span>
                                                        <span class="text-lg font-black text-slate-800">{{ $item->hk_days ?: 0 }} <span class="text-xs text-slate-400">HK</span></span>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Status Garansi</span>
                                                        <span class="text-lg font-black {{ $item->is_warranty ? 'text-emerald-500' : 'text-slate-300' }}">{{ $item->is_warranty ? 'Bergaransi' : 'Tidak' }}</span>
                                                    </div>
                                                </div>
                                                
                                                <button wire:click="openEditItem({{ $item->id }})" class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl hover:bg-emerald-500 hover:text-white transition-all shadow-xl shadow-emerald-500/5">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="glass-panel p-20 text-center">
                                        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                                            <svg class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                        </div>
                                        <h4 class="text-xl font-black font-display text-slate-400 uppercase tracking-widest">Belum Ada Barang</h4>
                                        <p class="text-slate-400 mt-2 font-medium">Siap untuk memulai perjalanan restorasi baru?</p>
                                        <button wire:click="$set('showDraftModal', true)" class="mt-8 text-emerald-500 font-black text-sm uppercase tracking-[0.2em] hover:text-emerald-700 transition-colors">MULAI PENAWARAN ➔</button>
                                    </div>
                                @endforelse
                            </div>
                        @endif

                        @if($activeTab === 'draft')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($lead->quotations as $q)
                                    <div class="glass-panel p-8 border-b-8 border-emerald-500/10">
                                        <div class="flex justify-between items-start mb-6">
                                            <div>
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">ID Penawaran</p>
                                                <h4 class="text-xl font-black font-display text-slate-900">#{{ $q->quotation_number }}</h4>
                                                <p class="text-[10px] font-bold text-emerald-500 mt-1">REVISI V{{ $q->version }}</p>
                                            </div>
                                            <span class="px-4 py-2 bg-emerald-50 text-emerald-700 text-[10px] font-black rounded-xl border border-emerald-100 shadow-sm">{{ $q->status }}</span>
                                        </div>
                                        <div class="space-y-3 mb-8">
                                             <div class="flex justify-between text-sm">
                                                <span class="text-slate-500 font-bold">Jumlah Barang</span>
                                                <span class="font-black text-slate-800">{{ $q->quotationItems->count() }} Pcs</span>
                                             </div>
                                             <div class="flex justify-between text-sm">
                                                <span class="text-slate-500 font-bold">Dibuat Pada</span>
                                                <span class="font-black text-slate-800">{{ $q->created_at->format('d/m/Y') }}</span>
                                             </div>
                                        </div>
                                        <div class="pt-6 border-t border-slate-100 flex justify-between items-center">
                                            <div class="flex -space-x-3">
                                                @foreach($q->quotationItems as $item)
                                                    <div class="w-10 h-10 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-[10px] font-black text-slate-500">{{ substr($item->category, 0, 1) }}</div>
                                                @endforeach
                                            </div>
                                            <p class="text-2xl font-black text-emerald-600 font-display">Rp {{ number_format($q->total, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($activeTab === 'spk')
                            @if($lead->spk)
                                <div class="bg-slate-900 rounded-[40px] p-10 lg:p-14 text-white relative overflow-hidden shadow-2xl shadow-slate-900/40">
                                    <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-emerald-500/10 blur-[100px] rounded-full"></div>
                                    <div class="absolute -left-20 -top-20 w-80 h-80 bg-blue-500/5 blur-[100px] rounded-full"></div>
                                    
                                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-16 relative z-10 gap-8">
                                        <div>
                                            <span class="px-5 py-2 bg-emerald-500 text-white text-[10px] font-black rounded-xl tracking-[0.3em] mb-6 inline-block shadow-xl shadow-emerald-500/40 uppercase">Master SPK</span>
                                            <h4 class="text-6xl font-black font-display tracking-tighter text-white">#{{ $lead->spk->spk_number }}</h4>
                                            <p class="text-slate-400 font-bold uppercase tracking-[0.2em] text-[10px] mt-4 flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Terbit Pada {{ $lead->spk->created_at->translatedFormat('l, d F Y') }}
                                            </p>
                                        </div>
                                        <div class="bg-white/5 backdrop-blur-md p-6 rounded-[30px] border border-white/10 min-w-[200px]">
                                            <p class="text-[9px] font-black text-slate-500 uppercase mb-3 tracking-widest">Status Workshop</p>
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center border border-emerald-500/20">
                                                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-black text-white leading-none uppercase tracking-tight">Dalam Antrean</p>
                                                    <p class="text-[10px] text-slate-500 mt-1 font-bold">Menunggu Teknisi</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if(!$lead->spk->work_order_id)
                                    <div class="mb-16 flex justify-center relative z-10">
                                        <button wire:click="openHandover" class="group bg-emerald-500 hover:bg-emerald-400 text-white px-12 py-6 rounded-[30px] font-black font-display text-xl flex items-center gap-4 transition-all shadow-2xl shadow-emerald-500/30">
                                            <span>SERAHKAN KE WORKSHOP</span>
                                            <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7-7 7M5 5l7 7-7 7" /></svg>
                                        </button>
                                    </div>
                                    @endif

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
                                        <div class="bg-white/5 p-8 rounded-[35px] border border-white/10 hover:bg-white/[0.07] transition-all">
                                            <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-3">Logika Pengiriman</p>
                                            <p class="text-2xl font-black text-white">{{ $lead->spk->delivery_type }}</p>
                                        </div>
                                        <div class="bg-white/5 p-8 rounded-[35px] border border-white/10 hover:bg-white/[0.07] transition-all">
                                            <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-3">Jalur Prioritas</p>
                                            <p class="text-2xl font-black text-white tracking-tight">{{ $lead->spk->priority }}</p>
                                        </div>
                                        <div class="bg-white/5 p-8 rounded-[35px] border border-white/10 hover:bg-white/[0.07] transition-all">
                                            <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-3">Target Selesai</p>
                                            <p class="text-2xl font-black text-white">{{ ($lead->spk->expected_delivery_date && $lead->spk->expected_delivery_date->year > 2000) ? $lead->spk->expected_delivery_date->format('d M Y') : 'BELUM DISET' }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-12 space-y-4 relative z-10">
                                        <h4 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.3em] mb-6 px-2">Rincian Barang Produksi</h4>
                                        @foreach($lead->spk->items as $spkItem)
                                            <div class="p-8 bg-white/5 rounded-[35px] border border-white/5 flex justify-between items-center group hover:border-emerald-500/30 transition-all cursor-default">
                                                <div class="flex items-center gap-8">
                                                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center font-black text-emerald-400 text-2xl border border-white/10 shadow-inner">{{ $loop->iteration }}</div>
                                                    <div>
                                                        <h5 class="text-xl font-black text-white group-hover:text-emerald-400 transition-colors">{{ $spkItem->shoe_brand }} {{ $spkItem->shoe_type }}</h5>
                                                            <div class="flex gap-4 text-[11px] font-bold text-slate-500 mt-2 uppercase tracking-widest">
                                                                <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-slate-700"></span>{{ $spkItem->category }}</span>
                                                                <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-slate-700"></span>{{ $spkItem->hk_days }} HK</span>
                                                            </div>

                                                            <div class="mt-6 flex flex-wrap gap-2">
                                                                @foreach($spkItem->services as $service)
                                                                    <div class="flex items-center gap-2 px-4 py-2 bg-emerald-500/10 rounded-xl border border-emerald-500/20">
                                                                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                                                        <span class="text-[10px] font-black text-emerald-400 uppercase tracking-wider">{{ $service['name'] }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                </div>
                                                <div class="text-right">
                                                     <p class="text-2xl font-black text-emerald-400 font-display">Rp {{ number_format($spkItem->item_total_price, 0, ',', '.') }}</p>
                                                     <p class="text-[10px] font-black text-slate-600 uppercase mt-1 tracking-tighter">Subtotal Produksi</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="glass-panel p-20 text-center">
                                    <h4 class="text-xl font-black font-display text-slate-400 uppercase tracking-widest">SPK Belum Terbit</h4>
                                    <p class="text-slate-400 mt-2">Terbitkan SPK untuk memulai mesin workshop.</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- PREMIUM MODALS SECTION --}}
        
        {{-- 1. DRAFT QUOTATION MODAL --}}
        @if($showDraftModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xl">
            <div class="glass-panel bg-white/95 w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)] border-white">
                <div class="p-10 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h2 class="text-4xl font-black font-display text-slate-900 tracking-tight">Mulai Penawaran</h2>
                        <p class="text-sm text-slate-500 font-medium">Susun item restorasi premium untuk lead ini.</p>
                    </div>
                    <button wire:click="$set('showDraftModal', false)" class="w-14 h-14 bg-slate-100 text-slate-400 rounded-2xl hover:bg-red-500 hover:text-white transition-all flex items-center justify-center group">
                        <svg class="w-6 h-6 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l18 18" /></svg>
                    </button>
                </div>

                <div class="flex-grow overflow-y-auto p-10 space-y-12 custom-scroll">
                    @foreach($draftItems as $idx => $item)
                        <div class="relative bg-white p-8 rounded-[40px] shadow-2xl shadow-slate-200/50 border border-slate-100 group">
                            <div class="absolute -top-6 -left-6 w-14 h-14 bg-emerald-500 text-white rounded-2xl flex items-center justify-center font-black text-xl shadow-2xl shadow-emerald-500/40 z-10">
                                {{ $idx + 1 }}
                            </div>
                            @if($idx > 0)
                                <button wire:click="removeDraftItem({{ $idx }})" class="absolute top-6 right-6 text-slate-300 hover:text-red-500 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                                <div>
                                    <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest">Kategori Item (Rumus SPK)</label>
                                    <select wire:model="draftItems.{{ $idx }}.category" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 appearance-none">
                                        <option value="Sepatu">Sepatu (S)</option>
                                        <option value="Tas">Tas (T)</option>
                                        <option value="Headwear">Headwear (H)</option>
                                        <option value="Apparel">Apparel (A)</option>
                                        <option value="Lainnya">Lainnya (L)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest">Brand</label>
                                    <input type="text" wire:model="draftItems.{{ $idx }}.shoe_brand" placeholder="cth: Nike" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm font-black focus:ring-4 focus:ring-emerald-500/10">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest">Model / Tipe</label>
                                    <input type="text" wire:model="draftItems.{{ $idx }}.shoe_type" placeholder="cth: Air Force 1" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm font-black focus:ring-4 focus:ring-emerald-500/10">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest">Warna / Ukuran</label>
                                    <div class="flex gap-2">
                                        <input type="text" wire:model="draftItems.{{ $idx }}.shoe_color" placeholder="Warna" class="w-1/2 bg-slate-50 border-0 rounded-2xl p-4 text-sm font-black">
                                        <input type="text" wire:model="draftItems.{{ $idx }}.shoe_size" placeholder="Uk" class="w-1/2 bg-slate-50 border-0 rounded-2xl p-4 text-sm font-black">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-10 p-8 bg-slate-50 rounded-[40px] border border-slate-100 shadow-inner">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                    <div>
                                        <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-[0.2em]">Filter Kategori Jasa (Katalog)</label>
                                        <select wire:model.live="draftItems.{{ $idx }}.service_category_filter" class="w-full bg-white border border-slate-100 rounded-2xl p-4 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 shadow-sm">
                                            <option value="">Semua Kategori</option>
                                            @foreach($this->services->pluck('category')->filter()->unique()->sort() as $serviceCat)
                                                <option value="{{ $serviceCat }}">{{ $serviceCat }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-[0.2em]">Cari Jasa</label>
                                        <div class="relative">
                                            <input type="text" wire:model.live.debounce.300ms="draftItems.{{ $idx }}.service_search" placeholder="Cari nama jasa..." class="w-full bg-white border border-slate-100 rounded-2xl p-4 pl-12 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 shadow-sm transition-all">
                                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- SELECTED SERVICES SUMMARY (Always Visible) --}}
                                @if(count($draftItems[$idx]['selected_services']) > 0)
                                    <div class="mb-8">
                                        <label class="text-[10px] font-black uppercase text-emerald-600 mb-4 block tracking-[0.2em]">Jasa Terpilih, Edit Detail & Harga</label>
                                        <div class="space-y-3">
                                            @foreach($this->services->whereIn('id', $draftItems[$idx]['selected_services']) as $selectedSvc)
                                                <div wire:key="selected-svc-{{ $idx }}-{{ $selectedSvc->id }}" class="flex flex-col md:flex-row items-center gap-4 p-4 bg-emerald-50/50 rounded-2xl border border-emerald-100 shadow-sm">
                                                    <div class="flex items-center gap-3 w-full md:w-1/3">
                                                        <button wire:click="toggleService({{ $idx }}, {{ $selectedSvc->id }})" class="text-emerald-500 hover:text-red-500 transition-colors shrink-0">
                                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z" clip-rule="evenodd" /></svg>
                                                        </button>
                                                        <div class="truncate">
                                                            <p class="text-[11px] font-black text-slate-800 uppercase truncate">{{ $selectedSvc->name }}</p>
                                                            <p class="text-[8px] font-bold text-slate-400 uppercase">{{ $selectedSvc->category }} | {{ $selectedSvc->hk_days }} HK</p>
                                                        </div>
                                                    </div>

                                                    <div class="flex-grow w-full">
                                                        <input type="text" 
                                                            wire:model.live="draftItems.{{ $idx }}.service_details.{{ $selectedSvc->id }}" 
                                                            placeholder="Detail instruksi untuk jasa ini..." 
                                                            class="w-full bg-white/60 border-0 rounded-xl p-3 text-[11px] font-black text-slate-600 focus:ring-4 focus:ring-emerald-500/10 placeholder:text-slate-300">
                                                    </div>

                                                    <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl border border-emerald-100 shrink-0">
                                                        <span class="text-xs font-black text-slate-400">Rp</span>
                                                        <input type="number" 
                                                            wire:model.live="draftItems.{{ $idx }}.custom_service_prices.{{ $selectedSvc->id }}" 
                                                            class="w-28 bg-transparent border-0 p-0 text-sm font-black text-emerald-600 focus:ring-0 text-right">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- SERVICE CATALOG (Filtered) --}}
                                <label class="text-[10px] font-black uppercase text-slate-400 mb-4 block tracking-[0.2em]">Tambah Jasa (Pilih dari Katalog)</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @php
                                        $filteredServices = $this->services;
                                        if(!empty($draftItems[$idx]['service_category_filter'])) {
                                            $filteredServices = $filteredServices->where('category', $draftItems[$idx]['service_category_filter']);
                                        }
                                        if(!empty($draftItems[$idx]['service_search'])) {
                                            $searchTerm = strtolower($draftItems[$idx]['service_search']);
                                            $filteredServices = $filteredServices->filter(function($s) use ($searchTerm) {
                                                return str_contains(strtolower($s->name), $searchTerm);
                                            });
                                        }
                                        // Exclude already selected to keep catalog clean
                                        $filteredServices = $filteredServices->whereNotIn('id', $draftItems[$idx]['selected_services']);
                                    @endphp
                                    @foreach($filteredServices as $svc)
                                        <div wire:key="catalog-svc-{{ $idx }}-{{ $svc->id }}" wire:click="toggleService({{ $idx }}, {{ $svc->id }})" class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-transparent hover:border-emerald-200 transition-all cursor-pointer group shadow-sm">
                                            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-300 group-hover:bg-emerald-50 group-hover:text-emerald-500 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                                            </div>
                                            <div class="flex-grow min-w-0">
                                                <p class="text-[10px] font-black text-slate-700 truncate uppercase leading-none">{{ $svc->name }}</p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <p class="text-[9px] font-bold text-emerald-500">Rp {{ number_format($svc->price, 0, ',', '.') }}</p>
                                                    <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                                    <p class="text-[9px] font-bold text-slate-400">{{ $svc->hk_days }} HK</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- CUSTOM SERVICES SECTION --}}
                                <div class="mt-8 pt-8 border-t border-slate-100">
                                    <div class="flex justify-between items-center mb-4 px-2">
                                        <div class="flex flex-col">
                                            <label class="text-[10px] font-black uppercase text-amber-600 tracking-[0.2em]">Jasa Kustom (Luar Katalog)</label>
                                            <span class="text-[8px] font-bold text-slate-400 uppercase">Gunakan jika jasa tidak ada di pilihan katalog di atas</span>
                                        </div>
                                        <button wire:click="addCustomService({{ $idx }})" class="text-[9px] font-black bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg shadow-amber-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                                            Tambah Jasa Kustom
                                        </button>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        @foreach($draftItems[$idx]['custom_services'] as $cIdx => $customSvc)
                                            <div wire:key="custom-svc-{{ $idx }}-{{ $cIdx }}" class="flex flex-col gap-3 p-5 bg-amber-50/50 rounded-2xl border border-amber-100 shadow-sm relative group">
                                                <div class="flex flex-col md:flex-row items-center gap-4">
                                                    {{-- KATEGORI --}}
                                                    <div class="w-full md:w-1/4">
                                                        <label class="text-[8px] font-black text-amber-600 mb-1 block uppercase tracking-widest">Pilih Kategori</label>
                                                        <select wire:model.live="draftItems.{{ $idx }}.custom_services.{{ $cIdx }}.category" 
                                                            class="w-full bg-white border-0 rounded-xl p-3 text-[11px] font-black text-slate-800 focus:ring-4 focus:ring-amber-500/10">
                                                            @foreach($this->categories as $cat)
                                                                <option value="{{ $cat }}">{{ $cat }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    {{-- NAMA JASA --}}
                                                    <div class="w-full md:w-2/4">
                                                        <label class="text-[8px] font-black text-amber-600 mb-1 block uppercase tracking-widest">Nama Jasa Kustom</label>
                                                        <input type="text" 
                                                            wire:model.live="draftItems.{{ $idx }}.custom_services.{{ $cIdx }}.name" 
                                                            placeholder="Contoh: Jahit Ulang Sol..." 
                                                            class="w-full bg-white border-0 rounded-xl p-3 text-[11px] font-black text-slate-800 focus:ring-4 focus:ring-amber-500/10">
                                                    </div>

                                                    {{-- HARGA --}}
                                                    <div class="w-full md:w-1/4">
                                                        <label class="text-[8px] font-black text-amber-600 mb-1 block uppercase tracking-widest text-right">Harga Jasa</label>
                                                        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl border border-amber-100">
                                                            <span class="text-[10px] font-black text-slate-400">Rp</span>
                                                            <input type="number" 
                                                                wire:model.live="draftItems.{{ $idx }}.custom_services.{{ $cIdx }}.price" 
                                                                class="w-full bg-transparent border-0 p-0 text-sm font-black text-amber-600 focus:ring-0 text-right">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-3">
                                                    {{-- DETAIL --}}
                                                    <div class="flex-grow">
                                                        <label class="text-[8px] font-black text-amber-600 mb-1 block uppercase tracking-widest">Detail Instruksi Pengerjaan</label>
                                                        <input type="text" 
                                                            wire:model.live="draftItems.{{ $idx }}.custom_services.{{ $cIdx }}.manual_detail" 
                                                            placeholder="Masukkan detail instruksi di sini..." 
                                                            class="w-full bg-white/60 border-0 rounded-xl p-3 text-[11px] font-black text-slate-600 focus:ring-4 focus:ring-amber-500/10">
                                                    </div>
                                                    <div class="w-16">
                                                        <label class="text-[8px] font-black text-amber-600 mb-1 block uppercase tracking-widest text-center">HK</label>
                                                        <input type="number" 
                                                            wire:model.live="draftItems.{{ $idx }}.custom_services.{{ $cIdx }}.hk_days" 
                                                            placeholder="0" 
                                                            class="w-full bg-white border-0 rounded-xl p-3 text-[11px] font-black text-amber-600 focus:ring-4 focus:ring-amber-500/10 text-center">
                                                    </div>

                                                    <button wire:click="removeCustomService({{ $idx }}, {{ $cIdx }})" class="mt-4 text-red-300 hover:text-red-500 transition-colors p-2 rounded-lg hover:bg-red-50">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start mt-8">
                                        {{-- 1. Catatan Tambahan --}}
                                        <div class="md:col-span-5">
                                            <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-[0.2em] px-2">Catatan Tambahan</label>
                                            <div class="group relative">
                                                <input type="text" wire:model.live.debounce.150ms="draftItems.{{ $idx }}.extra_notes" placeholder="Catatan manual (cth: Prioritas)..." class="w-full bg-white border border-slate-100 rounded-2xl p-5 text-sm font-bold focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-200 transition-all shadow-sm">
                                                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-emerald-400 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 2. Estimasi HK --}}
                                        <div class="md:col-span-2">
                                            <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-[0.2em] px-2 text-center">Total HK</label>
                                            <div class="group relative">
                                            <div class="relative">
                                                <input type="number" 
                                                    wire:model.live.debounce.150ms="draftItems.{{ $idx }}.hk_days" 
                                                    class="w-full bg-white cursor-text border border-slate-100 rounded-2xl p-4 text-sm font-black text-center focus:ring-4 focus:ring-amber-500/10 focus:border-amber-200 transition-all shadow-sm">
                                                
                                                <div class="absolute -top-2 -right-2 bg-emerald-500 text-white p-1.5 rounded-lg shadow-xl" title="Terbuka (Bisa diubah manual)">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>
                                                </div>
                                            </div>
                                            </div>
                                        </div>

                                        {{-- 3. Keterangan Garansi --}}
                                        <div class="md:col-span-5">
                                            <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-[0.2em] px-2">Cover Garansi</label>
                                            <div class="flex items-center gap-3 bg-white rounded-2xl p-2 pr-5 border border-slate-100 shadow-sm focus-within:border-emerald-200 focus-within:ring-4 focus-within:ring-emerald-500/5 transition-all">
                                                {{-- Premium Toggle --}}
                                                <button wire:click="toggleDraftWarranty({{ $idx }})" type="button" class="relative inline-flex h-10 w-16 shrink-0 cursor-pointer rounded-xl border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ ($draftItems[$idx]['is_warranty'] ?? false) ? 'bg-emerald-500' : 'bg-slate-100' }}">
                                                    <span class="pointer-events-none inline-block h-6 w-6 transform rounded-lg bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($draftItems[$idx]['is_warranty'] ?? false) ? 'translate-x-7 mt-1.5' : 'translate-x-1 mt-1.5' }}"></span>
                                                </button>
                                                <input type="text" wire:model.live.debounce.150ms="draftItems.{{ $idx }}.warranty_label" placeholder="Cth: 30 Hari" class="flex-grow bg-transparent border-0 p-0 text-sm font-black text-slate-700 focus:ring-0 placeholder:text-slate-300">
                                            </div>
                                        </div>

                                        {{-- 3. Ringkasan --}}
                                        <div class="md:col-span-3">
                                            <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-[0.2em] px-2">Ringkasan Item</label>
                                            <div class="bg-slate-50/80 backdrop-blur-sm border border-slate-100 rounded-2xl p-4 min-h-[58px] flex items-center shadow-inner">
                                                <p class="text-[11px] font-black text-emerald-600 italic leading-snug">
                                                    {{ $this->calculateDraftSummary($idx) ?: 'Pilih jasa untuk melihat ringkasan' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                        </div>
                    @endforeach

                    <button wire:click="addDraftItem" class="w-full border-4 border-dashed border-slate-100 p-10 rounded-[40px] text-slate-300 font-black uppercase tracking-[0.4em] hover:bg-emerald-50 hover:border-emerald-100 hover:text-emerald-500 transition-all duration-500">
                        + Tambah Item Restorasi
                    </button>
                </div>

                <div class="p-10 border-t border-slate-100 bg-white flex flex-col md:flex-row justify-between items-center gap-8">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Total Nilai Penawaran</p>
                        <p class="text-5xl font-black text-emerald-600 font-display tracking-tighter">Rp {{ number_format($this->totalQuotationValue, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex gap-6 items-center">
                        <button wire:click="$set('showDraftModal', false)" class="text-sm font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">Batalkan Draft</button>
                        <button wire:click="saveQuotation" class="btn-action px-16 py-6 text-lg rounded-[25px]">
                            Simpan Penawaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- 2. EDIT ITEM MODAL (Bahasa Indonesia) --}}
        @if($showEditItemModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xl">
            <div class="glass-panel bg-white/95 w-full max-w-2xl overflow-hidden shadow-2xl border-white">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h2 class="text-2xl font-black font-display text-slate-900 uppercase tracking-tight">Ubah Detail Barang</h2>
                    <button wire:click="$set('showEditItemModal', false)" class="text-slate-400 hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l18 18" /></svg>
                    </button>
                </div>
                           <div class="p-8 max-h-[75vh] overflow-y-auto custom-scroll space-y-8">
                    {{-- 1. INFORMASI DASAR --}}
                    <div class="space-y-6">
                        {{-- Row 1: Utama --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase mb-3 block tracking-[0.2em] px-2">Kategori Item</label>
                                <select wire:model.live="editingData.category" class="w-full bg-slate-50 border-slate-100 rounded-2xl p-5 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-200 transition-all shadow-sm">
                                    <option value="Sepatu">Sepatu (S)</option>
                                    <option value="Tas">Tas (T)</option>
                                    <option value="Headwear">Headwear (H)</option>
                                    <option value="Apparel">Apparel (A)</option>
                                    <option value="Lainnya">Lainnya (L)</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 uppercase mb-3 block tracking-[0.2em] px-2">Brand / Merk</label>
                                        <input type="text" wire:model.live="editingData.shoe_brand" placeholder="Nike, Adidas, etc." class="w-full bg-slate-50 border-slate-100 rounded-2xl p-5 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-200 transition-all shadow-sm">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 uppercase mb-3 block tracking-[0.2em] px-2">Model / Tipe</label>
                                        <input type="text" wire:model.live="editingData.shoe_type" placeholder="Air Force 1, Jordan, etc." class="w-full bg-slate-50 border-slate-100 rounded-2xl p-5 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-200 transition-all shadow-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Row 2: Detail Fisik --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="md:col-span-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase mb-3 block tracking-[0.2em] px-2 text-center md:text-left">Warna Dominan</label>
                                <input type="text" wire:model.live="editingData.shoe_color" placeholder="Putih, Hitam, dsb." class="w-full bg-slate-50 border-slate-100 rounded-2xl p-5 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-200 transition-all shadow-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase mb-3 block tracking-[0.2em] px-2 text-center md:text-left">Ukuran / Size</label>
                                <input type="text" wire:model.live="editingData.shoe_size" placeholder="42, XL, dsb." class="w-full bg-slate-50 border-slate-100 rounded-2xl p-5 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-200 transition-all shadow-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="h-px bg-slate-100 my-8"></div>
                    
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-3">Catatan Internal</p>
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-sm italic text-slate-600 leading-relaxed shadow-inner">
                        "{{ $lead->notes ?: 'Tidak ada catatan khusus untuk lead ini.' }}"
                    </div>

                    {{-- 2. JASA TERPILIH (RINCIAN) --}}
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <h4 class="text-[11px] font-black text-slate-900 uppercase tracking-[0.2em]">Jasa Terpilih</h4>
                            <span class="bg-emerald-100 text-emerald-600 text-[9px] font-black px-3 py-1 rounded-lg uppercase">{{ count($editingData['selected_services'] ?? []) + count($editingData['custom_services'] ?? []) }} Jasa</span>
                        </div>
                        
                        <div class="grid gap-4">
                            @foreach($editingData['selected_services'] ?? [] as $sId)
                                @php $svc = $this->services->find($sId); @endphp
                                @if($svc)
                                <div wire:key="edit-selected-{{ $sId }}" class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:border-emerald-200 transition-all">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-black text-xs">
                                                {{ substr($svc->category, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">{{ $svc->category }}</p>
                                                    <span class="bg-slate-100 text-slate-500 text-[8px] font-black px-2 py-0.5 rounded uppercase leading-none">{{ $svc->hk_days ?? 0 }} HK</span>
                                                </div>
                                                <h5 class="text-sm font-black text-slate-800 uppercase">{{ $svc->name }}</h5>
                                            </div>
                                        </div>
                                        <button wire:click="toggleEditingService({{ $sId }})" class="text-slate-300 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-slate-50">
                                        <div class="col-span-2">
                                            <label class="text-[8px] font-black text-slate-400 uppercase mb-1 block">Detail Pengerjaan (SPK)</label>
                                            <input type="text" wire:model.live="editingData.service_details.{{ $sId }}" placeholder="Instruksi manual..." class="w-full bg-slate-50 border-0 rounded-lg p-3 text-[10px] font-bold focus:ring-2 focus:ring-emerald-500/10">
                                        </div>
                                        <div>
                                            <label class="text-[8px] font-black text-slate-400 uppercase mb-1 block">Harga Final</label>
                                            <div class="flex items-center gap-1 bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                                                <span class="text-[9px] font-black text-slate-300">Rp</span>
                                                <input type="number" wire:model.live="editingData.custom_service_prices.{{ $sId }}" class="w-full bg-transparent border-0 p-0 text-[11px] font-black text-emerald-600 focus:ring-0 text-right">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- 3. KATALOG JASA (FILTER) --}}
                    <div class="space-y-4 pt-4">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <h4 class="text-[11px] font-black text-slate-900 uppercase tracking-[0.2em]">Tambah Jasa Katalog</h4>
                            <div class="flex items-center gap-4 w-full md:w-auto">
                                <div class="relative flex-grow md:flex-grow-0">
                                    <input type="text" wire:model.live.debounce.300ms="editingData.service_search" placeholder="Cari jasa..." class="w-full md:w-48 bg-slate-50 border-slate-100 rounded-xl p-3 pl-10 text-[10px] font-black focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    </div>
                                </div>
                                <div class="flex gap-2 overflow-x-auto pb-2 md:pb-0 custom-scroll">
                                    <button wire:click="$set('editingData.service_category_filter', '')" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase transition-all whitespace-nowrap {{ ($editingData['service_category_filter'] ?? '') == '' ? 'bg-slate-900 text-white shadow-lg' : 'bg-slate-50 text-slate-400 hover:bg-slate-100' }}">Semua</button>
                                    @foreach($this->categories as $cat)
                                        <button wire:click="$set('editingData.service_category_filter', '{{ $cat }}')" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase transition-all whitespace-nowrap {{ ($editingData['service_category_filter'] ?? '') == $cat ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'bg-slate-50 text-slate-400 hover:bg-slate-100' }}">{{ $cat }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[300px] overflow-y-auto pr-2 custom-scroll">
                            @php
                                $filteredServices = $this->services;
                                if(!empty($editingData['service_category_filter'])) {
                                    $filteredServices = $this->services->where('category', $editingData['service_category_filter']);
                                }
                                if(!empty($editingData['service_search'])) {
                                    $searchTerm = strtolower($editingData['service_search']);
                                    $filteredServices = $filteredServices->filter(function($s) use ($searchTerm) {
                                        return str_contains(strtolower($s->name), $searchTerm);
                                    });
                                }
                                $filteredServices = $filteredServices->whereNotIn('id', $editingData['selected_services'] ?? []);
                            @endphp
                            @foreach($filteredServices as $svc)
                                <div wire:key="edit-cat-{{ $svc->id }}" wire:click="toggleEditingService({{ $svc->id }})" class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl border border-transparent hover:border-emerald-200 transition-all cursor-pointer group">
                                    <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-slate-300 group-hover:bg-emerald-50 group-hover:text-emerald-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                                    </div>
                                    <div class="flex-grow min-w-0">
                                        <p class="text-[10px] font-black text-slate-700 truncate uppercase leading-none">{{ $svc->name }}</p>
                                        <p class="text-[9px] font-bold text-emerald-500 mt-1">Rp {{ number_format($svc->price, 0, ',', '.') }} | {{ $svc->hk_days }} HK</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 4. JASA KUSTOM --}}
                    <div class="bg-amber-50/30 rounded-[35px] border border-amber-100/50 p-8 space-y-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="text-[11px] font-black text-amber-600 uppercase tracking-[0.2em]">Jasa Kustom</h4>
                                <p class="text-[8px] font-bold text-amber-400 uppercase mt-1">Gunakan jika jasa tidak ada di katalog</p>
                            </div>
                            <button wire:click="addEditingCustomService" class="bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-black px-6 py-3 rounded-xl shadow-lg shadow-amber-500/20 transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                                TAMBAH JASA KUSTOM
                            </button>
                        </div>

                        <div class="space-y-4">
                            @foreach($editingData['custom_services'] ?? [] as $cIdx => $customSvc)
                                <div wire:key="edit-custom-{{ $cIdx }}" class="bg-white rounded-2xl border border-amber-100 p-5 shadow-sm relative group">
                                    <div class="flex flex-col md:flex-row items-center gap-4 mb-4">
                                        <div class="w-full md:w-1/4">
                                            <label class="text-[8px] font-black text-amber-600 mb-1 block uppercase tracking-widest">Kategori</label>
                                            <select wire:model.live="editingData.custom_services.{{ $cIdx }}.category" class="w-full bg-slate-50 border-0 rounded-lg p-3 text-[10px] font-black focus:ring-2 focus:ring-amber-500/10">
                                                @foreach($this->categories as $cat)
                                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="w-full md:w-2/4">
                                            <label class="text-[8px] font-black text-amber-600 mb-1 block uppercase tracking-widest">Nama Jasa</label>
                                            <input type="text" wire:model.live="editingData.custom_services.{{ $cIdx }}.name" class="w-full bg-slate-50 border-0 rounded-lg p-3 text-[10px] font-black focus:ring-2 focus:ring-amber-500/10">
                                        </div>
                                        <div class="w-full md:w-1/4">
                                            <label class="text-[8px] font-black text-amber-600 mb-1 block uppercase tracking-widest text-right">Harga</label>
                                            <input type="number" wire:model.live="editingData.custom_services.{{ $cIdx }}.price" class="w-full bg-slate-50 border-0 rounded-lg p-3 text-[10px] font-black text-amber-600 focus:ring-2 focus:ring-amber-500/10 text-right">
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex-grow">
                                            <label class="text-[8px] font-black text-amber-600 mb-1 block uppercase tracking-widest">Detail Jasa Kustom</label>
                                            <input type="text" wire:model.live="editingData.custom_services.{{ $cIdx }}.manual_detail" placeholder="Detail instruksi kustom..." class="w-full bg-slate-50/50 border-0 rounded-lg p-3 text-[10px] font-bold">
                                        </div>
                                        <div class="w-20">
                                            <label class="text-[8px] font-black text-amber-600 mb-1 block uppercase tracking-widest">HK</label>
                                            <input type="number" wire:model.live="editingData.custom_services.{{ $cIdx }}.hk_days" class="w-full bg-slate-50 border-0 rounded-lg p-3 text-[10px] font-black text-amber-600 focus:ring-2 focus:ring-amber-500/10 text-center" placeholder="0">
                                        </div>
                                        <div class="pt-4">
                                            <button wire:click="removeEditingCustomService({{ $cIdx }})" class="text-red-300 hover:text-red-500 transition-colors">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1-1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 5. RINGKASAN & NOTES (STACKED LAYOUT) --}}
                    <div class="mt-8 space-y-6 bg-slate-50/50 p-8 rounded-[40px] border border-slate-100">
                        {{-- Row 1: Catatan --}}
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-[0.2em] px-2">Catatan Tambahan</label>
                            <input type="text" wire:model.live.debounce.150ms="editingData.extra_notes" placeholder="Contoh: Prioritas, Barang Titipan, dll..." class="w-full bg-white border border-slate-200 rounded-2xl p-5 text-sm font-bold focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-300 transition-all shadow-sm">
                        </div>

                        {{-- Row 2: HK & Garansi (Dua Kolom) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Total HK --}}
                            <div class="bg-white p-6 rounded-[25px] border border-slate-100 shadow-sm">
                                <label class="text-[10px] font-black uppercase text-slate-400 mb-4 block tracking-[0.2em]">Estimasi Waktu (Total HK)</label>
                                <div class="relative flex items-center gap-4">
                                    <input type="number" 
                                        wire:model.live.debounce.150ms="editingData.hk_days" 
                                        class="w-32 bg-amber-50/50 text-amber-600 border border-slate-100 rounded-xl p-4 text-xl font-black text-center focus:ring-amber-500/20 focus:border-amber-300 transition-all">
                                    
                                    <div class="flex-grow">
                                        <p class="text-[10px] font-black text-slate-400 uppercase leading-tight">Hari Kerja</p>
                                        <p class="text-[9px] font-bold text-emerald-500 mt-1 italic text-wrap">Status: Terbuka (Manual)</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Cover Garansi --}}
                            <div class="bg-white p-6 rounded-[25px] border border-slate-100 shadow-sm">
                                <label class="text-[10px] font-black uppercase text-slate-400 mb-4 block tracking-[0.2em]">Jaminan / Garansi</label>
                                <div class="flex items-center gap-4">
                                    <button wire:click="toggleEditingWarranty" type="button" class="relative inline-flex h-9 w-16 shrink-0 cursor-pointer rounded-xl border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $editingData['is_warranty'] ? 'bg-emerald-500' : 'bg-slate-200' }}">
                                        <span class="pointer-events-none inline-block h-6 w-6 transform rounded-lg bg-white shadow ring-0 transition duration-200 ease-in-out {{ $editingData['is_warranty'] ? 'translate-x-8 mt-1' : 'translate-x-1 mt-1' }}"></span>
                                    </button>
                                    <div class="flex-grow">
                                        <input type="text" wire:model.live.debounce.150ms="editingData.warranty_label" class="w-full bg-transparent border-0 p-0 text-sm font-black text-slate-700 focus:ring-0 placeholder:text-slate-300 uppercase">
                                        <p class="text-[9px] font-bold text-slate-400 mt-1 italic">Klik tombol untuk ganti status</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Row 3: Ringkasan (Full Width) --}}
                        <div class="bg-slate-900 rounded-[30px] p-6 shadow-xl relative overflow-hidden">
                            {{-- Decorative Background --}}
                            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl"></div>
                            
                            <label class="text-[9px] font-black uppercase text-emerald-400/50 mb-3 block tracking-[0.3em] relative z-10">Ringkasan SPK Item</label>
                            <div class="flex items-center gap-4 relative z-10">
                                <div class="bg-emerald-500/20 p-3 rounded-2xl text-emerald-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <h4 class="text-lg font-black text-white tracking-tight uppercase italic leading-tight">
                                    {{ $this->calculateEditingSummary() ?: 'BELUM ADA JASA TERPILIH' }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-10 border-t border-slate-100 bg-white flex flex-col md:flex-row justify-between items-center gap-8">
                    <div>
                        @php 
                            $editTotal = collect($editingData['custom_service_prices'] ?? [])->sum() + collect($editingData['custom_services'] ?? [])->sum('price');
                        @endphp
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Total Nilai Item</p>
                        <p class="text-4xl font-black text-emerald-600 font-display tracking-tighter">Rp {{ number_format($editTotal, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex gap-6 items-center">
                        <button wire:click="$set('showEditItemModal', false)" class="text-sm font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">Batal</button>
                        <button wire:click="updateItem" class="btn-action px-16 py-6 text-lg rounded-[25px]">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- 3. GENERATE SPK MODAL (Bahasa Indonesia) --}}
        @if($showSpkModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xl">
            <div class="glass-panel bg-white/95 w-full max-w-6xl max-h-[95vh] overflow-hidden flex flex-col shadow-2xl border-white">
                 <div class="p-10 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div class="flex items-center gap-6">
                        <div>
                            <h2 class="text-4xl font-black font-display text-slate-900 tracking-tight leading-none">Finalisasi SPK</h2>
                            <p class="text-sm text-slate-500 font-medium mt-2">Pastikan data pengiriman dan jasa sudah sesuai.</p>
                        </div>
                        <div class="h-16 w-px bg-slate-100 mx-2"></div>
                        <div class="bg-slate-900 px-8 py-4 rounded-[25px] shadow-2xl shadow-slate-900/20 border border-slate-800 animate-in fade-in slide-in-from-right-4 duration-500">
                            <p class="text-[9px] font-black text-emerald-400 uppercase tracking-[0.3em] mb-1 leading-none">PRATINJAU NOMOR SPK</p>
                            <p class="text-2xl font-black text-white font-display tracking-tight">{{ $this->currentSpkNumber }}</p>
                        </div>
                    </div>
                    <button wire:click="$set('showSpkModal', false)" class="w-14 h-14 bg-white text-slate-400 rounded-2xl shadow-sm hover:text-red-500 transition-all flex items-center justify-center group">
                        <svg class="w-7 h-7 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l18 18" /></svg>
                    </button>
                </div>

                <div class="flex-grow overflow-y-auto p-10 lg:p-14 custom-scroll">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                        {{-- Form Kiri --}}
                        <div class="space-y-10">
                            <div>
                                <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6 border-b border-slate-100 pb-2">Informasi Pengiriman</h4>
                                <div class="grid grid-cols-2 gap-6">
                                    <div class="col-span-2">
                                        <label class="text-[10px] font-black uppercase text-slate-500 mb-2 block tracking-widest">Metode Pengiriman</label>
                                        <select wire:model.live="spkData.delivery_type" class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 appearance-none shadow-sm @error('spkData.delivery_type') ring-2 ring-red-500 @enderror">
                                            <option value="">-- Pilih Metode --</option>
                                            <option value="Offline">Offline (Ambil Sendiri)</option>
                                            <option value="Online">Online (Ekspedisi)</option>
                                            <option value="Pickup">Pickup (Kurir Workshop)</option>
                                            <option value="Ojol">Ojol (Grab/Gojek)</option>
                                        </select>
                                        @error('spkData.delivery_type') <span class="text-[10px] text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-[10px] font-black uppercase text-slate-500 mb-2 block tracking-widest">Kode CS (Manual)</label>
                                        <input type="text" wire:model="spkData.manual_cs_code" placeholder="cth: SWY" class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 shadow-sm @error('spkData.manual_cs_code') ring-2 ring-red-500 @enderror">
                                        @error('spkData.manual_cs_code') <span class="text-[10px] text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-span-2">
                                        <label class="text-[10px] font-black uppercase text-slate-500 mb-2 block tracking-widest">Alamat Lengkap</label>
                                        <textarea wire:model="spkData.customer_address" rows="3" placeholder="Masukkan alamat lengkap pengiriman..." class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 shadow-sm resize-none"></textarea>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black uppercase text-slate-500 mb-2 block tracking-widest">Kota</label>
                                        <input type="text" wire:model="spkData.customer_city" placeholder="cth: Bandung" class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 shadow-sm">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black uppercase text-slate-500 mb-2 block tracking-widest">Provinsi</label>
                                        <input type="text" wire:model="spkData.customer_province" placeholder="cth: Jawa Barat" class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 shadow-sm">
                                    </div>
                                    <div class="col-span-2 mt-4">
                                        <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6 border-b border-slate-100 pb-2">Informasi Pengerjaan</h4>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black uppercase text-slate-500 mb-2 block tracking-widest">Prioritas Kerja</label>
                                        <select wire:model="spkData.priority" class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 shadow-sm">
                                            <option value="NORMAL">NORMAL</option>
                                            <option value="Urgensi">URGENSI (FLASH)</option>
                                            <option value="PRIORITAS">PRIORITAS</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black uppercase text-slate-500 mb-2 block tracking-widest">Estimasi Selesai (Opsional)</label>
                                        <input type="date" wire:model="spkData.expected_delivery_date" class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 shadow-sm">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6 border-b border-slate-100 pb-2">Logika Pembayaran</h4>
                                <div class="bg-emerald-500 p-8 rounded-[40px] shadow-2xl shadow-emerald-500/30">
                                    <div class="flex justify-between items-center mb-6">
                                        <span class="text-sm font-black text-white uppercase tracking-widest">Uang Muka (DP)</span>
                                        <div class="relative w-1/2">
                                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600 font-black text-sm">Rp</span>
                                            <input type="number" wire:model="spkData.dp_amount" class="w-full bg-white border-0 rounded-2xl py-4 pl-12 pr-5 text-right font-black text-emerald-700 focus:ring-4 focus:ring-white/20">
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-black text-white uppercase tracking-widest">Kode Promo</span>
                                        <input type="text" wire:model="spkData.promo_code" placeholder="Gunakan kode promo..." class="w-1/2 bg-white/20 border-0 rounded-2xl py-4 px-6 text-right font-black text-white placeholder:text-white/40 focus:ring-4 focus:ring-white/20">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Kanan: Barang --}}
                        <div>
                            <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6 border-b border-slate-100 pb-2">Konfirmasi Barang</h4>
                            <div class="space-y-6 max-h-[550px] overflow-y-auto pr-4 custom-scroll">
                                @php $latestQuotation = $lead->getLatestQuotation(); @endphp
                                @foreach($latestQuotation?->quotationItems ?? [] as $qItem)
                                    <div class="bg-white border border-slate-100 rounded-[35px] p-8 shadow-xl shadow-slate-200/20 group hover:border-emerald-500/30 transition-all">
                                        <div class="flex justify-between items-start mb-6">
                                            <div>
                                                <h5 class="text-xl font-black text-slate-900 font-display">{{ $qItem->shoe_brand }} {{ $qItem->shoe_type }}</h5>
                                                <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mt-1">{{ $qItem->category }} | {{ $qItem->shoe_color }} ({{ $qItem->shoe_size }})</p>
                                            </div>
                                            @if($qItem->is_warranty)
                                                <span class="bg-emerald-100 text-emerald-600 text-[8px] font-black px-2 py-1 rounded-lg uppercase tracking-widest">Garansi</span>
                                            @endif
                                        </div>

                                        <div class="mb-6">
                                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Jasa Terpilih</label>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($qItem->services ?? [] as $svc)
                                                    <span class="bg-slate-900 text-white text-[9px] font-black px-4 py-1.5 rounded-xl tracking-tight shadow-lg shadow-slate-900/10">{{ strtoupper($svc['name']) }}</span>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="flex justify-between items-end pt-6 border-t border-slate-50">
                                            <div class="flex gap-8">
                                                <div class="flex flex-col">
                                                    <span class="text-[9px] font-black text-slate-400 uppercase">HARI KERJA</span>
                                                    <span class="text-sm font-black text-slate-800">{{ $qItem->hk_days }} HK</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-[9px] font-black text-slate-400 uppercase">SUBTOTAL</span>
                                                    <span class="text-sm font-black text-emerald-600 font-display">Rp {{ number_format($qItem->item_total_price, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-10 lg:p-14 border-t border-slate-100 bg-white flex flex-col md:flex-row justify-between items-center gap-10">
                    <div class="flex gap-16 items-center">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase leading-none mb-2 tracking-widest">Total Tagihan</p>
                            <p class="text-5xl font-black text-slate-900 font-display tracking-tighter">Rp {{ number_format($this->totalQuotationValue - $spkData['discount_amount'], 0, ',', '.') }}</p>
                        </div>
                        @if($spkData['dp_amount'] > 0)
                            <div class="h-12 w-px bg-slate-100 hidden md:block"></div>
                            <div>
                                <p class="text-[10px] font-black text-emerald-600 uppercase leading-none mb-2 tracking-widest">Sisa Pembayaran</p>
                                <p class="text-5xl font-black text-emerald-600 font-display tracking-tighter">Rp {{ number_format($this->totalQuotationValue - $spkData['discount_amount'] - $spkData['dp_amount'], 0, ',', '.') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="flex gap-8 items-center">
                        <button wire:click="$set('showSpkModal', false)" class="text-sm font-black text-slate-400 uppercase tracking-widest hover:text-red-500 transition-colors">Batal</button>
                        <button wire:click="generateSpk" class="btn-action px-20 py-7 text-xl rounded-[30px] shadow-2xl shadow-emerald-500/30">
                            Terbitkan SPK Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- 4. HANDOVER MODAL (Serah Terima Workshop) --}}
        @if($showHandoverModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-2xl">
            <div class="glass-panel bg-white w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl border-white animate-in zoom-in duration-300 rounded-[40px]">
                <div class="p-10 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h2 class="text-3xl font-black font-display text-slate-900 tracking-tight uppercase">Serah Terima Workshop</h2>
                        <p class="text-sm text-slate-500 font-medium">Dokumentasikan barang sebelum masuk ke lini produksi.</p>
                    </div>
                    <button wire:click="$set('showHandoverModal', false)" class="w-14 h-14 bg-slate-100 text-slate-400 rounded-2xl hover:bg-red-500 hover:text-white transition-all flex items-center justify-center group">
                        <svg class="w-6 h-6 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="flex-grow overflow-y-auto p-10 space-y-10 custom-scroll bg-slate-50/30">
                    @foreach($handoverItems as $itemId => $item)
                        <div wire:key="handover-{{ $itemId }}" class="bg-white p-8 rounded-[40px] shadow-sm border border-slate-100">
                            <div class="flex items-center gap-6 mb-8">
                                <div class="w-14 h-14 bg-slate-900 text-white rounded-2xl flex items-center justify-center font-black text-xl shadow-lg">
                                    {{ $loop->iteration }}
                                </div>
                                <div>
                                    <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ $item['shoe_brand'] }} {{ $item['shoe_type'] }}</h4>
                                    <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mt-1">{{ $item['category'] }} • {{ $item['shoe_color'] }}</p>
                                    
                                    {{-- Display services for confirmation --}}
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @php 
                                            $spkItem = $lead->spk->items->where('id', $itemId)->first();
                                        @endphp
                                        @if($spkItem)
                                            @foreach($spkItem->services as $service)
                                                <span class="px-3 py-1 bg-slate-100 rounded-lg text-[9px] font-bold text-slate-500 border border-slate-200">
                                                    {{ $service['name'] }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-6">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest">Brand Konfirmasi</label>
                                            <input type="text" wire:model="handoverItems.{{ $itemId }}.shoe_brand" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 @error('handoverItems.'.$itemId.'.shoe_brand') ring-2 ring-red-500 @enderror">
                                            @error('handoverItems.'.$itemId.'.shoe_brand') <p class="text-[9px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest">Model Konfirmasi</label>
                                            <input type="text" wire:model="handoverItems.{{ $itemId }}.shoe_type" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm font-black focus:ring-4 focus:ring-emerald-500/10 @error('handoverItems.'.$itemId.'.shoe_type') ring-2 ring-red-500 @enderror">
                                            @error('handoverItems.'.$itemId.'.shoe_type') <p class="text-[9px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest">Jenis Item (Prefix SPK)</label>
                                        <select wire:model="handoverItems.{{ $itemId }}.item_type" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm font-black focus:ring-4 focus:ring-emerald-500/10">
                                            <option value="Sepatu">Sepatu (Prefix S)</option>
                                            <option value="Tas">Tas (Prefix T)</option>
                                            <option value="Headwear">Headwear / Topi / Helm (Prefix H)</option>
                                            <option value="Apparel">Apparel / Jaket / Baju (Prefix A)</option>
                                            <option value="Lainnya">Lainnya (Prefix L)</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Foto Referensi (Opsional)</label>
                                    <div class="flex flex-wrap gap-3">
                                        @if(!empty($handoverItems[$itemId]['ref_photos']))
                                            @foreach($handoverItems[$itemId]['ref_photos'] as $phIdx => $photo)
                                                <div class="relative w-24 h-24 rounded-2xl overflow-hidden border-2 border-emerald-500">
                                                    <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        <label class="w-24 h-24 rounded-2xl bg-slate-100 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center cursor-pointer hover:bg-emerald-50 hover:border-emerald-200 transition-all group">
                                            <svg class="w-6 h-6 text-slate-300 group-hover:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                                            <span class="text-[8px] font-black text-slate-400 uppercase mt-1">Tambah Foto</span>
                                            <input type="file" wire:model="handoverItems.{{ $itemId }}.ref_photos" multiple class="hidden">
                                        </label>
                                    </div>
                                    @error('handoverItems.' . $itemId . '.ref_photos') <p class="text-[10px] text-red-500 font-bold mt-2">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-10 border-t border-slate-100 bg-white flex justify-between items-center">
                    <button wire:click="$set('showHandoverModal', false)" class="text-sm font-black text-slate-400 uppercase tracking-widest hover:text-red-500 transition-colors">Batal</button>
                    <button wire:click="submitHandover" class="btn-action px-20 py-7 text-xl rounded-[30px] shadow-2xl shadow-emerald-500/30">
                        Konfirmasi & Kirim ke Produksi
                        <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

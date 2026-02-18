<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Workshop - {{ $workOrder->spk_number }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <!-- Tailwind (Playful but Premium) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #F8FAFC; }
        .bg-workshop-green { background-color: #22AF85; }
        .text-workshop-green { color: #22AF85; }
        .bg-workshop-yellow { background-color: #FFC232; }
        .border-workshop-green { border-color: #22AF85; }
        
        /* Glassmorphism subtle */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* Prevent image context menu for security */
        img { pointer-events: none; -webkit-user-select: none; user-select: none; }
    </style>
</head>
<body class="antialiased text-slate-800">

    <!-- Header / Brand -->
    <header class="bg-workshop-green text-white pb-24 pt-10 px-6 rounded-b-[40px] shadow-lg relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 sm:w-64 sm:h-64 sm:-mr-32 sm:-mt-32"></div>
        
        <div class="max-w-md mx-auto relative z-10 text-center">
            <p class="text-[10px] uppercase tracking-[4px] font-bold opacity-80 mb-2">After-Service Report</p>
            <h1 class="text-3xl font-extrabold tracking-tight">KUALITAS TERJAMIN</h1>
            <p class="mt-2 text-sm font-medium opacity-90">Sepatu kakak sudah siap tampil beda!</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-md mx-auto px-5 -mt-16 pb-12 relative z-20">
        
        <!-- SPK Card -->
        <div class="glass-card rounded-3xl p-6 shadow-xl mb-6 flex justify-between items-center">
            <div>
                <p class="text-[10px] uppercase tracking-widest font-extrabold text-workshop-green mb-1">Nomor SPK</p>
                <h2 class="text-xl font-bold">{{ $workOrder->spk_number }}</h2>
            </div>
            <div class="bg-workshop-yellow px-4 py-2 rounded-2xl shadow-inner font-black text-slate-800 text-sm">
                {{ $workOrder->status->label() }}
            </div>
        </div>

        <!-- Hero Photo (First available FINISH photo) -->
        <div class="mb-8 group">
            <h3 class="text-lg font-extrabold mb-4 px-1 border-l-4 border-workshop-green ml-1">📸 Foto Hasil Akhir</h3>
            
            <div class="grid grid-cols-1 gap-6">
                @forelse($photos as $photo)
                    <div class="glass-card rounded-[2.5rem] overflow-hidden shadow-2xl transition-transform duration-500 hover:scale-[1.02]">
                        <div class="bg-slate-50 p-2">
                             <!-- Show URL or placeholder -->
                             <img src="{{ asset('storage/' . $photo->file_path) }}" 
                                  alt="Finish Photo" 
                                  class="w-full aspect-square object-cover rounded-[2rem] shadow-inner bg-slate-200">
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="bg-workshop-yellow w-8 h-8 rounded-full flex items-center justify-center font-black text-xs">#{{ $loop->iteration }}</span>
                                <p class="text-sm font-bold opacity-80 uppercase tracking-widest text-[11px]">{{ $photo->caption ?: 'DOKUMENTASI WORKSHOP' }}</p>
                            </div>
                            <p class="text-[10px] text-slate-400 font-medium ml-11">DIAMBIL PADA {{ $photo->created_at->format('d M Y - H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center glass-card rounded-3xl">
                        <p class="text-slate-400 font-medium italic">Belum ada foto dokumentasi tersedia.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Information Summary -->
        <div class="glass-card rounded-3xl p-8 shadow-lg mb-8">
            <h3 class="text-base font-extrabold mb-6 flex items-center gap-2">
                <span class="w-2 h-6 bg-workshop-yellow rounded-full"></span>
                Ringkasan Order
            </h3>

            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <div class="bg-slate-100 p-3 rounded-2xl">👤</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nama Customer</p>
                        <p class="font-bold text-lg">{{ $workOrder->customer_name }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="bg-slate-100 p-3 rounded-2xl">👟</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Brand / Model</p>
                        <p class="font-bold text-lg">{{ $workOrder->shoe_brand ?: '-' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-4 border-t pt-6">
                    <div class="bg-slate-100 p-3 rounded-2xl">✨</div>
                    <div class="flex-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Layanan Kami</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($workOrder->workOrderServices as $service)
                                <span class="bg-workshop-green/10 text-workshop-green text-[10px] font-extrabold px-3 py-1.5 rounded-xl border border-workshop-green/20">
                                    {{ $service->custom_service_name ?? ($service->service->name ?? '-') }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer / Call to Action -->
        <div class="text-center px-4">
            <p class="text-sm font-medium text-slate-400 mb-6">Punya pertanyaan mengenai hasil pengerjaan?</p>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('app.contact_whatsapp', '628123456789')) }}" 
               class="bg-workshop-green text-white block w-full py-5 rounded-[2rem] font-extrabold text-lg shadow-xl hover:shadow-2xl hover:scale-[1.01] transition-all transform active:scale-95">
                HUBUNGI CUSTOMER SERVICE
            </a>
            
            <p class="mt-10 text-[10px] font-bold text-slate-300 uppercase tracking-[4px]">
                Powered by ShoeWorkshop.id
            </p>
        </div>

    </main>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Laporan Hasil - {{ $workOrder->spk_number }} | ShoeWorkshop</title>
    <meta name="description" content="Laporan hasil pengerjaan sepatu untuk SPK {{ $workOrder->spk_number }}">
    <meta name="robots" content="noindex, nofollow">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Alpine.js for Lightbox --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green-primary: #1A9E74;
            --green-dark: #147A59;
            --green-light: #E8F5EF;
            --gold: #F5A623;
            --gold-light: #FFF8EC;
            --slate-50: #F8FAFC;
            --slate-100: #F1F5F9;
            --slate-200: #E2E8F0;
            --slate-300: #CBD5E1;
            --slate-400: #94A3B8;
            --slate-500: #64748B;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1E293B;
            --slate-900: #0F172A;
            --radius-lg: 20px;
            --radius-xl: 28px;
            --shadow-card: 0 4px 24px rgba(0,0,0,0.06);
            --shadow-elevated: 0 12px 40px rgba(0,0,0,0.12);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--slate-50);
            color: var(--slate-800);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* ═══ HEADER ═══ */
        .hero-header {
            background: linear-gradient(135deg, var(--green-primary) 0%, var(--green-dark) 100%);
            color: white;
            padding: 48px 24px 80px;
            position: relative;
            overflow: hidden;
        }
        .hero-header::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 200px; height: 200px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }
        .hero-header::after {
            content: '';
            position: absolute;
            bottom: -40px; left: -40px;
            width: 140px; height: 140px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .hero-inner {
            max-width: 480px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            text-align: center;
        }
        .hero-badge {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            opacity: 0.7;
            margin-bottom: 12px;
        }
        .hero-title {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.2;
            margin-bottom: 8px;
        }
        .hero-subtitle {
            font-size: 14px;
            font-weight: 400;
            opacity: 0.85;
        }

        /* ═══ MAIN CONTAINER ═══ */
        .main-container {
            max-width: 480px;
            margin: -48px auto 0;
            padding: 0 16px 48px;
            position: relative;
            z-index: 10;
        }

        /* ═══ SPK CARD ═══ */
        .spk-card {
            background: white;
            border-radius: var(--radius-xl);
            padding: 20px 24px;
            box-shadow: var(--shadow-elevated);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        .spk-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--green-primary);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .spk-number {
            font-size: 18px;
            font-weight: 800;
            color: var(--slate-900);
        }
        .status-badge {
            background: var(--gold);
            color: var(--slate-900);
            font-size: 11px;
            font-weight: 800;
            padding: 8px 16px;
            border-radius: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ═══ SECTION HEADERS ═══ */
        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        .section-header .bar {
            width: 4px;
            height: 24px;
            background: var(--green-primary);
            border-radius: 4px;
        }
        .section-header h3 {
            font-size: 16px;
            font-weight: 800;
            color: var(--slate-900);
        }

        /* ═══ PHOTO GRID ═══ */
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 32px;
        }
        .photo-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-card);
            cursor: pointer;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
            position: relative;
        }
        .photo-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: var(--shadow-elevated);
        }
        .photo-card:active {
            transform: scale(0.97);
        }
        .photo-card .photo-wrapper {
            position: relative;
            padding-top: 100%; /* 1:1 Aspect Ratio */
            overflow: hidden;
            background: var(--slate-100);
        }
        .photo-card .photo-wrapper img {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .photo-card:hover .photo-wrapper img {
            transform: scale(1.08);
        }
        .photo-card .photo-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, transparent 100%);
            padding: 24px 12px 10px;
            pointer-events: none;
        }
        .photo-card .photo-number {
            position: absolute;
            top: 10px; left: 10px;
            background: var(--gold);
            color: var(--slate-900);
            width: 28px; height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            z-index: 2;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .photo-card .zoom-icon {
            position: absolute;
            bottom: 10px; right: 10px;
            background: rgba(255,255,255,0.9);
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .photo-card .zoom-icon svg {
            width: 16px; height: 16px;
            color: var(--slate-600);
        }

        /* First photo spans full width */
        .photo-grid .photo-card:first-child {
            grid-column: 1 / -1;
        }
        .photo-grid .photo-card:first-child .photo-wrapper {
            padding-top: 65%;
        }

        /* ═══ EMPTY STATE ═══ */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-card);
            margin-bottom: 32px;
        }
        .empty-state .icon { font-size: 48px; margin-bottom: 16px; }
        .empty-state p {
            color: var(--slate-400);
            font-weight: 500;
            font-style: italic;
        }

        /* ═══ INFO CARD ═══ */
        .info-card {
            background: white;
            border-radius: var(--radius-xl);
            padding: 28px;
            box-shadow: var(--shadow-card);
            margin-bottom: 24px;
        }
        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 16px 0;
        }
        .info-row + .info-row {
            border-top: 1px solid var(--slate-100);
        }
        .info-icon {
            width: 44px; height: 44px;
            background: var(--slate-50);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .info-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
        }
        .info-value {
            font-size: 16px;
            font-weight: 700;
            color: var(--slate-800);
        }

        /* ═══ SERVICE TAGS ═══ */
        .service-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 4px;
        }
        .service-tag {
            background: var(--green-light);
            color: var(--green-primary);
            font-size: 11px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 12px;
            border: 1px solid rgba(26, 158, 116, 0.15);
        }

        /* ═══ CTA SECTION ═══ */
        .cta-section { text-align: center; margin-top: 8px; }
        .cta-label {
            font-size: 13px;
            color: var(--slate-400);
            font-weight: 500;
            margin-bottom: 16px;
        }
        .cta-button {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, var(--green-primary), var(--green-dark));
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 700;
            padding: 18px;
            border-radius: var(--radius-lg);
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(26, 158, 116, 0.35);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(26, 158, 116, 0.45);
        }
        .cta-button:active { transform: scale(0.98); }

        .footer-brand {
            margin-top: 40px;
            font-size: 9px;
            font-weight: 700;
            color: var(--slate-300);
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        /* ═══ LIGHTBOX ═══ */
        .lightbox-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.92);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .lightbox-backdrop.active { opacity: 1; }

        .lightbox-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            flex-shrink: 0;
        }
        .lightbox-counter {
            color: rgba(255,255,255,0.7);
            font-size: 13px;
            font-weight: 600;
        }
        .lightbox-close {
            width: 40px; height: 40px;
            background: rgba(255,255,255,0.1);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .lightbox-close:hover { background: rgba(255,255,255,0.2); }
        .lightbox-close:focus { outline: 2px solid var(--gold); }

        .lightbox-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 20px 8px;
            overflow: hidden;
            touch-action: pan-y pinch-zoom;
        }
        .lightbox-body img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            user-select: none;
            -webkit-user-drag: none;
            pointer-events: auto;
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 44px; height: 44px;
            background: rgba(255,255,255,0.12);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 22px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
            z-index: 10;
        }
        .lightbox-nav:hover { background: rgba(255,255,255,0.25); }
        .lightbox-nav.prev { left: 12px; }
        .lightbox-nav.next { right: 12px; }

        .lightbox-footer {
            padding: 16px 20px 32px;
            text-align: center;
            flex-shrink: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        }
        .lightbox-caption {
            color: rgba(255,255,255,0.95);
            font-size: 14px;
            font-weight: 700;
        }
        .lightbox-date {
            color: rgba(255,255,255,0.5);
            font-size: 11px;
            font-weight: 500;
            margin-top: 4px;
            margin-bottom: 20px;
        }

        /* ═══ LIGHTBOX ACTIONS ═══ */
        .lightbox-actions {
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: 10px;
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
        }

        .lightbox-action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
        }

        .lightbox-action-btn svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .lightbox-action-btn.download {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding-left: 12px;
            padding-right: 12px;
        }

        .lightbox-action-btn.inquiry {
            background: #25D366;
            color: white;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
            flex: 1; /* Make inquiry slightly wider than download */
            max-width: 250px;
        }

        .lightbox-action-btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        .lightbox-action-btn:active {
            transform: scale(0.96);
        }

        /* ═══ CTA SECTION FIX ═══ */
        .wa-cta-btn svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        /* ═══ ANIMATIONS ═══ */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }

        /* ═══ RESPONSIVE: Tablet & Desktop ═══ */
        @media (min-width: 640px) {
            .hero-title { font-size: 36px; }
            .main-container { max-width: 560px; padding: 0 24px 60px; }
            .photo-grid { gap: 16px; }
        }

        @media (min-width: 1024px) {
            .main-container { max-width: 640px; }
            .hero-header { padding: 60px 32px 96px; }
            .main-container { margin-top: -56px; }
        }
    </style>
</head>
<body>

    {{-- ═══ HEADER ═══ --}}
    <header class="hero-header">
        <div class="hero-inner">
            <span class="hero-badge">After-Service Report</span>
            <h1 class="hero-title">Sepatu Kakak<br>Sudah Siap! ✨</h1>
            <p class="hero-subtitle">Berikut dokumentasi hasil pengerjaan dari tim workshop kami</p>
        </div>
    </header>

    {{-- ═══ MAIN CONTENT ═══ --}}
    <main class="main-container" 
          x-data="photoLightbox()" 
          @keydown.escape.window="close()" 
          @keydown.left.window="prev()" 
          @keydown.right.window="next()">
        
        {{-- SPK Card --}}
        <div class="spk-card animate-in delay-1">
            <div>
                <p class="spk-label">Nomor SPK</p>
                <p class="spk-number">{{ $workOrder->spk_number }}</p>
            </div>
            <span class="status-badge">{{ $workOrder->status->label() }}</span>
        </div>

        {{-- Info Card --}}
        <div class="info-card animate-in delay-2">
            <div class="section-header" style="margin-bottom:16px;">
                <span class="bar" style="background:var(--gold);"></span>
                <h3>Ringkasan Order</h3>
            </div>

            <div class="info-row">
                <div class="info-icon">👤</div>
                <div>
                    <p class="info-label">Nama Customer</p>
                    <p class="info-value">{{ $workOrder->customer_name }}</p>
                </div>
            </div>

            <div class="info-row">
                <div class="info-icon">👟</div>
                <div>
                    <p class="info-label">Brand / Model</p>
                    <p class="info-value">{{ $workOrder->shoe_brand ?: '-' }}</p>
                </div>
            </div>

            <div class="info-row">
                <div class="info-icon">✨</div>
                <div style="flex:1;">
                    <p class="info-label">Layanan</p>
                    <div class="service-tags">
                        @foreach($workOrder->workOrderServices as $service)
                            <span class="service-tag">
                                {{ $service->custom_service_name ?? ($service->service->name ?? '-') }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Photo Section --}}
        <div class="animate-in delay-3">
            <div class="section-header">
                <span class="bar"></span>
                <h3>📸 Foto Hasil Akhir</h3>
            </div>

            @if($photos->count() > 0)
                <div class="photo-grid">
                    @foreach($photos as $photo)
                        @php
                            $filePath = $photo->file_path;
                            if (str_starts_with($filePath, 'http')) {
                                $imgSrc = $filePath;
                            } else {
                                $imgSrc = asset('storage/' . $filePath);
                            }
                        @endphp
                        <div class="photo-card" @click="open({{ $loop->index }})">
                            <span class="photo-number">{{ $loop->iteration }}</span>
                            <div class="photo-wrapper">
                                <img src="{{ $imgSrc }}" 
                                     alt="Foto Finish #{{ $loop->iteration }}" 
                                     loading="lazy">
                                <div class="photo-overlay"></div>
                            </div>
                            <span class="zoom-icon">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                </svg>
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="icon">📷</div>
                    <p>Belum ada foto dokumentasi tersedia.</p>
                </div>
            @endif
        </div>

        {{-- CTA --}}
        <div class="cta-section animate-in delay-4">
            <p class="cta-label">Punya pertanyaan mengenai hasil pengerjaan?</p>
            <a href="https://wa.me/62895339939800?text={{ urlencode('Halo Admin ShoeWorkshop, saya ingin bertanya tentang SPK ' . $workOrder->spk_number) }}" 
               target="_blank" 
               class="wa-cta-btn">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.413-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.654zm6.236-3.361c1.556.924 3.084 1.411 4.708 1.411 5.452 0 9.888-4.435 9.891-9.886.003-5.452-4.432-9.887-9.895-9.887-5.451 0-9.888 4.435-9.891 9.886l-.001 2.233 1.268 3.313 1.488 1.29 2.432 1.64zm11.751-6.901c-.139-.232-.511-.348-1.069-.626-.557-.279-2.593-1.28-2.966-1.42-.372-.139-.643-.209-.916.209-.271.418-.51 1.063-.51 1.063s-.186.232-.511.116c-.328-.119-1.383-.511-2.636-1.626-1.071-.954-1.782-2.126-1.995-2.521-.213-.394-.023-.607.174-.804.177-.176.395-.464.593-.695.197-.232.261-.397.394-.664.133-.267.067-.502-.034-.734-.1-.233-.916-2.203-1.256-3.016-.33-.799-.664-.691-.916-.703l-.782-.014c-.27 0-.712.102-1.084.512-.371.41-.418.819-1.418 2.302-.999 1.483-2.184 2.919-2.184 2.919s.139 1.486 1.486 3.129c1.347 1.642 2.646 3.238 2.646 3.238s.229.344.59.131c.361-.213 1.579-.918 2.103-1.41 1.144-1.076 1.109-1.146 1.109-1.146s.418-.139.789-.046c.371.093 2.502 1.21 2.502 1.21s.373.186.418.42c.045.234.045 1.348-.511 2.279z"/>
                </svg>
                Chat Admin via WhatsApp
            </a>

            <p class="footer-brand">Powered by ShoeWorkshop.id</p>
        </div>

        {{-- ═══ FULLSCREEN LIGHTBOX ═══ --}}
        <template x-if="isOpen">
            <div class="lightbox-backdrop" 
                 :class="{ 'active': isVisible }"
                 @click.self="close()">
                
                {{-- Header --}}
                <div class="lightbox-header">
                    <span class="lightbox-counter" x-text="`Foto ${currentIndex + 1} dari ${photos.length}`"></span>
                    <button class="lightbox-close" @click="close()" aria-label="Tutup">✕</button>
                </div>

                {{-- Image --}}
                <div class="lightbox-body">
                    <button class="lightbox-nav prev" @click.stop="prev()" x-show="photos.length > 1" aria-label="Sebelumnya">‹</button>
                    <img :src="photos[currentIndex]?.src" :alt="photos[currentIndex]?.alt">
                    <button class="lightbox-nav next" @click.stop="next()" x-show="photos.length > 1" aria-label="Selanjutnya">›</button>
                </div>

                {{-- Footer --}}
                <div class="lightbox-footer">
                    <p class="lightbox-caption" x-text="photos[currentIndex]?.caption"></p>
                    <p class="lightbox-date" x-text="photos[currentIndex]?.date"></p>
                    
                    <div class="lightbox-actions">
                        <button @click="downloadPhoto()" class="lightbox-action-btn download" title="Simpan Foto">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            SIMPAN
                        </button>

                        <a :href="getWhatsAppLink()" 
                           target="_blank"
                           class="lightbox-action-btn inquiry">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.413-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.654zm6.236-3.361c1.556.924 3.084 1.411 4.708 1.411 5.452 0 9.888-4.435 9.891-9.886.003-5.452-4.432-9.887-9.895-9.887-5.451 0-9.888 4.435-9.891 9.886l-.001 2.233 1.268 3.313 1.488 1.29 2.432 1.64zm11.751-6.901c-.139-.232-.511-.348-1.069-.626-.557-.279-2.593-1.28-2.966-1.42-.372-.139-.643-.209-.916.209-.271.418-.51 1.063-.51 1.063s-.186.232-.511.116c-.328-.119-1.383-.511-2.636-1.626-1.071-.954-1.782-2.126-1.995-2.521-.213-.394-.023-.607.174-.804.177-.176.395-.464.593-.695.197-.232.261-.397.394-.664.133-.267.067-.502-.034-.734-.1-.233-.916-2.203-1.256-3.016-.33-.799-.664-.691-.916-.703l-.782-.014c-.27 0-.712.102-1.084.512-.371.41-.418.819-1.418 2.302-.999 1.483-2.184 2.919-2.184 2.919s.139 1.486 1.486 3.129c1.347 1.642 2.646 3.238 2.646 3.238s.229.344.59.131c.361-.213 1.579-.918 2.103-1.41 1.144-1.076 1.109-1.146 1.109-1.146s.418-.139.789-.046c.371.093 2.502 1.21 2.502 1.21s.373.186.418.42c.045.234.045 1.348-.511 2.279z"/>
                            </svg>
                            TANYA MEGENAI FOTO
                        </a>
                    </div>

                </div>
            </div>
        </template>
    </main>

    {{-- ═══ ALPINE.JS LIGHTBOX LOGIC ═══ --}}
    <script>
        function photoLightbox() {
            return {
                isOpen: false,
                isVisible: false,
                currentIndex: 0,
                photos: [
                    @foreach($photos as $photo)
                    {
                        src: "{{ str_starts_with($photo->file_path, 'http') ? $photo->file_path : asset('storage/' . $photo->file_path) }}",
                        alt: "Foto Finish #{{ $loop->iteration }}",
                        caption: "{{ $photo->caption ?: 'Dokumentasi Workshop' }}",
                        date: "{{ $photo->created_at->format('d M Y • H:i') }}"
                    },
                    @endforeach
                ],
                open(index) {
                    this.currentIndex = index;
                    this.isOpen = true;
                    document.body.style.overflow = 'hidden';
                    requestAnimationFrame(() => { this.isVisible = true; });
                },
                close() {
                    this.isVisible = false;
                    setTimeout(() => {
                        this.isOpen = false;
                        document.body.style.overflow = '';
                    }, 300);
                },
                prev() {
                    if (!this.isOpen) return;
                    this.currentIndex = (this.currentIndex - 1 + this.photos.length) % this.photos.length;
                },
                next() {
                    if (!this.isOpen) return;
                    this.currentIndex = (this.currentIndex + 1) % this.photos.length;
                },
                getWhatsAppLink() {
                    const phone = "62895339939800";
                    const spk = "{{ $workOrder->spk_number }}";
                    const photoNum = this.currentIndex + 1;
                    const photoUrl = this.photos[this.currentIndex]?.src || '';
                    const caption = this.photos[this.currentIndex]?.caption || 'Dokumentasi Workshop';
                    
                    // Placing link at the end with a preview prompt
                    const message = `Halo Admin ShoeWorkshop,\n\nSaya ingin bertanya tentang hasil pengerjaan pada *Foto #${photoNum}* (${caption}).\n\n*Detail Order:*\nNomor SPK: ${spk}\nCustomer: {{ $workOrder->customer_name }}\n\n(Foto terlampir di link ini) 👇\n${photoUrl}`;
                    
                    return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
                },
                downloadPhoto() {
                    const photo = this.photos[this.currentIndex];
                    if (!photo) return;
                    
                    const link = document.createElement('a');
                    link.href = photo.src;
                    link.download = `ShoeWorkshop-${photo.caption.replace(/\s+/g, '-')}-${Date.now()}.jpg`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }
        }
    </script>
</body>
</html>

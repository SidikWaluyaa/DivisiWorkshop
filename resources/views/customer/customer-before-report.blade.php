<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Laporan Kondisi Sebelum - {{ $workOrder->spk_number }} | ShoeWorkshop</title>
    <meta name="description" content="Laporan kondisi sebelum pengerjaan sepatu untuk SPK {{ $workOrder->spk_number }}">
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
            --blue-primary: #2563EB;
            --blue-dark: #1E3A8A;
            --blue-light: #EFF6FF;
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
            background: linear-gradient(135deg, var(--blue-primary) 0%, var(--blue-dark) 100%);
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
            color: var(--blue-primary);
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
            background: var(--slate-200);
            color: var(--slate-800);
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
            background: var(--blue-primary);
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
            padding-top: 100%;
            overflow: hidden;
            background: var(--slate-100);
        }
        .photo-card .photo-wrapper img {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .photo-card:hover .photo-wrapper img {
            transform: scale(1.05);
        }
        .photo-card .photo-info {
            padding: 12px 16px;
        }
        .photo-card .photo-tag {
            font-size: 10px;
            font-weight: 800;
            color: var(--blue-primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .photo-card .photo-caption {
            font-size: 11px;
            font-weight: 500;
            color: var(--slate-600);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ═══ DETAIL CARD ═══ */
        .details-card {
            background: white;
            border-radius: var(--radius-xl);
            padding: 24px;
            box-shadow: var(--shadow-card);
            margin-bottom: 32px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
            border-bottom: 1px solid var(--slate-100);
            padding-bottom: 12px;
        }
        .detail-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .detail-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .detail-value {
            font-size: 14px;
            font-weight: 700;
            color: var(--slate-900);
        }

        /* ═══ EMPTY STATE ═══ */
        .empty-state {
            background: white;
            border-radius: var(--radius-xl);
            padding: 48px 24px;
            text-align: center;
            box-shadow: var(--shadow-card);
            margin-bottom: 32px;
        }
        .empty-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        .empty-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--slate-900);
            margin-bottom: 8px;
        }
        .empty-text {
            font-size: 13px;
            color: var(--slate-500);
            max-width: 280px;
            margin: 0 auto;
        }

        /* ═══ FOOTER ═══ */
        .footer {
            text-align: center;
            padding-top: 16px;
        }
        .footer-logo {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--slate-900);
            margin-bottom: 6px;
        }
        .footer-logo span {
            color: var(--blue-primary);
        }
        .footer-copy {
            font-size: 10px;
            color: var(--slate-400);
            font-weight: 500;
        }

        /* ═══ LIGHTBOX ═══ */
        .lightbox-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.95);
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
        .lightbox-close:focus { outline: 2px solid var(--blue-primary); }

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
            flex: 1;
            max-width: 250px;
        }

        .lightbox-action-btn:hover {
            transform: translateY(-2px);
        }
        .lightbox-action-btn:active {
            transform: scale(0.95);
        }
        .lightbox-action-btn.download {
            background: white;
            color: var(--slate-900);
        }
        .lightbox-action-btn.inquiry {
            background: #25D366;
            color: white;
        }
        .lightbox-action-btn svg {
            width: 16px; height: 16px;
        }
    </style>
</head>
<body x-data="photoLightbox()">
    {{-- Header --}}
    <header class="hero-header">
        <div class="hero-inner">
            <span class="hero-badge">Condition Report</span>
            <h1 class="hero-title">Foto Sebelum Treatment</h1>
            <p class="hero-subtitle">Dokumentasi kondisi sepatu Anda sebelum diproses oleh tim workshop kami.</p>
        </div>
    </header>

    {{-- Main --}}
    <main class="main-container">
        {{-- SPK Card --}}
        <section class="spk-card">
            <div>
                <p class="spk-label">Nomor SPK</p>
                <h2 class="spk-number">{{ $workOrder->spk_number }}</h2>
            </div>
            <span class="status-badge">Before</span>
        </section>

        {{-- Photos Section --}}
        <section>
            <div class="section-header">
                <div class="bar"></div>
                <h3>Galeri Foto Sepatu</h3>
            </div>

            @if($photos->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">📸</div>
                    <h4 class="empty-title">Belum Ada Foto</h4>
                    <p class="empty-text">Foto kondisi sebelum treatment untuk SPK ini belum diunggah oleh tim gudang/reception.</p>
                </div>
            @else
                <div class="photo-grid">
                    @foreach($photos as $photo)
                        <div class="photo-card" @click="open({{ $loop->index }})">
                            <div class="photo-wrapper">
                                <img src="{{ str_starts_with($photo->file_path, 'http') ? $photo->file_path : asset('storage/' . $photo->file_path) }}" alt="Foto Sebelum #{{ $loop->iteration }}">
                            </div>
                            <div class="photo-info">
                                <p class="photo-tag">Foto #{{ $loop->iteration }}</p>
                                <p class="photo-caption">{{ $photo->caption ?: 'Kondisi Awal Sepatu' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Details Section --}}
        <section>
            <div class="section-header">
                <div class="bar"></div>
                <h3>Detail Sepatu</h3>
            </div>

            <div class="details-card">
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label">Nama Customer</span>
                        <span class="detail-value">{{ $workOrder->customer_name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Brand Sepatu</span>
                        <span class="detail-value">{{ $workOrder->shoe_brand }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tipe / Model</span>
                        <span class="detail-value">{{ $workOrder->shoe_type ?: '-' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Warna Sepatu</span>
                        <span class="detail-value">{{ $workOrder->shoe_color }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tanggal Registrasi</span>
                        <span class="detail-value">{{ $workOrder->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="footer">
            <h4 class="footer-logo">Shoe<span>Workshop</span></h4>
            <p class="footer-copy">&copy; {{ date('Y') }} ShoeWorkshop. All rights reserved.</p>
        </footer>

        {{-- Lightbox Modal --}}
        <template x-if="isOpen">
            <div class="lightbox-backdrop" 
                 :class="{ 'active': isVisible }"
                 @click.self="close()"
                 @keydown.window.escape="close()"
                 @keydown.window.arrow-left="prev()"
                 @keydown.window.arrow-right="next()">
                
                {{-- Header --}}
                <div class="lightbox-header">
                    <span class="lightbox-counter" x-text="`Foto ${currentIndex + 1} dari ${photos.length}`"></span>
                    <button class="lightbox-close" @click="close()" aria-label="Tutup">✕</button>
                </div>

                {{-- Image display --}}
                <div class="lightbox-body" @click.self="close()">
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
                            </a>
                        </div>
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
                        alt: "Foto Sebelum #{{ $loop->iteration }}",
                        caption: "{{ $photo->caption ?: 'Dokumentasi Kondisi Sepatu' }}",
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
                    const caption = this.photos[this.currentIndex]?.caption || 'Dokumentasi Kondisi Sepatu';
                    
                    const message = `Halo Admin ShoeWorkshop,\n\nSaya ingin bertanya tentang kondisi awal sepatu saya pada *Foto Sebelum #${photoNum}* (${caption}).\n\n*Detail Order:*\nNomor SPK: ${spk}\nCustomer: {{ $workOrder->customer_name }}\n\n(Foto terlampir di link ini) 👇\n${photoUrl}`;
                    
                    return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
                },
                downloadPhoto() {
                    const photo = this.photos[this.currentIndex];
                    if (!photo) return;
                    
                    const link = document.createElement('a');
                    link.href = photo.src;
                    link.download = `ShoeWorkshop-Before-${photo.caption.replace(/\s+/g, '-')}-${Date.now()}.jpg`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }
        }
    </script>
</body>
</html>

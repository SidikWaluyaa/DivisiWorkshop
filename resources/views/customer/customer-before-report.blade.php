<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Laporan Sebelum Treatment — {{ $workOrder->spk_number }} | ShoeWorkshop</title>
    <meta name="description" content="Dokumentasi resmi kondisi awal fisik & foto sepatu sebelum treatment untuk SPK {{ $workOrder->spk_number }}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600&display=swap" rel="stylesheet">

    {{-- Alpine.js for Lightbox & Interaction --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brand-green: #22AF85;
            --brand-green-dark: #125740;
            --brand-green-deep: #0D3E2E;
            --brand-green-light: #EBF8F4;
            --brand-green-subtle: rgba(34, 175, 133, 0.08);

            --brand-gold: #FFC232;
            --brand-gold-dark: #D99B16;
            --brand-gold-light: #FFF9E6;
            --brand-gold-subtle: rgba(255, 194, 50, 0.12);

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

            --radius-md: 12px;
            --radius-lg: 18px;
            --radius-xl: 24px;
            --radius-2xl: 32px;

            --shadow-subtle: 0 4px 20px -2px rgba(15, 23, 42, 0.04), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
            --shadow-card: 0 10px 30px -4px rgba(15, 23, 42, 0.06), 0 4px 10px -2px rgba(15, 23, 42, 0.03);
            --shadow-elevated: 0 20px 40px -8px rgba(18, 87, 64, 0.12), 0 8px 16px -4px rgba(15, 23, 42, 0.04);
            --shadow-glow: 0 12px 30px -4px rgba(34, 175, 133, 0.35);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--slate-50);
            color: var(--slate-800);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* ═══ TOP NAVBAR ═══ */
        .site-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s ease;
        }
        .nav-inner {
            max-width: 960px;
            margin: 0 auto;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }
        .brand-logo-img {
            height: 42px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.06));
        }
        .brand-meta {
            display: flex;
            flex-direction: column;
        }
        .brand-title {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: -0.3px;
            color: var(--slate-900);
            line-height: 1.1;
            text-transform: uppercase;
        }
        .brand-sub {
            font-size: 10px;
            font-weight: 700;
            color: var(--brand-green);
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .nav-badge-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .badge-verified {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--brand-green-subtle);
            color: var(--brand-green);
            border: 1px solid rgba(34, 175, 133, 0.25);
            padding: 6px 12px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .badge-verified .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--brand-green);
            box-shadow: 0 0 0 3px rgba(34, 175, 133, 0.2);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        /* ═══ HERO SECTION ═══ */
        .hero-banner {
            position: relative;
            background: linear-gradient(135deg, var(--brand-green-deep) 0%, var(--brand-green-dark) 45%, var(--brand-green) 100%);
            color: white;
            padding: 56px 20px 96px;
            overflow: hidden;
        }
        .hero-banner::before {
            content: '';
            position: absolute;
            top: -120px;
            right: -100px;
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(255, 194, 50, 0.25) 0%, rgba(34, 175, 133, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-banner::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-content {
            position: relative;
            z-index: 10;
            max-width: 840px;
            margin: 0 auto;
            text-align: center;
        }
        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.24);
            backdrop-filter: blur(8px);
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 800;
            color: var(--brand-gold);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 18px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .hero-heading {
            font-size: 32px;
            font-weight: 900;
            letter-spacing: -1px;
            line-height: 1.15;
            margin-bottom: 12px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.12);
        }
        .hero-lead {
            font-size: 15px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            max-width: 580px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* ═══ MAIN APP CONTAINER ═══ */
        .content-wrap {
            max-width: 900px;
            margin: -60px auto 40px;
            padding: 0 20px;
            position: relative;
            z-index: 20;
        }

        /* ═══ SPK BADGE BAR ═══ */
        .spk-bar-card {
            background: #FFFFFF;
            border-radius: var(--radius-xl);
            padding: 24px 28px;
            box-shadow: var(--shadow-elevated);
            border: 1px solid rgba(226, 232, 240, 0.9);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
        }
        .spk-id-group {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .spk-icon-box {
            width: 52px;
            height: 52px;
            background: var(--brand-green-subtle);
            border: 1px solid rgba(34, 175, 133, 0.2);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand-green);
            flex-shrink: 0;
        }
        .spk-icon-box svg {
            width: 26px;
            height: 26px;
        }
        .spk-text-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--slate-400);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .spk-text-value {
            font-size: 22px;
            font-weight: 900;
            color: var(--slate-900);
            letter-spacing: -0.5px;
            font-family: inherit;
        }
        .spk-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--brand-gold-subtle);
            color: var(--brand-gold-dark);
            border: 1px solid rgba(255, 194, 50, 0.35);
            padding: 8px 18px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 2px 6px rgba(217, 155, 22, 0.1);
        }

        /* ═══ CARDS SYSTEM ═══ */
        .card-panel {
            background: #FFFFFF;
            border-radius: var(--radius-2xl);
            padding: 32px;
            box-shadow: var(--shadow-card);
            border: 1px solid var(--slate-200);
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }
        .card-panel:hover {
            box-shadow: var(--shadow-elevated);
        }
        .card-header-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--slate-100);
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .card-title-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .card-title-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: var(--brand-green-subtle);
            color: var(--brand-green);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .card-title-icon svg {
            width: 20px;
            height: 20px;
        }
        .card-title-text {
            font-size: 18px;
            font-weight: 900;
            color: var(--slate-900);
            letter-spacing: -0.3px;
        }

        /* ═══ SPECS GRID ═══ */
        .specs-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 16px;
        }
        @media (min-width: 640px) {
            .specs-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }
        .spec-tile {
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            padding: 18px 20px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            transition: all 0.2s ease;
        }
        .spec-tile:hover {
            background: #FFFFFF;
            border-color: rgba(34, 175, 133, 0.4);
            transform: translateY(-2px);
            box-shadow: var(--shadow-subtle);
        }
        .spec-tile-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #FFFFFF;
            border: 1px solid var(--slate-200);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--brand-green);
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .spec-tile-body {
            flex: 1;
            min-width: 0;
        }
        .spec-label {
            font-size: 10px;
            font-weight: 800;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
        }
        .spec-val {
            font-size: 15px;
            font-weight: 800;
            color: var(--slate-800);
            word-break: break-word;
        }
        .service-pill-box {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 6px;
        }
        .service-pill {
            background: var(--brand-green-subtle);
            color: var(--brand-green);
            border: 1px solid rgba(34, 175, 133, 0.2);
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* ═══ QC PHYSICAL CONDITIONS (3 ITEMS) ═══ */
        .qc-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }
        @media (min-width: 768px) {
            .qc-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        .qc-tile {
            background: #FFFFFF;
            border: 1px solid rgba(34, 175, 133, 0.2);
            border-radius: var(--radius-lg);
            padding: 20px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: var(--shadow-subtle);
            transition: all 0.25s ease;
        }
        .qc-tile::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--brand-green), var(--brand-gold));
        }
        .qc-tile:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-card);
            border-color: var(--brand-green);
        }
        .qc-tile-head {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .qc-tile-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--brand-green-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .qc-tile-caption {
            font-size: 12px;
            font-weight: 900;
            color: var(--slate-900);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .qc-tile-desc {
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-700);
            line-height: 1.5;
            background: var(--slate-50);
            padding: 12px 14px;
            border-radius: var(--radius-md);
            border: 1px solid var(--slate-200);
            flex: 1;
        }

        /* ═══ PHOTO GALLERY ═══ */
        .photo-gallery-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 24px;
        }
        .photo-count-pill {
            background: var(--slate-100);
            color: var(--slate-600);
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 800;
            border: 1px solid var(--slate-200);
        }
        .photo-grid-layout {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        @media (min-width: 680px) {
            .photo-grid-layout {
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }
        }
        .photo-card-item {
            position: relative;
            background: #FFFFFF;
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid var(--slate-200);
            box-shadow: var(--shadow-subtle);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            aspect-ratio: 1 / 1;
        }
        .photo-card-item:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: var(--shadow-elevated);
            border-color: rgba(34, 175, 133, 0.4);
        }
        .photo-card-item:active {
            transform: scale(0.98);
        }
        .photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }
        .photo-card-item:hover .photo-img {
            transform: scale(1.08);
        }
        .photo-overlay-scrim {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.6) 0%, rgba(15, 23, 42, 0) 50%);
            opacity: 0.8;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }
        .photo-card-item:hover .photo-overlay-scrim {
            opacity: 0.95;
        }
        .photo-chip-num {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(18, 87, 64, 0.85);
            backdrop-filter: blur(8px);
            color: #FFFFFF;
            font-size: 11px;
            font-weight: 900;
            padding: 4px 10px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 5;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .photo-zoom-hint {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background: rgba(255, 255, 255, 0.92);
            color: var(--slate-800);
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.25s ease;
            z-index: 5;
        }
        .photo-card-item:hover .photo-zoom-hint {
            background: var(--brand-green);
            color: #FFFFFF;
            transform: scale(1.1);
        }
        .photo-zoom-hint svg {
            width: 18px;
            height: 18px;
        }
        .photo-caption-preview {
            position: absolute;
            bottom: 12px;
            left: 12px;
            right: 50px;
            color: #FFFFFF;
            font-size: 11px;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            z-index: 5;
            text-shadow: 0 1px 3px rgba(0,0,0,0.6);
        }

        /* ═══ EMPTY STATE ═══ */
        .empty-gallery-state {
            text-align: center;
            padding: 60px 24px;
            background: var(--slate-50);
            border: 2px dashed var(--slate-300);
            border-radius: var(--radius-xl);
            margin: 10px 0;
        }
        .empty-icon-bubble {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--slate-100);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 14px;
        }
        .empty-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--slate-700);
            margin-bottom: 4px;
        }
        .empty-sub {
            font-size: 13px;
            color: var(--slate-400);
        }

        /* ═══ ACTION HUB & FOOTER ═══ */
        .action-hub {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin-top: 32px;
        }
        @media (min-width: 640px) {
            .action-hub {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 18px 24px;
            border-radius: var(--radius-xl);
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.3px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-card);
            cursor: pointer;
            border: none;
        }
        .action-btn svg {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
        }
        .action-btn.btn-wa {
            background: #25D366;
            color: #FFFFFF;
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
        }
        .action-btn.btn-wa:hover {
            background: #20BD5A;
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(37, 211, 102, 0.4);
        }
        .action-btn.btn-track {
            background: #FFFFFF;
            color: var(--slate-800);
            border: 1px solid var(--slate-300);
        }
        .action-btn.btn-track:hover {
            border-color: var(--brand-green);
            color: var(--brand-green);
            transform: translateY(-2px);
            box-shadow: var(--shadow-elevated);
        }

        .footer-note {
            text-align: center;
            padding: 40px 20px 20px;
            color: var(--slate-400);
            font-size: 12px;
        }
        .footer-logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 12px;
        }
        .footer-logo-img {
            height: 32px;
            opacity: 0.8;
            filter: grayscale(20%);
        }
        .footer-brand-title {
            font-size: 13px;
            font-weight: 900;
            color: var(--slate-700);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ═══ FULLSCREEN LIGHTBOX 2.0 ═══ */
        .lb-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(10, 15, 29, 0.94);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            z-index: 99999;
            display: flex;
            flex-direction: column;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .lb-backdrop.lb-active {
            opacity: 1;
        }
        .lb-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            color: #FFFFFF;
            z-index: 10;
        }
        .lb-counter {
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.1);
            padding: 6px 14px;
            border-radius: 100px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .lb-close-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .lb-close-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.08);
        }
        .lb-close-btn svg {
            width: 20px;
            height: 20px;
        }

        .lb-viewport {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 10px 16px;
            overflow: hidden;
        }
        .lb-viewport img {
            max-width: 95%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: var(--radius-lg);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
            user-select: none;
            -webkit-user-drag: none;
            transition: transform 0.3s ease;
        }
        .lb-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 20;
        }
        .lb-nav-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-50%) scale(1.1);
        }
        .lb-nav-btn.prev-btn { left: 20px; }
        .lb-nav-btn.next-btn { right: 20px; }
        .lb-nav-btn svg {
            width: 24px;
            height: 24px;
        }

        .lb-bottom-sheet {
            padding: 20px 24px 32px;
            background: linear-gradient(to top, rgba(10, 15, 29, 0.95), rgba(10, 15, 29, 0));
            text-align: center;
            color: #FFFFFF;
            z-index: 10;
        }
        .lb-title-cap {
            font-size: 15px;
            font-weight: 800;
            margin-bottom: 4px;
        }
        .lb-time-cap {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 20px;
        }
        .lb-btn-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 12px;
            max-width: 480px;
            margin: 0 auto;
        }
        .lb-action-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 22px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .lb-action-pill.btn-download {
            background: rgba(255, 255, 255, 0.15);
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        .lb-action-pill.btn-download:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }
        .lb-action-pill.btn-chat-wa {
            background: #25D366;
            color: #FFFFFF;
            box-shadow: 0 4px 16px rgba(37, 211, 102, 0.35);
        }
        .lb-action-pill.btn-chat-wa:hover {
            background: #20BD5A;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.5);
        }
        .lb-action-pill svg {
            width: 18px;
            height: 18px;
        }

        /* ═══ PRINT STYLES ═══ */
        @media print {
            .site-nav, .action-hub, .hero-banner::before, .hero-banner::after {
                display: none !important;
            }
            .hero-banner {
                background: white !important;
                color: black !important;
                padding: 20px 0 !important;
            }
            .content-wrap {
                margin-top: 0 !important;
            }
        }
    </style>
</head>
<body>

    {{-- ═══ TOP NAVBAR WITH OFFICIAL LOGO ═══ --}}
    <nav class="site-nav">
        <div class="nav-inner">
            <a href="{{ url('/') }}" class="brand-logo-wrap">
                <img src="{{ asset('images/logo.png') }}" alt="ShoeWorkshop Logo" class="brand-logo-img">
                <div class="brand-meta">
                    <span class="brand-title">SHOEWORKSHOP</span>
                    <span class="brand-sub">Premium Care & Repair</span>
                </div>
            </a>

            <div class="nav-badge-wrap">
                <span class="badge-verified">
                    <span class="dot"></span>
                    Verified Before Report
                </span>
            </div>
        </div>
    </nav>

    {{-- ═══ HERO SECTION ═══ --}}
    <header class="hero-banner">
        <div class="hero-content">
            <div class="hero-pill">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 10h2v4h-2zm0 6h2v2h-2z"/>
                </svg>
                BEFORE-TREATMENT REPORT
            </div>
            <h1 class="hero-heading">
                Dokumentasi Kondisi Awal Sepatu
            </h1>
            <p class="hero-lead">
                Transparansi penuh atas kondisi fisik awal sepatu Anda saat diterima di workshop sebelum proses pengerjaan dimulai.
            </p>
        </div>
    </header>

    {{-- ═══ MAIN CONTENT ═══ --}}
    <main class="content-wrap"
          x-data="photoLightbox()" 
          @keydown.escape.window="close()" 
          @keydown.left.window="prev()" 
          @keydown.right.window="next()">

        {{-- 1. SPK IDENTITY BAR --}}
        <div class="spk-bar-card">
            <div class="spk-id-group">
                <div class="spk-icon-box">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="spk-text-label">Nomor SPK Pelanggan</div>
                    <div class="spk-text-value">{{ $workOrder->spk_number }}</div>
                </div>
            </div>
            <div class="spk-status-pill">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                Status: Dokumen Before
            </div>
        </div>

        {{-- 2. DETAIL SEPATU & LAYANAN --}}
        <section class="card-panel">
            <div class="card-header-line">
                <div class="card-title-group">
                    <div class="card-title-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h2 class="card-title-text">Detail Sepatu & Layanan Treatment</h2>
                </div>
                <span class="badge-verified" style="font-size: 10px;">
                    Order Data
                </span>
            </div>

            <div class="specs-grid">
                {{-- Nama Customer --}}
                <div class="spec-tile">
                    <div class="spec-tile-icon">👤</div>
                    <div class="spec-tile-body">
                        <div class="spec-label">Nama Pelanggan</div>
                        <div class="spec-val">{{ $workOrder->customer_name }}</div>
                    </div>
                </div>

                {{-- Brand & Model --}}
                <div class="spec-tile">
                    <div class="spec-tile-icon">👟</div>
                    <div class="spec-tile-body">
                        <div class="spec-label">Brand & Model Sepatu</div>
                        <div class="spec-val">
                            {{ $workOrder->shoe_brand ?: '-' }}
                            @if($workOrder->shoe_type)
                                <span style="color: var(--slate-400); font-weight: 500;">/ {{ $workOrder->shoe_type }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Warna Sepatu --}}
                <div class="spec-tile">
                    <div class="spec-tile-icon">🎨</div>
                    <div class="spec-tile-body">
                        <div class="spec-label">Warna Sepatu</div>
                        <div class="spec-val">{{ $workOrder->shoe_color ?: '-' }}</div>
                    </div>
                </div>

                {{-- Tanggal Masuk --}}
                <div class="spec-tile">
                    <div class="spec-tile-icon">📅</div>
                    <div class="spec-tile-body">
                        <div class="spec-label">Tanggal Masuk Workshop</div>
                        <div class="spec-val">
                            {{ $workOrder->entry_date ? \Carbon\Carbon::parse($workOrder->entry_date)->translatedFormat('d F Y') : $workOrder->created_at->translatedFormat('d F Y') }}
                        </div>
                    </div>
                </div>

                {{-- Layanan Terpilih --}}
                <div class="spec-tile" style="grid-column: 1 / -1;">
                    <div class="spec-tile-icon">✨</div>
                    <div class="spec-tile-body">
                        <div class="spec-label">Layanan / Treatment yang Dikerjakan</div>
                        <div class="service-pill-box">
                            @forelse($workOrder->workOrderServices as $service)
                                <span class="service-pill">
                                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    {{ $service->custom_service_name ?? ($service->service->name ?? '-') }}
                                </span>
                            @empty
                                <span class="spec-val" style="color: var(--slate-400); font-weight: 500;">Belum ada rincian layanan tercatat.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 3. HASIL PEMERIKSAAN AWAL (QC FISIK GUDANG) --}}
        @if(!empty($workOrder->desc_upper) || !empty($workOrder->desc_sol) || !empty($workOrder->desc_kondisi_bawaan))
        <section class="card-panel">
            <div class="card-header-line">
                <div class="card-title-group">
                    <div class="card-title-icon" style="background: var(--brand-gold-subtle); color: var(--brand-gold-dark);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="card-title-text">Hasil Pemeriksaan Fisik Masuk (QC Awal)</h2>
                        <div style="font-size: 11px; font-weight: 600; color: var(--slate-400); margin-top: 2px;">
                            Pencatatan resmi tim QC Workshop saat sepatu pertama kali tiba di warehouse
                        </div>
                    </div>
                </div>
            </div>

            <div class="qc-grid">
                {{-- 1. Upper --}}
                <div class="qc-tile">
                    <div class="qc-tile-head">
                        <div class="qc-tile-avatar">👟</div>
                        <div>
                            <div class="qc-tile-caption">1. Bagian Upper</div>
                            <span style="font-size: 10px; font-weight: 700; color: var(--brand-green);">Kondisi Atas</span>
                        </div>
                    </div>
                    <div class="qc-tile-desc">
                        {{ $workOrder->desc_upper ?: 'Tidak ada catatan kerusakan/noda khusus.' }}
                    </div>
                </div>

                {{-- 2. Sol --}}
                <div class="qc-tile">
                    <div class="qc-tile-head">
                        <div class="qc-tile-avatar">🦶</div>
                        <div>
                            <div class="qc-tile-caption">2. Bagian Sol</div>
                            <span style="font-size: 10px; font-weight: 700; color: var(--brand-green);">Kondisi Bawah</span>
                        </div>
                    </div>
                    <div class="qc-tile-desc">
                        {{ $workOrder->desc_sol ?: 'Tidak ada catatan aus/kerusakan khusus.' }}
                    </div>
                </div>

                {{-- 3. Bawaan / Aksesoris --}}
                <div class="qc-tile">
                    <div class="qc-tile-head">
                        <div class="qc-tile-avatar">📦</div>
                        <div>
                            <div class="qc-tile-caption">3. Kondisi Bawaan</div>
                            <span style="font-size: 10px; font-weight: 700; color: var(--brand-green);">Kelengkapan & Aksesoris</span>
                        </div>
                    </div>
                    <div class="qc-tile-desc">
                        {{ $workOrder->desc_kondisi_bawaan ?: 'Tidak ada kelengkapan khusus tercatat.' }}
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- 4. DOKUMENTASI FOTO SEBELUM TREATMENT --}}
        <section class="card-panel">
            <div class="photo-gallery-header">
                <div class="card-title-group">
                    <div class="card-title-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="card-title-text">Dokumentasi Foto Sebelum Treatment</h2>
                        <div style="font-size: 11px; font-weight: 600; color: var(--slate-400); margin-top: 2px;">
                            Klik pada foto untuk melihat ukuran penuh & mengunduh gambar
                        </div>
                    </div>
                </div>

                @if($photos->count() > 0)
                    <span class="photo-count-pill">
                        📸 {{ $photos->count() }} Foto Dokumentasi
                    </span>
                @endif
            </div>

            @if($photos->count() > 0)
                <div class="photo-grid-layout">
                    @foreach($photos as $photo)
                        @php
                            $filePath = $photo->file_path;
                            $imgSrc = str_starts_with($filePath, 'http') ? $filePath : asset('storage/' . $filePath);
                        @endphp
                        <div class="photo-card-item" @click="open({{ $loop->index }})">
                            <span class="photo-chip-num">Foto #{{ $loop->iteration }}</span>
                            <img src="{{ $imgSrc }}" 
                                 alt="Foto Sebelum #{{ $loop->iteration }}" 
                                 class="photo-img"
                                 loading="lazy">
                            <div class="photo-overlay-scrim"></div>
                            
                            @if($photo->caption)
                                <div class="photo-caption-preview">{{ $photo->caption }}</div>
                            @endif

                            <div class="photo-zoom-hint">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                </svg>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-gallery-state">
                    <div class="empty-icon-bubble">📷</div>
                    <div class="empty-title">Belum Ada Foto Sebelum Treatment</div>
                    <p class="empty-sub">Dokumentasi foto sedang diproses oleh petugas warehouse workshop kami.</p>
                </div>
            @endif
        </section>

        {{-- 5. ACTION HUB: WhatsApp & Live Tracking --}}
        <div class="action-hub">
            {{-- Hubungi WhatsApp --}}
            <a href="https://wa.me/62895339939800?text={{ urlencode('Halo Admin ShoeWorkshop, saya ingin berkonsultasi mengenai foto sebelum treatment pada SPK ' . $workOrder->spk_number . ' atas nama ' . $workOrder->customer_name) }}" 
               target="_blank" 
               class="action-btn btn-wa">
                <svg fill="currentColor" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.413-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.654zm6.236-3.361c1.556.924 3.084 1.411 4.708 1.411 5.452 0 9.888-4.435 9.891-9.886.003-5.452-4.432-9.887-9.895-9.887-5.451 0-9.888 4.435-9.891 9.886l-.001 2.233 1.268 3.313 1.488 1.29 2.432 1.64zm11.751-6.901c-.139-.232-.511-.348-1.069-.626-.557-.279-2.593-1.28-2.966-1.42-.372-.139-.643-.209-.916.209-.271.418-.51 1.063-.51 1.063s-.186.232-.511.116c-.328-.119-1.383-.511-2.636-1.626-1.071-.954-1.782-2.126-1.995-2.521-.213-.394-.023-.607.174-.804.177-.176.395-.464.593-.695.197-.232.261-.397.394-.664.133-.267.067-.502-.034-.734-.1-.233-.916-2.203-1.256-3.016-.33-.799-.664-.691-.916-.703l-.782-.014c-.27 0-.712.102-1.084.512-.371.41-.418.819-1.418 2.302-.999 1.483-2.184 2.919-2.184 2.919s.139 1.486 1.486 3.129c1.347 1.642 2.646 3.238 2.646 3.238s.229.344.59.131c.361-.213 1.579-.918 2.103-1.41 1.144-1.076 1.109-1.146 1.109-1.146s.418-.139.789-.046c.371.093 2.502 1.21 2.502 1.21s.373.186.418.42c.045.234.045 1.348-.511 2.279z"/>
                </svg>
                Tanya CS via WhatsApp
            </a>

            {{-- Live Tracking --}}
            <a href="{{ route('tracking.index') }}" 
               class="action-btn btn-track">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                          d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                Cek Live Tracking Sepatu
            </a>
        </div>

        {{-- FOOTER BRAND --}}
        <footer class="footer-note">
            <div class="footer-logo-row">
                <img src="{{ asset('images/logo.png') }}" alt="ShoeWorkshop" class="footer-logo-img">
                <span class="footer-brand-title">ShoeWorkshop Indonesia</span>
            </div>
            <p>Sistem Manajemen Workshop & Layanan Transparansi Pelanggan Digital</p>
            <p style="margin-top: 4px; font-size: 11px; opacity: 0.8;">
                Dokumentasi ini dibuat secara otomatis oleh sistem pada {{ $generatedAt }}
            </p>
        </footer>

        {{-- ═══ FULLSCREEN LIGHTBOX 2.0 ═══ --}}
        <template x-if="isOpen">
            <div class="lb-backdrop" 
                 :class="{ 'lb-active': isVisible }"
                 @click.self="close()">
                
                {{-- Topbar --}}
                <div class="lb-topbar">
                    <span class="lb-counter" x-text="`Foto ${currentIndex + 1} dari ${photos.length}`"></span>
                    <button class="lb-close-btn" @click="close()" aria-label="Tutup Preview">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Image Viewport --}}
                <div class="lb-viewport" @click.self="close()">
                    <button class="lb-nav-btn prev-btn" @click.stop="prev()" x-show="photos.length > 1" aria-label="Foto Sebelumnya">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    
                    <img :src="photos[currentIndex]?.src" :alt="photos[currentIndex]?.alt">
                    
                    <button class="lb-nav-btn next-btn" @click.stop="next()" x-show="photos.length > 1" aria-label="Foto Selanjutnya">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                {{-- Bottom Sheet --}}
                <div class="lb-bottom-sheet">
                    <div class="lb-title-cap" x-text="photos[currentIndex]?.caption"></div>
                    <div class="lb-time-cap" x-text="photos[currentIndex]?.date"></div>
                    
                    <div class="lb-btn-row">
                        <button @click="downloadPhoto()" class="lb-action-pill btn-download">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            SIMPAN FOTO
                        </button>

                        <a :href="getWhatsAppLink()" 
                           target="_blank"
                           class="lb-action-pill btn-chat-wa">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.413-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.654zm6.236-3.361c1.556.924 3.084 1.411 4.708 1.411 5.452 0 9.888-4.435 9.891-9.886.003-5.452-4.432-9.887-9.895-9.887-5.451 0-9.888 4.435-9.891 9.886l-.001 2.233 1.268 3.313 1.488 1.29 2.432 1.64zm11.751-6.901c-.139-.232-.511-.348-1.069-.626-.557-.279-2.593-1.28-2.966-1.42-.372-.139-.643-.209-.916.209-.271.418-.51 1.063-.51 1.063s-.186.232-.511.116c-.328-.119-1.383-.511-2.636-1.626-1.071-.954-1.782-2.126-1.995-2.521-.213-.394-.023-.607.174-.804.177-.176.395-.464.593-.695.197-.232.261-.397.394-.664.133-.267.067-.502-.034-.734-.1-.233-.916-2.203-1.256-3.016-.33-.799-.664-.691-.916-.703l-.782-.014c-.27 0-.712.102-1.084.512-.371.41-.418.819-1.418 2.302-.999 1.483-2.184 2.919-2.184 2.919s.139 1.486 1.486 3.129c1.347 1.642 2.646 3.238 2.646 3.238s.229.344.59.131c.361-.213 1.579-.918 2.103-1.41 1.144-1.076 1.109-1.146 1.109-1.146s.418-.139.789-.046c.371.093 2.502 1.21 2.502 1.21s.373.186.418.42c.045.234.045 1.348-.511 2.279z"/>
                            </svg>
                            TANYA CS MENGENAI FOTO
                        </a>
                    </div>
                </div>
            </div>
        </template>
    </main>

    {{-- ═══ LIGHTBOX JAVASCRIPT STATE ═══ --}}
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
                        caption: @json($photo->caption ?: 'Dokumentasi Kondisi Fisik Sepatu #' . $loop->iteration),
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
                    if (!this.isOpen || this.photos.length <= 1) return;
                    this.currentIndex = (this.currentIndex - 1 + this.photos.length) % this.photos.length;
                },
                next() {
                    if (!this.isOpen || this.photos.length <= 1) return;
                    this.currentIndex = (this.currentIndex + 1) % this.photos.length;
                },
                getWhatsAppLink() {
                    const phone = "62895339939800";
                    const spk = @json($workOrder->spk_number);
                    const photoNum = this.currentIndex + 1;
                    const photoUrl = this.photos[this.currentIndex]?.src || '';
                    const caption = this.photos[this.currentIndex]?.caption || 'Dokumentasi Kondisi Sepatu';
                    const customer = @json($workOrder->customer_name);
                    
                    const message = `Halo Admin ShoeWorkshop,\n\nSaya ingin bertanya tentang kondisi awal sepatu saya pada *Foto Sebelum #${photoNum}* (${caption}).\n\n*Detail Order:*\nNomor SPK: ${spk}\nCustomer: ${customer}\n\n(Foto terlampir di link ini) 👇\n${photoUrl}`;
                    
                    return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
                },
                downloadPhoto() {
                    const photo = this.photos[this.currentIndex];
                    if (!photo) return;
                    
                    const link = document.createElement('a');
                    link.href = photo.src;
                    const safeCaption = (photo.caption || 'Foto').replace(/[^a-zA-Z0-9]/g, '-');
                    link.download = `ShoeWorkshop-Before-${safeCaption}-${Date.now()}.jpg`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            };
        }
    </script>
</body>
</html>

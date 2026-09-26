<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <title>Laporan Sebelum Treatment — {{ $workOrder->spk_number }} | ShoeWorkshop</title>
    <meta name="description" content="Dokumentasi resmi kondisi awal fisik & foto sepatu sebelum treatment untuk SPK {{ $workOrder->spk_number }}">
    <meta name="theme-color" content="#125740">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600&display=swap" rel="stylesheet">

    {{-- Alpine.js for Lightbox & Native Mobile Interaction --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
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

            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-xl: 26px;
            --radius-2xl: 32px;

            --shadow-subtle: 0 4px 20px -2px rgba(15, 23, 42, 0.04), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
            --shadow-card: 0 10px 30px -4px rgba(15, 23, 42, 0.06), 0 4px 10px -2px rgba(15, 23, 42, 0.03);
            --shadow-elevated: 0 20px 40px -8px rgba(18, 87, 64, 0.14), 0 8px 16px -4px rgba(15, 23, 42, 0.04);
            --shadow-glow: 0 12px 30px -4px rgba(34, 175, 133, 0.35);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--slate-50);
            color: var(--slate-800);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            min-height: 100vh;
            padding-bottom: 100px; /* Space for Mobile Sticky Action Dock */
        }

        /* ═══ TOP NAVBAR ═══ */
        .site-nav {
            position: sticky;
            top: 0;
            z-index: 40;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.85);
            transition: all 0.3s ease;
        }
        .nav-inner {
            max-width: 900px;
            margin: 0 auto;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: inherit;
        }
        .brand-logo-img {
            height: 38px;
            width: auto;
            object-fit: contain;
        }
        .brand-meta {
            display: flex;
            flex-direction: column;
        }
        .brand-title {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: -0.2px;
            color: var(--brand-green-dark);
            line-height: 1.1;
        }
        .brand-sub {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--slate-500);
            text-transform: uppercase;
        }
        .nav-badge-wrap {
            display: flex;
            align-items: center;
        }
        .badge-verified {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--brand-green-light);
            color: var(--brand-green-dark);
            padding: 4px 10px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 800;
            border: 1px solid rgba(34, 175, 133, 0.25);
            white-space: nowrap;
        }
        .badge-verified .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--brand-green);
            animation: pulse-dot 2s infinite ease-in-out;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        /* ═══ HERO BANNER ═══ */
        .hero-banner {
            background: linear-gradient(135deg, var(--brand-green-dark) 0%, var(--brand-green-deep) 60%, #0A261D 100%);
            color: #FFFFFF;
            padding: 32px 18px 46px;
            position: relative;
            overflow: hidden;
            border-bottom-left-radius: 28px;
            border-bottom-right-radius: 28px;
            box-shadow: 0 12px 30px rgba(18, 87, 64, 0.15);
        }
        .hero-banner::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 194, 50, 0.2) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-banner::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: -40px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(34, 175, 133, 0.25) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-content {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            text-align: center;
        }
        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 194, 50, 0.18);
            border: 1px solid rgba(255, 194, 50, 0.4);
            color: var(--brand-gold);
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .hero-heading {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.4px;
            line-height: 1.25;
            margin-bottom: 8px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
        }
        @media (min-width: 640px) {
            .hero-banner {
                padding: 40px 24px 54px;
            }
            .hero-heading {
                font-size: 28px;
            }
        }
        .hero-lead {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.85);
            max-width: 520px;
            margin: 0 auto;
            line-height: 1.45;
        }

        /* ═══ CONTENT WRAPPER ═══ */
        .content-wrap {
            max-width: 860px;
            margin: -24px auto 0;
            padding: 0 14px;
            position: relative;
            z-index: 10;
        }

        /* ═══ SPK IDENTIFIER CARD ═══ */
        .spk-bar-card {
            background: #FFFFFF;
            border-radius: var(--radius-lg);
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: var(--shadow-card);
            border: 1px solid rgba(226, 232, 240, 0.9);
            margin-bottom: 14px;
        }
        @media (min-width: 540px) {
            .spk-bar-card {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                padding: 16px 20px;
            }
        }
        .spk-id-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .spk-icon-box {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, var(--brand-green-light), #D4F4EA);
            color: var(--brand-green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .spk-icon-box svg {
            width: 22px;
            height: 22px;
        }
        .spk-text-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .spk-text-value {
            font-size: 17px;
            font-weight: 900;
            letter-spacing: -0.3px;
            color: var(--slate-900);
        }
        .spk-status-pill {
            display: inline-flex;
            align-items: center;
            align-self: flex-start;
            gap: 6px;
            background: var(--brand-gold-subtle);
            color: #925800;
            padding: 5px 12px;
            border-radius: 100px;
            font-size: 11.5px;
            font-weight: 800;
            border: 1px solid rgba(255, 194, 50, 0.4);
        }
        @media (min-width: 540px) {
            .spk-status-pill {
                align-self: center;
            }
        }

        /* ═══ APP SECTION CARDS ═══ */
        .card-panel {
            background: #FFFFFF;
            border-radius: var(--radius-lg);
            padding: 18px 16px;
            box-shadow: var(--shadow-subtle);
            border: 1px solid rgba(226, 232, 240, 0.85);
            margin-bottom: 14px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        @media (min-width: 640px) {
            .card-panel {
                padding: 24px;
                margin-bottom: 20px;
            }
        }
        .card-header-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--slate-100);
        }
        .card-title-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-title-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--brand-green-subtle);
            color: var(--brand-green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .card-title-icon svg {
            width: 18px;
            height: 18px;
        }
        .card-title-text {
            font-size: 15px;
            font-weight: 900;
            color: var(--slate-900);
            letter-spacing: -0.2px;
        }

        /* ═══ SPECS GRID ═══ */
        .specs-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }
        @media (min-width: 540px) {
            .specs-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
        }
        .spec-tile {
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
        }
        .spec-tile-icon {
            font-size: 20px;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--slate-200);
        }
        .spec-tile-body {
            flex: 1;
            min-width: 0;
        }
        .spec-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--slate-400);
            margin-bottom: 2px;
        }
        .spec-val {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--slate-800);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .service-pill-box {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 4px;
        }
        .service-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--brand-green-light);
            color: var(--brand-green-dark);
            border: 1px solid rgba(34, 175, 133, 0.3);
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11.5px;
            font-weight: 800;
        }

        /* ═══ QC PHYSICAL CARDS ═══ */
        .qc-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }
        @media (min-width: 640px) {
            .qc-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 12px;
            }
        }
        .qc-tile {
            position: relative;
            background: #FFFFFF;
            border-radius: var(--radius-md);
            padding: 14px;
            border: 1px solid var(--slate-200);
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: var(--shadow-subtle);
            overflow: hidden;
        }
        .qc-tile::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--brand-green), var(--brand-gold));
        }
        .qc-tile-head {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .qc-tile-avatar {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--brand-green-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }
        .qc-tile-caption {
            font-size: 11px;
            font-weight: 900;
            color: var(--slate-900);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .qc-tile-desc {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--slate-700);
            line-height: 1.45;
            background: var(--slate-50);
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--slate-200);
            flex: 1;
        }

        /* ═══ PHOTO GALLERY (MOBILE-APP OPTIMIZED) ═══ */
        .photo-gallery-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 14px;
        }
        .photo-count-pill {
            background: var(--slate-100);
            color: var(--slate-600);
            padding: 4px 10px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 800;
            border: 1px solid var(--slate-200);
            white-space: nowrap;
        }
        .photo-grid-layout {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        @media (min-width: 680px) {
            .photo-grid-layout {
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
            }
        }
        .photo-card-item {
            position: relative;
            background: #FFFFFF;
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid var(--slate-200);
            box-shadow: var(--shadow-subtle);
            cursor: pointer;
            aspect-ratio: 1 / 1;
            touch-action: manipulation;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .photo-card-item:active {
            transform: scale(0.96);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        @media (hover: hover) {
            .photo-card-item:hover {
                transform: translateY(-3px) scale(1.01);
                box-shadow: var(--shadow-card);
                border-color: var(--brand-green);
            }
        }
        .photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .photo-overlay-scrim {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.65) 0%, rgba(15, 23, 42, 0.05) 50%, rgba(15, 23, 42, 0.2) 100%);
            pointer-events: none;
        }
        .photo-chip-num {
            position: absolute;
            top: 8px;
            left: 8px;
            background: rgba(18, 87, 64, 0.88);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            color: #FFFFFF;
            font-size: 10px;
            font-weight: 900;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            z-index: 5;
            box-shadow: 0 2px 6px rgba(0,0,0,0.25);
        }
        .photo-zoom-hint {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(255, 255, 255, 0.95);
            color: var(--slate-900);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            z-index: 5;
            transition: all 0.2s ease;
        }
        .photo-zoom-hint svg {
            width: 15px;
            height: 15px;
        }
        .photo-caption-preview {
            position: absolute;
            bottom: 8px;
            left: 8px;
            right: 42px;
            color: #FFFFFF;
            font-size: 10px;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            z-index: 5;
            text-shadow: 0 1px 3px rgba(0,0,0,0.8);
        }

        /* ═══ EMPTY GALLERY STATE ═══ */
        .empty-gallery-state {
            text-align: center;
            padding: 44px 20px;
            background: var(--slate-50);
            border: 2px dashed var(--slate-300);
            border-radius: var(--radius-lg);
            margin: 10px 0;
        }
        .empty-icon-bubble {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--slate-100);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .empty-title {
            font-size: 14.5px;
            font-weight: 800;
            color: var(--slate-700);
            margin-bottom: 4px;
        }
        .empty-sub {
            font-size: 12px;
            color: var(--slate-400);
        }

        /* ═══ STICKY BOTTOM APP DOCK (MOBILE-FIRST) ═══ */
        .mobile-bottom-dock {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 35;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid rgba(226, 232, 240, 0.9);
            padding: 10px 16px;
            padding-bottom: max(10px, env(safe-area-inset-bottom));
            box-shadow: 0 -8px 25px -4px rgba(15, 23, 42, 0.08);
        }
        .mobile-dock-inner {
            max-width: 860px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        @media (min-width: 640px) {
            .mobile-bottom-dock {
                position: static;
                background: transparent;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
                border-top: none;
                padding: 0;
                box-shadow: none;
                margin-top: 20px;
            }
            body {
                padding-bottom: 30px;
            }
        }
        .dock-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 14px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .dock-btn:active {
            transform: scale(0.97);
        }
        .dock-btn-wa {
            background: #25D366;
            color: #FFFFFF;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.35);
        }
        .dock-btn-wa svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }
        .dock-btn-track {
            background: #FFFFFF;
            color: var(--slate-800);
            border: 1px solid var(--slate-300);
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }
        .dock-btn-track svg {
            width: 17px;
            height: 17px;
            color: var(--brand-green-dark);
            flex-shrink: 0;
        }

        /* ═══ FOOTER ═══ */
        .footer-note {
            text-align: center;
            padding: 30px 16px 20px;
            color: var(--slate-400);
            font-size: 11.5px;
        }
        .footer-logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 8px;
        }
        .footer-logo-img {
            height: 26px;
            opacity: 0.85;
        }
        .footer-brand-title {
            font-size: 12px;
            font-weight: 900;
            color: var(--slate-700);
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        /* ═══════════════════════════════════════════════════════════════
           IMMERSIVE MOBILE PHOTO VIEWER (ALA IOS / WHATSAPP MEDIA)
           ═══════════════════════════════════════════════════════════════ */
        .viewer-container {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: #000000;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            user-select: none;
            -webkit-user-select: none;
            touch-action: none;
            overflow: hidden;
        }

        /* TOP APP BAR */
        .viewer-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            padding-top: max(12px, env(safe-area-inset-top));
            background: linear-gradient(to bottom, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0) 100%);
            z-index: 50;
        }
        .viewer-icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .viewer-icon-btn:active {
            transform: scale(0.9);
            background: rgba(255, 255, 255, 0.3);
        }
        .viewer-icon-btn svg {
            width: 19px;
            height: 19px;
        }
        .viewer-pill-meta {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .viewer-counter-badge {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #FFFFFF;
            font-size: 12px;
            font-weight: 800;
            padding: 3px 12px;
            border-radius: 100px;
            letter-spacing: 0.5px;
        }
        .viewer-spk-badge {
            font-size: 9.5px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 2px;
            letter-spacing: 0.5px;
        }
        .viewer-top-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* VIEWPORT CANVAS WITH GESTURES */
        .viewer-viewport {
            flex: 1;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            width: 100%;
            height: 100%;
        }
        .viewer-main-img {
            max-width: 100vw;
            max-height: calc(100vh - 180px);
            object-fit: contain;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            touch-action: none;
            will-change: transform;
            cursor: grab;
        }
        .viewer-main-img:active {
            cursor: grabbing;
        }

        /* DESKTOP / TABLET ARROWS */
        .viewer-nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #FFFFFF;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 45;
            transition: all 0.2s ease;
        }
        @media (min-width: 768px) {
            .viewer-nav-arrow {
                display: flex;
            }
        }
        .viewer-nav-arrow:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-50%) scale(1.08);
        }
        .viewer-nav-arrow.arrow-prev { left: 16px; }
        .viewer-nav-arrow.arrow-next { right: 16px; }
        .viewer-nav-arrow svg {
            width: 22px;
            height: 22px;
        }

        /* FLOATING ZOOM CONTROLS FOR PRO MOBILE TOUCH */
        .viewer-zoom-dock {
            position: absolute;
            top: 70px;
            right: 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            z-index: 45;
        }
        .zoom-dock-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .zoom-dock-btn:active {
            transform: scale(0.9);
            background: rgba(34, 175, 133, 0.8);
        }
        .zoom-dock-btn svg {
            width: 18px;
            height: 18px;
        }

        /* BOTTOM APP SHEET & THUMBNAILS */
        .viewer-bottom-bar {
            background: linear-gradient(to top, rgba(0,0,0,0.92) 0%, rgba(0,0,0,0.7) 70%, rgba(0,0,0,0) 100%);
            padding: 10px 14px;
            padding-bottom: max(14px, env(safe-area-inset-bottom));
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 50;
        }
        .viewer-caption-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 4px;
        }
        .viewer-caption-title {
            color: #FFFFFF;
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .viewer-caption-date {
            color: rgba(255, 255, 255, 0.6);
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* THUMBNAIL STRIP FOR QUICK JUMPING (THUMB NAVIGATION) */
        .viewer-thumb-strip {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            padding: 4px 2px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .viewer-thumb-strip::-webkit-scrollbar {
            display: none;
        }
        .viewer-thumb-item {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid transparent;
            opacity: 0.55;
            transition: all 0.2s ease;
            flex-shrink: 0;
            cursor: pointer;
            position: relative;
        }
        .viewer-thumb-item.thumb-active {
            opacity: 1;
            border-color: var(--brand-green);
            transform: scale(1.08);
            box-shadow: 0 0 12px rgba(34, 175, 133, 0.6);
        }
        .viewer-thumb-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* BOTTOM QUICK BUTTONS */
        .viewer-dock-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 2px;
        }
        .viewer-action-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease;
        }
        .viewer-action-btn:active {
            transform: scale(0.97);
        }
        .viewer-action-btn.btn-save {
            background: rgba(255, 255, 255, 0.18);
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        .viewer-action-btn.btn-wa-photo {
            background: #25D366;
            color: #FFFFFF;
        }

        /* SWIPE HINT TOAST */
        .viewer-swipe-hint {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            color: rgba(255, 255, 255, 0.8);
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 100px;
            pointer-events: none;
            opacity: 0.8;
            transition: opacity 0.5s ease;
            z-index: 40;
            white-space: nowrap;
        }

        /* ═══ PRINT STYLES ═══ */
        @media print {
            .site-nav, .mobile-bottom-dock, .hero-banner::before, .hero-banner::after, .viewer-container {
                display: none !important;
            }
            .hero-banner {
                background: white !important;
                color: black !important;
                padding: 15px 0 !important;
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
                    Verified Report
                </span>
            </div>
        </div>
    </nav>

    {{-- ═══ HERO SECTION ═══ --}}
    <header class="hero-banner">
        <div class="hero-content">
            <div class="hero-pill">
                <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
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

    {{-- ═══ MAIN CONTENT & ALPINE CONTROLLER ═══ --}}
    <main class="content-wrap"
          x-data="mobilePhotoViewer()" 
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
                <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
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
                                    <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24">
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
                        <div style="font-size: 11px; font-weight: 600; color: var(--slate-400); margin-top: 1px;">
                            Pencatatan resmi tim QC Workshop saat sepatu tiba
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
                            <span style="font-size: 9.5px; font-weight: 700; color: var(--brand-green);">Kondisi Atas</span>
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
                            <span style="font-size: 9.5px; font-weight: 700; color: var(--brand-green);">Kondisi Bawah</span>
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
                            <span style="font-size: 9.5px; font-weight: 700; color: var(--brand-green);">Kelengkapan & Aksesoris</span>
                        </div>
                    </div>
                    <div class="qc-tile-desc">
                        {{ $workOrder->desc_kondisi_bawaan ?: 'Tidak ada kelengkapan khusus tercatat.' }}
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- 4. DOKUMENTASI FOTO SEBELUM TREATMENT (TOUCH-OPTIMIZED GRID) --}}
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
                        <div style="font-size: 11px; font-weight: 600; color: var(--slate-400); margin-top: 1px;">
                            Tap foto untuk membuka viewer fullscreen interaktif
                        </div>
                    </div>
                </div>

                @if($photos->count() > 0)
                    <span class="photo-count-pill">
                        📸 {{ $photos->count() }} Foto
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

        {{-- 5. STICKY BOTTOM APP DOCK (ALWAYS ACCESSIBLE IN MOBILE) --}}
        <div class="mobile-bottom-dock">
            <div class="mobile-dock-inner">
                {{-- Hubungi WhatsApp --}}
                <a href="https://wa.me/62895339939800?text={{ urlencode('Halo Admin ShoeWorkshop, saya ingin berkonsultasi mengenai foto sebelum treatment pada SPK ' . $workOrder->spk_number . ' atas nama ' . $workOrder->customer_name) }}" 
                   target="_blank" 
                   class="dock-btn dock-btn-wa">
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.413-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.654zm6.236-3.361c1.556.924 3.084 1.411 4.708 1.411 5.452 0 9.888-4.435 9.891-9.886.003-5.452-4.432-9.887-9.895-9.887-5.451 0-9.888 4.435-9.891 9.886l-.001 2.233 1.268 3.313 1.488 1.29 2.432 1.64zm11.751-6.901c-.139-.232-.511-.348-1.069-.626-.557-.279-2.593-1.28-2.966-1.42-.372-.139-.643-.209-.916.209-.271.418-.51 1.063-.51 1.063s-.186.232-.511.116c-.328-.119-1.383-.511-2.636-1.626-1.071-.954-1.782-2.126-1.995-2.521-.213-.394-.023-.607.174-.804.177-.176.395-.464.593-.695.197-.232.261-.397.394-.664.133-.267.067-.502-.034-.734-.1-.233-.916-2.203-1.256-3.016-.33-.799-.664-.691-.916-.703l-.782-.014c-.27 0-.712.102-1.084.512-.371.41-.418.819-1.418 2.302-.999 1.483-2.184 2.919-2.184 2.919s.139 1.486 1.486 3.129c1.347 1.642 2.646 3.238 2.646 3.238s.229.344.59.131c.361-.213 1.579-.918 2.103-1.41 1.144-1.076 1.109-1.146 1.109-1.146s.418-.139.789-.046c.371.093 2.502 1.21 2.502 1.21s.373.186.418.42c.045.234.045 1.348-.511 2.279z"/>
                    </svg>
                    Tanya CS
                </a>

                {{-- Live Tracking --}}
                <a href="{{ route('tracking.index') }}" 
                   class="dock-btn dock-btn-track">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Live Tracking
                </a>
            </div>
        </div>

        {{-- FOOTER BRAND --}}
        <footer class="footer-note">
            <div class="footer-logo-row">
                <img src="{{ asset('images/logo.png') }}" alt="ShoeWorkshop" class="footer-logo-img">
                <span class="footer-brand-title">ShoeWorkshop Indonesia</span>
            </div>
            <p>Sistem Workshop & Layanan Transparansi Pelanggan Digital</p>
            <p style="margin-top: 3px; font-size: 10.5px; opacity: 0.8;">
                Dokumentasi dibuat otomatis pada {{ $generatedAt }}
            </p>
        </footer>

        {{-- ═══════════════════════════════════════════════════════════════
             IMMERSIVE MOBILE PHOTO VIEWER 2.0 (ALA IOS / WHATSAPP MEDIA)
             ═══════════════════════════════════════════════════════════════ --}}
        <template x-if="isOpen">
            <div class="viewer-container" 
                 x-show="isOpen"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @touchstart="handleTouchStart($event)"
                 @touchmove="handleTouchMove($event)"
                 @touchend="handleTouchEnd($event)">
                
                {{-- 1. TOP BAR --}}
                <div class="viewer-topbar">
                    {{-- Close Button --}}
                    <button class="viewer-icon-btn" @click="close()" aria-label="Tutup Viewer">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    {{-- Counter & SPK Badge --}}
                    <div class="viewer-pill-meta">
                        <span class="viewer-counter-badge" x-text="`${currentIndex + 1} / ${photos.length}`"></span>
                        <span class="viewer-spk-badge">{{ $workOrder->spk_number }}</span>
                    </div>

                    {{-- Top Actions: Download & Share/WA --}}
                    <div class="viewer-top-actions">
                        <button class="viewer-icon-btn" @click="downloadCurrentPhoto()" aria-label="Simpan Foto">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- 2. VIEWPORT CANVAS WITH TOUCH & DOUBLE TAP ZOOM --}}
                <div class="viewer-viewport" 
                     @click.self="toggleControls()" 
                     @dblclick="toggleZoom()">
                    
                    {{-- Nav Arrow Left (Desktop/Tablet) --}}
                    <button class="viewer-nav-arrow arrow-prev" 
                            @click.stop="prev()" 
                            x-show="photos.length > 1" 
                            aria-label="Foto Sebelumnya">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    {{-- Main Image with Zoom & Swipe Translation --}}
                    <img :src="photos[currentIndex]?.src" 
                         :alt="photos[currentIndex]?.alt"
                         class="viewer-main-img"
                         :style="`transform: translate(${touchDeltaX}px, ${touchDeltaY}px) scale(${zoomScale});`"
                         @load="resetZoom()">

                    {{-- Nav Arrow Right (Desktop/Tablet) --}}
                    <button class="viewer-nav-arrow arrow-next" 
                            @click.stop="next()" 
                            x-show="photos.length > 1" 
                            aria-label="Foto Selanjutnya">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    {{-- Floating Zoom Dock (Pro Mobile Controls) --}}
                    <div class="viewer-zoom-dock" x-show="showControls">
                        <button class="zoom-dock-btn" @click.stop="zoomIn()" aria-label="Perbesar">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </button>
                        <button class="zoom-dock-btn" @click.stop="zoomOut()" aria-label="Perkecil">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 12H6"/>
                            </svg>
                        </button>
                        <button class="zoom-dock-btn" @click.stop="resetZoom()" x-show="zoomScale !== 1" aria-label="Reset Zoom">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Swipe Hint Toast (Appears initially) --}}
                    <div class="viewer-swipe-hint" x-show="showSwipeHint" x-transition.opacity.duration.300ms>
                        👈 Geser untuk ganti foto • Geser ke bawah untuk menutup 👇
                    </div>
                </div>

                {{-- 3. BOTTOM BAR (CAPTION, THUMBNAILS, & ACTIONS) --}}
                <div class="viewer-bottom-bar" x-show="showControls">
                    {{-- Caption & Date --}}
                    <div class="viewer-caption-box">
                        <div class="viewer-caption-title" x-text="photos[currentIndex]?.caption || `Dokumentasi Foto #${currentIndex + 1}`"></div>
                        <div class="viewer-caption-date" x-text="photos[currentIndex]?.date"></div>
                    </div>

                    {{-- Thumbnails Strip (Instant Jump with Thumb) --}}
                    @if($photos->count() > 1)
                    <div class="viewer-thumb-strip">
                        <template x-for="(p, idx) in photos" :key="idx">
                            <div class="viewer-thumb-item" 
                                 :class="{ 'thumb-active': currentIndex === idx }"
                                 @click.stop="goTo(idx)">
                                <img :src="p.src" :alt="p.alt" loading="lazy">
                            </div>
                        </template>
                    </div>
                    @endif

                    {{-- Bottom Action Buttons --}}
                    <div class="viewer-dock-actions">
                        <button @click.stop="downloadCurrentPhoto()" class="viewer-action-btn btn-save">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Simpan Foto
                        </button>

                        <a :href="getWhatsAppPhotoLink()" 
                           target="_blank"
                           class="viewer-action-btn btn-wa-photo">
                            <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.413-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.654zm6.236-3.361c1.556.924 3.084 1.411 4.708 1.411 5.452 0 9.888-4.435 9.891-9.886.003-5.452-4.432-9.887-9.895-9.887-5.451 0-9.888 4.435-9.891 9.886l-.001 2.233 1.268 3.313 1.488 1.29 2.432 1.64zm11.751-6.901c-.139-.232-.511-.348-1.069-.626-.557-.279-2.593-1.28-2.966-1.42-.372-.139-.643-.209-.916.209-.271.418-.51 1.063-.51 1.063s-.186.232-.511.116c-.328-.119-1.383-.511-2.636-1.626-1.071-.954-1.782-2.126-1.995-2.521-.213-.394-.023-.607.174-.804.177-.176.395-.464.593-.695.197-.232.261-.397.394-.664.133-.267.067-.502-.034-.734-.1-.233-.916-2.203-1.256-3.016-.33-.799-.664-.691-.916-.703l-.782-.014c-.27 0-.712.102-1.084.512-.371.41-.418.819-1.418 2.302-.999 1.483-2.184 2.919-2.184 2.919s.139 1.486 1.486 3.129c1.347 1.642 2.646 3.238 2.646 3.238s.229.344.59.131c.361-.213 1.579-.918 2.103-1.41 1.144-1.076 1.109-1.146 1.109-1.146s.418-.139.789-.046c.371.093 2.502 1.21 2.502 1.21s.373.186.418.42c.045.234.045 1.348-.511 2.279z"/>
                            </svg>
                            Tanya CS
                        </a>
                    </div>
                </div>
            </div>
        </template>
    </main>

    {{-- ═══ MOBILE PHOTO VIEWER LOGIC (NATIVE GESTURES & ALPINE) ═══ --}}
    <script>
        function mobilePhotoViewer() {
            return {
                isOpen: false,
                currentIndex: 0,
                zoomScale: 1,
                showControls: true,
                showSwipeHint: false,
                touchStartX: 0,
                touchStartY: 0,
                touchDeltaX: 0,
                touchDeltaY: 0,
                isSwiping: false,
                lastTapTime: 0,
                photos: [
                    @foreach($photos as $photo)
                    {
                        src: "{{ str_starts_with($photo->file_path, 'http') ? $photo->file_path : asset('storage/' . $photo->file_path) }}",
                        alt: "Foto Sebelum #{{ $loop->iteration }}",
                        caption: @json($photo->caption ?: 'Dokumentasi Kondisi Sepatu #' . $loop->iteration),
                        date: "{{ $photo->created_at->translatedFormat('d M Y • H:i') }}"
                    },
                    @endforeach
                ],
                open(index) {
                    this.currentIndex = index;
                    this.isOpen = true;
                    this.resetZoom();
                    this.showControls = true;
                    this.showSwipeHint = true;
                    document.body.style.overflow = 'hidden';

                    // Auto hide hint after 2.5 seconds
                    setTimeout(() => {
                        this.showSwipeHint = false;
                    }, 2500);
                },
                close() {
                    this.isOpen = false;
                    this.resetZoom();
                    document.body.style.overflow = '';
                },
                goTo(index) {
                    if (index >= 0 && index < this.photos.length) {
                        this.currentIndex = index;
                        this.resetZoom();
                    }
                },
                prev() {
                    if (!this.isOpen || this.photos.length <= 1) return;
                    this.currentIndex = (this.currentIndex - 1 + this.photos.length) % this.photos.length;
                    this.resetZoom();
                },
                next() {
                    if (!this.isOpen || this.photos.length <= 1) return;
                    this.currentIndex = (this.currentIndex + 1) % this.photos.length;
                    this.resetZoom();
                },
                toggleControls() {
                    this.showControls = !this.showControls;
                },
                zoomIn() {
                    this.zoomScale = Math.min(this.zoomScale + 0.5, 3.0);
                },
                zoomOut() {
                    this.zoomScale = Math.max(this.zoomScale - 0.5, 1.0);
                },
                resetZoom() {
                    this.zoomScale = 1;
                    this.touchDeltaX = 0;
                    this.touchDeltaY = 0;
                },
                toggleZoom() {
                    if (this.zoomScale > 1) {
                        this.resetZoom();
                    } else {
                        this.zoomScale = 2.2;
                    }
                },

                /* Touch & Swipe Gestures (Ala WhatsApp / iOS Media) */
                handleTouchStart(e) {
                    if (e.touches.length === 1) {
                        this.touchStartX = e.touches[0].clientX;
                        this.touchStartY = e.touches[0].clientY;
                        this.touchDeltaX = 0;
                        this.touchDeltaY = 0;
                        this.isSwiping = true;

                        // Double tap detection
                        const now = Date.now();
                        if (now - this.lastTapTime < 300) {
                            this.toggleZoom();
                        }
                        this.lastTapTime = now;
                    }
                },
                handleTouchMove(e) {
                    if (!this.isSwiping || e.touches.length !== 1) return;
                    
                    const currentX = e.touches[0].clientX;
                    const currentY = e.touches[0].clientY;
                    const diffX = currentX - this.touchStartX;
                    const diffY = currentY - this.touchStartY;

                    // If zoomed in, allow dragging or ignore horizontal swipe
                    if (this.zoomScale > 1) {
                        return;
                    }

                    // Pull-down to dismiss gesture
                    if (diffY > 10 && Math.abs(diffY) > Math.abs(diffX)) {
                        this.touchDeltaY = diffY * 0.5; // damped resistance
                    } else if (Math.abs(diffX) > 10) {
                        this.touchDeltaX = diffX * 0.4; // horizontal preview slide
                    }
                },
                handleTouchEnd(e) {
                    if (!this.isSwiping) return;
                    this.isSwiping = false;

                    // Pull down enough to close
                    if (this.touchDeltaY > 90) {
                        this.close();
                        return;
                    }

                    // Horizontal swipe to next/prev
                    if (this.touchDeltaX < -50) {
                        this.next();
                    } else if (this.touchDeltaX > 50) {
                        this.prev();
                    }

                    // Reset visual drag
                    this.touchDeltaX = 0;
                    this.touchDeltaY = 0;
                },

                getWhatsAppPhotoLink() {
                    const phone = "62895339939800";
                    const spk = @json($workOrder->spk_number);
                    const photoNum = this.currentIndex + 1;
                    const photoUrl = this.photos[this.currentIndex]?.src || '';
                    const caption = this.photos[this.currentIndex]?.caption || 'Dokumentasi Kondisi Sepatu';
                    const customer = @json($workOrder->customer_name);
                    
                    const message = `Halo Admin ShoeWorkshop,\n\nSaya ingin bertanya mengenai kondisi fisik awal sepatu saya pada *Foto Sebelum #${photoNum}* (${caption}).\n\n*Detail Order:*\nNomor SPK: ${spk}\nNama Pelanggan: ${customer}\n\nLink Foto: ${photoUrl}`;
                    
                    return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
                },
                downloadCurrentPhoto() {
                    const photo = this.photos[this.currentIndex];
                    if (!photo) return;
                    
                    const link = document.createElement('a');
                    link.href = photo.src;
                    const safeSpk = (@json($workOrder->spk_number)).replace(/[^a-zA-Z0-9]/g, '-');
                    link.download = `ShoeWorkshop-Before-${safeSpk}-Foto${this.currentIndex + 1}.jpg`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            };
        }
    </script>
</body>
</html>

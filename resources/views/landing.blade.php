{{--
|=============================================================================
| LANDING PAGE — Lapangan Badminton Adenia Salsa
|=============================================================================
| File    : resources/views/landing.blade.php
| Route   : GET / (web.php) — ditampilkan ke pengunjung yang belum login
| Tujuan  : Halaman depan publik yang menampilkan profil lapangan, fasilitas,
|           harga sewa, cara booking, galeri, kontak, dan media sosial.
|           Pengunjung yang sudah login akan di-redirect ke dashboard masing-masing.
|=============================================================================
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    {{-- ===== META & SEO ===== --}}
    {{-- Charset dan viewport agar responsif di semua perangkat --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Meta SEO: Deskripsi dan kata kunci untuk mesin pencari (Google, Bing, dll.) --}}
    <meta name="description" content="Lapangan Badminton Adenia Salsa – Tempat terbaik untuk bermain badminton di kota Anda. Fasilitas modern, harga terjangkau, reservasi mudah.">
    <meta name="keywords" content="badminton, lapangan badminton, adenia salsa, reservasi lapangan, olahraga">

    {{-- Open Graph: Tampilan pratinjau saat link dibagikan di media sosial (WhatsApp, Facebook, dll.) --}}
    <meta property="og:title" content="Lapangan Badminton Adenia Salsa">
    <meta property="og:description" content="Fasilitas badminton premium dengan reservasi online mudah.">

    {{-- Judul tab browser --}}
    <title>Lapangan Badminton Adenia Salsa – Reservasi Online</title>

    {{-- ===== FAVICON ===== --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('favicon-192.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    {{-- ===== FONT EKSTERNAL ===== --}}
    {{-- Preconnect mempercepat koneksi ke server Google Fonts sebelum font dimuat --}}
    <!-- Google Fonts: Outfit (judul/heading) + Space Grotesk (teks body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome: Library ikon (fa-whatsapp, fa-instagram, fa-map-marker-alt, dll.) --}}
    <!-- Font Awesome 6 CDN — digunakan untuk seluruh ikon di landing page -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ===== CSS RESET & ROOT ===== */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            /* Primary: Sakura Pink — sesuai warna app (#e91e8c = sakura-500) */
            --primary: #e91e8c;
            --primary-light: #f571b0;
            --primary-dark: #b01464;
            --primary-glow: rgba(233, 30, 140, 0.3);

            /* Secondary: Purple accent */
            --secondary: #8b5cf6;
            --secondary-light: #a78bfa;
            --secondary-glow: rgba(139, 92, 246, 0.25);

            /* Background: Navy dark (sesuai app #0f1d36 → #152647) */
            --bg-dark: #080e1c;
            --bg-mid: #0f1d36;
            --bg-deep: #152647;
            --bg-card: rgba(255,255,255,0.04);
            --bg-card-hover: rgba(233, 30, 140, 0.06);

            /* UI */
            --border: rgba(255,255,255,0.08);
            --border-accent: rgba(233, 30, 140, 0.2);
            --text-primary: #e2e8f0;
            --text-muted: #94a3b8;

            /* Gradients */
            --grad-primary: linear-gradient(135deg, #e91e8c, #8b5cf6);
            --grad-primary-alt: linear-gradient(135deg, #8b5cf6, #e91e8c);
            --grad-subtle: linear-gradient(135deg, rgba(233,30,140,0.1), rgba(139,92,246,0.08));
            --grad-navy: linear-gradient(135deg, #0f1d36, #152647);

            --font-head: 'Outfit', sans-serif;
            --font-body: 'Space Grotesk', sans-serif;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            background: var(--bg-dark);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-dark); }
        ::-webkit-scrollbar-thumb { background: linear-gradient(var(--primary), var(--secondary)); border-radius: 3px; }

        /* ===== ANIMATED BG ===== */
        .bg-grid {
            position: fixed; inset: 0; z-index: 0;
            background-image:
                linear-gradient(rgba(233,30,140,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(139,92,246,0.025) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        .bg-orb {
            position: fixed; border-radius: 50%;
            filter: blur(90px); pointer-events: none; z-index: 0;
            animation: orbFloat 9s ease-in-out infinite alternate;
        }
        .orb-1 { width: 520px; height: 520px; background: rgba(233,30,140,0.11); top: -120px; left: -120px; }
        .orb-2 { width: 420px; height: 420px; background: rgba(139,92,246,0.11); bottom: 15%; right: -120px; animation-delay: -4s; }
        .orb-3 { width: 300px; height: 300px; background: rgba(245,113,176,0.07); top: 55%; left: 38%; animation-delay: -2s; }

        @keyframes orbFloat {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 45px) scale(1.08); }
        }

        /* ===== NAVBAR ===== */
        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            padding: 1rem 2rem;
            display: flex; align-items: center; justify-content: space-between;
            transition: all 0.3s ease;
        }
        nav.scrolled {
            background: rgba(8, 14, 28, 0.92);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-accent);
            padding: 0.75rem 2rem;
        }
        .nav-logo {
            display: flex; align-items: center; gap: 0.75rem;
            text-decoration: none;
        }
        .nav-logo-icon {
            width: 42px; height: 42px;
            background: var(--grad-primary);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: white;
            box-shadow: 0 0 20px var(--primary-glow);
        }
        .nav-logo-text {
            font-family: var(--font-head);
            font-weight: 800; font-size: 1.1rem;
            color: white; line-height: 1.1;
        }
        .nav-logo-text span { color: var(--primary-light); }
        .nav-links {
            display: flex; align-items: center; gap: 2rem;
            list-style: none;
        }
        .nav-links a {
            color: var(--text-muted); text-decoration: none;
            font-size: 0.9rem; font-weight: 500;
            transition: color 0.2s; position: relative;
        }
        .nav-links a::after {
            content: ''; position: absolute; bottom: -4px; left: 0; right: 0;
            height: 2px; background: var(--grad-primary);
            transform: scaleX(0); transition: transform 0.3s;
        }
        .nav-links a:hover { color: var(--primary-light); }
        .nav-links a:hover::after { transform: scaleX(1); }
        .nav-cta {
            background: var(--grad-primary) !important;
            color: white !important;
            padding: 0.55rem 1.4rem;
            border-radius: 50px;
            font-weight: 700 !important; font-size: 0.85rem !important;
            transition: box-shadow 0.3s, transform 0.2s !important;
        }
        .nav-cta:hover { box-shadow: 0 0 28px var(--primary-glow) !important; transform: translateY(-1px); }
        .nav-cta::after { display: none !important; }
        .hamburger {
            display: none; background: none; border: none;
            color: white; font-size: 1.5rem; cursor: pointer;
        }

        /* ===== HERO ===== */
        #hero {
            position: relative; min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            text-align: center; padding: 8rem 1.5rem 4rem;
            overflow: hidden; z-index: 1;
        }
        .hero-bg {
            position: absolute; inset: 0;
            background: url('/images/hero-court.png') center/cover no-repeat;
            filter: brightness(0.1) saturate(1.3);
        }
        .hero-overlay {
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse at 65% 35%, rgba(233,30,140,0.15) 0%, transparent 60%),
                radial-gradient(ellipse at 30% 70%, rgba(139,92,246,0.12) 0%, transparent 55%),
                linear-gradient(to bottom, rgba(8,14,28,0.5) 0%, rgba(8,14,28,0.95) 100%);
        }
        .hero-content { position: relative; z-index: 2; max-width: 860px; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: rgba(233,30,140,0.1);
            border: 1px solid rgba(233,30,140,0.35);
            padding: 0.4rem 1.1rem; border-radius: 50px;
            font-size: 0.8rem; font-weight: 600;
            color: var(--primary-light); margin-bottom: 1.5rem;
            animation: fadeSlideUp 0.6s ease forwards;
        }
        .hero-badge .dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--primary); animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.75); }
        }
        .hero h1 {
            font-family: var(--font-head);
            font-size: clamp(2.8rem, 7vw, 5.5rem);
            font-weight: 900; line-height: 1.05;
            color: white; margin-bottom: 1.5rem;
            animation: fadeSlideUp 0.8s ease 0.1s both;
        }
        .hero h1 .highlight {
            background: var(--grad-primary);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero p {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: var(--text-muted); max-width: 580px; margin: 0 auto 2.5rem;
            line-height: 1.75; animation: fadeSlideUp 0.8s ease 0.2s both;
        }
        .hero-btns {
            display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;
            animation: fadeSlideUp 0.8s ease 0.3s both;
        }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 0.6rem;
            background: var(--grad-primary);
            color: white; padding: 1rem 2.2rem;
            border-radius: 50px; font-weight: 700; font-size: 1rem;
            text-decoration: none; border: none; cursor: pointer;
            transition: all 0.3s; position: relative; overflow: hidden;
        }
        .btn-primary::before {
            content: ''; position: absolute; inset: 0;
            background: var(--grad-primary-alt);
            opacity: 0; transition: opacity 0.3s;
        }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 14px 40px var(--primary-glow), 0 6px 20px var(--secondary-glow); }
        .btn-primary:hover::before { opacity: 1; }
        .btn-primary span, .btn-primary i { position: relative; z-index: 1; }
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 0.6rem;
            background: transparent; color: white;
            padding: 1rem 2.2rem; border-radius: 50px;
            font-weight: 600; font-size: 1rem;
            text-decoration: none;
            border: 1px solid rgba(233,30,140,0.4);
            transition: all 0.3s;
        }
        .btn-secondary:hover {
            background: rgba(233,30,140,0.08);
            border-color: var(--primary-light); color: var(--primary-light);
            box-shadow: 0 0 20px var(--primary-glow);
        }
        .hero-stats {
            display: flex; justify-content: center; gap: 3rem;
            margin-top: 4rem; flex-wrap: wrap;
            animation: fadeSlideUp 0.8s ease 0.4s both;
        }
        .stat-item { text-align: center; }
        .stat-number {
            font-family: var(--font-head); font-size: 2.2rem; font-weight: 800;
            background: var(--grad-primary);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-label { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem; }
        .hero-scroll {
            position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%);
            display: flex; flex-direction: column; align-items: center; gap: 0.5rem;
            color: var(--text-muted); font-size: 0.75rem;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(8px); }
        }
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ===== SECTIONS ===== */
        section { position: relative; z-index: 1; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
        .section-header { text-align: center; margin-bottom: 4rem; }
        .section-tag {
            display: inline-block;
            font-size: 0.75rem; font-weight: 700; letter-spacing: 0.15em;
            text-transform: uppercase; color: var(--primary-light); margin-bottom: 0.75rem;
        }
        .section-title {
            font-family: var(--font-head);
            font-size: clamp(2rem, 4vw, 2.8rem); font-weight: 800;
            color: white; margin-bottom: 1rem; line-height: 1.2;
        }
        .section-title span {
            background: var(--grad-primary);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .section-desc { font-size: 1.05rem; color: var(--text-muted); max-width: 520px; margin: 0 auto; line-height: 1.7; }
        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary), var(--secondary), transparent);
            opacity: 0.18; margin: 0;
        }

        /* ===== FACILITIES ===== */
        #fasilitas { padding: 6rem 0; }
        .facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .facility-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px; padding: 2rem;
            transition: all 0.4s ease;
            position: relative; overflow: hidden;
        }
        .facility-card::before {
            content: ''; position: absolute; inset: 0;
            background: var(--grad-subtle);
            opacity: 0; transition: opacity 0.4s;
        }
        .facility-card:hover { transform: translateY(-6px); border-color: var(--border-accent); box-shadow: 0 8px 32px rgba(233,30,140,0.1); }
        .facility-card:hover::before { opacity: 1; }
        .facility-icon {
            width: 56px; height: 56px;
            background: var(--grad-primary);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; color: white;
            margin-bottom: 1.25rem;
            box-shadow: 0 0 20px var(--primary-glow);
        }
        .facility-card h3 {
            font-family: var(--font-head); font-weight: 700; font-size: 1.1rem;
            color: white; margin-bottom: 0.5rem; position: relative;
        }
        .facility-card p { font-size: 0.9rem; color: var(--text-muted); line-height: 1.6; position: relative; }

        /* ===== COURTS ===== */
        #lapangan { padding: 6rem 0; background: linear-gradient(180deg, rgba(233,30,140,0.02), rgba(139,92,246,0.03)); }
        .courts-layout {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 3rem; align-items: center;
        }
        .courts-image-wrap {
            position: relative; border-radius: 24px; overflow: hidden;
        }
        .courts-image-wrap img {
            width: 100%; height: 400px; object-fit: cover;
            display: block; transition: transform 0.6s ease;
        }
        .courts-image-wrap:hover img { transform: scale(1.05); }
        .courts-image-wrap::after {
            content: ''; position: absolute; inset: 0;
            border: 1px solid rgba(233,30,140,0.25);
            border-radius: 24px; pointer-events: none;
        }
        .courts-glow {
            position: absolute; inset: -1px;
            border-radius: 24px;
            background: linear-gradient(135deg, rgba(233,30,140,0.18), rgba(139,92,246,0.12), transparent 55%);
            pointer-events: none; opacity: 0.7; z-index: 1;
        }
        .courts-info h2 {
            font-family: var(--font-head); font-size: 2.2rem;
            font-weight: 800; color: white; margin-bottom: 1rem;
        }
        .courts-info p { color: var(--text-muted); line-height: 1.8; margin-bottom: 1.5rem; }
        .courts-list { list-style: none; display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 2rem; }
        .courts-list li {
            display: flex; align-items: center; gap: 0.75rem;
            color: var(--text-primary); font-size: 0.95rem;
        }
        .courts-list li .check {
            width: 22px; height: 22px; border-radius: 50%;
            background: rgba(233,30,140,0.12); border: 1px solid var(--primary);
            display: flex; align-items: center; justify-content: center;
            color: var(--primary-light); font-size: 0.7rem; flex-shrink: 0;
        }

        /* ===== PRICE ===== */
        #harga { padding: 6rem 0; }
        .price-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem; align-items: stretch;
        }
        .price-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 24px; padding: 2.5rem;
            position: relative; overflow: hidden;
            transition: all 0.4s ease;
            display: flex; flex-direction: column;
        }
        .price-card:hover { transform: translateY(-6px); border-color: var(--border-accent); }
        .price-card.featured {
            background: linear-gradient(145deg, rgba(233,30,140,0.12), rgba(139,92,246,0.09));
            border-color: rgba(233,30,140,0.45);
            box-shadow: 0 0 55px rgba(233,30,140,0.12), 0 0 25px rgba(139,92,246,0.08);
        }
        .price-badge {
            display: inline-block;
            background: var(--grad-primary);
            color: white; font-size: 0.7rem; font-weight: 700;
            padding: 0.3rem 0.9rem; border-radius: 50px;
            margin-bottom: 1.5rem; letter-spacing: 0.05em;
            text-transform: uppercase; width: fit-content;
            box-shadow: 0 4px 14px var(--primary-glow);
        }
        .price-label {
            font-family: var(--font-head); font-weight: 700;
            font-size: 1.2rem; color: white; margin-bottom: 0.5rem;
        }
        .price-time { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem; }
        .price-amount {
            font-family: var(--font-head); font-size: 2.5rem;
            font-weight: 900; color: white; margin-bottom: 0.25rem;
        }
        .price-amount span { font-size: 1rem; font-weight: 500; color: var(--text-muted); }
        .price-per { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 2rem; }
        .price-divider { height: 1px; background: var(--border); margin-bottom: 1.5rem; }
        .price-features { list-style: none; display: flex; flex-direction: column; gap: 0.75rem; flex: 1; }
        .price-features li {
            display: flex; align-items: center; gap: 0.75rem;
            font-size: 0.9rem; color: var(--text-muted);
        }
        .price-features li i { color: var(--primary-light); font-size: 0.85rem; }
        .price-card .btn-primary { margin-top: 2rem; justify-content: center; }

        /* ===== HOW TO BOOK ===== */
        #cara-booking { padding: 6rem 0; background: linear-gradient(180deg, rgba(139,92,246,0.03), rgba(233,30,140,0.02)); }
        .steps-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 2rem; position: relative;
        }
        .steps-grid::before {
            content: ''; position: absolute;
            top: 40px; left: 8%; right: 8%; height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary), var(--secondary), transparent);
            opacity: 0.25;
        }
        .step-card {
            text-align: center; padding: 2rem 1.5rem;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 20px; transition: all 0.3s;
            position: relative; z-index: 1;
        }
        .step-card:hover { transform: translateY(-5px); border-color: var(--border-accent); box-shadow: 0 8px 28px rgba(233,30,140,0.1); }
        .step-number {
            width: 64px; height: 64px;
            background: var(--grad-primary);
            border-radius: 50%; margin: 0 auto 1.25rem;
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-head); font-weight: 900; font-size: 1.4rem;
            color: white; box-shadow: 0 0 25px var(--primary-glow);
        }
        .step-card h3 { font-family: var(--font-head); font-weight: 700; color: white; margin-bottom: 0.5rem; }
        .step-card p { font-size: 0.88rem; color: var(--text-muted); line-height: 1.6; }

        /* ===== GALLERY ===== */
        #galeri { padding: 6rem 0; }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(2, 220px);
            gap: 1rem;
        }
        .gallery-item {
            border-radius: 16px; overflow: hidden;
            position: relative; cursor: pointer;
            border: 1px solid transparent;
            transition: border-color 0.3s;
        }
        .gallery-item:first-child { grid-column: span 2; }
        .gallery-item img {
            width: 100%; height: 100%;
            object-fit: cover; display: block;
            transition: transform 0.5s ease;
        }
        .gallery-item:hover { border-color: rgba(233,30,140,0.4); }
        .gallery-item:hover img { transform: scale(1.08); }
        .gallery-item::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(8,14,28,0.75), transparent);
            opacity: 0; transition: opacity 0.3s;
        }
        .gallery-item:hover::after { opacity: 1; }

        /* ===== CONTACT ===== */
        #kontak { padding: 6rem 0; background: linear-gradient(180deg, rgba(233,30,140,0.02), rgba(139,92,246,0.03)); }
        .contact-layout {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 4rem; align-items: start;
        }
        .contact-info h2 {
            font-family: var(--font-head); font-size: 2rem;
            font-weight: 800; color: white; margin-bottom: 1rem;
        }
        .contact-info p { color: var(--text-muted); line-height: 1.8; margin-bottom: 2rem; }
        .contact-items { display: flex; flex-direction: column; gap: 1.25rem; }
        .contact-item {
            display: flex; align-items: flex-start; gap: 1rem;
            padding: 1.2rem; background: var(--bg-card);
            border: 1px solid var(--border); border-radius: 16px;
            transition: all 0.3s; text-decoration: none; color: inherit;
        }
        .contact-item:hover { border-color: var(--border-accent); transform: translateX(4px); background: var(--bg-card-hover); }
        .contact-item-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: var(--grad-primary);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: white; flex-shrink: 0;
            box-shadow: 0 4px 12px var(--primary-glow);
        }
        .contact-item-text strong {
            display: block; color: white; font-weight: 600; margin-bottom: 0.2rem;
        }
        .contact-item-text span { font-size: 0.9rem; color: var(--text-muted); }

        .social-section { margin-top: 2.5rem; }
        .social-section h3 {
            font-family: var(--font-head); font-weight: 700;
            font-size: 1rem; color: white; margin-bottom: 1rem;
        }
        .social-links { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .social-link {
            display: flex; align-items: center; gap: 0.6rem;
            padding: 0.65rem 1.2rem;
            border-radius: 12px; border: 1px solid var(--border);
            background: var(--bg-card); color: var(--text-muted);
            text-decoration: none; font-size: 0.88rem; font-weight: 500;
            transition: all 0.3s;
        }
        .social-link i { font-size: 1rem; transition: transform 0.2s; }
        .social-link:hover { transform: translateY(-2px); border-color: var(--border-accent); color: var(--primary-light); }
        .social-link:hover i { transform: scale(1.2); }

        .contact-form {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 24px; padding: 2.5rem;
        }
        .contact-form h3 {
            font-family: var(--font-head); font-size: 1.4rem;
            font-weight: 700; color: white; margin-bottom: 1.75rem;
        }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block; font-size: 0.85rem; font-weight: 600;
            color: var(--text-muted); margin-bottom: 0.5rem;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 0.85rem 1rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border); border-radius: 12px;
            color: var(--text-primary); font-family: var(--font-body);
            font-size: 0.95rem; outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }
        .form-group textarea { height: 120px; resize: vertical; }
        .form-group select option { background: var(--bg-mid); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

        /* ===== MAP ===== */
        .map-wrap {
            border-radius: 20px; overflow: hidden;
            border: 1px solid var(--border-accent);
            height: 280px; margin-top: 2rem; position: relative;
        }
        .map-wrap iframe { width: 100%; height: 100%; display: block; filter: grayscale(80%) invert(90%); }

        /* ===== FOOTER ===== */
        footer {
            padding: 4rem 0 2rem;
            border-top: 1px solid var(--border-accent);
            position: relative; z-index: 1;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem; margin-bottom: 3rem;
        }
        .footer-brand p { font-size: 0.9rem; color: var(--text-muted); line-height: 1.7; margin-top: 1rem; margin-bottom: 1.5rem; }
        .footer-social { display: flex; gap: 0.75rem; }
        .footer-social-btn {
            width: 40px; height: 40px; border-radius: 10px;
            background: var(--bg-card); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted); text-decoration: none; font-size: 1rem;
            transition: all 0.3s;
        }
        .footer-social-btn:hover {
            background: var(--grad-primary); color: white;
            border-color: var(--primary); transform: translateY(-2px);
            box-shadow: 0 6px 18px var(--primary-glow);
        }
        .footer-col h4 {
            font-family: var(--font-head); font-weight: 700;
            font-size: 0.95rem; color: white; margin-bottom: 1.25rem;
        }
        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 0.6rem; }
        .footer-links a {
            font-size: 0.875rem; color: var(--text-muted);
            text-decoration: none; transition: color 0.2s;
        }
        .footer-links a:hover { color: var(--primary-light); }
        .footer-bottom {
            border-top: 1px solid var(--border); padding-top: 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 1rem;
        }
        .footer-bottom p { font-size: 0.85rem; color: var(--text-muted); }
        .footer-bottom .made-with { display: flex; align-items: center; gap: 0.4rem; color: var(--text-muted); font-size: 0.85rem; }
        .footer-bottom .made-with i { color: var(--primary-light); }
        .online-badge {
            display: inline-flex; align-items: center; gap: 0.4rem;
            font-size: 0.8rem; color: var(--primary-light);
        }
        .online-badge span { width: 8px; height: 8px; border-radius: 50%; background: var(--primary); animation: pulse 2s infinite; }

        /* ===== FLOATING WA ===== */
        .float-wa {
            position: fixed; bottom: 2rem; right: 2rem;
            width: 58px; height: 58px; border-radius: 50%;
            background: #25d366;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1.6rem; text-decoration: none;
            box-shadow: 0 8px 25px rgba(37,211,102,0.4);
            z-index: 999; transition: all 0.3s;
            animation: floatPulse 3s ease-in-out infinite;
        }
        .float-wa:hover { transform: scale(1.1); box-shadow: 0 12px 35px rgba(37,211,102,0.5); }
        @keyframes floatPulse {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .float-wa .tooltip {
            position: absolute; right: 70px;
            background: #1a9e4e; color: white;
            padding: 0.4rem 0.8rem; border-radius: 8px;
            font-size: 0.8rem; font-weight: 600; white-space: nowrap;
            opacity: 0; pointer-events: none; transition: opacity 0.2s;
        }
        .float-wa:hover .tooltip { opacity: 1; }

        /* ===== MOBILE NAV ===== */
        .mobile-menu {
            display: none; position: fixed; inset: 0; z-index: 999;
            background: rgba(8,14,28,0.97); backdrop-filter: blur(20px);
            flex-direction: column; align-items: center; justify-content: center;
            gap: 2rem;
        }
        .mobile-menu.open { display: flex; }
        .mobile-menu a {
            font-family: var(--font-head); font-size: 2rem; font-weight: 700;
            color: var(--text-muted); text-decoration: none; transition: color 0.2s;
        }
        .mobile-menu a:hover { color: var(--primary-light); }
        .mobile-close {
            position: absolute; top: 1.5rem; right: 1.5rem;
            background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;
        }

        /* ===== SCROLL REVEAL ===== */
        .reveal { opacity: 0; transform: translateY(40px); transition: all 0.7s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 2rem; }
            .courts-layout { grid-template-columns: 1fr; }
            .courts-image-wrap img { height: 300px; }
        }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hamburger { display: block; }
            .contact-layout { grid-template-columns: 1fr; }
            .gallery-grid { grid-template-columns: 1fr 1fr; grid-template-rows: auto; }
            .gallery-item:first-child { grid-column: span 2; }
            .footer-grid { grid-template-columns: 1fr; }
            .steps-grid::before { display: none; }
            .form-row { grid-template-columns: 1fr; }
            .hero-stats { gap: 2rem; }
        }
        @media (max-width: 480px) {
            .gallery-grid { grid-template-columns: 1fr; }
            .gallery-item:first-child { grid-column: span 1; }
        }
    </style>
</head>
<body>

    <!-- BG EFFECTS -->
    <div class="bg-grid"></div>
    <div class="bg-orb orb-1"></div>
    <div class="bg-orb orb-2"></div>
    <div class="bg-orb orb-3"></div>

    {{-- FLOATING WA — tombol mengambang WhatsApp di pojok kanan bawah --}}
    <a href="https://wa.me/6285248867071" target="_blank" class="float-wa" id="float-wa" aria-label="WhatsApp">
        <i class="fab fa-whatsapp"></i>
        <span class="tooltip">Chat via WhatsApp</span>
    </a>

    <!-- MOBILE MENU -->
    <div class="mobile-menu" id="mobileMenu">
        <button class="mobile-close" id="mobileClose" aria-label="Tutup Menu"><i class="fas fa-times"></i></button>
        <a href="#fasilitas" class="mobile-link">Fasilitas</a>
        <a href="#lapangan" class="mobile-link">Lapangan</a>
        <a href="#harga" class="mobile-link">Harga</a>
        <a href="#galeri" class="mobile-link">Galeri</a>
        <a href="#kontak" class="mobile-link">Kontak</a>
        <a href="{{ route('login') }}" class="btn-primary" style="font-size:1rem; padding: 0.85rem 2rem;">
            <span>Reservasi Sekarang</span>
        </a>
    </div>

    <!-- NAVBAR -->
    <nav id="navbar">
        <a href="#hero" class="nav-logo">
            <div class="nav-logo-icon">🏸</div>
            <div class="nav-logo-text">Adenia <span>Salsa</span></div>
        </a>
        <ul class="nav-links">
            <li><a href="#fasilitas">Fasilitas</a></li>
            <li><a href="#lapangan">Lapangan</a></li>
            <li><a href="#harga">Harga</a></li>
            <li><a href="#cara-booking">Cara Booking</a></li>
            <li><a href="#galeri">Galeri</a></li>
            <li><a href="#kontak">Kontak</a></li>
            <li><a href="{{ route('login') }}" class="nav-cta">Reservasi ✦</a></li>
        </ul>
        <button class="hamburger" id="hamburger" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>
    </nav>

    <!-- HERO -->
    <section id="hero">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-badge">
                <span class="dot"></span>
                Lapangan Badminton Profesional
            </div>
            <h1>
                Arena <span class="highlight">Badminton</span><br>
                Terbaik di Kota Anda
            </h1>
            <p>
                Lapangan Badminton <strong style="color: var(--primary-light);">Adenia Salsa</strong> — fasilitas modern, lantai berkualitas premium,
                dan sistem reservasi online yang mudah. Siap melayani sesi latihan hingga turnamen.
            </p>
            <div class="hero-btns">
                <a href="{{ route('login') }}" class="btn-primary">
                    <span>🏸 Reservasi Sekarang</span>
                </a>
                <a href="#lapangan" class="btn-secondary">
                    <i class="fas fa-play-circle"></i> Lihat Lapangan
                </a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number" data-target="6">0</div>
                    <div class="stat-label">Lapangan Aktif</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-target="500">0</div>
                    <div class="stat-label">Member Aktif</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-target="3">0</div>
                    <div class="stat-label">Tahun Beroperasi</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-target="98">0</div>
                    <div class="stat-label">% Kepuasan</div>
                </div>
            </div>
        </div>
        <div class="hero-scroll">
            <span>Scroll</span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- FASILITAS -->
    <section id="fasilitas">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-tag">✦ Fasilitas Kami</div>
                <h2 class="section-title">Semua yang Anda <span>Butuhkan</span></h2>
                <p class="section-desc">Kami menyediakan fasilitas lengkap untuk kenyamanan bermain Anda dari awal hingga akhir sesi.</p>
            </div>
            <div class="facilities-grid">
                <div class="facility-card reveal reveal-delay-1">
                    <div class="facility-icon">🏸</div>
                    <h3>Lapangan Standar BWF</h3>
                    <p>6 lapangan berstandar Badminton World Federation dengan lantai kayu sintetis premium anti-slip.</p>
                </div>
                <div class="facility-card reveal reveal-delay-2">
                    <div class="facility-icon">💡</div>
                    <h3>Pencahayaan LED Profesional</h3>
                    <p>Lampu LED 1000 lux dengan distribusi cahaya merata, tanpa bayangan silang untuk visibilitas optimal.</p>
                </div>
                <div class="facility-card reveal reveal-delay-3">
                    <div class="facility-icon">❄️</div>
                    <h3>AC & Sirkulasi Udara</h3>
                    <p>Sistem pendingin udara modern memastikan kenyamanan bermain sepanjang hari dalam kondisi cuaca apapun.</p>
                </div>
                <div class="facility-card reveal reveal-delay-4">
                    <div class="facility-icon">🚿</div>
                    <h3>Kamar Mandi & Loker</h3>
                    <p>Fasilitas kamar mandi bersih terpisah pria & wanita, dilengkapi loker keamanan untuk barang bawaan.</p>
                </div>
                <div class="facility-card reveal reveal-delay-1">
                    <div class="facility-icon">☕</div>
                    <h3>Kantin & Minuman</h3>
                    <p>Area kantin menyajikan minuman segar, makanan ringan, dan suplemen olahraga untuk menjaga stamina.</p>
                </div>
                <div class="facility-card reveal reveal-delay-2">
                    <div class="facility-icon">📱</div>
                    <h3>Reservasi Online 24/7</h3>
                    <p>Pesan lapangan kapan saja dan dari mana saja melalui sistem reservasi online terintegrasi.</p>
                </div>
                <div class="facility-card reveal reveal-delay-3">
                    <div class="facility-icon">🅿️</div>
                    <h3>Parkir Luas & Gratis</h3>
                    <p>Area parkir kendaraan roda 2 dan roda 4 yang luas tersedia tanpa biaya tambahan bagi pengunjung.</p>
                </div>
                <div class="facility-card reveal reveal-delay-4">
                    <div class="facility-icon">🔒</div>
                    <h3>Keamanan 24 Jam</h3>
                    <p>CCTV aktif dan petugas keamanan berjaga memastikan lingkungan bermain yang aman dan nyaman.</p>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- LAPANGAN -->
    <section id="lapangan">
        <div class="container">
            <div class="courts-layout">
                <div class="courts-image-wrap reveal">
                    <div class="courts-glow"></div>
                    <img src="/images/court-interior.png" alt="Interior Lapangan Badminton Adenia Salsa">
                </div>
                <div class="courts-info reveal reveal-delay-2">
                    <div class="section-tag">✦ Tentang Lapangan</div>
                    <h2>Lapangan Berkelas<br>
                        <span style="background: var(--grad-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Pengalaman Terbaik</span>
                    </h2>
                    <p>Adenia Salsa Badminton dibangun dengan standar lapangan internasional. Setiap detail dirancang untuk memberikan pengalaman bermain yang sempurna, dari tekstur lantai hingga ketinggian atap.</p>
                    <ul class="courts-list">
                        <li><span class="check"><i class="fas fa-check"></i></span> 6 lapangan aktif dengan karpet sintetis premium</li>
                        <li><span class="check"><i class="fas fa-check"></i></span> Tinggi atap 9 meter standar internasional</li>
                        <li><span class="check"><i class="fas fa-check"></i></span> Net badminton standar BWF, diganti secara berkala</li>
                        <li><span class="check"><i class="fas fa-check"></i></span> Lahan parkir luas untuk 50+ kendaraan</li>
                        <li><span class="check"><i class="fas fa-check"></i></span> Buka setiap hari pukul 07.00 – 23.00 WIB</li>
                        <li><span class="check"><i class="fas fa-check"></i></span> Tersedia peralatan sewa (raket & kok)</li>
                    </ul>
                    <a href="{{ route('login') }}" class="btn-primary" style="width: fit-content;">
                        <span>Booking Lapangan</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- HARGA -->
    <section id="harga">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-tag">✦ Tarif Sewa</div>
                <h2 class="section-title">Harga <span>Transparan</span></h2>
                <p class="section-desc">Nikmati lapangan premium dengan harga yang terjangkau. Tidak ada biaya tersembunyi.</p>
            </div>
            <div class="price-grid">
                <div class="price-card reveal reveal-delay-1">
                    <div class="price-label">Pagi Hari</div>
                    <div class="price-time"><i class="fas fa-sun" style="color: #fbbf24;"></i> 07:00 – 12:00 WIB</div>
                    <div class="price-amount">Rp 40<span>.000</span></div>
                    <div class="price-per">per jam / lapangan</div>
                    <div class="price-divider"></div>
                    <ul class="price-features">
                        <li><i class="fas fa-check-circle"></i> Akses penuh lapangan</li>
                        <li><i class="fas fa-check-circle"></i> Pencahayaan optimal</li>
                        <li><i class="fas fa-check-circle"></i> Fasilitas kamar mandi</li>
                        <li><i class="fas fa-check-circle"></i> Area parkir gratis</li>
                    </ul>
                    <a href="{{ route('login') }}" class="btn-primary"><span>Pesan Sekarang</span></a>
                </div>
                <div class="price-card featured reveal reveal-delay-2">
                    <div class="price-badge">⚡ Paling Populer</div>
                    <div class="price-label">Sore & Malam</div>
                    <div class="price-time"><i class="fas fa-moon" style="color: var(--secondary-light);"></i> 17:00 – 23:00 WIB</div>
                    <div class="price-amount">Rp 60<span>.000</span></div>
                    <div class="price-per">per jam / lapangan</div>
                    <div class="price-divider"></div>
                    <ul class="price-features">
                        <li><i class="fas fa-check-circle"></i> Akses penuh lapangan</li>
                        <li><i class="fas fa-check-circle"></i> Pencahayaan LED terang</li>
                        <li><i class="fas fa-check-circle"></i> Fasilitas kamar mandi</li>
                        <li><i class="fas fa-check-circle"></i> Area parkir gratis</li>
                        <li><i class="fas fa-check-circle"></i> Prioritas booking berikutnya</li>
                    </ul>
                    <a href="{{ route('login') }}" class="btn-primary"><span>Pesan Sekarang</span></a>
                </div>
                <div class="price-card reveal reveal-delay-3">
                    <div class="price-label">Siang Hari</div>
                    <div class="price-time"><i class="fas fa-cloud-sun" style="color: #f97316;"></i> 12:00 – 17:00 WIB</div>
                    <div class="price-amount">Rp 50<span>.000</span></div>
                    <div class="price-per">per jam / lapangan</div>
                    <div class="price-divider"></div>
                    <ul class="price-features">
                        <li><i class="fas fa-check-circle"></i> Akses penuh lapangan</li>
                        <li><i class="fas fa-check-circle"></i> Pencahayaan natural & LED</li>
                        <li><i class="fas fa-check-circle"></i> Fasilitas kamar mandi</li>
                        <li><i class="fas fa-check-circle"></i> Area parkir gratis</li>
                    </ul>
                    <a href="{{ route('login') }}" class="btn-primary"><span>Pesan Sekarang</span></a>
                </div>
            </div>
            <p class="reveal" style="text-align:center; margin-top: 2rem; color: var(--text-muted); font-size: 0.9rem;">
                💡 Harga untuk <strong style="color: var(--text-primary);">akhir pekan</strong> mungkin berbeda. Hubungi kami untuk info member & paket bulanan.
            </p>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- CARA BOOKING -->
    <section id="cara-booking">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-tag">✦ Cara Reservasi</div>
                <h2 class="section-title">Booking <span>Mudah</span> & Cepat</h2>
                <p class="section-desc">Hanya 4 langkah untuk mengamankan lapangan favorit Anda.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card reveal reveal-delay-1">
                    <div class="step-number">1</div>
                    <h3>Daftar / Login</h3>
                    <p>Buat akun gratis atau masuk ke akun yang sudah ada di platform reservasi kami.</p>
                </div>
                <div class="step-card reveal reveal-delay-2">
                    <div class="step-number">2</div>
                    <h3>Pilih Jadwal</h3>
                    <p>Pilih tanggal, jam, dan lapangan yang tersedia sesuai keinginan Anda.</p>
                </div>
                <div class="step-card reveal reveal-delay-3">
                    <div class="step-number">3</div>
                    <h3>Bayar Online</h3>
                    <p>Lakukan pembayaran aman via transfer bank, e-wallet, atau kartu kredit.</p>
                </div>
                <div class="step-card reveal reveal-delay-4">
                    <div class="step-number">4</div>
                    <h3>Mainkan!</h3>
                    <p>Datang ke lapangan sesuai jadwal dan tunjukkan bukti booking. Selamat bermain!</p>
                </div>
            </div>
            <div class="reveal" style="text-align: center; margin-top: 3rem;">
                <a href="{{ route('login') }}" class="btn-primary" style="display: inline-flex;">
                    <span>Mulai Reservasi</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- GALERI -->
    <section id="galeri">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-tag">✦ Galeri</div>
                <h2 class="section-title">Lihat <span>Fasilitasnya</span></h2>
                <p class="section-desc">Sekilas pandang fasilitas lapangan badminton Adenia Salsa.</p>
            </div>
            <div class="gallery-grid reveal">
                <div class="gallery-item">
                    <img src="/images/court-interior.png" alt="Interior Lapangan">
                </div>
                <div class="gallery-item">
                    <img src="/images/hero-court.png" alt="Lapangan Atas">
                </div>
                <div class="gallery-item">
                    <img src="/images/court-interior.png" alt="Area Duduk">
                </div>
                <div class="gallery-item">
                    <img src="/images/hero-court.png" alt="Net Lapangan">
                </div>
                <div class="gallery-item">
                    <img src="/images/court-interior.png" alt="Pencahayaan">
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- KONTAK -->
    <section id="kontak">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-tag">✦ Hubungi Kami</div>
                <h2 class="section-title">Kontak & <span>Lokasi</span></h2>
                <p class="section-desc">Kami siap membantu Anda. Hubungi kami melalui berbagai saluran yang tersedia.</p>
            </div>
            <div class="contact-layout">
                <div class="contact-info reveal">
                    <h2>Temukan Kami</h2>
                    <p>Kunjungi atau hubungi Lapangan Badminton Adenia Salsa. Tim kami siap melayani Anda setiap hari.</p>
                    <div class="contact-items">
                        {{-- Alamat lengkap lapangan di Banjarmasin — klik untuk buka Google Maps --}}
                        <a href="https://www.google.com/maps/place/lapangan+adenia+salsa/data=!4m2!3m1!1s0x2de423003e4fb8eb:0x4b3d95a902753586" target="_blank" class="contact-item">
                            <div class="contact-item-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div class="contact-item-text">
                                <strong>Alamat</strong>
                                <span>Jl. Pangeran Hidayatullah, Jl. Sungai Andai No.33, Sungai Jingah, Banjarmasin Utara, Kota Banjarmasin</span>
                            </div>
                        </a>
                        {{-- WhatsApp sebagai saluran komunikasi utama --}}
                        <a href="https://wa.me/6285248867071" target="_blank" class="contact-item">
                            <div class="contact-item-icon"><i class="fab fa-whatsapp"></i></div>
                            <div class="contact-item-text">
                                <strong>WhatsApp</strong>
                                <span>+62 852-4886-7071 (Chat & Booking)</span>
                            </div>
                        </a>
                        {{-- Instagram sebagai media sosial utama --}}
                        <a href="https://instagram.com/adenia.badmintoncourt" target="_blank" class="contact-item">
                            <div class="contact-item-icon"><i class="fab fa-instagram"></i></div>
                            <div class="contact-item-text">
                                <strong>Instagram</strong>
                                <span>@adenia.badmintoncourt</span>
                            </div>
                        </a>
                        {{-- Jam operasional lapangan --}}
                        <div class="contact-item" style="cursor: default;">
                            <div class="contact-item-icon"><i class="fas fa-clock"></i></div>
                            <div class="contact-item-text">
                                <strong>Jam Operasional</strong>
                                <span>Senin – Minggu: 07.00 – 23.00 WIB</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol media sosial yang aktif: hanya Instagram & WhatsApp --}}
                    <div class="social-section">
                        <h3>Ikuti Kami di Media Sosial</h3>
                        <div class="social-links">
                            <a href="https://instagram.com/adenia.badmintoncourt" target="_blank" class="social-link instagram">
                                <i class="fab fa-instagram"></i> Instagram
                            </a>
                            <a href="https://wa.me/6285248867071" target="_blank" class="social-link whatsapp">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                    </div>

                    {{-- Embed Google Maps — Lapangan Adenia Salsa (Place ID: 0x2de423003e4fb8eb:0x4b3d95a902753586) --}}
                    <div class="map-wrap">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3967.8!2d114.5872!3d-3.3148!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2de423003e4fb8eb%3A0x4b3d95a902753586!2sLapangan%20Adenia%20Salsa!5e0!3m2!1sid!2sid!4v1"
                            allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Lokasi Lapangan Badminton Adenia Salsa – Banjarmasin"
                        ></iframe>
                    </div>
                </div>

                <div class="contact-form reveal reveal-delay-2">
                    <h3>💬 Kirim Pesan</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact-name">Nama Lengkap</label>
                            <input type="text" id="contact-name" placeholder="John Doe">
                        </div>
                        <div class="form-group">
                            <label for="contact-phone">No. WhatsApp</label>
                            <input type="tel" id="contact-phone" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contact-email">Email</label>
                        <input type="email" id="contact-email" placeholder="nama@email.com">
                    </div>
                    <div class="form-group">
                        <label for="contact-subject">Topik</label>
                        <select id="contact-subject">
                            <option value="">-- Pilih Topik --</option>
                            <option>Reservasi Lapangan</option>
                            <option>Informasi Harga</option>
                            <option>Paket Member</option>
                            <option>Turnamen & Event</option>
                            <option>Kerjasama</option>
                            <option>Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contact-message">Pesan</label>
                        <textarea id="contact-message" placeholder="Tuliskan pertanyaan atau kebutuhan Anda di sini..."></textarea>
                    </div>
                    <button type="button" class="btn-primary" onclick="sendMessage()" style="width:100%; justify-content:center; margin-top: 0.5rem;">
                        <span>Kirim via WhatsApp</span>
                        <i class="fab fa-whatsapp"></i>
                    </button>
                    <p style="font-size: 0.8rem; color: var(--text-muted); text-align: center; margin-top: 0.75rem;">
                        Pesan Anda akan diteruskan via WhatsApp ke admin kami.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="#hero" class="nav-logo" style="text-decoration: none;">
                        <div class="nav-logo-icon">🏸</div>
                        <div class="nav-logo-text">Adenia <span>Salsa</span></div>
                    </a>
                    <p>
                        Lapangan Badminton Adenia Salsa – destinasi olahraga badminton terbaik dengan fasilitas modern dan layanan prima untuk semua kalangan.
                    </p>
                    {{-- Ikon sosial media footer: hanya Instagram & WhatsApp --}}
                    <div class="footer-social">
                        <a href="https://instagram.com/adenia.badmintoncourt" target="_blank" class="footer-social-btn" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/6285248867071" target="_blank" class="footer-social-btn" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Navigasi</h4>
                    <ul class="footer-links">
                        <li><a href="#fasilitas">Fasilitas</a></li>
                        <li><a href="#lapangan">Lapangan</a></li>
                        <li><a href="#harga">Harga Sewa</a></li>
                        <li><a href="#cara-booking">Cara Booking</a></li>
                        <li><a href="#galeri">Galeri</a></li>
                        <li><a href="#kontak">Kontak</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Layanan</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('login') }}">Reservasi Online</a></li>
                        <li><a href="#harga">Paket Member</a></li>
                        <li><a href="#kontak">Sewa Peralatan</a></li>
                        <li><a href="#kontak">Event & Turnamen</a></li>
                        <li><a href="#kontak">Coaching / Pelatihan</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Info Cepat</h4>
                    <ul class="footer-links">
                        <li><a href="https://wa.me/6285248867071" target="_blank">💬 WhatsApp Kami</a></li>
                        <li><a href="https://instagram.com/adenia.badmintoncourt" target="_blank">📸 Instagram Kami</a></li>
                        <li><a href="#kontak">📍 Lihat Lokasi</a></li>
                        <li><span class="online-badge"><span></span> Sistem Online</span></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Lapangan Badminton Adenia Salsa. Semua hak dilindungi.</p>
                <div class="made-with">
                    Dibuat dengan <i class="fas fa-heart"></i> oleh <strong>M Risky Alpin Redo</strong>
                </div>
            </div>
        </div>
    </footer>

    <script>
        /**
         * ============================================================
         * LANDING PAGE — JAVASCRIPT INTERAKTIF
         * Adenia Salsa Badminton — resources/views/landing.blade.php
         * ============================================================
         * Berisi semua logika interaksi client-side untuk landing page:
         *   1. Navbar glassmorphism saat scroll
         *   2. Mobile hamburger menu
         *   3. Scroll reveal animasi elemen
         *   4. Counter angka statistik hero
         *   5. Form kontak kirim pesan via WhatsApp
         *   6. Highlight link navbar aktif sesuai section
         *   7. Efek parallax pada background hero
         * ============================================================
         */

        // ============================================================
        // 1. NAVBAR SCROLL EFFECT
        // ============================================================
        // Saat pengguna scroll lebih dari 50px, tambahkan class 'scrolled'
        // ke navbar agar tampil dengan background blur (glassmorphism).
        // Efek ini didefinisikan di CSS: nav.scrolled { background: ...; backdrop-filter: blur(); }
        // ============================================================
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });

        // ============================================================
        // 2. MOBILE HAMBURGER MENU
        // ============================================================
        // Mengontrol buka/tutup menu navigasi mobile (#mobileMenu).
        // - Tombol hamburger (≡) membuka menu fullscreen
        // - Tombol tutup (✕) menutup menu
        // - Klik salah satu link menu juga otomatis menutup menu
        // ============================================================
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileClose = document.getElementById('mobileClose');

        // Buka menu saat tombol hamburger diklik
        hamburger.addEventListener('click', () => mobileMenu.classList.add('open'));

        // Tutup menu saat tombol ✕ diklik
        mobileClose.addEventListener('click', () => mobileMenu.classList.remove('open'));

        // Tutup menu otomatis saat salah satu link navigasi diklik
        document.querySelectorAll('.mobile-link').forEach(link => {
            link.addEventListener('click', () => mobileMenu.classList.remove('open'));
        });

        // ============================================================
        // 3. SCROLL REVEAL — ANIMASI ELEMEN MUNCUL
        // ============================================================
        // Menggunakan IntersectionObserver untuk memantau elemen dengan
        // class '.reveal'. Saat elemen masuk viewport (terlihat 10%),
        // class 'visible' ditambahkan sehingga animasi fadeSlideUp
        // dari CSS berjalan (opacity 0→1, translateY 40px→0).
        // Dipakai di: kartu fasilitas, section lapangan, harga, dll.
        // ============================================================
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                // Tambahkan 'visible' hanya saat elemen masuk viewport
                if (entry.isIntersecting) entry.target.classList.add('visible');
            });
        }, { threshold: 0.1 }); // Animasi mulai saat 10% elemen terlihat

        // Daftarkan semua elemen .reveal ke observer
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

        // ============================================================
        // 4. COUNTER ANIMASI ANGKA STATISTIK (HERO)
        // ============================================================
        // Menganimasikan angka statistik di section Hero (mis: "6+", "500+", "98%")
        // dari 0 hingga nilai target saat elemen masuk viewport.
        // Nilai target diambil dari atribut data-target="{angka}" di HTML.
        // Suffix ditambah otomatis: '%' jika target=98, '+' jika target>10.
        // Flag dataset.done mencegah animasi berjalan ulang jika di-scroll.
        // ============================================================
        const counters = document.querySelectorAll('.stat-number');
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                // Jalankan hanya sekali (cek flag 'done') saat terlihat 50%
                if (entry.isIntersecting && !entry.target.dataset.done) {
                    entry.target.dataset.done = true; // Tandai sudah dijalankan

                    const target = parseInt(entry.target.dataset.target); // Nilai akhir
                    let current = 0;
                    const step = target / 60; // Kecepatan: selesai dalam ~60 frame

                    // Interval 20ms ≈ 50fps — angka naik bertahap sampai target
                    const timer = setInterval(() => {
                        current += step;
                        if (current >= target) {
                            clearInterval(timer); // Hentikan saat mencapai target
                            // Tampilkan angka final dengan suffix yang sesuai
                            entry.target.textContent = target + (target === 98 ? '%' : target > 10 ? '+' : '');
                        } else {
                            // Tampilkan angka sementara (dibulatkan ke bawah)
                            entry.target.textContent = Math.floor(current) + (target === 98 ? '%' : target > 10 ? '+' : '');
                        }
                    }, 20);
                }
            });
        }, { threshold: 0.5 }); // Animasi mulai saat 50% elemen terlihat

        // Daftarkan semua elemen counter ke observer
        counters.forEach(counter => counterObserver.observe(counter));

        // ============================================================
        // 5. FORM KONTAK — KIRIM PESAN VIA WHATSAPP
        // ============================================================
        // Mengumpulkan data dari form kontak (nama, no. HP, topik, pesan),
        // memformatnya menjadi teks WhatsApp yang rapi, lalu membuka
        // wa.me link di tab baru agar langsung tersambung ke admin.
        // Validasi sederhana: nama dan pesan wajib diisi.
        // ============================================================
        function sendMessage() {
            // Ambil nilai dari setiap field form
            const name    = document.getElementById('contact-name').value;
            const phone   = document.getElementById('contact-phone').value;
            const subject = document.getElementById('contact-subject').value;
            const message = document.getElementById('contact-message').value;

            // Validasi: nama dan pesan tidak boleh kosong
            if (!name || !message) { alert('Harap isi nama dan pesan terlebih dahulu.'); return; }

            // Format pesan WhatsApp dengan template yang informatif
            const text = encodeURIComponent(
                `*Pesan dari Website Adenia Salsa*\n\n` +
                `👤 Nama: ${name}\n📞 No. HP: ${phone || '-'}\n📋 Topik: ${subject || '-'}\n\n📝 Pesan:\n${message}`
            );

            // Buka WhatsApp Web/App dengan pesan yang sudah diformat
            window.open(`https://wa.me/6285248867071?text=${text}`, '_blank');
        }

        // ============================================================
        // 6. HIGHLIGHT LINK NAVBAR AKTIF
        // ============================================================
        // Memantau posisi scroll dan menentukan section mana yang
        // sedang aktif di viewport. Link navbar yang sesuai section
        // aktif akan diberi warna pink (--primary-light).
        // Offset 120px agar highlight muncul sebelum section tepat di atas.
        // ============================================================
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-links a:not(.nav-cta)');

        window.addEventListener('scroll', () => {
            let current = ''; // Menyimpan ID section yang sedang aktif

            // Cari section terakhir yang sudah di-scroll melewati batasnya
            sections.forEach(section => {
                if (window.scrollY >= section.offsetTop - 120) current = section.getAttribute('id');
            });

            // Terapkan warna highlight ke link yang sesuai dengan section aktif
            navLinks.forEach(link => {
                link.style.color = link.href.includes(current) && current ? 'var(--primary-light)' : '';
            });
        });

        // ============================================================
        // 7. PARALLAX EFFECT — BACKGROUND HERO
        // ============================================================
        // Membuat efek kedalaman (parallax) pada gambar background Hero.
        // Saat user scroll ke bawah, background bergerak lebih lambat (0.3x)
        // dibanding konten, menciptakan ilusi kedalaman 3D.
        // Efek ini hanya aktif selama hero masih terlihat (scrollY < tinggi viewport).
        // ============================================================
        window.addEventListener('scroll', () => {
            const heroBg = document.querySelector('#hero .hero-bg');
            if (heroBg && window.scrollY < window.innerHeight) {
                // Geser background 30% dari jarak scroll (lebih lambat = efek parallax)
                heroBg.style.transform = `translateY(${window.scrollY * 0.3}px)`;
            }
        });
    </script>
</body>
</html>

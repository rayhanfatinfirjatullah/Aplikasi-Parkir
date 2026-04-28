<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="IntegraPark — Sistem Manajemen Parkir Profesional. Smart Access. Solid Integrity.">
    <title>IntegraPark — Smart Parking Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #020617;
            color: #e2e8f0;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* ── Animated grid background ── */
        .grid-bg {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(34,211,238,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(34,211,238,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            animation: grid-pulse 8s ease-in-out infinite;
        }
        @keyframes grid-pulse {
            0%, 100% { opacity: 0.4; }
            50%       { opacity: 1; }
        }

        /* ── Ambient orbs ── */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            animation: orb-float 12s ease-in-out infinite;
        }
        .orb-1 { width:600px; height:600px; background:rgba(6,182,212,0.06); top:-200px; right:-200px; animation-delay: 0s; }
        .orb-2 { width:500px; height:500px; background:rgba(59,130,246,0.05); bottom:-150px; left:-150px; animation-delay: 4s; }
        .orb-3 { width:300px; height:300px; background:rgba(6,182,212,0.04); top:50%; left:50%; transform:translate(-50%,-50%); animation-delay: 8s; }
        @keyframes orb-float {
            0%, 100% { transform: translate(0,0) scale(1); }
            33%       { transform: translate(30px,-30px) scale(1.05); }
            66%       { transform: translate(-20px,20px) scale(0.95); }
        }
        .orb-3 { animation-name: orb-pulse; }
        @keyframes orb-pulse {
            0%, 100% { opacity: 0.5; transform: translate(-50%,-50%) scale(1); }
            50%       { opacity: 1;   transform: translate(-50%,-50%) scale(1.3); }
        }

        /* ── Card entrance ── */
        @keyframes card-in {
            from { opacity: 0; transform: translateY(32px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0)    scale(1); }
        }
        .card-animate {
            animation: card-in 0.8s cubic-bezier(0.16,1,0.3,1) both;
        }

        /* ── Glowing text ── */
        .text-glow {
            text-shadow: 0 0 30px rgba(34,211,238,0.5), 0 0 60px rgba(34,211,238,0.2);
        }

        /* ── Button ── */
        .btn-enter {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 32px;
            background: linear-gradient(135deg, #06b6d4, #3b82f6);
            color: #ffffff;
            font-weight: 700;
            font-size: 15px;
            border-radius: 12px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 30px rgba(6,182,212,0.3);
            transition: all 0.2s ease;
            letter-spacing: 0.02em;
        }
        .btn-enter:hover {
            box-shadow: 0 12px 40px rgba(6,182,212,0.5);
            transform: translateY(-2px);
        }
        .btn-enter:active {
            transform: translateY(0) scale(0.98);
        }

        /* ── Feature badges ── */
        .feature-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: rgba(15,23,42,0.8);
            border: 1px solid rgba(30,41,59,1);
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
            color: #94a3b8;
            backdrop-filter: blur(8px);
        }
        .feature-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
        }

        /* ── Stats strip ── */
        .stat-strip {
            display: flex;
            gap: 32px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 48px;
        }
        .stat-item { text-align: center; }
        .stat-num {
            font-size: 28px;
            font-weight: 900;
            color: #e2e8f0;
            letter-spacing: -0.02em;
        }
        .stat-label {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    <!-- Backgrounds -->
    <div class="grid-bg"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Main Content -->
    <div class="relative z-10 text-center px-6 max-w-3xl mx-auto card-animate">

        <!-- Logo -->
        <div style="display:inline-flex; align-items:center; justify-content:center; margin-bottom:32px;">
            <div style="position:relative;">
                <div style="position:absolute; inset:0; background:rgba(6,182,212,0.2); border-radius:20px; filter:blur(20px); transform:scale(1.3);"></div>
                <div style="position:relative; width:80px; height:80px; background:#0F172A; border:1px solid rgba(6,182,212,0.3); border-radius:20px; display:flex; align-items:center; justify-content:center; box-shadow: 0 0 40px rgba(6,182,212,0.15);">
                    <img src="{{ asset('img/5.png') }}" alt="IntegraPark" style="width:70px; height:70px; object-fit:contain;">
                </div>
            </div>
        </div>

        <!-- Headline -->
        <div style="display:flex; align-items:center; justify-content:center; gap:12px; margin-bottom:12px; flex-wrap:wrap;">
            <div class="feature-badge">
                <span class="feature-dot" style="background:#22d3ee; box-shadow: 0 0 6px rgba(34,211,238,0.8); animation: blink 2s ease-in-out infinite;"></span>
                <style>@keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }</style>
                Sistem Aktif
            </div>
        </div>

        <h1 style="font-size:clamp(40px,7vw,72px); font-weight:900; letter-spacing:-0.03em; line-height:1.05; margin-bottom:16px;">
            Integra<span style="color:#22d3ee;" class="text-glow">Park</span>
        </h1>

        <p style="font-size:18px; color:#94a3b8; font-weight:400; margin-bottom:8px; line-height:1.6;">
            Sistem Manajemen Parkir Profesional Generasi Berikutnya.
        </p>
        <p style="font-size:14px; color:#475569; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:40px; font-weight:600;">
            Smart Access &bull; Solid Integrity
        </p>

        <!-- Feature Badges -->
        <div style="display:flex; flex-wrap:wrap; gap:10px; justify-content:center; margin-bottom:40px;">
            <span class="feature-badge">
                <span class="feature-dot" style="background:#22d3ee;"></span>
                Real-time Monitoring
            </span>
            <span class="feature-badge">
                <span class="feature-dot" style="background:#10b981;"></span>
                Multi-role Access
            </span>
            <span class="feature-badge">
                <span class="feature-dot" style="background:#f59e0b;"></span>
                VIP / VVIP Priority
            </span>
            <span class="feature-badge">
                <span class="feature-dot" style="background:#6366f1;"></span>
                Auto Tarif Calculation
            </span>
        </div>

        <!-- CTA Button -->
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-enter">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Buka Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-enter">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Masuk ke Sistem
                </a>
            @endauth
        @endif

        <!-- Divider -->
        <div style="width:1px; height:48px; background:linear-gradient(to bottom, transparent, rgba(30,41,59,1), transparent); margin:40px auto;"></div>

        <!-- Stats -->
        <div class="stat-strip">
            <div class="stat-item">
                <div class="stat-num">99.9<span style="color:#22d3ee;">%</span></div>
                <div class="stat-label">Uptime</div>
            </div>
            <div style="width:1px; background:rgba(30,41,59,1);"></div>
            <div class="stat-item">
                <div class="stat-num">&lt;1<span style="color:#22d3ee;">s</span></div>
                <div class="stat-label">Response Time</div>
            </div>
            <div style="width:1px; background:rgba(30,41,59,1);"></div>
            <div class="stat-item">
                <div class="stat-num">3<span style="color:#22d3ee;">+</span></div>
                <div class="stat-label">Role Types</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div style="position:fixed; bottom:20px; left:50%; transform:translateX(-50%); font-size:12px; color:#334155; letter-spacing:0.05em;">
        &copy; {{ date('Y') }} IntegraPark. All rights reserved.
    </div>

</body>
</html>

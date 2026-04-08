<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aplikasi Parkir - Sistem Manajemen Parkir">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="font-inter antialiased bg-slate-50 dark:bg-slate-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white shadow-2xl transform transition-transform duration-300 ease-in-out"
               id="sidebar"
               x-data="{ open: true }">
            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700/50">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight">Parkir</h1>
                    <p class="text-xs text-slate-400">Management System</p>
                </div>
            </div>

            <!-- User Info -->
            <div class="px-6 py-4 border-b border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-sm font-bold shadow-lg">
                        {{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold">{{ auth()->user()->nama_lengkap }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            @if(auth()->user()->role === 'admin') bg-blue-500/20 text-blue-300
                            @elseif(auth()->user()->role === 'petugas') bg-emerald-500/20 text-emerald-300
                            @else bg-amber-500/20 text-amber-300 @endif">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="px-4 py-4 space-y-1 flex-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                          {{ request()->routeIs('dashboard') ? 'bg-blue-600/20 text-blue-300 shadow-lg shadow-blue-500/10' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                @can('admin')
                <div class="pt-3">
                    <p class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Admin Menu</p>
                    <a href="{{ route('admin.users') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('admin.users') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        Kelola User
                    </a>
                    <a href="{{ route('admin.tarif') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('admin.tarif') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Tarif Parkir
                    </a>
                    <a href="{{ route('admin.area') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('admin.area') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Area Parkir
                    </a>
                    <a href="{{ route('admin.kendaraan') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('admin.kendaraan') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 5h8M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                        </svg>
                        Kendaraan
                    </a>
                    <a href="{{ route('admin.log') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('admin.log') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Log Aktivitas
                    </a>
                </div>
                @endcan

                @can('petugas')
                <div class="pt-3">
                    <p class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Petugas Menu</p>
                    <a href="{{ route('petugas.transaksi-masuk') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('petugas.transaksi-masuk') ? 'bg-emerald-600/20 text-emerald-300' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Kendaraan Masuk
                    </a>
                    <a href="{{ route('petugas.transaksi-keluar') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('petugas.transaksi-keluar') ? 'bg-emerald-600/20 text-emerald-300' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Kendaraan Keluar
                    </a>
                </div>
                @endcan

                @can('owner')
                <div class="pt-3">
                    <p class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Owner Menu</p>
                    <a href="{{ route('owner.laporan') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('owner.laporan') ? 'bg-amber-600/20 text-amber-300' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Laporan Transaksi
                    </a>
                </div>
                @endcan
            </nav>

            <!-- Logout -->
            <div class="px-4 py-4 border-t border-slate-700/50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64">
            <!-- Top Bar -->
            <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border-b border-slate-200 dark:border-slate-700/50 px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-white">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">@yield('page-subtitle', 'Selamat datang di Aplikasi Parkir')</p>
                    </div>
                    <div class="flex flex-col items-end leading-tight">
                        <span id="live-clock" class="text-lg font-bold text-slate-500 dark:text-slate-400 tabular-nums"></span>
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-8">
                <!-- Flash Messages -->
                @if (session()->has('success'))
                <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
                @endif

                @if (session()->has('error'))
                <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>
    @livewireScripts

    <script>
    function updateClock() {
        const now = new Date();
        
        // Ambil jam dan menit, pastikan selalu 2 digit dengan padStart
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        // Gabungkan
        const timeString = `${hours}:${minutes}`;
        
        // Masukkan ke elemen dengan ID 'live-clock'
        document.getElementById('live-clock').textContent = timeString;
    }

    // Jalankan fungsi setiap 1 detik agar waktu selalu akurat
    setInterval(updateClock, 1000);

    // Panggil langsung agar saat page load tidak kosong
    updateClock();

    // Global Enter key handler untuk memindahkan fokus ke input selanjutnya
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'BUTTON' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            const form = e.target.form;
            if(form) {
                const focusable = Array.from(form.elements).filter(el => 
                    !el.disabled && !el.readOnly && el.type !== 'hidden' &&
                    (el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'BUTTON')
                );
                const index = focusable.indexOf(e.target);
                if (index > -1 && index < focusable.length - 1) {
                    focusable[index + 1].focus();
                }
            }
        }
    });
</script>
</body>
</html>

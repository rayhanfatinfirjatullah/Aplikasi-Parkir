<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="IntegraPark — Sistem Manajemen Parkir Profesional">
    <title>{{ config('app.name', 'IntegraPark') }} — @yield('page-title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('img/5.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/5.png') }}">
</head>
{{-- bg-brand-bg maps to #020617 defined in @theme --}}
<body class="font-inter antialiased bg-[#020617] text-[#e2e8f0] min-h-screen">

{{-- ============================================================
     APP SHELL — Sidebar + Main
     ============================================================ --}}
<div class="flex min-h-screen"
     x-data="{ sidebarOpen: window.innerWidth >= 768 }"
     @resize.window="sidebarOpen = window.innerWidth >= 768">

    {{-- ===== SIDEBAR ===== --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-[#0F172A] border-r border-[#1e293b] shadow-2xl
                  transform transition-transform duration-300 ease-in-out"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        {{-- ── Logo ── --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-[#1e293b] shrink-0">
            <div class="relative w-10 h-10 shrink-0">
                {{-- Glow ring behind logo --}}
                <div class="absolute inset-0 rounded-xl bg-cyan-400/10 ring-1 ring-cyan-500/40 blur-[2px]"></div>
                <img src="{{ asset('img/5.png') }}" alt="IntegraPark Logo"
                     class="relative w-10 h-10 object-contain rounded-xl">
            </div>
            <div>
                <h1 class="text-base font-extrabold tracking-tight text-[#e2e8f0] leading-none">
                    Integra<span class="text-cyan-400">Park</span>
                </h1>
                <p class="text-[10px] text-[#94a3b8] mt-0.5 tracking-wide">Smart Access. Solid Integrity.</p>
            </div>
        </div>

        {{-- ── User Info ── --}}
        <div class="px-5 py-4 border-b border-[#1e293b] shrink-0">
            <div class="flex items-center gap-3">
                {{-- Avatar with role-colored ring --}}
                <div class="relative shrink-0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-[#020617]
                                @if(auth()->user()->role === 'admin') bg-cyan-400
                                @elseif(auth()->user()->role === 'petugas') bg-emerald-400
                                @else bg-amber-400 @endif">
                        {{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 1)) }}
                    </div>
                    {{-- Online dot --}}
                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 border-2 border-[#0F172A] rounded-full"></span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-[#e2e8f0] truncate">{{ auth()->user()->nama_lengkap }}</p>
                    <span class="badge mt-0.5
                        @if(auth()->user()->role === 'admin') badge-cyan
                        @elseif(auth()->user()->role === 'petugas') badge-emerald
                        @else badge-amber @endif">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- ── Navigation ── --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            {{-- Dashboard (semua role) --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('dashboard')
                            ? 'bg-[#1e293b] text-cyan-400 border-l-4 border-cyan-400 pl-[0.625rem]'
                            : 'text-[#94a3b8] hover:bg-[#1e293b]/60 hover:text-[#e2e8f0] border-l-4 border-transparent' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            {{-- ── Admin Menu ── --}}
            @can('admin')
            <div class="pt-4">
                <p class="nav-section-label">Admin</p>

                <a href="{{ route('admin.users') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                          {{ request()->routeIs('admin.users')
                                ? 'bg-[#1e293b] text-cyan-400 border-l-4 border-cyan-400 pl-[0.625rem]'
                                : 'text-[#94a3b8] hover:bg-[#1e293b]/60 hover:text-[#e2e8f0] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                    Kelola User
                </a>

                <a href="{{ route('admin.tarif') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                          {{ request()->routeIs('admin.tarif')
                                ? 'bg-[#1e293b] text-cyan-400 border-l-4 border-cyan-400 pl-[0.625rem]'
                                : 'text-[#94a3b8] hover:bg-[#1e293b]/60 hover:text-[#e2e8f0] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tarif Parkir
                </a>

                <a href="{{ route('admin.area') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                          {{ request()->routeIs('admin.area')
                                ? 'bg-[#1e293b] text-cyan-400 border-l-4 border-cyan-400 pl-[0.625rem]'
                                : 'text-[#94a3b8] hover:bg-[#1e293b]/60 hover:text-[#e2e8f0] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Area Parkir
                </a>

                <a href="{{ route('admin.kendaraan') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                          {{ request()->routeIs('admin.kendaraan')
                                ? 'bg-[#1e293b] text-cyan-400 border-l-4 border-cyan-400 pl-[0.625rem]'
                                : 'text-[#94a3b8] hover:bg-[#1e293b]/60 hover:text-[#e2e8f0] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l1.293-1.293A1 1 0 015 14.414V13H4v2H2v2h2l1-1h8l1 1h2v-2h-1M13 16V6l5 3v7h-5z"/>
                    </svg>
                    Kendaraan
                </a>

                <a href="{{ route('admin.log') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                          {{ request()->routeIs('admin.log')
                                ? 'bg-[#1e293b] text-cyan-400 border-l-4 border-cyan-400 pl-[0.625rem]'
                                : 'text-[#94a3b8] hover:bg-[#1e293b]/60 hover:text-[#e2e8f0] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Log Aktivitas
                </a>
            </div>
            @endcan

            {{-- ── Petugas Menu ── --}}
            @can('petugas')
            <div class="pt-4">
                <p class="nav-section-label">Operasional</p>

                <a href="{{ route('petugas.transaksi-masuk') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                          {{ request()->routeIs('petugas.transaksi-masuk')
                                ? 'bg-[#1e293b] text-cyan-400 border-l-4 border-cyan-400 pl-[0.625rem]'
                                : 'text-[#94a3b8] hover:bg-[#1e293b]/60 hover:text-[#e2e8f0] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Kendaraan Masuk
                </a>

                <a href="{{ route('petugas.transaksi-keluar') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                          {{ request()->routeIs('petugas.transaksi-keluar')
                                ? 'bg-[#1e293b] text-cyan-400 border-l-4 border-cyan-400 pl-[0.625rem]'
                                : 'text-[#94a3b8] hover:bg-[#1e293b]/60 hover:text-[#e2e8f0] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Kendaraan Keluar
                </a>
            </div>
            @endcan

            {{-- ── Owner Menu ── --}}
            @can('owner')
            <div class="pt-4">
                <p class="nav-section-label">Manajemen</p>

                <a href="{{ route('owner.laporan') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                          {{ request()->routeIs('owner.laporan')
                                ? 'bg-[#1e293b] text-cyan-400 border-l-4 border-cyan-400 pl-[0.625rem]'
                                : 'text-[#94a3b8] hover:bg-[#1e293b]/60 hover:text-[#e2e8f0] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Laporan Transaksi
                </a>
            </div>
            @endcan

        </nav>

        {{-- ── Logout ── --}}
        <div class="px-3 py-4 border-t border-[#1e293b] shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-sm font-medium
                               text-rose-400 border-l-4 border-transparent
                               hover:bg-rose-500/10 hover:text-rose-300 hover:border-rose-500
                               transition-all duration-200 cursor-pointer">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>

    </aside>
    {{-- END SIDEBAR --}}

    {{-- ===== MAIN CONTENT AREA ===== --}}
    <div class="flex flex-col flex-1 min-w-0 transition-all duration-300"
         :class="sidebarOpen ? 'ml-64' : 'ml-0'">

        {{-- ── Top Header Bar ── --}}
        <header class="sticky top-0 z-40 bg-[#0F172A]/80 backdrop-blur-xl border-b border-[#1e293b] px-6 md:px-8 py-4 shrink-0">
            <div class="flex items-center justify-between gap-4">

                {{-- Left: Hamburger + Page Title --}}
                <div class="flex items-center gap-4 min-w-0">
                    {{-- Mobile sidebar toggle --}}
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="p-2 rounded-lg text-[#94a3b8] hover:bg-[#1e293b] hover:text-[#e2e8f0] transition-colors duration-200 shrink-0"
                            aria-label="Toggle Sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-[#e2e8f0] leading-tight truncate">
                            @yield('page-title', 'Dashboard')
                        </h2>
                        <p class="text-xs text-[#94a3b8] mt-0.5 truncate">
                            @yield('page-subtitle', 'Selamat datang di IntegraPark')
                        </p>
                    </div>
                </div>

                {{-- Right: Live Clock --}}
                <div class="flex flex-col items-end shrink-0">
                    <span id="live-clock"
                          class="text-xl font-bold tabular-nums text-cyan-400 tracking-wider leading-tight text-glow-cyan">
                    </span>
                    <span class="text-xs text-[#94a3b8] mt-0.5">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>

            </div>
        </header>

        {{-- ── Page Content ── --}}
        <main class="flex-1 p-6 md:p-8">

            {{-- Global Toasts will appear at top right, handled by Alpine at bottom of layout --}}

            {{-- Livewire / Blade slot content --}}
            {{ $slot }}

        </main>

    </div>
    {{-- END MAIN --}}

    {{-- ===== MOBILE SIDEBAR OVERLAY ===== --}}
    <div class="fixed inset-0 z-40 bg-[#020617]/60 backdrop-blur-sm md:hidden"
         x-show="sidebarOpen && window.innerWidth < 768"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         style="display: none;">
    </div>

</div>
{{-- END APP SHELL --}}

@livewireScripts

<script>
/* ── Live Clock (HH:MM:SS) ── */
function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const el = document.getElementById('live-clock');
    if (el) el.textContent = `${h}:${m}:${s}`;
}
setInterval(updateClock, 1000);
updateClock();

/* ── Global Enter-key focus handler ── */
document.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && e.target.tagName !== 'BUTTON' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
        const form = e.target.form;
        if (form) {
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
    {{-- ===== GLOBAL TOAST NOTIFICATION ===== --}}
    <div x-data="{ 
            toasts: [],
            add(toast) {
                toast.id = Date.now();
                this.toasts.push(toast);
                setTimeout(() => { this.remove(toast.id) }, 4000);
            },
            remove(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }
         }"
         @toast.window="add({ type: $event.detail.type || ($event.detail[0] && $event.detail[0].type), message: $event.detail.message || ($event.detail[0] && $event.detail[0].message) })"
         x-init="
            @if(session()->has('success')) add({ type: 'success', message: '{{ session('success') }}' }); @endif
            @if(session()->has('error')) add({ type: 'error', message: '{{ session('error') }}' }); @endif
            @if(session()->has('warning')) add({ type: 'warning', message: '{{ session('warning') }}' }); @endif
         "
         class="fixed top-6 right-6 z-[100] flex flex-col gap-3 max-w-sm w-full pointer-events-none">
         
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-x-12"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-12"
                 :class="{
                     'bg-[#0F172A]/90 border-emerald-500/30 shadow-[0_0_20px_rgba(52,211,153,0.1)]': toast.type === 'success',
                     'bg-[#0F172A]/90 border-rose-500/30 shadow-[0_0_20px_rgba(244,63,94,0.15)]': toast.type === 'error',
                     'bg-[#0F172A]/90 border-amber-500/30 shadow-[0_0_20px_rgba(251,191,36,0.1)]': toast.type === 'warning'
                 }"
                 class="relative overflow-hidden pointer-events-auto backdrop-blur-xl border flex items-start gap-3 p-4 rounded-xl"
                 role="alert">

                {{-- Left color accent border --}}
                <div class="absolute left-0 top-0 bottom-0 w-1"
                     :class="{
                         'bg-emerald-500': toast.type === 'success',
                         'bg-rose-500': toast.type === 'error',
                         'bg-amber-500': toast.type === 'warning'
                     }"></div>

                {{-- Icon --}}
                <div class="mt-0.5 shrink-0 pl-1">
                    <template x-if="toast.type === 'success'">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <div class="w-8 h-8 rounded-full bg-rose-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <div class="w-8 h-8 rounded-full bg-amber-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </template>
                </div>

                {{-- Message --}}
                <div class="flex-1 min-w-0 pr-2">
                    <template x-if="toast.type === 'success'">
                        <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-0.5">Success</h4>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <h4 class="text-xs font-bold text-rose-400 uppercase tracking-wider mb-0.5">Error</h4>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <h4 class="text-xs font-bold text-amber-400 uppercase tracking-wider mb-0.5">Warning</h4>
                    </template>
                    <p class="text-sm text-[#e2e8f0] font-medium leading-snug" x-text="toast.message"></p>
                </div>

                {{-- Close Button --}}
                <button @click="remove(toast.id)" class="shrink-0 p-1 rounded-md text-[#94a3b8] hover:bg-[#1e293b] hover:text-[#e2e8f0] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>
        </template>
    </div>
</body>
</html>

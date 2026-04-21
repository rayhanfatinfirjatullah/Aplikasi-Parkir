<div class="relative min-h-screen flex items-center justify-center bg-[#020617] px-4 overflow-hidden">

    {{-- Ambient background orbs --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-60 -right-60 w-[500px] h-[500px] bg-cyan-500/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-60 -left-60 w-[500px] h-[500px] bg-blue-600/5 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] bg-cyan-400/3 rounded-full blur-3xl"></div>
        {{-- Grid pattern overlay --}}
        <div class="absolute inset-0 opacity-[0.015]"
             style="background-image: linear-gradient(rgba(34,211,238,1) 1px, transparent 1px), linear-gradient(90deg, rgba(34,211,238,1) 1px, transparent 1px); background-size: 40px 40px;">
        </div>
    </div>

    <div class="relative w-full max-w-md z-10">

        {{-- Logo Section --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-5">
                <div class="relative">
                    <div class="absolute inset-0 rounded-2xl bg-cyan-400/20 blur-xl scale-110"></div>
                    <div class="relative w-16 h-16 bg-[#0F172A] border border-cyan-500/30 rounded-2xl flex items-center justify-center shadow-2xl">
                        <img src="{{ asset('img/5.png') }}" alt="IntegraPark Logo" class="w-14 h-14 object-contain">
                    </div>
                </div>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-[#e2e8f0]">
                Integra<span class="text-cyan-400 text-glow-cyan">Park</span>
            </h1>
            <p class="text-[#94a3b8] mt-2 text-sm">Smart Access. Solid Integrity.</p>
        </div>

        {{-- Login Card --}}
        <div class="bg-[#0F172A] border border-[#1e293b] rounded-2xl shadow-2xl p-8">
            <p class="text-xs font-semibold text-[#94a3b8] uppercase tracking-widest mb-6 text-center">Masuk ke Sistem</p>

            <form wire:submit="login">
                {{-- Error Banner --}}
                @if ($errors->any())
                <div class="alert-error mb-6">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
                @endif

                <div class="space-y-5">
                    {{-- Username --}}
                    <div>
                        <label for="username" class="input-label">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input wire:model="username" type="text" id="username" placeholder="Masukkan username" autocomplete="username"
                                   class="input-base pl-11">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div x-data="{
                            show: false,
                            timer: null,
                            toggle() {
                                if (this.show) {
                                    // Sudah visible, klik lagi = sembunyikan
                                    this.hide();
                                } else {
                                    // Tampilkan dan mulai timer 5 detik
                                    this.show = true;
                                    this.startTimer();
                                }
                            },
                            startTimer() {
                                clearTimeout(this.timer);
                                this.timer = setTimeout(() => {
                                    this.hide();
                                }, 5000);
                            },
                            hide() {
                                this.show = false;
                                clearTimeout(this.timer);
                            }
                        }">
                        <label for="password" class="input-label">Password</label>
                        <div class="relative">
                            {{-- Ikon gembok kiri --}}
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>

                            {{-- Input password --}}
                            <input wire:model="password"
                                   :type="show ? 'text' : 'password'"
                                   id="password"
                                   placeholder="Masukkan password"
                                   autocomplete="current-password"
                                   class="input-base pl-11 pr-11"
                                   @keydown.enter.prevent="document.getElementById('btn-submit').click()">

                            {{-- Tombol toggle mata kanan (tabindex=-1 agar dilewati Tab/Enter) --}}
                            <button type="button"
                                    @click="toggle()"
                                    tabindex="-1"
                                    aria-hidden="true"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-[#94a3b8] hover:text-cyan-400 transition-colors duration-200 focus:outline-none"
                                    :title="show ? 'Sembunyikan password' : 'Tampilkan password'">

                                {{-- Icon Mata Terbuka (password visible) --}}
                                <svg x-show="show"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-90"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-90"
                                     class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>

                                {{-- Icon Mata Tertutup (password hidden) --}}
                                <svg x-show="!show"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-90"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-90"
                                     class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            id="btn-submit"
                            class="btn-primary w-full py-3 text-base"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75 cursor-not-allowed">
                        <svg wire:loading class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span wire:loading.remove>Masuk</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <p class="text-center text-[#334155] text-xs mt-6">
            &copy; {{ date('Y') }} IntegraPark. All rights reserved.
        </p>
    </div>
</div>

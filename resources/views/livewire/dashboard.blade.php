@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan data parkir hari ini')

<div>
    {{-- ── Stat Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        {{-- Total Kendaraan --}}
        <div class="card p-6 hover:border-cyan-500/30 hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-[#94a3b8] uppercase tracking-wider">Total Kendaraan</p>
                    <p class="stat-number mt-1">{{ number_format($totalKendaraan) }}</p>
                </div>
                <div class="w-12 h-12 bg-cyan-500/10 border border-cyan-500/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l1.293-1.293A1 1 0 015 14.414V13H4v2H2v2h2l1-1h8l1 1h2v-2h-1M13 16V6l5 3v7h-5z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 h-px bg-gradient-to-r from-cyan-500/30 to-transparent"></div>
        </div>

        {{-- Transaksi Hari Ini --}}
        <div class="card p-6 hover:border-emerald-500/30 hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-[#94a3b8] uppercase tracking-wider">Transaksi Hari Ini</p>
                    <p class="stat-number mt-1">{{ number_format($transaksiHariIni) }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 h-px bg-gradient-to-r from-emerald-500/30 to-transparent"></div>
        </div>

        {{-- Sedang Terparkir --}}
        <div class="card p-6 hover:border-amber-500/30 hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-[#94a3b8] uppercase tracking-wider">Sedang Terparkir</p>
                    <p class="stat-number mt-1">{{ number_format($kendaraanTerparkir) }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-500/10 border border-amber-500/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 h-px bg-gradient-to-r from-amber-500/30 to-transparent"></div>
        </div>

        {{-- Pendapatan Hari Ini --}}
        <div class="card p-6 hover:border-blue-500/30 hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-[#94a3b8] uppercase tracking-wider">Pendapatan Hari Ini</p>
                    <p class="text-2xl font-extrabold text-[#e2e8f0] mt-1 tracking-tight truncate">
                        Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-500/10 border border-blue-500/20 rounded-xl flex items-center justify-center shrink-0 ml-2">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 h-px bg-gradient-to-r from-blue-500/30 to-transparent"></div>
        </div>
    </div>

    {{-- ── Bottom Grid ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Area Parkir Status --}}
        <div class="card p-6">
            <h3 class="text-base font-bold text-[#e2e8f0] mb-5 flex items-center gap-2">
                <span class="w-1.5 h-5 bg-cyan-400 rounded-full"></span>
                Status Area Parkir
            </h3>
            <div class="space-y-5">
                @foreach($areaParkir as $area)
                @php $pct = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas) * 100 : 0; @endphp
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-[#e2e8f0]">{{ $area->nama_area }}</span>
                        <span class="text-xs font-mono tabular-nums
                            {{ $pct > 80 ? 'text-rose-400' : ($pct > 50 ? 'text-amber-400' : 'text-cyan-400') }}">
                            {{ $area->terisi }}<span class="text-[#94a3b8]">/{{ $area->kapasitas }}</span>
                        </span>
                    </div>
                    <div class="progress-track">
                        <div class="{{ $pct > 80 ? 'progress-fill-full' : ($pct > 50 ? 'progress-fill-warning' : 'progress-fill-ok') }}"
                             style="width: {{ $pct }}%"></div>
                    </div>
                    <div class="flex justify-between mt-1.5 text-[10px] text-[#94a3b8]">
                        <span>{{ $pct > 80 ? 'Hampir Penuh' : ($pct > 50 ? 'Setengah Terisi' : 'Tersedia') }}</span>
                        <span>{{ number_format(100 - $pct, 0) }}% kosong</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Transaksi Terbaru --}}
        <div class="lg:col-span-2 card overflow-hidden">
            <div class="px-6 pt-6 pb-4 border-b border-[#1e293b] flex items-center gap-2">
                <span class="w-1.5 h-5 bg-cyan-400 rounded-full"></span>
                <h3 class="text-base font-bold text-[#e2e8f0]">Transaksi Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Plat Nomor</th>
                            <th>Jenis</th>
                            <th>Area</th>
                            <th>Waktu Masuk</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiTerbaru as $t)
                        <tr>
                            <td class="font-bold tracking-wider text-[#e2e8f0]">{{ $t->plat_nomor }}</td>
                            <td>{{ ucfirst($t->tarif->jenis_kendaraan) }}</td>
                            <td>{{ $t->areaParkir->nama_area }}</td>
                            <td class="font-mono tabular-nums text-xs">{{ $t->waktu_masuk->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge {{ $t->status === 'masuk' ? 'badge-emerald' : 'badge-blue' }}">
                                    {{ ucfirst($t->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-[#94a3b8]">
                                Belum ada transaksi hari ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

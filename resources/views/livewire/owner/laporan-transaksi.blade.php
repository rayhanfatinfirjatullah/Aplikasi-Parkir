@section('page-title', 'Laporan Transaksi')
@section('page-subtitle', 'Lihat dan filter laporan transaksi parkir')

<div>
    {{-- ── Filter Panel ── --}}
    <div class="card p-6 mb-6">
        <h3 class="text-sm font-bold text-[#94a3b8] uppercase tracking-widest mb-5 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filter Laporan
        </h3>
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="input-label">Tanggal Mulai</label>
                <input wire:model.live="tanggal_mulai" type="date" class="input-base" max="{{ $tanggal_selesai }}">
            </div>
            <div>
                <label class="input-label">Tanggal Selesai</label>
                <input wire:model.live="tanggal_selesai" type="date" class="input-base" min="{{ $tanggal_mulai }}">
            </div>
            <div>
                <label class="input-label">Cari Plat Nomor</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari..."
                           class="input-base pl-10 w-44">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="filter" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filter
                </button>
                <button wire:click="resetFilter" class="btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </button>
            </div>
            <div class="ml-auto">
                <a href="{{ route('owner.laporan.cetak', ['tanggal_mulai' => $tanggal_mulai, 'tanggal_selesai' => $tanggal_selesai, 'search' => $search]) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white
                          bg-emerald-600 hover:bg-emerald-500 shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40
                          active:scale-[0.97] transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak Laporan
                </a>
            </div>
        </div>
    </div>

    {{-- ── Summary Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
        <div class="card p-6 hover:border-cyan-500/30 hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-[#94a3b8] uppercase tracking-wider">Total Transaksi</p>
                    <p class="stat-number mt-1">{{ number_format($totalTransaksi) }}</p>
                </div>
                <div class="w-12 h-12 bg-cyan-500/10 border border-cyan-500/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 h-px bg-gradient-to-r from-cyan-500/30 to-transparent"></div>
        </div>

        <div class="card p-6 hover:border-emerald-500/30 hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-[#94a3b8] uppercase tracking-wider">Total Pendapatan</p>
                    <p class="text-2xl font-extrabold text-emerald-400 mt-1 truncate">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center justify-center shrink-0 ml-3">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 h-px bg-gradient-to-r from-emerald-500/30 to-transparent"></div>
        </div>
    </div>

    {{-- ── Transactions Table ── --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Plat Nomor</th>
                        <th>Jenis</th>
                        <th>Area</th>
                        <th>Waktu Masuk</th>
                        <th>Waktu Keluar</th>
                        <th>Durasi</th>
                        <th>Biaya</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $i => $t)
                    <tr>
                        <td class="text-[#94a3b8] w-10">{{ $transaksis->firstItem() + $i }}</td>
                        <td class="font-bold tracking-wider text-[#e2e8f0]">{{ $t->plat_nomor }}</td>
                        <td class="text-[#94a3b8]">{{ ucfirst($t->tarif->jenis_kendaraan) }}</td>
                        <td class="text-[#94a3b8]">{{ $t->areaParkir->nama_area }}</td>
                        <td class="font-mono tabular-nums text-xs text-[#94a3b8]">{{ $t->waktu_masuk->format('d/m/Y H:i') }}</td>
                        <td class="font-mono tabular-nums text-xs text-[#94a3b8]">
                            {{ $t->waktu_keluar ? $t->waktu_keluar->format('d/m/Y H:i') : '—' }}
                        </td>
                        <td>
                            @if($t->durasi_jam)
                            <span class="badge badge-amber">{{ $t->durasi_jam }} jam</span>
                            @else
                            <span class="text-[#94a3b8]">—</span>
                            @endif
                        </td>
                        <td class="font-semibold text-[#e2e8f0] tabular-nums">
                            {{ $t->biaya_total ? 'Rp ' . number_format($t->biaya_total, 0, ',', '.') : '—' }}
                        </td>
                        <td>
                            <span class="badge {{ $t->status === 'masuk' ? 'badge-emerald' : 'badge-blue' }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-16 text-center text-[#94a3b8]">
                            <svg class="w-10 h-10 mx-auto mb-3 text-[#334155]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Tidak ada data transaksi pada periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[#1e293b]">{{ $transaksis->links() }}</div>
    </div>
</div>

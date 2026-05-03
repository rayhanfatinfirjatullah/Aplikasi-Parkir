{{-- ============================================================
     Cetak Karcis Masuk — IntegraPark
     Note: halaman ini berdiri sendiri (bukan pakai layout app).
     Diakses langsung setelah check-in berhasil.
     Print area: #karcis (80mm thermal receipt width)
     ============================================================ --}}
<div class="min-h-screen bg-[#020617] flex items-center justify-center p-4">

    {{-- ── Screen wrapper ── --}}
    <div class="w-full max-w-sm">

        {{-- ── KARCIS (area yg dicetak) ── --}}
        <div id="karcis"
             class="bg-white rounded-2xl overflow-hidden shadow-2xl shadow-black/40 border border-[#1e293b] print:shadow-none print:border-0 print:rounded-none">

            {{-- Header stripe --}}
            <div class="bg-gradient-to-r from-cyan-600 to-blue-700 px-6 pt-6 pb-5 text-center print:bg-slate-800">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-white/15 rounded-xl mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-extrabold text-white tracking-widest">KARCIS MASUK</h1>
                <p class="text-cyan-200 text-xs mt-1 font-medium tracking-wider">IntegraPark</p>

                {{-- VIP / VVIP badge --}}
                @if(($transaksi->status_spesial ?? 'reguler') === 'vvip')
                <span class="mt-3 inline-block px-3 py-0.5 bg-amber-400 text-amber-900 font-bold text-[10px] uppercase tracking-widest rounded-full">
                    ✦ Member VVIP
                </span>
                @elseif(($transaksi->status_spesial ?? 'reguler') === 'vip')
                <span class="mt-3 inline-block px-3 py-0.5 bg-white/20 text-white font-bold text-[10px] uppercase tracking-widest rounded-full border border-white/30">
                    ★ Member VIP
                </span>
                @endif
            </div>

            <div class="bg-white px-6 py-5 space-y-4">

                {{-- No. Karcis --}}
                <div class="text-center py-3 bg-slate-50 rounded-xl border border-slate-200">
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold mb-1">No. Karcis</p>
                    <p class="text-2xl font-black text-slate-900 tracking-[0.2em] font-mono">
                        #{{ str_pad($transaksi->id_parkir, 6, '0', STR_PAD_LEFT) }}
                    </p>
                </div>

                {{-- Divider --}}
                <div class="border-t-2 border-dashed border-slate-200"></div>

                {{-- Kendaraan --}}
                <div class="space-y-2">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Data Kendaraan</p>
                    @foreach([
                        ['Plat Nomor', $transaksi->plat_nomor, 'font-black tracking-widest text-slate-900'],
                        ['Jenis Kendaraan', ucfirst($transaksi->tarif->jenis_kendaraan), ''],
                        ['Warna', $transaksi->warna, ''],
                        ['Pemilik', $transaksi->pemilik, ''],
                    ] as [$label, $value, $extra])
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">{{ $label }}</span>
                        <span class="font-semibold text-slate-800 text-right {{ $extra ?? '' }}">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="border-t-2 border-dashed border-slate-200"></div>

                {{-- Parkir --}}
                <div class="space-y-2">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Info Parkir</p>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Area Parkir</span>
                        <span class="font-semibold text-slate-800">{{ $transaksi->areaParkir->nama_area }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Waktu Masuk</span>
                        <span class="font-semibold text-slate-800 font-mono">{{ $transaksi->waktu_masuk->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Tarif / Jam</span>
                        @if(in_array($transaksi->status_spesial ?? 'reguler', ['vip', 'vvip']))
                        <span class="font-bold text-emerald-600">GRATIS (Rp 0)</span>
                        @else
                        <span class="font-semibold text-slate-800">Rp {{ number_format($transaksi->tarif->tarif_jam_pertama, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Denda Karcis Hilang</span>
                        @if(($transaksi->status_spesial ?? 'reguler') === 'vvip')
                        <span class="font-bold text-emerald-600">Rp 0 (Bebas)</span>
                        @elseif(($transaksi->status_spesial ?? 'reguler') === 'vip')
                        <span class="font-semibold text-amber-600">Normal</span>
                        @else
                        <span class="font-semibold text-slate-800">Normal</span>
                        @endif
                    </div>
                </div>

                <div class="border-t-2 border-dashed border-slate-200"></div>

                {{-- Footer --}}
                <div class="text-center text-xs text-slate-400 space-y-0.5">
                    <p class="font-semibold text-slate-500">⚠ Simpan karcis ini untuk proses keluar!</p>
                    <p>Petugas: {{ $transaksi->user->nama_lengkap }}</p>
                    <p class="font-mono">{{ $transaksi->waktu_masuk->format('d/m/Y H:i:s') }}</p>
                </div>

            </div>
        </div>

        {{-- ── Action Buttons (hidden on print) ── --}}
        <div class="mt-5 flex gap-3 print:hidden">
            <button onclick="window.print()" class="btn-primary flex-1 justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Karcis
            </button>
            <a href="{{ route('petugas.transaksi-masuk') }}" class="btn-secondary flex-1 justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Transaksi Baru
            </a>
        </div>
    </div>

    <style>
        @page {
            size: auto;
            margin: 0mm;
        }
        @media print {
            body { background: white !important; }
            body * { visibility: hidden; }
            #karcis, #karcis * { visibility: visible; }
            #karcis {
                position: absolute; left: 0; top: 0;
                width: 80mm;
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</div>

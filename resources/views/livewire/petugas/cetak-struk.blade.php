{{-- ============================================================
     Cetak Struk Pembayaran — IntegraPark
     Note: halaman ini berdiri sendiri (bukan pakai layout app).
     Print area: #struk (80mm thermal receipt width)
     ============================================================ --}}
<div class="min-h-screen bg-[#020617] flex items-center justify-center p-4">

    <div class="w-full max-w-sm">

        {{-- ── STRUK (area yg dicetak) ── --}}
        <div id="struk"
             class="bg-white rounded-2xl overflow-hidden shadow-2xl shadow-black/40 border border-[#1e293b] print:shadow-none print:border-0 print:rounded-none">

            {{-- Header stripe --}}
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-6 pt-6 pb-5 text-center print:bg-slate-800">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-white/15 rounded-xl mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-extrabold text-white tracking-widest">STRUK PEMBAYARAN</h1>
                <p class="text-emerald-200 text-xs mt-1 font-medium tracking-wider">IntegraPark</p>

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

                {{-- No. Transaksi --}}
                <div class="text-center py-3 bg-slate-50 rounded-xl border border-slate-200">
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold mb-1">No. Transaksi</p>
                    <p class="text-2xl font-black text-slate-900 tracking-[0.2em] font-mono">
                        #{{ str_pad($transaksi->id_parkir, 6, '0', STR_PAD_LEFT) }}
                    </p>
                </div>

                <div class="border-t-2 border-dashed border-slate-200"></div>

                {{-- Data Kendaraan --}}
                <div class="space-y-2">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Data Kendaraan</p>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Plat Nomor</span>
                        <span class="font-black tracking-widest text-slate-900">{{ $transaksi->plat_nomor }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Jenis Kendaraan</span>
                        <span class="font-semibold text-slate-800">{{ ucfirst($transaksi->tarif->jenis_kendaraan) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Warna</span>
                        <span class="font-semibold text-slate-800">{{ $transaksi->warna }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Pemilik</span>
                        <span class="font-semibold text-slate-800">{{ $transaksi->pemilik }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Area Parkir</span>
                        <span class="font-semibold text-slate-800">{{ $transaksi->areaParkir->nama_area }}</span>
                    </div>
                </div>

                <div class="border-t-2 border-dashed border-slate-200"></div>

                {{-- Waktu --}}
                <div class="space-y-2">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Waktu Parkir</p>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Waktu Masuk</span>
                        <span class="font-semibold text-slate-800 font-mono">{{ $transaksi->waktu_masuk->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Waktu Keluar</span>
                        <span class="font-semibold text-slate-800 font-mono">
                            {{ $transaksi->waktu_keluar ? $transaksi->waktu_keluar->format('d/m/Y H:i') : '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Durasi</span>
                        <div class="text-right">
                            @php
                                $durasiJam = $transaksi->durasi_jam ?? 0;
                                $durasiMnt = $transaksi->waktu_keluar && $transaksi->waktu_masuk
                                    ? (int) floor($transaksi->waktu_masuk->diffInSeconds($transaksi->waktu_keluar) / 60) % 60
                                    : 0;
                            @endphp
                            <span class="font-bold text-amber-600">
                                {{ $durasiJam > 0 ? $durasiJam . ' Jam ' : '' }}{{ $durasiMnt }} Menit
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-t-2 border-dashed border-slate-200"></div>

                {{-- Biaya --}}
                <div class="space-y-2">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Rincian Biaya</p>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Tarif Jam Pertama</span>
                        <span class="font-semibold text-slate-800">Rp {{ number_format($transaksi->tarif->tarif_jam_pertama, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Tarif Jam Berikutnya</span>
                        <span class="font-semibold text-slate-800">Rp {{ number_format($transaksi->tarif->tarif_jam_berikutnya, 0, ',', '.') }}/jam</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Biaya Parkir</span>
                        @if(in_array($transaksi->status_spesial ?? 'reguler', ['vip', 'vvip']))
                        <span class="font-bold text-emerald-600">GRATIS (Rp 0)</span>
                        @elseif(($transaksi->biaya_parkir ?? 0) == 0 && $transaksi->status === 'keluar')
                        <span class="font-bold text-emerald-600">Rp 0 (Grace Period / Bebas Biaya)</span>
                        @else
                        <span class="font-semibold text-slate-800">Rp {{ number_format($transaksi->biaya_parkir ?? 0, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    @if($transaksi->denda > 0)
                    <div class="flex justify-between text-sm">
                        <span class="font-semibold text-red-500">Denda Karcis Hilang</span>
                        <span class="font-semibold text-red-600">Rp {{ number_format($transaksi->denda, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                {{-- Total --}}
                <div class="flex justify-between items-center py-3 px-4 bg-emerald-50 rounded-xl border-2 border-emerald-200">
                    <span class="text-base font-extrabold text-emerald-800 tracking-wide">TOTAL BAYAR</span>
                    <span class="text-lg font-black text-emerald-700">Rp {{ number_format($transaksi->biaya_total ?? 0, 0, ',', '.') }}</span>
                </div>

                <div class="border-t-2 border-dashed border-slate-200"></div>

                {{-- Footer --}}
                <div class="text-center text-xs text-slate-400 space-y-0.5">
                    <p>Petugas: {{ $transaksi->user->nama_lengkap }}</p>
                    <p class="font-semibold text-slate-500">Terima kasih atas kunjungan Anda!</p>
                    <p class="font-mono">{{ now()->format('d/m/Y H:i:s') }}</p>
                </div>

            </div>
        </div>

        {{-- ── Action Buttons (hidden on print) ── --}}
        <div class="mt-5 flex gap-3 print:hidden">
            <button onclick="window.print()"
                    class="flex-1 inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white
                           bg-gradient-to-r from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/20
                           hover:from-emerald-400 hover:to-teal-500 hover:shadow-emerald-500/40
                           active:scale-[0.97] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Struk
            </button>
            <a href="{{ route('petugas.transaksi-keluar') }}" class="btn-secondary flex-1 justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <style>
        @media print {
            body { background: white !important; }
            body * { visibility: hidden; }
            #struk, #struk * { visibility: visible; }
            #struk {
                position: absolute; left: 0; top: 0;
                width: 80mm;
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</div>

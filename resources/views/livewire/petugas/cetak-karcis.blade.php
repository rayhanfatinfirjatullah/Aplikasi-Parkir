<div class="min-h-screen bg-white flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <!-- Entry Ticket -->
        <div class="border-2 border-dashed border-slate-300 rounded-lg p-6" id="karcis">
            <!-- Header -->
            <div class="text-center border-b border-dashed border-slate-300 pb-4 mb-4">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-slate-800">KARCIS MASUK</h1>
                <p class="text-sm text-slate-500">Aplikasi Parkir</p>
                @if(($transaksi->status_spesial ?? 'reguler') === 'vvip')
                <div class="mt-2 inline-block px-3 py-0.5 bg-amber-100 text-amber-800 border border-amber-200 font-bold text-[10px] uppercase tracking-widest rounded-full">Member VVIP</div>
                @elseif(($transaksi->status_spesial ?? 'reguler') === 'vip')
                <div class="mt-2 inline-block px-3 py-0.5 bg-blue-100 text-blue-800 border border-blue-200 font-bold text-[10px] uppercase tracking-widest rounded-full">Member VIP</div>
                @endif
            </div>

            <!-- Ticket ID -->
            <div class="text-center mb-4 py-3 bg-slate-50 rounded-lg">
                <p class="text-xs text-slate-500 uppercase tracking-wider">No. Karcis</p>
                <p class="text-2xl font-bold text-slate-800 tracking-widest">#{{ str_pad($transaksi->id_parkir, 6, '0', STR_PAD_LEFT) }}</p>
            </div>

            <!-- Vehicle Details -->
            <div class="space-y-2.5 text-sm border-b border-dashed border-slate-300 pb-4 mb-4">
                <div class="flex justify-between">
                    <span class="text-slate-500">Plat Nomor</span>
                    <span class="font-bold text-slate-800 tracking-wider">{{ $transaksi->plat_nomor }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Jenis Kendaraan</span>
                    <span class="text-slate-800">{{ ucfirst($transaksi->tarif->jenis_kendaraan) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Warna</span>
                    <span class="text-slate-800">{{ $transaksi->warna }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Pemilik</span>
                    <span class="text-slate-800">{{ $transaksi->pemilik }}</span>
                </div>
            </div>

            <!-- Parking Details -->
            <div class="space-y-2.5 text-sm border-b border-dashed border-slate-300 pb-4 mb-4">
                <div class="flex justify-between">
                    <span class="text-slate-500">Area Parkir</span>
                    <span class="font-semibold text-slate-800">{{ $transaksi->areaParkir->nama_area }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu Masuk</span>
                    <span class="font-semibold text-slate-800">{{ $transaksi->waktu_masuk->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Tarif / Jam</span>
                    @if(in_array($transaksi->status_spesial ?? 'reguler', ['vip', 'vvip']))
                        <span class="text-emerald-600 font-bold">GRATIS (Rp 0)</span>
                    @else
                        <span class="text-slate-800">Rp {{ number_format($transaksi->tarif->tarif_per_jam, 0, ',', '.') }}</span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Denda</span>
                    @if(($transaksi->status_spesial ?? 'reguler') === 'vvip')
                        <span class="text-emerald-600 font-bold">Rp 0 (Bebas Denda)</span>
                    @elseif(($transaksi->status_spesial ?? 'reguler') === 'vip')
                        <span class="text-amber-600 font-semibold">Normal</span>
                    @else
                        <span class="text-slate-800">Normal</span>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-xs text-slate-400 space-y-1">
                <p class="font-medium text-slate-500">Simpan karcis ini untuk proses keluar!</p>
                <p>Petugas: {{ $transaksi->user->nama_lengkap }}</p>
                <p>{{ $transaksi->waktu_masuk->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>

        <!-- Action Buttons (hidden from print) -->
        <div class="mt-6 flex gap-3 print:hidden">
            <button onclick="window.print()"
                    class="flex-1 py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Karcis
            </button>
            <a href="{{ route('petugas.transaksi-masuk') }}"
               class="flex-1 py-3 px-4 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Transaksi Baru
            </a>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            #karcis, #karcis * { visibility: visible; }
            #karcis { position: absolute; left: 0; top: 0; width: 80mm; }
        }
    </style>
</div>

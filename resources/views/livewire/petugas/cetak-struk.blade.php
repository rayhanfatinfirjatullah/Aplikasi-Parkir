<div class="min-h-screen bg-white flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <!-- Payment Receipt -->
        <div class="border-2 border-dashed border-slate-300 rounded-lg p-6" id="struk">
            <!-- Header -->
            <div class="text-center border-b border-dashed border-slate-300 pb-4 mb-4">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-emerald-100 rounded-full mb-2">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-slate-800">STRUK PEMBAYARAN</h1>
                <p class="text-sm text-slate-500">Aplikasi Parkir</p>
            </div>

            <!-- Transaction ID -->
            <div class="text-center mb-4 py-3 bg-slate-50 rounded-lg">
                <p class="text-xs text-slate-500 uppercase tracking-wider">No. Transaksi</p>
                <p class="text-2xl font-bold text-slate-800 tracking-widest">#{{ str_pad($transaksi->id_parkir, 6, '0', STR_PAD_LEFT) }}</p>
            </div>

            <!-- Vehicle Details -->
            <div class="space-y-2 text-sm border-b border-dashed border-slate-300 pb-4 mb-4">
                <div class="flex justify-between">
                    <span class="text-slate-500">Plat Nomor</span>
                    <span class="font-bold text-slate-800 tracking-wider">{{ $transaksi->kendaraan->plat_nomor }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Jenis Kendaraan</span>
                    <span class="text-slate-800">{{ ucfirst($transaksi->tarif->jenis_kendaraan) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Warna</span>
                    <span class="text-slate-800">{{ $transaksi->kendaraan->warna }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Pemilik</span>
                    <span class="text-slate-800">{{ $transaksi->kendaraan->pemilik }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Area Parkir</span>
                    <span class="text-slate-800">{{ $transaksi->areaParkir->nama_area }}</span>
                </div>
            </div>

            <!-- Time Details -->
            <div class="space-y-2 text-sm border-b border-dashed border-slate-300 pb-4 mb-4">
                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu Masuk</span>
                    <span class="text-slate-800">{{ $transaksi->waktu_masuk->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu Keluar</span>
                    <span class="text-slate-800">{{ $transaksi->waktu_keluar ? $transaksi->waktu_keluar->format('d/m/Y H:i') : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Durasi</span>
                    <span class="font-semibold text-amber-600">{{ $transaksi->durasi_jam ?? '-' }} jam</span>
                </div>
            </div>

            <!-- Cost Breakdown -->
            <div class="space-y-2 text-sm border-b border-dashed border-slate-300 pb-4 mb-4">
                <div class="flex justify-between">
                    <span class="text-slate-500">Tarif / Jam</span>
                    <span class="text-slate-800">Rp {{ number_format($transaksi->tarif->tarif_per_jam, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Durasi</span>
                    <span class="text-slate-800">{{ $transaksi->durasi_jam ?? 0 }} jam</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Biaya Parkir</span>
                    <span class="text-slate-800">Rp {{ number_format(($transaksi->durasi_jam ?? 0) * $transaksi->tarif->tarif_per_jam, 0, ',', '.') }}</span>
                </div>
                @if($transaksi->denda > 0)
                <div class="flex justify-between">
                    <span class="text-red-500 font-medium">Denda Karcis Hilang</span>
                    <span class="text-red-600 font-semibold">Rp {{ number_format($transaksi->denda, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center pt-2 mt-2 border-t border-slate-200">
                    <span class="text-lg font-bold text-slate-800">TOTAL BAYAR</span>
                    <span class="text-lg font-bold text-emerald-600">Rp {{ number_format($transaksi->biaya_total ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-xs text-slate-400 space-y-1">
                <p>Petugas: {{ $transaksi->user->nama_lengkap }}</p>
                <p class="font-medium text-slate-500">Terima kasih atas kunjungan Anda!</p>
                <p>{{ now()->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>

        <!-- Action Buttons (hidden from print) -->
        <div class="mt-6 flex gap-3 print:hidden">
            <button onclick="window.print()"
                    class="flex-1 py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Struk
            </button>
            <a href="{{ route('petugas.transaksi-keluar') }}"
               class="flex-1 py-3 px-4 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            #struk, #struk * { visibility: visible; }
            #struk { position: absolute; left: 0; top: 0; width: 80mm; }
        }
    </style>
</div>

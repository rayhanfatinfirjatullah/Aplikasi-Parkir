<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan — IntegraPark</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Force light / print-friendly on this standalone page */
        html, body {
            background: #ffffff !important;
            color: #0f172a !important;
            font-family: 'Inter', sans-serif;
        }

        /* ── Print-specific overrides ── */
        @media print {
            .no-print { display: none !important; }

            @page {
                size: landscape;
                margin: 1.2cm;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            table { page-break-inside: auto; }
            tr    { page-break-inside: avoid; page-break-after: auto; }
        }

        /* Clean print table */
        .print-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .print-table th,
        .print-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            text-align: left;
        }
        .print-table thead tr {
            background-color: #f1f5f9;
        }
        .print-table thead th {
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
        }
        .print-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="p-8 font-inter antialiased" onload="window.print()">

    {{-- ── Letterhead ── --}}
    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-4 mb-6">
        <div class="flex items-center gap-4">
            <img src="{{ asset('img/5.png') }}" alt="IntegraPark Logo" class="w-12 h-12 object-contain">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                    Integra<span class="text-cyan-600">Park</span>
                </h1>
                <p class="text-xs text-slate-500 font-medium">Smart Access. Solid Integrity.</p>
            </div>
        </div>
        <div class="text-right">
            <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wider">Laporan Pendapatan Parkir</h2>
            <p class="text-sm text-slate-600 mt-0.5">Periode: <strong>{{ $periode }}</strong></p>
            <p class="text-xs text-slate-400 mt-0.5">Dicetak: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    {{-- ── Summary Box ── --}}
    <div class="flex gap-6 mb-6">
        <div class="border border-slate-300 rounded-lg px-5 py-3">
            <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">Total Transaksi</p>
            <p class="text-2xl font-extrabold text-slate-900">{{ number_format($totalTransaksi) }}</p>
        </div>
        <div class="border border-slate-300 rounded-lg px-5 py-3">
            <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">Total Pendapatan</p>
            <p class="text-2xl font-extrabold text-emerald-700">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- ── Data Table ── --}}
    <table class="print-table mb-8">
        <thead>
            <tr>
                <th style="width:40px">No</th>
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
            @foreach($transaksis as $i => $t)
            <tr>
                <td class="text-center text-slate-500">{{ $i + 1 }}</td>
                <td class="font-bold tracking-wider">{{ $t->plat_nomor }}</td>
                <td>{{ ucfirst($t->tarif->jenis_kendaraan) }}</td>
                <td>{{ $t->areaParkir->nama_area }}</td>
                <td class="font-mono text-xs">{{ $t->waktu_masuk->format('d/m/Y H:i') }}</td>
                <td class="font-mono text-xs">{{ $t->waktu_keluar ? $t->waktu_keluar->format('d/m/Y H:i') : '—' }}</td>
                <td>{{ $t->durasi_jam ? $t->durasi_jam . ' jam' : '—' }}</td>
                <td class="font-semibold">{{ $t->biaya_total ? 'Rp ' . number_format($t->biaya_total, 0, ',', '.') : '—' }}</td>
                <td>{{ ucfirst($t->status) }}</td>
            </tr>
            @endforeach
            @if($transaksis->isEmpty())
            <tr>
                <td colspan="9" class="text-center py-8 text-slate-400 italic">Tidak ada data transaksi</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- ── Signature Area ── --}}
    <div class="flex justify-between mt-10 px-8">
        <div class="text-center">
            <p class="text-sm text-slate-600 mb-16">Mengetahui,</p>
            <div class="border-t border-slate-900 pt-2 w-40 mx-auto">
                <p class="text-sm font-bold text-slate-900">Manager Parkir</p>
            </div>
        </div>
        <div class="text-center">
            <p class="text-sm text-slate-600 mb-16">Menyetujui,</p>
            <div class="border-t border-slate-900 pt-2 w-40 mx-auto">
                <p class="text-sm font-bold text-slate-900">Owner</p>
            </div>
        </div>
    </div>

    {{-- ── No-Print Actions ── --}}
    <div class="no-print fixed bottom-6 right-6 flex gap-3">
        <button onclick="window.print()"
                class="flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Ulang
        </button>
        <button onclick="window.close()"
                class="flex items-center gap-2 px-5 py-2.5 bg-slate-600 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Tutup Tab
        </button>
    </div>

</body>
</html>

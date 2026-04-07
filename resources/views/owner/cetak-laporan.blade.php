<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Transaksi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: white !important;
            color: black !important;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            @page {
                size: landscape;
                margin: 1cm;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        .table-print {
            width: 100%;
            border-collapse: collapse;
        }
        .table-print th, .table-print td {
            border: 1px solid black;
            padding: 8px;
            font-size: 13px;
        }
        .table-print th {
            background-color: #f8fafc;
            font-weight: bold;
            text-align: left;
        }
    </style>
</head>
<body class="font-sans antialiased p-8" onload="window.print()">

    <!-- Print Header -->
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-black uppercase tracking-wider">LAPORAN PENDAPATAN PARKIR - Aplikasi Parkir</h1>
        <p class="text-lg text-slate-800 mt-2">Periode: {{ $periode }}</p>
    </div>

    <!-- Table -->
    <table class="table-print mb-6">
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
            @foreach($transaksis as $i => $t)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="font-bold">{{ $t->kendaraan->plat_nomor }}</td>
                <td>{{ ucfirst($t->tarif->jenis_kendaraan) }}</td>
                <td>{{ $t->areaParkir->nama_area }}</td>
                <td>{{ $t->waktu_masuk->format('d/m/Y H:i') }}</td>
                <td>{{ $t->waktu_keluar ? $t->waktu_keluar->format('d/m/Y H:i') : '-' }}</td>
                <td>{{ $t->durasi_jam ? $t->durasi_jam . ' jam' : '-' }}</td>
                <td>{{ $t->biaya_total ? 'Rp ' . number_format($t->biaya_total, 0, ',', '.') : '-' }}</td>
                <td>{{ ucfirst($t->status) }}</td>
            </tr>
            @endforeach
            @if($transaksis->isEmpty())
            <tr>
                <td colspan="9" class="text-center py-4">Tidak ada data transaksi</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Summary -->
    <div class="mb-12 border border-black p-4 inline-block">
        <p class="text-lg"><strong>Total Transaksi:</strong> {{ number_format($totalTransaksi) }}</p>
        <p class="text-lg mt-2"><strong>Total Pendapatan:</strong> Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
    </div>

    <!-- Signatures -->
    <div class="flex justify-between mt-8 pt-8 px-12">
        <div class="text-center">
            <p class="mb-24">Mengetahui,</p>
            <p class="font-bold underline">Manager Parkir</p>
        </div>
        <div class="text-center">
            <p class="mb-24">Menyetujui,</p>
            <p class="font-bold underline">Owner</p>
        </div>
    </div>

    <!-- No Print Action Button -->
    <div class="mt-12 text-center no-print">
        <button onclick="window.print()" class="px-6 py-2 bg-emerald-600 text-white font-bold rounded shadow hover:bg-emerald-700 transition">
            Cetak Ulang
        </button>
        <button onclick="window.close()" class="px-6 py-2 bg-slate-500 text-white font-bold rounded shadow hover:bg-slate-600 ml-4 transition">
            Tutup Tab
        </button>
    </div>

</body>
</html>

<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CetakLaporanController extends Controller
{
    public function cetak(Request $request)
    {
        $tanggal_mulai = $request->query('tanggal_mulai');
        $tanggal_selesai = $request->query('tanggal_selesai');
        $search = $request->query('search');

        $query = Transaksi::with(['kendaraan', 'tarif', 'areaParkir', 'user']);

        if ($tanggal_mulai) {
            $query->whereDate('waktu_masuk', '>=', $tanggal_mulai);
        }
        if ($tanggal_selesai) {
            $query->whereDate('waktu_masuk', '<=', $tanggal_selesai);
        }
        if ($search) {
            $query->whereHas('kendaraan', function ($q) use ($search) {
                $q->where('plat_nomor', 'like', "%{$search}%");
            });
        }

        $totalTransaksi = (clone $query)->count();
        $totalPendapatan = (clone $query)->where('status', 'keluar')->sum('biaya_total');

        $transaksis = $query->orderBy('waktu_masuk', 'desc')->get();

        // Format periode
        $periode = 'Semua Waktu';
        if ($tanggal_mulai && $tanggal_selesai) {
            $periode = Carbon::parse($tanggal_mulai)->translatedFormat('d F Y') . ' - ' . Carbon::parse($tanggal_selesai)->translatedFormat('d F Y');
        } elseif ($tanggal_mulai) {
            $periode = 'Mulai ' . Carbon::parse($tanggal_mulai)->translatedFormat('d F Y');
        } elseif ($tanggal_selesai) {
            $periode = 'Hingga ' . Carbon::parse($tanggal_selesai)->translatedFormat('d F Y');
        }

        return view('owner.cetak-laporan', compact('transaksis', 'totalTransaksi', 'totalPendapatan', 'periode'));
    }
}

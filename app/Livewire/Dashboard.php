<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaksi;
use App\Models\Kendaraan;
use App\Models\AreaParkir;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();

        $totalKendaraan = Kendaraan::count();
        $transaksiHariIni = Transaksi::whereDate('waktu_masuk', today())->count();
        $kendaraanTerparkir = Transaksi::where('status', 'masuk')->count();
        $pendapatanHariIni = Transaksi::whereDate('waktu_masuk', today())
            ->where('status', 'keluar')
            ->sum('biaya_total');
        $areaParkir = AreaParkir::all();
        $transaksiTerbaru = Transaksi::with(['kendaraan', 'tarif', 'areaParkir', 'user'])
            ->latest('waktu_masuk')
            ->take(5)
            ->get();

        return view('livewire.dashboard', compact(
            'totalKendaraan',
            'transaksiHariIni',
            'kendaraanTerparkir',
            'pendapatanHariIni',
            'areaParkir',
            'transaksiTerbaru'
        ))->layout('layouts.app');
    }
}

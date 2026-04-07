<?php

namespace App\Livewire\Petugas;

use App\Models\Transaksi;
use App\Models\LogAktivitas;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class TransaksiKeluar extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showCheckout = false;
    public ?array $checkoutData = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCheckout($id)
    {
        $transaksi = Transaksi::with(['kendaraan', 'tarif', 'areaParkir'])->findOrFail($id);

        $waktuKeluar = now();
        $waktuMasuk = Carbon::parse($transaksi->waktu_masuk);
        $durasiJam = max(1, (int) ceil($waktuMasuk->diffInMinutes($waktuKeluar) / 60));
        $biayaTotal = $durasiJam * $transaksi->tarif->tarif_per_jam;

        $this->checkoutData = [
            'id_parkir' => $transaksi->id_parkir,
            'plat_nomor' => $transaksi->kendaraan->plat_nomor,
            'warna' => $transaksi->kendaraan->warna,
            'pemilik' => $transaksi->kendaraan->pemilik,
            'jenis_kendaraan' => ucfirst($transaksi->tarif->jenis_kendaraan),
            'area' => $transaksi->areaParkir->nama_area,
            'waktu_masuk' => $transaksi->waktu_masuk->format('d/m/Y H:i'),
            'waktu_keluar' => $waktuKeluar->format('d/m/Y H:i'),
            'durasi_jam' => $durasiJam,
            'tarif_per_jam' => $transaksi->tarif->tarif_per_jam,
            'biaya_total' => $biayaTotal,
        ];

        $this->showCheckout = true;
    }

    public function checkout()
    {
        if (!$this->checkoutData) return;

        $transaksi = Transaksi::with(['areaParkir', 'kendaraan', 'tarif'])->findOrFail($this->checkoutData['id_parkir']);

        $waktuKeluar = now();
        $waktuMasuk = Carbon::parse($transaksi->waktu_masuk);
        $durasiJam = max(1, (int) ceil($waktuMasuk->diffInMinutes($waktuKeluar) / 60));
        $biayaTotal = $durasiJam * $transaksi->tarif->tarif_per_jam;

        $transaksi->update([
            'waktu_keluar' => $waktuKeluar,
            'durasi_jam' => $durasiJam,
            'biaya_total' => $biayaTotal,
            'status' => 'keluar',
        ]);

        // Decrement area terisi
        $transaksi->areaParkir->decrement('terisi');

        // Log activity
        LogAktivitas::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => "Check-out kendaraan {$transaksi->kendaraan->plat_nomor}, biaya: Rp " . number_format($biayaTotal, 0, ',', '.'),
            'waktu_aktivitas' => now(),
        ]);

        $this->showCheckout = false;

        return redirect()->route('petugas.cetak-struk', $transaksi->id_parkir);
    }

    public function render()
    {
        $transaksis = Transaksi::with(['kendaraan', 'tarif', 'areaParkir', 'user'])
            ->where('status', 'masuk')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->whereHas('kendaraan', function ($q3) {
                        $q3->where('plat_nomor', 'like', "%{$this->search}%");
                    })->orWhere('id_parkir', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('waktu_masuk', 'desc')
            ->paginate(10);

        return view('livewire.petugas.transaksi-keluar', compact('transaksis'))
            ->layout('layouts.app');
    }
}

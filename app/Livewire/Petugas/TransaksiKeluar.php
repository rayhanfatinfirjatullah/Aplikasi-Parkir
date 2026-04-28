<?php

namespace App\Livewire\Petugas;

use App\Models\Transaksi;
use App\Models\StatusKendaraan;
use App\Models\LogAktivitas;
use App\Models\Pengaturan;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class TransaksiKeluar extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showCheckout = false;
    public ?array $checkoutData = null;

    // Denda karcis hilang
    public bool $isKarcisHilang = false;
    public int $nilaiDenda = 0;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCheckout($id)
    {
        $this->isKarcisHilang = false;
        $this->nilaiDenda = Pengaturan::getDendaKarcisHilang();

        $transaksi = Transaksi::with(['kendaraan', 'tarif', 'areaParkir'])->findOrFail($id);

        $waktuKeluar = now();
        $waktuMasuk = Carbon::parse($transaksi->waktu_masuk);
        $durasiJam = max(1, (int) ceil($waktuMasuk->diffInMinutes($waktuKeluar) / 60));
        $statusSpesial = $transaksi->status_spesial ?? 'reguler';
        $statusModel = StatusKendaraan::where('nama_status', $statusSpesial)->first();
        
        $metodeTarif = $statusModel ? $statusModel->metode_tarif : 'reguler';
        $nominalTarif = $statusModel ? (float) $statusModel->nominal_tarif : 0;
        $isImmuneDenda = $statusModel ? $statusModel->is_bebas_denda : false;
        
        $biayaParkir = 0;
        $isMembershipExpired = false;

        if ($metodeTarif === 'reguler') {
            $biayaParkir = $durasiJam * $transaksi->tarif->tarif_per_jam;
        } elseif ($metodeTarif === 'fix_per_hari') {
            $hasPaidToday = Transaksi::where('plat_nomor', $transaksi->plat_nomor)
                ->where('status', 'keluar')
                ->whereDate('waktu_keluar', today())
                ->where('biaya_parkir', '>', 0)
                ->exists();
            $biayaParkir = $hasPaidToday ? 0 : $nominalTarif;
        } elseif ($metodeTarif === 'membership') {
            if ($transaksi->kendaraan && $transaksi->kendaraan->isMembershipActive()) {
                $biayaParkir = 0;
            } else {
                $isMembershipExpired = true;
                $biayaParkir = $durasiJam * $transaksi->tarif->tarif_per_jam;
                $this->dispatch('toast', type: 'error', message: 'Membership Kadaluwarsa - Gunakan Tarif Reguler');
            }
        }

        $this->checkoutData = [
            'id_parkir' => $transaksi->id_parkir,
            'plat_nomor' => $transaksi->plat_nomor,
            'warna' => $transaksi->warna,
            'status_spesial' => $statusSpesial,
            'metode_tarif' => $metodeTarif,
            'is_membership_expired' => $isMembershipExpired,
            'is_immune_denda' => $isImmuneDenda,
            'pemilik' => $transaksi->pemilik,
            'jenis_kendaraan' => ucfirst($transaksi->tarif->jenis_kendaraan),
            'area' => $transaksi->areaParkir->nama_area,
            'waktu_masuk' => $transaksi->waktu_masuk->format('d/m/Y H:i'),
            'waktu_keluar' => $waktuKeluar->format('d/m/Y H:i'),
            'durasi_jam' => $durasiJam,
            'tarif_per_jam' => $transaksi->tarif->tarif_per_jam,
            'biaya_parkir' => $biayaParkir,
            'denda' => 0,
            'biaya_total' => $biayaParkir,
        ];

        $this->showCheckout = true;
    }

    /**
     * Reactively recalculate total when checkbox changes.
     */
    public function updatedIsKarcisHilang()
    {
        if (!$this->checkoutData) return;

        $isImmune = $this->checkoutData['is_immune_denda'] ?? false;
        $denda = ($this->isKarcisHilang && !$isImmune) ? $this->nilaiDenda : 0;
        $this->checkoutData['denda'] = $denda;
        $this->checkoutData['biaya_total'] = $this->checkoutData['biaya_parkir'] + $denda;
    }

    public function checkout()
    {
        if (!$this->checkoutData) return;

        $transaksi = Transaksi::with(['areaParkir', 'tarif', 'kendaraan'])->findOrFail($this->checkoutData['id_parkir']);

        $waktuKeluar = now();
        $waktuMasuk = Carbon::parse($transaksi->waktu_masuk);
        $durasiJam = max(1, (int) ceil($waktuMasuk->diffInMinutes($waktuKeluar) / 60));
        $statusSpesial = $transaksi->status_spesial ?? 'reguler';
        $statusModel = StatusKendaraan::where('nama_status', $statusSpesial)->first();
        
        $metodeTarif = $statusModel ? $statusModel->metode_tarif : 'reguler';
        $nominalTarif = $statusModel ? (float) $statusModel->nominal_tarif : 0;
        $isImmuneDenda = $statusModel ? $statusModel->is_bebas_denda : false;
        
        $biayaParkir = 0;

        if ($metodeTarif === 'reguler') {
            $biayaParkir = $durasiJam * $transaksi->tarif->tarif_per_jam;
        } elseif ($metodeTarif === 'fix_per_hari') {
            $hasPaidToday = Transaksi::where('plat_nomor', $transaksi->plat_nomor)
                ->where('status', 'keluar')
                ->whereDate('waktu_keluar', today())
                ->where('biaya_parkir', '>', 0)
                ->exists();
            $biayaParkir = $hasPaidToday ? 0 : $nominalTarif;
        } elseif ($metodeTarif === 'membership') {
            if ($transaksi->kendaraan && $transaksi->kendaraan->isMembershipActive()) {
                $biayaParkir = 0;
            } else {
                $biayaParkir = $durasiJam * $transaksi->tarif->tarif_per_jam;
            }
        }
        
        $denda = ($this->isKarcisHilang && !$isImmuneDenda) ? $this->nilaiDenda : 0;
        $biayaTotal = $biayaParkir + $denda;

        $transaksi->update([
            'waktu_keluar' => $waktuKeluar,
            'durasi_jam' => $durasiJam,
            'biaya_total' => $biayaTotal,
            'biaya_parkir' => $biayaParkir,
            'denda' => $denda,
            'status' => 'keluar',
        ]);

        // Decrement area terisi
        $transaksi->areaParkir->decrement('terisi');

        // Log activity
        $logMessage = "Check-out kendaraan {$transaksi->plat_nomor}, biaya: Rp " . number_format($biayaTotal, 0, ',', '.');
        if ($denda > 0) {
            $logMessage .= " (termasuk denda karcis hilang: Rp " . number_format($denda, 0, ',', '.') . ")";
        }

        LogAktivitas::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => $logMessage,
            'waktu_aktivitas' => now(),
        ]);

        $this->showCheckout = false;
        $this->isKarcisHilang = false;

        return redirect()->route('petugas.cetak-struk', $transaksi->id_parkir);
    }

    public function render()
    {
        $transaksis = Transaksi::with(['tarif', 'areaParkir', 'user'])
            ->where('status', 'masuk')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('plat_nomor', 'like', "%{$this->search}%")
                       ->orWhere('id_parkir', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('waktu_masuk', 'desc')
            ->paginate(10);

        $statuses = StatusKendaraan::all();
        return view('livewire.petugas.transaksi-keluar', compact('transaksis', 'statuses'))
            ->layout('layouts.app');
    }
}

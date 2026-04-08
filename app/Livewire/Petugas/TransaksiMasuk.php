<?php

namespace App\Livewire\Petugas;

use App\Models\Kendaraan;
use App\Models\Tarif;
use App\Models\AreaParkir;
use App\Models\Transaksi;
use App\Models\LogAktivitas;
use Livewire\Component;

class TransaksiMasuk extends Component
{
    public string $plat_nomor = '';
    public string $warna = '';
    public string $pemilik = '';
    public string $jenis_kendaraan = 'motor';
    public string $id_area = '';

    protected array $rules = [
        'plat_nomor' => 'required|string|max:20',
        'warna' => 'required|string|max:50',
        'pemilik' => 'required|string|max:255',
        'jenis_kendaraan' => 'required|in:motor,mobil,lainnya',
        'id_area' => 'required|exists:tb_area_parkir,id_area',
    ];

    protected array $messages = [
        'plat_nomor.required' => 'Plat nomor wajib diisi',
        'plat_nomor.max' => 'Plat nomor maksimal 20 karakter',
        'warna.required' => 'Warna kendaraan wajib diisi',
        'warna.max' => 'Warna kendaraan maksimal 50 karakter',
        'pemilik.required' => 'Nama pemilik wajib diisi',
        'pemilik.max' => 'Nama pemilik maksimal 255 karakter',
        'jenis_kendaraan.required' => 'Jenis kendaraan wajib dipilih',
        'jenis_kendaraan.in' => 'Jenis kendaraan tidak valid',
        'id_area.required' => 'Area parkir wajib dipilih',
        'id_area.exists' => 'Area parkir tidak ditemukan',
    ];

    public function updatedPlatNomor()
    {
        $this->plat_nomor = strtoupper($this->plat_nomor);
        $kendaraan = Kendaraan::where('plat_nomor', $this->plat_nomor)->first();
        if ($kendaraan) {
            $this->warna = $kendaraan->warna;
            $this->pemilik = $kendaraan->pemilik;
        }
    }

    public function checkin()
    {
        $this->validate();

        // Check area capacity
        $area = AreaParkir::findOrFail($this->id_area);
        if ($area->isFull()) {
            $this->addError('id_area', 'Area parkir sudah penuh!');
            return;
        }

        // Check if vehicle already parked
        $existing = Transaksi::whereHas('kendaraan', function ($q) {
            $q->where('plat_nomor', $this->plat_nomor);
        })->where('status', 'masuk')->first();

        if ($existing) {
            $this->addError('plat_nomor', 'Kendaraan dengan plat nomor ini masih terparkir!');
            return;
        }

        // Create or find kendaraan
        $kendaraan = Kendaraan::firstOrCreate(
            ['plat_nomor' => strtoupper($this->plat_nomor)],
            ['warna' => $this->warna, 'pemilik' => $this->pemilik]
        );

        // Update kendaraan if data changed
        $kendaraan->update(['warna' => $this->warna, 'pemilik' => $this->pemilik]);

        // Get tarif
        $tarif = Tarif::where('jenis_kendaraan', $this->jenis_kendaraan)->firstOrFail();

        // Create transaksi
        $transaksi = Transaksi::create([
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'id_tarif' => $tarif->id_tarif,
            'id_area' => $area->id_area,
            'id_user' => auth()->user()->id_user,
            'waktu_masuk' => now(),
            'status' => 'masuk',
        ]);

        // Increment area terisi
        $area->increment('terisi');

        // Log activity
        LogAktivitas::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => "Check-in kendaraan {$kendaraan->plat_nomor} di {$area->nama_area}",
            'waktu_aktivitas' => now(),
        ]);

        // Redirect to print entry ticket
        return redirect()->route('petugas.cetak-karcis', $transaksi->id_parkir);
    }

    public function render()
    {
        $areas = AreaParkir::all();
        $tarifs = Tarif::all();
        $registeredVehicles = Kendaraan::whereDoesntHave('transaksi', function ($query) {
            $query->where('status', 'masuk');
        })->get();

        return view('livewire.petugas.transaksi-masuk', compact('areas', 'tarifs', 'registeredVehicles'))
            ->layout('layouts.app');
    }
}

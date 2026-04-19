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
    public string $status_spesial = 'reguler';

    protected array $rules = [
        'plat_nomor' => 'required|string|max:20',
        'warna' => 'required|string|max:50',
        'pemilik' => 'required|string|max:255',
        'jenis_kendaraan' => 'required|exists:tb_tarif,jenis_kendaraan',
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
        'jenis_kendaraan.max' => 'Jenis kendaraan maksimal 50 karakter',
        'id_area.required' => 'Area parkir wajib dipilih',
        'id_area.exists' => 'Area parkir tidak ditemukan',
    ];

    public function updatedPlatNomor()
    {
        $this->plat_nomor = strtoupper($this->plat_nomor);
        $this->fillVehicleData();
    }

    public function updatedJenisKendaraan()
    {
        $this->autoSelectArea();
    }

    public function fillVehicleData()
    {
        $kendaraan = Kendaraan::where('plat_nomor', $this->plat_nomor)->first();
        if ($kendaraan) {
            $this->warna = $kendaraan->warna;
            $this->pemilik = $kendaraan->pemilik;
            $this->status_spesial = $kendaraan->status_spesial;
        } else {
            $this->status_spesial = 'reguler';
        }
        
        $this->autoSelectArea();
    }

    public function autoSelectArea()
    {
        $areas = AreaParkir::all();
        $validAreas = collect();

        foreach ($areas as $area) {
            if (!$area->isFull() && $area->canAcceptVehicleType($this->jenis_kendaraan) && $area->canAccessByPrivilege($this->status_spesial)) {
                $validAreas->push($area);
            }
        }

        if ($validAreas->isNotEmpty()) {
            $bestArea = $validAreas->sortByDesc(function ($a) {
                return $a->kapasitas - $a->terisi;
            })->first();
            $this->id_area = $bestArea->id_area;
        } else {
            $this->id_area = '';
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

        // Check vehicle type match
        if (!$area->canAcceptVehicleType($this->jenis_kendaraan)) {
            $this->addError('id_area', 'Area ini tidak diperuntukkan bagi kendaraan ' . ucfirst($this->jenis_kendaraan) . '!');
            return;
        }

        // Check privilege
        if (!$area->canAccessByPrivilege($this->status_spesial)) {
            $this->addError('id_area', 'Kendaraan ini tidak memiliki akses ke area ' . $area->nama_area . '!');
            return;
        }

        // Check if vehicle already parked (check directly on transaksi table)
        $existing = Transaksi::where('plat_nomor', strtoupper($this->plat_nomor))
            ->where('status', 'masuk')
            ->first();

        if ($existing) {
            $this->addError('plat_nomor', 'Kendaraan dengan plat nomor ini masih terparkir!');
            return;
        }

        // Determine if this is a registered (VIP/VVIP) vehicle
        $kendaraan = Kendaraan::where('plat_nomor', strtoupper($this->plat_nomor))->first();
        $idKendaraan = null;

        if ($kendaraan) {
            // Update master data if info changed
            $kendaraan->update(['warna' => $this->warna, 'pemilik' => $this->pemilik]);
            $idKendaraan = $kendaraan->id_kendaraan;
        }
        // For reguler vehicles, we DON'T create a master record — data goes directly into transaksi

        // Get tarif
        $tarif = Tarif::where('jenis_kendaraan', $this->jenis_kendaraan)->firstOrFail();

        // Create transaksi with denormalized vehicle data
        $transaksi = Transaksi::create([
            'id_kendaraan' => $idKendaraan,
            'id_tarif' => $tarif->id_tarif,
            'id_area' => $area->id_area,
            'id_user' => auth()->user()->id_user,
            'plat_nomor' => strtoupper($this->plat_nomor),
            'warna' => $this->warna,
            'pemilik' => $this->pemilik,
            'status_spesial' => $this->status_spesial,
            'waktu_masuk' => now(),
            'status' => 'masuk',
        ]);

        // Increment area terisi
        $area->increment('terisi');

        // Log activity
        LogAktivitas::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => "Check-in kendaraan " . strtoupper($this->plat_nomor) . " di {$area->nama_area}",
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

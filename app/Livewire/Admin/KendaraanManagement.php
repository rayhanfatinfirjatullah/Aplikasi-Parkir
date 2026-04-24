<?php

namespace App\Livewire\Admin;

use App\Models\Kendaraan;
use App\Models\Tarif;
use App\Models\StatusKendaraan;
use App\Models\LogAktivitas as LogModel;
use Livewire\Component;
use Livewire\WithPagination;

class KendaraanManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $editId = null;

    public string $plat_nomor = '';
    public string $warna = '';
    public string $pemilik = '';
    public string $status_spesial = 'reguler';
    public string $jenis_kendaraan = 'motor';

    protected function rules()
    {
        $uniqueRule = $this->isEdit ? 'unique:tb_kendaraan,plat_nomor,' . $this->editId . ',id_kendaraan' : 'unique:tb_kendaraan,plat_nomor';
        return [
            'plat_nomor' => 'required|string|max:20|' . $uniqueRule,
            'warna' => 'required|string|max:50',
            'pemilik' => 'required|string|max:255',
            'status_spesial' => 'required|exists:tb_status_kendaraan,nama_status',
            'jenis_kendaraan' => 'required|exists:tb_tarif,jenis_kendaraan',
        ];
    }

    protected array $messages = [
        'plat_nomor.required' => 'Plat nomor wajib diisi',
        'plat_nomor.max' => 'Plat nomor maksimal 20 karakter',
        'plat_nomor.unique' => 'Plat nomor sudah terdaftar',
        'warna.required' => 'Warna wajib diisi',
        'warna.max' => 'Warna maksimal 50 karakter',
        'pemilik.required' => 'Pemilik wajib diisi',
        'pemilik.max' => 'Pemilik maksimal 255 karakter',
        'jenis_kendaraan.required' => 'Jenis kendaraan wajib dipilih',
        'jenis_kendaraan.in' => 'Jenis kendaraan tidak valid',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->reset(['plat_nomor', 'warna', 'pemilik', 'status_spesial', 'jenis_kendaraan', 'editId', 'isEdit']);
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        $this->editId = $id;
        $this->isEdit = true;
        $this->plat_nomor = $kendaraan->plat_nomor;
        $this->warna = $kendaraan->warna;
        $this->pemilik = $kendaraan->pemilik;
        $this->status_spesial = $kendaraan->status_spesial ?? 'reguler';
        $this->jenis_kendaraan = $kendaraan->jenis_kendaraan ?? 'motor';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset(['plat_nomor', 'warna', 'pemilik', 'status_spesial', 'jenis_kendaraan', 'editId', 'isEdit']);
        $this->resetValidation();
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'plat_nomor' => strtoupper($this->plat_nomor),
            'warna' => $this->warna,
            'pemilik' => $this->pemilik,
            'status_spesial' => $this->status_spesial,
            'jenis_kendaraan' => $this->jenis_kendaraan,
        ];

        if ($this->isEdit) {
            Kendaraan::where('id_kendaraan', $this->editId)->update($data);
            $aktivitas = "Mengubah data kendaraan: {$this->plat_nomor}";
        } else {
            Kendaraan::create($data);
            $aktivitas = "Menambah kendaraan: {$this->plat_nomor}";
        }

        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => $aktivitas,
            'waktu_aktivitas' => now(),
        ]);

        $this->showModal = false;
        $this->dispatch('toast', type: 'success', message: 'Data kendaraan berhasil disimpan!');
    }

    // -- Renew Membership --
    public bool $showRenewModal = false;
    public ?int $renewId = null;
    public ?int $renewMonths = null;

    public function openRenew($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        $this->renewId = $id;
        $this->renewMonths = null;
        $this->showRenewModal = true;
    }

    public function closeRenewModal()
    {
        $this->showRenewModal = false;
        $this->reset(['renewId', 'renewMonths']);
        $this->resetValidation();
    }

    public function processRenew()
    {
        $this->validate([
            'renewMonths' => 'required|integer|min:1|max:12'
        ]);

        $kendaraan = Kendaraan::findOrFail($this->renewId);
        
        $currentExpiry = $kendaraan->masa_aktif_hingga && \Carbon\Carbon::parse($kendaraan->masa_aktif_hingga)->endOfDay()->isFuture() 
            ? \Carbon\Carbon::parse($kendaraan->masa_aktif_hingga) 
            : now();
            
        $newExpiry = $currentExpiry->addMonths($this->renewMonths);
        
        $kendaraan->update([
            'masa_aktif_hingga' => $newExpiry->format('Y-m-d')
        ]);

        $statusModel = \App\Models\StatusKendaraan::where('nama_status', $kendaraan->status_spesial)->first();
        $cost = $statusModel ? $statusModel->nominal_tarif * $this->renewMonths : 0;

        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => "Perpanjang Membership: {$kendaraan->plat_nomor} selama {$this->renewMonths} bulan (Rp " . number_format($cost, 0, ',', '.') . ")",
            'waktu_aktivitas' => now(),
        ]);

        $this->closeRenewModal();
        $this->dispatch('toast', type: 'success', message: 'Membership ' . $kendaraan->plat_nomor . ' berhasil diperpanjang hingga ' . $newExpiry->format('d/m/Y') . '!');
    }

    public function delete($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => "Menghapus kendaraan: {$kendaraan->plat_nomor}",
            'waktu_aktivitas' => now(),
        ]);
        $kendaraan->delete();
        $this->dispatch('toast', type: 'success', message: 'Kendaraan berhasil dihapus!');
    }

    public function render()
    {
        $kendaraans = Kendaraan::where(function ($q) {
            $q->where('plat_nomor', 'like', "%{$this->search}%")
              ->orWhere('pemilik', 'like', "%{$this->search}%");
        })->orderBy('id_kendaraan', 'desc')->paginate(10);

        $tarifs = Tarif::all();
        $statuses = StatusKendaraan::orderBy('prioritas_level')->get();

        return view('livewire.admin.kendaraan-management', compact('kendaraans', 'tarifs', 'statuses'))
            ->layout('layouts.app');
    }
}

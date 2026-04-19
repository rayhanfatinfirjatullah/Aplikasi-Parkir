<?php

namespace App\Livewire\Admin;

use App\Models\Kendaraan;
use App\Models\Tarif;
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
            'status_spesial' => 'required|in:reguler,vip,vvip',
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
        session()->flash('success', 'Data kendaraan berhasil disimpan!');
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
        session()->flash('success', 'Kendaraan berhasil dihapus!');
    }

    public function render()
    {
        $kendaraans = Kendaraan::where(function ($q) {
            $q->where('plat_nomor', 'like', "%{$this->search}%")
              ->orWhere('pemilik', 'like', "%{$this->search}%");
        })->orderBy('id_kendaraan', 'desc')->paginate(10);

        $tarifs = Tarif::all();

        return view('livewire.admin.kendaraan-management', compact('kendaraans', 'tarifs'))
            ->layout('layouts.app');
    }
}

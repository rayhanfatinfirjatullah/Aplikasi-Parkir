<?php

namespace App\Livewire\Admin;

use App\Models\Kendaraan;
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

    protected function rules()
    {
        $uniqueRule = $this->isEdit ? 'unique:tb_kendaraan,plat_nomor,' . $this->editId . ',id_kendaraan' : 'unique:tb_kendaraan,plat_nomor';
        return [
            'plat_nomor' => 'required|string|max:20|' . $uniqueRule,
            'warna' => 'required|string|max:50',
            'pemilik' => 'required|string|max:255',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->reset(['plat_nomor', 'warna', 'pemilik', 'editId', 'isEdit']);
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
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'plat_nomor' => strtoupper($this->plat_nomor),
            'warna' => $this->warna,
            'pemilik' => $this->pemilik,
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

        return view('livewire.admin.kendaraan-management', compact('kendaraans'))
            ->layout('layouts.app');
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\Tarif;
use App\Models\LogAktivitas as LogModel;
use Livewire\Component;
use Livewire\WithPagination;

class TarifManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $editId = null;

    public string $jenis_kendaraan = 'motor';
    public string $tarif_per_jam = '';

    protected array $rules = [
        'jenis_kendaraan' => 'required|in:motor,mobil,lainnya',
        'tarif_per_jam' => 'required|numeric|min:0',
    ];

    public function openCreate()
    {
        $this->reset(['jenis_kendaraan', 'tarif_per_jam', 'editId', 'isEdit']);
        $this->jenis_kendaraan = 'motor';
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $tarif = Tarif::findOrFail($id);
        $this->editId = $id;
        $this->isEdit = true;
        $this->jenis_kendaraan = $tarif->jenis_kendaraan;
        $this->tarif_per_jam = $tarif->tarif_per_jam;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'jenis_kendaraan' => $this->jenis_kendaraan,
            'tarif_per_jam' => $this->tarif_per_jam,
        ];

        if ($this->isEdit) {
            Tarif::where('id_tarif', $this->editId)->update($data);
            $aktivitas = "Mengubah tarif {$this->jenis_kendaraan}";
        } else {
            Tarif::create($data);
            $aktivitas = "Menambah tarif {$this->jenis_kendaraan}";
        }

        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => $aktivitas,
            'waktu_aktivitas' => now(),
        ]);

        $this->showModal = false;
        session()->flash('success', 'Tarif berhasil disimpan!');
    }

    public function delete($id)
    {
        $tarif = Tarif::findOrFail($id);
        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => "Menghapus tarif {$tarif->jenis_kendaraan}",
            'waktu_aktivitas' => now(),
        ]);
        $tarif->delete();
        session()->flash('success', 'Tarif berhasil dihapus!');
    }

    public function render()
    {
        $tarifs = Tarif::where('jenis_kendaraan', 'like', "%{$this->search}%")
            ->orderBy('id_tarif', 'desc')
            ->paginate(10);

        return view('livewire.admin.tarif-management', compact('tarifs'))
            ->layout('layouts.app');
    }
}

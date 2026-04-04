<?php

namespace App\Livewire\Admin;

use App\Models\AreaParkir;
use App\Models\LogAktivitas as LogModel;
use Livewire\Component;
use Livewire\WithPagination;

class AreaParkirManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $editId = null;

    public string $nama_area = '';
    public string $kapasitas = '';

    protected array $rules = [
        'nama_area' => 'required|string|max:255',
        'kapasitas' => 'required|integer|min:1',
    ];

    public function openCreate()
    {
        $this->reset(['nama_area', 'kapasitas', 'editId', 'isEdit']);
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $area = AreaParkir::findOrFail($id);
        $this->editId = $id;
        $this->isEdit = true;
        $this->nama_area = $area->nama_area;
        $this->kapasitas = $area->kapasitas;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nama_area' => $this->nama_area,
            'kapasitas' => $this->kapasitas,
        ];

        if ($this->isEdit) {
            AreaParkir::where('id_area', $this->editId)->update($data);
            $aktivitas = "Mengubah area parkir: {$this->nama_area}";
        } else {
            $data['terisi'] = 0;
            AreaParkir::create($data);
            $aktivitas = "Menambah area parkir: {$this->nama_area}";
        }

        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => $aktivitas,
            'waktu_aktivitas' => now(),
        ]);

        $this->showModal = false;
        session()->flash('success', 'Area parkir berhasil disimpan!');
    }

    public function delete($id)
    {
        $area = AreaParkir::findOrFail($id);
        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => "Menghapus area parkir: {$area->nama_area}",
            'waktu_aktivitas' => now(),
        ]);
        $area->delete();
        session()->flash('success', 'Area parkir berhasil dihapus!');
    }

    public function render()
    {
        $areas = AreaParkir::where('nama_area', 'like', "%{$this->search}%")
            ->orderBy('id_area', 'desc')
            ->paginate(10);

        return view('livewire.admin.area-parkir-management', compact('areas'))
            ->layout('layouts.app');
    }
}

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
    public array $tipe_kendaraan = ['semua'];
    public string $level_akses = 'reguler';

    protected array $rules = [
        'nama_area' => 'required|string|max:255',
        'kapasitas' => 'required|integer|min:1',
        'tipe_kendaraan' => 'required|array|min:1',
        'level_akses' => 'required|in:reguler,vip,vvip',
    ];

    protected array $messages = [
        'nama_area.required' => 'Nama area wajib diisi',
        'nama_area.max' => 'Nama area maksimal 255 karakter',
        'kapasitas.required' => 'Kapasitas wajib diisi',
        'kapasitas.integer' => 'Kapasitas harus berupa angka',
        'kapasitas.min' => 'Kapasitas minimal 1',
        'tipe_kendaraan.required' => 'Tipe kendaraan wajib dipilih (minimal 1)',
        'tipe_kendaraan.min' => 'Pilih minimal 1 tipe kendaraan',
        'level_akses.required' => 'Level akses wajib dipilih',
        'level_akses.in' => 'Level akses tidak valid',
    ];

    public function openCreate()
    {
        $this->reset(['nama_area', 'kapasitas', 'level_akses', 'editId', 'isEdit']);
        $this->tipe_kendaraan = ['semua'];
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $area = AreaParkir::findOrFail($id);
        $this->editId = $id;
        $this->isEdit = true;
        $this->nama_area = $area->nama_area;
        $this->kapasitas = $area->kapasitas;
        $this->tipe_kendaraan = array_map('trim', explode(',', $area->tipe_kendaraan));
        $this->level_akses = $area->level_akses;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset(['nama_area', 'kapasitas', 'level_akses', 'editId', 'isEdit']);
        $this->tipe_kendaraan = ['semua'];
        $this->resetValidation();
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nama_area' => $this->nama_area,
            'kapasitas' => $this->kapasitas,
            'tipe_kendaraan' => implode(', ', array_map('strtolower', $this->tipe_kendaraan)),
            'level_akses' => $this->level_akses,
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

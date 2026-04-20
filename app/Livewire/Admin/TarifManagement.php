<?php

namespace App\Livewire\Admin;

use App\Models\Tarif;
use App\Models\Pengaturan;
use App\Models\LogAktivitas as LogModel;
use Livewire\Component;

class TarifManagement extends Component
{

    public string $search = '';
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $editId = null;

    public string $jenis_kendaraan = '';
    public string $tarif_per_jam = '';

    // Pengaturan denda
    public string $denda_karcis_hilang = '';

    public function mount()
    {
        $this->denda_karcis_hilang = Pengaturan::getValue('denda_karcis_hilang', '20000');
    }

    public function rules()
    {
        return [
            'jenis_kendaraan' => 'required|string|max:50|unique:tb_tarif,jenis_kendaraan,' . ($this->isEdit ? $this->editId : 'NULL') . ',id_tarif',
            'tarif_per_jam' => 'required|numeric|min:0',
        ];
    }

    protected array $messages = [
        'jenis_kendaraan.required' => 'Jenis kendaraan wajib diisi',
        'jenis_kendaraan.unique' => 'Jenis kendaraan ini sudah ada di daftar tarif',
        'jenis_kendaraan.max' => 'Jenis kendaraan maksimal 50 karakter',
        'tarif_per_jam.required' => 'Tarif per jam wajib diisi',
        'tarif_per_jam.numeric' => 'Tarif per jam harus berupa angka',
        'tarif_per_jam.min' => 'Tarif per jam minimal 0',
    ];

    public function openCreate()
    {
        $this->reset(['jenis_kendaraan', 'tarif_per_jam', 'editId', 'isEdit']);
        $this->jenis_kendaraan = '';
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

    public function closeModal()
    {
        $this->reset(['jenis_kendaraan', 'tarif_per_jam', 'editId', 'isEdit']);
        $this->resetValidation();
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'jenis_kendaraan' => strtolower($this->jenis_kendaraan),
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
        $this->dispatch('toast', type: 'success', message: 'Tarif berhasil disimpan!');
    }

    public function updateDenda()
    {
        $this->validate([
            'denda_karcis_hilang' => 'required|numeric|min:0',
        ], [
            'denda_karcis_hilang.required' => 'Denda karcis hilang wajib diisi',
            'denda_karcis_hilang.numeric' => 'Denda karcis hilang harus berupa angka',
            'denda_karcis_hilang.min' => 'Denda karcis hilang minimal 0',
        ]);

        Pengaturan::setValue('denda_karcis_hilang', $this->denda_karcis_hilang);

        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => "Mengubah denda karcis hilang menjadi Rp " . number_format((int) $this->denda_karcis_hilang, 0, ',', '.'),
            'waktu_aktivitas' => now(),
        ]);

        $this->dispatch('toast', type: 'success', message: 'Denda karcis hilang berhasil diperbarui!');
    }

    public function delete($id)
    {
        $tarif = Tarif::findOrFail($id);
        $jenisTerhapus = strtolower($tarif->jenis_kendaraan);
        
        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => "Menghapus tarif {$tarif->jenis_kendaraan}",
            'waktu_aktivitas' => now(),
        ]);
        
        // Membersihkan tipe_kendaraan di Area Parkir yang mengandung jenis kendaraan ini
        $areas = \App\Models\AreaParkir::all();
        foreach ($areas as $area) {
            $types = array_map('trim', explode(',', $area->tipe_kendaraan));
            if (in_array($jenisTerhapus, $types)) {
                $types = array_filter($types, fn($t) => $t !== $jenisTerhapus);
                // Jika kosong, jadikan 'semua' sebagai default aman
                if (empty($types)) {
                    $types = ['semua'];
                }
                $area->update([
                    'tipe_kendaraan' => implode(', ', $types)
                ]);
            }
        }

        $tarif->delete();
        $this->dispatch('toast', type: 'success', message: 'Tarif berhasil dihapus!');
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

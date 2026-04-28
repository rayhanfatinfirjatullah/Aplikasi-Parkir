<?php

namespace App\Livewire\Admin;

use App\Models\Tarif;
use App\Models\StatusKendaraan;
use App\Models\Pengaturan;
use App\Models\LogAktivitas as LogModel;
use Livewire\Component;
use Livewire\WithPagination;

class TarifManagement extends Component
{
    use WithPagination;

    public string $activeTab = 'tarif';

    public string $search = '';
    public string $searchStatus = '';

    // -- Tarif Properties --
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $editId = null;
    public string $jenis_kendaraan = '';
    public string $tarif_per_jam = '';

    // -- Status Properties --
    public bool $showStatusModal = false;
    public bool $isEditStatus = false;
    public ?int $editStatusId = null;
    public string $status_nama = '';
    public string $status_label = '';
    public string $status_deskripsi = '';
    public string $status_metode = 'reguler';
    public string $status_nominal = '0';
    public bool $status_bebas_denda = false;
    public int $status_prioritas = 1;
    public string $status_warna = 'slate';

    // -- Global Settings --
    public string $denda_karcis_hilang = '';

    public function mount()
    {
        $this->denda_karcis_hilang = Pengaturan::getValue('denda_karcis_hilang', '20000');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingSearchStatus() { $this->resetPage(); }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // ==========================================
    // CRUD TARIF PARKIR
    // ==========================================

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

    public function delete($id)
    {
        $tarif = Tarif::findOrFail($id);
        $jenisTerhapus = strtolower($tarif->jenis_kendaraan);
        
        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => "Menghapus tarif {$tarif->jenis_kendaraan}",
            'waktu_aktivitas' => now(),
        ]);
        
        $areas = \App\Models\AreaParkir::all();
        foreach ($areas as $area) {
            $types = array_map('trim', explode(',', $area->tipe_kendaraan));
            if (in_array($jenisTerhapus, $types)) {
                $types = array_filter($types, fn($t) => $t !== $jenisTerhapus);
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

    // ==========================================
    // PENGATURAN DENDA
    // ==========================================

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

    // ==========================================
    // CRUD STATUS KENDARAAN
    // ==========================================

    public function openCreateStatus()
    {
        $this->reset(['editStatusId', 'isEditStatus', 'status_nama', 'status_label', 'status_deskripsi', 'status_metode', 'status_nominal', 'status_bebas_denda', 'status_prioritas', 'status_warna']);
        $this->status_metode = 'reguler';
        $this->status_nominal = '0';
        $this->status_prioritas = 1;
        $this->status_warna = 'slate';
        $this->showStatusModal = true;
    }

    public function openEditStatus($id)
    {
        $status = StatusKendaraan::findOrFail($id);
        $this->editStatusId = $id;
        $this->isEditStatus = true;
        
        $this->status_nama = $status->nama_status;
        $this->status_label = $status->label_status;
        $this->status_deskripsi = $status->deskripsi ?? '';
        $this->status_metode = $status->metode_tarif;
        $this->status_nominal = $status->nominal_tarif;
        $this->status_bebas_denda = $status->is_bebas_denda;
        $this->status_prioritas = $status->prioritas_level;
        $this->status_warna = $status->warna_badge;
        
        $this->showStatusModal = true;
    }

    public function closeStatusModal()
    {
        $this->reset(['editStatusId', 'isEditStatus', 'status_nama', 'status_label', 'status_deskripsi', 'status_metode', 'status_nominal', 'status_bebas_denda', 'status_prioritas', 'status_warna']);
        $this->resetValidation();
        $this->showStatusModal = false;
    }

    public function saveStatus()
    {
        $this->validate([
            'status_label' => 'required|string|max:50',
            'status_deskripsi' => 'nullable|string|max:255',
            'status_metode' => 'required|in:reguler,fix_per_hari,membership',
            'status_nominal' => 'required_unless:status_metode,reguler|numeric|min:0',
            'status_prioritas' => 'required|integer|min:1',
            'status_warna' => 'required|string|max:20',
        ], [], [
            'status_label' => 'Label Status',
            'status_deskripsi' => 'Deskripsi',
            'status_metode' => 'Metode Tarif',
            'status_nominal' => 'Nominal Tarif',
            'status_prioritas' => 'Prioritas Level',
            'status_warna' => 'Warna Badge',
        ]);

        $namaStatus = strtolower(str_replace(' ', '_', $this->status_label));

        if (!$this->isEditStatus) {
            $this->validate([
                'status_label' => 'unique:tb_status_kendaraan,nama_status',
            ], [
                'status_label.unique' => 'Status ini sudah ada, gunakan nama lain.'
            ]);
        }

        $data = [
            'label_status' => $this->status_label,
            'deskripsi' => $this->status_deskripsi,
            'metode_tarif' => $this->status_metode,
            'nominal_tarif' => $this->status_metode === 'reguler' ? 0 : $this->status_nominal,
            'is_bebas_denda' => $this->status_bebas_denda ? 1 : 0,
            'prioritas_level' => $this->status_prioritas,
            'warna_badge' => $this->status_warna,
        ];

        if ($this->isEditStatus) {
            StatusKendaraan::where('id_status', $this->editStatusId)->update($data);
            $aktivitas = "Mengubah status spesifik {$this->status_label}";
        } else {
            $data['nama_status'] = $namaStatus;
            StatusKendaraan::create($data);
            $aktivitas = "Menambah status spesifik {$this->status_label}";
        }

        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => $aktivitas,
            'waktu_aktivitas' => now(),
        ]);

        $this->showStatusModal = false;
        $this->dispatch('toast', type: 'success', message: 'Status berhasil disimpan!');
    }

    public function deleteStatus($id)
    {
        $status = StatusKendaraan::findOrFail($id);
        
        // Prevent deleting default critical ones if we want to, but assuming admin knows what they do.
        if (in_array($status->nama_status, ['reguler'])) {
            $this->dispatch('toast', type: 'error', message: 'Status Reguler tidak boleh dihapus!');
            return;
        }

        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => "Menghapus status {$status->label_status}",
            'waktu_aktivitas' => now(),
        ]);
        
        $status->delete();
        $this->dispatch('toast', type: 'success', message: 'Status berhasil dihapus!');
    }

    public function render()
    {
        $tarifs = Tarif::where('jenis_kendaraan', 'like', "%{$this->search}%")
            ->orderBy('id_tarif', 'desc')
            ->paginate(10);
            
        $statuses = StatusKendaraan::where('label_status', 'like', "%{$this->searchStatus}%")
            ->orderBy('prioritas_level', 'asc')
            ->paginate(10, ['*'], 'statusPage');

        return view('livewire.admin.tarif-management', compact('tarifs', 'statuses'))
            ->layout('layouts.app');
    }
}

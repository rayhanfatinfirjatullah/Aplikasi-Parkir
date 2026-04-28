<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\LogAktivitas as LogModel;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class UserManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $editId = null;

    public string $nama_lengkap = '';
    public string $username = '';
    public string $password = '';
    public string $role = 'petugas';
    public int $status_aktif = 1;

    protected function rules()
    {
        $uniqueRule = $this->isEdit ? 'unique:tb_user,username,' . $this->editId . ',id_user' : 'unique:tb_user,username';
        return [
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:255|' . $uniqueRule,
            'password' => $this->isEdit ? 'nullable|string|min:6' : 'required|string|min:6',
            'role' => 'required|in:superadmin,admin,petugas,owner',
            'status_aktif' => 'required|in:0,1',
        ];
    }

    protected array $messages = [
        'nama_lengkap.required' => 'Nama lengkap wajib diisi',
        'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter',
        'username.required' => 'Username wajib diisi',
        'username.max' => 'Username maksimal 255 karakter',
        'username.unique' => 'Username sudah terdaftar',
        'password.required' => 'Password wajib diisi',
        'password.min' => 'Password minimal 6 karakter',
        'role.required' => 'Role wajib dipilih',
        'role.in' => 'Role tidak valid',
        'status_aktif.required' => 'Status aktif wajib dipilih',
        'status_aktif.in' => 'Status aktif tidak valid',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->reset(['nama_lengkap', 'username', 'password', 'role', 'status_aktif', 'editId', 'isEdit']);
        $this->role = 'petugas';
        $this->status_aktif = 1;
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $user = User::findOrFail($id);
        $this->editId = $id;
        $this->isEdit = true;
        $this->nama_lengkap = $user->nama_lengkap;
        $this->username = $user->username;
        $this->password = '';
        $this->role = $user->role;
        $this->status_aktif = $user->status_aktif;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset(['nama_lengkap', 'username', 'password', 'role', 'status_aktif', 'editId', 'isEdit']);
        $this->resetValidation();
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nama_lengkap' => $this->nama_lengkap,
            'username' => $this->username,
            'role' => $this->role,
            'status_aktif' => $this->status_aktif,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEdit) {
            User::where('id_user', $this->editId)->update($data);
            $aktivitas = "Mengubah data user: {$this->username}";
        } else {
            User::create($data);
            $aktivitas = "Menambah user baru: {$this->username}";
        }

        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => $aktivitas,
            'waktu_aktivitas' => now(),
        ]);

        $message = $this->isEdit ? 'User berhasil diperbarui!' : 'User berhasil ditambahkan!';
        $this->showModal = false;
        $this->reset(['nama_lengkap', 'username', 'password', 'role', 'status_aktif', 'editId', 'isEdit']);
        $this->dispatch('toast', type: 'success', message: $message);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);

        if ($user->username === 'admin') {
            $this->dispatch('toast', type: 'error', message: 'Admin utama tidak dapat dihapus!');
            return;
        }

        $username = $user->username;
        $user->delete();

        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => "Menghapus user: {$username}",
            'waktu_aktivitas' => now(),
        ]);

        $this->dispatch('toast', type: 'success', message: 'User berhasil dihapus!');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status_aktif = !$user->status_aktif;
        $user->save();

        LogModel::create([
            'id_user' => auth()->user()->id_user,
            'aktivitas' => "Mengubah status user {$user->username} menjadi " . ($user->status_aktif ? 'aktif' : 'nonaktif'),
            'waktu_aktivitas' => now(),
        ]);
    }

    public function render()
    {
        $users = User::where(function ($q) {
            $q->where('nama_lengkap', 'like', "%{$this->search}%")
              ->orWhere('username', 'like', "%{$this->search}%");
        })->orderBy('id_user', 'desc')->paginate(10);

        return view('livewire.admin.user-management', compact('users'))
            ->layout('layouts.app');
    }
}

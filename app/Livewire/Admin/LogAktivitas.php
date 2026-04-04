<?php

namespace App\Livewire\Admin;

use App\Models\LogAktivitas as LogModel;
use Livewire\Component;
use Livewire\WithPagination;

class LogAktivitas extends Component
{
    use WithPagination;

    public string $search = '';
    public string $tanggal = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = LogModel::with('user')
            ->when($this->search, function ($q) {
                $q->where('aktivitas', 'like', "%{$this->search}%")
                  ->orWhereHas('user', function ($q2) {
                      $q2->where('nama_lengkap', 'like', "%{$this->search}%");
                  });
            })
            ->when($this->tanggal, function ($q) {
                $q->whereDate('waktu_aktivitas', $this->tanggal);
            })
            ->orderBy('waktu_aktivitas', 'desc')
            ->paginate(15);

        return view('livewire.admin.log-aktivitas', compact('logs'))
            ->layout('layouts.app');
    }
}

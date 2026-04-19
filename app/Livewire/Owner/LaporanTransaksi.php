<?php

namespace App\Livewire\Owner;

use App\Models\Transaksi;
use Livewire\Component;
use Livewire\WithPagination;

class LaporanTransaksi extends Component
{
    use WithPagination;

    public string $tanggal_mulai = '';
    public string $tanggal_selesai = '';
    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function filter()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->reset(['tanggal_mulai', 'tanggal_selesai', 'search']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Transaksi::with(['kendaraan', 'tarif', 'areaParkir', 'user']);

        if ($this->tanggal_mulai) {
            $query->whereDate('waktu_masuk', '>=', $this->tanggal_mulai);
        }
        if ($this->tanggal_selesai) {
            $query->whereDate('waktu_masuk', '<=', $this->tanggal_selesai);
        }
        if ($this->search) {
            $query->where(function($q) {
                $q->where('plat_nomor', 'like', "%{$this->search}%")
                  ->orWhere('pemilik', 'like', "%{$this->search}%");
            });
        }

        $totalTransaksi = (clone $query)->count();
        $totalPendapatan = (clone $query)->where('status', 'keluar')->sum('biaya_total');

        $transaksis = $query->orderBy('waktu_masuk', 'desc')->paginate(15);

        return view('livewire.owner.laporan-transaksi', compact('transaksis', 'totalTransaksi', 'totalPendapatan'))
            ->layout('layouts.app');
    }
}

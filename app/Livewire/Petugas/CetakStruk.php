<?php

namespace App\Livewire\Petugas;

use App\Models\Transaksi;
use Livewire\Component;

class CetakStruk extends Component
{
    public $transaksi;

    public function mount($id)
    {
        $this->transaksi = Transaksi::with(['kendaraan', 'tarif', 'areaParkir', 'user'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.petugas.cetak-struk')
            ->layout('layouts.guest');
    }
}

<?php

namespace App\Livewire\Petugas;

use App\Models\Transaksi;
use Livewire\Component;

class CetakKarcis extends Component
{
    public $transaksi;

    public function mount($id)
    {
        $this->transaksi = Transaksi::with(['kendaraan', 'tarif', 'areaParkir', 'user'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.petugas.cetak-karcis')
            ->layout('layouts.guest');
    }
}

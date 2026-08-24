<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kendaraan extends Model
{
    protected $table = 'tb_kendaraan';
    protected $primaryKey = 'id_kendaraan';

    protected $fillable = [
        'plat_nomor',
        'warna',
        'pemilik',
        'status_spesial',
        'jenis_kendaraan',
        'masa_aktif_hingga',
    ];

    protected function casts(): array
    {
        return [
            'masa_aktif_hingga' => 'date',
        ];
    }

    /**
     * Get the billing setup for this vehicle based on its dynamic status.
     */
    public function getStatusModel()
    {
        return \App\Models\StatusKendaraan::where('nama_status', $this->status_spesial)->first();
    }

    public function isMembershipActive(): bool
    {
        if (!$this->masa_aktif_hingga) return true; // Treat as lifetime/always active if not set
        return $this->masa_aktif_hingga->endOfDay()->isFuture() || $this->masa_aktif_hingga->isToday();
    }

    /**
     * Check if vehicle is immune to denda based on dynamic status.
     */
    public function isImmuneDenda(): bool
    {
        $statusModel = \App\Models\StatusKendaraan::where('nama_status', $this->status_spesial)->first();
        return $statusModel ? $statusModel->is_bebas_denda : false;
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_kendaraan', 'id_kendaraan');
    }
}

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
    ];

    /**
     * Check if vehicle is VIP or VVIP (free parking).
     */
    public function isFreeParkir(): bool
    {
        return in_array($this->status_spesial, ['vip', 'vvip']);
    }

    /**
     * Check if vehicle is immune to denda (VVIP only).
     */
    public function isImmuneDenda(): bool
    {
        return $this->status_spesial === 'vvip';
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_kendaraan', 'id_kendaraan');
    }
}

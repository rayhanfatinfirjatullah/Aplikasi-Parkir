<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusKendaraan extends Model
{
    protected $table = 'tb_status_kendaraan';
    protected $primaryKey = 'id_status';

    protected $fillable = [
        'nama_status',
        'label_status',
        'deskripsi',
        'metode_tarif',
        'nominal_tarif',
        'is_bebas_denda',
        'prioritas_level',
        'warna_badge',
    ];

    protected function casts(): array
    {
        return [
            'nominal_tarif' => 'decimal:2',
            'is_bebas_denda' => 'boolean',
            'prioritas_level' => 'integer',
        ];
    }
}

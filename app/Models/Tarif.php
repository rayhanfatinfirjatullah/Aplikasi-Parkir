<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarif extends Model
{
    protected $table = 'tb_tarif';
    protected $primaryKey = 'id_tarif';

    protected $fillable = [
        'jenis_kendaraan',
        'tarif_jam_pertama',
        'tarif_jam_berikutnya',
    ];

    protected function casts(): array
    {
        return [
            'tarif_jam_pertama' => 'decimal:2',
            'tarif_jam_berikutnya' => 'decimal:2',
        ];
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_tarif', 'id_tarif');
    }
}

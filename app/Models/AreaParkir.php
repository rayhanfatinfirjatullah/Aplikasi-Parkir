<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AreaParkir extends Model
{
    protected $table = 'tb_area_parkir';
    protected $primaryKey = 'id_area';

    protected $fillable = [
        'nama_area',
        'kapasitas',
        'terisi',
        'tipe_kendaraan',
        'level_akses',
    ];

    protected function casts(): array
    {
        return [
            'kapasitas' => 'integer',
            'terisi' => 'integer',
        ];
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_area', 'id_area');
    }

    public function getSisaKapasitasAttribute(): int
    {
        return $this->kapasitas - $this->terisi;
    }

    public function isFull(): bool
    {
        return $this->terisi >= $this->kapasitas;
    }

    /**
     * Mengecek apakah jenis kendaraan cocok dengan tipe area
     */
    public function canAcceptVehicleType(string $jenisKendaraan): bool
    {
        if ($this->tipe_kendaraan === 'semua') return true;
        // Hanya membolehkan yang tepat eksak (motor untuk motor, mobil untuk mobil)
        return $this->tipe_kendaraan === strtolower($jenisKendaraan);
    }

    /**
     * Mengecek apakah status spesial (privilage) bisa mengakses area ini
     */
    public function canAccessByPrivilege(string $statusSpesial): bool
    {
        $statusSpesial = strtolower($statusSpesial);
        // Mapping hirarki akses
        $levelValue = ['reguler' => 1, 'vip' => 2, 'vvip' => 3];
        
        $kendaraanLevel = $levelValue[$statusSpesial] ?? 1;
        $areaLevel = $levelValue[$this->level_akses] ?? 1;

        return $kendaraanLevel >= $areaLevel;
    }
}

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
        $allowedTypes = array_map('trim', explode(',', strtolower($this->tipe_kendaraan)));
        if (in_array('semua', $allowedTypes)) return true;
        return in_array(strtolower($jenisKendaraan), $allowedTypes);
    }

    /**
     * Mengecek apakah status spesial (privilage) bisa mengakses area ini
     */
    public function canAccessByPrivilege(string $statusSpesial): bool
    {
        $statusSpesial = strtolower($statusSpesial);
        
        $areaLevelModel = \App\Models\StatusKendaraan::where('nama_status', $this->level_akses)->first();
        $areaLevel = $areaLevelModel ? $areaLevelModel->prioritas_level : 1;

        $kendaraanLevelModel = \App\Models\StatusKendaraan::where('nama_status', $statusSpesial)->first();
        $kendaraanLevel = $kendaraanLevelModel ? $kendaraanLevelModel->prioritas_level : 1;

        return $kendaraanLevel >= $areaLevel;
    }

    public function getFormatTipeKendaraanAttribute(): string
    {
        $types = array_map('trim', explode(',', strtolower($this->tipe_kendaraan)));
        if (in_array('semua', $types)) {
            return 'Semua Jenis';
        }

        $validTarifs = \App\Models\Tarif::pluck('jenis_kendaraan')->map(fn($item) => strtolower($item))->toArray();
        $filtered = array_intersect($types, $validTarifs);

        if (empty($filtered)) {
            return 'Tidak Ada / Invalid';
        }

        return implode(', ', array_map('ucfirst', $filtered));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'tb_pengaturan';

    protected $fillable = [
        'nama_pengaturan',
        'nilai_pengaturan',
    ];

    /**
     * Get a setting value by name.
     */
    public static function getValue(string $nama, $default = null): ?string
    {
        $pengaturan = static::where('nama_pengaturan', $nama)->first();
        return $pengaturan ? $pengaturan->nilai_pengaturan : $default;
    }

    /**
     * Set a setting value by name (create or update).
     */
    public static function setValue(string $nama, string $nilai): void
    {
        static::updateOrCreate(
            ['nama_pengaturan' => $nama],
            ['nilai_pengaturan' => $nilai]
        );
    }

    /**
     * Get denda karcis hilang value as integer.
     */
    public static function getDendaKarcisHilang(): int
    {
        return (int) static::getValue('denda_karcis_hilang', '20000');
    }
}

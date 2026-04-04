<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tarif;
use App\Models\AreaParkir;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Users with explicit Hash::make()
        User::create([
            'nama_lengkap' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status_aktif' => 1,
        ]);

        User::create([
            'nama_lengkap' => 'Petugas Parkir',
            'username' => 'petugas',
            'password' => Hash::make('petugas123'),
            'role' => 'petugas',
            'status_aktif' => 1,
        ]);

        User::create([
            'nama_lengkap' => 'Owner Parkir',
            'username' => 'owner',
            'password' => Hash::make('owner123'),
            'role' => 'owner',
            'status_aktif' => 1,
        ]);

        // Seed Tarif
        Tarif::create([
            'jenis_kendaraan' => 'motor',
            'tarif_per_jam' => 2000,
        ]);

        Tarif::create([
            'jenis_kendaraan' => 'mobil',
            'tarif_per_jam' => 5000,
        ]);

        Tarif::create([
            'jenis_kendaraan' => 'lainnya',
            'tarif_per_jam' => 3000,
        ]);

        // Seed Area Parkir
        AreaParkir::create([
            'nama_area' => 'Area A',
            'kapasitas' => 50,
            'terisi' => 0,
        ]);

        AreaParkir::create([
            'nama_area' => 'Area B',
            'kapasitas' => 30,
            'terisi' => 0,
        ]);

        AreaParkir::create([
            'nama_area' => 'Area C',
            'kapasitas' => 20,
            'terisi' => 0,
        ]);
    }
}

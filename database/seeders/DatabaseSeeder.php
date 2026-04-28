<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tarif;
use App\Models\AreaParkir;
use App\Models\Pengaturan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Users with explicit Hash::make()
        User::create([
            'nama_lengkap' => 'Super Administrator',
            'username' => 'superadmin',
            'password' => Hash::make('superadmin123'),
            'role' => 'superadmin',
            'status_aktif' => 1,
        ]);

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
            'tarif_per_jam' => 7000,
        ]);

        // Seed Area Parkir
        AreaParkir::create([
            'nama_area' => 'Parkir Motor',
            'kapasitas' => 150,
            'terisi' => 0,
            'tipe_kendaraan' => 'motor',
            'level_akses' => 'reguler',
        ]);

        AreaParkir::create([
            'nama_area' => 'Parkir Mobil',
            'kapasitas' => 75,
            'terisi' => 0,
            'tipe_kendaraan' => 'mobil',
            'level_akses' => 'reguler',
        ]);

        AreaParkir::create([
            'nama_area' => 'Logistik',
            'kapasitas' => 25,
            'terisi' => 0,
            'tipe_kendaraan' => 'lainnya',
            'level_akses' => 'reguler',
        ]);

        AreaParkir::create([
            'nama_area' => 'Staff Lane (VIP)',
            'kapasitas' => 50,
            'terisi' => 0,
            'tipe_kendaraan' => 'semua',
            'level_akses' => 'vip',
        ]);

        AreaParkir::create([
            'nama_area' => 'Executive Prime (VVIP)',
            'kapasitas' => 10,
            'terisi' => 0,
            'tipe_kendaraan' => 'semua',
            'level_akses' => 'vvip',
        ]);

        // Seed Pengaturan
        Pengaturan::create([
            'nama_pengaturan' => 'denda_karcis_hilang',
            'nilai_pengaturan' => '20000',
        ]);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_status_kendaraan', function (Blueprint $table) {
            $table->enum('metode_tarif', ['reguler', 'fix_per_hari', 'membership'])->default('reguler')->after('deskripsi');
            $table->decimal('nominal_tarif', 12, 2)->default(0)->after('metode_tarif');
            $table->dropColumn('is_gratis_parkir');
        });
        
        // Update data statis VVIP menjadi membership (contoh)
        \Illuminate\Support\Facades\DB::table('tb_status_kendaraan')->where('nama_status', 'vvip')->update([
            'metode_tarif' => 'membership',
            'nominal_tarif' => 150000,
            'label_status' => 'Member VVIP'
        ]);

        // Update data statis VIP menjadi pegawai / harian (contoh)
        \Illuminate\Support\Facades\DB::table('tb_status_kendaraan')->where('nama_status', 'vip')->update([
            'metode_tarif' => 'fix_per_hari',
            'nominal_tarif' => 3000,
            'label_status' => 'Pegawai'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_status_kendaraan', function (Blueprint $table) {
            $table->dropColumn('metode_tarif');
            $table->dropColumn('nominal_tarif');
            $table->boolean('is_gratis_parkir')->default(false)->after('deskripsi');
        });
    }
};

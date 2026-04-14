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
        Schema::table('tb_area_parkir', function (Blueprint $table) {
            $table->string('tipe_kendaraan')->default('semua')->after('terisi')->comment('motor, mobil, semua');
            $table->string('level_akses')->default('reguler')->after('tipe_kendaraan')->comment('reguler, vip, vvip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_area_parkir', function (Blueprint $table) {
            $table->dropColumn(['tipe_kendaraan', 'level_akses']);
        });
    }
};

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
        Schema::table('tb_kendaraan', function (Blueprint $table) {
            $table->date('masa_aktif_hingga')->nullable()->after('jenis_kendaraan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_kendaraan', function (Blueprint $table) {
            $table->dropColumn('masa_aktif_hingga');
        });
    }
};

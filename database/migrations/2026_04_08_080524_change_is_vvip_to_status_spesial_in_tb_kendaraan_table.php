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
            if (Schema::hasColumn('tb_kendaraan', 'is_vvip')) {
                $table->dropColumn('is_vvip');
            }
            $table->string('status_spesial')->default('reguler')->after('pemilik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_kendaraan', function (Blueprint $table) {
            $table->dropColumn('status_spesial');
            $table->boolean('is_vvip')->default(false)->after('pemilik');
        });
    }
};

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
            $table->string('jenis_kendaraan')->default('mobil')->after('status_spesial');
        });

        Schema::table('tb_transaksi', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['id_kendaraan']);
            
            // Modify column and add new ones
            $table->unsignedBigInteger('id_kendaraan')->nullable()->change();
            $table->string('plat_nomor')->nullable()->after('id_kendaraan');
            $table->string('warna')->nullable()->after('plat_nomor');
            $table->string('pemilik')->nullable()->after('warna');
            $table->string('status_spesial')->default('reguler')->after('pemilik');

            // Re-add foreign key constraint with cascade on delete (but now optional)
            $table->foreign('id_kendaraan')->references('id_kendaraan')->on('tb_kendaraan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropForeign(['id_kendaraan']);
            
            $table->dropColumn(['plat_nomor', 'warna', 'pemilik', 'status_spesial']);
            $table->unsignedBigInteger('id_kendaraan')->nullable(false)->change();
            
            $table->foreign('id_kendaraan')->references('id_kendaraan')->on('tb_kendaraan')->onDelete('cascade');
        });

        Schema::table('tb_kendaraan', function (Blueprint $table) {
            $table->dropColumn('jenis_kendaraan');
        });
    }
};

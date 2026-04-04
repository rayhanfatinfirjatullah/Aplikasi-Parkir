<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_transaksi', function (Blueprint $table) {
            $table->id('id_parkir');
            $table->unsignedBigInteger('id_kendaraan');
            $table->unsignedBigInteger('id_tarif');
            $table->unsignedBigInteger('id_area');
            $table->unsignedBigInteger('id_user');
            $table->datetime('waktu_masuk');
            $table->datetime('waktu_keluar')->nullable();
            $table->integer('durasi_jam')->nullable();
            $table->decimal('biaya_total', 10, 2)->nullable();
            $table->enum('status', ['masuk', 'keluar'])->default('masuk');
            $table->timestamps();

            $table->foreign('id_kendaraan')->references('id_kendaraan')->on('tb_kendaraan')->onDelete('cascade');
            $table->foreign('id_tarif')->references('id_tarif')->on('tb_tarif')->onDelete('cascade');
            $table->foreign('id_area')->references('id_area')->on('tb_area_parkir')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_transaksi');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_status_kendaraan', function (Blueprint $table) {
            $table->id('id_status');
            $table->string('nama_status')->unique();
            $table->string('label_status');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_gratis_parkir')->default(false);
            $table->boolean('is_bebas_denda')->default(false);
            $table->integer('prioritas_level')->default(1);
            $table->string('warna_badge')->default('slate');
            $table->timestamps();
        });

        // Insert default statuses
        DB::table('tb_status_kendaraan')->insert([
            [
                'nama_status' => 'reguler',
                'label_status' => 'Reguler',
                'deskripsi' => 'Tarif Normal',
                'is_gratis_parkir' => false,
                'is_bebas_denda' => false,
                'prioritas_level' => 1,
                'warna_badge' => 'slate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_status' => 'vip',
                'label_status' => 'VIP',
                'deskripsi' => 'Gratis, Denda Tetap',
                'is_gratis_parkir' => true,
                'is_bebas_denda' => false,
                'prioritas_level' => 2,
                'warna_badge' => 'blue',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_status' => 'vvip',
                'label_status' => 'VVIP',
                'deskripsi' => 'Gratis Total 100%',
                'is_gratis_parkir' => true,
                'is_bebas_denda' => true,
                'prioritas_level' => 3,
                'warna_badge' => 'cyan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_status_kendaraan');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Mengubah sistem tarif dari flat-rate (tarif_per_jam) menjadi
     * tarif progresif (tarif_jam_pertama + tarif_jam_berikutnya).
     */
    public function up(): void
    {
        // 1. Rename tarif_per_jam → tarif_jam_pertama
        Schema::table('tb_tarif', function (Blueprint $table) {
            $table->renameColumn('tarif_per_jam', 'tarif_jam_pertama');
        });

        // 2. Tambah kolom tarif_jam_berikutnya
        Schema::table('tb_tarif', function (Blueprint $table) {
            $table->decimal('tarif_jam_berikutnya', 12, 2)->default(0)->after('tarif_jam_pertama');
        });

        // 3. Set default tarif_jam_berikutnya = 50% dari tarif_jam_pertama
        DB::table('tb_tarif')->get()->each(function ($tarif) {
            DB::table('tb_tarif')
                ->where('id_tarif', $tarif->id_tarif)
                ->update(['tarif_jam_berikutnya' => round($tarif->tarif_jam_pertama * 0.5)]);
        });

        // 4. Tambah kolom biaya_parkir pada tb_transaksi (jika belum ada)
        if (!Schema::hasColumn('tb_transaksi', 'biaya_parkir')) {
            Schema::table('tb_transaksi', function (Blueprint $table) {
                $table->decimal('biaya_parkir', 12, 2)->default(0)->after('biaya_total');
            });
        }

        // 5. Seed pengaturan grace period & tarif setengah
        DB::table('tb_pengaturan')->insertOrIgnore([
            [
                'nama_pengaturan' => 'menit_grace_period',
                'nilai_pengaturan' => '10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pengaturan' => 'menit_tarif_setengah',
                'nilai_pengaturan' => '30',
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
        // Remove pengaturan entries
        DB::table('tb_pengaturan')
            ->whereIn('nama_pengaturan', ['menit_grace_period', 'menit_tarif_setengah'])
            ->delete();

        // Drop biaya_parkir if we added it
        if (Schema::hasColumn('tb_transaksi', 'biaya_parkir')) {
            Schema::table('tb_transaksi', function (Blueprint $table) {
                $table->dropColumn('biaya_parkir');
            });
        }

        // Drop tarif_jam_berikutnya
        Schema::table('tb_tarif', function (Blueprint $table) {
            $table->dropColumn('tarif_jam_berikutnya');
        });

        // Rename back
        Schema::table('tb_tarif', function (Blueprint $table) {
            $table->renameColumn('tarif_jam_pertama', 'tarif_per_jam');
        });
    }
};

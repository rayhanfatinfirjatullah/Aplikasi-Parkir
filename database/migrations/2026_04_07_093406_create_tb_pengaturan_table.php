<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengaturan')->unique();
            $table->string('nilai_pengaturan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_pengaturan');
    }
};

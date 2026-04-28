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
        DB::statement("ALTER TABLE tb_user MODIFY COLUMN role ENUM('superadmin', 'admin', 'petugas', 'owner') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE tb_user MODIFY COLUMN role ENUM('admin', 'petugas', 'owner') NOT NULL");
    }
};

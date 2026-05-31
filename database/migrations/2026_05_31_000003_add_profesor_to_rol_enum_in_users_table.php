<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY rol ENUM('desarrollador','administracion','socio','profesor') NOT NULL DEFAULT 'administracion'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY rol ENUM('desarrollador','administracion','socio') NOT NULL DEFAULT 'administracion'");
    }
};

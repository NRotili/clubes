<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disciplina_socio', function (Blueprint $table) {
            $table->boolean('beca')->default(false)->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('disciplina_socio', function (Blueprint $table) {
            $table->dropColumn('beca');
        });
    }
};

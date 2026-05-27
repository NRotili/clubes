<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('rol', ['desarrollador', 'administracion', 'socio'])
                ->default('administracion')
                ->after('email');
            $table->foreignId('socio_id')
                ->nullable()
                ->constrained('socios')
                ->nullOnDelete()
                ->after('rol');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['socio_id']);
            $table->dropColumn(['rol', 'socio_id']);
        });
    }
};

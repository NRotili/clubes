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
        Schema::create('cuota_configs', function (Blueprint $table) {
            $table->id();
            $table->string('categoria', 20); // adulto, junior, cadete, bebe, jubilado
            $table->string('genero', 5);     // M, F, X
            $table->decimal('monto', 10, 2)->default(0);
            $table->unique(['categoria', 'genero']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuota_configs');
    }
};

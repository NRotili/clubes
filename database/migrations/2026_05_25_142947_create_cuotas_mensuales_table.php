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
        Schema::create('cuotas_mensuales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socio_id')->constrained('socios')->cascadeOnDelete();
            $table->string('periodo', 7);          // YYYY-MM
            $table->json('items');                 // [{tipo, descripcion, monto}]
            $table->decimal('monto_total', 10, 2);
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->string('estado', 20)->default('pendiente'); // pendiente, parcial, pagado
            $table->unique(['socio_id', 'periodo']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuotas_mensuales');
    }
};

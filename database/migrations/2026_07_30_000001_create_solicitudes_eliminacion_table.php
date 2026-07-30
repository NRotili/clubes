<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_eliminacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('identificador', 150); // email, DNI o N° de socio, tal como lo ingresó el solicitante
            $table->text('motivo')->nullable();
            $table->enum('estado', ['pendiente', 'procesada'])->default('pendiente');
            $table->timestamp('procesada_en')->nullable();
            $table->foreignId('procesada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_eliminacion');
    }
};

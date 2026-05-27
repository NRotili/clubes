<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('socios', function (Blueprint $table) {
            $table->id();
            $table->string('numero_socio', 10)->unique();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->enum('tipo_documento', ['DNI', 'PASAPORTE', 'LC', 'LE', 'CI'])->default('DNI');
            $table->string('numero_documento', 20)->unique();
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['M', 'F', 'X']);
            $table->string('email', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('codigo_postal', 10)->nullable();
            $table->enum('categoria', ['adulto', 'junior', 'cadete', 'bebe', 'jubilado'])->default('adulto');
            $table->enum('estado', ['activo', 'inactivo', 'suspendido', 'pendiente'])->default('pendiente');
            $table->date('fecha_alta');
            $table->foreignId('socio_titular_id')->nullable()->constrained('socios')->nullOnDelete();
            $table->string('parentesco', 50)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('socios');
    }
};

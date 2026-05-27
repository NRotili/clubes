<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->string('asunto');
            $table->text('cuerpo');
            $table->string('tipo');           // individual | recordatorio | masiva
            $table->string('destinatario_tipo'); // socio | deudores | categoria | todos
            $table->string('filtro')->nullable(); // categoria o socio_id
            $table->unsignedInteger('enviados')->default(0);
            $table->unsignedInteger('fallidos')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicaciones');
    }
};

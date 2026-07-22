<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('turno_servicio', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->enum('estado', ['libre', 'reservado', 'confirmado', 'cancelado'])->default('libre');
            $table->foreignId('orden_servicio_id')->nullable()->constrained('ordenes_servicio')->nullOnDelete();
            $table->text('nota')->nullable();
            $table->timestamps();
            $table->unique(['fecha', 'hora_inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turnos_servicio');
    }
};

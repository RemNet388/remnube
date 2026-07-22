<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ordenes_servicio', function (Blueprint $table) {
            $table->id();

            // Relación con clientes (ajustar nombre de tabla si en tu app es distinto)
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();

            // Equipo (marca y modelo desde la base)
            $table->foreignId('marca_id')->constrained('marcas')->restrictOnDelete();
            $table->foreignId('modelo_id')->constrained('modelos')->restrictOnDelete();

            // Datos del equipo y del pedido de reparación
            $table->string('numero', 40)->unique(); // Nº de OT único (lo generamos en el modelo/controlador)
            $table->string('identificador')->nullable(); // Serie/IMEI/Patente/etc (opcional)
            $table->text('detalle_reparacion')->nullable(); // Lo que trae/describe el cliente
            $table->text('observaciones')->nullable(); // Notas internas del taller

            // Estado del flujo
            $table->enum('estado', ['pendiente', 'en_progreso', 'finalizada', 'rechazado'])->default('pendiente');

            // Presupuesto
            $table->decimal('presupuesto', 12, 2)->nullable();
            $table->boolean('presupuesto_aprobado')->default(false);
            $table->timestamp('presupuesto_aprobado_el')->nullable();

            // Fechas útiles (opcionales)
            $table->date('fecha_prometida')->nullable();
            $table->timestamp('finalizada_el')->nullable();
            $table->timestamp('entregada_el')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices para performance en listados/búsquedas
            $table->index(['cliente_id', 'estado']);
            $table->index('numero');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_servicio');
    }
};

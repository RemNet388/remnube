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
    Schema::create('cuentas_corrientes', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('entidad_id');     // id de cliente o proveedor
        $table->string('entidad_tipo');               // 'cliente' o 'proveedor'
        $table->date('fecha')->default(now());
        $table->string('concepto')->nullable();
        $table->decimal('debe', 15, 2)->default(0);   // cargos
        $table->decimal('haber', 15, 2)->default(0);  // abonos
        $table->decimal('saldo', 15, 2)->default(0);  // saldo acumulado
        $table->timestamps();

        $table->index(['entidad_id', 'entidad_tipo']); // búsquedas rápidas
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_corrientes');
    }
};

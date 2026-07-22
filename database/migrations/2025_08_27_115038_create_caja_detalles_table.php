<?php

// database/migrations/xxxx_xx_xx_create_caja_detalles_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('caja_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas')->onDelete('cascade');
            $table->foreignId('forma_pago_id')->constrained('formas_pago')->onDelete('cascade');
            $table->decimal('monto', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('caja_detalles');
    }
};

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
        Schema::create('retiros', function (Blueprint $table) {
   		 $table->id();
   		 $table->date('fecha'); // Día del retiro
  		  $table->decimal('monto', 12, 2); // Efectivo retirado
  		  $table->decimal('dejar_para_siguiente_caja', 12, 2)->default(0); // lo que queda en caja
   		 $table->timestamps();
	});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retiros');
    }
};

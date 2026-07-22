<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('ordenes_servicio', function (Blueprint $table) {
        $table->foreignId('turno_id')
              ->nullable()
              ->constrained('turno_servicio')
              ->nullOnDelete();
    });
}

public function down()
{
    Schema::table('ordenes_servicio', function (Blueprint $table) {
        $table->dropConstrainedForeignId('turno_id');
    });
}
};

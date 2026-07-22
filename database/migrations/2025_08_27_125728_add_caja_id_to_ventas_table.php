<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('ventas', function (Blueprint $table) {
        $table->foreignId('caja_id')->nullable()->constrained('cajas');
    });
}

public function down()
{
    Schema::table('ventas', function (Blueprint $table) {
        $table->dropForeign(['caja_id']);
        $table->dropColumn('caja_id');
    });
}

};

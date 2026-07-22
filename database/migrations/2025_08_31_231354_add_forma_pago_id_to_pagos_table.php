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
    Schema::table('pagos', function (Blueprint $table) {
        $table->unsignedBigInteger('forma_pago_id')->default(1)->after('monto'); // 1 = efectivo
        $table->foreign('forma_pago_id')->references('id')->on('formas_pago');
    });
}

public function down()
{
    Schema::table('pagos', function (Blueprint $table) {
        $table->dropForeign(['forma_pago_id']);
        $table->dropColumn('forma_pago_id');
    });
}

};

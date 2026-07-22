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
    Schema::table('compras', function (Blueprint $table) {
        $table->unsignedBigInteger('caja_id')->nullable()->after('id');
        $table->foreign('caja_id')->references('id')->on('cajas')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('compras', function (Blueprint $table) {
        $table->dropForeign(['caja_id']);
        $table->dropColumn('caja_id');
    });
}

};

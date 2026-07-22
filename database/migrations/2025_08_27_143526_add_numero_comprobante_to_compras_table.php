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
        $table->string('numero_comprobante')->nullable()->after('id'); // O después del campo que quieras
    });
}

public function down()
{
    Schema::table('compras', function (Blueprint $table) {
        $table->dropColumn('numero_comprobante');
    });
}

};

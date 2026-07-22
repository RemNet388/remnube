<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // SQLite no permite drop FK directo, así que recreamos la tabla
        //DB::statement('PRAGMA foreign_keys=off');
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('ALTER TABLE movimientos_caja RENAME TO old_movimientos_caja');

        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas')->cascadeOnDelete();
            $table->string('tipo');
            $table->string('concepto');
            $table->decimal('monto', 12, 2);
            $table->timestamps();
        });

        DB::statement('INSERT INTO movimientos_caja (id, caja_id, tipo, concepto, monto, created_at, updated_at)
                       SELECT id, caja_id, tipo, concepto, monto, created_at, updated_at FROM old_movimientos_caja');

        DB::statement('DROP TABLE old_movimientos_caja');

        //DB::statement('PRAGMA foreign_keys=on');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down()
    {
        // Opcional: revertir a la versión anterior apuntando a caja_diaria
    }
};

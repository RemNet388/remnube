<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiar el ENUM para agregar "rechazada"
        DB::statement("ALTER TABLE ordenes_servicio MODIFY estado ENUM('pendiente','en_progreso','finalizada','rechazada') NOT NULL DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        // Volver al estado anterior si se revierte
        DB::statement("ALTER TABLE ordenes_servicio MODIFY estado ENUM('pendiente','en_progreso','finalizada') NOT NULL DEFAULT 'pendiente'");
    }
};

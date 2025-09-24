<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Si tenías estados viejos, los mapeamos antes de cambiar el ENUM
        DB::table('quotations')
            ->whereIn('estado', ['abonado','pagado'])
            ->update(['estado' => 'pendiente']);

        // Cambiamos el ENUM a los definitivos
        DB::statement(
            "ALTER TABLE quotations 
             MODIFY COLUMN estado 
             ENUM('pendiente','aceptada','cancelada') 
             NOT NULL DEFAULT 'pendiente'"
        );
    }

    public function down(): void
    {
        // Volver al esquema anterior (ajústalo si lo necesitas)
        DB::statement(
            "ALTER TABLE quotations 
             MODIFY COLUMN estado 
             ENUM('pendiente','abonado','pagado') 
             NOT NULL DEFAULT 'pendiente'"
        );
    }
};

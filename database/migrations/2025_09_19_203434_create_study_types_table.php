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
    Schema::create('study_types', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique();        // ej: geo_obra_nueva
        $table->string('label');                // texto visible
        $table->text('description');            // plantilla de descripción
        $table->string('default_unit')->default('UND');
        $table->unsignedInteger('default_qty')->default(1);
        $table->unsignedBigInteger('default_unit_price')->default(0); // en COP
        $table->timestamps();
    });
}

};

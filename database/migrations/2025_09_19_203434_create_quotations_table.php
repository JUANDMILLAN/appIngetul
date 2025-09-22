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
    Schema::create('quotations', function (Blueprint $table) {
        $table->id();
        $table->date('fecha');
        $table->string('ciudad');
        $table->string('departamento');
        $table->string('dirigido_a');
        $table->text('objeto')->nullable();
        $table->json('notas')->nullable(); // guardamos arreglo de notas
        $table->timestamps();
    });
}

};

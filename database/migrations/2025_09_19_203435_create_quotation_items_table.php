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
    Schema::create('quotation_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
        $table->text('descripcion');
        $table->string('und')->default('UND');
        $table->unsignedInteger('cantidad')->default(1);
        $table->unsignedBigInteger('vr_unitario')->default(0);
        $table->unsignedBigInteger('vr_total')->default(0); // guardamos total fila
        $table->timestamps();
    });
}

};

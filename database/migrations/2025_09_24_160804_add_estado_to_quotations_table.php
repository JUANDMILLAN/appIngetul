<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'estado')) {
                $table->enum('estado', ['aceptada','pendiente','cancelada'])
                      ->default('pendiente')
                      ->after('notas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'referente')) {
                $table->string('referente', 150)->nullable()->after('dirigido_a');
                $table->index('referente');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'referente')) {
                $table->dropIndex(['referente']);
                $table->dropColumn('referente');
            }
        });
    }
};

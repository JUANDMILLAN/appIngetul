<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('quotations', 'consecutivo')) {
                $table->unsignedBigInteger('consecutivo')->after('user_id')->unique(); // consecutivo global
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'consecutivo')) {
                $table->dropUnique(['consecutivo']);
                $table->dropColumn('consecutivo');
            }
            if (Schema::hasColumn('quotations', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};

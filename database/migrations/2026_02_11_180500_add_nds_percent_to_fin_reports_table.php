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
        Schema::table('fin_reports', function (Blueprint $table) {
            $table->decimal('nds_percent', 5, 2)->nullable()->after('nds_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fin_reports', function (Blueprint $table) {
            $table->dropColumn('nds_percent');
        });
    }
};

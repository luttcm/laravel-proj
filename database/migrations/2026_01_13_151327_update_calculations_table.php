<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.s
     */
    public function up(): void
    {
        Schema::table('calculations', function (Blueprint $table) {
            $table->float('manager_payment')->default(0);
            $table->float('manager_salary_brutto')->default(0)->after('manager_payment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calculations', function (Blueprint $table) {
            $table->dropColumn('manager_payment');
            $table->dropColumn('manager_salary_brutto');
        });
    }
};

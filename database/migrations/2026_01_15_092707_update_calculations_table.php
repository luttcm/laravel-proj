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
        Schema::table('calculations', callback: function (Blueprint $table) {
            $table->float('in_the_hand')->default(0)->after('manager_salary_brutto');
            $table->float('in_the_deal')->default(0)->after('in_the_hand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calculations', function (Blueprint $table) {
            $table->dropColumn('in_the_hand');
            $table->dropColumn('in_the_deal');
        });
    }
};

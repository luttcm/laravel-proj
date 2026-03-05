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
            $table->decimal('logistics_bonus', 15, 2)->default(0)->after('markup');
            $table->decimal('fin_admin_bonus', 15, 2)->default(0)->after('logistics_bonus');
            $table->decimal('fbr_bonus', 15, 2)->default(0)->after('fin_admin_bonus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fin_reports', function (Blueprint $table) {
            $table->dropColumn(['logistics_bonus', 'fin_admin_bonus', 'fbr_bonus']);
        });
    }
};

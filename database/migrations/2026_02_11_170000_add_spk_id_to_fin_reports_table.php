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
        Schema::table('fin_reports', function (Blueprint $blueprint) {
            $blueprint->unsignedBigInteger('spk_id')->nullable()->after('order_number');
            $blueprint->foreign('spk_id')->references('id')->on('spks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fin_reports', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['spk_id']);
            $blueprint->dropColumn('spk_id');
        });
    }
};

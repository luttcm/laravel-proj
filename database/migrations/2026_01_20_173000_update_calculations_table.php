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
        Schema::table('calculations', function (Blueprint $table) {
            $table->decimal('in_the_hand_sum', 15, 2)->nullable()->after('in_the_deal');
            $table->decimal('in_the_deal_sum', 15, 2)->nullable()->after('in_the_hand_sum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('calculations', function (Blueprint $table) {
           $table->dropColumn('in_the_hand_sum');
           $table->dropColumn('in_the_deal_sum');
       });
    }
};

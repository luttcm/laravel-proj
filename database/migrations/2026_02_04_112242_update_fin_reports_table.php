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
            $table->string('customer')->nullable()->after('report_title');
            $table->string('order_number')->nullable()->after('customer');
            $table->string('spk')->nullable()->after('order_number');
            $table->integer('tz_count')->nullable()->after('spk');
            $table->integer('received_amount')->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fin_reports', function (Blueprint $table) {
            $table->dropColumn(['customer', 'order_number', 'spk', 'tz_count', 'received_amount']);
        });
    }
};

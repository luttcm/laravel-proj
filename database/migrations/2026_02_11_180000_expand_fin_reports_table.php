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
            $table->unsignedBigInteger('supplier_id')->nullable()->after('spk_id');
            $table->unsignedBigInteger('nds_id')->nullable()->after('supplier_id');
            $table->decimal('bonus_client', 15, 2)->nullable()->default(0)->after('nds_id');
            $table->decimal('net_sales', 15, 2)->nullable()->default(0)->after('bonus_client');
            $table->decimal('remainder', 15, 2)->nullable()->default(0)->after('net_sales');
            $table->string('manager_name')->nullable()->after('remainder');
            $table->string('supplier_invoice_number')->nullable()->after('manager_name');
            $table->decimal('supplier_amount', 15, 2)->nullable()->default(0)->after('supplier_invoice_number');
            $table->decimal('payment_manager', 15, 2)->nullable()->default(0)->after('supplier_amount');
            $table->decimal('payment_spk', 15, 2)->nullable()->default(0)->after('payment_manager');
            $table->string('sold_from')->nullable()->after('payment_spk');
            $table->decimal('profit', 15, 2)->nullable()->default(0)->after('sold_from');
            $table->decimal('markup', 8, 2)->nullable()->default(0)->after('profit');

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('nds_id')->references('id')->on('nds')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fin_reports', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['nds_id']);
            $table->dropColumn([
                'supplier_id',
                'nds_id',
                'bonus_client',
                'net_sales',
                'remainder',
                'manager_name',
                'supplier_invoice_number',
                'supplier_amount',
                'payment_manager',
                'payment_spk',
                'sold_from',
                'profit',
                'markup'
            ]);
        });
    }
};

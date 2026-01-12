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
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('buying_name')->nullable();
            $table->string('selling_name')->nullable();
            $table->string('spk')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('purchase_sum', 15, 2)->nullable();
            $table->decimal('markup_percent', 8, 2)->nullable();
            $table->decimal('selling_price', 15, 2)->nullable();
            $table->decimal('selling_sum', 15, 2)->nullable();
            $table->decimal('prf_percent', 8, 2)->nullable();
            $table->decimal('deal_payment', 15, 2)->nullable();
            $table->decimal('per_unit_payment', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('variables')
            ->whereNull('table_type')
            ->update(['table_type' => 'company']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('variables')
            ->update(['table_type' => null]);
    }
};

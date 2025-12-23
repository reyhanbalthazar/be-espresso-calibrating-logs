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
        Schema::table('calibration_sessions', function (Blueprint $table) {
            $table->foreignId('coffee_shop_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calibration_sessions', function (Blueprint $table) {
            $table->dropForeign(['coffee_shop_id']);
            $table->dropColumn('coffee_shop_id');
        });
    }
};

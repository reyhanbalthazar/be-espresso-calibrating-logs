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
        Schema::table('shots', function (Blueprint $table) {
            $table->decimal('water_temperature', 5, 2)->nullable()->after('time_seconds'); // Temperature in Celsius, e.g., 92.50
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shots', function (Blueprint $table) {
            $table->dropColumn('water_temperature');
        });
    }
};

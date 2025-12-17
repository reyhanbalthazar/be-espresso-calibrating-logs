<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calibration_session_id')->constrained()->onDelete('cascade');
            $table->integer('shot_number');
            $table->string('grind_setting', 50);
            $table->decimal('dose', 5, 2); // max 999.99
            $table->decimal('yield', 5, 2); // max 999.99
            $table->integer('time_seconds');
            $table->text('taste_notes')->nullable();
            $table->text('action_taken')->nullable();
            $table->timestamps();

            // Add composite unique constraint for session and shot number
            $table->unique(['calibration_session_id', 'shot_number']);

            // Add index for faster queries
            $table->index('calibration_session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shots');
    }
};

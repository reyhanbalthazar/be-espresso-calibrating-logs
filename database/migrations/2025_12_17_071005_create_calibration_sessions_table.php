<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calibration_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bean_id')->constrained()->onDelete('cascade');
            $table->foreignId('grinder_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('session_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add indexes for performance
            $table->index('session_date');
            $table->index(['bean_id', 'session_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calibration_sessions');
    }
};

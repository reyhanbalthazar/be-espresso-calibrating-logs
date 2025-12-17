<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('origin', 150)->nullable();
            $table->string('roastery', 150)->nullable();
            $table->enum('roast_level', ['light', 'medium', 'dark'])->nullable();
            $table->date('roast_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beans');
    }
};

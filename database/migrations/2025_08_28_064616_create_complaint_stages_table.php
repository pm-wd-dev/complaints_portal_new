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
        Schema::create('stages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name'); // Stage name like "Submitted", "Under Review"
            $table->integer('step_number')->nullable(); // Step 1, 2, 3, etc.
            $table->string('color')->default('#007bff'); // Color for display
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};

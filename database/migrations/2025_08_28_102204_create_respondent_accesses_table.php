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
        Schema::create('respondent_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('complaint_id')->constrained()->onDelete('cascade');
            $table->string('access_token', 64)->unique();
            $table->string('access_type')->default('respondent'); // 'respondent' or 'lawyer'
            $table->timestamp('expires_at');
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'complaint_id']);
            $table->index('access_token');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respondent_accesses');
    }
};

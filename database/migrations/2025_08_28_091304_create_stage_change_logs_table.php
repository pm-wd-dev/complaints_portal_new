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
        Schema::create('stage_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_stage_id')->nullable()->constrained('stages')->onDelete('set null');
            $table->foreignId('to_stage_id')->nullable()->constrained('stages')->onDelete('set null');
            $table->string('action')->nullable(); // 'submitted', 'stage_changed', 'sent_to_respondent', 'sent_to_lawyer', etc.
            $table->text('description')->nullable(); // Additional details about the change
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('performer_role')->nullable(); // 'admin', 'respondent', 'system'
            $table->json('additional_data')->nullable(); // Store any extra data like respondent_id, lawyer_email etc
            $table->timestamps();

            $table->index(['complaint_id', 'created_at']);
            $table->index('from_stage_id');
            $table->index('to_stage_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage_change_logs');
    }
};

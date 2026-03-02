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
        Schema::create('respondent_response_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The respondent user
            $table->string('respondent_email');
            $table->string('case_number');
            $table->string('venue_legal_name');
            $table->string('venue_city_state');
            $table->string('respondent_name');
            $table->date('complaint_date');
            $table->text('respondent_side_story');
            $table->text('issue_detail_description');
            $table->text('witnesses_information')->nullable();
            $table->string('supporting_evidence_type'); // photos, videos, messages, documents, none
            $table->text('evidence_description')->nullable();
            $table->boolean('has_supporting_evidence')->default(false);
            $table->timestamp('submitted_at');
            $table->timestamps();
            $table->index(['complaint_id', 'user_id']);
            $table->index('case_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respondent_response_details');
    }
};

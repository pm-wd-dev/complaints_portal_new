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
        Schema::create('lawyer_response_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('lawyer_email');
            $table->string('case_number');
            $table->string('law_firm_name');
            $table->string('lawyer_city_state');
            $table->string('lawyer_name');
            $table->date('review_date');
            $table->longText('legal_assessment');
            $table->longText('legal_recommendations');
            $table->longText('compliance_notes');
            $table->string('supporting_evidence_type');
            $table->longText('evidence_description')->nullable();
            $table->boolean('has_supporting_evidence')->default(false);
            $table->timestamp('submitted_at');
            $table->timestamps();
            
            $table->index(['complaint_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lawyer_response_details');
    }
};

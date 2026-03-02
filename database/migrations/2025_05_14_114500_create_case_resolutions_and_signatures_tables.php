<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaseResolutionsAndSignaturesTables extends Migration
{
    public function up()
    {
        // Table for storing the final resolution text and outcome
        Schema::create('case_resolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->onDelete('cascade');
            $table->text('resolution_text')->nullable();    // Detailed resolution input by admin
            $table->string('generated_pdf_path')->nullable(); // Path to stored PDF
            $table->string('template_type')->nullable();    // Type of template used
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null'); // Admin who generated it
            $table->timestamps();
        });

        // Table for tracking who signed or still needs to sign
        Schema::create('case_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->onDelete('cascade');
            $table->foreignId('resolution_id')->constrained('case_resolutions')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable(); // Can be null for guest signatures
            $table->string('signer_name');     // Store name for both users and guests
            $table->string('signer_email');    // Store email for both users and guests
            $table->string('signer_role');     // complainant, respondent, leadership
            $table->string('signature_path')->nullable();   // Uploaded signature image path
            $table->timestamp('signed_at')->nullable();     // When the document was signed
            $table->string('ip_address')->nullable();       // IP address of signer for audit
            $table->timestamps();

            // Foreign key with nullable user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // Prevent duplicate signatures from same person on same resolution
            $table->unique(['resolution_id', 'signer_email']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('case_signatures');
        Schema::dropIfExists('case_resolutions');
    }
}

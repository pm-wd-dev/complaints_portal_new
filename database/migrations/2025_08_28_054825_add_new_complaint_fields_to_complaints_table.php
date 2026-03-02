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
        Schema::table('complaints', function (Blueprint $table) {
            // Add fields for the updated complaint form
            $table->enum('submitted_as', ['cast_member', 'guest'])->default('guest')->after('submitted_by_admin_id');
            $table->boolean('is_anonymous')->default(false)->after('submitted_as');
            $table->string('complaint_about')->nullable()->after('description'); // Who is your complaint about?
            $table->string('complainee_name')->nullable()->after('complaint_about'); // Full name if available
            $table->string('complainee_email')->nullable()->after('complainee_name'); // Email if available
            $table->text('complainee_address')->nullable()->after('complainee_email'); // Address if available
            $table->text('witnesses')->nullable()->after('complainee_address'); // Were there witnesses?
            $table->enum('evidence_type', ['photo_screenshot', 'videos', 'messages_emails', 'other_documents', 'no_evidence'])->nullable()->after('witnesses'); // Support evidence type
            $table->text('evidence_description')->nullable()->after('evidence_type'); // Evidence description
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn([
                'submitted_as',
                'is_anonymous',
                'complaint_about',
                'complainee_name',
                'complainee_email',
                'complainee_address',
                'witnesses',
                'evidence_type',
                'evidence_description'
            ]);
        });
    }
};

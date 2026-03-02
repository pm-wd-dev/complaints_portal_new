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
        Schema::table('attachments', function (Blueprint $table) {
            $table->foreignId('respondent_response_id')->nullable()->constrained('respondent_response_details')->onDelete('cascade');
            $table->index('respondent_response_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropForeign(['respondent_response_id']);
            $table->dropColumn('respondent_response_id');
        });
    }
};

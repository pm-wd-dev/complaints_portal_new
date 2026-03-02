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
            $table->enum('send_to', ['respondent', 'lawyer', 'complainant'])->nullable()->after('stage_id');
            $table->string('lawyer_email')->nullable()->after('send_to');
            $table->string('lawyer_phone')->nullable()->after('lawyer_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['send_to', 'lawyer_email', 'lawyer_phone']);
        });
    }
};

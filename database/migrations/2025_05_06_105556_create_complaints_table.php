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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('case_number', 100)->unique();
            $table->boolean('submitted_by_admin')->default(false);
            $table->unsignedBigInteger('submitted_by_admin_id')->nullable();
            $table->string('name');
            $table->string('email');
            $table->text('description');
            $table->string('location');
            $table->string('complaint_type', 100);
            $table->string('attachment_path')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');            $table->enum('status', ['submitted', 'under_review', 'escalated', 'resolved', 'closed'])->default('submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            $table->foreign('submitted_by_admin_id')->references('id')->on('users')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};

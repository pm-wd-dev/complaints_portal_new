<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable()->after('complaint_response_id');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->string('type')->nullable()->after('file_type'); // For identifying QR codes
        });
    }

    public function down()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
            $table->dropColumn('type');
        });
    }
};

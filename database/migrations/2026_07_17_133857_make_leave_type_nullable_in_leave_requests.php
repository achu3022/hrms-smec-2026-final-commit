<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Drop the foreign key first, then make the column nullable
            $table->dropForeign(['leave_type_id']);
            $table->unsignedBigInteger('leave_type_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('leave_type_id')->nullable(false)->change();
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->cascadeOnDelete();
        });
    }
};

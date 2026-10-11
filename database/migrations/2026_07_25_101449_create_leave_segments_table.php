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
        if (Schema::hasTable('leave_segments')) {
            Schema::dropIfExists('leave_segments');
        }

        Schema::create('leave_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_request_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_days', 8, 2)->default(0);
            $table->decimal('casual_days', 8, 2)->default(0);
            $table->decimal('medical_days', 8, 2)->default(0);
            $table->decimal('lop_days', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_segments');
    }
};

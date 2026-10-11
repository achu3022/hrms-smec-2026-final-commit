<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('early_going_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time');
            $table->text('reason');
            $table->string('status')->default('Pending'); // Pending, TL Approved, Approved, Rejected
            $table->timestamp('tl_approved_at')->nullable();
            $table->timestamp('admin_approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('early_going_requests');
    }
};

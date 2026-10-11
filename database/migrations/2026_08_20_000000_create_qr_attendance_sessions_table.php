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
        Schema::create('qr_attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique()->index();
            $table->enum('status', ['active', 'scanned', 'consumed', 'expired'])->default('active')->index();
            $table->foreignId('scanned_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('scanned_at')->nullable();
            $table->timestamp('consumed_at')->nullable();
            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_attendance_sessions');
    }
};

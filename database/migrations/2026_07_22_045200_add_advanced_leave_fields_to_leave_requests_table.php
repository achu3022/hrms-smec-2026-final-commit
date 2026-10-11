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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->integer('paid_days')->default(0)->after('total_days');
            $table->integer('lop_days')->default(0)->after('paid_days');
            $table->string('medical_certificate_path')->nullable()->after('reason');
            $table->timestamp('hr_approved_at')->nullable()->after('medical_certificate_path');
            $table->timestamp('management_approved_at')->nullable()->after('hr_approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn([
                'paid_days',
                'lop_days',
                'medical_certificate_path',
                'hr_approved_at',
                'management_approved_at',
            ]);
        });
    }
};

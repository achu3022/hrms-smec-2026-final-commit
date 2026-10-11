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
            if (!Schema::hasColumn('leave_requests', 'is_half_day')) {
                $table->boolean('is_half_day')->default(false)->after('end_date');
            }
            if (!Schema::hasColumn('leave_requests', 'half_day_type')) {
                $table->string('half_day_type')->nullable()->after('is_half_day');
            }
            $table->decimal('total_days', 8, 2)->change();
            if (Schema::hasColumn('leave_requests', 'paid_days')) {
                $table->decimal('paid_days', 8, 2)->default(0)->change();
            }
            if (Schema::hasColumn('leave_requests', 'lop_days')) {
                $table->decimal('lop_days', 8, 2)->default(0)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            if (Schema::hasColumn('leave_requests', 'is_half_day')) {
                $table->dropColumn(['is_half_day', 'half_day_type']);
            }
        });
    }
};

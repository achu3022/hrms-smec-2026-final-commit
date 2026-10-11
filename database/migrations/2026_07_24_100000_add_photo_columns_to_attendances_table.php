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
        if (!Schema::hasColumn('attendances', 'checkin_photo')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->string('checkin_photo')->nullable();
                $table->string('checkout_photo')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('attendances', 'checkin_photo')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn(['checkin_photo', 'checkout_photo']);
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('leave_types')->insertOrIgnore([
            ['name' => 'Casual', 'days_allowed' => 12, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Medical', 'days_allowed' => 12, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Loss of Pay (LOP)', 'days_allowed' => 0, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('leave_types')->whereIn('name', ['Casual', 'Medical', 'Loss of Pay (LOP)'])->delete();
    }
};

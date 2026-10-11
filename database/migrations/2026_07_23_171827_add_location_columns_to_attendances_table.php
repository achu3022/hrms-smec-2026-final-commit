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
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('checkin_accuracy', 8, 2)->nullable();
            $table->decimal('checkin_distance', 8, 2)->nullable();
            
            $table->decimal('checkout_accuracy', 8, 2)->nullable();
            $table->decimal('checkout_distance', 8, 2)->nullable();
            
            $table->string('checkin_ip')->nullable();
            $table->string('checkout_ip')->nullable();
            
            $table->string('checkin_browser')->nullable();
            $table->string('checkout_browser')->nullable();
            
            $table->string('checkin_device')->nullable();
            $table->string('checkout_device')->nullable();
            
            $table->boolean('location_verified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'checkin_accuracy', 'checkin_distance', 
                'checkout_accuracy', 'checkout_distance',
                'checkin_ip', 'checkout_ip',
                'checkin_browser', 'checkout_browser',
                'checkin_device', 'checkout_device',
                'location_verified'
            ]);
        });
    }
};

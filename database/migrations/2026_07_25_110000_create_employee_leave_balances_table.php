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
        Schema::create('employee_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            
            $table->decimal('opening_cl', 8, 2)->default(0);
            $table->decimal('credited_cl', 8, 2)->default(0);
            $table->decimal('used_cl', 8, 2)->default(0);
            $table->decimal('closing_cl', 8, 2)->default(0);

            $table->decimal('opening_ml', 8, 2)->default(0);
            $table->decimal('credited_ml', 8, 2)->default(0);
            $table->decimal('used_ml', 8, 2)->default(0);
            $table->decimal('closing_ml', 8, 2)->default(0);

            $table->decimal('lop_days', 8, 2)->default(0);
            $table->timestamps();

            $table->unique(['employee_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leave_balances');
    }
};

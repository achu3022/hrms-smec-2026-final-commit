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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->string('name');
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->string('company_email')->unique()->nullable();
            $table->string('personal_email')->nullable();
            $table->string('personal_phone')->unique()->nullable();
            $table->string('office_phone')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('marital_status')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('emergency_name')->nullable();
            $table->string('emergency_relation')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('emergency_alt_phone')->nullable();
            $table->string('aadhaar')->unique()->nullable();
            $table->string('pan')->nullable();
            $table->string('passport')->nullable();
            $table->string('driving_license')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc')->nullable();
            $table->string('branch')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('reporting_manager')->nullable();
            $table->string('work_location')->nullable();
            $table->string('photo')->nullable();
            $table->string('aadhaar_file')->nullable();
            $table->string('pan_file')->nullable();
            $table->string('passport_file')->nullable();
            $table->string('license_file')->nullable();
            $table->string('bank_document')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

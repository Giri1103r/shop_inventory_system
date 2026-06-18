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
        Schema::create('masters_employee_temp', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('emp_id', 50)->nullable();
            $table->string('emp_name', 100)->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('nationality', 50)->nullable();
            $table->string('user_role', 100)->nullable();
            $table->string('id_type', 100)->nullable();
            $table->string('id_number', 100)->nullable();
            $table->dateTime('joining_date')->nullable();
            $table->string('mobile_no', 15)->nullable();
            $table->string('company', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('unit', 50)->nullable();
            $table->string('department', 50)->nullable();
            $table->string('designation', 50)->nullable();
            $table->string('status', 10)->nullable();
            $table->string('employee_status', 100)->nullable();
            $table->string('reporting_manager', 100)->nullable();
            $table->integer('upload_status')->nullable();
            $table->integer('error_status')->nullable();
            $table->string('error_remarks')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->enum('trash', ['YES', 'NO'])->default('NO');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masters_employee_temp');
    }
};

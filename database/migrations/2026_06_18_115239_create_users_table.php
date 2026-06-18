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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email')->nullable();
            $table->text('role')->nullable();
            $table->integer('user_type')->nullable()->comment('1 => employee, 2=> Contractor');
            $table->string('employee_id', 100)->nullable();
            $table->string('username', 100)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->dateTime('password_changed_at')->nullable();
            $table->rememberToken();
            $table->integer('company_id')->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('unit_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->string('designation_id')->nullable();
            $table->string('mobile', 50)->nullable();
            $table->integer('otp')->nullable();
            $table->string('otp_token')->nullable();
            $table->string('profile_image')->nullable();
            $table->text('signature_upload')->nullable();
            $table->text('permission')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('status')->default(1);
            $table->enum('trash', ['NO', 'YES'])->default('NO');
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

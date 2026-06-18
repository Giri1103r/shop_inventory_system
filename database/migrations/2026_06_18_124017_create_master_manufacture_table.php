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
        Schema::create('master_manufacture', function (Blueprint $table) {
            $table->id();

            $table->string('manufacture_id', 50)->unique();
            $table->string('manufacturer_name', 255);
            $table->string('short_name', 100)->nullable();

            $table->string('contact_person', 100)->nullable();
            $table->string('mobile_no', 15)->nullable();
            $table->string('email', 100)->nullable();

            $table->text('address')->nullable();

            $table->tinyInteger('status')
                ->default(1)
                ->comment('1=Active, 0=Inactive');

            $table->enum('trash', ['Yes', 'No'])
                ->default('No');

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_manufacture');
    }
};

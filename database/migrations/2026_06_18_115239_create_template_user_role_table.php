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
        Schema::create('template_user_role', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('role_id', 50)->nullable();
            $table->string('role_name', 100)->nullable();
            $table->text('role_permission')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('status')->default(1);
            $table->enum('trash', ['NO', 'YES'])->default('NO');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_user_role');
    }
};

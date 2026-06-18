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
        Schema::create('users_fcm_token', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id');
            $table->text('token');
            $table->string('device_type', 10);
            $table->integer('status')->default(1);
            $table->enum('trash', ['NO', 'YES'])->default('NO');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_fcm_token');
    }
};

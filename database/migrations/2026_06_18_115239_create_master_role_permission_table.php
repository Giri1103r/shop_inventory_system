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
        Schema::create('master_role_permission', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('role_id');
            $table->integer('menu_id');
            $table->text('role_permissions')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('status')->nullable()->default(1);
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
        Schema::dropIfExists('master_role_permission');
    }
};

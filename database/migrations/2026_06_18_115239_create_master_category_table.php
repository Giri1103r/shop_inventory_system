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
        Schema::create('master_category', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('category_id', 50)->nullable();
            $table->string('category_code', 100)->nullable();
            $table->string('category_name')->nullable();
            $table->text('description')->nullable();
            $table->integer('status')->default(1);
            $table->enum('trash', ['YES', 'NO'])->default('NO');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_category');
    }
};

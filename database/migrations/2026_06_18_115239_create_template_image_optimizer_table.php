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
        Schema::create('template_image_optimizer', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('image_path');
            $table->integer('optimize_status')->comment('0 => waiting for other process, 1 => Ready, 2 => completed');
            $table->integer('image_resize_require')->comment('0 => no ,1 => yes');
            $table->text('from_size');
            $table->text('to_size');
            $table->integer('image_resize_status');
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
        Schema::dropIfExists('template_image_optimizer');
    }
};

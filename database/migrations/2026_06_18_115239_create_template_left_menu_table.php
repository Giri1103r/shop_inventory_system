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
        Schema::create('template_left_menu', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100)->default('');
            $table->string('namekey', 200)->nullable();
            $table->string('link')->default('');
            $table->string('icon', 100)->nullable();
            $table->integer('parent_id')->default(0);
            $table->text('permission')->nullable();
            $table->boolean('is_parent')->default(false);
            $table->enum('is_module', ['0', '1'])->default('0')->comment('0=>not module, 1=> module');
            $table->string('modkey')->nullable();
            $table->integer('sort_order')->nullable();
            $table->integer('status')->default(1);
            $table->enum('trash', ['NO', 'YES'])->default('NO');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('upated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_left_menu');
    }
};

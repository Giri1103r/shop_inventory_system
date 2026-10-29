<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('masters_shift')) {
            return;
        }

        Schema::create('masters_shift', function (Blueprint $table) {
            $table->increments('id');
            $table->string('shift_code');
            $table->string('shift_name');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('total_hours', 5, 2)->nullable();
            $table->integer('status')->default(1);
            $table->enum('trash', ['YES', 'NO'])->default('NO');
            $table->integer('created_by');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masters_shift');
    }
};

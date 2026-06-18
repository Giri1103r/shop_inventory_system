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
        Schema::create('template_admin_upload_log_type', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('type_name', 100);
            $table->string('key_name', 50);
            $table->string('sample_file');
            $table->string('file_name', 100);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('status')->default(1);
            $table->enum('trash', ['NO', 'YES'])->default('NO');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_admin_upload_log_type');
    }
};

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
        Schema::create('template_admin_upload_log', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('upload_type')->comment('1 => Employee, 2 => Contractor');
            $table->integer('upload_status')->comment('0 => Not yet start, 1 => Processing, 2=> Completed');
            $table->string('file_name', 100);
            $table->string('file_orgname');
            $table->string('file_path');
            $table->string('file_size', 10);
            $table->string('file_extension', 10);
            $table->integer('created_by');
            $table->integer('status')->default(1);
            $table->enum('trash', ['NO', 'YES'])->default('NO');
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_admin_upload_log');
    }
};

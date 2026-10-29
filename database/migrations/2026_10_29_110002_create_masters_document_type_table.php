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
        if (Schema::hasTable('masters_document_type')) {
            return;
        }

        Schema::create('masters_document_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('doc_code');
            $table->string('document_name');
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
        Schema::dropIfExists('masters_document_type');
    }
};

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
        Schema::create('template_notification', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('notification_type')->comment(' 1 => notification');
            $table->string('module_type', 20)->comment('1 => uauc');
            $table->text('notification_message');
            $table->text('mobile_notification');
            $table->text('web_link')->nullable();
            $table->string('assigned_user');
            $table->string('viewed_user')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('status')->default(1);
            $table->enum('trash', ['NO', 'YES'])->nullable()->default('NO');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_notification');
    }
};

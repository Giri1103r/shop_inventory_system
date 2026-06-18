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
        Schema::create('template_blocked_userlog', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_login_id');
            $table->string('session_id', 100);
            $table->string('user_identifier')->nullable();
            $table->text('request_uri');
            $table->string('timestamp', 20);
            $table->string('client_ip', 50);
            $table->text('client_user_agent');
            $table->text('referer_page');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_blocked_userlog');
    }
};

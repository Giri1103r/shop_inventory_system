<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PharIo\Manifest\License;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('master_manufacture', function (Blueprint $table) {
           $table->renameColumn('short_name','license_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_manufacture', function (Blueprint $table) {
            $table->renameColumn('license_number','short_name');
        });
    }
};

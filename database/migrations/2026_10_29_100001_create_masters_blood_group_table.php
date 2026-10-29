<?php

use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable('masters_blood_group')) {
            Schema::create('masters_blood_group', function (Blueprint $table) {
                $table->increments('id');
                $table->string('bg_code');
                $table->string('blood_group_name');
                $table->integer('status')->default(1);
                $table->enum('trash', ['YES', 'NO'])->default('NO');
                $table->integer('created_by');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();
            });

            return;
        }

        /* The table already exists on live databases, so only bring it in line
           with the designation structure instead of recreating it. */
        Schema::table('masters_blood_group', function (Blueprint $table) {
            if (!Schema::hasColumn('masters_blood_group', 'bg_code')) {
                $table->string('bg_code')->nullable()->after('id');
            }
            if (!Schema::hasColumn('masters_blood_group', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        DB::table('masters_blood_group')
            ->whereNull('bg_code')
            ->orWhere('bg_code', '')
            ->orderBy('id')
            ->each(function ($row) {
                DB::table('masters_blood_group')
                    ->where('id', $row->id)
                    ->update(['bg_code' => 'BG-' . str_pad($row->id, 5, '0', STR_PAD_LEFT)]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('masters_blood_group')) {
            return;
        }

        Schema::table('masters_blood_group', function (Blueprint $table) {
            if (Schema::hasColumn('masters_blood_group', 'bg_code')) {
                $table->dropColumn('bg_code');
            }
            if (Schema::hasColumn('masters_blood_group', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};

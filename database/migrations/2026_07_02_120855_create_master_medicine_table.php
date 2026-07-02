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
        Schema::create('master_medicine', function (Blueprint $table) {
            $table->id();

            $table->string('medicine_id')->nullable();
            $table->string('medicine_name', 255);
            $table->string('generic_names')->nullable();

            $table->integer('category_id')->nullable();
            $table->integer('manufacture_id')->nullable();
            $table->integer('uom_id')->nullable();
            $table->integer('tax_id')->nullable();

            $table->string('storage_condition')->nullable();
            $table->string('package_size')->nullable();
            $table->string('scheduled_type')->nullable();

            $table->integer('reorder_level')->default(0);
            $table->integer('reorder_quantity')->default(0);

            $table->decimal('mrp', 10, 2)->default(0.00);

            $table->text('description')->nullable();

            $table->tinyInteger('status')
                ->default(1)
                ->comment('1 = Active, 0 = Inactive');

            $table->enum('trash', ['Yes', 'No'])
                ->default('No');

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();


            $table->foreign('category_id')->references('id')->on('master_category')->onDelete('set null');
            $table->foreign('manufacture_id')->references('id')->on('master_manufacture')->onDelete('set null');
            $table->foreign('uom_id')->references('id')->on('master_uom')->onDelete('set null');
            $table->foreign('tax_id')->references('id')->on('master_tax')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_medicine');
    }
};

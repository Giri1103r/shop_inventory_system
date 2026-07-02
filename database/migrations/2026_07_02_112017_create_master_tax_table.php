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
        Schema::create('master_tax', function (Blueprint $table) {
            $table->id();

            $table->string('tax_id')->nullable();
            $table->string('tax_name', 255);
            $table->string('hsn_code')->nullable();

            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('cgst_percentage', 5, 2)->default(0);
            $table->decimal('sgst_percentage', 5, 2)->default(0);
            $table->decimal('igst_percentage', 5, 2)->default(0);
            $table->decimal('cess_percentage', 5, 2)->default(0);

            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();

            $table->text('remarks')->nullable();

            $table->tinyInteger('status')
                ->default(1)
                ->comment('1=Active, 0=Inactive');

            $table->enum('trash', ['Yes', 'No'])
                ->default('No');

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_tax');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->noActionOnDelete();
            $table->foreignId('equipment_type_id')->constrained('equipment_types')->noActionOnDelete();
            $table->string('serial_number', 255);
            $table->string('brand', 255);
            $table->string('model', 255);
            $table->string('status', 50)->default('available');
            $table->string('location', 255)->nullable();
            $table->date('warranty_end')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->string('supplier', 255)->nullable();
            $table->text('notes')->nullable();
            $table->string('qr_code', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'serial_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};

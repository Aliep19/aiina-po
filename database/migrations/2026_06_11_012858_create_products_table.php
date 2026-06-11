<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code', 50)->unique();
            $table->string('name');
            $table->text('specification')->nullable();
            $table->string('unit', 30)->nullable();
            $table->decimal('default_price', 15, 2)->default(0);
            $table->timestamps();

            // Index untuk kolom yang sering di-search/sort
            $table->index('product_code');
            $table->index('name');
        });

        // Pivot: product ↔ supplier (many-to-many)
        Schema::create('product_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();
            $table->foreignId('supplier_id')
                  ->constrained('suppliers')
                  ->cascadeOnDelete();
            $table->decimal('supplier_price', 15, 2)->default(0); // harga dari supplier ini
            $table->boolean('is_primary')->default(false);         // supplier utama?
            $table->timestamps();

            $table->unique(['product_id', 'supplier_id']);
            $table->index('product_id');
            $table->index('supplier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
        Schema::dropIfExists('products');
    }
};

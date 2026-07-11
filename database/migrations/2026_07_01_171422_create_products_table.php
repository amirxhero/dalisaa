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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('brand');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->text('description');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('regular_price')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->json('highlights')->nullable();
            $table->json('specs')->nullable();
            $table->decimal('rating_cache', 2, 1)->default(0);
            $table->unsignedInteger('reviews_count_cache')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

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
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('color_hex', 7)->nullable()->change();
            $table->string('discount_type')->nullable()->after('regular_price');
            $table->unsignedBigInteger('discount_value')->default(0)->after('discount_type');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('discount_type')->nullable()->after('regular_price');
            $table->unsignedBigInteger('discount_value')->default(0)->after('discount_type');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('color_hex', 7)->nullable(false)->change();
            $table->dropColumn(['discount_type', 'discount_value']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value']);
        });
    }
};

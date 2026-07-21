<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Stores the raw amount as entered by admin (in price_currency, not converted to Toman)
            $table->decimal('price_original', 12, 2)->nullable()->after('price_currency');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('price_original');
        });
    }
};

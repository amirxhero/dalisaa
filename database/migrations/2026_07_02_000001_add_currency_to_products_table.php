<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->char('price_currency', 4)->default('IRR')->after('price');
            $table->decimal('price_original', 15, 2)->default(0)->after('price_currency');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price_currency', 'price_original']);
        });
    }
};

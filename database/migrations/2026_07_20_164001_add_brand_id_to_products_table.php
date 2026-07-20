<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained('brands')->nullOnDelete();
            $table->string('brand')->nullable()->change();
        });

        // Migrate existing brand text entries to brands table
        $existingBrands = DB::table('products')
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->pluck('brand');

        foreach ($existingBrands as $brandName) {
            $slug = Str::slug($brandName);
            if (empty($slug)) {
                $slug = 'brand-' . Str::random(5);
            }

            $counter = 1;
            $originalSlug = $slug;
            while (DB::table('brands')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $brandId = DB::table('brands')->insertGetId([
                'title'      => $brandName,
                'slug'       => $slug,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('products')->where('brand', $brandName)->update(['brand_id' => $brandId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });
    }
};

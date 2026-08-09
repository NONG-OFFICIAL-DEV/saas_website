<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_pricing_tiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestampsTz();

            $table->index(['product_id', 'sort_order']);
        });

        Schema::create('product_pricing_tier_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pricing_tier_id')->constrained('product_pricing_tiers')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name', 100);
            $table->string('price_label', 100)->nullable();
            $table->string('description', 500)->nullable();
            $table->text('features_text')->nullable(); // one bullet per line
            $table->string('cta_label', 100)->nullable();
            $table->timestampsTz();

            $table->unique(['pricing_tier_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_pricing_tier_translations');
        Schema::dropIfExists('product_pricing_tiers');
    }
};

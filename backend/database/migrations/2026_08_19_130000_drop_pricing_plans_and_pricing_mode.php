<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Pricing is controlled entirely within each product's own SaaS backend
// (its own real billing system) — this CMS never authors marketing-only
// pricing as a stand-in. There is no "pricing_mode" split: every product
// with pricing either has its own live pricing component wired to its own
// API (see PriceSection.vue / StudioPriceSection.vue), or it doesn't show
// a pricing section yet.
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('pricing_plan_translations');
        Schema::dropIfExists('pricing_plans');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('pricing_mode');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('pricing_mode', ['live', 'cms'])->default('cms')->after('cta_url');
        });

        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('slug', 100);
            $table->decimal('monthly_price', 10, 2)->nullable();
            $table->decimal('yearly_price', 10, 2)->nullable();
            $table->boolean('is_popular')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestampsTz();

            $table->unique(['product_id', 'slug']);
            $table->index(['product_id', 'sort_order']);
        });

        Schema::create('pricing_plan_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pricing_plan_id')->constrained('pricing_plans')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name', 100);
            $table->string('tagline', 255)->nullable();
            $table->string('cta_label', 100)->nullable();
            $table->jsonb('features')->nullable();
            $table->timestampsTz();

            $table->unique(['pricing_plan_id', 'locale']);
        });
    }
};

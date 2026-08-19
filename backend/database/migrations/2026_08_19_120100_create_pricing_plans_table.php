<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('pricing_plan_translations');
        Schema::dropIfExists('pricing_plans');
    }
};

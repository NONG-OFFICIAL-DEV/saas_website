<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_features', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('icon', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestampsTz();

            $table->index(['product_id', 'sort_order']);
        });

        Schema::create('product_feature_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_feature_id')->constrained('product_features')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->timestampsTz();

            $table->unique(['product_feature_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_feature_translations');
        Schema::dropIfExists('product_features');
    }
};

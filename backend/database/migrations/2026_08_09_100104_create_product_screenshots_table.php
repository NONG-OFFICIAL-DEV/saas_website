<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_screenshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('url', 500);
            $table->integer('sort_order')->default(0);
            $table->timestampsTz();

            $table->index(['product_id', 'sort_order']);
        });

        Schema::create('product_screenshot_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('screenshot_id')->constrained('product_screenshots')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('alt_text', 255)->nullable();
            $table->string('caption', 255)->nullable();
            $table->timestampsTz();

            $table->unique(['screenshot_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_screenshot_translations');
        Schema::dropIfExists('product_screenshots');
    }
};

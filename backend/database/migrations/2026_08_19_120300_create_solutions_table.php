<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solutions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug', 150)->unique();
            $table->string('icon', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestampsTz();

            $table->index('is_published');
            $table->index('sort_order');
        });

        Schema::create('solution_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('solution_id')->constrained('solutions')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name', 150);
            $table->string('tagline', 255)->nullable();
            $table->text('description')->nullable();
            $table->timestampsTz();

            $table->unique(['solution_id', 'locale']);
        });

        // A solution (e.g. "Coffee Shop") can point at one or more products
        // (e.g. Nexstack POS), and a product can serve more than one solution.
        Schema::create('solution_product', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('solution_id')->constrained('solutions')->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestampsTz();

            $table->unique(['solution_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solution_product');
        Schema::dropIfExists('solution_translations');
        Schema::dropIfExists('solutions');
    }
};

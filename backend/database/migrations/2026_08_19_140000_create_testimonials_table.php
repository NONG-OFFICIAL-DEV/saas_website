<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('author_name', 150);
            $table->string('author_title', 255)->nullable();
            $table->string('author_avatar_url', 500)->nullable();
            // Optional — which product this testimonial is about. Nullable
            // so a testimonial can be sitewide ("I run my whole business
            // on Nexstack") rather than tied to one specific product.
            $table->foreignUuid('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestampsTz();

            $table->index('is_published');
            $table->index('sort_order');
        });

        Schema::create('testimonial_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('testimonial_id')->constrained('testimonials')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('quote');
            $table->timestampsTz();

            $table->unique(['testimonial_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonial_translations');
        Schema::dropIfExists('testimonials');
    }
};

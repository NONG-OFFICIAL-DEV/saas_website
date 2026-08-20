<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentation_articles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug', 180)->unique();
            $table->foreignUuid('category_id')->constrained('documentation_categories')->cascadeOnDelete();
            // Independent of the category's own product_id — usually matches
            // it, but kept separate so the admin can assign/override it
            // directly on the article (spec requirement).
            $table->foreignUuid('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('cover_image_url', 500)->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->integer('sort_order')->default(0);
            $table->timestampTz('published_at')->nullable();
            $table->timestampsTz();

            $table->index('category_id');
            $table->index('product_id');
            $table->index('status');
            $table->index('sort_order');
        });

        Schema::create('documentation_article_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('article_id')->constrained('documentation_articles')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title', 200);
            $table->string('excerpt', 500)->nullable();
            // Rich HTML from the admin's Tiptap editor — rendered with a
            // sanitizer on the frontend, never raw content from public input.
            $table->text('content')->nullable();
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->timestampsTz();

            $table->unique(['article_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentation_article_translations');
        Schema::dropIfExists('documentation_articles');
    }
};

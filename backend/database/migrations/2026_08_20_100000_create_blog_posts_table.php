<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug', 150)->unique();
            $table->string('author_name', 150)->nullable();
            $table->string('cover_image_url', 500)->nullable();
            $table->timestampTz('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestampsTz();

            $table->index('is_published');
            $table->index('published_at');
        });

        Schema::create('blog_post_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title', 255);
            $table->string('excerpt', 500)->nullable();
            $table->longText('content');
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->timestampsTz();

            $table->unique(['blog_post_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_translations');
        Schema::dropIfExists('blog_posts');
    }
};

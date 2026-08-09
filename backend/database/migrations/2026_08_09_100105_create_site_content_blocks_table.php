<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Singleton content blocks: one row per key ('hero', 'about', 'footer').
// Each block type has a different shape (hero has stats, about has
// values/socials, footer has contact info + socials) — rather than a wide
// table of nullable columns covering the union of all shapes, translatable
// text lives in `content` (jsonb) on the translations table. This is edited
// exclusively through the admin CMS UI, never hand-edited as raw JSON.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_content_blocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key', 50)->unique(); // 'hero' | 'about' | 'footer'
            $table->jsonb('data')->nullable(); // non-translatable fields (urls, emails, phone, social hrefs)
            $table->timestampsTz();
        });

        Schema::create('site_content_block_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('block_id')->constrained('site_content_blocks')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->jsonb('content'); // all translatable text for this block
            $table->timestampsTz();

            $table->unique(['block_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_content_block_translations');
        Schema::dropIfExists('site_content_blocks');
    }
};

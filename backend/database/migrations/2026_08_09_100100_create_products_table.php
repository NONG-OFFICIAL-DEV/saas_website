<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug', 150)->unique();
            $table->enum('status', ['live', 'beta', 'coming_soon'])->default('coming_soon');
            $table->enum('cta_type', ['register', 'external_link', 'waitlist'])->default('waitlist');
            $table->string('cta_url', 255)->nullable();
            $table->string('accent_color', 20)->default('#6366F1');
            $table->string('logo_url', 500)->nullable();
            $table->string('hero_image_url', 500)->nullable();
            $table->string('lead_source', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestampsTz();
            $table->softDeletes();

            $table->index('is_published');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

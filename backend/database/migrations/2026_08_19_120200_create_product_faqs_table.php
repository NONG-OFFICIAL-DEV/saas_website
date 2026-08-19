<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_faqs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestampsTz();

            $table->index(['product_id', 'sort_order']);
        });

        Schema::create('product_faq_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_faq_id')->constrained('product_faqs')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('question', 255);
            $table->text('answer');
            $table->timestampsTz();

            $table->unique(['product_faq_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_faq_translations');
        Schema::dropIfExists('product_faqs');
    }
};

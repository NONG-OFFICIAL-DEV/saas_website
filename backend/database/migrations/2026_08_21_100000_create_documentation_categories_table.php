<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentation_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug', 150)->unique();
            $table->string('icon', 100)->nullable();
            // Optional — null means a general category (Getting Started,
            // Account & Settings, Troubleshooting) rather than one scoped
            // to a specific product (Studio, Smart Store).
            $table->foreignUuid('product_id')->nullable()->constrained('products')->nullOnDelete();
            // Self-referencing so the tree can go one level deeper later
            // (e.g. Studio > Bookings > Recurring Bookings) without a
            // schema change, even though today's structure is flat. Added
            // as a plain column here — the FK constraint is added below in
            // a separate Schema::table() call, since Postgres can't resolve
            // a self-referencing FK against a table still being created in
            // the same statement.
            $table->uuid('parent_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();

            $table->index('product_id');
            $table->index('parent_id');
            $table->index('sort_order');
        });

        Schema::table('documentation_categories', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('documentation_categories')->cascadeOnDelete();
        });

        Schema::create('documentation_category_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('documentation_categories')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->timestampsTz();

            $table->unique(['category_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentation_category_translations');
        Schema::dropIfExists('documentation_categories');
    }
};

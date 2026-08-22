<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Waitlist-form CTA type removed (no consumer left in the frontend) —
        // any existing 'waitlist' rows are migrated to 'register' before the
        // check constraint is tightened, so the constraint change never fails.
        DB::table('products')->where('cta_type', 'waitlist')->update(['cta_type' => 'register']);

        DB::statement('ALTER TABLE products DROP CONSTRAINT IF EXISTS products_cta_type_check');
        DB::statement("ALTER TABLE products ADD CONSTRAINT products_cta_type_check CHECK (cta_type IN ('register', 'external_link'))");
        DB::statement("ALTER TABLE products ALTER COLUMN cta_type SET DEFAULT 'register'");

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('lead_source');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('lead_source', 100)->nullable();
        });

        DB::statement('ALTER TABLE products DROP CONSTRAINT IF EXISTS products_cta_type_check');
        DB::statement("ALTER TABLE products ADD CONSTRAINT products_cta_type_check CHECK (cta_type IN ('register', 'external_link', 'waitlist'))");
        DB::statement("ALTER TABLE products ALTER COLUMN cta_type SET DEFAULT 'waitlist'");
    }
};

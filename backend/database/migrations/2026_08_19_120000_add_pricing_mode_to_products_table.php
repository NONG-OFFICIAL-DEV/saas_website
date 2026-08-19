<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// A product either has its own real billing system to fetch live plans from
// (nexstack-pos, studio-management — each already has a bespoke pricing
// section wired to that product's own backend) or it doesn't yet, in which
// case pricing is CMS-authored marketing content ("starting at" cards, no
// checkout) via the pricing_plans table. New products default to 'cms'
// until they have a real billing backend to switch to 'live'.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('pricing_mode', ['live', 'cms'])->default('cms')->after('cta_url');
        });

        DB::table('products')
            ->whereIn('slug', ['nexstack-pos', 'studio-management'])
            ->update(['pricing_mode' => 'live']);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('pricing_mode');
        });
    }
};

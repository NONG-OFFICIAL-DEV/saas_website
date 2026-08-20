<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Backs the /docs page — a demo/walkthrough video is the
            // realistic form of "documentation" for a small solo-built
            // product; nullable since not every product has one recorded.
            $table->string('demo_video_url', 500)->nullable()->after('hero_image_url');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('demo_video_url');
        });
    }
};

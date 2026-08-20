<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Purely a visibility log for the solo builder — "who signed up,
        // for which product, did it succeed" in one place instead of
        // checking Studio's and Smart Store's own admin panels separately.
        // Not an account/auth table: the real tenant lives entirely inside
        // whichever product actually provisioned it.
        Schema::create('onboarding_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('product_slug', 100);
            $table->string('business_name', 150);
            $table->string('owner_first_name', 80);
            $table->string('owner_last_name', 80);
            $table->string('email', 255);
            $table->string('phone', 30)->nullable();
            $table->string('plan_code', 100)->nullable();
            $table->enum('status', ['success', 'failed']);
            $table->text('error_message')->nullable();
            $table->timestampsTz();

            $table->index('product_slug');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_submissions');
    }
};

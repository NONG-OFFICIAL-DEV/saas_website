<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

// Custom pivot so attach()/sync() auto-generate the uuid id — solution_product
// has its own uuid primary key (like every other table in this app) rather
// than a plain composite key, so it needs HasUuids the same way any other
// model here would.
class SolutionProduct extends Pivot
{
    use HasUuids;

    protected $table = 'solution_product';
}

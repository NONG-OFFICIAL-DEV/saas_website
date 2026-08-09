<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@nexstack.local'],
            [
                'name' => 'Nong Phloeut',
                'password' => Hash::make('changeme123'),
                'role' => 'admin',
            ]
        );
    }
}

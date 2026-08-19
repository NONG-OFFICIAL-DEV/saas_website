<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $pos = Product::where('slug', 'nexstack-pos')->first();

        foreach ([
            [
                'author_name' => 'Sokha Ly',
                'author_title' => 'Owner, Golden Spoon Restaurant',
                'rating' => 5,
                'sort_order' => 1,
                'quote' => "Before Nexstack, closing the register at night took forever and numbers never quite matched. Now every branch syncs in real time and I can see today's sales from my phone before I even get to the restaurant.",
            ],
            [
                'author_name' => 'Dara Chan',
                'author_title' => 'Owner, Dara Coffee House',
                'rating' => 5,
                'sort_order' => 2,
                'quote' => 'The QR ordering feature alone paid for itself in the first month — fewer staff needed at peak hours and customers order faster than when they had to flag someone down.',
            ],
            [
                'author_name' => 'Bopha Kim',
                'author_title' => 'Manager, Bopha Retail Shop',
                'rating' => 4,
                'sort_order' => 3,
                'quote' => "Switching from paper stock counts to Nexstack's inventory tracking was the single best change we made this year. I finally know what's actually on the shelf.",
            ],
        ] as $t) {
            $testimonial = Testimonial::create([
                'author_name' => $t['author_name'],
                'author_title' => $t['author_title'],
                'product_id' => $pos?->id,
                'rating' => $t['rating'],
                'sort_order' => $t['sort_order'],
                'is_published' => true,
            ]);

            $testimonial->translations()->create([
                'locale' => 'en',
                'quote' => $t['quote'],
            ]);
        }
    }
}

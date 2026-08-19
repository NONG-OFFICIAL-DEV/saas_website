<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Solution;
use Illuminate\Database\Seeder;

class SolutionSeeder extends Seeder
{
    public function run(): void
    {
        $pos = Product::where('slug', 'nexstack-pos')->first();
        $studio = Product::where('slug', 'studio-management')->first();

        foreach ([
            ['photography-studio', 'mdi-camera-outline', 'Photography Studio', 'Bookings, packages, and client galleries in one place.', 'Run your photography studio without juggling spreadsheets — clients book their own slots, staff schedules stay conflict-free, and packages sell themselves.', [$studio]],
            ['coffee-shop', 'mdi-coffee-outline', 'Coffee Shop', 'Fast counter checkout with live stock on hand.', 'Ring up orders in seconds, keep an eye on milk and syrup stock across every location, and see which drinks are actually selling.', [$pos]],
            ['restaurant', 'mdi-silverware-fork-knife', 'Restaurant', 'Table orders, kitchen tickets, and QR ordering.', 'From table-side ordering to kitchen display to the bill, every order stays in sync — and customers can scan a QR code to order straight from their phone.', [$pos]],
            ['retail-store', 'mdi-storefront-outline', 'Retail Store', 'Barcode checkout with multi-branch inventory.', 'Sell by unit, pack, or box with barcode scanning, and keep stock levels accurate across every branch you run.', [$pos]],
            ['small-business', 'mdi-briefcase-outline', 'Small Business', 'Simple day-to-day operations, without the overhead.', 'Whatever you sell or book, get a system that fits how you actually work — not the other way around.', [$pos, $studio]],
            ['service-business', 'mdi-handshake-outline', 'Service Business', 'Bookings and payments for appointment-based work.', 'Let clients book themselves, keep staff schedules straight, and get paid without back-and-forth messages.', [$studio]],
        ] as $i => [$slug, $icon, $name, $tagline, $description, $products]) {
            $solution = Solution::create([
                'slug' => $slug,
                'icon' => $icon,
                'sort_order' => $i + 1,
                'is_published' => true,
            ]);

            $solution->translations()->create([
                'locale' => 'en',
                'name' => $name,
                'tagline' => $tagline,
                'description' => $description,
            ]);

            foreach (array_filter($products) as $j => $product) {
                $solution->products()->attach($product->id, ['sort_order' => $j + 1]);
            }
        }
    }
}

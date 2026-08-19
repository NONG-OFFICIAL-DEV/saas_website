<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

// Ports the exact current live content from the old Supabase-based CMS
// (supabase/schema.sql in the frontend repo) so nothing changes visually
// once the frontend is switched over to this backend.
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $pos = Product::create([
            'slug' => 'nexstack-pos',
            'status' => 'live',
            'cta_type' => 'register',
            'pricing_mode' => 'live',
            'accent_color' => '#3B5BDB',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $pos->translations()->create([
            'locale' => 'en',
            'name' => 'Nexstack POS',
            'tagline' => 'One system. Every branch. Zero guesswork.',
            'summary' => 'Point-of-sale, inventory, and multi-branch management for restaurants, cafes, and retail shops.',
            'description' => 'Nexstack POS keeps every branch of your restaurant, café, or retail store in perfect sync, in real time — from table orders to kitchen tickets to daily revenue. Built for Cambodian businesses with full Khmer and English support.',
            'cta_label' => 'Start Free Trial',
        ]);

        foreach ([
            ['mdi-cash-register', 'Fast checkout', 'Sell by unit, pack, or box with barcode scanning and multi-unit pricing.', 1],
            ['mdi-warehouse', 'Live inventory', 'Purchase orders, stock movements, and low-stock alerts across every branch.', 2],
            ['mdi-qrcode-scan', 'QR ordering', 'Customers scan, browse the menu, and order straight from their table.', 3],
            ['mdi-chart-line', 'Sales & profit reports', 'Daily, weekly, and monthly reports broken down by branch and staff.', 4],
        ] as [$icon, $title, $description, $sort]) {
            $feature = $pos->features()->create(['icon' => $icon, 'sort_order' => $sort]);
            $feature->translations()->create(['locale' => 'en', 'title' => $title, 'description' => $description]);
        }

        foreach ([
            ['Can I use Nexstack POS across multiple branches?', 'Yes — every branch stays in sync in real time, and you can view combined or per-branch reports from one account.'],
            ['Does it work offline?', 'Checkout keeps working during a connectivity drop and syncs automatically once you\'re back online.'],
            ['Is Khmer language supported?', 'Yes, the whole system — receipts, reports, and the customer-facing QR menu — supports both Khmer and English.'],
        ] as $i => [$question, $answer]) {
            $faq = $pos->faqs()->create(['sort_order' => $i + 1]);
            $faq->translations()->create(['locale' => 'en', 'question' => $question, 'answer' => $answer]);
        }

        $studio = Product::create([
            'slug' => 'studio-management',
            'status' => 'coming_soon',
            'cta_type' => 'waitlist',
            'pricing_mode' => 'live',
            'accent_color' => '#F59E0B',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        $studio->translations()->create([
            'locale' => 'en',
            'name' => 'Studio Management System',
            'tagline' => 'Bookings, staff, and payments in one place.',
            'summary' => 'Scheduling, staff, and client management built for studios — fitness, beauty, photography, and more.',
            'description' => 'Studio Management System helps studio owners run bookings, staff schedules, and client payments without juggling spreadsheets. Currently in development — join the waitlist to get early access.',
            'cta_label' => 'Join Waitlist',
        ]);

        foreach ([
            ['mdi-calendar-check', 'Online booking', 'Clients book available slots themselves — no back-and-forth messages.', 1],
            ['mdi-account-group', 'Staff scheduling', 'Assign staff to sessions and avoid double-bookings automatically.', 2],
            ['mdi-credit-card-outline', 'Payments & packages', 'Sell class packs, memberships, and single sessions in one place.', 3],
        ] as [$icon, $title, $description, $sort]) {
            $feature = $studio->features()->create(['icon' => $icon, 'sort_order' => $sort]);
            $feature->translations()->create(['locale' => 'en', 'title' => $title, 'description' => $description]);
        }

        foreach ([
            ['Who is Studio Management for?', 'Any studio that books appointments and sells packages — photography, fitness, beauty, and similar service businesses.'],
            ['Can clients book without calling or messaging?', 'Yes — clients pick an open slot from your live calendar and book themselves.'],
            ['When can I join?', 'Studio Management is currently in development. Join the waitlist and we\'ll reach out as soon as early access opens up.'],
        ] as $i => [$question, $answer]) {
            $faq = $studio->faqs()->create(['sort_order' => $i + 1]);
            $faq->translations()->create(['locale' => 'en', 'question' => $question, 'answer' => $answer]);
        }
    }
}

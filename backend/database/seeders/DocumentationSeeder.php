<?php

namespace Database\Seeders;

use App\Models\DocumentationArticle;
use App\Models\DocumentationCategory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DocumentationSeeder extends Seeder
{
    public function run(): void
    {
        $studio = Product::where('slug', 'studio-management')->first();
        $smartStore = Product::where('slug', 'nexstack-pos')->first();

        $categories = [
            ['getting-started', 'mdi-rocket-launch-outline', 'Getting Started', 'The first things to do after signing up.', null, 1],
            ['studio', 'mdi-camera-outline', 'Studio', 'Bookings, customers, and everything else in Studio.', $studio, 2],
            ['smart-store', 'mdi-storefront-outline', 'Smart Store', 'Products, POS, inventory, and sales.', $smartStore, 3],
            ['account-settings', 'mdi-cog-outline', 'Account & Settings', 'Your profile, business settings, and billing.', null, 4],
            ['troubleshooting', 'mdi-lifebuoy', 'Troubleshooting', 'Fixes for common issues.', null, 5],
        ];

        $categoryModels = [];
        foreach ($categories as [$slug, $icon, $name, $description, $product, $sort]) {
            $category = DocumentationCategory::create([
                'slug' => $slug,
                'icon' => $icon,
                'product_id' => $product?->id,
                'sort_order' => $sort,
                'is_active' => true,
            ]);
            $category->translations()->create(['locale' => 'en', 'name' => $name, 'description' => $description]);
            $categoryModels[$slug] = $category;
        }

        $tip = fn (string $html) => '<div data-type="callout" data-variant="tip"><p><strong>Tip</strong></p>'.$html.'</div>';
        $important = fn (string $html) => '<div data-type="callout" data-variant="important"><p><strong>Important</strong></p>'.$html.'</div>';
        $note = fn (string $html) => '<div data-type="callout" data-variant="note"><p><strong>Note</strong></p>'.$html.'</div>';

        $articles = [
            // ── Getting Started ─────────────────────────────────────────
            ['introduction', 'getting-started', null, 'Introduction',
                'What Nexstack is and how the documentation is organized.',
                '<p>Nexstack is a family of small, focused business tools — currently <strong>Studio</strong> (for photography and service businesses that take bookings) and <strong>Smart Store</strong> (point-of-sale for restaurants, cafés, and retail).</p><p>Each product is its own separate app with its own account. This documentation is organized the same way: general setup steps apply to any product, and each product has its own section for the features specific to it.</p>'.
                $note('<p>Not sure which product is right for your business? See the product pages, or reach out via the Help Center.</p>'),
                1],
            ['create-your-account', 'getting-started', null, 'Create your account',
                'How to sign up and provision your workspace.',
                '<ol><li>Choose a product from the <strong>Get Started</strong> page.</li><li>Fill in your business name and a few basic details.</li><li>Enter your name, email, and a password.</li><li>Submit the form — your workspace is created automatically inside that product.</li><li>You\'ll be taken straight to that product\'s own login page to sign in with the email and password you just chose.</li></ol>'.
                $tip('<p>You can pick up where you left off if you accidentally close the tab before finishing — your progress is saved in your browser.</p>'),
                2],
            ['set-up-your-business', 'getting-started', null, 'Set up your business',
                'The first settings worth configuring after signing up.',
                '<p>Once you\'re logged in, spend a few minutes on these before your team starts using the system day-to-day:</p><ul><li>Business name, logo, and contact details</li><li>Currency, timezone, and locale</li><li>Staff accounts and their roles/permissions</li><li>Anything product-specific — see the Studio or Smart Store sections</li></ul>',
                3],
            ['getting-started-guide', 'getting-started', null, 'Getting started guide',
                'A four-step overview from signup to your first real transaction.',
                '<ol><li><strong>Create your account</strong> — pick a product and sign up.</li><li><strong>Set up your business</strong> — logo, currency, staff.</li><li><strong>Configure your product</strong> — add your services/products, categories, and pricing.</li><li><strong>Start using your system</strong> — take your first booking or process your first sale.</li></ol>',
                4],

            // ── Studio ───────────────────────────────────────────────────
            ['studio-dashboard', 'studio', $studio, 'Dashboard',
                'What the Studio dashboard shows at a glance.',
                '<p>The dashboard is the home screen after logging in — it summarizes upcoming bookings, recent invoices, and overall activity so you can see what needs attention without digging through every section.</p>',
                1],
            ['studio-customers', 'studio', $studio, 'Adding customers',
                'How to create and manage customer profiles.',
                '<ol><li>Open <strong>Customers</strong> from the sidebar.</li><li>Click <strong>New Customer</strong>.</li><li>Enter their name, email, and phone number.</li><li>Click <strong>Save</strong>.</li></ol><p>Once a customer exists, you can select them directly when creating a booking instead of retyping their details each time.</p>',
                2],
            ['studio-services-and-packages', 'studio', $studio, 'Services & packages',
                'Setting up what you offer and how it\'s priced.',
                '<p>Before you can take bookings, add the services or packages you offer:</p><ol><li>Open <strong>Services & Packages</strong> from the sidebar.</li><li>Click <strong>New Service</strong> (or <strong>New Package</strong> to bundle several services together).</li><li>Set a name, description, price, and duration.</li><li>Save it — it now appears as an option when creating a booking.</li></ol>',
                3],
            ['studio-bookings', 'studio', $studio, 'Managing bookings',
                'Learn how to create, update, and cancel customer bookings.',
                '<h3>Create a booking</h3><ol><li>Open <strong>Bookings</strong> from the sidebar.</li><li>Click <strong>New Booking</strong>.</li><li>Select a customer.</li><li>Select a service or package.</li><li>Choose the date and time.</li><li>Click <strong>Create Booking</strong>.</li></ol><h3>Update a booking</h3><p>Open the booking from the <strong>Bookings</strong> list, change the date, time, service, or customer, then save.</p><h3>Cancel a booking</h3><p>Open the booking and choose <strong>Cancel</strong>. The customer keeps their history, but the slot becomes available again.</p>'.
                $tip('<p>Double-booking the same time slot is blocked automatically — if a slot looks unavailable, check whether another booking already exists for it.</p>'),
                4],
            ['studio-orders', 'studio', $studio, 'Orders',
                'Tracking what a client has purchased.',
                '<p>An order records what a customer has purchased — a package, add-ons, or extra items tied to one of their bookings. Open a customer or booking to see their order history alongside it.</p>',
                5],
            ['studio-invoices', 'studio', $studio, 'Invoices',
                'Billing customers for services and packages.',
                '<ol><li>Open <strong>Invoices</strong> from the sidebar.</li><li>Click <strong>New Invoice</strong> and select the customer and booking/order it\'s for.</li><li>Review the line items and total.</li><li>Send it to the customer, or mark it paid once you\'ve been paid outside the system.</li></ol>'.
                $important('<p>Make sure your business currency is set correctly in Business Settings before sending your first invoice — changing it later won\'t update invoices already sent.</p>'),
                6],
            ['studio-reports', 'studio', $studio, 'Reports',
                'Where to see your studio\'s activity over time.',
                '<p>Reports summarize bookings and revenue over a date range you choose, so you can see trends (busiest days, most-booked services) without exporting anything manually.</p>',
                7],

            // ── Smart Store ─────────────────────────────────────────────
            ['smart-store-dashboard', 'smart-store', $smartStore, 'Dashboard',
                'What the Smart Store dashboard shows at a glance.',
                '<p>The dashboard summarizes today\'s sales, low-stock alerts, and recent orders across your branches, so you can spot problems (like a product about to run out) before they affect a sale.</p>',
                1],
            ['smart-store-products', 'smart-store', $smartStore, 'Adding products',
                'How to add products, set prices, and organize them into categories.',
                '<ol><li>Open <strong>Products</strong> from the sidebar.</li><li>Click <strong>New Product</strong>.</li><li>Enter a name, category, and price.</li><li>Add any variants (size, flavor) or units (piece, box) if it\'s sold more than one way.</li><li>Click <strong>Save</strong>.</li></ol>',
                2],
            ['smart-store-categories', 'smart-store', $smartStore, 'Categories',
                'Organizing your menu or catalog for faster checkout.',
                '<p>Categories group related products together (e.g. Drinks, Mains, Snacks) so staff can find items quickly at the POS screen instead of scrolling one long list. Create a category first, then assign products to it while adding or editing them.</p>',
                3],
            ['smart-store-inventory', 'smart-store', $smartStore, 'Managing inventory',
                'Tracking stock levels and restocking.',
                '<ol><li>Open <strong>Inventory</strong> from the sidebar to see current stock levels per product.</li><li>Record a <strong>Stock Adjustment</strong> when you count physical stock and it doesn\'t match the system.</li><li>Create a <strong>Purchase Order</strong> to a supplier when you need to restock, and receive it once the goods arrive to update stock automatically.</li></ol>'.
                $note('<p>Low-stock thresholds can be set per product so you get an alert before you actually run out.</p>'),
                4],
            ['smart-store-pos', 'smart-store', $smartStore, 'Processing a sale',
                'The checkout flow at the POS screen.',
                '<ol><li>Open the <strong>POS</strong> screen.</li><li>Tap products (or select a table, for dine-in) to add them to the order.</li><li>Adjust quantities or add modifiers if needed.</li><li>Tap <strong>Charge</strong> and choose a payment method.</li><li>Complete the payment — a receipt is generated automatically.</li></ol>',
                5],
            ['smart-store-orders', 'smart-store', $smartStore, 'Orders',
                'Viewing and managing order history.',
                '<p>Every sale — dine-in, takeaway, or online — appears in <strong>Orders</strong> with its status, items, and total. Use it to look up a past sale, check what\'s still in the kitchen queue, or issue a refund.</p>',
                6],
            ['smart-store-payments', 'smart-store', $smartStore, 'Payments',
                'Accepted payment methods and the cash drawer.',
                '<p>Payments can be taken by cash or card at checkout. If you track a physical cash drawer, open it at the start of a shift and close it at the end — the system reconciles expected vs. counted cash automatically.</p>',
                7],
            ['smart-store-reports', 'smart-store', $smartStore, 'Reports',
                'Daily sales summaries and product performance.',
                '<p>Reports show daily sales totals, best-selling products, and trends across branches — use them to decide what to restock or which branch needs attention.</p>',
                8],

            // ── Account & Settings ───────────────────────────────────────
            ['profile', 'account-settings', null, 'Profile',
                'Updating your name, email, and password.',
                '<p>Open your account menu and choose <strong>Profile</strong> to update your name, email, phone number, or password. Changing your email may require confirming it again.</p>',
                1],
            ['business-settings', 'account-settings', null, 'Business settings',
                'Configuring your business name, logo, currency, and timezone.',
                '<p>Business Settings controls the details that apply across your whole account: business name, logo, currency, timezone, and locale. Update these early — some, like currency, are best set before you start invoicing or selling.</p>',
                2],
            ['users-and-permissions', 'account-settings', null, 'Users & permissions',
                'Adding staff accounts and controlling what they can access.',
                '<ol><li>Open <strong>Users</strong> (or <strong>Staff</strong>) from Settings.</li><li>Click <strong>Add User</strong> and enter their email and role.</li><li>They\'ll receive access based on the role you assign — e.g. Owner, Manager, or Staff.</li></ol>'.
                $note('<p>Roles determine which sections of the system a user can see and edit. Give staff the least access they need to do their job.</p>'),
                3],
            ['subscription-and-billing', 'account-settings', null, 'Subscription & billing',
                'Viewing your plan and managing billing.',
                '<p>Your current plan, its features, and billing history live under Subscription & Billing. Upgrading or downgrading takes effect based on your billing cycle — check there for the exact details before switching plans.</p>',
                4],

            // ── Troubleshooting ──────────────────────────────────────────
            ['common-issues', 'troubleshooting', null, 'Common issues',
                'Quick fixes for the most frequent problems.',
                '<ul><li><strong>Can\'t log in?</strong> Double-check you\'re on the right product\'s login page — each product has its own.</li><li><strong>Changes not saving?</strong> Check your internet connection and try again; unsaved changes are not retried automatically.</li><li><strong>Missing a feature?</strong> Confirm your plan includes it under Subscription & Billing.</li></ul>',
                1],
            ['printing-problems', 'troubleshooting', null, 'Printing problems',
                'Fixing receipt or invoice printing issues.',
                '<p>If a receipt or invoice won\'t print:</p><ol><li>Confirm the printer is powered on and connected.</li><li>Try printing a test page from your device\'s system settings, outside the app.</li><li>Reload the page and try again — a stuck browser tab is a common cause.</li></ol>',
                2],
            ['payment-problems', 'troubleshooting', null, 'Payment problems',
                'What to do when a payment fails or looks wrong.',
                '<p>If a payment fails at checkout, don\'t retry immediately if you\'re unsure whether it went through — check the order/invoice status first to avoid charging twice. If a completed payment looks incorrect, open the order or invoice to review its line items before making any correction.</p>',
                3],
            ['contact-support', 'troubleshooting', null, 'Contact support',
                'How to reach out when this documentation doesn\'t answer your question.',
                '<p>If you can\'t find what you need here, visit the <strong>Help Center</strong> for FAQs and direct contact options (email or Telegram) — every message is read and answered personally.</p>',
                4],
        ];

        foreach ($articles as $i => [$slug, $categorySlug, $product, $title, $excerpt, $content, $sort]) {
            $article = DocumentationArticle::create([
                'slug' => $slug,
                'category_id' => $categoryModels[$categorySlug]->id,
                'product_id' => $product?->id,
                'status' => 'published',
                'sort_order' => $sort,
                'published_at' => now(),
            ]);

            $article->translations()->create([
                'locale' => 'en',
                'title' => $title,
                'excerpt' => $excerpt,
                'content' => $content,
            ]);
        }
    }
}

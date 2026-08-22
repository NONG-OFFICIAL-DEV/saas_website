<?php

namespace Database\Seeders;

use App\Models\SiteContentBlock;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $hero = SiteContentBlock::create([
            'key' => 'hero',
            'data' => [
                'cta_secondary_url' => 'https://t.me/Nong_Phloeut',
            ],
        ]);
        $hero->translations()->create([
            'locale' => 'en',
            'content' => [
                'badge_text' => '🇰🇭 Independent SaaS builder',
                'headline' => 'One Builder.',
                'subheadline' => 'Multiple Products, Real Support.',
                'description' => 'I design, build, and personally support small SaaS products for real businesses — starting with point-of-sale, and growing from there.',
                'trust_line' => 'No agencies · No account managers · Direct support from the person who builds it',
                'cta_primary_label' => 'View Products',
                'cta_secondary_label' => 'Talk to Me',
                'stats' => [
                    ['num' => '2+', 'label' => 'Products Live'],
                    ['num' => '300+', 'label' => 'Businesses Served'],
                    ['num' => '24/7', 'label' => 'Direct Support'],
                    ['num' => '🇰🇭', 'label' => 'Built in Cambodia'],
                ],
            ],
        ]);

        $about = SiteContentBlock::create([
            'key' => 'about',
            'data' => [
                'email' => 'phloeutnong@gmail.com',
                'profile_photo_url' => '',
                'socials' => [
                    ['name' => 'Email', 'href' => 'mailto:phloeutnong@gmail.com'],
                    ['name' => 'Telegram', 'href' => 'https://t.me/Nong_Phloeut'],
                    ['name' => 'Facebook', 'href' => 'https://www.facebook.com/share/18nWjqydNc/?mibextid=wwXIfr'],
                    ['name' => 'TikTok', 'href' => 'https://www.tiktok.com/@nexstack.tech?_r=1'],
                ],
            ],
        ]);
        $about->translations()->create([
            'locale' => 'en',
            'content' => [
                'hero_tag' => 'About',
                'hero_heading' => 'Building simple software for real businesses.',
                'hero_description' => "I'm an independent software developer focused on creating practical tools that help small businesses manage their daily operations more easily.",
                'hero_cta_primary_label' => 'Explore My Products',
                'hero_cta_secondary_label' => 'Contact Me',

                'story_title' => 'Why I build these products',
                'story_content' => "Many small businesses still rely on notebooks, spreadsheets, chat messages, or several disconnected tools to manage their daily operations.\n\nI wanted to build something simpler — software that business owners can understand, use, and grow with without needing technical knowledge.",

                'approach_tag' => 'My Approach',
                'approach_title' => 'How I build',
                'approach_cards' => [
                    ['icon' => 'mdi-lightbulb-outline', 'title' => 'Simple', 'description' => "Software should be easy to understand, even if you're not technical."],
                    ['icon' => 'mdi-target', 'title' => 'Practical', 'description' => 'I focus on solving real business problems rather than adding unnecessary complexity.'],
                    ['icon' => 'mdi-currency-usd', 'title' => 'Affordable', 'description' => 'I want useful business software to be accessible to small and growing businesses.'],
                    ['icon' => 'mdi-refresh', 'title' => 'Always Improving', 'description' => 'Products evolve through real feedback, testing, and continuous improvement.'],
                ],

                'audience_title' => 'Built for small businesses',
                'audience_description' => "Technology shouldn't be the difficult part of running a business.",
                'audience_examples' => [
                    ['icon' => 'mdi-camera-outline', 'label' => 'Photography Studios', 'description' => 'Manage bookings, packages, and client galleries in one simple system.', 'image_url' => 'https://jonathantaylorphotography.com/wp-content/uploads/2019/01/photography-studio-rental-bangkok.jpg', 'featured' => false],
                    ['icon' => 'mdi-coffee-outline', 'label' => 'Coffee Shops', 'description' => 'Quick order taking, till management, and simple daily sales tracking.', 'image_url' => '', 'featured' => false],
                    ['icon' => 'mdi-silverware-fork-knife', 'label' => 'Restaurants', 'description' => 'Table orders, kitchen display, and multi-branch management in one POS.', 'image_url' => '', 'featured' => true],
                    ['icon' => 'mdi-storefront-outline', 'label' => 'Retail Stores', 'description' => 'Track inventory, ring up sales, and know what\'s moving off the shelves.', 'image_url' => '', 'featured' => false],
                    ['icon' => 'mdi-store-outline', 'label' => 'Small Businesses', 'description' => 'Any business that needs simple, reliable tools without the complexity.', 'image_url' => '', 'featured' => false],
                ],

                'products_tag' => 'What I Build',
                'products_title' => 'What I build',
                'products_description' => 'I create practical software designed around real business workflows.',

                'profile_greeting' => "Hi, I'm Nong.",
                'profile_name' => 'Nong Phloeut',
                'profile_bio' => "I'm a software developer who enjoys turning real-world business problems into simple software products.",
                'profile_skills' => [],

                'cta_title' => 'Have a business problem I can help solve?',
                'cta_description' => 'Whether you need a business management system or have an idea for custom software, feel free to get in touch.',
                'cta_primary_label' => 'Contact Me',
                'cta_secondary_label' => 'Explore Products',
            ],
        ]);

        $footer = SiteContentBlock::create([
            'key' => 'footer',
            'data' => [
                'email' => 'phloeutnong@gmail.com',
                'phone' => '066 53 86 01',
                'socials' => [
                    ['name' => 'Telegram', 'href' => 'https://t.me/Nong_Phloeut'],
                    ['name' => 'Facebook', 'href' => 'https://www.facebook.com/share/18nWjqydNc/?mibextid=wwXIfr'],
                    ['name' => 'TikTok', 'href' => 'https://www.tiktok.com/@nexstack.tech?_r=1'],
                ],
            ],
        ]);
        $footer->translations()->create([
            'locale' => 'en',
            'content' => [
                'address' => 'Phnom Penh, Cambodia',
            ],
        ]);
    }
}

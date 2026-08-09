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
                'tag' => 'About',
                'greeting' => "Hi, I'm Nong Phloeut",
                'bio' => "I'm an independent developer who builds and runs small, focused SaaS products — Nexstack POS is the first, with more on the way. No agency, no account managers — just me building tools I'd want to use myself, and supporting the businesses that run on them.",
                'cta_label' => "See what I've built",
                'how_tag' => 'How I work',
                'how_title' => 'What you can expect',
                'values' => [
                    ['icon' => 'mdi-account-check-outline', 'title' => 'Direct support', 'description' => "You're talking to the person who built the product, not a support queue."],
                    ['icon' => 'mdi-rocket-launch-outline', 'title' => 'Ship, then improve', 'description' => 'Products launch focused and improve steadily based on real usage, not guesswork.'],
                    ['icon' => 'mdi-map-marker-outline', 'title' => 'Built for local businesses', 'description' => 'Designed with Cambodian small businesses in mind — pricing, language, and workflow included.'],
                ],
                'talk_title' => "Let's talk",
                'talk_description' => "Questions about a product, or curious what's next? Reach out directly — I read and reply to every message myself.",
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

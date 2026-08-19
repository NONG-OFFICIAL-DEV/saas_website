<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            [
                'slug' => 'closing-the-register-faster',
                'author_name' => 'Nong Phloeut',
                'published_at' => now()->subDays(10),
                'title' => 'Three ways to close the register faster every night',
                'excerpt' => 'Small changes to your end-of-day routine that save real time across every branch.',
                'content' => "Closing the register shouldn't take longer than the shift itself. Here are three changes that make the biggest difference: reconcile per-branch instead of waiting for a combined report, let staff submit their own cash counts from their phone, and review yesterday's numbers before today's shift starts instead of after it ends.",
            ],
            [
                'slug' => 'qr-ordering-worth-it',
                'author_name' => 'Nong Phloeut',
                'published_at' => now()->subDays(3),
                'title' => 'Is QR ordering actually worth it for a small restaurant?',
                'excerpt' => 'What we saw after a dozen small restaurants switched on table-side QR ordering.',
                'content' => 'Short answer: yes, if your peak hours regularly leave customers waiting to order. QR ordering removes that bottleneck entirely — customers order the moment they sit down, and kitchen tickets print before a server ever reaches the table.',
            ],
        ] as $p) {
            $post = BlogPost::create([
                'slug' => $p['slug'],
                'author_name' => $p['author_name'],
                'published_at' => $p['published_at'],
                'is_published' => true,
            ]);

            $post->translations()->create([
                'locale' => 'en',
                'title' => $p['title'],
                'excerpt' => $p['excerpt'],
                'content' => $p['content'],
            ]);
        }
    }
}

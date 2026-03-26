<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\BlogContent;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $blogs = [
            [
                'title'     => 'The Ultimate Guide to Choosing Your Next Smartphone',
                'thumbnail' => null,
                'contents'  => [
                    [
                        'heading' => 'Determine Your Budget',
                        'content' => 'Before diving into the sea of smartphones, it is crucial to set a budget. High-end flagships offer the best features but come at a premium price.',
                        'order'   => 1,
                    ],
                    [
                        'heading' => 'Operating System: iOS vs Android',
                        'content' => 'Choose between the simplicity and ecosystem of iOS or the customization and variety of Android.',
                        'order'   => 2,
                    ],
                ]
            ],
            [
                'title'     => 'Why Second-Hand Phones Are a Great Choice in 2024',
                'thumbnail' => null,
                'contents'  => [
                    [
                        'heading' => 'Cost Savings',
                        'content' => 'You can get a previous year flagship at a fraction of its original price.',
                        'order'   => 1,
                    ],
                    [
                        'heading' => 'Environmental Impact',
                        'content' => 'Buying used helps reduce electronic waste and is better for the planet.',
                        'order'   => 2,
                    ],
                ]
            ],
            [
                'title'     => 'Maintaining Your Device for Longevity',
                'thumbnail' => null,
                'contents'  => [
                    [
                        'heading' => 'Battery Care',
                        'content' => 'Avoid letting your battery drop to 0% and try to keep it between 20% and 80% for maximum lifespan.',
                        'order'   => 1,
                    ],
                    [
                        'heading' => 'Software Updates',
                        'content' => 'Always keep your device updated to the latest software version for security and performance improvements.',
                        'order'   => 2,
                    ],
                ]
            ],
        ];

        foreach ($blogs as $blogData) {
            $contents = $blogData['contents'];
            unset($blogData['contents']);

            $blog = Blog::updateOrCreate(
                ['title' => $blogData['title']],
                $blogData
            );

            foreach ($contents as $content) {
                BlogContent::updateOrCreate(
                    [
                        'blog_id' => $blog->id,
                        'heading' => $content['heading']
                    ],
                    $content
                );
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BoutiqueProduct;

class BoutiqueProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Announcement',
                'slug' => 'announcement',
                'category' => 'announcements',
                'type' => 'announcement',
                'description' => 'Get advertising space for 30 days to publish your ad.',
                'price' => 3000,
                'currency' => 'MAD',
                'duration_days' => 30,
                'badge' => null,
                'is_recommended' => false,
                'requires_property' => false,
                'requires_published_property' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Hot Ad',
                'slug' => 'hot-ad',
                'category' => 'announcements',
                'type' => 'hot-ad',
                'description' => 'Get advertising space for 30 days and place your ad above the regular ads.',
                'price' => 7800,
                'currency' => 'MAD',
                'duration_days' => 30,
                'badge' => null,
                'is_recommended' => false,
                'requires_property' => true,
                'requires_published_property' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Super Hot Ad',
                'slug' => 'super-hot-ad',
                'category' => 'announcements',
                'type' => 'super-hot-ad',
                'description' => 'Get a 30-day advertising placement and place your ad at the top of search results.',
                'price' => 21000,
                'currency' => 'MAD',
                'duration_days' => 30,
                'badge' => null,
                'is_recommended' => false,
                'requires_property' => true,
                'requires_published_property' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Refreshment credits',
                'slug' => 'refreshment-credits',
                'category' => 'credits',
                'type' => 'refreshment-credits',
                'description' => 'Update the time of your published ads and bring them back to the top.',
                'price' => 240,
                'currency' => 'MAD',
                'duration_days' => null,
                'badge' => null,
                'is_recommended' => false,
                'requires_property' => true,
                'requires_published_property' => true,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Credits for ads in story',
                'slug' => 'credits-for-ads-in-story',
                'category' => 'credits',
                'type' => 'story-credits',
                'description' => 'Gain more visibility by posting your ad in your story.',
                'price' => 1000,
                'currency' => 'MAD',
                'duration_days' => null,
                'badge' => null,
                'is_recommended' => false,
                'requires_property' => true,
                'requires_published_property' => true,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Credits for verified photograph',
                'slug' => 'credits-for-verified-photograph',
                'category' => 'credits',
                'type' => 'verified-photograph',
                'description' => 'Enhance the visual appeal of your property with our premium professional photography service.',
                'price' => 3600,
                'currency' => 'MAD',
                'duration_days' => null,
                'badge' => 'Recommended',
                'is_recommended' => true,
                'requires_property' => true,
                'requires_published_property' => true,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Credits for verified videography',
                'slug' => 'credits-for-verified-videography',
                'category' => 'credits',
                'type' => 'verified-videography',
                'description' => 'Bring your property to life with our captivating videography service.',
                'price' => 12000,
                'currency' => 'MAD',
                'duration_days' => null,
                'badge' => 'Recommended',
                'is_recommended' => true,
                'requires_property' => true,
                'requires_published_property' => true,
                'is_active' => true,
                'sort_order' => 7,
            ],
        ];

        foreach ($products as $product) {
            BoutiqueProduct::updateOrCreate(
                ['slug' => $product['slug']],
                $product
            );
        }
    }
}
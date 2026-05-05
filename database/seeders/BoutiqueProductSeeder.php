<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BoutiqueProduct;

class BoutiqueProductSeeder extends Seeder
{
    public function run(): void
    {
        BoutiqueProduct::truncate();

        $products = [
            // Announcements
            [
                'name'                       => 'Announcement',
                'description'                => 'Get advertising space for 30 days to publish your ad.',
                'price'                      => 3000,
                'currency'                   => 'MAD',
                'type'                       => 'announcement',
                'category'                   => 'announcements',
                'requires_property'          => false,
                'requires_published_property'=> false,
                'sort_order'                 => 1,
            ],
            [
                'name'                       => 'Hot Ad',
                'description'                => 'Get advertising space for 30 days and place your ad above the regular ads.',
                'price'                      => 7800,
                'currency'                   => 'MAD',
                'type'                       => 'hot-ad',
                'category'                   => 'announcements',
                'badge'                      => 'Published property required',
                'requires_published_property'=> true,
                'sort_order'                 => 2,
            ],
            [
                'name'                       => 'Super Hot Ad',
                'description'                => 'Get a 30-day advertising placement and place your ad at the top of search results.',
                'price'                      => 21000,
                'currency'                   => 'MAD',
                'type'                       => 'super-hot-ad',
                'category'                   => 'announcements',
                'badge'                      => 'Published property required',
                'requires_published_property'=> true,
                'sort_order'                 => 3,
            ],

            // Credits
            [
                'name'                       => 'Refreshment credits',
                'description'                => 'Update the time of your published ads and bring them back to the top.',
                'price'                      => 240,
                'currency'                   => 'MAD',
                'type'                       => 'refresh-credit',
                'category'                   => 'credits',
                'badge'                      => 'Published property required',
                'requires_published_property'=> true,
                'sort_order'                 => 1,
            ],
            [
                'name'                       => 'Credits for ads in story',
                'description'                => 'Gain more visibility by posting your ad in your story.',
                'price'                      => 1000,
                'currency'                   => 'MAD',
                'type'                       => 'story-credit',
                'category'                   => 'credits',
                'badge'                      => 'Published property required',
                'requires_published_property'=> true,
                'sort_order'                 => 2,
            ],
            [
                'name'                       => 'Credits for verified photograph',
                'description'                => 'Enhance the visual appeal of your property with our premium professional photography service.',
                'price'                      => 3600,
                'currency'                   => 'MAD',
                'type'                       => 'photo-credit',
                'category'                   => 'credits',
                'badge'                      => 'Published property required',
                'is_recommended'             => true,
                'requires_published_property'=> true,
                'sort_order'                 => 3,
            ],
            [
                'name'                       => 'Credits for verified videography',
                'description'                => 'Bring your property to life with our captivating videography service.',
                'price'                      => 12000,
                'currency'                   => 'MAD',
                'type'                       => 'video-credit',
                'category'                   => 'credits',
                'badge'                      => 'Published property required',
                'is_recommended'             => true,
                'requires_published_property'=> true,
                'sort_order'                 => 4,
            ],
        ];

        foreach ($products as $product) {
            BoutiqueProduct::create($product);
        }
    }
}
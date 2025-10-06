<?php

namespace Database\Seeders;

use App\Models\AffiliateLink;
use Illuminate\Database\Seeder;

class AffiliateLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $links = [
            [
                'platform' => 'netflix',
                'name' => 'Netflix',
                'url' => 'https://www.netflix.com/sk/',
                'active' => true,
                'sort_order' => 1,
            ],
            [
                'platform' => 'hbo',
                'name' => 'HBO Max',
                'url' => 'https://www.hbomax.com/',
                'active' => true,
                'sort_order' => 2,
            ],
            [
                'platform' => 'disney',
                'name' => 'Disney+',
                'url' => 'https://www.disneyplus.com/',
                'active' => true,
                'sort_order' => 3,
            ],
            [
                'platform' => 'amazon',
                'name' => 'Amazon Prime Video',
                'url' => 'https://www.primevideo.com/',
                'active' => true,
                'sort_order' => 4,
            ],
            [
                'platform' => 'apple',
                'name' => 'Apple TV+',
                'url' => 'https://tv.apple.com/',
                'active' => true,
                'sort_order' => 5,
            ],
            [
                'platform' => 'max',
                'name' => 'Max',
                'url' => 'https://www.max.com/',
                'active' => true,
                'sort_order' => 6,
            ],
            [
                'platform' => 'paramount',
                'name' => 'Paramount+',
                'url' => 'https://www.paramountplus.com/',
                'active' => true,
                'sort_order' => 7,
            ],
            [
                'platform' => 'peacock',
                'name' => 'Peacock',
                'url' => 'https://www.peacocktv.com/',
                'active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($links as $link) {
            AffiliateLink::create($link);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            // České/Slovenské zdroje (menej)
            [
                'name' => 'ČSFD.cz',
                'url' => 'https://www.csfd.cz/',
                'type' => 'review',
                'language' => 'cs',
                'active' => true,
            ],
            [
                'name' => 'TotalFilm.cz',
                'url' => 'https://www.totalfilm.cz/',
                'type' => 'review',
                'language' => 'cs',
                'active' => true,
            ],
            [
                'name' => 'Kinema.sk',
                'url' => 'https://www.kinema.sk/',
                'type' => 'news',
                'language' => 'sk',
                'active' => true,
            ],

            // Anglické zdroje - hlavné filmové weby
            [
                'name' => 'IMDb News',
                'url' => 'https://www.imdb.com/news/',
                'type' => 'news',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'Rotten Tomatoes',
                'url' => 'https://www.rottentomatoes.com/',
                'type' => 'review',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'The Movie Database',
                'url' => 'https://www.themoviedb.org/',
                'type' => 'streaming',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'Variety',
                'url' => 'https://variety.com/',
                'type' => 'news',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'The Hollywood Reporter',
                'url' => 'https://www.hollywoodreporter.com/',
                'type' => 'news',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'Deadline Hollywood',
                'url' => 'https://deadline.com/',
                'type' => 'news',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'IndieWire',
                'url' => 'https://www.indiewire.com/',
                'type' => 'news',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'Screen Daily',
                'url' => 'https://www.screendaily.com/',
                'type' => 'news',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'Collider',
                'url' => 'https://collider.com/',
                'type' => 'news',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'Empire',
                'url' => 'https://www.empireonline.com/movies/',
                'type' => 'review',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'Film Comment',
                'url' => 'https://www.filmcomment.com/',
                'type' => 'review',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'SlashFilm',
                'url' => 'https://www.slashfilm.com/',
                'type' => 'news',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'Joblo',
                'url' => 'https://www.joblo.com/',
                'type' => 'news',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'MovieWeb',
                'url' => 'https://movieweb.com/',
                'type' => 'news',
                'language' => 'en',
                'active' => true,
            ],

            // Streamingové služby
            [
                'name' => 'Netflix News',
                'url' => 'https://about.netflix.com/en/news',
                'type' => 'streaming',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'HBO Max News',
                'url' => 'https://www.max.com/news',
                'type' => 'streaming',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'Disney+ News',
                'url' => 'https://www.disneyplus.com/news',
                'type' => 'streaming',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'Apple TV+ News',
                'url' => 'https://tv.apple.com/news',
                'type' => 'streaming',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'Prime Video News',
                'url' => 'https://www.primevideo.com/news',
                'type' => 'streaming',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'Hulu News',
                'url' => 'https://www.hulu.com/press',
                'type' => 'streaming',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'Paramount+ News',
                'url' => 'https://www.paramountplus.com/news',
                'type' => 'streaming',
                'language' => 'en',
                'active' => true,
            ],
            [
                'name' => 'Peacock News',
                'url' => 'https://www.peacocktv.com/news',
                'type' => 'streaming',
                'language' => 'en',
                'active' => true,
            ],
        ];

        foreach ($sources as $source) {
            \App\Models\Source::create($source);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            [
                'name' => 'Jan Filmový',
                'slug' => 'jan-filmovy',
                'bio' => 'Profesionálny filmový kritik s viac ako 15-ročnými skúsenosťami v oblasti kinematografie. Špecializuje sa na analýzu réžie, kinematografie a autorských vízií. Pravidelne navštevuje filmové festivaly a venuje sa hlbšej analýze filmových diel.',
                'specialization' => 'professional_critic',
                'personality_prompt' => 'Profesionálny filmový kritik s analytickým prístupom. Sústreďuje sa na réžiu, kinematografiu a autorskú víziu. Je objektívny ale má rád kvalitné filmy.',
                'writing_style_prompt' => 'Analytický, sofistikovaný štýl písania. Používa filmovú terminológiu, ale zostáva prístupný pre širokú verejnosť. Uprednostňuje hlbšiu analýzu pred povrchnými názormi.',
                'active' => true,
            ],
            [
                'name' => 'Marie Popkultúra',
                'slug' => 'marie-popkultura',
                'bio' => 'Nadšená bloggerka a milovníčka popkultúry. Sleduje všetky blockbustery, franšízy a streamovací obsah. Má rada zábavu a vie oceniť aj menej náročné filmy, ktoré prinášajú radosť.',
                'specialization' => 'enthusiastic_blogger',
                'personality_prompt' => 'Nadšená bloggerka popkultúry. Miluje blockbustery, franšízy a streamovací obsah. Je priateľská a prístupná, rada odporúča filmy na sledovanie.',
                'writing_style_prompt' => 'Konverzačný, vtipný štýl s popkultúrnymi referenciami. Priateľský a prístupný tón, ktorý láka čitateľov. Vie oceniť aj menej náročné filmy.',
                'active' => true,
            ],
            [
                'name' => 'Tomáš Žáner',
                'slug' => 'tomas-zaner',
                'specialization' => 'skeptical_expert',
                'bio' => 'Skeptický expert na žánrové filmy. Špecialista na horor, thriller a sci-fi s osobitým zmyslom pre čierny humor. Nebojí sa kontroverzných názorov a často objavuje skryté poklady v žánroch.',
                'personality_prompt' => 'Skeptický expert na žánre. Špecialista na horor, thriller a sci-fi s čiernym humorom. Nebojí sa kontroverzných názorov a má rád nekonvenčné filmy.',
                'writing_style_prompt' => 'Kritický, sarkastický štýl s čiernym humorom. Nebojí sa kontroverzných názorov a často ide proti prúdu. Uprednostňuje autentické názory pred politickou korektnosťou.',
                'active' => true,
            ],
        ];

        foreach ($authors as $author) {
            \App\Models\Author::create($author);
        }
    }
}

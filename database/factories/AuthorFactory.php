<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $authors = [
            [
                'name' => 'Jan Filmový',
                'slug' => 'jan-filmovy',
                'specialization' => 'professional_critic',
                'personality_prompt' => 'Profesionálny filmový kritik s analytickým prístupom. Sústreďuje sa na réžiu, kinematografiu a autorskú víziu.',
                'writing_style_prompt' => 'Analytický, sofistikovaný štýl písania. Používa filmovú terminológiu, ale zostáva prístupný.',
            ],
            [
                'name' => 'Marie Popkultúra',
                'slug' => 'marie-popkultura',
                'specialization' => 'enthusiastic_blogger',
                'personality_prompt' => 'Nadšená bloggerka popkultúry. Miluje blockbustery, franšízy a streamovací obsah.',
                'writing_style_prompt' => 'Konverzačný, vtipný štýl s popkultúrnymi referenciami. Priateľský a prístupný.',
            ],
            [
                'name' => 'Tomáš Žáner',
                'slug' => 'tomas-zaner',
                'specialization' => 'skeptical_expert',
                'personality_prompt' => 'Skeptický expert na žánre. Špecialista na horor, thriller a sci-fi s čiernym humorom.',
                'writing_style_prompt' => 'Kritický, sarkastický štýl. Čierny humor, nebojí sa kontroverzných názorov.',
            ],
        ];

        $author = fake()->randomElement($authors);

        return [
            'name' => $author['name'],
            'slug' => $author['slug'],
            'bio' => fake()->paragraph(3),
            'avatar_path' => null, // Will be set later
            'specialization' => $author['specialization'],
            'personality_prompt' => $author['personality_prompt'],
            'writing_style_prompt' => $author['writing_style_prompt'],
            'active' => true,
        ];
    }
}

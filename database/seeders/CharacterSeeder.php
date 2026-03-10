<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Character;
use Illuminate\Support\Str;

class CharacterSeeder extends Seeder
{
    public function run(): void
    {
        $globals = [
            [
                'name'        => 'Alicia',
                'slug'        => 'alicia-the-economist',
                'description' => 'A calm, relatable 30-year-old financial educator who makes complex money concepts feel accessible. She\'s the "boring but rich" archetype — living below her means while quietly building generational wealth.',
                'niche'       => 'US Wealth Logic',
                'is_global'   => true,
                'visual_traits' => [
                    'age'              => 'Late 20s to early 30s',
                    'ethnicity'        => 'Light-skinned Black woman',
                    'hair'             => 'Short natural coils, dark brown',
                    'eyes'             => 'Warm dark brown, almond-shaped',
                    'build'            => 'Slender, medium height',
                    'style'            => 'Smart casual — clean neutral tones, simple sweaters or blazers, minimal jewellery',
                    'signature_detail' => 'Small gold hoop earrings, a subtle freckle above her left brow, warm confident smile',
                ],
            ],
            [
                'name'        => 'Marcus',
                'slug'        => 'marcus-the-investigator',
                'description' => 'A no-nonsense investigative journalist in his mid-40s who has seen it all. The go-to narrator for True Crime, Scam, and Conspiracy stories — gravelly voice, sharp eyes, and an unflinching drive for truth.',
                'niche'       => 'True Crime',
                'is_global'   => true,
                'visual_traits' => [
                    'age'              => 'Mid 40s',
                    'ethnicity'        => 'White male, slightly weathered complexion',
                    'hair'             => 'Salt-and-pepper short cut, slightly dishevelled',
                    'eyes'             => 'Steel grey, piercing and alert',
                    'build'            => 'Athletic, broad-shouldered',
                    'style'            => 'Reporter aesthetic — dark overcoat, open-collar shirt, practical watch',
                    'signature_detail' => 'A small scar on his right jaw, always carries a worn leather notebook',
                ],
            ],
            [
                'name'        => 'Elena',
                'slug'        => 'elena-the-billionaire',
                'description' => 'The enigmatic protagonist of Business & Wealth stories. Cold, brilliant, and relentlessly driven. She built a $400M empire from nothing and now operates from the shadows of boardrooms.',
                'niche'       => 'Business & Wealth',
                'is_global'   => true,
                'visual_traits' => [
                    'age'              => 'Early 40s',
                    'ethnicity'        => 'Eastern European, olive complexion',
                    'hair'             => 'Sleek dark hair, worn straight or in a sharp low bun',
                    'eyes'             => 'Deep green, calculating and intense',
                    'build'            => 'Tall, lean, impeccably poised posture',
                    'style'            => 'Luxury minimalist — tailored monochrome suits, silk blouses, statement watch',
                    'signature_detail' => 'A thin platinum ring on her right index finger, never smiles for cameras',
                ],
            ],
        ];

        foreach ($globals as $data) {
            Character::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        $this->command->info('Global starter characters seeded: Alicia, Marcus, Elena.');
    }
}

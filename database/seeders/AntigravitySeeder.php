<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Niche;
use App\Models\Channel;
use Illuminate\Support\Str;

class AntigravitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Default Channel
        $channel = Channel::firstOrCreate(
            ['name' => 'Antigravity Main'],
            [
                'strategy_type' => 'Documentary',
                'hybrid_intensity' => '50',
                'risk_mode' => 'Safe',
                'primary_niche' => 'Documentary',
                'output_frequency' => 'Weekly'
            ]
        );

        $this->command->info('Default Channel Created: ' . $channel->name);

        // 2. Create Niches
        $niches = [
            // Tier 1: High CPM (Wealth, Business, Tech)
            ['name' => 'Business & Wealth', 'tier' => '1', 'cpm' => 25.00, 'desc' => 'Finance, crypto, success stories.'],
            ['name' => 'Tech & Future', 'tier' => '1', 'cpm' => 18.50, 'desc' => 'AI, gadgets, future timelines.'],
            ['name' => 'Health & Biohacking', 'tier' => '1', 'cpm' => 15.00, 'desc' => 'Longevity, fitness, medical mysteries.'],
            ['name' => 'Billionaire Kindness', 'tier' => '1', 'cpm' => 12.00, 'desc' => 'Viral moral stories involving wealth.'],
            ['name' => 'Poor & Rich', 'tier' => '1', 'cpm' => 12.00, 'desc' => 'Class contrast determination stories.'],
            ['name' => 'Luxury Lifestyle', 'tier' => '1', 'cpm' => 20.00, 'desc' => 'Mansions, yachts, elite living.'],

            // Tier 2: High Retention (Crime, Mystery, History, Drama)
            ['name' => 'True Crime', 'tier' => '2', 'cpm' => 8.50, 'desc' => 'Unsolved mysteries, criminal psychology.'],
            ['name' => 'Mafia', 'tier' => '2', 'cpm' => 9.00, 'desc' => 'Organized crime history and stories.'],
            ['name' => 'Cartel', 'tier' => '2', 'cpm' => 8.50, 'desc' => 'Drug trade, law enforcement clashes.'],
            ['name' => 'Scam / Con Artist', 'tier' => '2', 'cpm' => 8.00, 'desc' => 'Fraud schemes, impersonation stories.'],
            ['name' => 'Prison', 'tier' => '2', 'cpm' => 7.00, 'desc' => 'Life behind bars, escapes, inmate stories.'],
            ['name' => 'Mystery / Unsolved', 'tier' => '2', 'cpm' => 7.50, 'desc' => 'Cold cases, disappearances, strange events.'],
            ['name' => 'History & Mythology', 'tier' => '2', 'cpm' => 6.00, 'desc' => 'Ancient civilizations, folklore, war stories.'],
            ['name' => 'Royal / Historical Drama', 'tier' => '2', 'cpm' => 6.50, 'desc' => 'Monarchy scandals, historical romance.'],
            ['name' => 'Undercover / Secret Agent', 'tier' => '2', 'cpm' => 7.00, 'desc' => 'Espionage, infiltration, double agents.'],
            ['name' => 'Scientific Mysteries', 'tier' => '2', 'cpm' => 7.50, 'desc' => 'Space, physics anomalies, paradoxes.'],
            ['name' => 'African Folktales', 'tier' => '2', 'cpm' => 5.50, 'desc' => 'Traditional cultural stories and morals.'],
            ['name' => 'Wild West', 'tier' => '2', 'cpm' => 6.00, 'desc' => 'Outlaws, sheriffs, frontier survival.'],

            // Tier 3: Viral Potential (Shock, Emotion, Paranormal, Viral Fiction)
            ['name' => 'Survival / Stranded', 'tier' => '3', 'cpm' => 4.50, 'desc' => 'Shipwrecks, plane crashes, extreme survival.'],
            ['name' => 'Evil Stepmother', 'tier' => '3', 'cpm' => 4.00, 'desc' => 'Family drama, toxic relations, revenge.'],
            ['name' => 'Paternity Fraud', 'tier' => '3', 'cpm' => 4.50, 'desc' => 'Shocking DNA reveals, family secrets.'],
            ['name' => 'Celebrity Downfall', 'tier' => '3', 'cpm' => 5.50, 'desc' => 'Fame to shame stories.'],
            ['name' => 'Redemption / Second Chance', 'tier' => '3', 'cpm' => 4.00, 'desc' => 'Turning life around, forgiveness.'],
            ['name' => 'Revenge', 'tier' => '3', 'cpm' => 4.00, 'desc' => 'Satisfying payback stories.'],
            ['name' => 'Justice Served', 'tier' => '3', 'cpm' => 4.50, 'desc' => 'Instant karma, court cases, rights vindicated.'],
            ['name' => 'Racism & Justice', 'tier' => '3', 'cpm' => 4.50, 'desc' => 'Social justice, overcoming prejudice.'],
            ['name' => 'Forbidden', 'tier' => '3', 'cpm' => 4.00, 'desc' => 'Taboo relationships, secret acts.'],
            ['name' => 'Kidnapping / Hostage', 'tier' => '3', 'cpm' => 5.00, 'desc' => 'Abduction survival, rescue missions.'],
            ['name' => 'Paranormal & High Strangeness', 'tier' => '3', 'cpm' => 5.00, 'desc' => 'Cryptids, aliens, glitches in reality.'],
            ['name' => 'Vampire', 'tier' => '3', 'cpm' => 4.00, 'desc' => 'Supernatural fiction, lore.'],
            ['name' => 'Werewolf', 'tier' => '3', 'cpm' => 4.00, 'desc' => 'Skinwalkers, transformation stories.'],
            ['name' => 'Haunted House', 'tier' => '3', 'cpm' => 4.50, 'desc' => 'Ghost hunting, possessed locations.'],
            ['name' => 'Conspiracy', 'tier' => '3', 'cpm' => 5.50, 'desc' => 'Secret societies, cover-ups.'],
            ['name' => 'Adventure', 'tier' => '3', 'cpm' => 4.50, 'desc' => 'Exploration, treasure hunting.'],
            ['name' => 'Discovery', 'tier' => '3', 'cpm' => 5.00, 'desc' => 'Finding lost artifacts, new species.'],
        ];

        foreach ($niches as $nicheData) {
            Niche::firstOrCreate(
                ['name' => $nicheData['name']],
                [
                    'tier' => $nicheData['tier'],
                    'monetization_cpm' => $nicheData['cpm'],
                    'description' => $nicheData['desc'],
                    'rotation_weight' => 1.0
                ]
            );
        }

        $this->command->info('Antigravity Niches Seeded!');
    }
}

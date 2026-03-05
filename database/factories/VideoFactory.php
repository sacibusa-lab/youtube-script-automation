<?php

namespace Database\Factories;

use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideoFactory extends Factory
{
    protected $model = Video::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'topic' => $this->faker->sentence(3),
            'niche' => 'True Crime',
            'duration_minutes' => 60,
            'tier1_country' => 'USA',
            'selected_title' => $this->faker->sentence(5),
            'mega_hook' => $this->faker->sentence(10),
            'status' => 'pending',
            'monetization_tier' => 'High',
        ];
    }
}

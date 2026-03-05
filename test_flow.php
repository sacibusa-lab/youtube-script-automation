<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$video = \App\Models\Video::create([
    'user_id' => \App\Models\User::first()->id,
    'channel_id' => \App\Models\Channel::first()->id ?? 1,
    'niche_id' => \App\Models\Niche::first()->id,
    'content_structure_id' => \App\Models\ContentStructure::first()->id ?? 1,
    'emotional_tone_id' => \App\Models\EmotionalTone::first()->id ?? 1,
    'topic' => 'AI Impact on Jobs',
    'niche' => 'Technology',
    'tier1_country' => 'USA',
    'duration_minutes' => 30,
    'chapter_count' => 10,
    'status' => 'pending',
    'metadata' => ['hybrid_intensity' => 50, 'risk_mode' => 'Safe']
]);

echo "Created Video ID: {$video->id}\n";
try {
    \App\Jobs\GenerateConceptsJob::dispatchSync($video);
    echo "Status: " . $video->fresh()->status . "\n";
    echo "Strategies Count: " . is_array($video->fresh()->strategies) ? count($video->fresh()->strategies) : 0 . "\n";
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
    echo "Status: " . $video->fresh()->status . "\n";
}

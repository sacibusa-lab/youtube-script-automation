<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$video = \App\Models\Video::with(['scenes'])->findOrFail(30);

echo "Total Scenes: " . $video->scenes->count() . "\n";

foreach ($video->scenes->sortBy('scene_number') as $scene) {
    echo "ID: {$scene->id} | Scene #: {$scene->scene_number} | URL: '{$scene->image_url}' | Dur: {$scene->duration_seconds}\n";
    
    if ($scene->image_url) {
        $relativePath = str_replace('/storage/', '', parse_url($scene->image_url, PHP_URL_PATH));
        $fullPath = storage_path('app/public/' . str_replace('/', DIRECTORY_SEPARATOR, $relativePath));
        
        if (!file_exists($fullPath)) {
            echo "  --> FILE MISSING: $fullPath\n";
        }
    }
}

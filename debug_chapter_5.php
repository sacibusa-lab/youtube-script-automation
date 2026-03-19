<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Video;
use App\Models\Chapter;

$video = Video::find(33);
if (!$video) {
    echo "Video 33 not found.\n";
    exit;
}

$chapter5 = $video->chapters()->where('chapter_number', 5)->first();
if (!$chapter5) {
    echo "Chapter 5 for Video 33 not found.\n";
    exit;
}

echo "Resetting Chapter 5 [Current Status: {$chapter5->status}]...\n";

// Emulate ProjectController@resetChapter
$chapter5->scenes()->delete();
$chapter5->update(['status' => 'pending']);

if ($video->status === 'completed') {
    $video->update(['status' => 'generating_chapters']);
}

echo "Chapter 5 Status now: " . $chapter5->fresh()->status . "\n";
echo "Scenes count: " . $chapter5->scenes()->count() . "\n";
echo "Video Status now: " . $video->fresh()->status . "\n";

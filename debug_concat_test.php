<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Video;
use Illuminate\Support\Facades\Storage;

$video = Video::with(['scenes'])->findOrFail(30);
$scenes = $video->scenes->sortBy('scene_number');

$concatContent = "";
foreach ($scenes as $index => $scene) {
    if (!$scene->image_url) continue;
    
    // Parse filename from URL: /storage/scenes/FILENAME.png
    $filename = basename(parse_url($scene->image_url, PHP_URL_PATH));
    $path = Storage::disk('public')->path("scenes/{$filename}");
    
    if (!file_exists($path)) {
        echo "WARNING: File missing: $path\n";
        continue;
    }
    
    $path = str_replace('\\', '/', $path);
    $concatContent .= "file '{$path}'\n";
    $concatContent .= "duration " . ($scene->duration_seconds ?: 5) . "\n";
}

// Repeat last file to ensure last duration is respected
if ($scenes->isNotEmpty()) {
    $lastScene = $scenes->last();
    $filename = basename(parse_url($lastScene->image_url, PHP_URL_PATH));
    $path = Storage::disk('public')->path("scenes/{$filename}");
    $path = str_replace('\\', '/', $path);
    $concatContent .= "file '{$path}'\n";
}

$tempFileName = "video_{$video->id}_concat.txt";
Storage::disk('public')->put("temp/{$tempFileName}", $concatContent);
$concatFilePath = Storage::disk('public')->path("temp/{$tempFileName}");

echo "Concat file created at: $concatFilePath\n";

$exportFileName = "video_{$video->id}_concat_test.mp4";
$outputPath = Storage::disk('public')->path("exports/{$exportFileName}");

$ffmpegBinary = config('laravel-ffmpeg.binaries.ffmpeg') ?: 'ffmpeg';

// Clean up old file
if (file_exists($outputPath)) unlink($outputPath);

$command = "{$ffmpegBinary} -y -f concat -safe 0 -i \"{$concatFilePath}\" -vcodec libx264 -pix_fmt yuv420p \"{$outputPath}\" 2>&1";

echo "Executing Command: $command\n";

$output = shell_exec($command);

echo "Output:\n" . substr($output, -1000) . "\n"; // Show last 1000 chars

if (file_exists($outputPath)) {
    echo "SUCCESS: Video created at $outputPath\n";
} else {
    echo "FAILED: Video not found.\n";
}

<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264;
use ProtoneMedia\LaravelFFMpeg\Filesystem\Media;

try {
    $inputPaths = [
        storage_path('app/public/test1.jpg') => 'test1.jpg',
        storage_path('app/public/test2.jpg') => 'test2.jpg',
    ];

    foreach ($inputPaths as $path => $name) {
        if (!file_exists($path)) {
            // create dummy image
            $img = imagecreatetruecolor(100, 100);
            imagecolorallocate($img, 255, 0, 0);
            imagejpeg($img, $path);
        }
    }

    $complexFilter = "[0:v]loop=loop=25:size=1:start=0,setpts=PTS-STARTPTS[v0]; [1:v]loop=loop=25:size=1:start=0,setpts=PTS-STARTPTS[v1]; [v0][v1]concat=n=2:v=1:a=0[outv]";

    $relativePaths = array_values($inputPaths);
    $exporter = FFMpeg::fromDisk('public')->open($relativePaths)
        ->export()
        ->addFormatOutputMapping(new X264('aac', 'libx264'), Media::make('public', 'exports/test_out.mp4'), ['[outv]'])
        ->addFilter('', $complexFilter, '');

    echo "Command:\n";
    $exporter->dd();

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}

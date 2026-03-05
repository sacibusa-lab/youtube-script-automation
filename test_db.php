<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$video = \App\Models\Video::latest()->first();
echo "Status: " . $video->status . "\n";
echo "Strategies:\n" . json_encode($video->strategies, JSON_PRETTY_PRINT) . "\n";

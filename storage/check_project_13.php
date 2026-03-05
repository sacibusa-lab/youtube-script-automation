<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Video;

$v = Video::find(13);
if (!$v) {
    echo "Video 13 not found\n";
    exit;
}

echo "STATUS: " . $v->status . "\n";
echo "STRATEGIES (JSON): " . json_encode($v->strategies) . "\n";
echo "GENERATED TITLES COUNT: " . $v->generatedTitles->count() . "\n";
foreach ($v->generatedTitles as $gt) {
    echo " - TITLE: " . $gt->title . " (ID: " . $gt->id . ", Saved: " . ($gt->is_saved ? 'Yes' : 'No') . ")\n";
}

<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Video;

$p = Video::find(30);
if ($p) {
    $p->status = 'assembly_failed';
    $p->save();
    echo "Project 30 status reset to assembly_failed.\n";
}


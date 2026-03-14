<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Video;

$projects = Video::where('user_id', \App\Models\User::first()->id)->get();
echo "Total Projects: " . $projects->count() . "\n";
foreach ($projects as $p) {
    echo "ID: {$p->id} | Status: {$p->status} | Title: {$p->selected_title}\n";
}

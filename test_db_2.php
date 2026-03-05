<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
if ($user) {
    \Illuminate\Support\Facades\Auth::login($user);
}

$video = \App\Models\Video::latest()->whereNotNull('strategies')->first();
if ($video) {
    file_put_contents('test_output.html', view('videos.show', ['project' => $video])->render());
    echo 'Saved view to test_output.html in utf-8';
} else {
    echo "No video with strategies found.\n";
}

<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Video;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

$user = \App\Models\User::first();
Auth::login($user);

$project = Video::with(['chapters.scenes', 'niche', 'channel'])->find(30);

try {
    $json = json_encode($project);
    if ($json === false) {
        echo "JSON encoding FAILED: " . json_last_error_msg() . "\n";
    } else {
        echo "JSON encoding successful. Size: " . strlen($json) . "\n";
    }
    
    $html = View::make('videos.studio', ['project' => $project])->render();
    echo "Rendered successfully. Length: " . strlen($html) . "\n";
    
    // Check for x-data content
    if (preg_match('/x-data="(.*?)"/s', $html, $matches)) {
        echo "Found x-data attribute.\n";
        // Check for double quotes inside
        $xdata = $matches[1];
        if (strpos($xdata, '"') !== false) {
             echo "WARNING: Double quotes found in x-data attribute value!\n";
        }
    } else {
        echo "Could not find x-data attribute in rendered HTML.\n";
    }
} catch (\Exception $e) {
    echo "Render failed: " . $e->getMessage() . "\n";
}

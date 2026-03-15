<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Video;
use App\Services\Video\VideoAssemblyService;

try {
    $project = Video::find(30);
    if (!$project) {
        echo "Project 30 not found.\n";
        exit;
    }
    
    $assemblyService = app(VideoAssemblyService::class);
    $videoUrl = $assemblyService->assemble($project);
    echo "Success: $videoUrl\n";

} catch (\Throwable $e) {
    echo "Encoding Exception: " . $e->getMessage() . "\n";
    
    if (method_exists($e, 'getCommand')) {
        echo "Command: " . $e->getCommand() . "\n";
    }
    
    if (method_exists($e, 'getErrorOutput')) {
        echo "Error Output: " . $e->getErrorOutput() . "\n";
    }

    if ($e->getPrevious()) {
        $prev = $e->getPrevious();
        echo "Previous Exception: " . get_class($prev) . " - " . $prev->getMessage() . "\n";
        if (method_exists($prev, 'getCommand')) {
            echo "Prev Command: " . $prev->getCommand() . "\n";
        }
        if (method_exists($prev, 'getErrorOutput')) {
            echo "Prev Error Output: " . $prev->getErrorOutput() . "\n";
        }
    }
    echo "\nFull Exception:\n" . $e . "\n";
}

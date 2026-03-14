<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

try {
    $facade = new ReflectionClass(FFMpeg::class);
    echo "Facade: " . FFMpeg::class . "\n";
    
    $root = FFMpeg::getFacadeRoot();
    echo "Root object class: " . get_class($root) . "\n";
    
    $refl = new ReflectionClass($root);
    foreach (['open', 'openAdvanced', 'fromFiles'] as $method) {
        if ($refl->hasMethod($method)) {
            $m = $refl->getMethod($method);
            echo "Method $method: ";
            foreach ($m->getParameters() as $p) {
                echo "$" . $p->getName() . ($p->isOptional() ? " (optional)" : "") . " ";
            }
            echo "\n";
        } else {
             echo "Method $method NOT FOUND\n";
        }
    }
} catch (\Exception $e) {
    echo "Reflection failed: " . $e->getMessage() . "\n";
}

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

use App\Models\User;
use App\Models\Plan;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Http\Request;

$admin = User::where('is_admin', true)->first();
$user = User::find(5);

if (!$user) {
    die("User 5 not found\n");
}

echo "BEFORE UPDATE: Plan ID " . $user->plan_id . "\n";

// Mock the request
$request = Request::create('/admin/users/5', 'PATCH', [
    'plan_id' => 3, // Change to Creator
    'total_credits' => 5000000,
    'used_credits' => 0,
    'total_image_tokens' => 100,
    'used_image_tokens' => 0,
]);

// Auth as admin
auth()->login($admin);

$controller = new UserController();
try {
    $response = $controller->update($request, $user);
    echo "CONTROLLER UPDATE CALLED\n";
    echo "AFTER UPDATE: Plan ID " . $user->fresh()->plan_id . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

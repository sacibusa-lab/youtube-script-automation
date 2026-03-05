<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\User;
use App\Models\Plan;

$u = User::find(5);
if ($u) {
    echo "USER_DATA_START\n";
    print_r($u->only(['id', 'email', 'plan_id', 'total_credits', 'used_credits', 'updated_at']));
    echo "PLAN_NAME: " . ($u->plan?->name ?? 'None') . "\n";
    echo "USER_DATA_END\n";
} else {
    echo "User 5 not found\n";
}

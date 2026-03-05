<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\User;

foreach (User::with('plan')->get() as $u) {
    echo "ID: " . $u->id . " | EMAIL: " . $u->email . " | PLAN_ID: " . ($u->plan_id ?? 'NULL') . " (" . ($u->plan?->name ?? 'N/A') . ") | CREDITS: " . $u->total_credits . " | ADMIN: " . ($u->is_admin ? 'YES' : 'NO') . "\n";
}

<?php

use App\Http\Controllers\ApiGatewayController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/pricing', [\App\Http\Controllers\HomeController::class, 'pricing'])->name('pricing');

// ── Paystack Webhook (public, server-to-server, HMAC verified internally) ────
Route::post('/payment/webhook', [\App\Http\Controllers\PaymentController::class, 'webhook'])
    ->name('payment.webhook');
// ─────────────────────────────────────────────────────────────────────────────

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/generator', [\App\Http\Controllers\GeneratorController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('generator');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Payments & Subscriptions
    Route::get('/payment/initialize', [\App\Http\Controllers\PaymentController::class, 'initialize'])->name('payment.initialize');
    Route::post('/payment/initialize/topup', [\App\Http\Controllers\PaymentController::class, 'initializeTopup'])->name('payment.initialize.topup');
    Route::get('/payment/callback', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('payment.callback');

    // Billing & Usage History
    Route::get('/billing/history', [\App\Http\Controllers\BillingController::class, 'history'])->name('billing.history');
    Route::get('/billing/topup', [\App\Http\Controllers\BillingController::class, 'topup'])->name('topup.index');

    // Projects (Story Marathons)
    Route::resource('projects', ProjectController::class);
    Route::get('/studio', [ProjectController::class, 'studioIndex'])->name('studio.index');
    Route::get('/bookmarks', [ProjectController::class, 'bookmarks'])->name('projects.bookmarks');
    Route::post('/projects/titles/{title}/toggle-bookmark', [ProjectController::class, 'toggleBookmark'])->name('projects.titles.toggle-bookmark');
    Route::get('/projects/titles/{title}/status', [ProjectController::class, 'checkTitleStatus'])->name('projects.titles.status');
    
    // AI Production Roles API
    Route::post('/api/v1/ai/production-role', [App\Http\Controllers\Api\AIRoleController::class, 'execute'])->name('api.ai.role.execute');
    Route::post('/projects/{project}/retry', [ProjectController::class, 'retry'])->name('projects.retry');
    Route::post('/projects/{project}/select-title', [ProjectController::class, 'selectTitle'])->name('projects.select-title');
    Route::post('/projects/{project}/launch', [ProjectController::class, 'launchMission'])->name('projects.launch');
    Route::post('/projects/{project}/select-strategy', [ProjectController::class, 'selectStrategy'])->name('projects.select-strategy');
    Route::get('/projects/{project}/export', [ProjectController::class, 'export'])->name('projects.export');
    Route::post('/projects/{project}/chapters/{chapter}/architect', [ProjectController::class, 'architectChapter'])->name('projects.chapters.architect');
    Route::post('/projects/{project}/approve-chapter/{chapter}', [ProjectController::class, 'approveChapter'])->name('projects.chapters.approve');
    Route::post('/projects/{project}/chapters/{chapter}/scenes/{scene}/generate-image', [ProjectController::class, 'generateSceneImage'])->name('projects.scenes.generate-image');
    Route::get('/projects/{project}/chapters/{chapter}/scenes/{scene}/image-status', [ProjectController::class, 'checkSceneImageStatus'])->name('projects.scenes.image-status');
    Route::post('/projects/{project}/regenerate-hook', [ProjectController::class, 'regenerateHook'])->name('projects.regenerate-hook');
    Route::post('/projects/{project}/regenerate-thumbnail', [ProjectController::class, 'regenerateThumbnail'])->name('projects.regenerate-thumbnail');
    Route::post('/projects/titles/{title}/clone', [ProjectController::class, 'cloneFromConcept'])->name('projects.clone');
    Route::post('/projects/titles/{title}/generate-image', [ProjectController::class, 'generateThumbnailImage'])->name('projects.titles.generate-image');
    Route::get('/projects/{project}/studio', [ProjectController::class, 'studio'])->name('projects.studio');
    Route::post('/projects/{project}/studio/save', [ProjectController::class, 'saveStudioState'])->name('projects.studio.save');

    // Character Library
    Route::resource('characters', \App\Http\Controllers\CharacterController::class);
    Route::get('/api/characters', [\App\Http\Controllers\CharacterController::class, 'apiList'])->name('characters.api-list');

    // User Analytics
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics');

    // Admin Infrastructure (Domiciled)
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::redirect('/', '/admin/dashboard');
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // API Gateway
        Route::get('/api-gateway', [\App\Http\Controllers\Admin\ApiGatewayController::class, 'index'])->name('api-gateway.index');
        Route::post('/api-gateway', [\App\Http\Controllers\Admin\ApiGatewayController::class, 'store'])->name('api-gateway.store');
        Route::patch('/api-gateway/{apiKey}/toggle', [\App\Http\Controllers\Admin\ApiGatewayController::class, 'toggleStatus'])->name('api-gateway.toggle');
        Route::post('/api-gateway/strategy', [\App\Http\Controllers\Admin\ApiGatewayController::class, 'updateStrategy'])->name('api-gateway.update-strategy');
        Route::patch('/api-gateway/{apiKey}/set-primary', [\App\Http\Controllers\Admin\ApiGatewayController::class, 'setPrimary'])->name('api-gateway.set-primary');
        Route::delete('/api-gateway/{apiKey}', [\App\Http\Controllers\Admin\ApiGatewayController::class, 'destroy'])->name('api-gateway.destroy');
        Route::post('/api-gateway/{apiKey}/test', [\App\Http\Controllers\Admin\ApiGatewayController::class, 'test'])->name('api-gateway.test');
        Route::patch('/api-gateway/roles/{role}', [\App\Http\Controllers\Admin\ApiGatewayController::class, 'updateRole'])->name('api-gateway.update-role');

        // System Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'store'])->name('settings.store');

        // Plan Management
        Route::post('plans/reorder', [\App\Http\Controllers\Admin\PlanController::class, 'reorder'])->name('plans.reorder');
        Route::resource('plans', \App\Http\Controllers\Admin\PlanController::class)->except(['show', 'destroy']);

        // Top-up Package Management
        Route::resource('topup-packages', \App\Http\Controllers\Admin\TopupPackageController::class)->except(['show']);

        // User Management
        Route::patch('/users/{user}/toggle-admin', [\App\Http\Controllers\Admin\UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['create', 'store', 'show']);

        // Revenue & Payments
        Route::get('/revenue', [\App\Http\Controllers\Admin\RevenueController::class, 'index'])->name('revenue.index');

        // AI Token Economy & Analytics
        Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/ai-usage', [\App\Http\Controllers\Admin\AiUsageController::class, 'index'])->name('ai-usage.index');

        // Global Story Logs
        Route::get('/story-logs', [\App\Http\Controllers\Admin\ProjectController::class, 'index'])->name('projects.index');
    });
});

require __DIR__.'/auth.php';

// ── Fallback Route for serving Storage Files Locally (bypasses Windows 403 errors) ────
Route::get('/storage/{path}', function (string $path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*')->name('storage.local');

<?php

use App\Http\Controllers\Api\VerifyController;
use Illuminate\Support\Facades\Route;

Route::post('/mcp/verify', VerifyController::class)
    ->middleware(['bridge.key', 'throttle:mcp-verify'])
    ->name('mcp.verify');
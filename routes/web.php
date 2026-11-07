<?php

use Illuminate\Support\Facades\Route;
use App\Mcp\PolicyAuthServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/auth', PolicyAuthServer::class)
    ->middleware('bridge.key');
Route::get('/', function () {
    return view('welcome');
});

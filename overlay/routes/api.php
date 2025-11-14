<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\GatewayAdminController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/purchase', [PurchaseController::class, 'purchase']);

Route::middleware('auth:sanctum')->group(function () {
    Route::patch('/gateways/{id}/toggle', [GatewayAdminController::class, 'toggle']);
    Route::patch('/gateways/{id}/priority', [GatewayAdminController::class, 'setPriority']);
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::post('/transactions/{id}/refund', [TransactionController::class, 'refund']);
    Route::apiResource('products', ProductController::class)->only(['index','store','show','update','destroy']);
    Route::apiResource('clients', ClientController::class)->only(['index','store','show','update']);
    Route::apiResource('users', UserController::class)->only(['index','store','show','update']);
});

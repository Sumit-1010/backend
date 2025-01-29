<?php
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;

// Public Routes
Route::post('signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login']);

// Protected Routes

Route::middleware(['auth:sanctum'])->post('logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/assets/balances', [AssetController::class, 'getBalances']);
    Route::post('/assets/{id}/update-balances', [AssetController::class, 'updateBalances']);
    Route::post('/assets/transfer', [AssetController::class, 'transferAsset']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('assets/{id}/purchase', [AssetController::class, 'updatePurchaseBalance']);
    Route::post('assets/{id}/transfers_in', [AssetController::class, 'updateTransferInBalance']);
    Route::post('assets/{id}/transfers_out', [AssetController::class, 'updateTransferOutBalance']);
    Route::delete('assets/{id}/deleteAsset', [AssetController::class, 'deleteAsset']);
});



// // Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/assets/transfer', [AssetController::class, 'transferAsset']);
// // });


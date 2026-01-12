<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\PaymentController;

Route::middleware('throttle:60,1')->prefix('v1')->group(function () {
    Route::post('init', [PaymentController::class, 'init']);
    Route::get('status/{order_id}', [PaymentController::class, 'status']);
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EbayWebhookController;
use App\Http\Controllers\EbayCallbackController;

Route::get('/ebay/account-deletion', [EbayWebhookController::class, 'challenge']);
Route::post('/ebay/account-deletion', [EbayWebhookController::class, 'notification']);
Route::post('/ebay/notifications', [EbayWebhookController::class, 'handleInventoryNotification']);

Route::get('/ebay/callback', [EbayCallbackController::class, 'handle']);
Route::post('/ebay/callback', [EbayCallbackController::class, 'handle']);


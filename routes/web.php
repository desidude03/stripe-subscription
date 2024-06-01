<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WebhookController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [SubscriptionController::class, 'checkout'])->name('checkout');
Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');


Route::post('webhook', [WebhookController::class, 'handleWebhook']);


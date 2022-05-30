<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;    

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/push-notificaiton', [NotificationController::class, 'index'])->name('push-notificaiton');
Route::post('/store-token', [NotificationController::class, 'storeToken'])->name('store.token');
Route::post('/send-notification', [NotificationController::class, 'sendNotification'])->name('send.notification');
<?php

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function() {
    Route::group(['prefix' => 'conversation'], function() {
        Route::get('',[ChatController::class, 'open']);
        Route::post('message',[ChatController::class, 'chat']);
        // Route::get('closeChat',[ChatController::class, 'close']);
    });
});

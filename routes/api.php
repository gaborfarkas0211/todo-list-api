<?php

use App\Http\Controllers\Api\ItemsController;
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

//Route::prefix('items')->controller(ItemsController::class)->group(function () {
//    Route::get('', 'index');
//    Route::get('{item}', 'view');
//    Route::post('store', 'store');
//});
Route::resource('items', ItemsController::class)->except(['create', 'edit']);

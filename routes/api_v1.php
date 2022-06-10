<?php

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

// TODO
// Middleware Authentication
// Error Handling
Route::controller(App\Http\Controllers\V1\TaskController::class)
->group(function () {
    Route::get('/tasks', 'index');
    Route::get('/tasks/{id}/file_url', 'show_file_url');
    Route::post('/tasks', 'store');
    Route::post('/tasks/{id}/tag', 'store_tag');
    Route::put('/tasks/{id}', 'update');
    Route::patch('/tasks/{id}/status', 'update_status');
});

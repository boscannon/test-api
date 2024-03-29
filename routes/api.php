<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [Controllers\Authcontroller::class, 'login']);

Route::apiResource('post', Controllers\Backend\PostController::class);
Route::apiResource('category', Controllers\Backend\CategoryController::class);
Route::apiResource('product', Controllers\Backend\ProductController::class);

//操作紀錄
Route::resource('/audits', Controllers\Backend\AuditController::class)->only(['index']);

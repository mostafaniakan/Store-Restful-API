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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->middleware('auth:sanctum');
Route::prefix('/user')->group(function () {
    Route::post('/create', [\App\Http\Controllers\UserController::class, 'store']);
    Route::get('/index/{index}', [\App\Http\Controllers\UserController::class, 'show'])->middleware('auth:sanctum');
    Route::put('/update/{id}',[\App\Http\Controllers\UserController::class,'update'])->middleware('auth:sanctum');
    Route::delete('/delete/{id}',[\App\Http\Controllers\UserController::class,'destroy'])->middleware('auth:sanctum');
    Route::post('/login', [\App\Http\Controllers\UserController::class, 'login']);
    Route::get('/logout', [\App\Http\Controllers\UserController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/info', [\App\Http\Controllers\UserController::class, 'userInfo'])->middleware('auth:sanctum');
    Route::get('/{user}/orders',[\App\Http\Controllers\UserController::class,'orders'])->middleware('auth:sanctum');
});


//province
Route::apiResource('province', \App\Http\Controllers\ProvinceController::class);
Route::get('/provinces/{province}/children', [\App\Http\Controllers\ProvinceController::class, 'cities']);

Route::apiResource('city', \App\Http\Controllers\CityController::class);


Route::apiResource('brands', \App\Http\Controllers\BrandController::class)->middleware('auth:sanctum');
Route::get('/brands/{brand}/products', [\App\Http\Controllers\BrandController::class, 'products'])->middleware('auth:sanctum');
Route::apiResource('categories', \App\Http\Controllers\CategoryController::class)->middleware('auth:sanctum');
Route::get('/categories/{category}/children', [\App\Http\Controllers\CategoryController::class, 'children'])->middleware('auth:sanctum');
Route::get('/categories/{category}/parent', [\App\Http\Controllers\CategoryController::class, 'parent'])->middleware('auth:sanctum');

Route::get('/categories/{category}/products', [\App\Http\Controllers\CategoryController::class, 'products'])->middleware('auth:sanctum');

Route::apiResource('products', \App\Http\Controllers\ProductController::class);

//pay
Route::post('/payment/send', [\App\Http\Controllers\PaymentController::class, 'send'])->middleware('auth:sanctum');
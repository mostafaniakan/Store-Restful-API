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

Route::apiResource('brands',\App\Http\Controllers\BrandController::class);
Route::get('/brands/{brand}/products',[\App\Http\Controllers\BrandController::class,'products']);
Route::apiResource('categories',\App\Http\Controllers\CategoryController::class);
Route::get('/categories/{category}/children',[\App\Http\Controllers\CategoryController::class,'children']);
Route::get('/categories/{category}/parent',[\App\Http\Controllers\CategoryController::class,'parent']);

Route::get('/categories/{category}/products',[\App\Http\Controllers\CategoryController::class,'products']);

Route::apiResource('products',\App\Http\Controllers\ProductController::class);

//pay
Route::post('/payment/send',[\App\Http\Controllers\PaymentController::class,'send']);
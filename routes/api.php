<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductLocationController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 🟢 تسجيل الدخول والتسجيل
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// 🟢 تأمين بعض الـ Routes باستخدام Middleware
Route::middleware('auth:api')->group(function () {

    // 🟢 استرجاع بيانات المستخدم
    Route::get('profile', [AuthController::class, 'profile']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    // 🟢 المستخدم العادي يستطيع رؤية المنتجات فقط
    Route::get('/products', [ProductController::class, 'index']);

    // 🟢 المستخدم العادي يستطيع رؤية مواقع المنتجات
    Route::get('/products/{product_id}/locations', [ProductLocationController::class, 'index']);

    // 🔴 المسؤول فقط يمكنه إضافة، تعديل، وحذف المنتجات والمواقع
    Route::middleware('role:admin')->group(function () {
        Route::post('/products',        [ProductController::class, 'store']);
        Route::put('/products/{id}',    [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        // 🔴 إضافة، تعديل، وحذف مواقع المنتجات
        Route::get('/product-locations',                         [ProductLocationController::class, 'index']);
        Route::post('/product-locations',                        [ProductLocationController::class, 'store']);
        Route::put('/product-locations/{id}',                    [ProductLocationController::class, 'update']);
        Route::delete('/product-locations/{id}',                 [ProductLocationController::class, 'destroy']);
        Route::get('/products-by-location',                      [ProductController::class, 'productsByLocation']);
        Route::get('/product-locations/{product_id}/{location}', [ProductLocationController::class, 'findProductInLocation']);
        Route::post('/orders',              [OrderController::class, 'store']);
        Route::get('/orders',               [OrderController::class, 'index']);
        Route::get('/orders/{id}',          [OrderController::class, 'show']);
        Route::put('/orders/{id}',          [OrderController::class, 'update']);
        Route::delete('/orders/{id}',       [OrderController::class, 'destroy']);
        Route::put('/orders/{id}/approve',  [OrderController::class, 'approve']);
        Route::put('/orders/{id}/reject',   [OrderController::class, 'reject']);


    });

});

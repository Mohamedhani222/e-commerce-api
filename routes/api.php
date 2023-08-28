<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

// orders
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [OrderController::class, 'cart']);
    Route::post('/remove_from_cart', [OrderController::class, 'remove_from_cart']);
});

Route::resources([
    'products' => ProductController::class,
    'orders' => OrderController::class,
    'roles' => RoleController::class,
    'categories' => CategoryController::class
]);








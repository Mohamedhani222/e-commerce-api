<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\PermissionController;
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
Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::get('/', [OrderController::class, 'cart']);
    Route::post('/remove_item', [OrderController::class, 'remove_from_cart']);
    Route::post('/add_qty', [OrderController::class, 'add_qty']);
    Route::post('/sub_qty', [OrderController::class, 'sub_qty']);
    Route::post('/confirm_order/{id}', [OrderController::class, 'confirm_order']);
});

Route::resources([
    'products' => ProductController::class,
    'orders' => OrderController::class,
    'roles' => RoleController::class,
    'permissions' => PermissionController::class,
    'categories' => CategoryController::class,
    'coupons' =>CouponController::class
]);

//Route::get('pay/{orderId}' , [PaymentController::class,'pay']);
//Route::get('/payments/verify/{payment?}',[PaymentController::class,'verifyWithPaymobWallet'])->name('payment-verify');





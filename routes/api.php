<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\FoodItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FoodOrderController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\TruckEmployee;
use App\Http\Controllers\FeedbackController;

//Authentication
Route::post('/login', [ApiController::class,'login']);
Route::post('/logout', [ApiController::class,'logout'])->middleware('auth:api');


Route::post('/feedbacks', [FeedbackController::class, 'store']);
Route::get('/feedbacks', [FeedbackController::class, 'index']);
Route::get('/feedback/ratings-summary', [FeedbackController::class, 'ratingsSummary']);






//Account CRUD
Route::prefix('account')
    ->group(function(){
        Route::post('/', [ApiController::class,'register']);
        Route::get('/', [ApiController::class,'detail'])->middleware('auth:api');
    });

//Food Iteams CRUD
Route::get('/items-list', [FoodItemController::class, 'index']);
Route::prefix('food-items')
    ->middleware('auth:api')
    ->group(function () {
        Route::get('/', [FoodItemController::class, 'index']);
        Route::post('/', [FoodItemController::class, 'store']);
        Route::get('/{foodItem}', [FoodItemController::class, 'show']);
        Route::put('/{foodItem}', [FoodItemController::class, 'update']);
        Route::delete('/{foodItem}', [FoodItemController::class, 'destroy']);
    });


//Food Categories CRUD
Route::prefix('categories')
    ->middleware('auth:api')
    ->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{category}', [CategoryController::class, 'show']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
    });

Route::prefix('employees')
    ->middleware('auth:api')
    ->group(function(){
        Route::get('/',[TruckEmployee::class,'index']);
        Route::post('/', [TruckEmployee::class, 'store']);
        Route::get('/{employee}', [TruckEmployee::class, 'show']);
        Route::put('/{employee}', [TruckEmployee::class, 'update']);
        Route::delete('/{employee}', [TruckEmployee::class, 'destroy']);
    });

Route::prefix('trucks')
    ->middleware('auth:api')
    ->group(function () {
        Route::get('/', [TruckController::class, 'index']);
        Route::post('/', [TruckController::class, 'store']);
        Route::get('/{truck}', [TruckController::class, 'show']);
        Route::put('/{truck}', [TruckController::class, 'update']);
        Route::delete('/{truck}', [TruckController::class, 'destroy']);
    });

Route::prefix('orders')
    ->middleware('auth:api')
    ->group(function () {
        Route::get('/', [FoodOrderController::class, 'index']);
        Route::post('/', [FoodOrderController::class, 'store']);
        Route::get('/{order}', [FoodOrderController::class, 'show']);
        Route::put('/{order}', [FoodOrderController::class, 'update']);
        Route::delete('/{id}', [FoodOrderController::class, 'destroyOrder']);
        Route::get('receipt-pdf/{orderId}',[FoodOrderController::class,'generateReceiptPdf']);
        Route::post('/food/most-ordered-food', [FoodOrderController::class, 'getMostOrderedFood']);
        Route::post('/food/total_food_orders', [FoodOrderController::class, 'getOrdersWithSummary']);
    });

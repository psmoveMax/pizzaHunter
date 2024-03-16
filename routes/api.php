<?php


use App\Http\Controllers\API\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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



//Создание заказа
Route::post('/orders', [OrderController::class, 'createOrder']);
Route::post('/orders/{order_id}/items', [OrderController::class, 'addItemToOrder']);
Route::get('/orders/{order_id}', [OrderController::class, 'getOrder']);






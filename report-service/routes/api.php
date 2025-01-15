<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;


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
Route::middleware('verify.jwt')->group(function () {
    // Order Reports
    Route::get('/reports/orders', [ReportController::class, 'getOrderReports']);
    Route::get('/reports/orders/{id}', [ReportController::class, 'getOrderReport']);
    Route::post('/reports/orders', [ReportController::class, 'createOrderReport']);
    Route::delete('/reports/orders/{id}', [ReportController::class, 'deleteOrderReport']);

    // Product Reports
    Route::get('/reports/products', [ReportController::class, 'getProductReports']);
    Route::get('/reports/products/{id}', [ReportController::class, 'getProductReport']);
    Route::post('/reports/products', [ReportController::class, 'createProductReport']);
    Route::delete('/reports/products/{id}', [ReportController::class, 'deleteProductReport']);
});
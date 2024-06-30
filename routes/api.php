<?php

use App\Http\Controllers\api\companiasController;
use App\Http\Controllers\api\InvoiceController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(companiasController::class)
    ->prefix("companies")
    ->group(function () {
        Route::post("/CreateCompania", "CreateCompania");
    });
    Route::controller(InvoiceController::class)
    ->prefix("invoice")
    ->group(function () {
        Route::post("/send", "send");
    });
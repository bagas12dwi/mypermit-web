<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\HousekeepingController;
use App\Http\Controllers\Api\RequestPermittController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/getDetail', [AuthController::class, 'getDetail']);

Route::post('/storePermitt', [RequestPermittController::class, 'store']);
Route::post('/getPermittByUser', [RequestPermittController::class, 'getAllPermittByUser']);
Route::post('/getApprovePermit', [RequestPermittController::class, 'getApprovePermit']);
Route::post('/getDetailPermit', [RequestPermittController::class, 'getDetailPermit']);
Route::post('/confirmPermit', [RequestPermittController::class, 'confirmPermit']);
Route::post('/getOpenPermit', [RequestPermittController::class, 'getOpenPermit']);
Route::post('/openPermit', [RequestPermittController::class, 'openPermit']);

Route::post('/getHistory', [HistoryController::class, 'getHistory']);
Route::post('/storeHousekeeping', [HousekeepingController::class, 'storeHousekeeping']);

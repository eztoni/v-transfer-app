<?php

use App\Http\Controllers\LogController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});

Route::post('/reservation-notify',[\App\Http\Controllers\NotifyController::class,'update']);

Route::get('/remap-bookings',[\App\Http\Controllers\ReservationMapperController::class,'update']);

Route::get('/reservation-alert',[\App\Http\Controllers\ReservationAlertController::class,'update']);

Route::get('/price-load',[\App\Http\Controllers\PriceUpdateController::class,'update']);

Route::get('/scheduler', [\App\Http\Controllers\SchedulerRunController::class,'update']);

// LOG CONTROLLER
Route::get('/logs', [\App\Http\Controllers\LogController::class, 'show'])->name('logs');

//emailsend
Route::post('email', [\App\Http\Controllers\AlarmMailController::class, 'sendEmail']);

Route::get('/reception-report', [\App\Http\Controllers\ReservationReceptionNotifyController::class,'update']);

Route::get('/partner-report', [\App\Http\Controllers\PartnerReportNotifyController::class,'update']);

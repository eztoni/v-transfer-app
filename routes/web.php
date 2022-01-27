<?php

use App\Http\Controllers\AddNewServiceWizardController;
use App\Http\Controllers\EditRadniciController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\WorkersController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\EditUserController;
use App\Http\Livewire\CommentsOverview;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\EditAddPartner;
use App\Http\Livewire\EditAddWorker;
use App\Http\Livewire\PartnersOverview;
use App\Http\Livewire\PastTasksOverview;
use App\Http\Livewire\Statistics;
use App\Http\Livewire\TasksOverview;
use App\Http\Livewire\WorkersOverview;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {

    Route::group(['middleware' => ['role:super-admin']], function () {
        Route::get('laravel-logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('laravel-logs');
        Route::get('super-admin-dashboard', [SuperAdminDashboardController::class, 'show'])->name('super-admin-dashboard');
        Route::get('edit-user/{user}', [EditUserController::class, 'showUser'])->name('edit-user');
    });

    Route::get('/', Dashboard::class)->name('dashboard');


});



<?php

use App\Http\Controllers\EditUserController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Livewire\ActivityLogDashboard;
use App\Http\Livewire\CompanyOverview;
use App\Http\Livewire\LanguageOverview;

 /*
     |--------------------------------------------------------------------------
     | Super admin role routes
     |--------------------------------------------------------------------------
     | These routes will be available for these roles:
     |  - SUPER-ADMIN
 */


Route::get('/phpinfo', function () {return view('phpini');})->name('phpinfo');


Route::get('laravel-logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('laravel-logs');
Route::get('super-admin-dashboard', [SuperAdminDashboardController::class, 'show'])->name('super-admin-dashboard');
Route::get('/language-overview', LanguageOverview::class)->name('language-overview');
Route::get('edit-user/{user}', [EditUserController::class, 'showUser'])->name('edit-user');
Route::get('/company-overview', CompanyOverview::class)->name('company-overview');
Route::get('activity-log-dashboard', ActivityLogDashboard::class)->name('activity-log-dashboard');

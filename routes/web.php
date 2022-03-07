<?php

use App\Http\Controllers\LogController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\EditUserController;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Destinations;
use App\Http\Livewire\PointsOverview;
use App\Models\User;
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

    Route::middleware(
        ['role:' . User::ROLE_SUPER_ADMIN . '|' . User::ROLE_ADMIN . '|' . User::ROLE_USER]
    )->group(function () {
#------------------------------------------------------------------------------------------EVERYONE AUTHENTICATED
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('/master-data', Dashboard::class)->name('master-data');
        Route::get('/selling', Dashboard::class)->name('selling');
        Route::get('/bookings', Dashboard::class)->name('bookings');
        Route::get('/reports', Dashboard::class)->name('reports');

        //Age Groups
        Route::get('/age-groups', \App\Http\Livewire\AgeGroupOverview::class)->name('age-groups');
        Route::get('/age-group-categories/{ageGroup}', \App\Http\Livewire\AgeGroupCategories::class)->name('age-group-categories');
        //Routes
        Route::get('/routes-overview', \App\Http\Livewire\RoutesOverview::class)->name('routes-overview');
        Route::get('/partners-overview', \App\Http\Livewire\PartnersOverview::class)->name('partners-overview');
        //Extras
        Route::get('/extras-overview', \App\Http\Livewire\ExtrasOverview::class)->name('extras-overview');
        Route::get('/extras-edit/{extra}', \App\Http\Livewire\ExtrasEdit::class)->name('extras-edit');
#------------------------------------------------------------------------------------------EVERYONE AUTHENTICATED END
        Route::middleware(
            ['role:' . User::ROLE_SUPER_ADMIN . '|' . User::ROLE_ADMIN]
        )->group(function () {
#------------------------------------------------------------------------------------------ADMINS
            Route::prefix('admin')->name('admin.')->group(function () {
                Route::get('/points-overview', PointsOverview::class)->name('points-overview');
                Route::get('/company-overview', \App\Http\Livewire\CompanyOverview::class)->name('company-overview');
                Route::get('/destinations', Destinations::class)->name('destinations');
                Route::get('/user-overview', \App\Http\Livewire\UserOverview::class)->name('user-overview');
            });
#------------------------------------------------------------------------------------------ADMINS END
            Route::middleware(
                ['role:' . User::ROLE_SUPER_ADMIN]
            )->group(function () {
#------------------------------------------------------------------------------------------SUPERADMINS
                Route::get('laravel-logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('laravel-logs');
                Route::get('super-admin-dashboard', [SuperAdminDashboardController::class, 'show'])->name('super-admin-dashboard');
                Route::get('edit-user/{user}', [EditUserController::class, 'showUser'])->name('edit-user');
                Route::get('activity-log-dashboard', \App\Http\Livewire\ActivityLogDashboard::class)->name('activity-log-dashboard');
#------------------------------------------------------------------------------------------SUPERADMINS END

            });
        });
    });
});



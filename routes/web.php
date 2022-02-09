<?php

use App\Http\Controllers\LogController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\EditUserController;
use App\Http\Livewire\Dashboard;
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
#------------------------------------------------------------------------------------------EVERYONE AUTHENTICATED END
        Route::middleware(
            ['role:' . User::ROLE_SUPER_ADMIN . '|' . User::ROLE_ADMIN]
        )->group(function () {
#------------------------------------------------------------------------------------------ADMINS
            Route::prefix('admin')->name('admin.')->group(function () {
                Route::get('/admin/edit-user', Dashboard::class)->name('edit-user');
                Route::get('/admin/destinations', Dashboard::class)->name('destinations');
            });
#------------------------------------------------------------------------------------------ADMINS END
            Route::middleware(
                ['role:' . User::ROLE_SUPER_ADMIN]
            )->group(function () {
#------------------------------------------------------------------------------------------SUPERADMINS
                Route::get('laravel-logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('laravel-logs');
                Route::get('super-admin-dashboard', [SuperAdminDashboardController::class, 'show'])->name('super-admin-dashboard');
                Route::get('edit-user/{user}', [EditUserController::class, 'showUser'])->name('edit-user');
#------------------------------------------------------------------------------------------SUPERADMINS END

            });
        });
    });
});



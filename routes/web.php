<?php

use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\EditUserController;
use App\Http\Controllers\UploadImageController;
use App\Http\Livewire\ActivityLogDashboard;
use App\Http\Livewire\AgeGroupCategories;
use App\Http\Livewire\AgeGroupOverview;
use App\Http\Livewire\BookingOverview;
use App\Http\Livewire\CompanyDashboard;
use App\Http\Livewire\CompanyOverview;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Destinations;
use App\Http\Livewire\ExtrasEdit;
use App\Http\Livewire\ExtrasOverview;
use App\Http\Livewire\InternalReservation;
use App\Http\Livewire\PartnerEdit;
use App\Http\Livewire\PartnersOverview;
use App\Http\Livewire\PointsOverview;
use App\Http\Livewire\ReservationView;
use App\Http\Livewire\RoutesOverview;
use App\Http\Livewire\TransferOverview;
use App\Http\Livewire\UserOverview;
use App\Http\Livewire\VehicleEdit;
use App\Http\Livewire\VehicleOverview;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

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
    )->group(callback: function () {
        /*
            |--------------------------------------------------------------------------
            | User routes
            |--------------------------------------------------------------------------
        */
        include_once 'role-user.php';

        Route::middleware(
            ['role:' . User::ROLE_SUPER_ADMIN . '|' . User::ROLE_ADMIN]
        )->group(function () {
            /*
                |--------------------------------------------------------------------------
                | Admin routes
                |--------------------------------------------------------------------------
            */
            include_once 'role-admin.php';


            Route::middleware(
                ['role:' . User::ROLE_SUPER_ADMIN]
            )->group(function () {
                /*
                    |--------------------------------------------------------------------------
                    | SUPER  ADMIN ONLY ROUTES
                    |--------------------------------------------------------------------------
                */
                include_once 'role-super-admin.php';
            });
        });
    });
});


// For development only
if (!App::isProduction()) {
    Route::middleware(
        ['role:' . User::ROLE_SUPER_ADMIN]
    )->group(function () {


    });
}

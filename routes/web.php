<?php

use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\EditUserController;
use App\Http\Controllers\UploadImageController;
use App\Http\Livewire\ActivityLogDashboard;
use App\Http\Livewire\AgeGroupCategories;
use App\Http\Livewire\AgeGroupOverview;
use App\Http\Livewire\BookingOverview;
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
#------------------------------------------------------------------------------------------EVERYONE AUTHENTICATED
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('/master-data', Dashboard::class)->name('master-data');
        Route::get('/selling', Dashboard::class)->name('selling');
        Route::get('/bookings', BookingOverview::class)->name('bookings');
        Route::get('/reservation-view/{id}', ReservationView::class)->name('reservation-view');
        Route::get('/reports', Dashboard::class)->name('reports');


        //Age Groups
        Route::get('/age-groups', AgeGroupOverview::class)->name('age-groups');
        Route::get('/age-group-categories/{ageGroup}', AgeGroupCategories::class)->name('age-group-categories');
        //Routes
        Route::get('/routes-overview', \App\Http\Livewire\CRUD\RoutesOverview::class)->name('routes-overview');
        //Extras
        Route::get('/extras-overview', ExtrasOverview::class)->name('extras-overview');
        Route::get('/extras-edit/{extra}', ExtrasEdit::class)->name('extras-edit');
        //Vehicles
        Route::get('/vehicle-overview', VehicleOverview::class)->name('vehicle-overview');
        Route::get('/vehicle-edit/{vehicle}', VehicleEdit::class)->name('vehicle-edit');

        Route::get('/transfer-overview', TransferOverview::class)->name('transfer-overview');
        Route::get('/transfer-edit/{transfer}', \App\Http\Livewire\TransferEdit::class)->name('transfer-edit');

        Route::get('/internal-reservation', InternalReservation::class)->name('internal-reservation');

        Route::get('/transfer-prices', \App\Http\Livewire\TransferPrices::class)->name('transfer-prices');

#------------------------------------------------------------------------------------------EVERYONE AUTHENTICATED END
        Route::middleware(
            ['role:' . User::ROLE_SUPER_ADMIN . '|' . User::ROLE_ADMIN]
        )->group(function () {
#------------------------------------------------------------------------------------------ADMINS
            Route::prefix('admin')->name('admin.')->group(function () {
                Route::get('/points-overview', PointsOverview::class)->name('points-overview');
                Route::get('/company-overview', CompanyOverview::class)->name('company-overview');
                Route::get('/destinations', Destinations::class)->name('destinations');
                Route::get('/user-overview', UserOverview::class)->name('user-overview');
                Route::post('/upload-images', [UploadImageController::class, 'store'])->name('upload-images');
                Route::get('/company-dashboard', \App\Http\Livewire\CompanyDashboard::class)->name('company-dashboard');

                Route::get('/partners-overview', PartnersOverview::class)->name('partners-overview');
                Route::get('/partner-edit/{partner}', PartnerEdit::class)->name('partner-edit');
            });
#------------------------------------------------------------------------------------------ADMINS END
            Route::middleware(
                ['role:' . User::ROLE_SUPER_ADMIN]
            )->group(function () {
#------------------------------------------------------------------------------------------SUPERADMINS
                Route::get('laravel-logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('laravel-logs');
                Route::get('super-admin-dashboard', [SuperAdminDashboardController::class, 'show'])->name('super-admin-dashboard');
                Route::get('/language-overview', \App\Http\Livewire\LanguageOverview::class)->name('language-overview');
                Route::get('edit-user/{user}', [EditUserController::class, 'showUser'])->name('edit-user');
                Route::get('/company-overview', \App\Http\Livewire\CompanyOverview::class)->name('company-overview');
                Route::get('activity-log-dashboard', ActivityLogDashboard::class)->name('activity-log-dashboard');
#------------------------------------------------------------------------------------------SUPERADMINS END

            });
        });
    });
});



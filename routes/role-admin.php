<?php

use App\Http\Controllers\UploadImageController;
use App\Http\Livewire\CompanyDashboard;
use App\Http\Livewire\CompanyOverview;
use App\Http\Livewire\CRUD\OwnerOverview;
use App\Http\Livewire\CRUD\RoutesOverview;
use App\Http\Livewire\DestinationReport;
use App\Http\Livewire\Destinations;
use App\Http\Livewire\ExtrasEdit;
use App\Http\Livewire\ExtrasOverview;
use App\Http\Livewire\NewTransferPrices;
use App\Http\Livewire\PartnerEdit;
use App\Http\Livewire\PartnersOverview;
use App\Http\Livewire\PointsOverview;
use App\Http\Livewire\TransferEdit;
use App\Http\Livewire\TransferOverview;
use App\Http\Livewire\UserOverview;
use App\Http\Livewire\VehicleEdit;
use App\Http\Livewire\VehicleOverview;

/*
    |--------------------------------------------------------------------------
    | Admin role routes
    |--------------------------------------------------------------------------
    | These routes will be available for these roles:
    |  - SUPER-ADMIN
    |  - ADMIN
*/


// Reports
Route::get('/reports', DestinationReport::class)->name('reports');
Route::get('/partner-reports', DestinationReport::class)->name('partner-reports');
//Routes
Route::get('/routes-overview', RoutesOverview::class)->name('routes-overview');
//Owner
Route::get('/owner-overview', OwnerOverview::class)->name('owner-overview');
//Extras
Route::get('/extras-overview', ExtrasOverview::class)->name('extras-overview');
Route::get('/extras-edit/{extraId}', ExtrasEdit::class)->name('extras-edit');
//Vehicles
Route::get('/vehicle-overview', VehicleOverview::class)->name('vehicle-overview');
Route::get('/vehicle-edit/{vehicleId}', VehicleEdit::class)->name('vehicle-edit');

Route::get('/transfer-overview', TransferOverview::class)->name('transfer-overview');
Route::get('/transfer-edit/{transferId}', TransferEdit::class)->name('transfer-edit');
Route::get('/transfer-prices', NewTransferPrices::class)->name('transfer-prices');


// Prefixed admin routes. There is no difference other than /admin/ prefix in url
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/points-overview', PointsOverview::class)->name('points-overview');
    Route::get('/company-overview', CompanyOverview::class)->name('company-overview');
    Route::get('/destinations', Destinations::class)->name('destinations');
    Route::get('/user-overview', UserOverview::class)->name('user-overview');
    Route::post('/upload-images', [UploadImageController::class, 'store'])->name('upload-images');
    Route::get('/company-dashboard', CompanyDashboard::class)->name('company-dashboard');

    Route::get('/partners-overview', PartnersOverview::class)->name('partners-overview');
    Route::get('/partner-edit/{partner}', PartnerEdit::class)->name('partner-edit');
});

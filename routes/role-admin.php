<?php

use App\Events\ReservationUpdatedEvent;
use App\Events\ReservationWarningEvent;
use App\Http\Controllers\UploadImageController;
use App\Http\Livewire\CompanyDashboard;
use App\Http\Livewire\CompanyOverview;
use App\Http\Livewire\CRUD\OwnerOverview;
use App\Http\Livewire\CRUD\RoutesOverview;
use App\Http\Livewire\DestinationReport;
use App\Http\Livewire\Destinations;
use App\Http\Livewire\DestinationSetupChecker;
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
use App\Http\Livewire\PartnerDaily;
use App\Http\Livewire\AgentEfficiency;
use App\Models\Point;
use App\Models\Reservation;

/*
    |--------------------------------------------------------------------------
    | Admin role routes
    |--------------------------------------------------------------------------
    | These routes will be available for these roles:
    |  - SUPER-ADMIN
    |  - ADMIN
*/


// Reports
Route::get('/partner-report', DestinationReport::class)->name('partner-report');
Route::get('/ppom-report', DestinationReport::class)->name('ppom-report');
Route::get('/rpo-report', DestinationReport::class)->name('rpo-report');
Route::get('/agent-report', DestinationReport::class)->name('agent-report');

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

Route::get('/agent-efficiency',\App\Http\Livewire\AgentEfficiency::class)->name('agent-efficiency');

Route::get('/mail-test',function(){

    $reservation = Reservation::findOrFail(163);

    $price = $reservation->price;

    if($reservation->isRoundTrip && $reservation->returnReservation->status == 'confirmed'){
        $price = $price*2;
    }

    if($reservation->isRoundTrip && $reservation->status == 'cancelled' && $reservation->returnReservation->status == 'confirmed'){
        $price = $reservation->returnReservation->price;
    }


    $amount = number_format($price/100,2,'.','');

    dd($amount);
});

// Prefixed admin routes. There is no difference other than /admin/ prefix in url
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/points-overview', PointsOverview::class)->name('points-overview');
    Route::get('/destination-setup-checker', DestinationSetupChecker::class)->name('destination-setup-checker');
    Route::get('/company-overview', CompanyOverview::class)->name('company-overview');
    Route::get('/destinations', Destinations::class)->name('destinations');
    Route::get('/user-overview', UserOverview::class)->name('user-overview');
    Route::post('/upload-images', [UploadImageController::class, 'store'])->name('upload-images');
    Route::get('/company-dashboard', CompanyDashboard::class)->name('company-dashboard');
    Route::get('/partners-overview', PartnersOverview::class)->name('partners-overview');
    Route::get('/partner-edit/{partner}', PartnerEdit::class)->name('partner-edit');
});

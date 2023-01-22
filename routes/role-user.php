<?php
use App\Http\Livewire\BookingOverview;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\InternalReservation;
use App\Http\Livewire\ReservationDetails;

 /*
     |--------------------------------------------------------------------------
     | User role routes
     |--------------------------------------------------------------------------
     | These routes will be available for these roles:
     |  - SUPER-ADMIN
     |  - ADMIN
     |  - USER
 */


Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/bookings', BookingOverview::class)->name('bookings');
Route::get('/reservation-details/{reservation}', ReservationDetails::class)->name('reservation-details');
Route::get('/internal-reservation', InternalReservation::class)->name('internal-reservation');

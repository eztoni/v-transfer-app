<?php
use App\Http\Livewire\BookingOverview;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\InternalReservation;
use App\Http\Livewire\PartnerDaily;
use App\Http\Livewire\ReservationDetails;
use App\Models\Point;
use App\Models\Reservation;

/*
    |--------------------------------------------------------------------------
    | User role routes
    |--------------------------------------------------------------------------
    | These routes will be available for these roles:
    |
    |  - RECEPTION
*/


Route::get('/partner-daily', PartnerDaily::class)->name('partner-daily');


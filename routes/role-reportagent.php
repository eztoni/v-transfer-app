<?php
use App\Http\Livewire\BookingOverview;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\DestinationReport;
use App\Http\Livewire\InternalReservation;
use App\Http\Livewire\PartnerDaily;
use App\Http\Livewire\ReservationDetails;
use App\Models\Point;
use App\Models\Reservation;
use App\Http\Livewire\AgentEfficiency;


/*
    |--------------------------------------------------------------------------
    | User role routes
    |--------------------------------------------------------------------------
    | These routes will be available for these roles:
    |
    |  - REPORTAGENT
*/


Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/partner-report', DestinationReport::class)->name('partner-report');
Route::get('/ppom-report', DestinationReport::class)->name('ppom-report');
Route::get('/rpo-report', DestinationReport::class)->name('rpo-report');
Route::get('/agent-report', DestinationReport::class)->name('agent-report');
Route::get('/agent-efficiency',\App\Http\Livewire\AgentEfficiency::class)->name('agent-efficiency');
Route::get('/partner-daily', PartnerDaily::class)->name('partner-daily');



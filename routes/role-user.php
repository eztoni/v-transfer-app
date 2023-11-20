<?php

use App\Models\Point;
use App\Http\Livewire\BookingOverview;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\InternalReservation;
use App\Http\Livewire\ReservationDetails;
use App\Models\Reservation;

/*
    |--------------------------------------------------------------------------
    | User role routes
    |--------------------------------------------------------------------------
    | These routes will be available for these roles:
    |  - SUPER-ADMIN
    |  - ADMIN
    |  - USER
       - RECEPTION
*/


Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/bookings', BookingOverview::class)->name('bookings');
Route::get('/reservation-details/{reservation}', ReservationDetails::class)->name('reservation-details');
Route::get('/internal-reservation', InternalReservation::class)->name('internal-reservation');

Route::get('preview_partner_mail_list/{accommodation}/{date_from}/{date_to}',function ($accommodation,$date_from,$date_to){


    $reservations = Reservation::query()
        ->whereIsMain(true)
        ->with(['leadTraveller', 'pickupLocation', 'dropoffLocation', 'returnReservation'])
        ->where('status',Reservation::STATUS_CONFIRMED)
        ->where(function ($q) use($date_from,$date_to) {
            $q->where(function ($q) use($date_from,$date_to){
                $q->whereDate('date_time', '>=', $date_from)
                    ->whereDate('date_time', '<=',  $date_to);
            })->orWHereHas('returnReservation',function ($q)use($date_from,$date_to){
                $q->whereDate('date_time', '>=',  $date_from)
                    ->whereDate('date_time', '<=',  $date_to);
            });
        })->where(function($q) use($accommodation) {
            $q->where('pickup_address_id',$accommodation)
                ->orWhere('dropoff_address_id',$accommodation);
        })->get();


    $accommodation_name = Point::findOrFail($accommodation)->name;

    return  \App\Actions\Attachments\GenerateTransferOverviewPDF::generate(\Carbon\Carbon::make($date_from),\Carbon\Carbon::make($date_to),$reservations,$accommodation_name)->download('reception_report.pdf');
});

Route::get('download_document/{type}/{reservation_id}',function($type,$reservation_id){

    $reservation = Reservation::findOrFail($reservation_id);
    $file_name = 'BookingConfirmation'.$reservation_id.'.pdf';

    switch ($type){
        case 'booking-confirmation':
            $view = 'attachments.booking_confirmation';
            break;
        case 'booking-cancellation':
            $view = 'attachments.booking_cancellation';
            $file_name = 'BookingCancellation'.$reservation_id.'.pdf';
            break;
        case 'booking-cancellation-fee':
            $view = 'attachments.booking_cancellation_fee';
            $file_name = 'BookingCancellationFee'.$reservation_id.'.pdf';
            break;
        case 'download-voucher':
            $view = 'attachments.voucher';
            $file_name = 'BookingVoucher_'.$reservation_id.'.pdf';
    }


    return \Barryvdh\DomPDF\Facade\Pdf::loadView($view, ['reservation'=> $reservation])->setPaper('A4', 'portrait')->set_option('isRemoteEnabled', true)->download($file_name);
});

<?php

namespace App\Http\Controllers;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Models\Traveller;
use App\Services\Api\ValamarClientApi;
use App\Services\Api\ValamarOperaApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Point;
use DB;

class ReservationMapperController extends Controller
{

    const RESERVATION_STATUS_ARRAY = array(
        'NEW',
        'RESERVED',
        'CANCEL',
        'CHECKED OUT',
        'CHECKED IN',
        'NO SHOW'
    );

    private $api_handler;

    function __construct()
    {
        $this->api_handler = new ValamarClientApi();
    }

    public function update(){

        $incomplete_bookings = array();

        $future_bookings = Reservation::query()->where('is_main',1)
            ->where('date_time','>=',Carbon::now()->format('Y-m-d h:i:s'))
            ->where('status','confirmed')
            ->where('opera_sync',0)
            ->get();

        dd($future_bookings);
        if(!empty($future_bookings)){
            foreach($future_bookings as $booking){

                $booking_mappable = true;

                if($booking->leadTraveller->reservation_number){
                   if(!$booking->leadTraveller->reservation_opera_id || !$booking->leadTraveller->reservation_opera_confirmation){
                      $incomplete_bookings[] = $booking;
                   }
                }else{
                    $booking_mappable = false;
                }
            }
        }

        if(!empty($incomplete_bookings)){

           $opera_sync = new ValamarOperaApi();

           foreach($incomplete_bookings as $booking){

               $this->api_handler->setReservationCodeFilter($booking->leadTraveller->reservation_number);

               $res_info = $this->api_handler->getReservationList();

               if(!empty($res_info[mb_strtolower($booking->leadTraveller->reservation_number)])){

                  $live_booking = $res_info[mb_strtolower($booking->leadTraveller->reservation_number)];

                  $leadTraveller = Traveller::findOrFail($booking->leadTraveller->id);

                  $leadTraveller->reservation_opera_id = $live_booking['OPERA']['RESV_NAME_ID'];
                  $leadTraveller->reservation_opera_confirmation = $live_booking['OPERA']['CONFIRMATION_NO'];

                  $leadTraveller->save();

                  $opera_sync->syncReservationWithOperaFull($booking->id);
               }
           }
        }

    }

}

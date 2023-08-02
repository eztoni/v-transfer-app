<?php

namespace App\Http\Controllers;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Events\ReservationAlertEvent;
use App\Models\Traveller;
use App\Services\Api\ValamarClientApi;
use App\Services\Api\ValamarOperaApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Point;
use DB;

class ReservationAlertController extends Controller
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

        $missing_res_number = array();
        $not_synced = array();
        $has_data_failed_sync = array();

        dd("pk");

        $bookings = Reservation::query()->where('is_main',1)
            ->where('date_time','>=',Carbon::now()->format('Y-m-d h:i:s'))
            ->where('created_at','>=',Carbon::now()->sub(1)->format('Y-m-d h:i:s'))
            ->where('status','confirmed')
            ->where('opera_sync',0)
            ->get();



        if(!empty($bookings)){
            foreach($bookings as $booking){

                if(!$booking->leadTraveller->reservation_number){
                    $missing_res_number[] = $booking;
                }elseif(!$booking->leadTraveller->reservation_opera_id || !$booking->leadTraveller->reservation_opera_confirmation){
                    $not_synced[] = $booking;
                }else{
                    $has_data_failed_sync[] = $booking;
                }
            }
        }

        if(!empty($missing_res_number) || !empty($not_synced) || !empty($has_data_failed_sync)){

            $alert_report = array();

            if(!empty($missing_res_number)){
                $alert_report['missing_reservation_number'] = $missing_res_number;
            }

            if(!empty($not_synced)){
                $alert_report['not_synced'] = $not_synced;
            }

            if(!empty($has_data_failed_sync)){
                $alert_report['has_data_failed_sync'] = $has_data_failed_sync;
            }

            dd("test");

            if(!empty($alert_report)){
                ReservationAlertEvent::dispatch($alert_report,[
                    ReservationAlertEvent::SEND_MAIL_CONFIG_PARAM => true
                ]);
            }
        }

    }

}

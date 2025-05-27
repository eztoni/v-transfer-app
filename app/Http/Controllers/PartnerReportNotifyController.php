<?php

namespace App\Http\Controllers;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Events\ReservationAlertEvent;
use App\Events\ReservationReportEvent;
use App\Mail\Guest\ReservationConfirmationMail;
use App\Mail\Guest\ReservationReceptionReportMail;
use App\Models\Traveller;
use App\Services\Api\ValamarClientApi;
use App\Services\Api\ValamarFiskalizacija;
use App\Services\Api\ValamarOperaApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Partner;
use App\Models\Point;
use DB;

class PartnerReportNotifyController extends Controller
{

    private $bookings_per_partner = array();
    private $accommodation_loader = array();

    private $partner_loader = array();


    function __construct()
    {

    }

    public function update(){



        $points = Point::query()->where('type','accommodation')->get();

        $tomorrow = strtotime('+1 day');

        $date_from = gmdate('Y-m-d',$tomorrow);
        $date_to = gmdate('Y-m-d',$tomorrow);

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
            })->get();

        if(!empty($reservations)){

            foreach($reservations as $reservation){


                $accommodation_id = $reservation->pickupAddress->type == 'accommodation' ? $reservation->pickup_address_id : $reservation->dropoff_address_id;

                $partner_id = $reservation->partner_id;

                if(empty($this->partner_loader[$partner_id])){
                    $this->partner_loader[$partner_id] = Partner::findOrFail($partner_id);
                }

                $this->bookings_per_partner[$partner_id][] = $reservation;

                if(empty($this->accommodation_loader[$accommodation_id])){
                    $this->accommodation_loader[$accommodation_id] = Point::findOrFail($accommodation_id);
                }
            }
        }


        if(!empty($this->bookings_per_partner)){


            foreach($this->bookings_per_partner as $partner_id => $reservation_list){

                $email_list = array();

                $partner_name = 'Unknown Partner';

                if(!empty($this->partner_loader[$partner_id])){
                  //  $email_list[] = $this->partner_loader[$partner_id]->email;
                    $partner_name = $this->partner_loader[$partner_id]->name;
                }


                $email_list[] = 'njiric.toni@gmail.com';

                $reservation_list = collect($reservation_list);

                $subject = ': Popis rezervacija za datum '.$date_from.' - '.$partner_name;

                Mail::to($email_list)->locale('hr')->send(new ReservationReceptionReportMail($reservation_list,$subject,$date_from,$date_to,$accommodation_name));

            }
        }


    }

}

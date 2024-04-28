<?php

namespace App\Http\Controllers;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
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
use App\Models\Point;
use DB;

class ReservationReceptionNotifyController extends Controller
{

    private $bookings_per_property = array();
    private $accommodation_loader = array();


    function __construct()
    {

    }

    public function update(){

        $points = Point::query()->where('type','accommodation')->get();

        $date_from = gmdate('Y-m-d');
        $date_to = gmdate('Y-m-d');

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
                $accommodation_id = $reservation->pickupLocation->type == 'accommodation' ? $reservation->pickup_addres_id : $reservation->dropoff_address_id;
                $this->bookings_per_property[$accommodation_id][] = $reservation;

                if(empty($this->accommodation_loader[$accommodation_id])){
                    $this->accommodation_loader[$accommodation_id] = Point::findOrFail($accommodation_id);
                }
            }
        }

        echo "Broj rezevacija je ".count($reservations);

        if(!empty($this->bookings_per_property)){
            foreach($this->bookings_per_property as $accommodation_id => $reservation_list){

                $accommodation_name = "Unknown property";

                if(!empty($this->accommodation_loader[$accommodation_id])){
                    $accommodation_name = $this->accommodation_loader[$accommodation_id]->name;
                }

                $travellerMail = 'njiric.toni@mail.com';
                $reservation_list = collect($reservation_list);

                $subject = $accommodation_name.': Popis rezervacija za datum '.$date_from.' - '.$accommodation_name;

                Mail::to($travellerMail)->locale('hr')->send(new ReservationReceptionReportMail($reservation_list,$subject,$date_from,$date_to,$accommodation_name));
            }
        }


    }

}

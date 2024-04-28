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


        if(!empty($this->bookings_per_property)){
            foreach($this->bookings_per_property as $accommodation_id => $reservation_list){

                $accommodation_name = "Unknown property";

                if(!empty($this->accommodation_loader[$accommodation_id])){
                    $accommodation_name = $this->accommodation_loader[$accommodation_id]->name;
                }

                $email_list = array($this->accommodation_loader[$accommodation_id]->reception_email);

                $reservation_list = collect($reservation_list);

                if($accommodation_id > 0){
                    switch($accommodation_id){
                        #LaCroma
                        case 34:
                            $email_list[] = 'ozana.simunovic@imperial.hr';
                            $email_list[] = 'transfer.dubrovnik@traveler.agency';
                            break;
                        #Tirena
                        case 65:
                            $email_list[] = 'dragan.stankovic@imperial.hr';
                            $email_list[] = 'transfer.dubrovnik@traveler.agency';
                            break;
                        #Solitudo Camping
                        case 134:
                            $email_list[] = 'ivona.camo@valamar.com';
                            $email_list[] = 'transfer.dubrovnik@traveler.agency';
                            break;
                        #Argosy
                        case 18:
                            $email_list[] = 'mirko.komnenovic@valamar.com';
                            $email_list[] = 'transfer.dubrovnik@traveler.agency';
                            break;
                        case 17:
                            #President
                            $email_list[] = 'jasmina.kneziccumo@valamar.com';
                            $email_list[] = 'transfer.dubrovnik@traveler.agency';
                            break;
                        case 35:
                            #President
                            $email_list[] = 'josip.begusic@imperial.hr';
                            $email_list[] = 'transfer.dubrovnik@traveler.agency';
                            break;
                    }
                }
                
                $email_list[] = 'njiric.toni@gmail.com';

                $subject = $accommodation_name.': Popis rezervacija za datum '.$date_from.' - '.$accommodation_name;

                Mail::to($email_list)->locale('hr')->send(new ReservationReceptionReportMail($reservation_list,$subject,$date_from,$date_to,$accommodation_name));

            }
        }


    }

}

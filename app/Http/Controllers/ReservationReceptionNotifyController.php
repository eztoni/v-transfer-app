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

                $accommodation_email = $this->accommodation_loader[$accommodation_id]->reception_email;

                $email_list = array();
                if(!empty($accommodation_email)){
                    $email_list[] = $accommodation_email;
                }


                $reservation_list = collect($reservation_list);

                if($accommodation_id > 0){
                    switch($accommodation_id){
                        #Isabella
                        case 31:
                            $email_list[] = 'igor.bratovic@valamar.com';
                            break;
                        #Marea
                        case 4:
                            $email_list[] = 'tara.redzic@valamar.com';
                            break;
                        #Valamar Tamaris Resort
                        case 70:
                            $email_list[] = 'ines.sumic@valamar.com';
                            break;
                        #Lanterna Resort
                        case 30:
                            $email_list[] = 'davor.korlevic@valamar.com';
                            break;
                        #Solaris Camping Resort
                        case 69:
                            $email_list[] = 'martina.liovic@valamar.com';
                            break;
                        #Parentino
                        case 36:
                            $email_list[] = 'nada.bozic@imperial.hr';
                            break;
                        #Riviera
                        case 68:
                            $email_list[] = 'kristina.telezar@valamar.com';
                            break;
                        #Crystal Sunny
                        case 66:
                            $email_list[] = 'dragan.ruzic@valamar.com';
                            break;
                        #Rubin Sunny
                        case 67:
                            $email_list[] = 'fran.volovic@valamar.com';
                            break;
                        #Lanterna Premium Camping
                        case 151:
                            $email_list[] = 'danijela.gergeta@valamar.com';
                            break;
                        #Orsera
                        case 150:
                            $email_list[] = 'ivana.jugovac2@valamar.com';
                            break;
                        #Istra Premium Camping
                        case 149:
                            $email_list[] = 'danijela.kljucec@valamar.com';
                            break;
                        #Miramar Sunny plus
                        case 73:
                            $email_list[] = 'dejana.demarin@valamar.com';
                            break;
                        #Allegro
                        case 71:
                            $email_list[] = 'samanta.luketicpeteani@valamar.com';
                            break;
                        #Allegro
                        case 72:
                            $email_list[] = 'goran.smokovic@valamar.com';
                            break;
                        #Corinthia
                        case 77:
                        #Atrium
                        case 76:
                        #Zvonimir
                        case 79:
                            $email_list[] = 'igor.brkljac@valamar.com';
                            break;
                        #Ježevac
                        case 80:
                            $email_list[] = 'mirela.obradovic@valamar.com';
                            break;
                        #Krk Premium Camping
                        case 81:
                            $email_list[] = 'vlatka.morozin@valamar.com';
                            break;
                        #Tunarica
                        case 161:
                            $email_list[] = 'iva.milevoj@valamar.com';
                            break;
                        #Škrila
                        case 153:
                            $email_list[] = 'franko.crncic@valamar.com';
                            break;
                        #Marina Camping
                        case 160:
                            $email_list[] = 'leonida.stemberga@valamar.com';
                            break;
                        #Bellevue
                        case 74:
                            $email_list[] = 'tamara.simec@valamar.com';
                            break;
                        #Baška Beach Camping
                        case 154:
                            $email_list[] = 'dajana.rakic@valamar.com';
                            break;
                        #Bunculuka
                        case 152:
                            $email_list[] = 'suzane.pavelic@valamar.com';
                            break;
                        #Valamar Padova Hotel
                        case 144:
                            $email_list[] = 'sanja.macolic@imperial.hr';
                            break;
                        #Imperial Rab
                        case 143:
                            $email_list[] = 'anamarija.precca@imperial.hr';
                            break;
                        #Valamar Carolina
                        case 141:
                        #Suha Punta
                        case 142:
                            $email_list[] = 'deborah.huncek@imperial.hr';
                            break;
                        #Padova Camping Resort
                        case 145:
                            $email_list[] = 'miljenko.matusan@imperial.hr';
                            break;
                        #Meteor
                        case 105:
                            $email_list[] = 'jelena.martic@imperial.hr';
                            break;
                        #Dalmacija Places
                        case 104:
                            $email_list[] = 'ana.cvitanovic@imperial.hr';
                            break;
                        #San Marino Camping Resort
                        case 146:
                            $email_list[] = 'ines.pahljina@imperial.hr';
                            break;
                        #San Marino TN ?
                        case 147:
                            $email_list[] = 'petar.macolic@imperial.hr';
                            break;
                        #Diamant Hotel and residence
                        case 3:
                        case 29:
                            $email_list[] = 'ivan.gostic@valamar.com';
                            break;
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
                            #Club Dubrovnik Sunny Hotel
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

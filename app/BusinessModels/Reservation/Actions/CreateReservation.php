<?php

namespace App\BusinessModels\Reservation\Actions;

use App\BusinessModels\Reservation\Reservation;
use App\Events\ReservationCreatedEvent;
use App\Models\Transfer;
use App\Models\Traveller;
use App\Services\Api\ValamarClientApi;
use App\Services\Api\ValamarFiskalizacija;
use App\Services\Api\ValamarOperaApi;
use App\Services\Helpers\ReservationPartnerOrderCache;
use Carbon\Carbon;
use Cknow\Money\Money;
use Illuminate\Support\Collection;

class CreateReservation extends Reservation
{
    public function setRequiredAttributes(
        int        $destionationId,
        Carbon     $dateTime,
        int        $pickupLocation,
        string     $pickupAddress,
        int        $dropoffLocation,
        string     $dropoffAddress,
        int        $adults,
        int        $children,
        int        $infants,
        int             $partnerId,
        Money           $price,
        string          $confirmationLanguage,
        Collection      $extras,
        Transfer        $transfer,
        array           $priceBreakdown,
        string          $remark='',
        string          $flightNumber ='',
        array           $childSeats = [],
        int             $luggage = 0,
        int|string|null $pickupAddressId = null,
        int|string|null $dropoffAddressId= null,
        bool $includedInAccommodationReservation= false,
        string $rate_plan = '',
        bool $vlevelrateplanReservation = false

    ): void
    {
        $this->model->destination_id = $destionationId;
        $this->model->date_time = $dateTime;
        $this->model->pickup_location = $pickupLocation;
        $this->model->pickup_address = $pickupAddress;
        $this->model->dropoff_location = $dropoffLocation;
        $this->model->dropoff_address = $dropoffAddress;
        $this->model->adults = $adults;
        $this->model->children = $children;
        $this->model->infants = $infants;
        $this->model->partner_id = $partnerId;
        $this->model->price = $price->getAmount();
        $this->model->child_seats =$childSeats;
        $this->model->flight_number = $flightNumber;
        $this->model->remark = $remark;
        $this->model->transfer_id = $transfer->id;
        $this->model->price_breakdown =  $priceBreakdown;
        $this->extras = $extras;
        $this->model->luggage = $luggage;
        $this->model->confirmation_language = $confirmationLanguage;
        $this->model->pickup_address_id = $pickupAddressId;
        $this->model->dropoff_address_id = $dropoffAddressId;
        $this->model->included_in_accommodation_reservation = $includedInAccommodationReservation;
        $this->model->rate_plan = $rate_plan;
        $this->model->v_level_reservation = $vlevelrateplanReservation;

    }
    public function saveReservation(): int
    {

        $this->validateReservation();

        \DB::transaction(function (){
            $this->model->save();
            $this->model->travellers()->save($this->leadTraveller);
            $this->model->extras()->saveMany($this->extras);
            foreach ($this->otherTravellers as $k => $traveller) {
                $this->model->travellers()->save($traveller, ['comment' => $this->travellerComments[$k], 'lead' => false]);
            }

            if ($this->roundTrip) {
                $this->saveRoundTrip();
            }


            if($this->model->included_in_accommodation_reservation == 0 && $this->model->v_level_reservation == 0){
                #Send Reservation To Opera
                $OperaAPI = new ValamarOperaApi();
                $OperaAPI->syncReservationWithOperaFull($this->model->id);
            }


            #Send To Invoicing
            $fiskalAPI = new ValamarFiskalizacija($this->model->id);
            $fiskalAPI->fiskalReservation();

            if($this->model->getInvoiceData('zki') != '-'){
                ReservationCreatedEvent::dispatch($this->model,[
                    ReservationCreatedEvent::SEND_MAIL_CONFIG_PARAM => $this->sendMail
                ]);
            }
        });

        //Cache the order
        $partnerOrder = new ReservationPartnerOrderCache($this->model->destination_id);
        $partnerOrder->cacheDestinationPartners();

        return $this->model->id;
    }

    public function fiskalInvoice($reservation_id){
        if($reservation_id > 0){
            $invoicing = new ValamarFiskalizacija($reservation_id);
            $invoicing->fiskalReservation();
        }
    }

    public function sendReservationToOpera(){
        $operaAPI = new ValamarOperaApi();
        $operaAPI->syncReservationWithOperaFull($this->model->id);
    }

    private function saveRoundTrip()
    {
        $roundTrip = $this->model->replicate();

        $roundTrip->pickup_location = $this->model->dropoff_location;
        $roundTrip->pickup_address = $this->model->dropoff_address;
        $roundTrip->dropoff_location = $this->model->pickup_location;
        $roundTrip->dropoff_address = $this->model->pickup_address;
        $roundTrip->date_time = $this->returnDate;
        $roundTrip->flight_number = $this->returnFlightNumber;
        $roundTrip->is_main = false;

        $roundTrip->save();
        $roundTrip->extras()->saveMany($this->extras);
        $roundTrip->travellers()->save($this->leadTraveller);
        foreach ($this->otherTravellers as $k => $traveller) {
            $roundTrip->travellers()->save($traveller, ['comment' => $this->travellerComments[$k], 'lead' => false]);
        }

        $this->model->round_trip_id = $roundTrip->id;
        $this->model->save();

    }


    public function setRoundTrip(Carbon $returnDateTime,string $flightNumber = '')
    {
        $this->roundTrip = true;
        $this->returnDate = $returnDateTime;
        $this->returnFlightNumber = $flightNumber;
    }

    public function addLeadTraveller(Traveller $traveller)
    {
        $this->leadTraveller = $traveller;
        return $this;
    }

    public function setSendMail(bool $sendMail)
    {
        $this->sendMail = $sendMail;
        return $this;
    }

    public function addOtherTraveller(Traveller $traveller, $comment)
    {
        $this->travellerComments[] = $comment;
        $this->otherTravellers->push($traveller);

        return $this;
    }
}

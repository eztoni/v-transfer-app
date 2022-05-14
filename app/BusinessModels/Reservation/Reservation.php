<?php

namespace App\BusinessModels\Reservation;

use App\Models\Extra;
use App\Models\Transfer;
use App\Models\Traveller;
use App\Services\Helpers\ReservationPartnerOrderCache;
use Carbon\Carbon;
use Cknow\Money\Money;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class Reservation
{


    private \App\Models\Reservation $model;
    private bool $roundTrip = false;
    private Traveller $leadTraveller;
    private Collection $otherTravellers ;
    private array $travellerComments;

    private $returnDate;
    private $returnTime;
    private $returnFlightNumber;


    public function __construct(\App\Models\Reservation $model)
    {
        $this->model = $model;
        $this->otherTravellers = collect([]);
    }


    public function saveReservation():int
    {
        //WILL SHOW IN LIVEWIRE
        $validator = Validator::make($this->model->toArray(), [
            'transfer' => 'required|array',
            'transfer.vehicle'=>'required'
        ]);


        if($validator->fails()){
            throw new \InvalidArgumentException($validator->errors());
        }

        $this->model->save();
        $this->model->travellers()->save($this->leadTraveller);

        foreach ($this->otherTravellers as $k => $traveller ){
            $this->model->travellers()->save($traveller,['comment'=>$this->travellerComments[$k],'lead'=>false]);
        }

        if($this->roundTrip){
            $this->saveRoundTrip();
        }

        $partnerOrder = new ReservationPartnerOrderCache($this->model->destination_id);
        $partnerOrder->cacheDestinationPartners();

        return $this->model->id;
    }


    private function saveRoundTrip(){
        $roundTrip = $this->model->replicate();

        $roundTrip->date = $this->returnDate;
        $roundTrip->time = $this->returnTime;
        $roundTrip->flight_number = $this->returnFlightNumber;
        $roundTrip->round_trip = $this->model->id;


        $roundTrip->save();
        $roundTrip->travellers()->save($this->leadTraveller);

        $this->model->round_trip = $roundTrip->id;
        $this->model->save();

        foreach ($this->otherTravellers as $k => $traveller ){
            $roundTrip->travellers()->save($traveller,['comment'=>$this->travellerComments[$k],'lead'=>false]);
        }


    }


    public function roundTrip($returnDate,$returnTime,$flightNumber)
    {
        $this->roundTrip = true;
        $this->model->round_trip = true;
        $this->returnDate = $returnDate;
        $this->returnTime = $returnTime;
        $this->returnFlightNumber = $flightNumber;
    }

    public function addLeadTraveller(Traveller $traveller)
    {
        $this->leadTraveller = $traveller;
        return $this;
    }

    public function addOtherTraveller(Traveller $traveller,$comment)
    {
        $this->travellerComments[] = $comment;
        $this->otherTravellers->push($traveller);

        return $this;
    }

    public function setRequiredAttributes(
        int $destionationId,
        Carbon $date,
        Carbon $time,
        int $pickupLocation,
        string $pickupAddress,
        int $dropoffLocation,
        string $dropoffAddress,
        int $adults,
        int $children,
        int $infants,
        int $partnerId,
        Money $price,
        array $childSeats,
        string $flightNumber,
        string $remark,
        string $confirmationLanguage,
        array $routeData,
        Collection $extras,
        Transfer $transfer,
        int $luggage = 0,

    ): void
    {

        $this->model->destination_id = $destionationId;
        $this->model->date = $date;
        $this->model->time = $time;
        $this->model->pickup_location = $pickupLocation;
        $this->model->pickup_address = $pickupAddress;
        $this->model->dropoff_location = $dropoffLocation;
        $this->model->dropoff_address = $dropoffAddress;
        $this->model->adults = $adults;
        $this->model->children = $children;
        $this->model->infants = $infants;
        $this->model->partner_id = $partnerId;
        $this->model->price = $price->getAmount();
        $this->model->child_seats = json_encode($childSeats);
        $this->model->flight_number = $flightNumber;
        $this->model->remark = $remark;
        $this->model->route =json_encode( $routeData);
        $this->model->transfer = $transfer->loadMissing('vehicle');
        $this->model->extras = $extras->toJson();
        $this->model->luggage = $luggage;
        $this->model->confirmation_language = $confirmationLanguage;

    }


}

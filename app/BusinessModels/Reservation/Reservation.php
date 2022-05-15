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
    private Collection $otherTravellers;
    private array $travellerComments;

    private Collection $extras;

    private $returnDate;
    private $returnTime;
    private $returnFlightNumber;    


    public function __construct(\App\Models\Reservation $model)
    {
        $this->model = $model;
        $this->otherTravellers = collect([]);
    }
    public function setRequiredAttributes(
        int        $destionationId,
        Carbon     $date,
        Carbon     $time,
        int        $pickupLocation,
        string     $pickupAddress,
        int        $dropoffLocation,
        string     $dropoffAddress,
        int        $adults,
        int        $children,
        int        $infants,
        int        $partnerId,
        Money      $price,
        string     $confirmationLanguage,
        Collection $extras,
        Transfer   $transfer,
        string     $remark='',
        string     $flightNumber ='',
        array      $childSeats = [],
        int        $luggage = 0,

    ): void
    {
        $this->model->destination_id = $destionationId;
        $this->model->date = $date;
        $this->model->time = $time->format('H:i');
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
        $this->model->transfer_id = $transfer->id;
        $this->extras = $extras;
        $this->model->luggage = $luggage;
        $this->model->confirmation_language = $confirmationLanguage;

    }

    private function validateModel()
    {
        $validator = Validator::make($this->model->toArray(),
            [
                'destination_id' => 'required',
                'date' => 'required|date',
                'time' => 'required',
                'pickup_location' => 'required|integer',
                'pickup_address' => 'required|string',
                'dropoff_location' => 'required|integer',
                'dropoff_address' => 'required|string',
                'adults' => 'required|integer|min:1',
                'children' => 'required|integer',
                'infants' => 'required|integer',
                'partner_id' => 'required|integer',
                'price' => 'required|integer',
                'child_seats' => 'json',
                'flight_number' => 'string',
                'remark' => 'string',
                'luggage' => 'required',
                'confirmation_language' => 'required',
                'transfer_id' => 'required|int',
            ]
        );
        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors());
        }
    }

    public function saveReservation(): int
    {

        $this->validateModel();

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
        });

        //Cache the order
        $partnerOrder = new ReservationPartnerOrderCache($this->model->destination_id);
        $partnerOrder->cacheDestinationPartners();

        return $this->model->id;
    }


    private function saveRoundTrip()
    {
        $roundTrip = $this->model->replicate();

        $roundTrip->pickup_location = $this->model->dropoff_location;
        $roundTrip->pickup_address = $this->model->dropoff_address;
        $roundTrip->dropoff_location = $this->model->pickup_location;
        $roundTrip->dropoff_address = $this->model->pickup_address;
        $roundTrip->date = $this->returnDate;
        $roundTrip->time = $this->returnTime;
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


    public function setRoundTrip(Carbon $returnDate,Carbon $returnTime,string $flightNumber = '')
    {
        $this->roundTrip = true;
        $this->returnDate = $returnDate;
        $this->returnTime = $returnTime;
        $this->returnFlightNumber = $flightNumber;
    }

    public function addLeadTraveller(Traveller $traveller)
    {
        $this->leadTraveller = $traveller;
        return $this;
    }

    public function addOtherTraveller(Traveller $traveller, $comment)
    {
        $this->travellerComments[] = $comment;
        $this->otherTravellers->push($traveller);

        return $this;
    }



}

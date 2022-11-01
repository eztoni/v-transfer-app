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


abstract class Reservation
{


    protected \App\Models\Reservation $model;
    protected bool $roundTrip = false;
    protected Traveller $leadTraveller;
    protected Collection $otherTravellers;
    protected array $travellerComments;
    protected bool $sendMail = false;

    protected Collection $extras;

    protected $returnDate;
    protected $returnTime;
    protected $returnFlightNumber;


    public function __construct(\App\Models\Reservation $model)
    {
        $this->model = $model;
        $this->otherTravellers = collect([]);
    }


    protected function validateReservation()
    {
        $validator = Validator::make($this->model->toArray(),
            [
                'destination_id' => 'required',
                'date_time' => 'required|date',
                'pickup_location' => 'required|integer',
                'pickup_address' => 'required|string',
                'dropoff_location' => 'required|integer',
                'dropoff_address' => 'required|string',
                'adults' => 'required|integer|min:1',
                'children' => 'required|integer',
                'infants' => 'required|integer',
                'partner_id' => 'required|integer',
                'price' => 'required|integer',
                'child_seats' => 'array',
                'price_breakdown' => 'array',
                'price_breakdown.*' => 'required_array_keys:item,amount',
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







}

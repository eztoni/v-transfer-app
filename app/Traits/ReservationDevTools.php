<?php

namespace App\Traits;

trait ReservationDevTools
{


    public $devRoundTrip = false;

    public array $populateReservationModes = [
        'populate-regular',
        'populate-full'
    ];


    private function populatePickupLocations(){
        $this->stepOneFields['startingPointId']= $this->startingPoints->first()?->id;
        $this->stepOneFields['endingPointId']= $this->endingPoints->first()?->id;

        $this->stepOneFields['pickupAddress'] = $this->pickupAddressPoints->first()?->name;
        $this->stepOneFields['pickupAddressId'] = $this->pickupAddressPoints->first()?->id;
        $this->stepOneFields['dropoffAddress'] = $this->dropoffAddressPoints->first()?->name;
        $this->stepOneFields['dropoffAddressId'] = $this->dropoffAddressPoints->first()?->id;
    }

    private function populateDates()
    {
        $this->stepOneFields['dateTime'] = now()->format('d.m.Y H:i');
        $this->roundTrip= false;

        if($this->devRoundTrip){
            $this->roundTrip= true;
            $this->stepOneFields['returnDateTime'] = now()->addDays(3)->format('d.m.Y H:i');
        }
    }

    private function selectFirstTransfer(){
        if(!empty($this->availableTransfers) && $firstAvailableTransfer = $this->availableTransfers->first()){
            $this->selectTransfer($firstAvailableTransfer->transfer_id,$firstAvailableTransfer->partner_id);
        }
    }
    private function populateFlightDetails()
    {
        $this->stepTwoFields['remark'] = fake()->sentence;
        $this->stepTwoFields['arrivalFlightNumber'] = fake()->numberBetween(1000,10000);
        if ($this->roundTrip){
            $this->stepTwoFields['departureFlightNumber'] =  fake()->numberBetween(1000,10000);
        }
    }

    private function populatePasseneger()
    {
        $this->stepTwoFields['leadTraveller']=[
            'firstName' => 'John',
            'lastName' => 'Doe',
            'reservationNumber' => '21673',
            'email' => 'john.doe@ez-booker.com',
            'reservationOperaID'=>'4128763',
            'reservationOperaConfirmation'=>'CONFIRMATION',
            'phone' => '+385 91 1126213',
        ];
    }



    public function devPopulateReservation($mode){

        $this->populatePickupLocations();
        $this->populateDates();
        $this->selectFirstTransfer();
        $this->populatePasseneger();

        switch ($mode){
            case 'populate-regular':

            break;

            case 'populate-full':

            break;
        }



    }



}

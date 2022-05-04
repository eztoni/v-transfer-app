<?php

namespace App\BusinessModels\Reservation;

use App\Models\Traveller;


class Reservation
{

    private \App\Models\Reservation $model;
    private bool $roundTrip = false;


    public function __construct(\App\Models\Reservation $model)
    {
        $this->model = $model;
    }


    public function saveReservation(){

        if($this->roundTrip){
            $this->saveRoundTrip();
        }

        $this->model->save();
        $this->model->travellers()->save($this->leadTraveller);
    }


    private function saveRoundTrip(){
        $roundTrip = $this->model->replicate();

        $roundTrip->date = $this->returnDate;
        $roundTrip->time = $this->returnTime;

        $roundTrip->save();
    }


    public function roundTrip($returnDate,$returnTime)
    {
        $this->roundTrip = true;
        $this->returnDate = $returnDate;
        $this->returnTime = $returnTime;
    }

    public function addLeadTraveller(Traveller $traveller)
    {
        $this->leadTraveller = $traveller;
        return $this;
    }

    public function addOtherTraveller(Traveller $traveller)
    {
        $this->otherTravellers->push($traveller);
        return $this;
    }





}

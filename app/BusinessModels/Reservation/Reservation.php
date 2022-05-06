<?php

namespace App\BusinessModels\Reservation;

use App\Models\Traveller;
use Illuminate\Support\Collection;


class Reservation
{

    private \App\Models\Reservation $model;
    private bool $roundTrip = false;
    private Traveller $leadTraveller;
    private Collection $otherTravellers ;
    private array $travellerComments;

    public function __construct(\App\Models\Reservation $model)
    {
        $this->model = $model;
        $this->otherTravellers = collect([]);
    }


    public function saveReservation():int
    {
        $this->model->save();
        $this->model->travellers()->save($this->leadTraveller);

        foreach ($this->otherTravellers as $k => $traveller ){
            $this->model->travellers()->save($traveller,['comment'=>$this->travellerComments[$k],'lead'=>false]);
        }

        if($this->roundTrip){
            $this->saveRoundTrip();
        }
        return $this->model->id;
    }


    private function saveRoundTrip(){
        $roundTrip = $this->model->replicate();

        $roundTrip->date = $this->returnDate;
        $roundTrip->time = $this->returnTime;

        $roundTrip->save();
        $roundTrip->travellers()->save($this->leadTraveller);


        foreach ($this->otherTravellers as $k => $traveller ){
            $roundTrip->travellers()->save($traveller,['comment'=>$this->travellerComments[$k],'lead'=>false]);
        }


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

    public function addOtherTraveller(Traveller $traveller,$comment)
    {
        $this->travellerComments[] = $comment;
        $this->otherTravellers->push($traveller);

        return $this;
    }





}

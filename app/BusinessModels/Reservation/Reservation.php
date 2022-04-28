<?php

namespace App\BusinessModels\Reservation;

use App\Models\Partner;
use App\Models\Point;
use App\Models\Route;
use App\Models\Transfer;
use App\Models\Traveller;
use Carbon\Carbon;
use Carbon\Exceptions\Exception;
use Illuminate\Support\Collection;

class Reservation
{

    private \App\Models\Reservation $model;

    private Carbon $date;
    private Carbon $time;

    private Carbon $returnDate;
    private Carbon $returnTime;

    private Point $pickupLocation;
    private string $pickupAddress;

    private Point $dropoffLocation;
    private string $dropoffAddress;

    private int $adults;
    private int $children;
    private int $infants;


    private Collection $otherTravellers;
    private Traveller $leadTraveller;

    private Transfer $transfer;

    private Partner $partner;

    private  $routeObject;

    private int $price;

    private int $luggage;
    private bool $twoWay;


    public function __construct(\App\Models\Reservation $model)
    {
        $this->model = $model;
    }

    public function saveReservation(){

        $this->model->date = $this->date;
        $this->model->time = $this->time;

        $this->model->pickup_location = $this->pickupLocation->id;
        $this->model->pickup_address = $this->pickupAddress;
        $this->model->dropoff_location = $this->dropoffLocation->id;
        $this->model->dropoff_address = $this->dropoffAddress;
        $this->model->adults = $this->adults;
        $this->model->children = $this->children;
        $this->model->infants = $this->infants;
        $this->model->two_way = false;

        $this->model->luggage = $this->luggage;

        $this->model->route = json_encode((array)$this->routeObject);
        $this->model->transfer = $this->transfer->toJson();
        $this->model->partner_id = $this->partner->id;

        $this->model->price = $this->price;




        if($this->twoWay){
            $this->saveTwoWay();

        }

        $this->model->save();
        $this->model->travellers()->save($this->leadTraveller);


    }
    private function saveTwoWay(){
        $twoWay = $this->model->replicate();

        $twoWay->date = $this->returnDate;
        $twoWay->time = $this->returnTime;

        $twoWay->save();
    }
    public function twoWay($returnDate,$returnTime)
    {
        $this->twoWay = true;
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



    /**
     * @param Carbon $date
     * @return Reservation
     */
    public function setDate(Carbon $date): Reservation
    {
        $this->date = $date;
        return $this;
    }


    /**
     * @param Carbon $time
     * @return Reservation
     */
    public function setTime(Carbon $time): Reservation
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @param Point $pickupLocation
     * @return Reservation
     */
    public function setPickupLocation(Point $pickupLocation): Reservation
    {
        $this->pickupLocation = $pickupLocation;
        return $this;
    }

    /**
     * @param string $pickupAddress
     * @return Reservation
     */
    public function setPickupAddress(string $pickupAddress): Reservation
    {
        $this->pickupAddress = $pickupAddress;
        return $this;
    }

    /**
     * @param Point $dropoffLocation
     * @return Reservation
     */
    public function setDropoffLocation(Point $dropoffLocation): Reservation
    {
        $this->dropoffLocation = $dropoffLocation;
        return $this;
    }

    /**
     * @param string $dropoffAddress
     * @return Reservation
     */
    public function setDropoffAddress(string $dropoffAddress): Reservation
    {
        $this->dropoffAddress = $dropoffAddress;
        return $this;
    }

    /**
     * @param int $adults
     * @return Reservation
     */
    public function setAdults(int $adults): Reservation
    {
        $this->adults = $adults;
        return $this;
    }

    /**
     * @param int $children
     * @return Reservation
     */
    public function setChildren(int $children): Reservation
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @param int $infants
     * @return Reservation
     */
    public function setInfants(int $infants): Reservation
    {
        $this->infants = $infants;
        return $this;
    }

    /**
     * @param int $luggage
     * @return Reservation
     */
    public function setLuggage(int $luggage): Reservation
    {
        $this->luggage = $luggage;
        return $this;
    }


    /**
     * @param Reservation $model
     * @return Reservation
     */
    public function setModel(\App\Models\Reservation $model): Reservation
    {
        $this->model = $model;
        return $this;
    }

    public function setTransfer(Transfer $transfer) : Reservation
    {
        $this->transfer = $transfer;
        return $this;
    }

    /**
     * @param Partner $partner
     * @return Reservation
     */
    public function setPartner(Partner $partner): Reservation
    {
        $this->partner = $partner;
        return $this;
    }



    /**
     * @param mixed $routeObject
     * @return Reservation
     */
    public function setRouteObject($routeObject)
    {
        $this->routeObject = $routeObject;
        return $this;
    }

    /**
     * @param int $price
     * @return Reservation
     */
    public function setPrice(int $price): Reservation
    {
        $this->price = $price;
        return $this;
    }


}

<?php

namespace App\BusinessModels\Reservation;

use App\Models\Point;
use App\Models\Traveller;
use Carbon\Carbon;
use Carbon\Exceptions\Exception;
use Illuminate\Support\Collection;

class Reservation
{

    private \App\Models\Reservation $model;

    private Carbon $date;
    private Carbon $time;
    private Point $pickupLocation;
    private string $pickupAddress;

    private Point $dropoffLocation;
    private string $dropoffAddress;

    private int $adults;
    private int $children;
    private int $infants;

    private Collection $otherTravellers;
    private Traveller $leadTraveller;




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

        $this->model->save();

    }

    public function addLeadTraveller(Traveller $traveller)
    {
        $this->leadTraveller = $traveller;
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
     * @param bool $two_way
     * @return Reservation
     */
    public function setTwoWay(bool $two_way): Reservation
    {
        $this->two_way = $two_way;
        return $this;
    }
    private int $luggage;
    private bool $two_way;



    /**
     * @param Reservation $model
     * @return Reservation
     */
    public function setModel(Reservation $model): Reservation
    {
        $this->model = $model;
        return $this;
    }


}

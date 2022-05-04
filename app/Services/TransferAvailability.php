<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\Route;
use App\Models\Transfer;
use Carbon\Carbon;

class TransferAvailability
{

    private Carbon $dateTo;
    private Carbon $timeTo;
    private Carbon $dateFrom;
    private Carbon $timeFrom;
    private bool $roundTrip = false;

    private Route $route;

    private int $luggage = 0;
    private int $seniors = 0;
    private int $adults = 0;
    private int $children = 0;
    private int $infants = 0;


    public function __construct(int $adults, Route $route, int $children =0 , int $infants=0,  int $luggage = 0)
    {
        $this->route = $route;
        $this->luggage = $luggage;
        $this->adults = $adults;
        $this->children = $children;
        $this->infants = $infants;
    }


    private function getTotalNumOfPeople(): int
    {
        return $this->seniors + $this->adults + $this->children + $this->infants;
    }

    public function getAvailablePartnerTransfers()
    {
        if (empty($this->getTotalNumOfPeople())) {
            return collect([]);
        }
        if (!$this->route) {
            return collect([]);
        }

        $routeWithTransfersPerPartner =
            Route::whereId($this->route->id)->with(['transfers', 'transfers.vehicle'])
                ->whereHas('transfers.vehicle', function ($q) {
                    $q->where('max_luggage', '>=', $this->luggage)
                        ->where('max_occ', '>=', $this->getTotalNumOfPeople());
                })
                ->first();


        if ($routeWithTransfersPerPartner) {
            $partnerIds = array_unique($routeWithTransfersPerPartner->transfers->pluck('pivot.partner_id')->toArray());

            $partners = Partner::findMany($partnerIds);

            $routeWithTransfersPerPartner->transfers = $routeWithTransfersPerPartner->transfers->map(function ($item) use ($partners) {
                $item->partner = $partners->where('id', $item->pivot->partner_id)->first();
                return $item;
            });

            $routeWithTransfersPerPartner->transfers = $routeWithTransfersPerPartner->transfers->sortBy('pivot.partner_id');

        }



        return $routeWithTransfersPerPartner->transfers ?? collect([]);
    }


    /**
     * @param int $luggage
     * @return TransferAvailability
     */
    public function setLuggage(int $luggage): TransferAvailability
    {
        $this->luggage = $luggage;
        return $this;
    }

    /**
     * @param bool $roundTrip
     * @return TransferAvailability
     */
    public function setRoundTrip(bool $roundTrip): TransferAvailability
    {
        $this->roundTrip = $roundTrip;
        return $this;
    }

    /**
     * @param int $infants
     * @return TransferAvailability
     */
    public function setInfants(int $infants): TransferAvailability
    {
        $this->infants = $infants;
        return $this;
    }

    /**
     * @param int $children
     * @return TransferAvailability
     */
    public function setChildren(int $children): TransferAvailability
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @param int $adults
     * @return TransferAvailability
     */
    public function setAdults(int $adults): TransferAvailability
    {
        $this->adults = $adults;
        return $this;
    }

    /**
     * @param int $seniors
     * @return TransferAvailability
     */
    public function setSeniors(int $seniors): TransferAvailability
    {
        $this->seniors = $seniors;
        return $this;
    }

    /**
     * @param Carbon $dateTo
     * @return TransferAvailability
     */
    public function setDateTo(Carbon $dateTo): TransferAvailability
    {
        $this->dateTo = $dateTo;
        return $this;
    }

    /**
     * @param Carbon $timeTo
     * @return TransferAvailability
     */
    public function setTimeTo(Carbon $timeTo): TransferAvailability
    {
        $this->timeTo = $timeTo;
        return $this;
    }

    /**
     * @param Carbon $dateFrom
     * @return TransferAvailability
     */
    public function setDateFrom(Carbon $dateFrom): TransferAvailability
    {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    /**
     * @param Carbon $timeFrom
     * @return TransferAvailability
     */
    public function setTimeFrom(Carbon $timeFrom): TransferAvailability
    {
        $this->timeFrom = $timeFrom;
        return $this;
    }


    /**
     * @param Route $route
     * @return TransferAvailability
     */
    public function setRoute(Route $route): TransferAvailability
    {
        $this->route = $route;
        return $this;
    }


}

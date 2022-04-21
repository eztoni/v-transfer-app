<?php

namespace App\Services;

use App\Models\Transfer;
use Carbon\Carbon;

class TransferAvailability
{

    private Carbon $dateTo;
    private Carbon $timeTo;
    private Carbon $dateFrom;
    private Carbon $timeFrom;
    private bool $twoWay = false;

    private  $destinationId;

    private int $luggage = 0;
    private int $seniors = 0;
    private int $adults = 0;
    private int $children = 0;
    private int $infants = 0;

    private function getTotalNumOfPeople() : int
    {
        return $this->seniors + $this->adults + $this->children + $this->infants;
    }

    public function getAvailableTransfers()
    {
        if(empty($this->getTotalNumOfPeople())){
            return collect([]);
        }


        return Transfer::query()
            ->with(['vehicle'])
            ->whereHas('vehicle',function ($q){
                $q->where('max_luggage','>=',$this->luggage)
                    ->where('max_occ','>=',$this->getTotalNumOfPeople());
            })
            ->where('destination_id',$this->destinationId)
            ->get();
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
     * @param bool $twoWay
     * @return TransferAvailability
     */
    public function setTwoWay(bool $twoWay): TransferAvailability
    {
        $this->twoWay = $twoWay;
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
     * @param int $destinationId
     * @return TransferAvailability
     */
    public function setDestinationId( $destinationId): TransferAvailability
    {
        $this->destinationId = $destinationId;
        return $this;
    }


}

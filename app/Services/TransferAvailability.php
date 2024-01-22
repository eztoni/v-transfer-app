<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\Route;
use App\Models\Transfer;
use App\Pivots\TransferPricePivot;
use App\Services\Helpers\ReservationPartnerOrderCache;
use Carbon\Carbon;

class TransferAvailability
{

    public function __construct(
        private Route $route,
        private int $adults,
        private int $children,
        private int $infants,
        private int $luggage,
        private bool $roundTrip = false
    )
    {
    }


    private function getTotalNumOfPeople(): int
    {
        return  (int)($this->adults + $this->children + $this->infants);
    }

    public function getAvailablePartnerTransfers()
    {
        if (empty($this->getTotalNumOfPeople())) {
            return collect([]);
        }
        if (!$this->route) {
            return collect([]);
        }

        $partnerOrder = new ReservationPartnerOrderCache($this->route->destination_id);
        $order = $partnerOrder->getPartnerOrder();
        $availableTransfers = TransferPricePivot::query()
            ->where('route_id', $this->route->id)
            ->when($this->roundTrip, function ($q) {
                $q->where('round_trip', $this->roundTrip);
            })
            ->with(['transfer', 'partner','transfer.media'])
            ->whereHas('partner')
            ->whereHas('transfer.vehicle', function ($q) {
                $q->where('max_luggage', '>=', $this->luggage)
                    ->where('max_occ', '>=', $this->getTotalNumOfPeople())
                    ->where('destination_id','!=',4);
                    
            })
            ->get()
            ->sortByDesc(function ($item) use ($order) {

                //Rabac Fix
                if($item->transfer->destination_id != 4){
                    return in_array($item->partner_id, $order) ? array_search($item->partner_id, $order) : 10   * (int) $item?->partner_id;
                }


            });

        return $availableTransfers ?? collect([]);
    }





}

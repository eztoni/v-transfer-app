<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\Route;
use App\Models\Transfer;
use App\Pivots\RouteTransfer;
use App\Services\Helpers\ReservationPartnerOrderCache;
use Carbon\Carbon;

class TransferAvailability
{


    private Route $route;

    private int $luggage;
    private int $adults;
    private int $children;
    private int $infants;


    public function __construct(int $adults, Route $route, int $children = 0, int $infants = 0, int $luggage = 0)
    {
        $this->route = $route;
        $this->luggage = $luggage;
        $this->adults = $adults;
        $this->children = $children;
        $this->infants = $infants;
    }


    private function getTotalNumOfPeople(): int
    {
        return  $this->adults + $this->children + $this->infants;
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

        $availableTransfers = RouteTransfer::query()
            ->where('route_id', $this->route->id)
            ->with(['transfer', 'partner'])
            ->whereHas('transfer.vehicle', function ($q) {
                $q->where('max_luggage', '>=', $this->luggage)
                    ->where('max_occ', '>=', $this->getTotalNumOfPeople());
            })
            ->get()
            ->sortByDesc(function ($item) use ($order) {
                return in_array($item->partner_id, $order) ? array_search($item->partner_id, $order) : 10   * (int) $item?->partner_id;
            });

        return $availableTransfers ?? collect([]);
    }





}

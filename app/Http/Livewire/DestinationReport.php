<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Partner;
use App\Models\Reservation;
use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exchange\SwapExchange;
use Money\Money;
use Swap\Laravel\Facades\Swap;

class DestinationReport extends Component
{
    public int $destination;

    public $dateFrom;
    public $dateTo;
    public $partner = 0;
    public $status = 0;

    public $pickupLocation = 0;
    public $dropoffLocation = 0;

    public $totalEur;
    public $totalHRK;



    protected $rules = [
        'destination' => 'required',
        'dateFrom' => 'required|date',
        'dateTo' => 'required|date|after_or_equal:dateFrom',
        'partner' => 'required',
    ];

    public array $filteredReservations;


    public function mount()
    {
        $this->destination = \Auth::user()->destination_id;
        $this->dateFrom = Carbon::now()->format('d.m.Y');
        $this->dateTo = Carbon::now()->format('d.m.Y');
        $this->filteredReservations = [];

    }

    public function getStatusesProperty()
    {
        $return = [0 => 'Select status'];
        foreach (Reservation::STATUS_ARRAY as $s) {
            $return[$s] = Str::ucfirst($s);
        }
        return $return;
    }

    public function getPickupLocationsProperty()
    {
        return Route::query()
            ->where('destination_id', $this->destination)
            ->with('startingPoint')
            ->get()
            ->pluck('startingPoint')
            ->unique()
            ->mapWithKeys(function ($i) {
                return [$i->id => $i->name];
            })
            ->prepend('All pickup destinations', 0)
            ->toArray();
    }

    public function getdropoffLocationsProperty()
    {

        return Route::query()
            ->with('endingPoint')
            ->where('destination_id', $this->destination)
            ->where('starting_point_id', $this->pickupLocation)
            ->get()
            ->pluck('endingPoint')
            ->unique()
            ->mapWithKeys(function ($i) {
                return [$i->id => $i->name];
            })
            ->prepend('All dropoff destinations', 0)
            ->toArray();
    }


    public function generate(\Swap\Swap $swap)
    {
        $this->totalEur = Money::EUR(0);
        $this->totalHRK = Money::HRK(0);

        $exchange = new SwapExchange($swap);

        $converter = new Converter(new ISOCurrencies(), $exchange);

        $this->filteredReservations =
            Reservation::query()
                ->whereIsMain(true)
                ->with(['leadTraveller', 'pickupLocation', 'dropoffLocation'])
                ->where('destination_id', $this->destination)
                ->whereDate('created_at', '>=', Carbon::createFromFormat('d.m.Y', $this->dateFrom))
                ->whereDate('created_at', '<=', Carbon::createFromFormat('d.m.Y', $this->dateTo))
                ->when($this->partner != 0, function ($q) {
                    $q->where('partner_id', $this->partner);
                })
                ->when($this->pickupLocation != 0, function ($q) {
                    $q->where('pickup_location', $this->pickupLocation);
                })
                ->when($this->dropoffLocation != 0, function ($q) {
                    $q->where('dropoff_location', $this->dropoffLocation);
                })
                ->when($this->status != 0, function ($q) {
                    $q->where('status', $this->status);
                })
                ->get()
                ->map(function ($i) use ($converter) {

                    $priceEur = \Cknow\Money\Money::EUR($i->price);
                    $priceHRK = \Cknow\Money\Money::
                    fromMoney($converter
                        ->convert($priceEur->getMoney(), new Currency('HRK')));

                    $this->totalEur = $this->totalEur->add($priceEur->getMoney());
                    $this->totalHRK = $this->totalHRK->add($priceHRK->getMoney());

                    return [
                        'id' => $i->id,
                        'name' => $i->leadTraveller?->first()->full_name,
                        'date' => $i->date?->format('d.m.Y'),
                        'partner' => $i->partner->name,
                        'adults' => $i->adults,
                        'children' => $i->children,
                        'infants' => $i->infants,
                        'transfer' => $i->transfer?->name,
                        'vehicle' => $i->transfer?->vehicle?->name,
                        'status' => $i->status,
                        'price_eur' => (string)$priceEur,
                        'price_hrk' => (string)$priceHRK,
                    ];
                })->toArray();


        $this->totalHRK = (string) \Cknow\Money\Money::fromMoney($this->totalHRK);
        $this->totalEur = (string)  \Cknow\Money\Money::fromMoney($this->totalEur);

    }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function getPartnersProperty()
    {

        return Partner::whereHas('destinations', function ($q) {
            $q->where('id', $this->destination);
        })->get()->mapWithKeys(function ($i) {
            return [$i->id => $i->name];
        })->prepend('All partners', 0)
            ->toArray();
    }

    public function getAdminDestinationsProperty()
    {
        return Destination::all()->mapWithKeys(function ($i) {
            return [$i->id => $i->name];
        })->toArray();
    }


    public function render()
    {
        return view('livewire.destination-report');
    }
}

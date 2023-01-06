<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Partner;
use App\Models\Reservation;
use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Livewire\Component;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exchange\SwapExchange;
use Money\Money;
use Swap\Laravel\Facades\Swap;
use WireUi\Traits\Actions;

class DestinationReport extends Component
{
    use Actions;

    public int $destination;

    public $dateFrom;
    public $dateTo;
    public $partner = 0;
    public $status = 'All';

    public $pickupLocation = 0;
    public $dropoffLocation = 0;

    public $totalEur;
    public $totalCommission;

    public bool $isPartnerReporting = false;


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


        $this->dateFrom = Carbon::now()->startOfMonth()->format('d.m.Y');
        $this->dateTo = Carbon::now()->endOfMonth()->format('d.m.Y');
        $this->filteredReservations = [];




        $this->isPartnerReporting = request()?->routeIs('partner-reports');

        if($this->isPartnerReporting){
            $p = Partner::first();
            if($p){
                $this->partner = $p->id;
            }
        }
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
        $this->totalCommission = \Cknow\Money\Money::EUR(0);
        $exchange = new SwapExchange($swap);

        $converter = new Converter(new ISOCurrencies(), $exchange);

        $this->filteredReservations =
            Reservation::query()
                ->whereIsMain(true)
                ->with(['leadTraveller', 'pickupLocation', 'dropoffLocation', 'returnReservation'])
                ->when($this->destination != 'All', function ($q) {
                    $q->where('destination_id', $this->destination);
                })
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
                ->when($this->status != 'All', function ($q) {
                    $q->where('status', $this->status);
                })
                ->get()
                ->map(function (Reservation $i) use ($converter) {

                    $priceEur = \Cknow\Money\Money::EUR($i->price);

                    if ($i->isConfirmed()) {
                        $this->totalEur = $this->totalEur->add($priceEur->getMoney());
                        $this->totalCommission = $this->totalCommission->add($i->total_commission_amount);
                    }


                    return [
                        'id' => $i->id,
                        'name' => $i->leadTraveller?->first()->full_name,
                        'date_time' => $i->date_time?->format('d.m.Y @ H:i'),
                        'partner' => $i->partner->name,
                        'adults' => $i->adults,
                        'children' => $i->children,
                        'infants' => $i->infants,
                        'transfer' => $i->transfer?->name,
                        'vehicle' => $i->transfer?->vehicle?->type,
                        'status' => $i->status,
                        'price_eur' => (string)$priceEur,
                        'round_trip' => $i->is_round_trip,
                        'round_trip_date' => $i->returnReservation?->date_time?->format('d.m.Y @ H:i'),
                        'tax_level'=>  \Arr::get($i->transfer_price_state,'price_data.tax_level'),
                        'commission'=>  \Arr::get($i->transfer_price_state,'price_data.commission'),
                        'commission_amount'=>  $i->total_commission_amount,
                    ];
                })->toArray();


        $this->totalEur = (string)\Cknow\Money\Money::fromMoney($this->totalEur);
        $this->totalCommission = (string)$this->totalCommission;

    }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function getPartnersProperty()
    {
        $partners = Partner::query();

        $partners = $partners->get()->mapWithKeys(function ($i) {
            return [$i->id => $i->name];
        });

        if (!$this->isPartnerReporting) {
            $partners->prepend('All partners', 0);
        }

        return $partners->toArray();
    }

    public function getAdminDestinationsProperty()
    {
        $destinations = Destination::all()->mapWithKeys(function ($i) {
            return [$i->id => $i->name];
        });

        if ($this->isPartnerReporting) {
            $destinations->prepend('All partners', 0);
        }

        return $destinations;
    }


    public function render()
    {
        return view('livewire.destination-report');
    }
}

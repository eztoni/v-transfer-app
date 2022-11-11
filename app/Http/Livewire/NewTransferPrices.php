<?php

namespace App\Http\Livewire;

use App\Facades\EzMoney;
use App\Models\Partner;
use App\Models\Route;
use App\Models\Transfer;
use App\Pivots\TransferPricePivot;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;
use WireUi\Traits\Actions;


class NewTransferPrices extends Component
{

    use Actions;

    public Transfer $transfer;
    public $showSearch = true;
    public Partner $partner;
    public $modelPrices;

    public int $transferId;
    public int $partnerId;

    public $routesdva = [];

    public $priceCalculations = [

    ];
    public $roundTripCalculations = [

    ];

    public function mount()
    {
        $this->partner = Partner::first();
        $this->partnerId = $this->partner->id;
        $this->setModelPrices();
    }

    public function updatedPartnerId()
    {
        $this->partner = Partner::find($this->transferId);

        $this->setModelPrices();
    }

    public function updatedTransferId()
    {
        $this->transfer = Transfer::find($this->transferId);

        $this->setModelPrices();

    }


    private function setModelPrices(): void
    {

        if ($this->transfer && $this->partner) {

            $this->modelPrices = TransferPricePivot::query()
                ->where('transfer_id', $this->transfer->id)
                ->where('partner_id', $this->partner->id)
                ->get()
                ->keyBy('route_id')
                ->toArray();
            /** @var Route $route */
            foreach ($this->routes as $route) {
                if (!\Arr::has($this->modelPrices, $route->id)) {
                    $this->modelPrices = \Arr::add($this->modelPrices, $route->id, TransferPricePivot::make()->toArray());

                }
            }

            foreach ($this->modelPrices as $k => $price) {
                $this->formatPrice($k);
            }

        }
    }

    public function formatPrice($k)
    {
        $this->formatValue($k,'price',fn($i)=>EzMoney::format($i));
        $this->formatValue($k,'price_round_trip',fn($i)=>EzMoney::format($i));
        $this->formatValue($k,'price_with_discount',fn($i)=>EzMoney::format($i));
        $this->formatValue($k,'round_trip_price_with_discount',fn($i)=>EzMoney::format($i));
        $this->formatValue($k,'price_with_commission',fn($i)=>EzMoney::format($i));
        $this->formatValue($k,'round_trip_price_with_commission',fn($i)=>EzMoney::format($i));
        $this->formatValue($k,'date_from',fn($i)=>Carbon::make($i)?->format('d.m.Y'));
        $this->formatValue($k,'date_to',fn($i)=>Carbon::make($i)?->format('d.m.Y'));
    }

    private function formatValue($i,$k,\Closure $fun){
        if ($currentValue = \Arr::get($this->modelPrices[$i], $k)) {
            \Arr::set($this->modelPrices[$i], $k, $fun($currentValue));
        }
    }



    public function updated($property)
    {
        if (Str::contains($property, [
            'price',
            'price_round_trip',
        ])) {
            $routeId = explode('.', $property)[1];

            $priceModel = TransferPricePivot::make($this->modelPrices[$routeId])->toArray();
            $this->modelPrices[$routeId] = $priceModel;
            $this->formatPrice($routeId);
        }
    }

    public function getRoutesProperty()
    {
        return Route::with('startingPoint', 'endingPoint')->get();
    }

    public function save($routeId)
    {
        if (!$priceArray = $this->modelPrices[$routeId]) {
            return;
        }

        $priceArray['price'] = EzMoney::parseForDb($priceArray['price']);
        if ($priceArray['price_round_trip'] ?? false) {
            $priceArray['price_round_trip'] = EzMoney::parseForDb($priceArray['price_round_trip']);
        }


        $priceArray['date_from'] = Carbon::createFromFormat('d.m.Y',$priceArray['date_from'])->format('Y-m-d');
        $priceArray['date_to'] = Carbon::createFromFormat('d.m.Y',$priceArray['date_to'])->format('Y-m-d');


        \DB::table('route_transfer')->updateOrInsert(
            [
                'route_id' => $priceArray['route_id'],
                'transfer_id' => $priceArray['transfer_id'],
                'partner_id' => $priceArray['partner_id'],
            ],
            Arr::only($priceArray, [
                'price',
                'price_round_trip',
                'round_trip',
                'date_from',
                'date_to',
                'tax_level',
                'calculation_type',
                'commission',
                'discount',
            ])
        );

        $this->notification()->success('Saved', 'Route Price Saved');

    }

    public function render()
    {
        return view('livewire.new-transfer-prices');
    }
}

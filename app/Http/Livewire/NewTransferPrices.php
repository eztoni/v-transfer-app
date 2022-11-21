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

    public ?Transfer $transfer = null;
    public $showSearch = true;
    public ?Partner $partner= null;
    public $modelPrices;

    public int $transferId;
    public int $partnerId;


    public function fieldNames($rId){ return
        [
        "modelPrices.$rId.price" => 'price',
        "modelPrices.$rId.price_round_trip" => 'price round trip',
        "modelPrices.$rId.round_trip" => 'round trip',
        "modelPrices.$rId.date_from" => 'date from',
        "modelPrices.$rId.date_to" => 'date to',
        "modelPrices.$rId.tax_level" => 'tax level',
        "modelPrices.$rId.calculation_type" => 'calculation type',
        "modelPrices.$rId.commission" => 'commission',
        "modelPrices.$rId.discount" => 'discount'
    ];}

    public function ruless($rId){
        return [
            "modelPrices.$rId.price" => 'required|min:1|regex:'. \App\Services\Helpers\EzMoney::MONEY_REGEX,
            "modelPrices.$rId.price_round_trip" => 'min:1|regex:'. \App\Services\Helpers\EzMoney::MONEY_REGEX,
            "modelPrices.$rId.round_trip" => 'boolean|nullable',
            "modelPrices.$rId.date_from" => "required|date_format:d.m.Y|before_or_equal:modelPrices.$rId.date_to",
            "modelPrices.$rId.date_to" => "required|date_format:d.m.Y|after_or_equal:modelPrices.$rId.date_from",
            "modelPrices.$rId.tax_level" => 'required',
            "modelPrices.$rId.calculation_type" => 'required',
            "modelPrices.$rId.commission" => 'required|integer|min:0|max:100',
            "modelPrices.$rId.discount" => 'required|integer|min:0|max:100',
        ];
    }


    public function mount()
    {
        $this->partner = Partner::first();
        if (!$this->transfer){
            $this->transfer = Transfer::first();
        }
        $this->transferId = $this->transfer->id;
        $this->partnerId = $this->partner->id;
        $this->setModelPrices();
    }

    public function updatedPartnerId()
    {
        $this->partner = Partner::find($this->partnerId);
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

                    $newPrice = TransferPricePivot::make()->toArray();
                    $newPrice['new_price']=true;

                    $this->modelPrices = \Arr::add(
                        $this->modelPrices,
                        $route->id,
                        $newPrice
                        );

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
            $this->validateOnly($property,$this->ruless($routeId), [], $this->fieldNames($routeId));

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

        $this->validate($this->ruless($routeId), [], $this->fieldNames($routeId));

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

        Arr::set($this->modelPrices[$routeId],'new_price',false);


        $this->notification()->success('Saved', 'Route Price Saved');

    }

    public function getTransfersProperty()
    {
        return Transfer::all();
    }

    public function render()
    {
        return view('livewire.new-transfer-prices');
    }
}

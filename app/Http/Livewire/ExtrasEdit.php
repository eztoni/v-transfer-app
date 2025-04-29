<?php

namespace App\Http\Livewire;

use App\Actions\ExtraPrice\GetExtraCommission;
use App\Actions\ExtraPrice\GetExtraDiscount;
use App\Actions\TransferPrice\GetRouteCommission;
use App\Models\Extra;
use App\Models\Language;
use App\Models\Partner;
use App\Pivots\ExtraPartner;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Facades\EzMoney;
use WireUi\Traits\Actions;

class ExtrasEdit extends Component
{
    use Actions;

  //  public $extra;
   // public $extraId;
    public $companyLanguages = ['en'];
    public $extraDateFrom;
    public $extraDateTo;
    public $extraTaxLevel;
    public $extraCalculationType;
    public $extraDiscountPercentage = 0;
    public $extraCommissionPercentage = 0;
    public $extraPriceWithDiscount;
    public $extraPriceCommission;
    public $extraOperaPackageId;
    public $extraPrice;
    public $hidden = 0;

    public int $extraId;
    public int $partnerId;

    public ?Extra $extra = null;
    public ?Partner $partner= null;

    public $modelPrices;

    public $extraName = [
        'en' => null
    ];
    public $extraDescription = [
        'en' => null
    ];

    /*protected $casts = [
        'extraPrice' => MoneyIntegerCast::class. ':EUR,true',
    ];*/

    public $fieldNames = [
        'extraName.*' => 'extra name',
        'extraDescription.*' => 'extra description'
    ];

    protected function rules()
    {
        $ruleArray = [
            'extraName.en' => 'required|min:3',
        ];
        foreach ($this->companyLanguages as $lang) {
            if ($lang !== 'en') {
                $ruleArray['extraName.' . $lang] = 'nullable|min:3';
            }
        }
        return $ruleArray;
    }

    public function extraPriceFieldNames($pId){ return
        [
            "modelPrices.$pId.commission" => 'extra commission percentage',
            "modelPrices.$pId.discount" => 'extra discount percentage',
            "modelPrices.$pId.price"  => 'price',
            "modelPrices.$pId.tax_level" => 'extra tax level',
            "modelPrices.$pId.calculation_type" => 'extra calculation type',
            "modelPrices.$pId.date_from" => 'date from',
            "modelPrices.$pId.date_to" => 'date to',
            "modelPrices.$pId.package_id" => 'package id',
        ];
    }

    protected function extraPriceRules($pId)
    {
        return [
            "modelPrices.$pId.commission" =>  'required|min:0|max:100|integer',
            "modelPrices.$pId.discount" => 'required|min:0|max:100|integer',
            "modelPrices.$pId.price" => 'required|min:1|regex:'. \App\Services\Helpers\EzMoney::MONEY_REGEX,
            "modelPrices.$pId.tax_level" => 'required',
            "modelPrices.$pId.calculation_type" => 'required',
            "modelPrices.$pId.date_from" => "required|date_format:d.m.Y|before_or_equal:modelPrices.$pId.date_to",
            "modelPrices.$pId.date_to" => "required|date_format:d.m.Y|after_or_equal:modelPrices.$pId.date_from",
        ];

    }

    public function instantiateComponentValues()
    {
        $this->companyLanguages = Language::all()->pluck('language_code')->toArray();
        foreach ($this->companyLanguages as $lang) {
            $this->extraName[$lang] = $this->extra->getTranslation('name', $lang, false);
            $this->extraDescription[$lang] = $this->extra->getTranslation('description', $lang, false);
        }
    }


    public function mount()
    {

        $this->partner = Partner::first();
        if($this->extraId){
            $this->extra = Extra::find($this->extraId);
        }

        $this->hidden = $this->extra->hidden;

        $this->partnerId = $this->partner->id;
        $this->setModelPrices();
        $this->instantiateComponentValues();
    }

    private function setModelPrices(){


        if ($this->extra && $this->partner) {


            $this->modelPrices = ExtraPartner::query()
                ->where('extra_id', $this->extra->id)
                ->where('partner_id', $this->partner->id)
                ->get()
                ->keyBy('partner_id')
                ->toArray();

            if (!\Arr::has($this->modelPrices, $this->partnerId)) {

                $newPrice = ExtraPartner::make()->toArray();
                $newPrice['new_price']=true;

                $this->modelPrices = \Arr::add(
                    $this->modelPrices,
                    $this->partnerId,
                    $newPrice
                );

            }

            foreach ($this->modelPrices as $k => $price) {
                $this->formatPrice($k);
            }

        }

    }

    public function formatPrice($k)
    {
        $this->formatValue($k,'price',fn($i)=> EzMoney::format($i));
        $this->formatValue($k,'price_with_discount',fn($i)=>EzMoney::format($i));
        $this->formatValue($k,'price_with_commission',fn($i)=>EzMoney::format($i));
        $this->formatValue($k,'date_from',fn($i)=>Carbon::make($i)?->format('d.m.Y'));
        $this->formatValue($k,'date_to',fn($i)=>Carbon::make($i)?->format('d.m.Y'));
    }

    private function formatValue($i,$k,\Closure $fun){
        if ($currentValue = \Arr::get($this->modelPrices[$i], $k)) {
            \Arr::set($this->modelPrices[$i], $k, $fun($currentValue));
        }
    }

    public function updatedPartnerId(): void
    {
        $this->partner = Partner::find($this->partnerId);
        $this->setModelPrices();
    }

    public function updatedExtraId(){



        $this->partnerId = Partner::first()?->id;
        $this->setModelPrices();
        $this->instantiateComponentValues();
    }

    public function getAllExtrasForSelectProperty()
    {
        return Extra::all()->transform(function (Extra $item){
           return ['id'=>(string) $item->id,
                   'name'=>$item->name];
        })->toArray();
    }

    public function updated($property)
    {

        if (Str::contains($property, [
            'price',
            'commission',
            'discount',
        ])) {
            $routeId = explode('.', $property)[1];
            $this->validateOnly($property,$this->extraPriceRules($this->partnerId), [], $this->extraPriceFieldNames($this->partnerId));

            $priceModel = ExtraPartner::make($this->modelPrices[$routeId])->toArray();
            $this->modelPrices[$routeId] = $priceModel;
            $this->formatPrice($routeId);
        }
    }

    public function updateExtraPrice(){
        $this->extraPriceWithDiscount = \App\Facades\EzMoney::format(GetExtraDiscount::run($this->extra,$this->partnerId,$this->extraDiscountPercentage,$this->extraPrice));
        $this->extraPriceCommission = \App\Facades\EzMoney::format(GetExtraCommission::run($this->extra,$this->partnerId,$this->extraCommissionPercentage,$this->extraPrice));
    }

    public function updatedExtraName()
    {
        $this->extra->setTranslations('name', $this->extraName);
        $this->extra->setTranslations('description', $this->extraDescription);
    }

    public function save(){

        if (!$priceArray = $this->modelPrices[$this->partnerId]) {
            return;
        }

        $this->validate($this->extraPriceRules($this->partnerId), [], $this->extraPriceFieldNames($this->partnerId));

        $priceArray['price'] = EzMoney::parseForDb($priceArray['price']);
        $priceArray['date_from'] = Carbon::createFromFormat('d.m.Y',$priceArray['date_from'])->format('Y-m-d');
        $priceArray['date_to'] = Carbon::createFromFormat('d.m.Y',$priceArray['date_to'])->format('Y-m-d');


        \DB::table('extra_partner')->updateOrInsert(
            [
                'extra_id'=>$this->extra->id,
                'partner_id'=>$this->partnerId,
            ],
            Arr::only($priceArray, [
                'price',
                'date_from',
                'date_to',
                'tax_level',
                'calculation_type',
                'commission',
                'discount',
                'package_id'
            ])
        );

        Arr::set($this->modelPrices[$this->partnerId],'new_price',false);

        $this->notification()->success('Saved', 'Extra Price Saved');

    }

    public function saveExtra()
    {

        $this->validate($this->rules(), [], $this->fieldNames);

        $this->extra->setTranslations('name', $this->extraName);
        $this->extra->setTranslations('description', $this->extraDescription);
        $this->extra->hidden = $this->hidden ? 1 : 0;
        $this->extra->save();

        $this->notification()->success('Update successful');
    }

    public function render()
    {
        return view('livewire.extras-edit');
    }
}

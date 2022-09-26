<?php

namespace App\Http\Livewire;

use App\Actions\ExtraPrice\GetExtraCommission;
use App\Actions\ExtraPrice\GetExtraDiscount;
use App\Actions\TransferPrice\GetRouteCommission;
use App\Models\Extra;
use App\Models\Language;
use App\Models\Partner;
use App\Models\Transfer;
use App\Services\Helpers\EzMoney;
use Carbon\Carbon;
use Cknow\Money\Casts\MoneyIntegerCast;
use Cknow\Money\Money;
use Livewire\Component;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Parser\DecimalMoneyParser;
use WireUi\Traits\Actions;

class ExtrasEdit extends Component
{
use Actions;
    public Extra $extra;
    public $extraId = null;
    public $companyLanguages = ['en'];
    public $extraDateFrom;
    public $extraDateTo;
    public $extraTaxLevel;
    public $extraCalculationType;
    public $extraDiscountPercentage;
    public $extraCommissionPercentage;
    public $extraPriceWithDiscount;
    public $extraPriceCommission;
    public $extraPrice;

    public $partnerId = 0;
    public $extraName = [
        'en' => null
    ];
    public $extraDescription = [
        'en' => null
    ];

    protected $casts = [
        'extraPrice' => MoneyIntegerCast::class. ':EUR,true',
    ];
    public $fieldNames = [
        'routeCommissionPercentage.*' => 'commission Percentage',
        'routePrice.*' => 'price',
        'routePriceRoundTrip.*' => 'round trip price',
        'routeTaxLevel.*' => 'route tax level',
        'routeCalculationType.*' => 'route calculation type',
        'routeDateFrom.*' => 'date from',
        'routeDateTo.*' => 'date to'
    ];

    protected function rules()
    {
        $ruleArray = [
            'extraName.en' => 'required|min:3',
            'extraCommissionPercentage' => 'required|min:0|max:100|integer',
            'extraDiscountPercentage' => 'required|min:0|max:100|integer',
            'extraPrice' => 'required|min:1|regex:'. \App\Services\Helpers\EzMoney::MONEY_REGEX,
            'extraTaxLevel' => 'required',
            'extraCalculationType' => 'required',
            'extraDateFrom' => 'required|date',
            'extraDateTo' => 'required|date|after_or_equal:extraDateFrom',
        ];
        foreach ($this->companyLanguages as $lang) {
            if ($lang !== 'en') {
                $ruleArray['extraName.' . $lang] = 'nullable|min:3';
            }
        }
        return $ruleArray;
    }

    public function mount()
    {
        $this->instantiateComponentValues();
        $this->partnerId = Partner::first()?->id;

        $this->setModelPrices();
    }

    public function updatedPartnerId()
    {
        $this->extraPrice =  null;
        $this->setModelPrices();
    }


    public function updateExtraPrice(){

        $this->extraPriceWithDiscount = \App\Facades\EzMoney::format(GetExtraDiscount::run($this->extra,$this->partnerId,$this->extraDiscountPercentage,$this->extraPrice));
        $this->extraPriceCommission = \App\Facades\EzMoney::format(GetExtraCommission::run($this->extra,$this->partnerId,$this->extraCommissionPercentage,$this->extraPrice));
    }

    public function updateDiscountPercentage(){
        $this->extraPriceWithDiscount = \App\Facades\EzMoney::format(GetExtraDiscount::run($this->extra,$this->partnerId,$this->extraDiscountPercentage,$this->extraPrice));
        $this->extraPriceCommission = \App\Facades\EzMoney::format(GetExtraCommission::run($this->extra,$this->partnerId,$this->extraCommissionPercentage,$this->extraPrice));
    }

    public function updateCommissionPercentage(){
        $this->extraPriceWithDiscount = \App\Facades\EzMoney::format(GetExtraDiscount::run($this->extra,$this->partnerId,$this->extraDiscountPercentage,$this->extraPrice));
        $this->extraPriceCommission = \App\Facades\EzMoney::format(GetExtraCommission::run($this->extra,$this->partnerId,$this->extraCommissionPercentage,$this->extraPrice));
    }



    public function instantiateComponentValues()
    {
        $this->companyLanguages = Language::all()->pluck('language_code')->toArray();
        $this->extraId = $this->extra->id;
        foreach ($this->companyLanguages as $lang) {
            $this->extraName[$lang] = $this->extra->getTranslation('name', $lang, false);
            $this->extraDescription[$lang] = $this->extra->getTranslation('description', $lang, false);
        }

    }

    public function updatedExtraName()
    {
        $this->extra->setTranslations('name', $this->extraName);
        $this->extra->setTranslations('description', $this->extraDescription);
    }

    public function updated($field)
    {
        $this->validateOnly($field);
    }

    private function setModelPrices(){

        if($this->extraId > 0){
            $this->extraPrice = \EzMoney::format($this->extra->getPrice($this->partnerId)); // 1,99;
        }

        if ($pivot_partner =   $this->extra->partner->where('id', $this->partnerId)->first()?->pivot){

            $this->extraCalculationType = $pivot_partner->calculation_type;
            $this->extraTaxLevel = $pivot_partner->tax_level;
            $this->extraDateFrom = Carbon::make($pivot_partner->date_from)?->format('d.m.Y') ?? '';
            $this->extraDateTo = Carbon::make($pivot_partner->date_to)?->format('d.m.Y') ?? '';
            $this->extraCommissionPercentage = $pivot_partner->commission;
            $this->extraDiscountPercentage = $pivot_partner->discount;
            $this->extraPriceWithDiscount = \App\Facades\EzMoney::format(GetExtraDiscount::run($this->extra,$this->partnerId));
            $this->extraPriceCommission= \App\Facades\EzMoney::format(GetExtraCommission::run($this->extra,$this->partnerId));

        }

    }


    public function save(){

       // $this->validate($this->rules(), [], $this->fieldNames);
        $this->validate();


        \DB::table('extra_partner')->updateOrInsert(
            [
                'extra_id'=>$this->extraId,
                'partner_id'=>$this->partnerId,
            ],
            [
                'price' =>  \EzMoney::parseForDb($this->extraPrice),
                'commission' => $this->extraCommissionPercentage,
                'discount' => $this->extraDiscountPercentage,
                'date_from' => Carbon::create($this->extraDateFrom),
                'date_to' => Carbon::create($this->extraDateTo),
                'tax_level' => $this->extraTaxLevel,
                'calculation_type' => $this->extraCalculationType
            ]
        );

        $this->notification()->success('Saved', 'Extra Price Saved');

    }

    public function saveExtra()
    {
        $this->extra->setTranslations('name', $this->extraName);
        $this->extra->setTranslations('description', $this->extraDescription);
        $this->extra->save();
        $this->notification()->success('Update successful');
    }

    public function render()
    {
        return view('livewire.extras-edit');
    }
}

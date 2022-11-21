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
use Illuminate\Support\Str;
use Livewire\Component;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Parser\DecimalMoneyParser;
use WireUi\Traits\Actions;

class ExtrasEdit extends Component
{
    use Actions;

    public $extra;
    public $extraId;
    public $companyLanguages = ['en'];
    public $extraDateFrom;
    public $extraDateTo;
    public $extraTaxLevel;
    public $extraCalculationType;
    public $extraDiscountPercentage = 0;
    public $extraCommissionPercentage = 0;
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

    public $extraPriceFieldNames = [
        'extraCommissionPercentage.*' => 'extra commission Percentage',
        'extraPrice.*' => 'price',
        'extraTaxLevel.*' => 'extra tax level',
        'extraCalculationType.*' => 'extra calculation type',
        'extraDateFrom.*' => 'date from',
        'extraDateTo.*' => 'date to'
    ];

    protected function extraPriceRules()
    {
        return [
            'extraCommissionPercentage' => 'required|min:0|max:100|integer',
            'extraDiscountPercentage' => 'required|min:0|max:100|integer',
            'extraPrice' => 'required|min:1|regex:'. \App\Services\Helpers\EzMoney::MONEY_REGEX,
            'extraTaxLevel' => 'required',
            'extraCalculationType' => 'required',
            'extraDateFrom' => 'required|date',
            'extraDateTo' => 'required|date|after_or_equal:extraDateFrom',
        ];

    }

    public function mount()
    {
        $this->partnerId = Partner::first()?->id;
        $this->setModelPrices();
        $this->instantiateComponentValues();
    }

    public function updatedPartnerId(): void
    {
        $this->extraDateFrom = [];
        $this->extraDateTo = [];
        $this->extraTaxLevel = [];
        $this->extraCalculationType = [];
        $this->extraCommissionPercentage = 0;
        $this->extraDiscountPercentage = 0;
        $this->extraPriceWithDiscount = [];
        $this->extraPriceCommission = [];
        $this->setModelPrices();
    }

    private function setModelPrices(){

        if( !empty($this->extraId) && !empty($this->partnerId)) {
            $this->extra = Extra::with(['partner' => function ($q) {
                $q->where('partner_id', $this->partnerId);
            }])->find($this->extraId);

            $extra_partner = $this->extra->partner->first();

            $this->extraPrice = \EzMoney::format($extra_partner->pivot->price); // 1,99;
            $this->extraCalculationType = $extra_partner->pivot->calculation_type;
            $this->extraTaxLevel = $extra_partner->pivot->tax_level;
            $this->extraDateFrom = Carbon::make($extra_partner->pivot->date_from)?->format('d.m.Y') ?? '';
            $this->extraDateTo = Carbon::make($extra_partner->pivot->date_to)?->format('d.m.Y') ?? '';
            $this->extraCommissionPercentage = $extra_partner->pivot->commission?: 0;
            $this->extraDiscountPercentage = $extra_partner->pivot->discount ?: 0;
            $this->extraPriceWithDiscount = \App\Facades\EzMoney::format(GetExtraDiscount::run($this->extra,$this->partnerId));
            $this->extraPriceCommission= \App\Facades\EzMoney::format(GetExtraCommission::run($this->extra,$this->partnerId));

        }
    }

    public function instantiateComponentValues()
    {
        $this->companyLanguages = Language::all()->pluck('language_code')->toArray();
        foreach ($this->companyLanguages as $lang) {
            $this->extraName[$lang] = $this->extra->getTranslation('name', $lang, false);
            $this->extraDescription[$lang] = $this->extra->getTranslation('description', $lang, false);
        }
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

    public function updated($field)
    {

        if(Str::contains($field, 'extraDiscountPercentage')){

            if($this->extraDiscountPercentage > 100){
                $this->extraDiscountPercentage = 100;
            }

            $this->updateExtraPrice();
        }

        if(Str::contains($field, 'extraCommissionPercentage')){

            if($this->extraCommissionPercentage > 100){
                $this->extraCommissionPercentage = 100;
            }

            $this->updateExtraPrice();
        }

        $this->validateOnly($field);
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

        $this->validate($this->extraPriceRules(), [], $this->extraPriceFieldNames);

        \DB::table('extra_partner')->updateOrInsert(
            [
                'extra_id'=>$this->extra->id,
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

        $this->validate($this->rules(), [], $this->fieldNames);

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

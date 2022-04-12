<?php

namespace App\Http\Livewire;

use App\Models\Extra;
use App\Models\Language;
use App\Models\Transfer;
use Livewire\Component;

class ExtrasEdit extends Component
{
    public Extra $extra;
    public $extraId = null;
    public $companyLanguages = ['en'];
    public $extraPrice;
    public $partnerId = 0;
    public $extraName = [
        'en' => null
    ];
    public $extraDescription = [
        'en' => null
    ];

    protected function rules()
    {
        $ruleArray = [
            'extraName.en' => 'required|min:3',
            'extraPrice.*' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
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
        $this->setModelPrices();
    }


    private function setModelPrices(){
        if($this->extraId > 0){
            $this->extraPrice = $this->extra->getPrice($this->partnerId);
        }

    }

    public function updatedPartnerId()
    {
        $this->extraPrice =  null;

        $this->setModelPrices();
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


    public function saveExtraPrice(){

        $this->validate();
        \DB::table('extra_partner')->updateOrInsert(
            [
                'extra_id'=>$this->extraId,
                'partner_id'=>$this->partnerId,
            ],
            [
                'price' =>  $this->extraPrice
            ]
        );

        $this->showToast('Saved', 'Extra Price Saved');

    }


    public function saveExtra()
    {
        $this->extra->setTranslations('name', $this->extraName);
        $this->extra->setTranslations('description', $this->extraDescription);
        $this->extra->save();
        $this->showToast('Update successful');
    }



    public function render()
    {
        return view('livewire.extras-edit');
    }
}

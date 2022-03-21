<?php

namespace App\Http\Livewire;

use App\Models\Extra;


use Cknow\Money\Money;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Parser\DecimalMoneyParser;

class ExtrasOverview extends Component
{

    public $search = '';
    public $extra;
    public $extraModal;
    public $price;

    protected $rules = [
        'extra.name' => 'required|max:255',
        'extra.description' => 'max:255',
        'price' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
    ];

    protected $messages = [
        'extra.price.regex' => 'The price format must be in 00.00',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openExtraModal(){
        $this->extraModal = true;
    }

    public function closeExtraModal(){
        $this->extraModal = false;
    }

    public function updateExtra($extraId){
        $this->openExtraModal();
        $this->extra = Extra::find($extraId);

        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);
        $this->price =  $moneyFormatter->format($this->extra->price->getMoney());
    }

    public function addExtra(){
        $this->openExtraModal();
        $this->extra = new Extra();
        $this->price = 0;
    }

    public function saveExtraData(){


        $this->validate();

        //Money
        $currencies = new ISOCurrencies();
        $moneyParser = new DecimalMoneyParser($currencies);
        $money = $moneyParser->parse($this->price,new Currency('EUR'));
        $this->extra->owner_id = Auth::user()->owner_id;
        $this->extra->price = $money->getAmount();
        $this->extra->save();
        $this->showToast('Success','Extra saved, add some info to it!');
        $this->closeExtraModal();

    }

    public function render()
    {
        $extras = Extra::search('name',$this->search)->paginate(10);
        return view('livewire.extras-overview',compact('extras'));
    }

}

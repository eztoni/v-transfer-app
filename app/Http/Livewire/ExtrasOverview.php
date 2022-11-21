<?php

namespace App\Http\Livewire;

use App\Models\Extra;


use Cknow\Money\Money;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Parser\DecimalMoneyParser;
use WireUi\Traits\Actions;

class ExtrasOverview extends Component
{
use Actions;
    use WithPagination;

    public $search = '';
    public $extra;
    public $extraModal;
    public $price;
    public $companyLanguages = ['en'];
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
            'extraDescription.en' => 'max:100',
        ];
        return $ruleArray;
    }


    protected $messages = [
        'extraName.en.required' => 'The extra name is required!',
        'extraName.en.min' => 'The extra name must be at least 3 characters.',
        'extraDescription.en.max' => 'The extra description max characters is 100.',
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
    }

    public function addExtra(){
        $this->openExtraModal();
        $this->extra = new Extra();
    }

    public function saveExtraData(){


        $this->validate();

        //Money
        $this->extra->setTranslations('name', $this->extraName);
        $this->extra->setTranslations('description', $this->extraDescription);
        $this->extra->owner_id = Auth::user()->owner_id;
        $this->extra->save();
        $this->notification()->success('Success','Extra saved, add some info to it!');
        $this->closeExtraModal();
        return redirect('extras-edit/'.$this->extra->id);

    }

    public function render()
    {
        $extras = Extra::search('name',$this->search)->with('media')->paginate(10);
        return view('livewire.extras-overview',compact('extras'));
    }

}

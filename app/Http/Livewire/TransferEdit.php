<?php

namespace App\Http\Livewire;

use App\Models\Language;
use App\Models\Transfer;
use Illuminate\Validation\Rule;
use Livewire\Component;

class TransferEdit extends Component
{
    public Transfer $transfer;
    public $companyLanguages = ['en'];
    public $transferName = [
        'en' => null
    ];
    protected function rules()
    {
        $ruleArray = [
            'transferName.en' => 'required|min:3',
        ];
        foreach ($this->companyLanguages as $lang) {
            if ($lang !== 'en') {
                $ruleArray['transferName.' . $lang] = 'nullable|min:3';
            }
        }
        return $ruleArray;
    }


    public function mount()
    {
        $this->instantiateComponentValues();

    }

    public function instantiateComponentValues()
    {
        $this->companyLanguages = Language::all()->pluck('language_code')->toArray();


        foreach ($this->companyLanguages as $lang) {
            $this->transferName[$lang] = $this->transfer->getTranslation('name', $lang, false);
            $desc = null;
            if ($this->transfer->description)
                $desc = $this->transfer->description->getTranslation('description', $lang, false);
            $this->transferDescription[$lang] = $desc;
        }
    }


    public function render()
    {
        return view('livewire.transfer-edit');
    }
}

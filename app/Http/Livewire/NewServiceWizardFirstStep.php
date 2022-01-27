<?php
/**
 * Created by PhpStorm
 * User: Tin Modrić
 * Date: 9/9/2021
 */

namespace App\Http\Livewire;

use Livewire\Component;

class NewServiceWizardFirstStep extends Component
{
    public $currentStep = 1;
    public $successMsg = '';


    public function render()
    {
        return view('livewire.new-service-wizard-first-step');
    }

    public function nextStep(){
        $this->dispatchBrowserEvent('step-change', ['step' => 2]);
    }

    private function firstStepSubmit()
    {
    }
    public function secondStepSubmit()
    {
    }
    private function submitForm()
    {



    }
    public function stepBack()
    {
        if($this->currentStep>1)
        $this->currentStep = $this->currentStep-1;
    }
    public function clearForm()
    {

    }
}

<?php
/**
 * Created by PhpStorm
 * User: Tin Modrić
 * Date: 9/9/2021
 */

namespace App\Http\Livewire;

use Livewire\Component;

class NewServiceWizardSecondStep extends Component
{
    public $ratePlans=[
      []
    ];

    public function addRp(){
        $this->ratePlans[] = [];
    }
    public function removeRp($index){
       unset( $this->ratePlans[$index]);
}

    public function render()
    {
        return view('livewire.new-service-wizard-second-step');
    }
}

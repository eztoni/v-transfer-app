<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ReservationSavingModal extends Component
{
    public $show = false;

    protected $listeners = ['showSavingModal', 'hideSavingModal'];

    public function showSavingModal()
    {
        $this->show = true;
    }

    public function hideSavingModal()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.reservation-saving-modal');
    }
}

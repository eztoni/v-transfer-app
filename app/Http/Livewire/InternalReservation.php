<?php

namespace App\Http\Livewire;

use App\Models\Transfer;
use Livewire\Component;

class InternalReservation extends Component
{

    public $stepOneFields = [
        'destinationId' => null,
        'pickupPointId' => null,
        'dropOffPointId' => null,
    ];

    public $fakeData = [
        'title'=>'',
        'fName'=>'',
        'lName'=>'',
        'resNum'=>'',
        'email'=>'',
        'phone'=>'',
    ];

    public bool $twoWay = false;

    public int $step = 1;

    public array $travellers = [1];
    public array $seats = [1];

    public function addTraveller()
    {
        $this->travellers[] = 1;
    }

    public function removeTraveller()
    {
        array_pop($this->travellers);
    }

    public function addSeat()
    {
        $this->seats[] = 1;
    }
    public function pullTraveller()
    {
       $this->fakeData = [
           'title'=>'Mr.',
           'fName'=>'John',
           'lName'=>'Doe',
           'resNum'=>'3127863',
           'email'=>'john@doe.test',
           'phone'=>'0959105570',
       ];
    }
    public function removeSeat()
    {
        array_pop($this->seats);
    }

    public function getTransfersProperty()
    {
        return Transfer::with('media')->get();
    }

    public function selectTransfer()
    {
        $this->step = 2;
    }

    public function render()
    {
        return view('livewire.internal-reservation');
    }
}
